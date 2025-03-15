<?php

namespace App\Http\Controllers;

use App\Models\Bpm;
use App\Models\DetailBpm;
use App\Models\DetailPo;
use App\Models\DetailPR;
use App\Models\DetailSjn;
use App\Models\DetailSpph;
use App\Models\Keproyekan;
use App\Models\Lppb;
use App\Models\Vendor;
use App\Models\Purchase_Order;
use App\Models\PurchaseRequest;
use App\Models\RegistrasiBarang;
use App\Models\Spph;
use App\Models\User;
use App\Models\Notification;
use App\Models\SuratJalan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use stdClass;


class SuratJalanController extends Controller
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

        $requests = SuratJalan::select('surat_jalan.*')
            // ->join('keproyekan', 'keproyekan.id', '=', 'bpm.proyek_id')
            ->orderBy('surat_jalan.id', 'asc')
            ->paginate(50);

        $proyeks = DB::table('keproyekan')->get();
        //  dd($requests);


        if ($search) {
            $requests = SuratJalan::where('barang', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $requests = SuratJalan::where("warehouse_id", $warehouse_id)->get();

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
            return view('surat_jalan.surat_jalan', compact('requests', 'proyeks'));
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

        $requests = Bpm::select('bpm.*', 'keproyekan.nama_proyek as proyek_name')
            ->join('keproyekan', 'keproyekan.id', '=', 'bpm.proyek_id')
            ->paginate(50);

        $proyeks = DB::table('keproyekan')->get();

        if ($search) {
            $requests = Bpm::where('nama_proyek', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $requests = Bpm::where("warehouse_id", $warehouse_id)->get();

            return response()->json($requests);
        } else {
            return view('home.apps.wilayah.purchase_request', compact('requests', 'proyeks'));
        }
    }



    //untuk menyimpan
    public function store(Request $request)
    {
        //Store untuk menambah data
        $sjn = $request->id;
        $request->validate(
            [

                'no_sjn' => 'required',
                'tgl_sjn' => 'required',
                'kepada' => 'required',
                'lokasi' => 'required',
                'pengirim' => 'required',
                'note' => 'required',

            ],
            [

                'no_sjn.required' => 'No Sjn harus diisi',
                'tgl_sjn.required' => 'Tanggal sjn harus diisi',
                'kepada.required' => 'Kepada harus diisi',
                'lokasi.required' => 'Lokasi harus diisi',
                'pengirim.required' => 'Pengirim harus diisi',
                'note.required' => 'Note harus diisi',
            ]
        );

        if (empty($sjn)) {
            DB::table('surat_jalan')->insert([

                'no_sjn' => $request->no_sjn,
                'tgl_sjn' => $request->tgl_sjn,
                'kepada' => $request->kepada,
                'lokasi' => $request->lokasi,
                'pengirim' => $request->pengirim,
                'note' => $request->note,
                'id_user' => auth()->user()->id,
            ]);

            return redirect()->route('surat_jalan.index')->with('success', 'Surat Jalan berhasil ditambahkan');
        } else {
            DB::table('surat_jalan')->where('id', $sjn)->update([

                'no_sjn' => $request->no_sjn,
                'tgl_sjn' => $request->tgl_sjn,
                'kepada' => $request->kepada,
                'lokasi' => $request->lokasi,
                'pengirim' => $request->pengirim,
                'note' => $request->note,
            ]);


            return redirect()->route('surat_jalan.index')->with('success', 'Surat Jalan berhasil diupdate');
        }

        // return redirect()->route('purchase_request.index')->with('success', 'Purchase Request berhasil disimpan');

    }
    //End untuk menyimpan





    public function detailSjnSave(Request $request)
    {
        $id_bpm = $request->id;
        $id = $request->id_sjn;
        // $no_sph = $request->no_sph;
        // $tanggal_sph = $request->tanggal_sph;
        // $no_just = $request->no_just;
        // $tanggal_just = $request->tanggal_just;
        // $no_nego1 = $request->no_nego1;
        // $tanggal_nego1 = $request->tanggal_nego1;
        // $batas_nego1 = $request->batas_nego1;
        // $no_nego2 = $request->no_nego2;
        // $tanggal_nego2 = $request->tanggal_nego2;
        // $batas_nego2 = $request->batas_nego2;

        // DetailPR::where('id', $id_pr)->update([
        //     'no_sph' => $no_sph,
        //     'tanggal_sph' => $tanggal_sph,
        //     'no_just' => $no_just,
        //     'tanggal_just' => $tanggal_just,
        //     'no_nego1' => $no_nego1,
        //     'tanggal_nego1' => $tanggal_nego1,
        //     'batas_nego1' => $batas_nego1,
        //     'no_nego2' => $no_nego2,
        //     'tanggal_nego2' => $tanggal_nego2,
        //     'batas_nego2' => $batas_nego2,
        // ]);

        $sjn = SuratJalan::where('id', $id)->first();
        $sjn->details = DetailSjn::where('id_sjn', $sjn->id)->get();
        // $pr->details = DetailPR::where('id_pr', $id)->leftJoin('kode_material', 'kode_material.id', '=', 'detail_pr.kode_material_id')->get();
        $sjn->details = $sjn->details->map(function ($item) {
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
            'sjn' => $sjn
        ]);
    }









    public function getDetailSjn(Request $request)
    {
        $id = $request->id;
        $sjn = SuratJalan::select('surat_jalan.*')
            // ->join('keproyekan', 'keproyekan.id', '=', 'bpm.proyek_id')
            ->where('surat_jalan.id', $id)
            ->first();
        $sjn->details = DetailSjn::where('id_sjn', $id)->get();
        // $pr->details = DetailPR::where('id_pr', $id)->leftJoin('kode_material', 'kode_material.id', '=', 'detail_pr.kode_material_id')->get();
        $sjn->details = $sjn->details->map(function ($item) {
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
            return $item;
        });
        return response()->json([
            'sjn' => $sjn
        ]);
    }



    //menambah item detail baru
    public function updateDetailSjn(Request $request)
    {
        if (!$request->qty) {
            return response()->json([
                'success' => false,
                'message' => 'QTY tidak boleh kosong'
            ]);
        }
        // $request->validate([
        //     'lampiran' => 'nullable',
        //     // 'lampiran' => 'nullable|file|mimes:pdf|max:500',
        // ]);

        // if ($request->file('lampiran')) {
        //     $file = $request->file('lampiran');
        //     // dd($file);
        //     $fileName = rand() . '.' . $file->getClientOriginalExtension();
        //     // dd($fileName);
        //     $file->move(public_path('lampiran'), $fileName);
        // } else {
        //     $fileName = null;
        // }

        $insert = DetailSjn::create([
            'id_sjn' => $request->id_sjn,
            // 'id_proyek' => $request->id_proyek,
            'kode_material' => $request->kode_material,
            'barang' => $request->barang,
            'spek' => $request->spek,
            'satuan' => $request->satuan,
            'qty' => $request->qty,

            'keterangan' => $request->keterangan,
            // 'lampiran' => $fileName,
        ]);

        if (!$insert) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan detail SJN'
            ]);
        }

        $sjn = DB::table('surat_jalan')->where('id', $request->id_sjn)->first();
        $sjn->details = DetailSjn::where('id_sjn', $request->id_sjn)->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambahkan detail SJN',
            'sjn' => $sjn
        ]);
    }
    //End Menambah item detail Baru





    //Hapus Detail
    public function hapusDetail(Request $request, $id)
    {
        // Mendapatkan nilai id_pr sebelum menghapus data
        $id_sjn = DetailSjn::where('id', $id)->value('id_sjn');

        // Menghapus data purchase request dan detailnya
        $delete_detail_sjn = DetailSjn::where('id', $id)->delete();

        // Periksa apakah permintaan utama berhasil dihapus dan kembalikan respons yang sesuai
        if ($delete_detail_sjn) {
            return response()->json(['success' => 'Data SJN berhasil dihapus', 'deletedId' => $id, 'id_sjn' => $id_sjn]);
        } else {
            return response()->json(['error' => 'Data SJN gagal dihapus'], 500);
        }
    }
    //End Hapus Detail



    //Hapus Multiple
    public function hapusMultipleSjn(Request $request)
    {
        if ($request->has('ids')) {
            $ids = $request->input('ids');

            // Perbarui kolom id_nego di tabel detail_pr menjadi null
            // DB::table('detail_pr')
            //     ->whereIn('id_nego', $ids)
            //     ->update(['id_nego' => null]);

            // Hapus data dari tabel detail_nego yang memiliki id_po sesuai
            DB::table('detail_sjn')
                ->whereIn('id_sjn', $ids)
                ->delete();

            // Hapus data dari tabel nego
            SuratJalan::whereIn('id', $ids)->delete();

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }
    //End Hapus Multiple





    //edit detail
    public function editDetail(Request $request)
    {
        if (!$request->qty) {
            return response()->json([
                'success' => false,
                'message' => 'QTY tidak boleh kosong'
            ]);
        }
        // $request->validate([
        //     'lampiran' => 'nullable',
        //     // 'lampiran' => 'nullable|file|mimes:pdf|max:500',
        // ]);

        // if ($request->file('lampiran')) {
        //     $file = $request->file('lampiran');
        //     // dd($file);
        //     $fileName = rand() . '.' . $file->getClientOriginalExtension();
        //     // dd($fileName);
        //     $file->move(public_path('lampiran'), $fileName);
        // } else {
        //     $fileName = null;
        // }

        // Validasi data yang diterima dari request
        $request->validate([
            'id_sjn' => 'required', // Pastikan id_sr wajib ada
            // 'id' => 'required',
            'kode_material' => 'nullable',
            'barang' => 'required',
            'spek' => 'required',
            'qty' => 'nullable',
            'satuan' => 'nullable',
            'keterangan' => 'nullable',
            // 'lampiran' => 'nullable',
        ]);


        $id = $request->id;


        // Cek apakah id_sr yang diberikan valid

        // dd($detailSR);
        if (!$id) {
            // Alihkan ke fungsi createDetailSr jika detail SR tidak ditemukan
            return $this->updateDetailSjn($request);
            // dd($request->all());
        }
        $detailSJN = DetailSjn::where('id', $id)->first();
        // Update data detail SR
        $detailSJN->update([
            'id_sjn' => $request->id_sjn,
            // 'id_proyek' => $request->id_proyek,
            'kode_material' => $request->kode_material,
            'barang' => $request->barang,
            'spek' => $request->spek,
            'satuan' => $request->satuan,
            'qty' => $request->qty,
            'keterangan' => $request->keterangan,
            // 'lampiran' => $fileName,
        ]);

        $sjn = DB::table('surat_jalan')->where('id', $request->id_sjn)->first();
        $sjn->details = DetailSjn::where('id_sjn', $request->id_sjn)->get();
        return response()->json([
            'success' => true,
            'message' => 'Data detail BPM berhasil diupdate.',
            'sjn' => $sjn // Mengembalikan data detail SR yang telah diupdate
        ]);
    }
    //end edit detail






    public function cetakSjn(Request $request)
    {
        // Ambil ID dari request
        $id = $request->input('id');

        // Ambil data Surat Jalan dan Detailnya dari database
        $suratJalan = SuratJalan::find($id);
        $details = DetailSjn::where('id_sjn', $id)->get();
        $suratJalan->formatted_tgl_sjn = \Carbon\Carbon::parse($suratJalan->tgl_sjn)->format('d F Y');

        // Jika tidak ditemukan, redirect dengan pesan error
        if (!$suratJalan) {
            return redirect()->back()->with('error', 'Data Surat Jalan tidak ditemukan.');
        }

        // Load view sjn_print.blade.php dan passing data Surat Jalan dan Detailnya
        $pdf = PDF::loadView('surat_jalan.sjn_print', [
            'suratJalan' => $suratJalan,
            'details' => $details
        ]);

        // Return PDF untuk di-download atau ditampilkan di tab baru
        return $pdf->stream('surat_jalan_' . $suratJalan->no_surat . '.pdf');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Request $request)
    // {
    //     $delete_sjn_id = $request->id;

    //     // Perbarui kolom id_nego di tabel detail_pr menjadi null
    //     $update_detail_sjn = DB::table('detail_sjn')
    //         ->where('id_sjn', $delete_sjn_id)
    //         ->update(['id_sjn' => null]);

    //     // Hapus data dari tabel detail_spph yang memiliki id_nego sesuai
    //     $delete_detail_sjn = DB::table('detail_sjn')
    //         ->where('id_sjn', $delete_sjn_id)
    //         ->delete();

    //     // Setelah memperbarui detail_pr dan menghapus detail_nego, hapus data dari tabel nego
    //     $delete_sjn = DB::table('surat_jalan')->where('id', $delete_sjn_id)->delete();

    //     if ($delete_sjn) {
    //         return redirect()->route('surat_jalan.index')->with('success', 'Data SJN berhasil dihapus');
    //     } else {
    //         return redirect()->route('surat_jalan.index')->with('error', 'Data SJN gagal dihapus');
    //     }
    // }

    public function destroy(Request $request)
    {
        $delete_sjn_id = $request->id;

        // Hapus data dari tabel detail_sjn yang memiliki id_sjn sesuai
        $delete_detail_sjn = DB::table('detail_sjn')
            ->where('id_sjn', $delete_sjn_id)
            ->delete();

        // Setelah menghapus detail_sjn, hapus data dari tabel surat_jalan
        $delete_sjn = DB::table('surat_jalan')->where('id', $delete_sjn_id)->delete();

        if ($delete_sjn) {
            return redirect()->route('surat_jalan.index')->with('success', 'Data SJN berhasil dihapus beserta detailnya');
        } else {
            return redirect()->route('surat_jalan.index')->with('error', 'Data SJN gagal dihapus');
        }
    }
}
