<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function index(){
        $history = History::all();
        return view('stock_history', compact('historys'));
    }

    public function deleteAll(Request $request){
        $ids = $request->ids;
        if(empty($ids)){
            return response()->json(["success"=>false,"message"=>"Pilih salah satu riwayat!"]);
        }
        DB::table('stock')->whereIn('stock_id',$ids)->delete();
        return response()->json(["success"=>true,"message"=>"Riwayat berhasil dihapus!"]);
    }
}
