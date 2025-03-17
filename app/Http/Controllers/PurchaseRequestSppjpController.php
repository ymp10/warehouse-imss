<?php

namespace App\Http\Controllers;

use App\Models\DetailLoi;
use App\Models\DetailNego;
use App\Models\DetailPo;
use App\Models\DetailPR;
use App\Models\DetailPRSppjp;
use App\Models\DetailSpph;
use App\Models\Keproyekan;
use App\Models\Kontrak;
use App\Models\Lppb;
use App\Models\Vendor;
use App\Models\Purchase_Order;
use App\Models\PurchaseRequest;
use App\Models\RegistrasiBarang;
use App\Models\Spph;
use App\Models\User;
use App\Models\Notification;
use App\Models\PenerimaanBarang;
use App\Models\PurchaseRequestSppjp;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use stdClass;

class PurchaseRequestSppjpController extends Controller
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

        $requests = PurchaseRequestSppjp::select('purchase_request_sppjp.*', 'kontrak.nama_pekerjaan as proyek_name', 'kontrak.nomor_kontrak as dasar_pr_sppjp')
            ->join('kontrak', 'kontrak.id', '=', 'purchase_request_sppjp.proyek_id')
            ->orderBy('purchase_request_sppjp.id', 'asc')
            ->paginate(10);

        $proyeks = DB::table('kontrak')->get();
        //  dd($requests);


        if ($search) {
            $requests = PurchaseRequestSppjp::where('nama_pekerjaan', 'LIKE', "%$search%")->paginate(10);
        }

        if ($request->format == "json") {
            $requests = PurchaseRequestSppjp::where("warehouse_id", $warehouse_id)->get();

            return response()->json($requests);
        } else {

            //looping the paginate
            foreach ($requests as $request) {
                $detail_pr = DetailPR::where('id_pr', $request->id)->get();
                //if detail_pr empty then editable true
                if ($detail_pr->isEmpty()) {
                    $request->editable = TRUE;
                } else {
                    //looping detail_pr then check in detailspph with id_detail_pr exist
                    foreach ($detail_pr as $detail) {
                        $detail_spph = DetailSpph::where('id_detail_pr', $detail->id)->first();
                        $po = Purchase_Order::where('id', $detail->id_po)->first();
                        if ($po && $po->tipe == '1') {
                            $request->editable = FALSE;
                            break;
                        } else {
                            if ($detail_spph) {
                                $request->editable = FALSE;
                                break;
                            } else {
                                $request->editable = TRUE;
                            }
                        }
                    }
                }
            }

            return view('purchase_request_sppjp.purchase_request_sppjp', compact('requests', 'proyeks'));
        }
    }

    public function indexApps(Request $request)
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
            return view('home.apps.wilayah.purchase_request', compact('requests', 'proyeks'));
        }
    }

    public function indexPr()
    {
        $user = Auth::user();
        $requests = PurchaseRequest::query();

        if ($user->role == 2) {
            // Pengguna dari Wilayah 1
            $requests->where('no_pr', 'like', '%wil1%');
        } elseif ($user->role == 3) {
            // Pengguna dari Wilayah 2
            $requests->where('no_pr', 'like', '%wil2%');
        } elseif ($user->role == 0) {
            // Admin, tampilkan semua data
            // Tidak ada filter tambahan
        } else {
            // Role tidak dikenali, jangan tampilkan apapun
            $requests->whereRaw('0 = 1');
        }

        $requests = $requests->get(); // Ambil hasil query

        return view('purchase_requests.index', compact('requests'));
    }

    public function getQtyStatus($id, $item)
    {
        // Relasi ke detail LOI dan menjumlahkan loi_qty
        $item->selisih_qty_loi = DetailLoi::where('id_detail_pr', $item->id)->sum('loi_qty');

        $item->selisih_qty = DetailSpph::where('id_detail_pr', $item->id)->sum('spph_qty');

        $item->selisih_qty_nego = DetailNego::where('id_detail_pr', $item->id)->sum('nego_qty');

        $item->selisih_qty_po = DetailPo::where('id_detail_pr', $item->id)->sum('po_qty');

        return $item;
    }


    public function getDetailPrSppjp(Request $request)
    {
        $id = $request->id;
        $pr = PurchaseRequestSppjp::select('purchase_request_sppjp.*', 'kontrak.nama_pekerjaan as nama_proyek')
            ->join('kontrak', 'kontrak.id', '=', 'purchase_request_sppjp.proyek_id')
            ->where('purchase_request_sppjp.id', $id)
            ->first();
        $pr->details = DetailPRSppjp::where('id_pr_sppjp', $id)->get();
        // $pr->details = DetailPR::where('id_pr', $id)->leftJoin('kode_material', 'kode_material.id', '=', 'detail_pr.kode_material_id')->get();
        $pr->details = $pr->details->map(function ($item) use ($id) {
            $item->spek = $item->spek ? $item->spek : '';
            $item->keterangan = $item->keterangan ? $item->keterangan : '';
            $item->kode_material = $item->kode_material ? $item->kode_material : '';
            $item->nomor_spph = Spph::where('id', $item->id_spph)->first()->nomor_spph ?? '';
            $item->no_po = Purchase_Order::where('id', $item->id_po)->first()->no_po ?? '';
            $item->userRole = User::where('id', $item->user_id)->first()->role ?? '';
            $item->no_sph = $item->no_sph ? $item->no_sph : '';
            $item->tanggal_sph = $item->tanggal_sph ? $item->tanggal_sph : '';
            $item->no_just = $item->no_just ? $item->no_just : '';
            $item->tanggal_just = $item->tanggal_just ? $item->tanggal_just : '';
            $item->no_nego1 = $item->no_nego1 ? $item->no_nego1 : '';
            $item->tanggal_nego1 = $item->tanggal_nego1 ? $item->tanggal_nego1 : '';
            $item->batas_nego1 = $item->batas_nego1 ? $item->batas_nego1 : '';
            $item->no_nego2 = $item->no_nego2 ? $item->no_nego2 : '';
            $item->tanggal_nego2 = $item->tanggal_nego2 ? $item->tanggal_nego2 : '';
            $item->batas_nego2 = $item->batas_nego2 ? $item->batas_nego2 : '';
            $item->batas_akhir = Purchase_Order::leftjoin('detail_po', 'detail_po.id_po', '=', 'purchase_order.id')->where('detail_po.id_detail_pr', $item->id)->first()->batas_akhir ?? '-';

            $ekspedisi = RegistrasiBarang::where('id_barang', $item->id)->first();
            if ($ekspedisi) {
                $keterangan = $ekspedisi->keterangan;
                $tanggal = $ekspedisi->created_at;
                $tanggal = Carbon::parse($tanggal)->isoFormat('D MMMM Y');
                $keterangan = $keterangan . ', ' . $tanggal;
            } else {
                $keterangan = null;
            }
            $item->ekspedisi = $keterangan;

            //qc
            if ($ekspedisi) {
                $qc = Lppb::where('id_registrasi_barang', $ekspedisi->id)->first();
            } else {
                $qc = null;
            }

            if ($qc) {
                $penerimaan = $qc->penerimaan;
                $hasil_ok = $qc->hasil_ok;
                $hasil_nok = $qc->hasil_nok;
                $tanggal_qc = $qc->created_at;
                $tanggal_qc = Carbon::parse($qc->created_at)->isoFormat('D MMMM Y');
                $qc = new stdClass();
                $qc->penerimaan = $penerimaan;
                $qc->hasil_ok = $hasil_ok;
                $qc->hasil_nok = $hasil_nok;
                $qc->tanggal_qc = $tanggal_qc;
            } else {
                $penerimaan = null;
                $hasil_ok = null;
                $hasil_nok = null;
                $tanggal_qc = null;
                $qc = null;
            }

            $item->qc = $qc;

            //countdown = waktu - date now
            $targetDate = Carbon::parse($item->waktu);
            $currentDate = Carbon::now();
            $diff = $currentDate->diff($targetDate);
            $remainingDays = $diff->days;

            $referenceDate = Carbon::parse($item->waktu); // Change this to your desired reference date

            if ($currentDate->lessThan($referenceDate)) {
                // If the current date is before the reference date
                $item->countdown = "$remainingDays  Hari Sebelum Waktu Penyelesaian";
                $item->backgroundcolor = "#FF0000"; // Red background
            } elseif ($currentDate->greaterThanOrEqualTo($referenceDate)) {
                // If the current date is on or after the reference date
                $item->countdown = "$remainingDays Hari Setelah Waktu Penyelesaian";
                $item->backgroundcolor = "#008000"; // Green background
            }

            $item = $this->getQtyStatus($id, $item);

            return $item;
        });

        return response()->json([
            'pr' => $pr
        ]);
    }

    public function getDetailBarang(Request $request)
    {
        $id = $request->id;
        $pr = PurchaseRequestSppjp::select('purchase_request_sppjp.*', 'kontrak.nama_pekerjaan as nama_proyek')
            ->join('kontrak', 'kontrak.id', '=', 'purchase_request_sppjp.proyek_id')
            ->where('purchase_request_sppjp.id', $id)
            ->first();
        $pr->details = DetailPR::where('id_pr_sppjp', $id)->get();
        return response()->json([
            'pr' => $pr
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
        //Store untuk menambah data
        $purchase_request = $request->id;
        $request->validate(
            [
                'proyek_id' => 'required',
                'no_pr_sppjp' => 'required',
                'dasar_pr_sppjp' => 'required',
                'tgl_pr_sppjp' => 'required',
            ],
            [
                'proyek_id.required' => 'Proyek harus diisi',
                'no_pr_sppjp.required' => 'No SPPJP harus diisi',
                'dasar_pr_sppjp.required' => 'Dasar SPPJP harus diisi',
                'tgl_pr_sppjp.required' => 'Tanggal SPPJP harus diisi',
            ]
        );

        if (empty($purchase_request_sppjp)) {
            DB::table('purchase_request_sppjp')->insert([
                'proyek_id' => $request->proyek_id,
                'no_pr_sppjp' => $request->no_pr_sppjp,
                'dasar_pr_sppjp' => $request->dasar_pr_sppjp,
                'tgl_pr_sppjp' => $request->tgl_pr_sppjp,
                'id_user' => auth()->user()->id,
            ]);

            return redirect()->route('purchase_request_sppjp.index')->with('success', 'SPPJP berhasil ditambahkan');
        } else {
            DB::table('purchase_request_sppjp')->where('id', $purchase_request_sppjp)->update([
                'proyek_id' => $request->proyek_id,
                'no_pr_sppjp' => $request->no_pr_sppjp,
                'dasar_pr_sppjp' => $request->dasar_pr_sppjp,
                'tgl_pr_sppjp' => $request->tgl_pr_sppjp,
            ]);


            return redirect()->route('purchase_request_sppjp.index')->with('success', 'SPPJP berhasil diupdate');
        }

        // return redirect()->route('purchase_request.index')->with('success', 'Purchase Request berhasil disimpan');

    }



    

    public function cetakPr(Request $request)
    {
        $id = $request->id;
        $pr = PurchaseRequest::where('purchase_request.id', $id)
            ->leftjoin('kontrak', 'kontrak.id', '=', 'purchase_request.proyek_id')->first();

        $pr->pic = User::where('id', $pr->id_user)->first()->name ?? '-';

        // Deteksi wilayah berdasarkan no_pr dengan regex dan case-insensitive
        if (preg_match('/wil1|wilayah1/i', $pr->no_pr)) {
            $pr->role = "Wilayah 1";
            $pr->kadiv = "EKO PRASETYO";
        } else {
            $pr->role = "Wilayah 2";
            $pr->kadiv = 'HARI SUBEKTI';
        }

        $pr->purchases = DetailPR::select('detail_pr.*', 'purchase_request.*')
            ->leftjoin('purchase_request', 'purchase_request.id', '=', 'detail_pr.id_pr')
            ->where('purchase_request.id', $id)
            ->get();

        $pdf = Pdf::loadview('purchase_request.pr_print', compact('pr'));
        $pdf->setPaper('A4', 'landscape');
        $no = $pr->no_pr;
        return $pdf->stream('PR-' . $no . '.pdf');
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

    // Hapus Multiple CheckBox
    public function hapusMultiplePr(Request $request)
    {
        if ($request->has('ids')) {
            $ids = $request->input('ids');

            // Hapus detail_pr terlebih dahulu
            DetailPr::whereIn('id_pr', $ids)->delete();

            // Hapus PurchaseRequest
            PurchaseRequest::whereIn('id', $ids)->delete();

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    

    //edit detail
    public function editDetail(Request $request)
    {
        if (!$request->stock) {
            return response()->json([
                'success' => false,
                'message' => 'QTY tidak boleh kosong'
            ]);
        }
        $request->validate([
            'lampiran' => 'nullable',
            // 'lampiran' => 'nullable|file|mimes:pdf|max:500',
        ]);

        if ($request->file('lampiran')) {
            $file = $request->file('lampiran');
            // dd($file);
            $fileName = rand() . '.' . $file->getClientOriginalExtension();
            // dd($fileName);
            $file->move(public_path('lampiran'), $fileName);
        } else {
            $fileName = null;
        }
        // Validasi data yang diterima dari request
        $request->validate([
            'id_pr' => 'required', // Pastikan id_sr wajib ada
            // 'id' => 'required',
            'kode_material' => 'nullable',
            'uraian' => 'required',
            'spek' => 'required',
            'qty' => 'nullable',
            'satuan' => 'nullable',
            'waktu' => 'nullable',
            'keterangan' => 'nullable',
            'lampiran' => 'nullable',
        ]);


        $id = $request->id;


        // Cek apakah id_sr yang diberikan valid

        // dd($detailSR);
        if (!$id) {
            // Alihkan ke fungsi createDetailSr jika detail SR tidak ditemukan
            return $this->updateDetailPr($request);
            // dd($request->all());
        }
        $detailPR = DetailPR::where('id', $id)->first();
        // Update data detail SR
        $detailPR->update([
            'id_pr' => $request->id_pr,
            'id_proyek' => $request->id_proyek,
            'kode_material' => $request->kode_material,
            'uraian' => $request->uraian,
            'spek' => $request->spek,
            'satuan' => $request->satuan,
            'qty' => $request->stock,
            // 'qty_spph' => $request->stock,
            // 'qty_loi' => $request->stock,
            // 'qty_nego' => $request->stock,
            // 'qty_po' => $request->stock,
            // 'qty2' => $request->stock,
            'waktu' => $request->waktu,
            'keterangan' => $request->keterangan,
            'lampiran' => $fileName,

        ]);

        $pr = DB::table('purchase_request')->where('id', $request->id_pr)->first();
        // TODO: tambah func disini
        $pr->details = DetailPR::where('id_pr', $request->id_pr)->get();
        $pr->details = $pr->details->map(function ($item) use ($request) {
            $item = $this->getQtyStatus($request->id_pr, $item);

            return $item;
        });
        return response()->json([
            'success' => true,
            'message' => 'Data detail SR berhasil diupdate.',
            'pr' => $pr // Mengembalikan data detail SR yang telah diupdate
        ]);
    }
    //end edit detail



    public function updateDetailPr(Request $request)
    {
        if (!$request->stock) {
            return response()->json([
                'success' => false,
                'message' => 'QTY tidak boleh kosong'
            ]);
        }
        $request->validate([
            'lampiran' => 'nullable',
            // 'lampiran' => 'nullable|file|mimes:pdf|max:500',
        ]);

        if ($request->file('lampiran')) {
            $file = $request->file('lampiran');
            // dd($file);
            $fileName = rand() . '.' . $file->getClientOriginalExtension();
            // dd($fileName);
            $file->move(public_path('lampiran'), $fileName);
        } else {
            $fileName = null;
        }
        $maxIdDel = DetailPR::max('id_del');  // Mengambil nilai maksimum id_del yang ada
        $idDel = $maxIdDel + 1;  // Menambahkan 1 pada nilai maksimum untuk mendapatkan id_del yang baru
        $insert = DetailPR::create([
            'id_pr' => $request->id_pr,
            'id_proyek' => $request->id_proyek,
            'kode_material' => $request->kode_material,
            'uraian' => $request->uraian,
            'spek' => $request->spek,
            'satuan' => $request->satuan,
            'qty' => $request->stock,
            // 'qty_spph' => $request->stock,
            // 'qty_loi' => $request->stock,
            // 'qty_nego' => $request->stock,
            // 'qty_po' => $request->stock,
            // 'qty2' => $request->stock,
            'waktu' => $request->waktu,
            'keterangan' => $request->keterangan,
            'lampiran' => $fileName,
            'id_del' => $idDel,  // Menggunakan id_del yang baru dihitung
        ]);

        if (!$insert) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan detail PR'
            ]);
        }

        $pr = DB::table('purchase_request')->where('id', $request->id_pr)->first();
        $pr->details = DetailPR::where('id_pr', $request->id_pr)->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambahkan detail PR',
            'pr' => $pr
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        $delete_pr = $request->id;
        $delete_pr = DB::table('purchase_request')->where('id', $delete_pr)->delete();
        $delete_detail_pr = DetailPR::where('id_pr', $request->id)->delete();
        // $delete_detail_po = DetailPo::where('id_pr', $request->id)->delete();
        // $delete_detail_spph = Spph::leftjoin('detail_spph', 'detail_spph.spph_id', '=', 'spph.id')->where('detail_spph.id_detail_pr', $request->id)->delete();

        // if ($delete_pr && $delete_detail_pr && $delete_detail_po && $delete_detail_spph) {
        if ($delete_pr) {
            return redirect()->route('purchase_request.index')->with('success', 'Data Request berhasil dihapus');
        } else {
            return redirect()->route('purchase_request.index')->with('error', 'Data Request gagal dihapus');
        }

        return redirect()->route('purchase_request.index');
    }


    public function hapusDetail(Request $request, $id)
    {
        // Mendapatkan nilai id_pr sebelum menghapus data
        $id_pr = DetailPR::where('id', $id)->value('id_pr');

        // Menghapus data purchase request dan detailnya
        $delete_detail_pr = DetailPR::where('id', $id)->delete();

        // Periksa apakah permintaan utama berhasil dihapus dan kembalikan respons yang sesuai
        if ($delete_detail_pr) {
            return response()->json(['success' => 'Data Request berhasil dihapus', 'deletedId' => $id, 'id_pr' => $id_pr]);
        } else {
            return response()->json(['error' => 'Data Request gagal dihapus'], 500);
        }
    }












    public function detailPrSave(Request $request)
    {
        $id_pr = $request->id;
        $id = $request->id_pr;
        $no_sph = $request->no_sph;
        $tanggal_sph = $request->tanggal_sph;
        $no_just = $request->no_just;
        $tanggal_just = $request->tanggal_just;
        $no_nego1 = $request->no_nego1;
        $tanggal_nego1 = $request->tanggal_nego1;
        $batas_nego1 = $request->batas_nego1;
        $no_nego2 = $request->no_nego2;
        $tanggal_nego2 = $request->tanggal_nego2;
        $batas_nego2 = $request->batas_nego2;

        DetailPR::where('id', $id_pr)->update([
            'no_sph' => $no_sph,
            'tanggal_sph' => $tanggal_sph,
            'no_just' => $no_just,
            'tanggal_just' => $tanggal_just,
            'no_nego1' => $no_nego1,
            'tanggal_nego1' => $tanggal_nego1,
            'batas_nego1' => $batas_nego1,
            'no_nego2' => $no_nego2,
            'tanggal_nego2' => $tanggal_nego2,
            'batas_nego2' => $batas_nego2,
        ]);

        $pr = PurchaseRequest::where('id', $id)->first();
        $pr->details = DetailPR::where('id_pr', $pr->id)->get();
        // $pr->details = DetailPR::where('id_pr', $id)->leftJoin('kode_material', 'kode_material.id', '=', 'detail_pr.kode_material_id')->get();
        $pr->details = $pr->details->map(function ($item) {
            $item->spek = $item->spek ? $item->spek : '';
            $item->keterangan = $item->keterangan ? $item->keterangan : '';
            $item->kode_material = $item->kode_material ? $item->kode_material : '';
            $item->nomor_spph = Spph::where('id', $item->id_spph)->first()->nomor_spph ?? '';
            $item->no_po = Purchase_Order::where('id', $item->id_po)->first()->no_po ?? '';

            $item->no_sph = $item->no_sph ?? '';
            $item->tanggal_sph = $item->tanggal_sph ?? '';
            $item->no_just = $item->no_just ?? '';
            $item->tanggal_just = $item->tanggal_just ?? '';
            $item->no_nego1 = $item->no_nego1 ?? '';
            $item->tanggal_nego1 = $item->tanggal_nego1 ?? '';
            $item->batas_nego1 = $item->batas_nego1 ?? '';
            $item->no_nego2 = $item->no_nego2 ?? '';
            $item->tanggal_nego2 = $item->tanggal_nego2 ?? '';
            $item->batas_nego2 = $item->batas_nego2 ?? '';
            return $item;
        });
        return response()->json([
            'pr' => $pr
        ]);
    }

    // edit detail produk oleh engginering

    public function showEditPr(Request $request)
    {
        $search = $request->q;

        if (Session::has('selected_warehouse_id')) {
            $warehouse_id = Session::get('selected_warehouse_id');
        } else {
            $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
        }

        $requests = PurchaseRequest::select('purchase_request.*', 'kontrak.nama_pekerjaan as proyek_name')
            ->join('kontrak', 'kontrak.id', '=', 'purchase_request.proyek_id')
            ->orderBy('purchase_request.id', 'asc')
            ->paginate(50);

        $proyeks = DB::table('kontrak')->get();
        //  dd($requests);

        if ($search) {
            $requests = PurchaseRequest::where('nama_proyek', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $requests = PurchaseRequest::where("warehouse_id", $warehouse_id)->get();

            return response()->json($requests);
        } else {

            //looping the paginate
            foreach ($requests as $request) {
                $detail_pr = DetailPR::where('id_pr', $request->id)->get();
                //if detail_pr empty then editable true
                if ($detail_pr->isEmpty()) {
                    $request->editable = TRUE;
                } else {
                    //looping detail_pr then check in detailspph with id_detail_pr exist
                    foreach ($detail_pr as $detail) {
                        $detail_spph = DetailSpph::where('id_detail_pr', $detail->id)->first();
                        $po = Purchase_Order::where('id', $detail->id_po)->first();
                        if ($po && $po->tipe == '1') {
                            $request->editable = FALSE;
                            break;
                        } else {
                            if ($detail_spph) {
                                $request->editable = FALSE;
                                break;
                            } else {
                                $request->editable = TRUE;
                            }
                        }
                    }
                }
            }
            return view('engineering.index', compact('requests', 'proyeks'));
        }
    }
    public function editPrEng(Request $request)
    {
        $id = $request->id;
        $id_pr = $request->id_pr;
        $kode_material = $request->kode_material;
        $spek = $request->spek;

        $edit = DetailPR::where('id', $id)->update([
            'kode_material' => $kode_material,
            'spek' => $spek,
        ]);

        if (!$edit) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengedit detail PR'
            ]);
        }

        $pr = PurchaseRequest::where('id', $request->id_pr)->first();
        $pr->details = DetailPR::where('id_pr', $pr->id_pr)->get();

        $pr->details = $pr->details->map(function ($item) {
            $item->spek = $item->spek ? $item->spek : '';
            $item->keterangan = $item->keterangan ? $item->keterangan : '';
            $item->kode_material = $item->kode_material ? $item->kode_material : '';
            $item->nomor_spph = Spph::where('id', $item->id_spph)->first()->nomor_spph ?? '';
            $item->no_po = Purchase_Order::where('id', $item->id_po)->first()->no_po ?? '';
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengedit detail PR',
            'pr' => $pr
        ]);
    }
    public function uploadFile(Request $request)
    {
        $request->validate([
            'lampiran' => 'nullable|file|mimes:pdf|max:500', // Menetapkan batasan tipe file dan ukuran
            // 'detail_id' => 'required|exists:details,id',
        ]);

        $detailId = $request->input('detail_id');
        $file = $request->file('lampiran');

        // Generate nama unik untuk file
        $fileName = 'lampiran' . time() . '_' . $file->getClientOriginalName();

        // Pindahkan file ke penyimpanan yang diinginkan (misalnya, storage/app/attachments)
        $file->storeAs('lampiran', $fileName);

        // Simpan informasi file di database, misalnya menyimpan nama file di kolom 'attachment' di tabel 'details'
        DetailPR::where('id', $detailId)->update(['lampiran' => $fileName]);

        return redirect()->back()->with('success', 'File berhasil diupload');
    }

    public function penerimaan_barang()
    {
        // $items = PurchaseRequest::select()
        // ->paginate(10);
        // $items = PurchaseRequest::with(['detailPr' => function ($query) {
        //     $query->join('purchase_order', 'detail_pr.id_po', '=', 'purchase_order.id')
        //         ->select('detail_pr.*', 'purchase_order.no_po');
        // }])->paginate(10);

        $items = DetailPo::select(
            'detail_po.*',
            // 'purchase_request.no_pr',
            'purchase_order.no_po',
            // 'purchase_request.nomor_lppb',
            // 'purchase_request.tanggal_lppb',
        )
            // ->leftjoin('purchase_request','purchase_request.id', '=', 'detail_po.id_pr')
            ->leftjoin('purchase_order', 'purchase_order.id', '=', 'detail_po.id_po')
            ->groupBy('id_po')->paginate(10);

        // dd($items);


        foreach ($items as $item) {
            $item->tipe = $item->tipe == 0 ? 'PO' : 'PO/PL';
            $item->diterima = RegistrasiBarang::where('id_barang', $item->id)->first() ? 1 : 0;
            $keterangan = RegistrasiBarang::where('id_barang', $item->id)->first() ? RegistrasiBarang::where('id_barang', $item->id)->first()->keterangan : '';
            $item->keterangan = RegistrasiBarang::where('id_barang', $item->id)->first() ? RegistrasiBarang::where('id_barang', $item->id)->first()->keterangan : '';

            $po = Purchase_Order::where('id', $item->id_po)->first();
            // dd($po);
            $split_proyek = explode(',', $po->proyek_id);
            // dd($split_proyek);
            $proyek_names = Kontrak::whereIn('id', $split_proyek)->pluck('nama_pekerjaan')->toArray();
            // dd($proyek_names);
            $item->proyeks = implode(',', $proyek_names);
            // dd($item->proyek);

            $po = Purchase_Order::where('id', $item->id_po)->first();
            $id_pr = explode(',', $po->pr_id);
            $item->no_pr = PurchaseRequest::whereIn('id', $id_pr)->pluck('no_pr')->implode(', ');
        }

        // dd($items);

        return view('penerimaan_barang.index', compact('items'));
    }

    //     public function penerimaan_barang()
    // {
    //     $items = DetailPo::select(
    //         'detail_po.*',
    //         'purchase_request.no_pr',
    //         'purchase_order.no_po',
    //         'purchase_request.nomor_lppb',
    //         'purchase_request.tanggal_lppb'
    //     )
    //     ->leftJoin('purchase_request', 'purchase_request.id', '=', 'detail_po.id_pr')
    //     ->leftJoin('purchase_order', 'purchase_order.id', '=', 'detail_po.id_po')
    //     ->groupBy('id_po')
    //     ->paginate(10);

    //     foreach ($items as $item) {
    //         $item->tipe = $item->tipe == 0 ? 'PO' : 'PO/PL';
    //         $item->diterima = RegistrasiBarang::where('id_barang', $item->id)->exists() ? 1 : 0;
    //         $item->keterangan = RegistrasiBarang::where('id_barang', $item->id)->value('keterangan') ?? '';

    //         $po = Purchase_Order::where('id', $item->id_po)->first();

    //         if ($po && $po->proyek_id) {
    //             $split_proyek = explode(',', $po->proyek_id);
    //             $proyek_names = Keproyekan::whereIn('id', $split_proyek)->pluck('nama_proyek')->toArray();
    //             $item->proyeks = implode(',', $proyek_names);
    //         } else {
    //             $item->proyeks = ''; // Berikan nilai default jika $po atau proyek_id tidak ditemukan
    //         }
    //     }

    //     return view('penerimaan_barang.index', compact('items'));
    // }




    public function registrasi_barang(Request $request)
    {
        $request->validate([
            'keterangan' => 'required',
        ], [
            'keterangan.required' => 'Keterangan harus diisi',
        ]);
        // dd($request->all());

        $id = $request->id_barang;
        $keterangan = $request->keterangan;

        $add = RegistrasiBarang::create([
            'id_barang' => $id,
            'id_user' => auth()->user()->id,
            'keterangan' => $keterangan,
        ]);

        return redirect()->route('penerimaan_barang')->with('success', 'Berhasil registrasi barang');
    }

    public function edit_registrasi_barang(Request $request)
    {
        $request->validate([
            'keterangan' => 'required',
        ], [
            'keterangan.required' => 'Keterangan harus diisi',
        ]);

        $id = $request->id_barang;
        $keterangan = $request->keterangan;

        $add = RegistrasiBarang::where('id_barang', $id)->update([
            'id_user' => auth()->user()->id,
            'keterangan' => $keterangan,
        ]);

        return redirect()->route('penerimaan_barang')->with('success', 'Berhasil mengubah keterangan');
    }


    public function lppb()
    {
        // $items = RegistrasiBarang::select('detail_pr.*', 'purchase_request.no_pr', 'purchase_order.no_po', 'purchase_order.tipe', 'keproyekan.nama_proyek', 'registrasi_barang.created_at as diterima_ekspedisi', 'registrasi_barang.id as id_registrasi_barang')
        //     ->leftjoin('detail_pr', 'detail_pr.id', '=', 'registrasi_barang.id_barang')
        //     ->leftjoin('purchase_request', 'purchase_request.id', '=', 'detail_pr.id_pr')
        //     ->leftjoin('purchase_order', 'purchase_order.id', '=', 'detail_pr.id_po')
        //     ->leftjoin('keproyekan', 'keproyekan.id', '=', 'purchase_request.proyek_id')
        //     ->whereNotNull('detail_pr.id_po')
        //     ->paginate(10);

        // $items = RegistrasiBarang::select(
        // 'purchase_request.*',
        // 'purchase_request.no_pr',
        // 'purchase_order.no_po',
        // 'purchase_order.tipe',
        // 'kontrak.nama_pekerjaan',
        // 'registrasi_barang.created_at as diterima_ekspedisi',
        // 'registrasi_barang.id as id_registrasi_barang'
        // )
        // ->leftjoin('purchase_request', 'purchase_request.id', '=', 'registrasi_barang.id_barang')
        // ->leftjoin(DB::raw('(SELECT * FROM detail_pr GROUP BY id_pr) as detail_pr'), 'detail_pr.id_pr', '=', 'purchase_request.id')
        // ->leftjoin('purchase_order', 'purchase_order.id', '=', 'detail_pr.id_po')
        // ->leftjoin('kontrak', 'kontrak.id', '=', 'purchase_request.proyek_id')
        // ->whereNotNull('detail_pr.id_po')
        // ->paginate(10);

        $items = DetailPo::select(
            'detail_po.*',
            // 'purchase_request.no_pr',
            'purchase_order.no_po',
            'purchase_order.nomor_lppb',
            'purchase_order.tanggal_lppb',
        )
            // ->leftjoin('purchase_request','purchase_request.id', '=', 'detail_po.id_pr')
            ->leftjoin('purchase_order', 'purchase_order.id', '=', 'detail_po.id_po')
            ->groupBy('id_po')->paginate(10);

        foreach ($items as $item) {
            $po = Purchase_Order::where('id', $item->id_po)->first();
            $id_pr = explode(',', $po->pr_id);
            $item->no_pr = PurchaseRequest::whereIn('id', $id_pr)->pluck('no_pr')->implode(', ');
        }

        // $items = DetailPo::select(
        //     'detail_po.*',
        //     'purchase_request.no_pr',
        //     'purchase_order.no_po',
        //     'purchase_request.nomor_lppb',
        //     'purchase_request.tanggal_lppb',
        // )
        // ->leftjoin('purchase_request','purchase_request.id', '=', 'detail_po.id_pr')
        // ->leftjoin('purchase_order', 'purchase_order.id', '=', 'detail_po.id_po')
        // ->groupBy('id_po')->paginate(10);

        // dd($items);



        // $items = RegistrasiBarang::with(['purchase_request' => function ($query) {
        //     $query->join('purchase_order', 'detail_pr.id_po', '=', 'purchase_order.id')
        //         ->select('detail_pr.*', 'purchase_order.no_po');
        // }])->paginate(10);

        // $items = PurchaseRequest::with(['detailPr' => function ($query) {
        //     $query->join('purchase_order', 'detail_pr.id_po', '=', 'purchase_order.id')
        //         ->select('detail_pr.*', 'purchase_order.no_po');
        // }])->paginate(10);

        foreach ($items as $item) {
            $item->tipe = $item->tipe == 0 ? 'PO' : 'PO/PL';
            // $item->diterima = Lppb::where('id_registrasi_barang', $item->id_registrasi_barang)->first() ? 1 : 0;
            // $keterangan = Lppb::where('id_registrasi_barang', $item->id)->first() ? Lppb::where('id_registrasi_barang', $item->id)->first()->keterangan : '';
            // $item->keterangan = Lppb::where('id_registrasi_barang', $item->id)->first() ? Lppb::where('id_registrasi_barang', $item->id)->first()->keterangan : '';
            // $item->diterima_ekspedisi = Carbon::parse($item->diterima_ekspedisi)->isoFormat('D MMMM Y');
        }

        return view('lppb.index', compact('items'));
    }

    public function getDetailLppb(Request $request)
    {
        //cari di tabel detail_po berdasarkan id_po dari $request->id_po
        $po = DetailPo::where('id_po', $request->id);

        $ids = $po->pluck('id_detail_pr');
        $id = $po->first()->id_pr;
        //then hasil dari $ids berupa array, misal [1,2] lalu taroh di detail_pr

        $pr = PurchaseRequest::select('purchase_request.*', 'kontrak.nama_pekerjaan as nama_proyek')
            ->join('kontrak', 'kontrak.id', '=', 'purchase_request.proyek_id')
            ->where('purchase_request.id', $id)
            ->first();
        $pr->id_po_woi = $request->id;
        //cari item PR berdasarkan $ids isine [1,2,3] dll
        // dd($ids);
        $pr->details = DetailPR::whereIn('id', $ids)->get();
        // $pr->details = DetailPR::where('id_pr', $id)->leftJoin('kode_material', 'kode_material.id', '=', 'detail_pr.kode_material_id')->get();
        // dd($pr->details);
        $pr->details = $pr->details->map(function ($item) use ($request) {
            $item->spek = $item->spek ? $item->spek : '';
            $item->keterangan = $item->keterangan ? $item->keterangan : '';
            $item->kode_material = $item->kode_material ? $item->kode_material : '';
            $item->nomor_spph = Spph::where('id', $item->id_spph)->first()->nomor_spph ?? '';
            $item->no_po = Purchase_Order::where('id', $request->id)->first()->no_po ?? '';
            $item->no_pr = PurchaseRequest::where('id', $item->id_pr)->first()->no_pr ?? '';
            $item->userRole = User::where('id', $item->user_id)->first()->role ?? '';
            $item->no_sph = $item->no_sph ? $item->no_sph : '';
            $item->tanggal_sph = $item->tanggal_sph ? $item->tanggal_sph : '';
            $item->no_just = $item->no_just ? $item->no_just : '';
            $item->tanggal_just = $item->tanggal_just ? $item->tanggal_just : '';
            $item->no_nego1 = $item->no_nego1 ? $item->no_nego1 : '';
            $item->tanggal_nego1 = $item->tanggal_nego1 ? $item->tanggal_nego1 : '';
            $item->batas_nego1 = $item->batas_nego1 ? $item->batas_nego1 : '';
            $item->no_nego2 = $item->no_nego2 ? $item->no_nego2 : '';
            $item->tanggal_nego2 = $item->tanggal_nego2 ? $item->tanggal_nego2 : '';
            $item->batas_nego2 = $item->batas_nego2 ? $item->batas_nego2 : '';
            $item->batas_akhir = Purchase_Order::leftjoin('detail_po', 'detail_po.id_po', '=', 'purchase_order.id')->where('detail_po.id_detail_pr', $item->id)->first()->batas_akhir ?? '-';

            $po = Purchase_Order::where('id', $item->id_po)->first();
            // dd($po);
            $split_proyek = explode(',', $po->proyek_id);
            // dd($split_proyek);
            $proyek_names = Kontrak::whereIn('id', $split_proyek)->pluck('nama_pekerjaan')->toArray();
            // dd($proyek_names);
            $item->proyeks = implode(',', $proyek_names);
            // dd($item->proyek);

            $ekspedisi = RegistrasiBarang::where('id_barang', $item->id)->first();
            if ($ekspedisi) {
                $keterangan = $ekspedisi->keterangan;
                $tanggal = $ekspedisi->created_at;
                $tanggal = Carbon::parse($tanggal)->isoFormat('D MMMM Y');
                $keterangan = $keterangan . ', ' . $tanggal;
            } else {
                $keterangan = null;
            }
            $item->ekspedisi = $keterangan;

            //qc
            if ($ekspedisi) {
                $qc = Lppb::where('id_registrasi_barang', $ekspedisi->id)->first();
            } else {
                $qc = null;
            }

            if ($qc) {
                $penerimaan = $qc->penerimaan;
                $hasil_ok = $qc->hasil_ok;
                $hasil_nok = $qc->hasil_nok;
                $tanggal_qc = $qc->created_at;
                $tanggal_qc = Carbon::parse($qc->created_at)->isoFormat('D MMMM Y');
                $qc = new stdClass();
                $qc->penerimaan = $penerimaan;
                $qc->hasil_ok = $hasil_ok;
                $qc->hasil_nok = $hasil_nok;
                $qc->tanggal_qc = $tanggal_qc;
            } else {
                $penerimaan = null;
                $hasil_ok = null;
                $hasil_nok = null;
                $tanggal_qc = null;
                $qc = null;
            }

            $item->qc = $qc;

            //countdown = waktu - date now
            $targetDate = Carbon::parse($item->waktu);
            $currentDate = Carbon::now();
            $diff = $currentDate->diff($targetDate);
            $remainingDays = $diff->days;

            $referenceDate = Carbon::parse($item->waktu); // Change this to your desired reference date

            if ($currentDate->lessThan($referenceDate)) {
                // If the current date is before the reference date
                $item->countdown = "$remainingDays  Hari Sebelum Waktu Penyelesaian";
                $item->backgroundcolor = "#FF0000"; // Red background
            } elseif ($currentDate->greaterThanOrEqualTo($referenceDate)) {
                // If the current date is on or after the reference date
                $item->countdown = "$remainingDays Hari Setelah Waktu Penyelesaian";
                $item->backgroundcolor = "#008000"; // Green background
            }

            $id_po_real = $request->id;
            // $detail_pr
            $penerimaan_barang = PenerimaanBarang::where('id_detail_pr', $item->id)->where('id_po', $id_po_real)->first();

            $item->penerimaan = $penerimaan_barang ? $penerimaan_barang->penerimaan : null;
            $item->hasil_ok = $penerimaan_barang ? $penerimaan_barang->hasil_ok : null;
            $item->hasil_nok = $penerimaan_barang ? $penerimaan_barang->hasil_nok : null;
            $item->diterima_qc = $penerimaan_barang ? $penerimaan_barang->diterima_qc : null;
            $item->belum_diterima_qc = $penerimaan_barang ? $penerimaan_barang->belum_diterima_qc : null;
            $item->tgl_diterima = $penerimaan_barang ? $penerimaan_barang->tanggal_diterima : null;

            return $item;
        });
        return response()->json([
            'pr' => $pr
        ]);
    }

    public function getDetailPenerimaanBarang(Request $request)
    {
        //cari di tabel detail_po berdasarkan id_po dari $request->id_po
        $po = DetailPo::where('id_po', $request->id);

        $ids = $po->pluck('id_detail_pr');
        $id = $po->first()->id_pr;
        //then hasil dari $ids berupa array, misal [1,2] lalu taroh di detail_pr

        $pr = PurchaseRequest::select('purchase_request.*', 'kontrak.nama_pekerjaan as nama_proyek')
            ->join('kontrak', 'kontrak.id', '=', 'purchase_request.proyek_id')
            ->where('purchase_request.id', $id)
            ->first();
        $pr->id_po_woi = $request->id;
        //cari item PR berdasarkan $ids isine [1,2,3] dll
        $pr->details = DetailPR::whereIn('id', $ids)->get();
        // $pr->details = DetailPR::where('id_pr', $id)->leftJoin('kode_material', 'kode_material.id', '=', 'detail_pr.kode_material_id')->get();
        $pr->details = $pr->details->map(function ($item) use ($request) {
            $item->spek = $item->spek ? $item->spek : '';
            $item->keterangan = $item->keterangan ? $item->keterangan : '';
            $item->kode_material = $item->kode_material ? $item->kode_material : '';
            $item->nomor_spph = Spph::where('id', $item->id_spph)->first()->nomor_spph ?? '';
            $item->no_po = Purchase_Order::where('id', $request->id)->first()->no_po ?? '';
            $item->no_pr = PurchaseRequest::where('id', $item->id_pr)->first()->no_pr ?? '';
            $item->userRole = User::where('id', $item->user_id)->first()->role ?? '';
            $item->no_sph = $item->no_sph ? $item->no_sph : '';
            $item->tanggal_sph = $item->tanggal_sph ? $item->tanggal_sph : '';
            $item->no_just = $item->no_just ? $item->no_just : '';
            $item->tanggal_just = $item->tanggal_just ? $item->tanggal_just : '';
            $item->no_nego1 = $item->no_nego1 ? $item->no_nego1 : '';
            $item->tanggal_nego1 = $item->tanggal_nego1 ? $item->tanggal_nego1 : '';
            $item->batas_nego1 = $item->batas_nego1 ? $item->batas_nego1 : '';
            $item->no_nego2 = $item->no_nego2 ? $item->no_nego2 : '';
            $item->tanggal_nego2 = $item->tanggal_nego2 ? $item->tanggal_nego2 : '';
            $item->batas_nego2 = $item->batas_nego2 ? $item->batas_nego2 : '';
            $item->batas_akhir = Purchase_Order::leftjoin('detail_po', 'detail_po.id_po', '=', 'purchase_order.id')->where('detail_po.id_detail_pr', $item->id)->first()->batas_akhir ?? '-';

            $po = Purchase_Order::where('id', $item->id_po)->first();
            // dd($po);
            $split_proyek = explode(',', $po->proyek_id);
            // dd($split_proyek);
            $proyek_names = Kontrak::whereIn('id', $split_proyek)->pluck('nama_pekerjaan')->toArray();
            // dd($proyek_names);
            $item->proyeks = implode(',', $proyek_names);

            $ekspedisi = RegistrasiBarang::where('id_barang', $item->id)->first();
            if ($ekspedisi) {
                $keterangan = $ekspedisi->keterangan;
                $tanggal = $ekspedisi->created_at;
                $tanggal = Carbon::parse($tanggal)->isoFormat('D MMMM Y');
                $keterangan = $keterangan . ', ' . $tanggal;
            } else {
                $keterangan = null;
            }
            $item->ekspedisi = $keterangan;

            //qc
            if ($ekspedisi) {
                $qc = Lppb::where('id_registrasi_barang', $ekspedisi->id)->first();
            } else {
                $qc = null;
            }

            if ($qc) {
                $penerimaan = $qc->penerimaan;
                $hasil_ok = $qc->hasil_ok;
                $hasil_nok = $qc->hasil_nok;
                $tanggal_qc = $qc->created_at;
                $tanggal_qc = Carbon::parse($qc->created_at)->isoFormat('D MMMM Y');
                $qc = new stdClass();
                $qc->penerimaan = $penerimaan;
                $qc->hasil_ok = $hasil_ok;
                $qc->hasil_nok = $hasil_nok;
                $qc->tanggal_qc = $tanggal_qc;
            } else {
                $penerimaan = null;
                $hasil_ok = null;
                $hasil_nok = null;
                $tanggal_qc = null;
                $qc = null;
            }

            $item->qc = $qc;

            //countdown = waktu - date now
            $targetDate = Carbon::parse($item->waktu);
            $currentDate = Carbon::now();
            $diff = $currentDate->diff($targetDate);
            $remainingDays = $diff->days;

            $referenceDate = Carbon::parse($item->waktu); // Change this to your desired reference date

            if ($currentDate->lessThan($referenceDate)) {
                // If the current date is before the reference date
                $item->countdown = "$remainingDays  Hari Sebelum Waktu Penyelesaian";
                $item->backgroundcolor = "#FF0000"; // Red background
            } elseif ($currentDate->greaterThanOrEqualTo($referenceDate)) {
                // If the current date is on or after the reference date
                $item->countdown = "$remainingDays Hari Setelah Waktu Penyelesaian";
                $item->backgroundcolor = "#008000"; // Green background
            }

            $id_po_real = $request->id;
            $penerimaan_barang = PenerimaanBarang::where('id_detail_pr', $item->id)->where('id_po', $id_po_real)->first();
            $item->diterima_eks = $penerimaan_barang ? $penerimaan_barang->diterima_eks : null;
            $item->belum_diterima_eks = $penerimaan_barang ? $penerimaan_barang->belum_diterima_eks : null;
            $item->diterima_qc = $penerimaan_barang ? $penerimaan_barang->diterima_qc : null;
            $item->belum_diterima_qc = $penerimaan_barang ? $penerimaan_barang->belum_diterima_qc : null;
            $item->tanggal_diterima = $penerimaan_barang ? $penerimaan_barang->tanggal_diterima : null;

            return $item;
        });
        return response()->json([
            'pr' => $pr
        ]);
    }


    public function tambah_lppb(Request $request)
    {
        $request->validate([
            'keterangan' => 'nullable',
            'kuantitas_penerimaan' =>   'required',
            'baik' => 'required',
            'tidak_baik' => 'required',
        ], [
            'keterangan.required' => 'Keterangan harus diisi',
            'kuantitas_penerimaan.required' => 'Kuantitas penerimaan harus diisi',
            'baik.required' => 'Kuantitas barang baik harus diisi',
            'tidak_baik.required' => 'Kuantitas barang tidak baik harus diisi',
        ]);

        $id = $request->id_barang;
        $id_registrasi_barang = $request->id_registrasi_barang;
        $keterangan = $request->keterangan;
        $kuantitas_penerimaan = $request->kuantitas_penerimaan;
        $baik = $request->baik;
        $tidak_baik = $request->tidak_baik;

        $add = Lppb::create([
            'id_registrasi_barang' => $id_registrasi_barang,
            'keterangan' => $keterangan,
            'penerimaan' => $kuantitas_penerimaan,
            'hasil_ok' => $baik,
            'hasil_nok' => $tidak_baik,
        ]);

        return redirect()->route('lppb')->with('success', 'Berhasil menerima barang');
    }

    public function getPurchaseRequestDetail($id)
    {
        $detail = PurchaseRequest::find($id);
        return response()->json($detail);
    }

    public function updatePurchaseRequestDetail(Request $request)
    {
        $detail = PurchaseRequest::find($request->id);
        $detail->kode_material = $request->kode_material;
        $detail->uraian = $request->uraian;
        $detail->spek = $request->spek;
        $detail->qty = $request->qty;
        $detail->satuan = $request->satuan;
        $detail->waktu = $request->waktu;
        $detail->keterangan = $request->keterangan;
        $detail->save();

        return response()->json(['message' => 'Item updated successfully']);
    }

    public function deleteDetail(Request $request)
    {
        try {
            $detail = PurchaseRequest::findOrFail($request->id);
            $detail->delete();

            return Response::json(['message' => 'Detail berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return Response::json(['message' => 'Gagal menghapus detail', 'error' => $e->getMessage()], 500);
        }
    }

    // public function cetakLPPB(Request $request)
    // {
    //     $id = $request->id;
    //     $data = PurchaseRequest::find($id);
    //     if ($data) {
    //         // Mengambil data dari model Keproyekan berdasarkan proyek_id dari model PurchaseRequest
    //         $proyek = Keproyekan::find($data->proyek_id)->nama_proyek;

    //         // Mengambil semua data dari model Keproyekan berdasarkan id_pr dari model PurchaseRequest
    //         $detailpr = DetailPR::where('id_pr', $data->id)->get();

    //         // Mengambil data no_po dan vendor_id dari model PurchaseOrder berdasarkan id_po dari model DetailPr
    //         $purchaseOrders = Purchase_Order::whereIn('id', $detailpr->pluck('id_po'))->get(['no_po', 'vendor_id']);

    //         // Memisahkan data no_po dan vendor_id ke dalam array terpisah
    //         $poNumbers = $purchaseOrders->pluck('no_po');
    //         $vendorIds = $purchaseOrders->pluck('vendor_id');

    //         // Mengambil semua data dari model Vendor berdasarkan vendor_id
    //         $vendors = Vendor::whereIn('id', $vendorIds)->get();

    //         $pdf = Pdf::loadview('lppb.lppb_print', compact('data', 'proyek', 'detailpr', 'poNumbers', 'vendors'));
    //         $pdf->setPaper('A4', 'landscape');
    //         return $pdf->stream('LPPB-' . '.pdf');
    //     } else {
    //         return response()->json([
    //             'message' => 'LPPB not found'
    //         ], 404);
    //     }
    // }


    public function cetakLPPB(Request $request)
    {
        $po_asli = Purchase_Order::where('id', $request->id)->first();
        $po = DetailPo::where('id_po', $request->id);

        $ids = $po->pluck('id_detail_pr');
        $id = $po->first()->id_pr;

        $data = PurchaseRequest::find($id);

        if ($data) {
            // Mengambil data dari model Keproyekan berdasarkan proyek_id dari model PurchaseRequest
            $proyek = Kontrak::find($data->proyek_id)->nama_pekerjaan;

            // Mengambil semua data dari model DetailPR berdasarkan id_pr dari model PurchaseRequest
            $detailpr = DetailPR::whereIn('id', $ids)
                ->whereNotNull('id_po') // Filter untuk memastikan hanya yang memiliki nomor PO
                ->get();

            $detailpr = $detailpr->map(function ($item) use ($request) {
                $id_po_real = $request->id;
                $penerimaan_barang = PenerimaanBarang::where('id_detail_pr', $item->id)->where('id_po', $id_po_real)->first();

                $item->penerimaan = $penerimaan_barang ? $penerimaan_barang->penerimaan : null;
                $item->hasil_ok = $penerimaan_barang ? $penerimaan_barang->hasil_ok : null;
                $item->hasil_nok = $penerimaan_barang ? $penerimaan_barang->hasil_nok : null;
                $item->diterima_qc = $penerimaan_barang ? $penerimaan_barang->diterima_qc : null;
                $item->belum_diterima_qc = $penerimaan_barang ? $penerimaan_barang->belum_diterima_qc : null;
                $item->tgl_diterima = $penerimaan_barang ? $penerimaan_barang->tanggal_diterima : null;
                return $item;
            });

            // Mengambil data no_po dan vendor_id dari model PurchaseOrder berdasarkan id_po dari model DetailPR
            // $purchaseOrders = Purchase_Order::whereIn('id', $detailpr->pluck('id_po'))->get(['no_po', 'vendor_id']);
            $purchaseOrders = Purchase_Order::where('id', $request->id)->get(['no_po', 'vendor_id']);


            // Memisahkan data no_po dan vendor_id ke dalam array terpisah
            $poNumbers = $purchaseOrders->pluck('no_po');
            $vendorIds = $purchaseOrders->pluck('vendor_id');

            // Mengambil semua data dari model Vendor berdasarkan vendor_id
            $vendors = Vendor::whereIn('id', $vendorIds)->get();

            // Memuat view dengan data yang diperlukan
            $pdf = Pdf::loadview('lppb.lppb_print', compact('data', 'proyek', 'detailpr', 'poNumbers', 'vendors', 'po_asli'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('LPPB-' . '.pdf');
        } else {
            return response()->json([
                'message' => 'LPPB not found'
            ], 404);
        }
    }


    public function editlppb(Request $request)
    {
        // dd($request->all());
        // Validasi data yang diterima dari request
        $request->validate([
            'id_detail' => 'required',
            'penerimaan' => 'nullable',
            'ok' => 'nullable',
            'nok' => 'nullable',
            'sdh_qc' => 'nullable',
            'blm' => 'nullable',
            'tgld' => 'required',
        ]);
        $id_po_real = Purchase_Order::where('no_po', $request->no_po)->first()->id;
        $id = $request->id_detail;
        // $detailPR = DetailPR::where('id', $id)->first();
        // $detailPR->update([
        //     'penerimaan' => $request->penerimaan,
        //     'hasil_ok' => $request->ok,
        //     'hasil_nok' => $request->nok,
        //     'diterima_qc' => $request->sdh_qc,
        //     'belum_diterima_qc' => $request->blm,
        //     'tgl_diterima' => $request->tgld,
        // ]);

        $updated_data = PenerimaanBarang::where('id_detail_pr', $request->id_detail)->where('id_po', $id_po_real)->first();

        if ($updated_data) {
            PenerimaanBarang::where('id_detail_pr', $request->id_detail)->where('id_po', $id_po_real)->update([
                'penerimaan' => $request->penerimaan,
                'hasil_ok' => $request->ok,
                'hasil_nok' => $request->nok,
                'diterima_qc' => $request->sdh_qc,
                'belum_diterima_qc' => $request->blm,
                'tanggal_diterima' => $request->tgld,
            ]);
        }

        $po = DetailPo::where('id_po', $request->id_po);

        $ids = $po->pluck('id_detail_pr');

        $pr = DB::table('purchase_request')->where('id', $request->id_pr)->first();
        $pr->id_po_real = $id_po_real;
        $pr->id_po_woi = $request->id_po;
        $pr->details = DetailPR::whereIn('id', $ids)
            ->whereNotNull('diterima_eks')
            ->whereNotNull('belum_diterima_eks')->get();
        $pr->details = $pr->details->map(function ($item) use ($request, $id_po_real) {
            $po = Purchase_Order::where('id', $item->id_po)->first();
            // dd($po);
            $split_proyek = explode(',', $po->proyek_id);
            // dd($split_proyek);
            $proyek_names = Kontrak::whereIn('id', $split_proyek)->pluck('nama_pekerjaan')->toArray();
            // dd($proyek_names);
            $item->proyeks = implode(',', $proyek_names);
            // dd($item->proyek);

            $penerimaan_barang = PenerimaanBarang::where('id_detail_pr', $item->id)->where('id_po', $id_po_real)->first();

            $item->penerimaan = $penerimaan_barang ? $penerimaan_barang->penerimaan : null;
            $item->hasil_ok = $penerimaan_barang ? $penerimaan_barang->hasil_ok : null;
            $item->hasil_nok = $penerimaan_barang ? $penerimaan_barang->hasil_nok : null;
            $item->diterima_qc = $penerimaan_barang ? $penerimaan_barang->diterima_qc : null;
            $item->belum_diterima_qc = $penerimaan_barang ? $penerimaan_barang->belum_diterima_qc : null;
            $item->tgl_diterima = $penerimaan_barang ? $penerimaan_barang->tanggal_diterima : null;

            return $item;
        });
        return response()->json([
            'success' => true,
            'no_po' => $request->no_po,
            'nama_proyek' => $request->nama_proyek,
            'message' => 'LPPB berhasil diupdate.',
            'pr' => $pr // Mengembalikan data detail SR yang telah diupdate
        ]);
    }


    public function editpenerimaan(Request $request)
    {
        // dd($request->all());
        // Validasi data yang diterima dari request
        $request->validate([
            'id' => 'required',
            'penerimaan' => 'nullable',
            'sdh' => 'nullable',
            'blm_sdh' => 'nullable',
        ]);

        $id = $request->id;
        // $detailPR = DetailPR::where('id', $id)->first();
        // $detailPR->update([
        //     'penerimaan' => $request->penerimaan,
        //     'diterima_eks' => $request->sdh,
        //     'belum_diterima_eks' => $request->blm_sdh,
        // ]);

        $id_po_real = Purchase_Order::where('no_po', $request->no_po)->first()->id;
        $updated_data = PenerimaanBarang::where('id_detail_pr', $request->id)->where('id_po', $id_po_real)->first();
        if ($updated_data) {
            PenerimaanBarang::where('id_detail_pr', $request->id)->where('id_po', $id_po_real)->update([
                'diterima_eks' => $request->sdh,
                'belum_diterima_eks' => $request->blm_sdh,
            ]);
        } else {
            PenerimaanBarang::create([
                'id_detail_pr' => $id,
                'id_po' => $id_po_real,
                'diterima_eks' => $request->sdh,
                'belum_diterima_eks' => $request->blm_sdh,
            ]);
        }

        $po = DetailPo::where('id_po', $request->id_po);

        $ids = $po->pluck('id_detail_pr');

        $pr = DB::table('purchase_request')->where('id', $request->id_pr)->first();
        $pr->id_po_real = $id_po_real;
        $pr->details = DetailPR::whereIn('id', $ids)->get();
        $pr->details = $pr->details->map(function ($item) use ($request) {
            $po = Purchase_Order::where('id', $item->id_po)->first();
            // dd($po);
            $split_proyek = explode(',', $po->proyek_id);
            // dd($split_proyek);
            $proyek_names = Kontrak::whereIn('id', $split_proyek)->pluck('nama_pekerjaan')->toArray();
            // dd($proyek_names);
            $item->proyeks = implode(',', $proyek_names);
            // dd($item->proyek);

            $id_po_real = Purchase_Order::where('no_po', $request->no_po)->first()->id;
            $penerimaan_barang = PenerimaanBarang::where('id_detail_pr', $item->id)->where('id_po', $id_po_real)->first();
            $item->diterima_eks = $penerimaan_barang ? $penerimaan_barang->diterima_eks : null;
            $item->belum_diterima_eks = $penerimaan_barang ? $penerimaan_barang->belum_diterima_eks : null;
            $item->diterima_qc = $penerimaan_barang ? $penerimaan_barang->diterima_qc : null;
            $item->belum_diterima_qc = $penerimaan_barang ? $penerimaan_barang->belum_diterima_qc : null;
            $item->tanggal_diterima = $penerimaan_barang ? $penerimaan_barang->tanggal_diterima : null;

            return $item;
        });
        return response()->json([
            'success' => true,
            'no_po' => $request->no_po,
            'nama_proyek' => $request->nama_proyek,
            'message' => 'LPPB berhasil diupdate.',
            'pr' => $pr // Mengembalikan data detail SR yang telah diupdate
        ]);
    }


    public function edit_nomor_lppb(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id_prr' => 'required',
            'nomor_lppb' => 'required',
            'tanggal_lppb' => 'required',
        ], [
            'id_prr.required' => 'ID harus diisi',
            'nomor_lppb.required' => 'Nomor LPPB harus diisi',
            'tanggal_lppb.required' => 'Tanggal LPPB harus diisi',
        ]);

        $id = $request->id_prr;
        // $nomor_lppb = $request->nomor_lppb;
        // $tanggal_lppb = $request->tanggal_lppb;
        $edit = Purchase_Order::where('id', $id)->first();
        // dd($request->all());
        $edit->update([
            'nomor_lppb' => $request->nomor_lppb,
            'tanggal_lppb' => $request->tanggal_lppb,
        ]);

        return redirect()->route('lppb')->with('success', 'Berhasil mengubah Nomor & Tanggal LPPB');
    }
}
