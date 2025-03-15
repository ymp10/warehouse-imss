<?php

namespace App\Http\Controllers;

use App\Models\Bpm;
use App\Models\DetailBpm;
use App\Models\DetailPo;
use App\Models\DetailPR;
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
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use stdClass;


class BpmController extends Controller
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

        $requests = Bpm::select('bpm.*', 'keproyekan.nama_proyek as proyek_name','keproyekan.dasar_proyek as dasar_pr')
            ->join('keproyekan', 'keproyekan.id', '=', 'bpm.proyek_id')
            ->orderBy('bpm.id', 'asc')
            ->paginate(50);

        $proyeks = DB::table('keproyekan')->get();
        //  dd($requests);
        

        if ($search) {
            $requests = Bpm::where('nama_proyek', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $requests = Bpm::where("warehouse_id", $warehouse_id)->get();

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
            return view('bpm.bpm', compact('requests', 'proyeks'));
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
        $bpm = $request->id;
        $request->validate(
            [
                'proyek_id' => 'required',
                'no_bpm' => 'required',
                'dasar_bpm' => 'required',
                'tgl_bpm' => 'required',
            ],
            [
                'proyek_id.required' => 'Proyek harus diisi',
                'no_bpm.required' => 'No bpm harus diisi',
                'dasar_bpm.required' => 'Dasar bpm harus diisi',
                'tgl_bpm.required' => 'Tanggal bpm harus diisi',
            ]
        );

        if (empty($bpm)) {
            DB::table('bpm')->insert([
                'proyek_id' => $request->proyek_id,
                'no_bpm' => $request->no_bpm,
                'dasar_bpm' => $request->dasar_bpm,
                'tgl_bpm' => $request->tgl_bpm,
                'id_user' => auth()->user()->id,
            ]);

            return redirect()->route('bpm.index')->with('success', 'BPM berhasil ditambahkan');
        } else {
            DB::table('bpm')->where('id', $bpm)->update([
                'proyek_id' => $request->proyek_id,
                'no_bpm' => $request->no_bpm,
                'dasar_bpm' => $request->dasar_bpm,
                'tgl_bpm' => $request->tgl_bpm,
            ]);

            
            return redirect()->route('bpm.index')->with('success', 'BPM berhasil diupdate');
        }

        // return redirect()->route('purchase_request.index')->with('success', 'Purchase Request berhasil disimpan');

    }
        //End untuk menyimpan





        public function detailBpmSave(Request $request)
    {
        $id_bpm = $request->id;
        $id = $request->id_bpm;
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

        $bpm = Bpm::where('id', $id)->first();
        $bpm->details = DetailBpm::where('id_bpm', $bpm->id)->get();
        // $pr->details = DetailPR::where('id_pr', $id)->leftJoin('kode_material', 'kode_material.id', '=', 'detail_pr.kode_material_id')->get();
        $bpm->details = $bpm->details->map(function ($item) {
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
            'bpm' => $bpm
        ]);
    }









        public function getDetailBpm(Request $request)
    {
        $id = $request->id;
        $bpm = Bpm::select('bpm.*', 'keproyekan.nama_proyek as nama_proyek')
            ->join('keproyekan', 'keproyekan.id', '=', 'bpm.proyek_id')
            ->where('bpm.id', $id)
            ->first();
        $bpm->details = DetailBpm::where('id_bpm', $id)->get();
        // $pr->details = DetailPR::where('id_pr', $id)->leftJoin('kode_material', 'kode_material.id', '=', 'detail_pr.kode_material_id')->get();
        $bpm->details = $bpm->details->map(function ($item) {
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
            'bpm' => $bpm
        ]);
    }



    //menambah item detail baru
    public function updateDetailBpm(Request $request)
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

        $insert = DetailBpm::create([
            'id_bpm' => $request->id_bpm,
            // 'id_proyek' => $request->id_proyek,
            'kode_material' => $request->kode_material,
            'uraian' => $request->uraian,
            'spek' => $request->spek,
            'satuan' => $request->satuan,
            'qty' => $request->qty,
            'tanggal_permintaan' => $request->tanggal_permintaan,
            'keterangan' => $request->keterangan,
            // 'lampiran' => $fileName,
        ]);

        if (!$insert) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan detail BPM'
            ]);
        }

        $bpm = DB::table('bpm')->where('id', $request->id_bpm)->first();
        $bpm->details = DetailBpm::where('id_bpm', $request->id_bpm)->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambahkan detail BPM',
            'bpm' => $bpm
        ]);
    }
    //End Menambah item detail Baru





    //Hapus Detail
    public function hapusDetail(Request $request, $id)
    {
        // Mendapatkan nilai id_pr sebelum menghapus data
        $id_bpm = DetailBpm::where('id', $id)->value('id_bpm');

        // Menghapus data purchase request dan detailnya
        $delete_detail_bpm = DetailBpm::where('id', $id)->delete();

        // Periksa apakah permintaan utama berhasil dihapus dan kembalikan respons yang sesuai
        if ($delete_detail_bpm) {
            return response()->json(['success' => 'Data Request berhasil dihapus', 'deletedId' => $id, 'id_bpm' => $id_bpm]);
        } else {
            return response()->json(['error' => 'Data Request gagal dihapus'], 500);
        }
    }
    //End Hapus Detail



    //Hapus Multiple
    public function hapusMultipleBpm(Request $request)
    {
        if ($request->has('ids')) {
            $ids = $request->input('ids');

            // Perbarui kolom id_nego di tabel detail_pr menjadi null
            // DB::table('detail_pr')
            //     ->whereIn('id_nego', $ids)
            //     ->update(['id_nego' => null]);

            // Hapus data dari tabel detail_nego yang memiliki id_po sesuai
            DB::table('detail_bpm')
                ->whereIn('id_bpm', $ids)
                ->delete();

            // Hapus data dari tabel nego
            Bpm::whereIn('id', $ids)->delete();

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
            'id_bpm' => 'required', // Pastikan id_sr wajib ada
            // 'id' => 'required',
            'kode_material' => 'nullable',
            'uraian' => 'required',
            'spek' => 'required',
            'qty' => 'nullable',
            'satuan' => 'nullable',
            'tanggal_permintaan' => 'nullable',
            'keterangan' => 'nullable',
            // 'lampiran' => 'nullable',
        ]);


        $id = $request->id;


        // Cek apakah id_sr yang diberikan valid

        // dd($detailSR);
        if (!$id) {
            // Alihkan ke fungsi createDetailSr jika detail SR tidak ditemukan
            return $this->updateDetailBpm($request);
            // dd($request->all());
        }
        $detailBPM = DetailBpm::where('id', $id)->first();
        // Update data detail SR
        $detailBPM->update([
            'id_bpm' => $request->id_bpm,
            // 'id_proyek' => $request->id_proyek,
            'kode_material' => $request->kode_material,
            'uraian' => $request->uraian,
            'spek' => $request->spek,
            'satuan' => $request->satuan,
            'qty' => $request->qty,
            'tanggal_permintaan' => $request->tanggal_permintaan,
            'keterangan' => $request->keterangan,
            // 'lampiran' => $fileName,
        ]);

        $bpm = DB::table('bpm')->where('id', $request->id_bpm)->first();
        $bpm->details = DetailBpm::where('id_bpm', $request->id_bpm)->get();
        return response()->json([
            'success' => true,
            'message' => 'Data detail BPM berhasil diupdate.',
            'bpm' => $bpm // Mengembalikan data detail SR yang telah diupdate
        ]);
    }
    //end edit detail






    public function cetakBpm(Request $request)
    {
        $id = $request->id;
        $bpm = Bpm::where('bpm.id', $id)
            ->leftjoin('keproyekan', 'keproyekan.id', '=', 'bpm.proyek_id')->first();

        $bpm->pic = User::where('id', $bpm->id_user)->first()->name ?? '-';

        // Deteksi wilayah berdasarkan no_pr dengan regex dan case-insensitive
        if (preg_match('/wil1|wilayah1/i', $bpm->no_bpm)) {
            $bpm->role = "Wilayah 1";
            $bpm->kabag = "RIKA K";
        } else {
            $bpm->role = "Wilayah 2";
            $bpm->kabag = 'HARLISTA DWI O';
        }

        $bpm->bpmes = DetailBpm::select('detail_bpm.*', 'bpm.*')
            ->leftjoin('bpm', 'bpm.id', '=', 'detail_bpm.id_bpm')
            ->where('bpm.id', $id)
            ->get();

        $pdf = Pdf::loadview('bpm.bpm_print', compact('bpm'));
        $pdf->setPaper('A4', 'landscape');
        $no = $bpm->no_bpm;
        return $pdf->stream('BPM-' . $no . '.pdf');
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
    public function destroy(Request $request)
    {
        $delete_bpm_id = $request->id;

        // Hapus data dari tabel detail_bpm yang memiliki id_bpm sesuai
        $delete_detail_bpm = DB::table('detail_bpm')
            ->where('id_bpm', $delete_bpm_id)
            ->delete();

        // Setelah menghapus detail_bpm, hapus data dari tabel bpm
        $delete_bpm = DB::table('bpm')->where('id', $delete_bpm_id)->delete();

        if ($delete_bpm) {
            return redirect()->route('bpm.index')->with('success', 'Data BPM dan detailnya berhasil dihapus');
        } else {
            return redirect()->route('bpm.index')->with('error', 'Data BPM gagal dihapus');
        }
    }


}
