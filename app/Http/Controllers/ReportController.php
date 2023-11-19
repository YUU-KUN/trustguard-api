<?php

namespace App\Http\Controllers;

use App\Models\Report;
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
            $input['user_id'] = Auth::user()->id;

            // $validated = $request->validate([
            //     'title' => 'required',
            // ])
    
            
            $report = Report::create($input);
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
