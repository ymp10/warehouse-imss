<?php

namespace App\Http\Controllers;

use App\Models\DetailPo;
use App\Models\DetailPR;
use App\Models\Keproyekan;
use App\Models\Kontrak;
use App\Models\Nego;
use App\Models\Proyek;
use App\Models\Purchase_Order;
use App\Models\PurchaseRequest;
use App\Models\Spph;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->q;
        if (Session::has('selected_warehouse_id')) {
            $warehouse_id = Session::get('selected_warehouse_id');
        } else {
            $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
        }

        $purchases = Purchase_Order::select('purchase_order.*', 'vendor.nama as vendor_name', 'kontrak.nama_pekerjaan as proyek_name', 'purchase_request.no_pr as pr_no')
            ->where('purchase_order.tipe', "0")
            ->join('vendor', 'vendor.id', '=', 'purchase_order.vendor_id')
            ->leftjoin('kontrak', 'kontrak.id', '=', 'purchase_order.proyek_id')
            ->leftjoin('purchase_request', 'purchase_request.id', '=', 'purchase_order.pr_id')
            ->orderBy('id', 'asc')
            ->paginate(50);
        $vendors = DB::table('vendor')->get();
        $proyeks = DB::table('kontrak')->get();

        foreach ($purchases as $purchase) {
            $split_proyek = explode(',', $purchase->proyek_id);

            $proyek_names = Kontrak::whereIn('id', $split_proyek)->pluck('nama_pekerjaan')->toArray();
            $proyek = implode(',', $proyek_names);

            // Add the proyek value (optional if already exists in the query result)
            // if ($proyek) {
            $purchase->proyek_name = $proyek;
            // }
        }


        if ($search) {
            $purchases = Purchase_Order::where('no_po', 'LIKE', "%$search%")->paginate(50);
        }

        $purchases->getCollection()->transform(function ($purchase) {
            $pr = PurchaseRequest::whereIn('id', explode(',', $purchase->pr_id))->get();
            $purchase->pr_no = $pr->pluck('no_pr')->implode(', ');
            return $purchase;
        });

        if ($request->format == "json") {
            $purchases = Purchase_Order::where("warehouse_id", $warehouse_id)->get();

            $purchases->getCollection()->transform(function ($purchase) {
                $pr = PurchaseRequest::whereIn('id', explode(',', $purchase->pr_id))->get();
                $purchase->pr_no = $pr->pluck('no_pr')->implode(', ');
                return $purchase;
            });

            return response()->json($purchases);
        } else {
            $prs = PurchaseRequest::all();
            return view('purchase_order.purchase_order', compact('purchases', 'vendors', 'proyeks', 'prs'));
        }
    }

    public function showPOPL(Request $request)
    {
        $search = $request->q;
        if (Session::has('selected_warehouse_id')) {
            $warehouse_id = Session::get('selected_warehouse_id');
        } else {
            $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
        }

        $purchases = Purchase_Order::select('purchase_order.*', 'kontrak.nama_pekerjaan as proyek_name', 'purchase_request.no_pr as pr_no')
            ->where('purchase_order.tipe', '1')
            ->leftjoin('kontrak', 'kontrak.id', '=', 'purchase_order.proyek_id')
            ->leftjoin('purchase_request', 'purchase_request.id', '=', 'purchase_order.pr_id')
            ->paginate(50);
        // $vendors = DB::table('vendor')->get();
        $proyeks = DB::table('kontrak')->get();


        if ($search) {
            $purchases = Purchase_Order::where('no_po', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $purchases = Purchase_Order::where("warehouse_id", $warehouse_id)->get();

            return response()->json($purchases);
        } else {
            $prs = PurchaseRequest::all();
            return view('purchase_order.po_pl', compact('purchases', 'proyeks', 'prs'));
        }
    }

    public function indexApps(Request $request)
    {
        $search = $request->q;

        $purchases = Purchase_Order::select('purchase_order.*', 'vendor.nama as vendor_name', 'kontrak.nama_pekerjaan as proyek_name', 'purchase_request.no_pr as pr_no')
            ->join('vendor', 'vendor.id', '=', 'purchase_order.vendor_id')
            ->leftjoin('kontrak', 'kontrak.id', '=', 'purchase_order.proyek_id')
            ->leftjoin('purchase_request', 'purchase_request.id', '=', 'purchase_order.pr_id')
            ->paginate(50);
        $vendors = DB::table('vendor')->get();
        $proyeks = DB::table('kontrak')->get();

        if ($search) {
            $purchases = Purchase_Order::where('no_po', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $purchases = Purchase_Order::all();

            return response()->json($purchases);
        } else {
            return view('home.apps.logistik.purchase_order', compact('purchases', 'vendors', 'proyeks'));
        }
    }

    public function getDetailPo(Request $request)
    {
        $id = $request->id;
        $po = Purchase_Order::select('purchase_order.*', 'vendor.nama as nama_vendor', 'kontrak.nama_pekerjaan as nama_proyek')
            ->join('vendor', 'vendor.id', '=', 'purchase_order.vendor_id')
            ->leftjoin('kontrak', 'kontrak.id', '=', 'purchase_order.proyek_id')
            ->where('purchase_order.id', $id)
            ->first();

        $split_proyek = explode(',', $po->proyek_id);
        $proyek_names = Kontrak::whereIn('id', $split_proyek)->pluck('nama_pekerjaan')->toArray();
        $po->nama_proyek = implode(',', $proyek_names);

        $po->details = DetailPo::where('detail_po.id_po', $id)
            ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_po.id_detail_pr')
            ->select('detail_pr.*', 'detail_po.id as id_detail_po', 'detail_po.harga as harga_per_unit', 'detail_po.mata_uang as mata_uang', 'detail_po.vat as vat', 'detail_po.batas_akhir as batas', 'detail_po.po_qty')
            ->get();
        return response()->json([
            'po' => $po,
        ]);
    }
    public function getDetailPOPL(Request $request)
    {
        $id = $request->id;
        $po = Purchase_Order::select('purchase_order.*', 'kontrak.nama_pekerjaan as nama_proyek')
            ->leftjoin('kontrak', 'kontrak.id', '=', 'purchase_order.proyek_id')
            ->where('purchase_order.id', $id)
            ->first();
        $po->details = DetailPo::where('detail_po.id_po', $po->id)
            ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_po.id_detail_pr')
            ->select('detail_pr.*', 'detail_po.id as id_detail_po', 'detail_po.harga as harga_per_unit', 'detail_po.mata_uang as mata_uang', 'detail_po.vat as vat', 'detail_po.batas_akhir as batas')
            ->get();

        return response()->json([
            'po' => $po
        ]);
        // dd($po);
    }

    public function detailPrSave(Request $request)
    {
        $id_po = $request->id_po;
        $id_detail_po = $request->id;
        $batas = $request->batas;
        $harga_per_unit = $request->harga_per_unit;
        $mata_uang = $request->mata_uang;
        $vat = $request->vat;

        DetailPo::where('id', $id_detail_po)->update([
            'batas_akhir' => $batas,
            'harga' => $harga_per_unit,
            'mata_uang' => $mata_uang,
            'vat' => $vat,
        ]);

        $po = Purchase_Order::select('purchase_order.*', 'vendor.nama as nama_vendor', 'kontrak.nama_pekerjaan as nama_proyek')
            ->leftjoin('vendor', 'vendor.id', '=', 'purchase_order.vendor_id')
            ->leftjoin('kontrak', 'kontrak.id', '=', 'purchase_order.proyek_id')
            ->where('purchase_order.id', $id_po)
            ->first();
        $po->details = DetailPo::where('detail_po.id_po', $po->id)
            ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_po.id_detail_pr')
            ->select('detail_pr.*', 'detail_po.id as id_detail_po', 'detail_po.harga as harga_per_unit', 'detail_po.mata_uang as mata_uang', 'detail_po.vat as vat', 'detail_po.batas_akhir as batas', 'detail_po.po_qty')
            ->get();
        return response()->json([
            'po' => $po
        ]);
    }

    // public function destroyDetailPo(Request $request)
    // {
    //     // dd($request->all());
    //     $id = $request->id;
    //     $id_po = $request->id_po;

    public function destroyDetailPo(Request $request)
    {
        $id = $request->id;
        $id_po = $request->id_po;
        $id_detailpr = $request->id_detail_pr;

        // Ambil data qty sebelum dihapus dari detail_po
        $detail_po = DetailPo::where('id', $id)->first();

        if ($detail_po) {
            $po_qty = $detail_po->po_qty;
            $id_del_po = $detail_po->id_del_po;

            // Hapus data dari detail_po
            $delete_detail_po = DetailPo::where('id', $id)->delete();

            // Update qty_po di detail_pr jika id_del_po dan id_del sama
            $update_detail_pr = DetailPR::where('id', $id_detailpr)
                ->where('id_del', $id_del_po)
                ->increment('qty_po', $po_qty);

            // Set id_po di detail_pr menjadi null
            $delete_detail_pr = DetailPR::where('id', $id_detailpr)->update([
                'id_po' => null
            ]);

            if ($delete_detail_po && $update_detail_pr) {
                $po = Purchase_Order::select('purchase_order.*', 'vendor.nama as nama_vendor', 'keproyekan.nama_proyek as nama_proyek')
                    ->leftjoin('vendor', 'vendor.id', '=', 'purchase_order.vendor_id')
                    ->leftjoin('keproyekan', 'keproyekan.id', '=', 'purchase_order.proyek_id')
                    ->where('purchase_order.id', $id_po)
                    ->first();

                $po->details = DetailPo::where('detail_po.id_po', $po->id)
                    ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_po.id_detail_pr')
                    ->select(
                        'detail_pr.*',
                        'detail_po.id as id_detail_po',
                        'detail_po.harga as harga_per_unit',
                        'detail_po.mata_uang as mata_uang',
                        'detail_po.vat as vat',
                        'detail_po.batas_akhir as batas',
                        'detail_po.po_qty'
                    )
                    ->get();

                return response()->json([
                    'po' => $po
                ]);
            }
        }

        return response()->json([
            'po' => null
        ]);
    }

    // public function destroyDetailPo(Request $request)
    // {

    //     $id = $request->id;
    //     $id_detail_pr = $request->id_detail_pr;
    //     $id_detail_po = $request->id_detail_po;
    //     $id_po = $request->id_po;

    //      // Mengambil data DetailPo dan DetailPR untuk validasi
    // $detail_po = DetailPo::find($id_detail_po);
    // $detail_pr = DetailPR::find($id);



    // // Validasi: cek jika id_del_po di DetailPo ada
    // if ($detail_po && $detail_pr) {
    //     if (!$detail_po->id_del_po) {
    //         // Jika tidak ada id_del_po, set qty_po dengan qty dari DetailPR
    //         $detail_pr->qty_po = $detail_pr->qty;
    //         $detail_pr->save();
    //     } else {
    //         // Jika id_del_po ada, tambahkan po_qty ke qty_po di DetailPR
    //         $po_qty = $detail_po->po_qty;
    //         $detail_pr->qty_po += $po_qty;
    //         $detail_pr->save();
    //     }
    // }




    //     $delete_detail_po = DetailPo::where('id', $id)->delete();
    //     $id_detailpr = $request->id_detail_pr;
    //     $delete_detail_pr = DetailPR::where('id', $id_detailpr)->update([
    //         'id_po' => null
    //     ]);


    //     if ($delete_detail_po && $delete_detail_pr) {
    //         $po = Purchase_Order::select('purchase_order.*', 'vendor.nama as nama_vendor', 'kontrak.nama_pekerjaan as nama_proyek')
    //             ->leftjoin('vendor', 'vendor.id', '=', 'purchase_order.vendor_id')
    //             ->leftjoin('kontrak', 'kontrak.id', '=', 'purchase_order.proyek_id')
    //             ->where('purchase_order.id', $id_po)
    //             ->first();

    //         $po->details = DetailPo::where('detail_po.id_po', $po->id)
    //             ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_po.id_detail_pr')
    //             ->select(
    //                 'detail_pr.*',
    //                 'detail_po.id as id_detail_po',
    //                 'detail_po.harga as harga_per_unit',
    //                 'detail_po.mata_uang as mata_uang',
    //                 'detail_po.vat as vat',
    //                 'detail_po.batas_akhir as batas'
    //             )
    //             ->get();
    //         return response()->json([
    //             'po' => $po
    //         ]);
    //     } else {
    //         return response()->json([
    //             'po' => null
    //         ]);
    //     }

    // }

    public function test_pr(Request $request)
    {
        $id_po = $request->id_po;
        $po = Purchase_Order::select('purchase_order.*', 'vendor.nama as nama_vendor', 'kontrak.nama_pekerjaan as nama_proyek')
            ->leftjoin('vendor', 'vendor.id', '=', 'purchase_order.vendor_id')
            ->leftjoin('kontrak', 'kontrak.id', '=', 'purchase_order.proyek_id')
            ->where('purchase_order.id', $id_po)
            ->first();
        $po->details = DetailPo::where('detail_po.id_po', $po->id)
            ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_po.id_detail_pr')
            ->select('detail_pr.*', 'detail_po.id as id_detail_po', 'detail_po.harga as harga_per_unit', 'detail_po.mata_uang as mata_uang', 'detail_po.vat as vat', 'detail_po.batas_akhir as batas')
            ->get();
        return response()->json([
            'po' => $po
        ]);
    }

    function tambahDetailPo(Request $request)
    {
        $id = $request->id_po;
        $selected = $request->selected;

        foreach ($selected as $key => $value) {
            // Ambil data dari DetailPR
            $detail_pr = DetailPR::find($value);

            // Ambil data dari DetailPo berdasarkan id_detail_pr
            $detail_po = DetailPo::where('id_detail_pr', $value)->first();

            // Update status dan id_po di tabel DetailPR
            $update = DetailPR::where('id', $value)->update([
                'id_po' => $id,
                'status' => 3,
                'qty_po1' => null,
            ]);
            $id_del = $detail_pr->id_del;
            // Create record baru di DetailPo dan kirimkan qty_po1 ke po_qty
            $add = DetailPo::create([
                'id_po' => $id,
                'id_pr' => $detail_pr->id_pr,
                'id_detail_pr' => $detail_pr->id,
                'po_qty' => $detail_pr->qty_po1,  // Menambahkan qty_po1 dari DetailPR ke po_qty di DetailPo
                'id_del_po' => $id_del,

            ]);
        }


        // Fetch the updated purchase order data
        $po = Purchase_Order::leftjoin('kontrak', 'kontrak.id', '=', 'purchase_order.proyek_id')
            ->leftjoin('purchase_request', 'purchase_request.id', '=', 'purchase_order.pr_id')
            ->leftjoin('vendor', 'vendor.id', '=', 'purchase_order.vendor_id')
            ->select('purchase_order.*', 'kontrak.*', 'vendor.*', 'purchase_request.*', 'purchase_order.id as id_po')
            ->where('purchase_order.id', $id)
            ->first();

        $po->details = DetailPo::where('detail_po.id_po', $po->id_po)
            ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_po.id_detail_pr')
            ->select(
                'detail_pr.*',
                'detail_po.id as id_detail_po',
                'detail_po.harga as harga_per_unit',
                'detail_po.mata_uang as mata_uang',
                'detail_po.vat as vat',
                'detail_po.batas_akhir as batas',
                'detail_po.po_qty'
            )
            ->get();

        return response()->json([
            'po' => $po
        ]);
    }


    public function QtyPoSave(Request $request)
    {
        // Validasi array
        $request->validate([
            'data' => 'required|array',
            'data.*.id' => 'required|integer',
            'data.*.qty_po1' => 'required|numeric'
        ]);

        foreach ($request->data as $item) {
            $poDetail = DetailPR::find($item['id']);

            if (!$poDetail) continue;

            // Pastikan qty2 tidak lebih besar dari qty_spph
            if ($poDetail->qty < $item['qty_po1']) {
                return response()->json(['error' => 'Qty tidak boleh lebih besar dari Qty1'], 400);
            }

            // Update data
            // $poDetail->qty_po -= $item['qty_po1'];
            // $poDetail->qty_po1 = $item['qty_po1'];
            // $poDetail->save();

            $detailPo = DetailPo::create([
                'id_po' => $item['id_po'],
                'id_detail_pr' => $item['id'],
                'po_qty' => $item['qty_po1'],
                'id_del_po' => 0,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function tracking(Request $request)
    {
        $search = $request->q;

        if (Session::has('selected_warehouse_id')) {
            $warehouse_id = Session::get('selected_warehouse_id');
        } else {
            $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
        }

        $requests = PurchaseRequest::select('purchase_request.*', 'kontrak.nama_pekerjaan as proyek_name')
            ->join('kontrak', 'kontrak.id', '=', 'purchase_request.proyek_id')
            ->paginate(50);

        $proyeks = DB::table('kontrak')->get();

        if ($search) {
            $requests = PurchaseRequest::where('nama_pekerjaan', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $requests = PurchaseRequest::where("warehouse_id", $warehouse_id)->get();

            return response()->json($requests);
        } else {
            return view('admin.trackingpr', compact('requests', 'proyeks'));
        }
    }

    public function trackingwil(Request $request)
    {
        $search = $request->q;

        if (Session::has('selected_warehouse_id')) {
            $warehouse_id = Session::get('selected_warehouse_id');
        } else {
            $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
        }

        $requests = PurchaseRequest::select('purchase_request.*', 'kontrak.nama_pekerjaan as proyek_name')
            ->join('kontrak', 'kontrak.id', '=', 'purchase_request.proyek_id')
            ->paginate(50);

        $proyeks = DB::table('kontrak')->get();

        if ($search) {
            $requests = PurchaseRequest::where('nama_pekerjaan', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $requests = PurchaseRequest::where("warehouse_id", $warehouse_id)->get();

            return response()->json($requests);
        } else {
            return view('admin.trackingwil', compact('requests', 'proyeks'));
        }
    }

    public function updateDetailPo(Request $request)
    {
        $id = $request->id;
        $po = Purchase_Order::where('id', $id)->update([
            'no_po' => $request->no_po,
            'vendor_id' => $request->vendor_id,
            'tanggal_po' => $request->tanggal_po,
            'batas_po' => $request->batas_po,
            'incoterm' => $request->incoterm,
            'pr_id' => $request->pr_id,
            'ref_sph' => $request->ref_sph,
            'no_just' => $request->no_just,
            'no_nego' => $request->no_nego,
            'ref_po' => $request->ref_po,
            'term_pay' => $request->term_pay,
            'garansi' => $request->garansi,
            'proyek_id' => $request->proyek_id,
        ]);
        return response()->json([
            'po' => $po
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $purchase_order = $request->id;
        // $vendors = DB::table('vendor')->get();

        // if (Session::has('selected_warehouse_id')) {
        //     $warehouse_id = Session::get('selected_warehouse_id');
        // } else {
        //     $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
        // }
        $request->validate(
            [
                'no_po' => 'required',
                'vendor_id' => 'required',
                'tanggal_po' => 'required',
                'batas_po' => 'required',
                'incoterm' => 'required',

                'term_pay' => 'required',
                'proyek_id' => 'required',

            ],
            [
                'no_po.required' => 'No. PO harus diisi',
                'vendor_id.required' => 'Vendor harus diisi',
                'tanggal_po.required' => 'Tanggal PO harus diisi',
                'batas_po.required' => 'Batas Akhir PO harus diisi',
                'incoterm.required' => 'Incoterm harus diisi',

                'term_pay.required' => 'Termin Pembayaran harus diisi',
                'proyek_id.required' => 'Proyek harus diisi',
            ]
        );

        if (empty($purchase_order)) {
            $tipe = 0; // 0 = PO biasa, 1 = PO PL
            $po = DB::table('purchase_order')->insertGetId([
                'no_po' => $request->no_po,
                'tipe' => $tipe,
                'vendor_id' => $request->vendor_id,
                'tanggal_po' => $request->tanggal_po,
                'batas_po' => $request->batas_po,
                'incoterm' => $request->incoterm,
                'ref_sph' => $request->ref_sph,
                'no_just' => $request->no_just,
                'no_nego' => $request->no_nego,
                'ref_po' => $request->ref_po,
                'term_pay' => $request->term_pay,
                'garansi' => $request->garansi,
                'proyek_id' => implode(',', $request->proyek_id),
                'pr_id' => implode(',', $request->nomor_pr),
                'catatan_vendor' => $request->catatan_vendor,
                'ongkos' => $request->ongkos,
                'asuransi' => $request->asuransi,
            ]);

            // $prs = DetailPR::where('id_pr', $request->pr_id)->get();


            // foreach ($prs as $pr) {
            //     DetailPo::insert([
            //         'id_po' => $po,
            //         'id_pr' => $request->pr_id,
            //         'id_detail_pr' => $pr->id,
            //     ]);
            // }
            // dd($request->all());
            return redirect()->route('purchase_order.index')->with('success', 'Data PO berhasil ditambahkan');
        } else {

            DB::table('purchase_order')->where('id', $purchase_order)->update([
                'no_po' => $request->no_po,
                'vendor_id' => $request->vendor_id,
                // "tanggal_po"  => Carbon::now()->setTimezone('Asia/Jakarta'),
                // "batas_po" => Carbon::now()->setTimezone('Asia/Jakarta')
                'tanggal_po' => $request->tanggal_po,
                'batas_po' => $request->batas_po,
                'incoterm' => $request->incoterm,
                'pr_id' => implode(',', $request->nomor_pr),
                'ref_sph' => $request->ref_sph,
                'no_just' => $request->no_just,
                'no_nego' => $request->no_nego,
                'ref_po' => $request->ref_po,
                'term_pay' => $request->term_pay,
                'garansi' => $request->garansi,
                'proyek_id' => implode(',', $request->proyek_id),
                'catatan_vendor' => $request->catatan_vendor,
                'ongkos' => $request->ongkos,
                'asuransi' => $request->asuransi,

            ]);

            return redirect()->route('purchase_order.index')->with('success', 'Data PO berhasil diubah');
        }
    }




    public function getByIds(Request $request)
    {
        $prIds = explode(',', $request->pr_ids);
        $purchaseRequests = PurchaseRequest::whereIn('id', $prIds)->get(['id', 'no_pr']);

        return response()->json($purchaseRequests);
    }




    //Cetak PO
    //     public function cetakPo(Request $request)
    // {
    //     $id = $request->id_po;
    //     $po = Purchase_Order::select('purchase_order.*', 'vendor.nama as nama_vendor', 'vendor.alamat as alamat_vendor', 'vendor.telp as telp_vendor', 'vendor.email as email_vendor', 'vendor.fax as fax_vendor',  'kontrak.nama_pekerjaan as nama_proyek', 'purchase_request.no_pr as pr_no')
    //         ->leftjoin('vendor', 'vendor.id', '=', 'purchase_order.vendor_id')
    //         ->leftjoin('kontrak', 'kontrak.id', '=', 'purchase_order.proyek_id')
    //         ->leftjoin('purchase_request', 'purchase_request.id', '=', 'purchase_order.pr_id')
    //         ->where('purchase_order.id', $id)
    //         ->first();
    //     //  dd($po);
    //     $po->batas_po = Carbon::parse($po->batas_po)->isoFormat('D MMMM Y');
    //     $po->tanggal_po = Carbon::parse($po->tanggal_po)->isoFormat('D MMMM Y');
    //    $po->details = DetailPo::where('detail_po.id_po', $po->id)
    // ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_po.id_detail_pr')
    // ->select('detail_pr.*', 'detail_po.id as id_detail_po', 'detail_po.harga as harga_per_unit', 'detail_po.mata_uang as mata_uang', 'detail_po.vat as vat', 'detail_po.batas_akhir as batas', 'detail_po.po_qty') // Menambahkan 'detail_pr.po_qty'
    // ->get();

    //     $po->details = $po->details->map(function ($detail) {
    //         $detail->no_pr = PurchaseRequest::find($detail->id_pr)->no_pr;
    //         return $detail;
    //     });
    //     $po->details = $po->details->map(function ($detail) {
    //         $detail->no_just = DetailPR::find($detail->id)->no_just;
    //         return $detail;
    //     });
    //     $po->details = $po->details->map(function ($detail) {
    //         $detail->no_sph = DetailPR::find($detail->id)->no_sph;
    //         return $detail;
    //     });

    //     $po->details = $po->details->map(function ($detail) {
    //         $detail->no_nego = DetailPR::find($detail->id)->no_nego1;
    //         return $detail;
    //     });

    //     $po->no_nego = $po->details->pluck('no_nego')->unique()->implode(', ');
    //     $po->no_pr = $po->details->pluck('no_pr')->unique()->implode(', ');
    //     $po->no_just = $po->details->pluck('no_just')->unique()->implode(', ');
    //     $po->subtotal = $po->details->sum(function ($detail) {
    //         return $detail->harga_per_unit * $detail->po_qty;
    //     });
    //     $po->ongkos = 0;
    //     $po->asuransi = 0;
    //     $po->total = $po->subtotal + $po->ongkos + $po->asuransi;
    //     $split_proyek = explode(',', $po->proyek_id);
    //     $proyek_names = Kontrak::whereIn('id', $split_proyek)->pluck('nama_pekerjaan')->toArray();
    //     $po->proyek = implode(',', $proyek_names);

    //     // dd($po);
    //     $pdf = PDF::loadview('purchase_order.po_print', compact('po'));
    //     $pdf->setPaper('A4', 'landscape');
    //     $nama = $po->nama_proyek;
    //     $no = $po->no_po;
    //     return $pdf->stream('PO-' . $nama . '(' . $no . ')' . '.pdf');
    //     // return view('purchase_order.po_print', compact('po'));
    // } 



    public function cetakPo(Request $request)
    {
        $id = $request->id_po;
        $po = Purchase_Order::select(
            'purchase_order.*',
            'vendor.nama as nama_vendor',
            'vendor.alamat as alamat_vendor',
            'vendor.telp as telp_vendor',
            'vendor.email as email_vendor',
            'vendor.fax as fax_vendor',
            'kontrak.nama_pekerjaan as nama_proyek',
            'purchase_request.no_pr as pr_no'
        )
            ->leftJoin('vendor', 'vendor.id', '=', 'purchase_order.vendor_id')
            ->leftJoin('kontrak', 'kontrak.id', '=', 'purchase_order.proyek_id')
            ->leftJoin('purchase_request', 'purchase_request.id', '=', 'purchase_order.pr_id')
            ->where('purchase_order.id', $id)
            ->first();

        if (!$po) {
            return back()->withErrors('Data PO tidak ditemukan');
        }

        $po->batas_po = Carbon::parse($po->batas_po)->isoFormat('D MMMM Y');
        $po->tanggal_po = Carbon::parse($po->tanggal_po)->isoFormat('D MMMM Y');

        $po->details = DetailPo::where('detail_po.id_po', $po->id)
            ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_po.id_detail_pr')
            ->select(
                'detail_pr.*',
                'detail_po.id as id_detail_po',
                'detail_po.harga as harga_per_unit',
                'detail_po.mata_uang as mata_uang',
                'detail_po.vat as vat',
                'detail_po.batas_akhir as batas',
                'detail_po.po_qty'
            )
            ->get();

        // Pastikan ada data sebelum diakses
        if ($po->details->isNotEmpty()) {
            $po->details = $po->details->map(function ($detail) {
                $detail->no_pr = optional(PurchaseRequest::find($detail->id_pr))->no_pr;
                $detail->no_just = optional(DetailPR::find($detail->id))->no_just;
                $detail->no_sph = optional(DetailPR::find($detail->id))->no_sph;
                $detail->no_nego = optional(DetailPR::find($detail->id))->no_nego1;
                return $detail;
            });

            $po->no_pr = $po->details->pluck('no_pr')->unique()->implode(', ');
        } else {
            $po->no_pr = '-';
        }

        $currentPr = PurchaseRequest::whereIn('id', explode(',', $po->pr_id))->get();
        $po->no_pr = $currentPr->pluck('no_pr')->implode(', ');

        // Pastikan variabel angka tidak bernilai null
        $po->subtotal = $po->details->sum(function ($detail) {
            return (float) ($detail->harga_per_unit * $detail->po_qty);
        });

        $po->ongkos = (float) ($po->ongkos ?? 0);
        $po->asuransi = (float) ($po->asuransi ?? 0);
        $po->total = $po->subtotal + $po->ongkos + $po->asuransi;

        $split_proyek = explode(',', $po->proyek_id);
        $proyek_names = Kontrak::whereIn('id', $split_proyek)->pluck('nama_pekerjaan')->toArray();
        $po->proyek = implode(', ', $proyek_names);

        $pdf = PDF::loadView('purchase_order.po_print', compact('po'));
        $pdf->setPaper('A4', 'landscape');

        $nama = $po->nama_proyek ?? 'Unknown';
        $no = $po->no_po ?? 'Unknown';

        return $pdf->stream('PO-' . $nama . '(' . $no . ')' . '.pdf');
    }

    //End Cetak PO



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request)
    {
        $delete_po_id = $request->id;

        // Ambil data detail_po yang akan dihapus
        $detail_po = DB::table('detail_po')->where('id_po', $delete_po_id)->first();

        if ($detail_po) {
            // Ambil data detail_pr terkait
            $detail_pr = DB::table('detail_pr')->where('id', $detail_po->id_detail_pr)->first();

            if ($detail_pr) {
                // Cek apakah ada id_del_po di detail_po
                if (!$detail_po->id_del_po) {
                    // Jika tidak ada id_del_po, set qty_po dengan nilai qty dari detail_pr
                    DB::table('detail_pr')->where('id', $detail_pr->id)->update(['qty_po' => $detail_pr->qty]);
                } else {
                    // Ambil semua data detail_po dengan id_po yang sama
                    $detail_po_list = DB::table('detail_po')->where('id_po', $detail_po->id_po)->get();

                    if ($detail_po_list->isNotEmpty()) {
                        // Kelompokkan data detail_po berdasarkan id_detail_pr
                        $grouped = $detail_po_list->groupBy('id_detail_pr');

                        foreach ($grouped as $id_detail_pr => $po_entries) {
                            // Hitung total po_qty untuk id_detail_pr tersebut
                            $total_po_qty = $po_entries->sum('po_qty');

                            // Ambil data detail_pr terkait
                            $detail_pr = DB::table('detail_pr')->where('id', $id_detail_pr)->first();
                            if ($detail_pr) {
                                $new_qty_po = ($detail_pr->qty_po ?? 0) + $total_po_qty;
                                DB::table('detail_pr')->where('id', $detail_pr->id)->update([
                                    'qty_po' => $new_qty_po
                                ]);
                            }
                        }
                    }
                }
            }
        }

        // Perbarui kolom id_po di tabel detail_pr menjadi null
        DB::table('detail_pr')->where('id_po', $delete_po_id)->update(['id_po' => null]);

        // Hapus data dari tabel detail_po yang memiliki id_po sesuai
        DB::table('detail_po')->where('id_po', $delete_po_id)->delete();

        // Setelah memperbarui detail_pr dan menghapus detail_po, hapus data dari tabel purchase_order
        $delete_po = DB::table('purchase_order')->where('id', $delete_po_id)->delete();

        if ($delete_po) {
            return redirect()->route('purchase_order.index')->with('success', 'Data PO berhasil dihapus, id_po pada detail_pr diubah menjadi null, dan detail_po berhasil dihapus');
        } else {
            return redirect()->route('purchase_order.index')->with('error', 'Data PO gagal dihapus');
        }
    }
    //End Hapus Data

    // //Hapus Data
    // public function destroy(Request $request)
    // {
    //     $delete_po_id = $request->id;

    //     // Perbarui kolom id_spph di tabel detail_pr menjadi null
    //     $update_detail_pr = DB::table('detail_pr')
    //         ->where('id_po', $delete_po_id)
    //         ->update(['id_po' => null]);

    //     // Hapus data dari tabel detail_spph yang memiliki id_spph sesuai
    //     $delete_detail_po = DB::table('detail_po')
    //         ->where('id_po', $delete_po_id)
    //         ->delete();

    //     // Setelah memperbarui detail_pr dan menghapus detail_spph, hapus data dari tabel spph
    //     $delete_po = DB::table('purchase_order')->where('id', $delete_po_id)->delete();

    //     if ($delete_po) {
    //         return redirect()->route('purchase_order.index')->with('success', 'Data PO berhasil dihapus, id_po pada detail_pr diubah menjadi null, dan detail_po berhasil dihapus');
    //     } else {
    //         return redirect()->route('purchase_order.index')->with('error', 'Data PO gagal dihapus');
    //     }
    // }
    // //End Hapus Data


    // Controller PO/PL

    public function storePOPL(Request $request)
    {
        $purchase_order = $request->id;
        $request->validate(
            [
                'no_po' => 'required',
                // 'vendor_id' => 'nullable',
                'tanggal_po' => 'required',
                'batas_po' => 'required',
                'incoterm' => 'required',
                // 'pr_id' => 'required',
                'term_pay' => 'required',
                'proyek_id' => 'required',
                'ref_po' => 'nullable',

            ],
            [
                'no_po.required' => 'No. PO harus diisi',
                // 'vendor_id.required' => 'Vendor harus diisi',
                'tanggal_po.required' => 'Tanggal PO harus diisi',
                'batas_po.required' => 'Batas Akhir PO harus diisi',
                'incoterm.required' => 'Incoterm harus diisi',
                // 'pr_id.required' => 'PR harus diisi',
                'term_pay.required' => 'Termin Pembayaran harus diisi',
                'proyek_id.required' => 'Proyek harus diisi',
            ]
        );

        if (empty($purchase_order)) {
            $tipe = 1; // 0 = PO biasa, 1 = PO PL
            $po = DB::table('purchase_order')->insertGetId([
                'tipe' => $tipe,
                'no_po' => $request->no_po,
                'tanggal_po' => $request->tanggal_po,
                'batas_po' => $request->batas_po,
                'incoterm' => $request->incoterm,
                'ref_po' => $request->ref_po,
                'term_pay' => $request->term_pay,
                'garansi' => $request->garansi,
                'proyek_id' => $request->proyek_id,
                'pr_id' => $request->pr_id,
                'catatan_vendor' => $request->catatan_vendor
            ]);

            $prs = DetailPR::where('id_pr', $request->pr_id)->get();


            foreach ($prs as $pr) {
                DetailPo::insert([
                    'id_po' => $po,
                    'id_pr' => $request->pr_id,
                    'id_detail_pr' => $pr->id,
                ]);
            }

            return redirect()->route('product.showPOPL')->with('success', 'Data PO berhasil ditambahkan');
        } else {
            DB::table('purchase_order')->where('id', $purchase_order)->update([
                'no_po' => $request->no_po,
                // 'vendor_id' => $request->vendor_id,
                'tanggal_po' => $request->tanggal_po,
                'batas_po' => $request->batas_po,
                'incoterm' => $request->incoterm,
                'pr_id' => $request->pr_id,
                'ref_po' => $request->ref_po,
                'term_pay' => $request->term_pay,
                'garansi' => $request->garansi,
                'proyek_id' => $request->proyek_id,
                'catatan_vendor' => $request->catatan_vendor

            ]);
            return redirect()->route('product.showPOPL')->with('success', 'Data PO berhasil diubah');
        }
    }

    public function destroyPOPL(Request  $request)
    {
        $delete_po = $request->id;
        $delete_po = DB::table('purchase_order')->where('id', $delete_po)->delete();

        if ($delete_po) {
            return redirect()->route('product.showPOPL')->with('success', 'Data PO berhasil dihapus');
        } else {
            return redirect()->route('product.showPOPL')->with('error', 'Data PO gagal dihapus');
        }

        return redirect()->route('product.showPOPL');
    }

    // Hapus Multiple CheckBox
    public function hapusMultiplePo(Request $request)
    {
        if ($request->has('ids')) {
            $ids = $request->input('ids');

            // Ambil semua data detail_po yang akan dihapus
            $detail_po_list = DB::table('detail_po')->whereIn('id_po', $ids)->get();

            if ($detail_po_list->isNotEmpty()) {
                // Kelompokkan data detail_po berdasarkan id_detail_pr
                $grouped = $detail_po_list->groupBy('id_detail_pr');

                foreach ($grouped as $id_detail_pr => $po_entries) {
                    // Hitung total po_qty untuk id_detail_pr tersebut
                    $total_po_qty = $po_entries->sum('po_qty');

                    // Ambil data detail_pr terkait
                    $detail_pr = DB::table('detail_pr')->where('id', $id_detail_pr)->first();
                    if ($detail_pr) {
                        // Update qty_po dengan menambahkan kembali total_po_qty
                        $new_qty_po = ($detail_pr->qty_po ?? 0) + $total_po_qty;
                        DB::table('detail_pr')->where('id', $detail_pr->id)->update([
                            'qty_po' => $new_qty_po
                        ]);
                    }
                }
            }

            // Perbarui kolom id_po di tabel detail_pr menjadi null
            DB::table('detail_pr')
                ->whereIn('id_po', $ids)
                ->update(['id_po' => null]);

            // Hapus data dari tabel detail_po yang memiliki id_po sesuai
            DB::table('detail_po')
                ->whereIn('id_po', $ids)
                ->delete();

            // Hapus data dari tabel po
            Purchase_Order::whereIn('id', $ids)->delete();

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }



    // Hapus Multiple CheckBox
    public function hapusMultiplePo_Pl(Request $request)
    {
        if ($request->has('ids')) {
            Purchase_Order::whereIn('id', $request->input('ids'))->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    // Hapus Multiple CheckBox
    public function hapusMultipleTracking(Request $request)
    {
        if ($request->has('ids')) {
            PurchaseRequest::whereIn('id', $request->input('ids'))->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }




    // CONTROLLER KEUANGAN
    public function aprrovedPO(Request  $request)
    {
        $search = $request->q;
        if (Session::has('selected_warehouse_id')) {
            $warehouse_id = Session::get('selected_warehouse_id');
        } else {
            $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
        }

        $purchases = Purchase_Order::select('purchase_order.*', 'vendor.nama as vendor_name', 'kontrak.nama_pekerjaan as proyek_name', 'purchase_request.no_pr as pr_no')
            ->join('vendor', 'vendor.id', '=', 'purchase_order.vendor_id')
            ->leftjoin('kontrak', 'kontrak.id', '=', 'purchase_order.proyek_id')
            ->leftjoin('purchase_request', 'purchase_request.id', '=', 'purchase_order.pr_id')
            ->paginate(50);
        $vendors = DB::table('vendor')->get();
        $proyeks = DB::table('kontrak')->get();


        if ($search) {
            $purchases = Purchase_Order::where('no_po', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $purchases = Purchase_Order::where("warehouse_id", $warehouse_id)->get();

            return response()->json($purchases);
        } else {
            $prs = PurchaseRequest::all();
            return view('keuangan.approvedPO', compact('purchases', 'vendors', 'proyeks', 'prs'));
        }
    }

    public function aprrovedPO_PL(Request  $request)
    {
        $search = $request->q;
        if (Session::has('selected_warehouse_id')) {
            $warehouse_id = Session::get('selected_warehouse_id');
        } else {
            $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
        }

        $purchases = Purchase_Order::select('purchase_order.*', 'kontrak.nama_pekerjaan as proyek_name', 'purchase_request.no_pr as pr_no')
            ->where('purchase_order.tipe', '1')
            ->leftjoin('kontrak', 'kontrak.id', '=', 'purchase_order.proyek_id')
            ->leftjoin('purchase_request', 'purchase_request.id', '=', 'purchase_order.pr_id')
            ->paginate(50);
        $vendors = DB::table('vendor')->get();
        $proyeks = DB::table('kontrak')->get();


        if ($search) {
            $purchases = Purchase_Order::where('no_po', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $purchases = Purchase_Order::where("warehouse_id", $warehouse_id)->get();

            return response()->json($purchases);
        } else {
            $prs = PurchaseRequest::all();
            return view('keuangan.approvedPOPL', compact('purchases', 'vendors', 'proyeks', 'prs'));
        }
    }


    


    //End Detail Product


    //Detail Product
    public function getProductPR(Request $request)
    {
        // dd($request);
        $id_pr = $request->id_pr; // Ambil id_pr dari request
        $proyek = strtolower($request->proyek);

        // Ambil DetailPR yang sesuai dengan id_pr
        $products = DetailPR::where('id_pr', $id_pr)->get();


        // Proses setiap produk
        $products = $products->map(function ($item) {
            $item->spek = $item->spek ? $item->spek : '';
            $item->keterangan = $item->keterangan ? $item->keterangan : '';
            $item->kode_material = $item->kode_material ? $item->kode_material : '';
            $item->nomor_nego = Nego::where('id', $item->id_nego)->first()->nomor_nego ?? '';
            $item->nomor_spph = Spph::where('id', $item->spph_id)->first()->nomor_spph ?? '';
            $item->pr_no = PurchaseRequest::where('id', $item->id_pr)->first()->no_pr ?? '';
            $item->po_no = Purchase_Order::where('id', $item->id_po)->first()->no_po ?? '';
            $item->nama_pekerjaan = Kontrak::where('id', $item->id_proyek)->first()->nama_pekerjaan ?? '';

            // Baru, hitung sisa Nego by QTY asli - jumlah di DetailNego by id_pr_detail
            $item->qty_po = $item->qty - DetailPo::where('id_detail_pr', $item->id)->sum('po_qty');
            return $item;
        });

        // Filter produk berdasarkan nama proyek
        $products = $products->filter(function ($item) use ($proyek) {
            return strpos(strtolower($item->nama_pekerjaan), $proyek) !== false;
        });

        // Kembalikan hasil dalam bentuk JSON
        return response()->json([
            'products' => $products
        ]);
    }
    //End Detail Product
}
