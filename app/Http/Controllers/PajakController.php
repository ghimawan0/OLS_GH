<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pajak;

class PajakController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Pajak::all();
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
        $status = 'success';
        $message = '';
        
        $result = DB::table('pajaks AS a')
            ->select(DB::raw(
                "COUNT(*) AS data"
            ))
            ->where('a.nama', $request->nama)
            ->get();
        
        if ($result[0]->data > 0) {
            $status = 'error';
            $message = 'nama pajak sudah ada.';
        } elseif ((float) $request->rate < 0) {
            $status = 'error';
            $message = 'rate harus lebih besar dari 0.';
        } else {}
        
        
        
        if ($status == 'success') {
            $pajak = new Pajak;
            $pajak->nama = $request->nama;
            $pajak->rate = $request->rate;
            $pajak->save();
            
            $message = "data berhasil disimpan.";
        } else {}
        
        $output['status'] = $status;
        $output['message'] = $message;
        
        return json_encode($output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $status = 'success';
        $message = '';
        
        $result = DB::table('pajaks AS a')
            ->select(DB::raw(
                "COUNT(*) AS data"
            ))
            ->where('a.nama', $request->nama)
            ->get();
        
        if ($result[0]->data > 0) {
            $status = 'error';
            $message = 'nama pajak sudah ada.';
        } elseif ((float) $request->rate < 0) {
            $status = 'error';
            $message = 'rate harus lebih besar dari 0.';
        } else {}
        
        
        
        if ($status == 'success') {
            $pajak = Pajak::find($id);
            $pajak->nama = $request->nama;
            $pajak->rate = $request->rate;
            $pajak->save();
            
            $message = "data berhasil di-update.";
        } else {}
        
        $output['status'] = $status;
        $output['message'] = $message;
        
        return json_encode($output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = 'success';
        $message = '';
        
        $result = DB::table('pajaks AS a')
            ->select(DB::raw(
                "COUNT(*) AS data"
            ))
            ->where('a.id', $id)
            ->get();
        
        if ($result[0]->data == 0) {
            $status = 'error';
            $message = 'id pajak tidak ditemukan.';
        } else {}
        
        
        
        if ($status == 'success') {
            $pajak = Pajak::find($id);
            $pajak->delete();
            
            $message = "data berhasil dihapus.";
        } else {}
        
        $output['status'] = $status;
        $output['message'] = $message;
        
        return json_encode($output);
    }
}
