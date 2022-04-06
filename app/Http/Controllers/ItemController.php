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
    public function store_old(Request $request)
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $status = 'success';
        $message = '';
        
        $arr_pajak = explode(',', $request->id_pajak);
        
        $result_item = DB::table('items AS a')
            ->select(DB::raw(
                "COUNT(*) AS data"
            ))
            ->where('a.nama', $request->nama)
            ->get();
        
        $result_pajak = DB::table('pajaks AS a')
            ->select(DB::raw(
                "COUNT(*) AS data"
            ))
            ->whereIn('a.id', $arr_pajak)
            ->get();
        
        
        if ($result_item[0]->data > 0) {
            $status = 'error';
            $message = 'nama item sudah ada.';
        } elseif (count($arr_pajak) < 2) {
            $status = 'error';
            $message = 'item harus memiliki paling sedikit 2 id pajak.';
        } elseif (count($arr_pajak) > $result_pajak[0]->data) {
            $status = 'error';
            $message = 'ada id pajak yang tidak ditemukan di database.';
        } else {}
        
        
        if ($status == 'success') {
            $item = new Item;
            $item->nama = $request->nama;
            $item->save();
            
            foreach ($arr_pajak as $id_pajak) {
                DB::table('items_pajaks')->insert([
                    'id_items' => $item->id,
                    'id_pajaks' => $id_pajak
                ]);
            }
            
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
    public function update_old(Request $request, $id)
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
        $error_item = '';
        
        $arr_pajak = explode(',', $request->id_pajak);
        
        $result_item = DB::table('items AS a')
            ->select(DB::raw(
                "a.id, a.nama"
            ))
            ->where('a.id', $id)
            ->get();
        
        $result_pajak = DB::table('pajaks AS a')
            ->select(DB::raw(
                "COUNT(*) AS data"
            ))
            ->whereIn('a.id', $arr_pajak)
            ->get();
        
        
        if (count($result_item) == 0) {
            $error_item = 'id item tidak ditemukan.';
        } elseif ($result_item[0]->nama != $request->nama) {
            $result = DB::table('items AS a')
                ->select(DB::raw(
                    "COUNT(*) AS data"
                ))
                ->where('a.nama', $request->nama)
                ->where('a.id', '<>', $id)
                ->get();
            
            if ($result[0]->data > 0) {
                $error_item = 'nama item sudah ada';
            } else {}
        } else {}
        
        
        
        if ($error_item != '') {
            $status = 'error';
            $message = $error_item;
        } elseif (count($arr_pajak) < 2) {
            $status = 'error';
            $message = 'item harus memiliki paling sedikit 2 id pajak.';
        } elseif (count($arr_pajak) > $result_pajak[0]->data) {
            $status = 'error';
            $message = 'ada id pajak yang tidak ditemukan di database.';
        } else {}
        
        
        if ($status == 'success') {
            $item = Item::find($id);
            $item->nama = $request->nama;
            $item->save();
            
            $deleted = DB::table('items_pajaks')->where('id_items', $id)->delete();
            
            foreach ($arr_pajak as $id_pajak) {
                DB::table('items_pajaks')->insert([
                    'id_items' => $id,
                    'id_pajaks' => $id_pajak
                ]);
            }
            
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
    public function destroy_old($id)
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
            
            $deleted = DB::table('items_pajaks')->where('id_items', $id)->delete();
            
            $message = "data berhasil dihapus.";
        } else {}
        
        $output['status'] = $status;
        $output['message'] = $message;
        
        return json_encode($output);
    }
}
