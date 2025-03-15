<?php

namespace App\Http\Controllers;

use App\Models\DetailPo;
use App\Models\DetailPR;
use App\Models\Keproyekan;
use App\Models\Kontrak;
use App\Models\Purchase_Order;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RiwayatpembelianController extends Controller
{
    public function riwayat_pembelian()
    {
        $items = DetailPR::whereNotNull('id_po')->groupBy('kode_material')->paginate(10);

        return view('riwayat_pembelian.index', compact('items'));
    }

    // public function getDetailRiwayatPembelian(Request $request)
    // {
    //     $komat = $request->kode_material;
    //     $items = DetailPR::whereNotNull('id_po')->where('kode_material', $komat)
    //     ->get();

    //     $items = $items->map(function($item){
    //         $po = Purchase_Order::where('id', $item->id_po)->first();
    //         // dd($po);
    //         $split_proyek = explode(',', $po->proyek_id);
    //         // dd($split_proyek);
    //         $proyek_names = Kontrak::whereIn('id', $split_proyek)->pluck('nama_pekerjaan')->toArray();
    //         // dd($proyek_names);
    //         $item->proyek = implode(',', $proyek_names);
    //         // dd($item->proyek);
    //         // $item->proyek = Keproyekan::where('id', $item->id_proyek)->first();
    //         $item->detail_po = DetailPo::where('id_detail_pr', $item->id)->first();
    //         $item->detail_po->harga = $this->format_rupiah($item->detail_po->harga);
    //         $po = Purchase_Order::where('id', $item->id_po)->first();
    //         $item->vendor = $po ? Vendor::where('id', $po->vendor_id)->first() : null;


    //         return $item;
    //     });

    //     return response()->json([
    //         'items' => $items
    //     ]);
    // }

    public function getDetailRiwayatPembelian(Request $request)
    {
        $komat = $request->kode_material;
        $items = DetailPR::whereNotNull('id_po')->where('kode_material', $komat)->get();

        $items = $items->map(function ($item) {
            $po = Purchase_Order::where('id', $item->id_po)->first();

            // Ambil nama proyek terkait
            $split_proyek = explode(',', $po->proyek_id);
            $proyek_names = Kontrak::whereIn('id', $split_proyek)->pluck('nama_pekerjaan')->toArray();

            // Tambahkan data tambahan
            $item->proyek = implode(',', $proyek_names);
            $item->no_po = $po->no_po; // Ambil no_po
            $item->tanggal_po = $po->tanggal_po ? Carbon::parse($po->tanggal_po)->format('d/m/Y') : null; // Format dd/mm/yyyy
            $item->detail_po = DetailPo::where('id_detail_pr', $item->id)->first();
            $item->detail_po->harga = $this->format_rupiah($item->detail_po->harga);
            $item->vendor = $po ? Vendor::where('id', $po->vendor_id)->first() : null;

            return $item;
        });

        return response()->json([
            'items' => $items,
        ]);
    }
    
}
