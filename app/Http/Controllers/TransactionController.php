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
    function checkAvailableFraudTransaction() {
        $is_fraud_detected = Transaction::where('user_id', Auth::user()->id)->where('transaction_code', true)->exists();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data transaksi fraud',
            'data' => $is_fraud_detected
        ], 200);
    }

    public function getTransactions() {
        // $transactions = Transaction::get()->groupBy('created_at');
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
            'data' => $user_bank->load('Bank:id,name')
        ], 200);
    }

    public function topup(Request $request) {
        $input = $request->all();
        $input['user_id'] = Auth::user()->id;

        // $user
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

        // Predict Fraud in Transaction
        $ml_input = [
            "amount" => 5758.59,
            "transaction_time" => 0.000000,
            "location_encode" => "0.469388",
            "customer_age" => 0.480769,
            "card_type" => "0.666667",
            "purchase_category" => "0.0"
        ];

        $input['is_fraud_detected'] = false;
        $response = Http::post(env('ML_BASE_URL') . "/predict", $ml_input)->json();
        if ($response['prediction'] == 1) {
            $input['is_fraud_detected'] = true;
        }

        $transaction = Transaction::create($input);
        if ($transaction) {
            $user = User::find(Auth::user()->id);
            $user->balance = $user->balance - $input['amount'];
            $user->save();

            $receiver = UserBank::find($transaction->user_bank_id)->User;
            $receiver->balance = $receiver->balance + $input['amount'];
            $receiver->save();
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
}
