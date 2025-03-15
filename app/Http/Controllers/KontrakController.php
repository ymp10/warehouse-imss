<?php

namespace App\Http\Controllers;

use App\Models\Bpm;
use App\Models\DetailBpm;
use App\Models\DetailKontrak;
use App\Models\DetailPo;
use App\Models\DetailPR;
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
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use stdClass;


class KontrakController extends Controller
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

        $requests = Kontrak::select('kontrak.*')
            // ->join('keproyekan', 'keproyekan.id', '=', 'bpm.proyek_id')
            ->orderBy('kontrak.id', 'asc')
            ->paginate(50);

        // $proyeks = DB::table('keproyekan')->get();
        //  dd($requests);


        // if ($search) {
        //     $requests = Bpm::where('nama_proyek', 'LIKE', "%$search%")->paginate(50);
        // }

        if ($request->format == "json") {
            $requests = Kontrak::where("warehouse_id", $warehouse_id)->get();

            return response()->json($requests);
        } else {

            //looping the paginate
            foreach ($requests as $request) {
                $detail_kontrak = DetailKontrak::where('kontrak_id', $request->id)->get();
                //if detail_pr empty then editable true
                if ($detail_kontrak->isEmpty()) {
                    $request->editable = TRUE;
                } else {
                    //looping detail_pr then check in detailspph with id_detail_pr exist
                    foreach ($detail_kontrak as $detail) {
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
            return view('kontrak.kontrak', compact('requests'));
        }
    }


    // public function indexApps(Request $request)
    // {
    //     $search = $request->q;

    //     if (Session::has('selected_warehouse_id')) {
    //         $warehouse_id = Session::get('selected_warehouse_id');
    //     } else {
    //         $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
    //     }

    //     $requests = Bpm::select('bpm.*', 'keproyekan.nama_proyek as proyek_name')
    //         ->join('keproyekan', 'keproyekan.id', '=', 'bpm.proyek_id')
    //         ->paginate(50);

    //     $proyeks = DB::table('keproyekan')->get();

    //     if ($search) {
    //         $requests = Bpm::where('nama_proyek', 'LIKE', "%$search%")->paginate(50);
    //     }

    //     if ($request->format == "json") {
    //         $requests = Bpm::where("warehouse_id", $warehouse_id)->get();

    //         return response()->json($requests);
    //     } else {
    //         return view('home.apps.wilayah.purchase_request', compact('requests', 'proyeks'));
    //     }
    // }


    // Menampilkan form otomatis Dasar Proyek
    public function getDasarProyek(Request $request)
    {
        $proyek_id = $request->proyek_id;
        $proyek = DB::table('kontrak')->where('id', $proyek_id)->first();

        if ($proyek) {
            return response()->json(['nomor_kontrak' => $proyek->nomor_kontrak]);
        } else {
            return response()->json(['nomor_kontrak' => ''], 404);
        }
    }

    //untuk menyimpan
    public function store(Request $request)
    {
        //Store untuk menambah data
        $kontrak = $request->id;
        $request->validate(
            [
                // 'tanggal' => 'required',
                // 'nomor_kontrak' => 'required',
                // 'nama_pekerjaan' => 'required',
                // 'nilai_pekerjaan' => 'required',
                // 'nama_pelanggan' => 'required',
                // 'nilai' => 'required',
                'tanggal' => 'required',
                'kode_proyek' => 'required',
                'nomor_kontrak' => 'required',
                'nama_pekerjaan' => 'required',
                'nilai_pekerjaan' => 'required',
                'nama_pelanggan' => 'required',
                'status' => 'nullable',
                // 'nilai' => 'nullable',
            ],
            [
                'tanggal.required' => 'Tanggal harus diisi',
                'kode_proyek.required' => 'Kode Proyek harus diisi',
                'nomor_kontrak.required' => 'Nomor Kontrak harus diisi',
                'nama_pekerjaan.required' => 'Nama Pekerjaan harus diisi',
                'nilai_pekerjaan.required' => 'Nilai Pekerjaan harus diisi',
                'nama_pelanggan.required' => 'Nama Pelanggan harus diisi',
                'status.required' => 'Status harus diisi',
                // 'nilai.required' => 'Nilai harus diisi',
            ]
        );

        if (empty($kontrak)) {
            DB::table('kontrak')->insert([
                'tanggal' => $request->tanggal,
                'kode_proyek' => $request->kode_proyek,
                'nomor_kontrak' => $request->nomor_kontrak,
                'nama_pekerjaan' => $request->nama_pekerjaan,
                'nilai_pekerjaan' => $request->nilai_pekerjaan,
                'nama_pelanggan' => $request->nama_pelanggan,
                'status' => $request->status,
                // 'nilai' => $request->nilai,
                // 'id_user' => auth()->user()->id,
            ]);

            return redirect()->route('kontrak.index')->with('success', 'Kontrak berhasil ditambahkan');
        } else {
            DB::table('kontrak')->where('id', $kontrak)->update([
                'tanggal' => $request->tanggal,
                'kode_proyek' => $request->kode_proyek,
                'nomor_kontrak' => $request->nomor_kontrak,
                'nama_pekerjaan' => $request->nama_pekerjaan,
                'nilai_pekerjaan' => $request->nilai_pekerjaan,
                'nama_pelanggan' => $request->nama_pelanggan,
                'status' => $request->status,
                // 'nilai' => $request->nilai,
            ]);


            return redirect()->route('kontrak.index')->with('success', 'Kontrak berhasil diupdate');
        }

        // return redirect()->route('purchase_request.index')->with('success', 'Purchase Request berhasil disimpan');

    }
    //End untuk menyimpan





    public function detailKontrakSave(Request $request)
    {
        $id_bpm = $request->id;
        $id = $request->id_kontrak;
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

        $kontrak = Kontrak::where('id', $id)->first();
        $kontrak->details = DetailKontrak::where('id_kontrak', $kontrak->id)->get();
        // $pr->details = DetailPR::where('id_pr', $id)->leftJoin('kode_material', 'kode_material.id', '=', 'detail_pr.kode_material_id')->get();
        $kontrak->details = $kontrak->details->map(function ($item) {
            $item->spek = $item->spek ? $item->spek : '';
            $item->nomor_dokumen = $item->nomor_dokumen ? $item->nomor_dokumen : '';
            $item->tanggal_dokumen = $item->tanggal_dokumen ? $item->tanggal_dokumen : '';
            $item->perihal = $item->perihal ? $item->perihal : '';
            $item->keterangan = $item->keterangan ? $item->keterangan : '';
            $item->lampiran = $item->lampiran ? $item->lampiran : '';
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
            'kontrak' => $kontrak
        ]);
    }








    // get detail kontrak
    public function getDetailKontrak(Request $request)
    {
        $id = $request->id;
        $kontrak = Kontrak::select('kontrak.*')
            // ->join('keproyekan', 'keproyekan.id', '=', 'bpm.proyek_id')
            ->where('kontrak.id', $id)
            ->first();


        $kontrak->details = DetailKontrak::where('kontrak_id', $id)->get();
        // $pr->details = DetailPR::where('id_pr', $id)->leftJoin('kode_material', 'kode_material.id', '=', 'detail_pr.kode_material_id')->get();
        $kontrak->details = $kontrak->details->map(function ($item) {
            // $item->spek = $item->spek ? $item->spek : '';
            // $item->keterangan = $item->keterangan ? $item->keterangan : '';
            $item->nomor_dokumen = $item->nomor_dokumen ? $item->nomor_dokumen : '';
            $item->tanggal_dokumen = $item->tanggal_dokumen ? $item->tanggal_dokumen : '';
            $item->perihal = $item->perihal ? $item->perihal : '';
            $item->keterangan = $item->keterangan ? $item->keterangan : '';
            // $item->lampiran = $item->lampiran ? $item->lampiran : '';
            // $item->nomor_spph = Spph::where('id', $item->id_spph)->first()->nomor_spph ?? '';
            // $item->no_po = Purchase_Order::where('id', $item->id_po)->first()->no_po ?? '';
            // $item->userRole = User::where('id', $item->user_id)->first()->role ?? '';
            // $item->no_sph = $item->no_sph ? $item->no_sph : '';
            // $item->tanggal_sph = $item->tanggal_sph ? $item->tanggal_sph : '';
            // $item->no_just = $item->no_just ? $item->no_just : '';
            // $item->tanggal_just = $item->tanggal_just ? $item->tanggal_just : '';
            // $item->no_nego1 = $item->no_nego1 ? $item->no_nego1 : '';
            // $item->tanggal_nego1 = $item->tanggal_nego1 ? $item->tanggal_nego1 : '';
            // $item->batas_nego1 = $item->batas_nego1 ? $item->batas_nego1 : '';
            // $item->no_nego2 = $item->no_nego2 ? $item->no_nego2 : '';
            // $item->tanggal_nego2 = $item->tanggal_nego2 ? $item->tanggal_nego2 : '';
            // $item->batas_nego2 = $item->batas_nego2 ? $item->batas_nego2 : '';
            // $item->batas_akhir = Purchase_Order::leftjoin('detail_po', 'detail_po.id_po', '=', 'purchase_order.id')->where('detail_po.id_detail_pr', $item->id)->first()->batas_akhir ?? '-';

            // $ekspedisi = RegistrasiBarang::where('id_barang', $item->id)->first();
            // if ($ekspedisi) {
            //     $keterangan = $ekspedisi->keterangan;
            //     $tanggal = $ekspedisi->created_at;
            //     $tanggal = Carbon::parse($tanggal)->isoFormat('D MMMM Y');
            //     $keterangan = $keterangan . ', ' . $tanggal;
            // } else {
            //     $keterangan = null;
            // }
            // $item->ekspedisi = $keterangan;

            // //qc
            // if ($ekspedisi) {
            //     $qc = Lppb::where('id_registrasi_barang', $ekspedisi->id)->first();
            // } else {
            //     $qc = null;
            // }

            // if ($qc) {
            //     $penerimaan = $qc->penerimaan;
            //     $hasil_ok = $qc->hasil_ok;
            //     $hasil_nok = $qc->hasil_nok;
            //     $tanggal_qc = $qc->created_at;
            //     $tanggal_qc = Carbon::parse($qc->created_at)->isoFormat('D MMMM Y');
            //     $qc = new stdClass();
            //     $qc->penerimaan = $penerimaan;
            //     $qc->hasil_ok = $hasil_ok;
            //     $qc->hasil_nok = $hasil_nok;
            //     $qc->tanggal_qc = $tanggal_qc;
            // } else {
            //     $penerimaan = null;
            //     $hasil_ok = null;
            //     $hasil_nok = null;
            //     $tanggal_qc = null;
            //     $qc = null;
            // }

            // $item->qc = $qc;

            // //countdown = waktu - date now
            // $targetDate = Carbon::parse($item->waktu);
            // $currentDate = Carbon::now();
            // $diff = $currentDate->diff($targetDate);
            // $remainingDays = $diff->days;

            // $referenceDate = Carbon::parse($item->waktu); // Change this to your desired reference date

            // if ($currentDate->lessThan($referenceDate)) {
            //     // If the current date is before the reference date
            //     $item->countdown = "$remainingDays  Hari Sebelum Waktu Penyelesaian";
            //     $item->backgroundcolor = "#FF0000"; // Red background
            // } elseif ($currentDate->greaterThanOrEqualTo($referenceDate)) {
            //     // If the current date is on or after the reference date
            //     $item->countdown = "$remainingDays Hari Setelah Waktu Penyelesaian";
            //     $item->backgroundcolor = "#008000"; // Green background
            // }
            return $item;
        });
        return response()->json([
            'kontrak' => $kontrak
        ]);
    }



    //menambah item detail baru
    // public function updateDetailKontrak(Request $request)
    // {
    //     // if (!$request->qty) {
    //     //     return response()->json([
    //     //         'success' => false,
    //     //         'message' => 'QTY tidak boleh kosong'
    //     //     ]);
    //     // }


    //     $request->validate([
    //         'lampiran' => 'nullable',
    //         // 'lampiran' => 'nullable|file|mimes:pdf|max:500',
    //     ]);

    //     if ($request->file('lampiran')) {
    //         $file = $request->file('lampiran');
    //         // dd($file);
    //         $fileName = rand() . '.' . $file->getClientOriginalExtension();
    //         // dd($fileName);
    //         $file->move(public_path('lampiran'), $fileName);
    //     } else {
    //         $fileName = null;
    //     }


    //     $insert = DetailKontrak::create([
    //         'kontrak_id' => $request->kontrak_id,
    //         // 'id_proyek' => $request->id_proyek,
    //         'nomor_dokumen' => $request->nomor_dokumen,
    //         'tanggal_dokumen' => $request->tanggal_dokumen,
    //         'perihal' => $request->perihal,
    //         'keterangan' => $request->keterangan,

    //         'lampiran' => $fileName,
    //     ]);

    //     if (!$insert) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal menambahkan detail BPM'
    //         ]);
    //     }

    //     $kontrak = DB::table('kontrak')->where('id', $request->kontrak_id)->first();
    //     $kontrak->details = DetailKontrak::where('kontrak_id', $request->kontrak_id)->get();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Berhasil menambahkan detail Kontrak',
    //         'kontrak' => $kontrak
    //     ]);
    // }

    public function updateDetailKontrak(Request $request)
    {
        $request->validate([
            'lampiran' => 'nullable', // Validasi file jika diperlukan
            // Tambahkan validasi lain jika diperlukan
        ]);

        // Inisialisasi nama file
        $fileName = null;

        // Proses upload file lampiran jika ada
        if ($request->file('lampiran')) {
            $file = $request->file('lampiran');
            // Gunakan nama asli file
            $fileName = $file->getClientOriginalName();

            // Pindahkan file ke folder 'lampiran' tanpa mengubah nama asli
            $file->move(public_path('lampiran'), $fileName);
        }

        // Simpan data ke database
        $insert = DetailKontrak::create([
            'kontrak_id' => $request->kontrak_id ?? '', // Kosongkan jika null
            'nomor_dokumen' => $request->nomor_dokumen ?? '', // Kosongkan jika null
            'tanggal_dokumen' => $request->tanggal_dokumen ?? '', // Kosongkan jika null
            'perihal' => $request->perihal ?? '', // Kosongkan jika null
            'keterangan' => $request->keterangan ?? '', // Kosongkan jika null
            'lampiran' => $fileName, // Simpan nama asli file (tidak boleh null)
        ]);

        // Cek apakah penyimpanan berhasil
        if (!$insert) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan detail Kontrak'
            ]);
        }

        // Ambil data kontrak yang diupdate dan detailnya
        $kontrak = DB::table('kontrak')->where('id', $request->kontrak_id)->first();
        $kontrak->details = DetailKontrak::where('kontrak_id', $request->kontrak_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambahkan detail Kontrak',
            'kontrak' => $kontrak
        ]);
    }
    //End Menambah item detail Baru




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
        DetailKontrak::where('id', $detailId)->update(['lampiran' => $fileName]);

        return redirect()->back()->with('success', 'File berhasil diupload');
    }





    //Hapus Detail
    public function hapusDetail(Request $request, $id)
    {
        // Mendapatkan nilai id_pr sebelum menghapus data
        $id_kontrak = DetailKontrak::where('id', $id)->value('kontrak_id');

        // Menghapus data purchase request dan detailnya
        $delete_detail_kontrak = DetailKontrak::where('id', $id)->delete();

        // Periksa apakah permintaan utama berhasil dihapus dan kembalikan respons yang sesuai
        if ($delete_detail_kontrak) {
            return response()->json(['success' => 'Data Request berhasil dihapus', 'deletedId' => $id, 'kontrak_id' => $id_kontrak]);
        } else {
            return response()->json(['error' => 'Data Request gagal dihapus'], 500);
        }
    }
    //End Hapus Detail



    //Hapus Multiple
    public function hapusMultipleKontrak(Request $request)
    {
        if ($request->has('ids')) {
            $ids = $request->input('ids');

            // Perbarui kolom id_nego di tabel detail_pr menjadi null
            // DB::table('detail_pr')
            //     ->whereIn('id_nego', $ids)
            //     ->update(['id_nego' => null]);

            // Hapus data dari tabel detail_nego yang memiliki id_po sesuai
            DB::table('detail_kontrak')
                ->whereIn('kontrak_id', $ids)
                ->delete();

            // Hapus data dari tabel nego
            Kontrak::whereIn('id', $ids)->delete();

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }
    //End Hapus Multiple





    //edit detail
    // public function editDetail(Request $request)
    // {
    //     // if (!$request->qty) {
    //     //     return response()->json([
    //     //         'success' => false,
    //     //         'message' => 'QTY tidak boleh kosong'
    //     //     ]);
    //     // }

    //     // $request->validate([
    //     //     'lampiran' => 'nullable',
    //     //     // 'lampiran' => 'nullable|file|mimes:pdf|max:500',
    //     // ]);

    //     // if ($request->file('lampiran')) {
    //     //     $file = $request->file('lampiran');
    //     //     // dd($file);
    //     //     $fileName = rand() . '.' . $file->getClientOriginalExtension();
    //     //     // dd($fileName);
    //     //     $file->move(public_path('lampiran'), $fileName);
    //     // } else {
    //     //     $fileName = null;
    //     // }

    //     // Validasi data yang diterima dari request
    //     $request->validate([
    //         'kontrak_id' => 'required', // Pastikan id_sr wajib ada
    //         // 'id' => 'required',
    //         'nomor_dokumen' => 'nullable',
    //         'tanggal_dokumen' => 'nullable',
    //         'perihal' => 'nullable',
    //         'keterangan' => 'nullable',
    //         // 'satuan' => 'nullable',
    //         // 'tanggal_permintaan' => 'nullable',
    //         // 'keterangan' => 'nullable',
    //         // 'lampiran' => 'nullable',
    //     ]);


    //     $id = $request->id;


    //     // Cek apakah id_sr yang diberikan valid

    //     // dd($detailSR);
    //     if (!$id) {
    //         // Alihkan ke fungsi createDetailSr jika detail SR tidak ditemukan
    //         return $this->updateDetailKontrak($request);
    //         // dd($request->all());
    //     }
    //     $detailKONTRAK = DetailKontrak::where('id', $id)->first();
    //     // Update data detail SR
    //     $detailKONTRAK->update([
    //         'kontrak_id' => $request->id_kontrak,
    //         // 'id_proyek' => $request->id_proyek,
    //         'nomor_dokumen' => $request->nomor_dokumen,
    //         'tanggal_dokumen' => $request->tanggal_dokumen,
    //         'perihal' => $request->perihal,
    //         'keterangan' => $request->keterangan,
    //         // 'qty' => $request->qty,
    //         // 'tanggal_permintaan' => $request->tanggal_permintaan,
    //         // 'keterangan' => $request->keterangan,
    //         // 'lampiran' => $fileName,
    //     ]);

    //     $kontrak = DB::table('kontrak')->where('id', $request->id_kontrak)->first();
    //     $kontrak->details = DetailKontrak::where('kontrak_id', $request->id_kontrak)->get();
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Data detail Kontrak berhasil diupdate.',
    //         'kontrak' => $kontrak // Mengembalikan data detail SR yang telah diupdate
    //     ]);
    // }

    //     public function editDetail(Request $request)
    // {
    //     $request->validate([
    //         'kontrak_id' => 'required|exists:kontrak,id', // Validasi ID kontrak
    //         'nomor_dokumen' => 'nullable|string|max:255',
    //         'tanggal_dokumen' => 'nullable|date',
    //         'perihal' => 'nullable|string|max:255',
    //         'keterangan' => 'nullable|string|max:500',
    //         // Validasi lampiran jika diperlukan
    //     ]);

    //     $id = $request->id;

    //     if (!$id) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'ID detail kontrak tidak ditemukan.'
    //         ], 404);
    //     }

    //     $detailKONTRAK = DetailKontrak::where('id', $id)->first();

    //     if (!$detailKONTRAK) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Detail kontrak tidak ditemukan.'
    //         ], 404);
    //     }

    //     // Proses menyimpan lampiran
    //     $fileName = null;
    //     if ($request->file('lampiran')) {
    //         $file = $request->file('lampiran');
    //         $fileName = rand() . '.' . $file->getClientOriginalExtension();
    //         $file->move(public_path('lampiran'), $fileName);
    //     }

    //     // Update detail kontrak
    //     $detailKONTRAK->update([
    //         'kontrak_id' => $request->kontrak_id,
    //         'nomor_dokumen' => $request->nomor_dokumen,
    //         'tanggal_dokumen' => $request->tanggal_dokumen,
    //         'perihal' => $request->perihal,
    //         'keterangan' => $request->keterangan,
    //         'lampiran' => $fileName,
    //     ]);

    //     $kontrak = DB::table('kontrak')->where('id', $request->kontrak_id)->first();
    //     $kontrak->details = DetailKontrak::where('kontrak_id', $request->kontrak_id)->get();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Data detail Kontrak berhasil diupdate.',
    //         'kontrak' => $kontrak,
    //     ]);
    // }

    // public function editDetail(Request $request)
    // {
    //     $request->validate([
    //         'kontrak_id' => 'required|exists:kontrak,id', // Validasi ID kontrak
    //         'nomor_dokumen' => 'nullable|string|max:255',
    //         'tanggal_dokumen' => 'nullable|date',
    //         'perihal' => 'nullable|string|max:255',
    //         'keterangan' => 'nullable|string|max:500',
    //         // Validasi lampiran jika diperlukan
    //     ]);

    //     $id = $request->id;

    //     if (!$id) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'ID detail kontrak tidak ditemukan.'
    //         ], 404);
    //     }

    //     $detailKONTRAK = DetailKontrak::where('id', $id)->first();

    //     if (!$detailKONTRAK) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Detail kontrak tidak ditemukan.'
    //         ], 404);
    //     }

    //     // Inisialisasi nama file
    //     $fileName = null;

    //     // Proses upload file lampiran jika ada
    //     if ($request->file('lampiran')) {
    //         $file = $request->file('lampiran');
    //         // Gunakan nama asli file
    //         $fileName = $file->getClientOriginalName();

    //         // Pindahkan file ke folder 'lampiran' tanpa mengubah nama asli
    //         $file->move(public_path('lampiran'), $fileName);
    //     }

    //     // Update detail kontrak
    //     $detailKONTRAK->update([
    //         'kontrak_id' => $request->kontrak_id,
    //         'nomor_dokumen' => $request->nomor_dokumen,
    //         'tanggal_dokumen' => $request->tanggal_dokumen,
    //         'perihal' => $request->perihal,
    //         'keterangan' => $request->keterangan,
    //         'lampiran' => $fileName, // Lampiran akan tetap null jika tidak ada file yang diunggah
    //     ]);

    //     // Ambil kontrak terkait
    //     $kontrak = DB::table('kontrak')->where('id', $request->kontrak_id)->first();
    //     $kontrak->details = DetailKontrak::where('kontrak_id', $request->kontrak_id)->get();

    //     // Periksa apakah kontrak ditemukan
    //     if (!$kontrak) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Kontrak tidak ditemukan.'
    //         ], 404);
    //     }

    //     // Ambil detail kontrak terkait
    //     $kontrak->details = DetailKontrak::where('kontrak_id', $request->kontrak_id)->get();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Data detail kontrak berhasil diupdate.',
    //         'kontrak' => $kontrak,
    //     ]);
    // }


    public function editDetail(Request $request)
    {
        $request->validate([
            'kontrak_id' => 'required|exists:kontrak,id', // Validasi ID kontrak
            'nomor_dokumen' => 'nullable|string|max:255',
            'tanggal_dokumen' => 'nullable|date',
            'perihal' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:500',
            // Validasi lampiran jika diperlukan
        ]);

        $id = $request->id;

        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'ID detail kontrak tidak ditemukan.'
            ], 404);
        }

        $detailKONTRAK = DetailKontrak::where('id', $id)->first();

        if (!$detailKONTRAK) {
            return response()->json([
                'success' => false,
                'message' => 'Detail kontrak tidak ditemukan.'
            ], 404);
        }

        // Inisialisasi nama file
        $fileName = null;

        // Proses upload file lampiran jika ada
        if ($request->file('lampiran')) {
            $file = $request->file('lampiran');
            // Gunakan nama asli file
            $fileName = $file->getClientOriginalName();

            // Pindahkan file ke folder 'lampiran' tanpa mengubah nama asli
            $file->move(public_path('lampiran'), $fileName);
        }

        // Update detail kontrak
        $detailKONTRAK->update([
            'kontrak_id' => $request->kontrak_id,
            'nomor_dokumen' => $request->nomor_dokumen,
            'tanggal_dokumen' => $request->tanggal_dokumen,
            'perihal' => $request->perihal,
            'keterangan' => $request->keterangan,
            'lampiran' => $fileName, // Lampiran akan tetap null jika tidak ada file yang diunggah
        ]);

        // Ambil kontrak terkait
        $kontrak = DB::table('kontrak')->where('id', $request->kontrak_id)->first();
        $kontrak->details = DetailKontrak::where('kontrak_id', $request->kontrak_id)->get();

        // Periksa apakah kontrak ditemukan
        if (!$kontrak) {
            return response()->json([
                'success' => false,
                'message' => 'Kontrak tidak ditemukan.'
            ], 404);
        }

        // Ambil detail kontrak terkait
        $kontrak->details = DetailKontrak::where('kontrak_id', $request->kontrak_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Data detail kontrak berhasil diupdate.',
            'kontrak' => $kontrak,
        ]);
    }
    //end edit detail






    





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
    //     $delete_bpm_id = $request->id;

    //     // Perbarui kolom id_nego di tabel detail_pr menjadi null
    //     $update_detail_bpm = DB::table('detail_bpm')
    //         ->where('id_bpm', $delete_bpm_id)
    //         ->update(['id_bpm' => null]);

    //     // Hapus data dari tabel detail_spph yang memiliki id_nego sesuai
    //     $delete_detail_bpm = DB::table('detail_bpm')
    //         ->where('id_bpm', $delete_bpm_id)
    //         ->delete();

    //     // Setelah memperbarui detail_pr dan menghapus detail_nego, hapus data dari tabel nego
    //     $delete_bpm = DB::table('bpm')->where('id', $delete_bpm_id)->delete();

    //     if ($delete_bpm) {
    //         return redirect()->route('bpm.index')->with('success', 'Data BPM berhasil dihapus');
    //     } else {
    //         return redirect()->route('bpm.index')->with('error', 'Data BPM gagal dihapus');
    //     }
    // }

    public function destroy(Request $request)
    {
        $delete_kontrak_id = $request->id;

        // Hapus data dari tabel detail_bpm yang memiliki id_bpm sesuai
        $delete_detail_kontrak = DB::table('detail_kontrak')
            ->where('kontrak_id', $delete_kontrak_id)
            ->delete();

        // Setelah menghapus detail_bpm, hapus data dari tabel bpm
        $delete_kontrak = DB::table('kontrak')->where('id', $delete_kontrak_id)->delete();

        if ($delete_kontrak) {
            return redirect()->route('kontrak.index')->with('success', 'Data Kontrak dan detailnya berhasil dihapus');
        } else {
            return redirect()->route('kontrak.index')->with('error', 'Data Kontrak gagal dihapus');
        }
    }
}
