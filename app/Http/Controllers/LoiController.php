<?php

namespace App\Http\Controllers;

use App\Models\DetailLoi;
use Carbon\Carbon;
use App\Models\Nego;
use App\Models\Spph;
use App\Models\Vendor;
use App\Models\Kontrak;
use App\Models\DetailPR;
use App\Models\DetailNego;
use App\Models\Keproyekan;
use App\Models\Loi;
use App\Models\LoiLampiran;
use Illuminate\Http\Request;
use App\Models\Purchase_Order;
use App\Models\PurchaseRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

class LoiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {
        $search = $request->q;

        if (Session::has('selected_warehouse_id')) {
            $warehouse_id = Session::get('selected_warehouse_id');
        } else {
            $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
        }
        $loies = Loi::paginate(50);
        foreach ($loies as $key => $item) {
            $id = json_decode($item->vendor_id);
            $item->vendor = Vendor::whereIn('id', $id)->get();
            $item->vendor = $item->vendor->map(function ($item) {
                return $item->nama;
            });
            //change $item->vendor collection to array
            $item->vendor = $item->vendor->toArray();
            $item->vendor = implode(', ', $item->vendor);

            //lampiran bisa lebih dari 1
            $lampiran = LoiLampiran::where('loi_id', $item->id)->pluck('file')->toArray();
            $item->lampiran = implode(', ', $lampiran);
            // $item->lampiran = json_decode($item->lampiran); 
        }
        $vendors = Vendor::all();
        // dd($spphes);
        if ($search) {
            $loies = Loi::where('tanggal_loi', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $categories = Loi::where("warehouse_id", $warehouse_id)->get();

            return response()->json($categories);
        } else {
            return view('loi.loi', compact('loies', 'vendors'));
        }
    }


    //** */
    public function indexApps(Request $request)
    {
        $search = $request->q;

        if (Session::has('selected_warehouse_id')) {
            $warehouse_id = Session::get('selected_warehouse_id');
        } else {
            $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
        }

        $loies = Loi::paginate(50);
        $vendors = Vendor::all();

        if ($search) {
            $loies = Loi::where('tanggal_loi', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $categories = Loi::where("warehouse_id", $warehouse_id)->get();

            return response()->json($categories);
        } else {
            return view('home.apps.logistik.loi', compact('loies', 'vendors'));
        }
    }
    //** */



    //** */
    function FunctionCountPages($path)
    {
        $pdftextfile = file_get_contents($path);
        $pagenumber = preg_match_all("/\/Page\W/", $pdftextfile, $dummy);
        return $pagenumber;
    }
    //** */



    // Simpan dan edit
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $loi = $request->id;
        // if (Session::has('selected_warehouse_id')) {
        //     $warehouse_id = Session::get('selected_warehouse_id');
        // } else {
        //     $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
        // }
        // dd($request->all());

        $request->validate([
            'nomor_loi' => 'required',
            'id_pr' => 'required',
            'nomor_pr' => 'required',
            // 'lampiran' => 'required',
            'vendor' => 'required',
            'tanggal_loi' => 'required',
            'batas_loi' => 'required',
            'perihal' => 'required',

            // 'penerima' => 'required',
            // 'alamat' => 'required'
        ], [
            'nomor_loi.required' => 'Nomor Loi harus diisi',
            'id_pr.required' => 'ID pr harus diisi',
            'nomor_pr.required' => 'Nomor pr harus diisi',
            // 'lampiran.required' => 'Lampiran harus diisi',
            'vendor.required' => 'Vendor harus diisi',
            'tanggal_loi.required' => 'Tanggal Loi harus diisi',
            'batas_loi.required' => 'Batas Loi harus diisi',
            'perihal.required' => 'Perihal harus diisi',
            'penerima.required' => 'Penerima harus diisi',
            'alamat.required' => 'Alamat harus diisi',

        ]);

        $data = [
            'nomor_loi' => $request->nomor_loi,
            'id_pr' => $request->id_pr,
            'nomor_pr' => $request->nomor_pr,
            'nomor_po' => $request->nomor_po,
            'vendor_id' => json_encode($request->vendor),
            'tanggal_po' => $request->tanggal_po,
            'tanggal_loi' => $request->tanggal_loi,
            'batas_loi' => $request->batas_loi,
            'perihal' => $request->perihal,
            'penerima' => json_encode($request->penerima),
            'alamat' => json_encode($request->alamat),
            'keterangan_loi' => $request->keterangan_loi,
        ];

        // Ubah data vendor menjadi ID berdasarkan nama
        $vendorNames = json_decode($data['vendor_id']);
        $vendors = Vendor::whereIn('nama', $vendorNames)->pluck('id')->toArray();
        $data['vendor_id'] = json_encode($vendors);


        // dd($data);

        if (empty($loi)) {
            $add = Loi::create($data);

            // Check if 'lampiran' exists and is not null
            if ($request->hasFile('lampiran')) {
                $files = $request->file('lampiran');
                foreach ($files as $file) {
                    $file_name = rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('lampiran'), $file_name);
                    LoiLampiran::create([
                        'loi_id' => $add->id,
                        'file' => $file_name,
                        'tipe' => $this->FunctionCountPages(public_path('lampiran/' . $file_name))
                    ]);
                }
            }

            if ($add) {
                return redirect()->route('loi.index')->with('success', 'Loi berhasil ditambahkan');
            } else {
                return redirect()->route('loi.index')->with('error', 'Loi gagal ditambahkan');
            }
        } else {
            $update = Loi::where('id', $loi)->update($data);
            if ($request->hasFile('lampiran')) {
                $files = $request->file('lampiran');
                foreach ($files as $file) {
                    $file_name = rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('lampiran'), $file_name);
                    LoiLampiran::create([
                        'loi_id' => $loi,
                        'file' => $file_name,
                        'tipe' => $this->FunctionCountPages(public_path('lampiran/' . $file_name))
                    ]);
                }
            }
            // Ambil nama lampiran yang diinginkan dari request
            $nama_lampiran_baru = explode(', ', $request->nama_lampiran); //masih error


            // Ambil semua lampiran yang terkait dengan $spph dari database
            $existing_files = explode(', ', $request->lampiran_awal);

            // dd($nama_lampiran_baru);

            // Loop untuk setiap lampiran yang sudah ada
            foreach ($existing_files as $existing_file) {
                // Jika lampiran tidak termasuk dalam nama lampiran yang baru diupload, hapus dari database dan filesystem
                if (!in_array($existing_file, $nama_lampiran_baru)) {
                    // Hapus dari database
                    LoiLampiran::where('loi_id', $loi)->where('file', $existing_file)->delete();

                    // Hapus dari filesystem jika perlu
                    // $file_path = public_path('lampiran/' . $existing_file);
                    // if (file_exists($file_path)) {
                    //     unlink($file_path);
                    // }
                }
            }

            // if ($request->hasFile('lampiran')) {
            //     $files = $request->file('lampiran');
            //     foreach ($files as $file) {
            //         $file_name = rand() . '.' . $file->getClientOriginalExtension();
            //         $file->move(public_path('lampiran'), $file_name);

            //         // Find the existing SpphLampiran record to update
            //         $lampiran = SpphLampiran::where('spph_id', $spph)->first();

            //         if ($lampiran) {
            //             // Update the existing record
            //             $lampiran->update([
            //                 'file' => $file_name,
            //                 'tipe' => $this->FunctionCountPages(public_path('lampiran/' . $file_name))
            //             ]);
            //         }
            //     }
            // }


            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'SPPH berhasil diupdate',
            //     'data' => $update
            // ]);

            if ($update) {
                return redirect()->route('loi.index')->with('success', 'loi berhasil diupdate');
            } else {
                return redirect()->route('loi.index')->with('error', 'loi gagal diupdate');
            }
        }

        // return redirect()->route('spph.index')->with('success', 'SPPH berhasil disimpan');
    }

    // End simpan dan edit
    public function QtyLoiSave(Request $request)
    {
        // Validasi array
        $request->validate([
            'data' => 'required|array',
            'data.*.id' => 'required|integer',
            'data.*.qty_loi1' => 'required|numeric'
        ]);

        foreach ($request->data as $item) {
            $loiDetail = DetailPR::find($item['id']);

            if (!$loiDetail) continue;

            // Pastikan qty2 tidak lebih besar dari qty_spph
            if ($loiDetail->qty < $item['qty_loi1']) {
                return response()->json(['error' => 'Qty tidak boleh lebih besar dari Qty1'], 400);
            }

            // // Update data
            // $loiDetail->qty_loi -= $item['qty_loi1'];
            // $loiDetail->qty_loi1 = $item['qty_loi1'];
            // $loiDetail->save();
            $detailLoi = DetailLoi::create([
                'loi_id' => $item['loi_id'],
                'id_detail_pr' => $item['id'],
                'loi_qty' => $item['qty_loi1'],
                'id_del_loi' => 0,
            ]);
        }

        return response()->json(['success' => true]);
    }


    //simpan detailloi
    public function detailLoiSave(Request $request)
    {
        // Validasi data yang masuk
        $request->validate([
            'id' => 'required|integer',
            'id_loi' => 'required|integer',
            'id_detail_loi' => 'required|integer', //ggwp
            'harga_per_unit' => 'required|numeric',

        ]);



        // Ambil data dari request
        $id = $request->id;
        $id_loi = $request->id_loi;
        $id_detail_loi = $request->id_detail_loi;
        $harga_per_unit = $request->harga_per_unit;


        // dd($request->all());

        // Update data di tabel DetailLoi
        $updated = DetailLoi::where('id', $id_detail_loi)->update([
            'harga' => $harga_per_unit,

        ]);

        // dd($updated);
        if (!$updated) {
            return response()->json(['message' => 'Gagal memperbarui data'], 500);
        }

        // Ambil data Loi dan detailnya setelah update
        $loi = Loi::select('loi.*')
            // ->leftjoin('vendor', 'vendor.id', '=', 'nego.vendor_id')
            // ->leftjoin('keproyekan', 'keproyekan.id', '=', 'nego.proyek_id')
            ->where('loi.id', $request->id_loi)
            ->first();

        if (!$loi) {
            return response()->json(['message' => 'Data Loi tidak ditemukan'], 404);
        }

        $loi->details = DetailLoi::where('detail_loi.loi_id', $loi->id)
            ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_loi.id_detail_pr')
            // ->leftJoin('spph', 'spph.id', '=', 'detail_pr.spph_id') // Tambahkan join ke tabel spph
            ->select(
                'detail_pr.*',
                'detail_loi.id as id_detail_loi',
                'detail_loi.harga as harga_per_unit',
                // 'detail_loi.harga_imss as harga_per_unit_imss',
                'detail_loi.loi_qty',
                // 'spph.nomor_spph' // Tambahkan kolom nomor_spph
            )
            ->get();


        return response()->json([
            'loi' => $loi
        ]);
    }
    //End simpan detail loi





    // Hapus
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $delete_loi_id = $request->id;
        $detail_loi = DB::table('detail_loi')->where('loi_id', $delete_loi_id)->first();
        if ($detail_loi) {
            // Ambil data detail_pr terkait
            $detail_pr = DB::table('detail_pr')->where('id', $detail_loi->id_detail_pr)->first();

            if ($detail_loi && $detail_pr) {
                if (!$detail_loi->id_del_loi) {
                    DB::table('detail_pr')->where('id', $detail_pr->id)->update(['qty_loi' => $detail_pr->qty]);
                } else {
                    // Jika ada id_del_loi, tambahkan qty_spph ke qty1
                    $loi_qty = $detail_loi->loi_qty;
                    $new_qty_loi = ($detail_pr->qty_loi ?? 0) + $loi_qty;
                    DB::table('detail_pr')->where('id', $detail_pr->id)->update(['qty_loi' => $new_qty_loi]);
                }
            }
        }

        // Perbarui kolom id_loi di tabel detail_pr menjadi null
        $update_detail_pr = DB::table('detail_pr')
            ->where('id_loi', $delete_loi_id)
            ->update(['id_loi' => null]);

        // Hapus data dari tabel detail_spph yang memiliki id_loi sesuai
        $delete_detail_loi = DB::table('detail_loi')
            ->where('loi_id', $delete_loi_id)
            ->delete();

        // Setelah memperbarui detail_pr dan menghapus detail_loi, hapus data dari tabel loi
        $delete_loi = DB::table('loi')->where('id', $delete_loi_id)->delete();

        if ($delete_loi) {
            return redirect()->route('loi.index')->with('success', 'Data loi berhasil dihapus, id_loi pada detail_pr diubah menjadi null, dan detail_loi berhasil dihapus');
        } else {
            return redirect()->route('loi.index')->with('error', 'Data loi gagal dihapus');
        }
    }
    // End Hapus




    //hapus detail loi
    public function destroyDetailLoi(Request $request)
    {
        // Menerima data dari request
        $id = $request->id;
        $id_loi = $request->id_loi;
        $id_detail_pr = $request->id_detail_pr;
        $id_detail_loi = $request->id_detail_loi; //ggwp

        // dd($request->all());
        // Mengambil data detail_spph dan detail_pr untuk validasi
        $detail_loi = DetailLoi::find($id_detail_loi);
        $detail_pr = DetailPR::find($id);

        // Validasi: cek jika id_del_spph di detail_spph ada
        if ($detail_loi && $detail_pr) {
            if (!$detail_loi->id_del_loi) {
                // Jika tidak ada id_del_spph, set qty1 dengan nilai qty dari detail_pr
                $detail_pr->qty_loi = $detail_pr->qty;
                $detail_pr->save();
            } else {
                // Jika id_del_spph ada dan data dihapus dari detail_spph
                // Ambil nilai qty_spph dan tambahkan ke qty1
                $loi_qty = $detail_loi->loi_qty;

                // Tambahkan qty_spph ke qty1 yang sudah ada
                $detail_pr->qty_loi += $loi_qty;
                $detail_pr->save();
            }
        }



        // Jika penghapusan berhasil, hapus referensi id_spph di detail_pr


        // Menghapus detail Loi berdasarkan ID
        $delete_detail_loi = DetailLoi::where('id', $id_detail_loi)->delete(); //ggwp

        // Menghapus semua referensi loi_id dari tabel detail_loi
        $delete_all_details = DetailLoi::where('loi_id', $id_detail_loi)->delete(); //ggwp, gk pake hapus aja bang

        // Mengupdate tabel DetailPR untuk menghapus referensi id_loi
        $delete_detail_pr = DetailPR::where('id', $id)->update([ //ggwp
            'id_loi' => null
        ]);

        // Jika semua operasi berhasil, ambil data loi yang diperbarui
        if ($delete_detail_loi) {  //ggwp
            $loi = Loi::select('loi.*')
                // ->leftjoin('vendor', 'vendor.id', '=', 'loi.vendor_id')
                // ->leftjoin('keproyekan', 'keproyekan.id', '=', 'loi.proyek_id')
                ->where('loi.id', $request->id_loi)
                ->first();

            if (!$loi) {
                return response()->json(['message' => 'Data loi tidak ditemukan'], 404);
            }

            $loi->details = DetailLoi::where('loi_id', $loi->id)
                ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_loi.id_detail_pr')
                ->select('detail_pr.*', 'detail_loi.id as id_detail_loi', 'detail_loi.harga as harga_per_unit',   'detail_loi.loi_qty')
                ->get();

            $loi->details = $loi->details->map(function ($item) use ($loi) {
                $item->id_loi = $loi->id;
                return $item;
            });

            return response()->json([
                'loi' => $loi
            ]);
        } else {
            // Mengembalikan response JSON dengan nilai loi null jika operasi gagal
            return response()->json([
                'loi' => null
            ]);
        }
    }
    //End hapus detail loi






    //Hapus Multiple
    public function hapusMultipleLoi(Request $request)
    {
        if ($request->has('ids')) {
            $ids = $request->input('ids');

            // Perbarui kolom id_loi di tabel detail_pr menjadi null
            DB::table('detail_pr')
                ->whereIn('id_loi', $ids)
                ->update(['id_loi' => null]);

            // Hapus data dari tabel detail_loi yang memiliki id_po sesuai
            DB::table('detail_loi')
                ->whereIn('loi_id', $ids)
                ->delete();

            // Hapus data dari tabel loi
            Loi::whereIn('id', $ids)->delete();

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }
    //End Hapus Multiple


    //Get Detail Loi isian lihat detail
    public function getDetailLoi(Request $request)
    {
        $id = $request->id;
        $loi = Loi::where('id', $id)->first();
        $vendor = json_decode($loi->vendor_id);
        $vendor = Vendor::whereIn('id', $vendor)->get();
        $vendor = $vendor->map(function ($item) {
            return $item->nama;
        });
        $vendor = $vendor->toArray();
        $vendor = implode(', ', $vendor);
        $loi->penerima = $vendor;

        $loi->details = DetailLoi::where('loi_id', $id)
            ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_loi.id_detail_pr')
            ->select('detail_pr.*', 'detail_loi.id as id_detail_loi', 'detail_loi.harga as harga_per_unit',  'detail_loi.loi_qty')
            ->get();

        $loi->details = $loi->details->map(function ($item) use ($id) {
            $item->id_loi = $id;
            $item->spek = $item->spek ? $item->spek : '';
            $item->keterangan = $item->keterangan ? $item->keterangan : '';
            $item->kode_material = $item->kode_material ? $item->kode_material : '';
            // $item->lampiran = $item->lampiran ? $item->lampiran : '';

            // // Start Get lampiran for each detail
            // $lampiran = SpphLampiran::where('spph_id', $item->id)->get();
            // $item->lampiran = $lampiran->map(function ($lampiran) {
            // $item->lampiran = $item->lampiran ? $item->lampiran : '';
            //     // dd($lampiran);
            //     return $lampiran->file; // Assuming `file_name` is the column name
            // })->toArray();
            // //End Get Lampiran for detail

            return $item;
        });
        // dd($loi->details);

        return response()->json([
            'loi' => $loi
        ]);
    }
    //End Detail Loi



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
            $item->nomor_loi = Loi::where('id', $item->id_loi)->first()->nomor_loi ?? '';
            $item->nomor_spph = Spph::where('id', $item->spph_id)->first()->nomor_spph ?? '';
            $item->pr_no = PurchaseRequest::where('id', $item->id_pr)->first()->no_pr ?? '';
            $item->po_no = Purchase_Order::where('id', $item->id_po)->first()->no_po ?? '';
            $item->nama_pekerjaan = Kontrak::where('id', $item->id_proyek)->first()->nama_pekerjaan ?? '';
           
            // Baru, hitung sisa Nego by QTY asli - jumlah di DetailNego by id_pr_detail
            $item->qty_loi = $item->qty - DetailLoi::where('id_detail_pr', $item->id)->sum('loi_qty');
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






    public function tambahLoiDetail(Request $request)
    {
        $id = $request->loi_id;
        $selected = $request->selected_id;

        // Cek jika selected_id kosong
        if (empty($selected)) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih barang terlebih dahulu'
            ]);
        }

        // foreach ($selected as $key => $value) {
        //     $id_barang = $value;
        //     // Temukan DetailPR berdasarkan ID
        //     $detailPr = DetailPR::find($value);



        //     // Dapatkan nilai qty_loi1 dan id_del
        //     $qty_loi1 = $detailPr->qty_loi1;
        //     $id_del = $detailPr->id_del;

        //     // Tambahkan data ke tabel Detailloi
        //     $detailLoi = DetailLoi::create([
        //         'loi_id' => $id,
        //         'id_detail_pr' => $id_barang,
        //         // Gunakan $value untuk id_detail_pr
        //         'loi_qty' => $qty_loi1,  // Masukkan qty_loi1 ke kolom qty_spph
        //         'id_del_loi' => $id_del,
        //     ]);

        //     // Update status dan qty_loi1 pada DetailPR
        //     $update = DetailPR::where('id', $value)->update([
        //         'status' => 2,
        //         'qty_loi1' => null,  // Set qty_loi1 menjadi null
        //         'id_loi' => $id,
        //     ]);

        //     // Tambahkan id_loi jika qty_loi bernilai 0
        //     if ($detailPr->qty_loi == 0) {
        //         $updateData = [
        //             'id_loi' => $id
        //         ];

        //         // Lakukan update pada DetailPR
        //         DetailPR::where('id', $value)->update($updateData);
        //     }
        // }

        // Cek jika Loi tidak ditemukan
        $loi = Loi::find($id);
        if (!$loi) {
            return response()->json(['message' => 'Data Loi tidak ditemukan'], 404);
        }

        // Ambil detail Loi
        $loi->details = DetailLoi::where('loi_id', $loi->id)
            ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_loi.id_detail_pr')
            ->select('detail_pr.*', 'detail_loi.id as id_detail_loi', 'detail_loi.harga as harga_per_unit', 'detail_loi.loi_qty')
            ->get();

        $loi->details = $loi->details->map(function ($item) use ($loi) {
            $item->id_loi = $loi->id;
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil ditambahkan',
            'loi' => $loi
        ]);
    }



    //End Tambah Detail


    public function nopr()
    {
        $data = PurchaseRequest::where('no_pr', 'LIKE', '%' . request('q') . '%')->paginate(10000);
        return response()->json($data);
    }


    //Print
    public function loiPrint(Request $request)
    {
        $id = $request->loi_id;
        $loi = Loi::where('id', $id)->first();
        $loi->details = DetailLoi::where('loi_id', $id)
            ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_loi.id_detail_pr')
            ->select('detail_pr.*', 'detail_loi.id as id_detail_loi', 'detail_loi.harga as harga_per_unit', 'detail_loi.loi_qty')
            ->get();

        $loi->tanggal_loi = Carbon::parse($loi->tanggal_loi)->isoFormat('D MMMM Y');
        $loi->batas_loi = Carbon::parse($loi->batas_loi)->isoFormat('D MMMM Y');

        $vendor = json_decode($loi->vendor_id);
        $vendor_name = Vendor::whereIn('id', $vendor)->pluck('nama')->toArray();
        $vendor_alamat = Vendor::whereIn('id', $vendor)->pluck('alamat')->toArray();

        $newObjects = [];
        foreach ($vendor_name as $key => $value) {
            $newObject = new \stdClass();
            $newObject->nama = $value;
            $newObject->alamat = $vendor_alamat[$key];
            array_push($newObjects, $newObject);
        }

        $lampiran = LoiLampiran::where('loi_id', $loi->id)->get();
        $loi->lampiran = $lampiran->count();
        $lois = $newObjects;
        $count = count($lois);

        // ✅ 1. Generate PDF utama (loi)
        $pdf = PDF::loadView('loi.loi_print', compact('loi', 'lois', 'count', 'lampiran'));
        $pdfPath = storage_path('app/temp_loi.pdf');
        $pdf->save($pdfPath);

        // ✅ 2. Buat FPDI untuk menggabungkan dokumen
        $fpdi = new FPDI();
        $fpdi->setSourceFile($pdfPath);
        $tplIdx = $fpdi->importPage(1);
        $fpdi->AddPage();
        $fpdi->useTemplate($tplIdx, 10, 10, 190);

        foreach ($lampiran as $file) {
            $filePath = public_path("/lampiran/{$file->file}");
            if (file_exists($filePath)) {
                $pageCount = $fpdi->setSourceFile($filePath);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplIdx = $fpdi->importPage($i);
                    $size = $fpdi->getTemplateSize($tplIdx);

                    // Deteksi orientasi berdasarkan ukuran halaman
                    $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

                    $fpdi->AddPage($orientation);

                    // Hitung scaling agar sesuai dengan halaman A4
                    $pageWidth = $orientation === 'L' ? 297 : 210; // A4 Landscape = 297mm, Portrait = 210mm
                    $pageHeight = $orientation === 'L' ? 210 : 297; // A4 Landscape = 210mm, Portrait = 297mm
                    $scaleX = $pageWidth / $size['width'];
                    $scaleY = $pageHeight / $size['height'];
                    $scale = min($scaleX, $scaleY); // Ambil skala yang lebih kecil agar tetap proporsional

                    // Posisikan gambar agar pas di tengah halaman
                    $x = ($pageWidth - ($size['width'] * $scale)) / 2;
                    $y = ($pageHeight - ($size['height'] * $scale)) / 2;

                    $fpdi->useTemplate($tplIdx, $x, $y, $size['width'] * $scale, $size['height'] * $scale);
                }
            }
        }


        // ✅ 4. Simpan hasil PDF yang sudah digabungkan
        $outputPath = storage_path("app/merged_loi.pdf");
        $fpdi->Output($outputPath, 'F');

        // ✅ 5. Kirimkan hasil PDF ke browser
        return response()->file($outputPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="LOI_' . $loi->nomor_loi . '.pdf"',
        ]);
    }
    //EndPrint

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
}
