<?php

namespace App\Http\Controllers;

use App\Models\DetailPR;
use App\Models\DetailNego;
use App\Models\Justi;
use App\Models\JustiLampiran;
use App\Models\Spph;
use App\Models\Keproyekan;
use App\Models\Purchase_Order;
use App\Models\PurchaseRequest;
use App\Models\Nego;
// use App\Models\NegoLampiran;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class JustiController extends Controller
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
        $justies = Justi::paginate(50);
        foreach ($justies as $key => $item) {
            $id = json_decode($item->vendor_id);
            $item->vendor = Vendor::whereIn('id', $id)->get();
            // $item->vendor = $vendor->nama;
            $item->vendor = $item->vendor->map(function ($item) {
                return $item->nama;
            });
            // change $item->vendor collection to array
            $item->vendor = $item->vendor->toArray();
            $item->vendor = implode(', ', $item->vendor);
            
            //lampiran bisa lebih dari 1
            $lampiran = JustiLampiran::where('id_justi', $item->id)->pluck('file')->toArray();
            if($lampiran){
                $item->lampiran = implode(', ', $lampiran);
            }
            // $item->lampiran = json_decode($item->lampiran); 
        }
        $vendors = Vendor::all();
        // dd($justies);
        // dd($spphes);
        if ($search) {
            $justies = Justi::where('justi', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $categories = Justi::where("warehouse_id", $warehouse_id)->get();

            return response()->json($categories);
        } else {
            return view('justi.justi', compact('justies', 'vendors'));
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

        $justies = Justi::paginate(50);
        $vendors = Vendor::all();

        if ($search) {
            $justies = Justi::where('justi', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $categories = Justi::where("warehouse_id", $warehouse_id)->get();

            return response()->json($categories);
        } else {
            return view('home.apps.logistik.justi', compact('justies', 'vendors'));
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


    public function nopr()
    {
        $data = PurchaseRequest::where('no_pr', 'LIKE', '%' . request('q') . '%')->paginate(10000);
        return response()->json($data);
    }



    // Simpan dan edit
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $justi = $request->id;
        // if (Session::has('selected_warehouse_id')) {
        //     $warehouse_id = Session::get('selected_warehouse_id');
        // } else {
        //     $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
        // }
        // dd($request->all());

        $request->validate([
            'nomor_justi' => 'required',
            'justi' => 'required',
            'dasar' => 'required',
            'perihal' => 'required',
            'id_pr' => 'required',
            'nomor_pr' => 'required',
            'pr' => 'required',
            'nomor_spph' => 'required',
            'spph' => 'required',
            // 'lampiran' => 'required',
            'vendor' => 'required',
            
            // 'penerima' => 'required',
            // 'alamat' => 'required'
        ], [
            'nomor_justi.required' => 'Nomor Justi harus diisi',
            'justi.required' => 'Nomor Justi harus diisi',
            'dasar.required' => 'Dasar harus diisi',
            'perihal.required' => 'Perihal harus diisi',
            'id_pr.required' => 'ID pr harus diisi',
            'nomor_pr.required' => 'Nomor pr harus diisi',
            'pr.required' => 'Tanggal pr harus diisi',
            'nomor_spph.required' => 'Nomor Spph harus diisi',
            'spph.required' => 'Tanggal Spph harus diisi',
            // 'lampiran.required' => 'Lampiran harus diisi',
            'vendor.required' => 'Vendor harus diisi',
            
            'penerima.required' => 'Penerima harus diisi',
            'alamat.required' => 'Alamat harus diisi',
           
        ]);

        $data = [
            'nomor_justi' => $request->nomor_justi,
            'justi' => $request->justi,
            'dasar' => $request->dasar,
            'perihal' => $request->perihal,
            'id_pr' => $request->id_pr,
            'nomor_pr' => $request->nomor_pr,
            'pr' => $request->pr,
            'nomor_spph' => $request->nomor_spph,
            'spph' => $request->spph,


            'vendor_id' => json_encode($request->vendor),
            
            'penerima' => json_encode($request->penerima),
            'alamat' => json_encode($request->alamat),
            
        ];

        // Ubah data vendor menjadi ID berdasarkan nama
        $vendorNames = json_decode($data['vendor_id']);
        $vendors = Vendor::whereIn('nama', $vendorNames)->pluck('id')->toArray();
        $data['vendor_id'] = json_encode($vendors);


        // dd($data);

        if (empty($justi)) {
            $add = Justi::create($data);

            // Check if 'lampiran' exists and is not null
            if ($request->hasFile('lampiran')) {
                $files = $request->file('lampiran');
                foreach ($files as $file) {
                    $file_name = rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('lampiran'), $file_name);
                    JustiLampiran::create([
                        'id_justi' => $add->id,
                        'file' => $file_name,
                        'tipe' => $this->FunctionCountPages(public_path('lampiran/' . $file_name))
                    ]);
                }
            }

            if ($add) {
                return redirect()->route('justi.index')->with('success', 'justi berhasil ditambahkan');
            } else {
                return redirect()->route('justi.index')->with('error', 'justi gagal ditambahkan');
            }
        } else {
            $update = Justi::where('id', $justi)->update($data);
            if ($request->hasFile('lampiran')) {
                $files = $request->file('lampiran');
                foreach ($files as $file) {
                    $file_name = rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('lampiran'), $file_name);
                    JustiLampiran::create([
                        'id_justi' => $justi,
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
                    JustiLampiran::where('id_justi', $justi)->where('file', $existing_file)->delete();

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
                return redirect()->route('justi.index')->with('success', 'justi berhasil diupdate');
            } else {
                return redirect()->route('justi.index')->with('error', 'justi gagal diupdate');
            }
        }

        // return redirect()->route('spph.index')->with('success', 'SPPH berhasil disimpan');
    }

    // End simpan dan edit




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
            $item->nomor_justi = Justi::where('id', $item->id_justi)->first()->nomor_justi ?? '';
            $item->nomor_spph = Spph::where('id', $item->spph_id)->first()->nomor_spph ?? '';
            $item->pr_no = PurchaseRequest::where('id', $item->id_pr)->first()->no_pr ?? '';
            $item->po_no = Purchase_Order::where('id', $item->id_po)->first()->no_po ?? '';
            $item->nama_proyek = Keproyekan::where('id', $item->id_proyek)->first()->nama_proyek ?? '';
            return $item;
        });

        // Filter produk berdasarkan nama proyek
        $products = $products->filter(function ($item) use ($proyek) {
            return strpos(strtolower($item->nama_proyek), $proyek) !== false;
        });

        // Kembalikan hasil dalam bentuk JSON
        return response()->json([
            'products' => $products
        ]);
    }
    //End Detail Product





    //simpan detailnego
    public function detailJustiSave(Request $request)
    {
        // Validasi data yang masuk
        $request->validate([
            'id' => 'required|integer',
            'id_justi' => 'required|integer',
            'id_detail_justi' => 'required|integer', //ggwp
            'nama_vendor' => 'required|integer', 
            'nomor_vendor' => 'required|integer', 
            'keterangan' => 'required|integer', 
            'harga_satuan' => 'required|numeric',
            
        ]);



        // Ambil data dari request
        $id = $request->id;
        $id_justi = $request->id_justi;
        $id_detail_justi = $request->id_detail_justi; //ggwp
        $harga_per_unit = $request->harga_per_unit;
        $harga_per_unit_imss = $request->harga_per_unit_imss;

        // dd($request->all());

        // Update data di tabel DetailNego
        $updated = DetailNego::where('id', $id_detail_justi)->update([
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

        $nego->details = DetailNego::where('nego_id', $nego->id)
            ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_nego.id_detail_pr')
            ->select('detail_pr.*', 'detail_nego.id as id_detail_nego', 'detail_nego.harga as harga_per_unit', 'detail_nego.harga_imss as harga_per_unit_imss')
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
 
         // Perbarui kolom id_nego di tabel detail_pr menjadi null
         $update_detail_pr = DB::table('detail_pr')
             ->where('id_nego', $delete_nego_id)
             ->update(['id_nego' => null]);
 
         // Hapus data dari tabel detail_spph yang memiliki id_nego sesuai
         $delete_detail_nego = DB::table('detail_nego')
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
     // End Hapus
 
 
 
     //hapus detail Nego
     public function destroyDetailNego(Request $request)
     {
         // Menerima data dari request
         $id = $request->id;
         $id_nego = $request->id_nego;
         $id_detail_pr = $request->id_detail_pr;
         $id_detail_nego = $request->id_detail_nego; //ggwp
 
         // dd($request->all());
 
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
             ->select('detail_pr.*', 'detail_nego.id as id_detail_nego', 'detail_nego.harga as harga_per_unit', 'detail_nego.harga_imss as harga_per_unit_imss')
             ->get();
 
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
 
             // Perbarui kolom id_nego di tabel detail_pr menjadi null
             DB::table('detail_pr')
                 ->whereIn('id_nego', $ids)
                 ->update(['id_nego' => null]);
 
             // Hapus data dari tabel detail_nego yang memiliki id_po sesuai
             DB::table('detail_nego')
                 ->whereIn('nego_id', $ids)
                 ->delete();
 
             // Hapus data dari tabel nego
             Nego::whereIn('id', $ids)->delete();
 
             return response()->json(['success' => true]);
         } else {
             return response()->json(['success' => false]);
         }
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
            ->select('detail_pr.*', 'detail_nego.id as id_detail_nego', 'detail_nego.harga as harga_per_unit','detail_nego.harga_imss as harga_per_unit_imss')
            ->get();

        $nego->details = $nego->details->map(function ($item) {
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










    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

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
    // public function destroy($id)
    // {
    //     //
    // }
}
