<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\UserBank;
use App\Models\ReportEvidence;
use Illuminate\Http\Request;
use Auth;
use Storage;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reports = Report::get();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data laporan',
            'data' => $reports
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();
            
            $validator = \Validator::make($input, [
                'suspect_account_name' => 'required',
                'bank_id' => "required|exists:banks,id",
                'suspect_account_name' => "required",
                'suspect_account_number' => "required|numeric",
                'suspect_phone' => "required|numeric",
                
                'platform_id' => "required|exists:platforms,id",
                'product_category_id' => "required|exists:product_categories,id",
                'chronology' => "required",
                'loss_amount' => "required|numeric",
                
                'reporter_name' => "required|max:255",
                'identity' => "required|in:ktp,sim.passport",
                'identity_number' => "required|numeric",
                'reporter_phone' => "required|numeric",
                'evidences' => 'required|array',
                // 'evidences.*' => 'required|mimes:png,jpg,jpeg,pdf|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()
                ], 404);
            }

            $input['user_id'] = Auth::user()->id;    
            $report = Report::create($input);
            $userbank = UserBank::where([
                ['account_name', 'like', '%' . $input['suspect_account_name'] . '%'],
                ['rekening_number', $input['suspect_account_number']],
            ])->first();


            $userbank->update([
                'is_reported' => true,
            ]);

            $userbank->User->update([
                'report_count' => $userbank->User->report_count + 1,
                'trust_score' => $userbank->User->trust_score - 5,
            ]);

            $userbank->User->report_count >= 3 && $userbank->User->update([
                'is_suspended' => true,
            ]);


            if ($report) {
                foreach ($input['evidences'] as $evidence) {
                    $evidence_filename = time().'.'.$evidence->extension();
                    $evidence_path = Storage::url('evidences/');
                    $evidence->move(public_path($evidence_path), $evidence_filename);
                    ReportEvidence::create([
                        'report_id' => $report->id,
                        'evidence' => $evidence_filename
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil membuat laporan',
                    'data' => $report
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function show(Report $report)
    {
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data laporan',
            'data' => $report
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy(Report $report)
    {
        $report->delete();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus laporan',
            'data' => $report
        ]);
    }
}
