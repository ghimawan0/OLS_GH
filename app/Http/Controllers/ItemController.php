<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Item::all();
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
        
        $result = DB::table('items AS a')
            ->select(DB::raw(
                "COUNT(*) AS data"
            ))
            ->where('a.nama', $request->nama)
            ->get();
        
        if ($result[0]->data > 0) {
            $status = 'error';
            $message = 'nama item sudah ada.';
        } else {}
        
        
        if ($status == 'success') {
            $item = new Item;
            $item->nama = $request->nama;
            $item->save();
            
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
        return str_replace('}"', '}', str_replace('"{', '{', str_replace(']"', ']', str_replace('"[', '[', str_replace('\\"', '"', str_replace('\\\\', '', DB::table('items AS a')
            ->select(DB::raw(
                "CONCAT('[', GROUP_CONCAT(JSON_OBJECT('id', a.id, 'nama', a.nama, 'pajak', (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT('id', b.id, 'nama', b.nama, 'rate', b.rate) ORDER BY b.id), ']') AS pajaks_list FROM pajaks AS b INNER JOIN items_pajaks AS c ON b.id = c.id_pajaks WHERE c.id_items = a.id)) ORDER BY a.id), ']') AS data"
            ))
            ->where('a.id', $id)
            ->get()
        ))))));
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
        
        $result = DB::table('items AS a')
            ->select(DB::raw(
                "COUNT(*) AS data"
            ))
            ->where('a.nama', $request->nama)
            ->get();
        
        if ($result[0]->data > 0) {
            $status = 'error';
            $message = 'nama item sudah ada.';
        } else {}
        
        
        if ($status == 'success') {
            $item = Item::find($id);
            $item->nama = $request->nama;
            $item->save();
            
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
        
        $result = DB::table('items AS a')
            ->select(DB::raw(
                "COUNT(*) AS data"
            ))
            ->where('a.id', $id)
            ->get();
        
        if ($result[0]->data == 0) {
            $status = 'error';
            $message = 'id item tidak ditemukan.';
        } else {}
        
        
        if ($status == 'success') {
            $item = Item::find($id);
            $item->delete();
            
            $message = "data berhasil dihapus.";
        } else {}
        
        $output['status'] = $status;
        $output['message'] = $message;
        
        return json_encode($output);
    }
}
