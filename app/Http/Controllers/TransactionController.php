<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserBank;
use App\Models\Transaction;

use Auth;
use Http;
use DB;

class TransactionController extends Controller
{
    public function checkAvailableFraudTransaction() {
        $is_fraud_detected = Transaction::where('user_id', Auth::user()->id)->where('is_fraud_detected', true)->exists();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data transaksi fraud',
            'data' => $is_fraud_detected
        ], 200);
    }

    public function getTransactions() {
        $transactions = Transaction::select('*', DB::raw('DATE(created_at) as date'))->orderBy('created_at', 'desc')
        ->get()->load('User:id,firstname,lastname', 'UserBank:id,bank_id,account_name,rekening_number', 'UserBank.bank:id,name')->groupBy('date');

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data transaksi',
            'data' => $transactions
        ], 200);  
    }

    public function getUserBank(Request $request) {
        $input = $request->all();

        $user_bank = UserBank::find($input['user_bank_id']);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data bank pengguna',
            'data' => $user_bank->load('Bank:id,name', 'User:id,firstname,lastname,trust_score,phone')
        ], 200);
    }

    public function getLastTransfer() {
        $last_transfer = Transaction::where('user_id', Auth::user()->id)->limit(3)->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data transfer terakhir',
            'data' => $last_transfer->load('UserBank:id,bank_id,account_name,rekening_number', 'UserBank.Bank:id,name')
        ]);
    }

    public function getRekeningStatus(Request $request) {
        $input = $request->all();
        $user_bank = UserBank::where([
            'rekening_number' => $input['rekening_number'],
            'bank_id' => $input['bank_id']
        ])->first();
            
        if (!$user_bank) {
            return response()->json([
                'success' => false,
                'message' => 'Rekening tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data rekening pengguna',
            'data' => $user_bank->load('Bank:id,name')
        ], 200);
    }

    public function transfer(Request $request) {
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;
        $input['transaction_code'] = 'tsd-'.rand(100, 999).'-'.rand(100, 999);

        if ($input['amount'] > Auth::user()->balance) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi'
            ], 404);
        }

        $transaction = Transaction::create($input);
        if ($transaction) {
            $user = User::find(Auth::user()->id);
            $user->balance = $user->balance - $input['amount'];
            $user->save();

            $receiver = UserBank::find($transaction->user_bank_id)->User;
            $receiver->balance = $receiver->balance + $input['amount'];
            $receiver->save();

            if ($transaction->is_fraud_detected) {
                $receiver->trust_score = $receiver->trust_score - 2.5;
                $receiver->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mentransfer',
            'data' => $transaction->load('User:id,firstname,lastname', 'UserBank:id,bank_id,account_name,rekening_number', 'UserBank.bank:id,name')
        ], 200);
    }

    public function getTransactionDetail(Request $request) {
        $transaction = Transaction::find($request->transaction_id);
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data transfer',
            'data' => $transaction->load('User:id,firstname,lastname', 'UserBank:id,bank_id,account_name,rekening_number', 'UserBank.bank:id,name')
        ], 200);
    }

    // Predict Fraud in Transaction
    public function checkFraud(Request $request) {
        $ml_input = $request->all();
        
        $validator = \Validator::make($ml_input, [
            'amount' => 'required|numeric',
            'type_number' => 'required|in:1,2,3,4,5', # 'CASH_OUT':1, 'PAYMENT':2, 'CASH_IN':3, 'TRANSFER':4, 'DEBIT':5
            'receiver_bank_id' => 'required|exists:user_banks,id',
            // 'oldbalanceOrg' => 'required|numeric',
            // 'newbalanceOrig' => 'required|numeric',
            // 'oldbalanceDest' => 'required|numeric',
            // 'newbalanceDest' => 'required|numeric',
            // 'isFlaggedFraud' => 'required|numeric',
        ]);

        // Sender Balance
        $ml_input['oldbalanceOrg'] = Auth::user()->balance;
        $ml_input['newbalanceOrig'] = Auth::user()->balance - $ml_input['amount'];
        
        // Receiver Balance
        $receiver_balance = UserBank::find($ml_input['receiver_bank_id'])->User->balance;
        // $receiver_balance = UserBank::where('rekening_number', $ml_input['account_number'])->first()->User->balance;
        $ml_input['oldbalanceDest'] = $receiver_balance;
        $ml_input['newbalanceDest'] = $receiver_balance + $ml_input['amount'];

        $ml_input['isFlaggedFraud'] = 0.0;

        
        if ($validator->fails()) {
            return response()->json([[
                'success' => false,
                'message' => $validator->errors()
            ]], 422);
        }

        $result['is_fraud_detected'] = false;
        try {
            $ml_url = env('APP_ENV') == 'local' ? env('ML_LOCAL_URL') : env('ML_PROD_URL');
            $response = Http::post($ml_url . "/predict", $ml_input)->json();
            if ($response['prediction'] == 1) {
                $result['is_fraud_detected'] = true;
            }
            
        } catch (\Throwable $th) {
            $result['is_fraud_detected'] = false;
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data transaksi',
            'data' => $result
        ], 200);
    }
}
