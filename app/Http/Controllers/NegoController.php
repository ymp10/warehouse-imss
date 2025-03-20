<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Nego;
use App\Models\Spph;
use App\Models\Vendor;
use App\Models\Kontrak;
use App\Models\DetailPR;
use App\Models\DetailNego;
use App\Models\Keproyekan;
use App\Models\NegoLampiran;
use Illuminate\Http\Request;
use App\Models\Purchase_Order;
use App\Models\PurchaseRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;


class NegoController extends Controller
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
        $negoes = Nego::paginate(50);
        foreach ($negoes as $key => $item) {
            $id = json_decode($item->vendor_id);
            $item->vendor = Vendor::whereIn('id', $id)->get();
            $item->vendor = $item->vendor->map(function ($item) {
                return $item->nama;
            });
            //change $item->vendor collection to array
            $item->vendor = $item->vendor->toArray();
            $item->vendor = implode(', ', $item->vendor);

            //lampiran bisa lebih dari 1
            $lampiran = NegoLampiran::where('nego_id', $item->id)->pluck('file')->toArray();
            $item->lampiran = implode(', ', $lampiran);
            // $item->lampiran = json_decode($item->lampiran); 
        }
        $vendors = Vendor::all();
        // dd($spphes);
        if ($search) {
            $negoes = Nego::where('tanggal_nego', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $categories = Nego::where("warehouse_id", $warehouse_id)->get();

            return response()->json($categories);
        } else {
            return view('nego.nego', compact('negoes', 'vendors'));
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

        $negoes = Nego::paginate(50);
        $vendors = Vendor::all();

        if ($search) {
            $negoes = Nego::where('tanggal_nego', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $categories = Nego::where("warehouse_id", $warehouse_id)->get();

            return response()->json($categories);
        } else {
            return view('home.apps.logistik.nego', compact('negoes', 'vendors'));
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
        $nego = $request->id;
        // if (Session::has('selected_warehouse_id')) {
        //     $warehouse_id = Session::get('selected_warehouse_id');
        // } else {
        //     $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
        // }
        // dd($request->all());

        $request->validate([
            'nomor_nego' => 'required',
            'id_pr' => 'required',
            'nomor_pr' => 'required',
            // 'lampiran' => 'required',
            'vendor' => 'required',
            'tanggal_nego' => 'required',
            'batas_nego' => 'required',
            'perihal' => 'required',
            'no_jawaban_vendor' => 'required',
            'franco' => 'required',
            // 'penerima' => 'required',
            // 'alamat' => 'required'
        ], [
            'nomor_nego.required' => 'Nomor Nego harus diisi',
            'id_pr.required' => 'ID pr harus diisi',
            'nomor_pr.required' => 'Nomor pr harus diisi',
            // 'lampiran.required' => 'Lampiran harus diisi',
            'vendor.required' => 'Vendor harus diisi',
            'tanggal_nego.required' => 'Tanggal nego harus diisi',
            'batas_nego.required' => 'Batas nego harus diisi',
            'perihal.required' => 'Perihal harus diisi',
            'penerima.required' => 'Penerima harus diisi',
            'alamat.required' => 'Alamat harus diisi',
            'no_jawaban_vendor.required' => 'Nomor Jawaban Nego Vendor harus diisi',
            'franco.required' => 'Franco harus diisi'
        ]);

        $data = [
            'nomor_nego' => $request->nomor_nego,
            'id_pr' => $request->id_pr,
            'nomor_pr' => $request->nomor_pr,
            'vendor_id' => json_encode($request->vendor),
            'tanggal_nego' => $request->tanggal_nego,
            'batas_nego' => $request->batas_nego,
            'perihal' => $request->perihal,
            'penerima' => json_encode($request->penerima),
            'alamat' => json_encode($request->alamat),
            'no_jawaban_vendor' => $request->no_jawaban_vendor,
            'franco' => $request->franco,
            'keterangan_nego' => $request->keterangan_nego,
        ];

        // Ubah data vendor menjadi ID berdasarkan nama
        $vendorNames = json_decode($data['vendor_id']);
        $vendors = Vendor::whereIn('nama', $vendorNames)->pluck('id')->toArray();
        $data['vendor_id'] = json_encode($vendors);


        // dd($data);

        if (empty($nego)) {
            $add = Nego::create($data);

            // Check if 'lampiran' exists and is not null
            if ($request->hasFile('lampiran')) {
                $files = $request->file('lampiran');
                foreach ($files as $file) {
                    $file_name = rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('lampiran'), $file_name);
                    NegoLampiran::create([
                        'nego_id' => $add->id,
                        'file' => $file_name,
                        'tipe' => $this->FunctionCountPages(public_path('lampiran/' . $file_name))
                    ]);
                }
            }

            if ($add) {
                return redirect()->route('nego.index')->with('success', 'Nego berhasil ditambahkan');
            } else {
                return redirect()->route('nego.index')->with('error', 'Nego gagal ditambahkan');
            }
        } else {
            $update = Nego::where('id', $nego)->update($data);
            if ($request->hasFile('lampiran')) {
                $files = $request->file('lampiran');
                foreach ($files as $file) {
                    $file_name = rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('lampiran'), $file_name);
                    NegoLampiran::create([
                        'nego_id' => $nego,
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
                    NegoLampiran::where('nego_id', $nego)->where('file', $existing_file)->delete();

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
                return redirect()->route('nego.index')->with('success', 'Nego berhasil diupdate');
            } else {
                return redirect()->route('nego.index')->with('error', 'Nego gagal diupdate');
            }
        }

        // return redirect()->route('spph.index')->with('success', 'SPPH berhasil disimpan');
    }

    // End simpan dan edit
    public function QtyNegoSave(Request $request)
    {
        // Validasi array
        $request->validate([
            'data' => 'required|array',
            'data.*.id' => 'required|integer',
            'data.*.qty_nego1' => 'required|numeric'
        ]);
    
        foreach ($request->data as $item) {
            $negoDetail = DetailNego::where('id_pr_detail',$item['id'])->first();
    
            if (!$negoDetail) continue;
    
            // Pastikan qty2 tidak lebih besar dari qty_spph
            if ($negoDetail->nego_qty > $item['qty_nego1']) {
                return response()->json(['error' => 'Qty2 tidak boleh lebih besar dari Qty1'], 400);
            }
    
            // Update data
            $negoDetail->qty_nego -= $item['qty_nego1'];
            $negoDetail->qty_nego1 = $item['qty_nego1'];
            $negoDetail->save();
        }
    
        return response()->json(['success' => true]);
    }
    

    //simpan detailnego
    public function detailNegoSave(Request $request)
    {
        // Validasi data yang masuk
        $request->validate([
            'id' => 'required|integer',
            'id_nego' => 'required|integer',
            'id_detail_nego' => 'required|integer', //ggwp
            'harga_per_unit' => 'required|numeric',
            'harga_per_unit_imss' => 'required|numeric',
        ]);



        // Ambil data dari request
        $id = $request->id;
        $id_nego = $request->id_nego;
        $id_detail_nego = $request->id_detail_nego;
        $harga_per_unit = $request->harga_per_unit;
        $harga_per_unit_imss = $request->harga_per_unit_imss;

        // dd($request->all());

        // Update data di tabel DetailNego
        $updated = DetailNego::where('id', $id_detail_nego)->update([
            'harga' => $harga_per_unit,
            'harga_imss' => $harga_per_unit_imss,
        ]);

        // dd($updated);
        if (!$updated) {
            return response()->json(['message' => 'Gagal memperbarui data'], 500);
        }

        // Ambil data Nego dan detailnya setelah update
        $nego = Nego::select('nego.*')
            // ->leftjoin('vendor', 'vendor.id', '=', 'nego.vendor_id')
            // ->leftjoin('keproyekan', 'keproyekan.id', '=', 'nego.proyek_id')
            ->where('nego.id', $request->id_nego)
            ->first();

        if (!$nego) {
            return response()->json(['message' => 'Data Nego tidak ditemukan'], 404);
        }

        $nego->details = DetailNego::where('detail_nego.nego_id', $nego->id)
        ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_nego.id_detail_pr')
        ->leftJoin('spph', 'spph.id', '=', 'detail_pr.spph_id') // Tambahkan join ke tabel spph
        ->select(
            'detail_pr.*',
            'detail_nego.id as id_detail_nego',
            'detail_nego.harga as harga_per_unit',
            'detail_nego.harga_imss as harga_per_unit_imss',
            'detail_nego.nego_qty',
            'spph.nomor_spph' // Tambahkan kolom nomor_spph
        )
        ->get();
    

        return response()->json([
            'nego' => $nego
        ]);
    }
    //End simpan detail nego





    // Hapus
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
 public function destroy(Request $request)
     {
         $delete_nego_id = $request->id;
         $detail_nego = DB::table('detail_nego')->where('nego_id', $delete_nego_id)->first();
     
         if ($detail_nego) {
             // Ambil data detail_pr terkait
             $detail_pr = DB::table('detail_pr')->where('id', $detail_nego->id_detail_pr)->first();
     
             if ($detail_pr) {
                 // Cek apakah ada id_del_nego di detail_nego
                 if (!$detail_nego->id_del_nego) {
                     // Jika tidak ada id_del_nego, set qty_nego dengan nilai qty dari detail_pr
                     DB::table('detail_pr')->where('id', $detail_pr->id)->update(['qty_nego' => $detail_pr->qty]);
                 } else {
                     // Ambil semua data detail_nego dengan nego_id yang akan dihapus
                     $detail_nego_list = DB::table('detail_nego')->where('nego_id', $delete_nego_id)->get();
     
                     if ($detail_nego_list->isNotEmpty()) {
                         // Kelompokkan data detail_nego berdasarkan id_detail_pr
                         $grouped = $detail_nego_list->groupBy('id_detail_pr');
     
                         foreach ($grouped as $id_detail_pr => $nego_entries) {
                             // Hitung total nego_qty untuk id_detail_pr tersebut
                             $total_nego_qty = $nego_entries->sum('nego_qty');
     
                             // Ambil data detail_pr terkait
                             $detail_pr = DB::table('detail_pr')->where('id', $id_detail_pr)->first();
                             if ($detail_pr) {
                                 $new_qty_nego = ($detail_pr->qty_nego ?? 0) + $total_nego_qty;
                                 DB::table('detail_pr')->where('id', $detail_pr->id)->update([
                                     'qty_nego' => $new_qty_nego
                                 ]);
                             }
                         }
                     }
                 }
             }
     
             // Perbarui kolom id_nego di tabel detail_pr menjadi null
             DB::table('detail_pr')
                 ->where('id_nego', $delete_nego_id)
                 ->update(['id_nego' => null]);
     
             // Hapus data dari tabel detail_nego yang memiliki id_nego sesuai
             DB::table('detail_nego')
                 ->where('nego_id', $delete_nego_id)
                 ->delete();
     
             // Setelah memperbarui detail_pr dan menghapus detail_nego, hapus data dari tabel nego
             $delete_nego = DB::table('nego')->where('id', $delete_nego_id)->delete();
     
             if ($delete_nego) {
                 return redirect()->route('nego.index')->with('success', 'Data Nego berhasil dihapus, id_nego pada detail_pr diubah menjadi null, dan detail_nego berhasil dihapus');
             } else {
                 return redirect()->route('nego.index')->with('error', 'Data Nego gagal dihapus');
             }
         }
     
         return redirect()->route('nego.index')->with('error', 'Data Nego tidak ditemukan');
     }
    // End Hapus
    
    
    // public function destroy(Request $request)
    // {
    //     $delete_nego_id = $request->id;

    //     // Perbarui kolom id_nego di tabel detail_pr menjadi null
    //     $update_detail_pr = DB::table('detail_pr')
    //         ->where('id_nego', $delete_nego_id)
    //         ->update(['id_nego' => null]);

    //     // Hapus data dari tabel detail_spph yang memiliki id_nego sesuai
    //     $delete_detail_nego = DB::table('detail_nego')
    //         ->where('nego_id', $delete_nego_id)
    //         ->delete();

    //     // Setelah memperbarui detail_pr dan menghapus detail_nego, hapus data dari tabel nego
    //     $delete_nego = DB::table('nego')->where('id', $delete_nego_id)->delete();

    //     if ($delete_nego) {
    //         return redirect()->route('nego.index')->with('success', 'Data Nego berhasil dihapus, id_nego pada detail_pr diubah menjadi null, dan detail_nego berhasil dihapus');
    //     } else {
    //         return redirect()->route('nego.index')->with('error', 'Data Nego gagal dihapus');
    //     }
    // }
    // // End Hapus



    //hapus detail Nego
    public function destroyDetailNego(Request $request)
    {
        // Menerima data dari request
        $id = $request->id;
        $id_nego = $request->id_nego;
        $id_detail_pr = $request->id_detail_pr;
        $id_detail_nego = $request->id_detail_nego; //ggwp

        // dd($request->all());
        // Mengambil data detail_spph dan detail_pr untuk validasi
    $detail_nego = DetailNego::find($id_detail_nego);
    $detail_pr = DetailPR::find($id);

    // Validasi: cek jika id_del_spph di detail_spph ada
    if ($detail_nego && $detail_pr) {
        if (!$detail_nego->id_del_nego) {
            // Jika tidak ada id_del_spph, set qty1 dengan nilai qty dari detail_pr
            $detail_pr->qty_nego = $detail_pr->qty;
            $detail_pr->save();
        } else {
            // Jika id_del_spph ada dan data dihapus dari detail_spph
            // Ambil nilai qty_spph dan tambahkan ke qty1
            $nego_qty = $detail_nego->nego_qty;
    
            // Tambahkan qty_spph ke qty1 yang sudah ada
            $detail_pr->qty_nego += $nego_qty; 
            $detail_pr->save();
        }
    }
    

    
    // Jika penghapusan berhasil, hapus referensi id_spph di detail_pr
    

        // Menghapus detail Nego berdasarkan ID
        $delete_detail_nego = DetailNego::where('id', $id_detail_nego)->delete(); //ggwp

        // Menghapus semua referensi nego_id dari tabel detail_nego
        $delete_all_details = DetailNego::where('nego_id', $id_detail_nego)->delete(); //ggwp, gk pake hapus aja bang

        // Mengupdate tabel DetailPR untuk menghapus referensi id_nego
        $delete_detail_pr = DetailPR::where('id', $id)->update([ //ggwp
            'id_nego' => null
        ]);

        // Jika semua operasi berhasil, ambil data Nego yang diperbarui
        if ($delete_detail_nego) {  //ggwp
            $nego = Nego::select('nego.*')
            // ->leftjoin('vendor', 'vendor.id', '=', 'nego.vendor_id')
            // ->leftjoin('keproyekan', 'keproyekan.id', '=', 'nego.proyek_id')
            ->where('nego.id', $request->id_nego)
            ->first();

        if (!$nego) {
            return response()->json(['message' => 'Data Nego tidak ditemukan'], 404);
        }

        $nego->details = DetailNego::where('nego_id', $nego->id)
            ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_nego.id_detail_pr')
            ->select('detail_pr.*', 'detail_nego.id as id_detail_nego', 'detail_nego.harga as harga_per_unit', 'detail_nego.harga_imss as harga_per_unit_imss', 'detail_nego.nego_qty')
            ->get();
            
        $nego->details = $nego->details->map(function ($item) use ($nego) {
            $item->id_nego = $nego->id;
            return $item;
        });

        return response()->json([
            'nego' => $nego
        ]);
        } else {
            // Mengembalikan response JSON dengan nilai nego null jika operasi gagal
            return response()->json([
                'nego' => null
            ]);
        }
    }
    //End hapus detail Nego






    //Hapus Multiple
    public function hapusMultipleNego(Request $request)
    {
        if ($request->has('ids')) {
            $ids = $request->input('ids');
    
            // Ambil semua data detail_nego yang akan dihapus
            $detail_nego_list = DB::table('detail_nego')->whereIn('nego_id', $ids)->get();
    
            if ($detail_nego_list->isNotEmpty()) {
                // Kelompokkan data detail_nego berdasarkan id_detail_pr
                $grouped = $detail_nego_list->groupBy('id_detail_pr');
    
                foreach ($grouped as $id_detail_pr => $nego_entries) {
                    // Hitung total nego_qty untuk id_detail_pr tersebut
                    $total_nego_qty = $nego_entries->sum('nego_qty');
    
                    // Ambil data detail_pr terkait
                    $detail_pr = DB::table('detail_pr')->where('id', $id_detail_pr)->first();
                    if ($detail_pr) {
                        // Update qty_nego dengan menambahkan kembali total_nego_qty
                        $new_qty_nego = ($detail_pr->qty_nego ?? 0) + $total_nego_qty;
                        DB::table('detail_pr')->where('id', $detail_pr->id)->update([
                            'qty_nego' => $new_qty_nego
                        ]);
                    }
                }
            }
    
            // Perbarui kolom id_nego di tabel detail_pr menjadi null
            DB::table('detail_pr')
                ->whereIn('id_nego', $ids)
                ->update(['id_nego' => null]);
    
            // Hapus data dari tabel detail_nego yang memiliki id_nego sesuai
            DB::table('detail_nego')
                ->whereIn('nego_id', $ids)
                ->delete();
    
            // Hapus data dari tabel nego
            Nego::whereIn('id', $ids)->delete();
    
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
    //End Hapus Multiple


    //Get Detail Nego isian lihat detail
    public function getDetailNego(Request $request)
    {
        $id = $request->id;
        $nego = Nego::where('id', $id)->first();
        $vendor = json_decode($nego->vendor_id);
        $vendor = Vendor::whereIn('id', $vendor)->get();
        $vendor = $vendor->map(function ($item) {
            return $item->nama;
        });
        $vendor = $vendor->toArray();
        $vendor = implode(', ', $vendor);
        $nego->penerima = $vendor;

        $nego->details = DetailNego::where('nego_id', $id)
        ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_nego.id_detail_pr')
        ->select('detail_pr.*', 'detail_nego.id as id_detail_nego', 'detail_nego.harga as harga_per_unit','detail_nego.harga_imss as harga_per_unit_imss', 'detail_nego.nego_qty')
        ->get();

        $nego->details = $nego->details->map(function ($item) use ($id) {
            $item->id_nego = $id;
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
        // dd($nego->details);

        return response()->json([
            'nego' => $nego
        ]);
    }
    //End Detail Nego



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
            $item->qty_nego = $item->qty - DetailNego::where('id_detail_pr', $item->id)->sum('nego_qty');
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





//     public function tambahNegoDetail(Request $request)
// {
//     $id = $request->nego_id;
//     $selected = $request->selected_id;

//     // Cek jika selected_id kosong
//     if (empty($selected)) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Pilih barang terlebih dahulu'
//         ]);
//     }

//     // Looping untuk setiap selected_id
//     foreach ($selected as $key => $value) {
//         // Temukan DetailPR berdasarkan ID
//         $detailPr = DetailPR::find($value);

//         // Jika DetailPR tidak ditemukan, lanjutkan ke barang berikutnya
//         if (!$detailPr) {
//             continue;
//         }

//         // Dapatkan nilai qty_nego1 dan id_del
//         $qty_nego1 = $detailPr->qty_nego1;
//         $id_del = $detailPr->id_del;

//         // Tambahkan data ke tabel DetailNego
//         $detailNego = DetailNego::create([
//             'nego_id' => $id,
//             'id_detail_pr' => $value,  // Gunakan $value untuk id_detail_pr
//             'nego_qty' => $qty_nego1,  // Masukkan qty_nego1 ke kolom qty_spph
//             'id_del_nego' => $id_del,
//         ]);

//         // Update status dan qty_nego1 pada DetailPR
//         $update = DetailPR::where('id', $value)->update([
//             'status' => 2,
//             'qty_nego1' => null,  // Set qty_nego1 menjadi null
//         ]);

//         // Tambahkan id_nego jika qty_nego bernilai 0
//         if ($detailPr->qty_nego == 0) {
//             $updateData = [
//                 'id_nego' => $id
//             ];

//             // Lakukan update pada DetailPR
//             DetailPR::where('id', $value)->update($updateData);
//         }
//     }

//     // Cek jika Nego tidak ditemukan
//     $nego = Nego::find($id);
//     if (!$nego) {
//         return response()->json(['message' => 'Data Nego tidak ditemukan'], 404);
//     }

//     // Ambil detail Nego
//     $nego->details = DetailNego::where('nego_id', $nego->id)
//         ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_nego.id_detail_pr')
//         ->select('detail_pr.*', 'detail_nego.id as id_detail_nego', 'detail_nego.harga as harga_per_unit', 'detail_nego.harga_imss as harga_per_unit_imss', 'detail_nego.nego_qty')
//         ->get();

//     return response()->json([
//         'success' => true,
//         'message' => 'Barang berhasil ditambahkan',
//         'nego' => $nego
//     ]);
// }
public function tambahNegoDetail(Request $request)
{
    $id = $request->nego_id;
    $selected = $request->selected_id;

    // Cek jika selected_id kosong
    if (empty($selected)) {
        return response()->json([
            'success' => false,
            'message' => 'Pilih barang terlebih dahulu'
        ]);
    }

    foreach ($selected as $key => $value) {
        $id_barang = $value;
        // Temukan DetailPR berdasarkan ID
        $detailPr = DetailPR::find($value);

        

        // Dapatkan nilai qty_nego1 dan id_del
        $qty_nego1 = $detailPr->qty_nego1;
        $id_del = $detailPr->id_del;

        // Tambahkan data ke tabel DetailNego
        $detailNego = DetailNego::create([
            'nego_id' => $id,
            'id_detail_pr' => $id_barang,
            // Gunakan $value untuk id_detail_pr
            'nego_qty' => $qty_nego1,  // Masukkan qty_nego1 ke kolom qty_spph
            'id_del_nego' => $id_del,
        ]);

        // Update status dan qty_nego1 pada DetailPR
        $update = DetailPR::where('id', $value)->update([
            'status' => 2,
            'qty_nego1' => null,  // Set qty_nego1 menjadi null
            'id_nego' => $id,
        ]);

        // Tambahkan id_nego jika qty_nego bernilai 0
        if ($detailPr->qty_nego == 0) {
            $updateData = [
                'id_nego' => $id
            ];

            // Lakukan update pada DetailPR
            DetailPR::where('id', $value)->update($updateData);
        }
    }

    // Cek jika Nego tidak ditemukan
    $nego = Nego::find($id);
    if (!$nego) {
        return response()->json(['message' => 'Data Nego tidak ditemukan'], 404);
    }

    // Ambil detail Nego
    $nego->details = DetailNego::where('nego_id', $nego->id)
        ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_nego.id_detail_pr')
        ->select('detail_pr.*', 'detail_nego.id as id_detail_nego', 'detail_nego.harga as harga_per_unit', 'detail_nego.harga_imss as harga_per_unit_imss', 'detail_nego.nego_qty')
        ->get();
        
    $nego->details = $nego->details->map(function ($item) use ($nego) {
            $item->id_nego = $nego->id;
            return $item;
        });

    return response()->json([
        'success' => true,
        'message' => 'Barang berhasil ditambahkan',
        'nego' => $nego
    ]);
}
// function tambahNegoDetail(Request $request)
//     {
//         $id = $request->nego_id;
//         $selected = $request->selected_id;
//         // dd($request->all());

//         if (empty($selected)) {
//             return response()->json([
//                 'success' => FALSE,
//                 'message' => 'Pilih barang terlebih dahulu'
//             ]);
//         }

//         //foreach selected_id

//         // foreach ($selected as $key => $value) {
//         //     $detail_pr = DetailPR::find($value);
//         //     $update = DetailPR::where('id', $value)->update([
//         //         'id_nego' => $id,
//         //         'status' => 1,
//         //     ]);
//         //     // $id_barang = $value;
//         //     $add = DetailNego::create([
//         //         'nego_id' => $id,
//         //         'id_detail_pr' => $detail_pr
//         //     ]);


//         // }

//         foreach ($selected as $key => $value) {
//             $id_barang = $value;
//             $add = DetailNego::create([
//                 'nego_id' => $id,
//                 'id_detail_pr' => $id_barang
//             ]);

//             $update = DetailPR::where('id', $id_barang)->update([
//                 'id_nego' => $id,
//                 'status' => 2,
//             ]);
//         }

//         $nego = Nego::select('nego.*')
//             // ->leftjoin('vendor', 'vendor.id', '=', 'nego.vendor_id')
//             // ->leftjoin('keproyekan', 'keproyekan.id', '=', 'nego.proyek_id')
//             ->where('nego.id', $request->nego_id)
//             ->first();

//         if (!$nego) {
//             return response()->json(['message' => 'Data Nego tidak ditemukan'], 404);
//         }

//         $nego->details = DetailNego::where('nego_id', $nego->id)
//             ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_nego.id_detail_pr')
//             ->select('detail_pr.*', 'detail_nego.id as id_detail_nego', 'detail_nego.harga as harga_per_unit', 'detail_nego.harga_imss as harga_per_unit_imss')
//             ->get();

//         return response()->json([
//             'success' => TRUE,
//             'message' => 'Barang berhasil ditambahkan',
//             'nego' => $nego
//         ]);
//     }

    
    //End Tambah Detail


    public function nopr()
    {
        $data = PurchaseRequest::where('no_pr', 'LIKE', '%' . request('q') . '%')->paginate(10000);
        return response()->json($data);
    }


    //Print
    public function negoPrint(Request $request)
{
    $id = $request->nego_id;
    $nego = Nego::where('id', $id)->first();
    $nego->details = DetailNego::where('nego_id', $id)
        ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_nego.id_detail_pr')
        ->select('detail_pr.*', 'detail_nego.id as id_detail_nego', 'detail_nego.harga as harga_per_unit', 'detail_nego.harga_imss as harga_per_unit_imss', 'detail_nego.nego_qty')
        ->get();

    $nego->tanggal_nego = Carbon::parse($nego->tanggal_nego)->isoFormat('D MMMM Y');
    $nego->batas_nego = Carbon::parse($nego->batas_nego)->isoFormat('D MMMM Y');

    $vendor = json_decode($nego->vendor_id);
    $vendor_name = Vendor::whereIn('id', $vendor)->pluck('nama')->toArray();
    $vendor_alamat = Vendor::whereIn('id', $vendor)->pluck('alamat')->toArray();

    $newObjects = [];
    foreach ($vendor_name as $key => $value) {
        $newObject = new \stdClass();
        $newObject->nama = $value;
        $newObject->alamat = $vendor_alamat[$key];
        array_push($newObjects, $newObject);
    }

    $lampiran = NegoLampiran::where('nego_id', $nego->id)->get();
    $nego->lampiran = $lampiran->count();
    $negos = $newObjects;
    $count = count($negos);

    // ✅ 1. Generate PDF utama (nego)
    $pdf = PDF::loadView('nego.nego_print', compact('nego', 'negos', 'count', 'lampiran'));
    $pdfPath = storage_path('app/temp_nego.pdf');
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
    $outputPath = storage_path("app/merged_nego.pdf");
    $fpdi->Output($outputPath, 'F');

    // ✅ 5. Kirimkan hasil PDF ke browser
    return response()->file($outputPath, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="NEGO_' . $nego->nomor_nego . '.pdf"',
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
