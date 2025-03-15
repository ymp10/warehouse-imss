<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Spph;
use App\Models\Vendor;
use App\Models\Kontrak;
use App\Models\Product;
use App\Models\DetailPR;
use App\Models\DetailSpph;
use App\Models\Keproyekan;
use App\Models\SpphLampiran;
use Illuminate\Http\Request;
use App\Models\Purchase_Order;
use App\Models\PurchaseRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

class SpphController extends Controller
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
        $spphes = Spph::paginate(50);
        foreach ($spphes as $key => $item) {
            $id = json_decode($item->vendor_id);
            $item->vendor = Vendor::whereIn('id', $id)->get();
            $item->vendor = $item->vendor->map(function ($item) {
                return $item->nama;
            });
            //change $item->vendor collection to array
            $item->vendor = $item->vendor->toArray();
            $item->vendor = implode(', ', $item->vendor);

            //lampiran bisa lebih dari 1
            $lampiran = SpphLampiran::where('spph_id', $item->id)->pluck('file')->toArray();
            $item->lampiran = implode(', ', $lampiran);
            // $item->lampiran = json_decode($item->lampiran);
        }
        $vendors = Vendor::all();
        // dd($spphes);
        if ($search) {
            $spphes = Spph::where('tanggal_spph', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $categories = Spph::where("warehouse_id", $warehouse_id)->get();

            return response()->json($categories);
        } else {
            return view('spph.spph', compact('spphes', 'vendors'));
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

        $spphes = Spph::paginate(50);
        $vendors = Vendor::all();

        if ($search) {
            $spphes = Spph::where('tanggal_spph', 'LIKE', "%$search%")->paginate(50);
        }

        if ($request->format == "json") {
            $categories = Spph::where("warehouse_id", $warehouse_id)->get();

            return response()->json($categories);
        } else {
            return view('home.apps.logistik.spph', compact('spphes', 'vendors'));
        }
    }

    function FunctionCountPages($path)
    {
        $pdftextfile = file_get_contents($path);
        $pagenumber = preg_match_all("/\/Page\W/", $pdftextfile, $dummy);
        return $pagenumber;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $spph = $request->id;
        // if (Session::has('selected_warehouse_id')) {
        //     $warehouse_id = Session::get('selected_warehouse_id');
        // } else {
        //     $warehouse_id = DB::table('warehouse')->first()->warehouse_id;
        // }
        // dd($request->all());

        $request->validate([
            'nomor_spph' => 'required',
            'id_pr' => 'required',
            'nomor_pr' => 'required',
            // 'lampiran' => 'required',
            'vendor' => 'required',
            'tanggal_spph' => 'required',
            'batas_spph' => 'required',
            'perihal' => 'required',
            // 'penerima' => 'required',
            // 'alamat' => 'required'
        ], [
            'nomor_spph.required' => 'Nomor SPPH harus diisi',
            'id_pr.required' => 'ID PR harus diisi',
            'nomor_pr.required' => 'Nomor PR harus diisi',
            // 'lampiran.required' => 'Lampiran harus diisi',
            'vendor.required' => 'Vendor harus diisi',
            'tanggal_spph.required' => 'Tanggal SPPH harus diisi',
            'batas_spph.required' => 'Batas SPPH harus diisi',
            'perihal.required' => 'Perihal harus diisi',
            'penerima.required' => 'Penerima harus diisi',
            'alamat.required' => 'Alamat harus diisi'
        ]);

        $data = [
            'nomor_spph' => $request->nomor_spph,
            'id_pr' => $request->id_pr,
            'nomor_pr' => $request->nomor_pr,
            'vendor_id' => json_encode($request->vendor),
            'tanggal_spph' => $request->tanggal_spph,
            'batas_spph' => $request->batas_spph,
            'perihal' => $request->perihal,
            'penerima' => json_encode($request->penerima),
            'alamat' => json_encode($request->alamat),
            'keterangan_spph' => $request->keterangan_spph,
        ];

        // Ubah data vendor menjadi ID berdasarkan nama
        $vendorNames = json_decode($data['vendor_id']);
        $vendors = Vendor::whereIn('nama', $vendorNames)->pluck('id')->toArray();
        $data['vendor_id'] = json_encode($vendors);


        // dd($data);

        if (empty($spph)) {
            $add = Spph::create($data);

            // Check if 'lampiran' exists and is not null
            if ($request->hasFile('lampiran')) {
                $files = $request->file('lampiran');
                foreach ($files as $file) {
                    $file_name = rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('lampiran'), $file_name);
                    SpphLampiran::create([
                        'spph_id' => $add->id,
                        'file' => $file_name,
                        'tipe' => $this->FunctionCountPages(public_path('lampiran/' . $file_name))
                    ]);
                }
            }

            if ($add) {
                return redirect()->route('spph.index')->with('success', 'SPPH berhasil ditambahkan');
            } else {
                return redirect()->route('spph.index')->with('error', 'SPPH gagal ditambahkan');
            }
        } else {
            $update = Spph::where('id', $spph)->update($data);
            if ($request->hasFile('lampiran')) {
                $files = $request->file('lampiran');
                foreach ($files as $file) {
                    $file_name = rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('lampiran'), $file_name);
                    SpphLampiran::create([
                        'spph_id' => $spph,
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
                    SpphLampiran::where('spph_id', $spph)->where('file', $existing_file)->delete();

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
                return redirect()->route('spph.index')->with('success', 'SPPH berhasil diupdate');
            } else {
                return redirect()->route('spph.index')->with('error', 'SPPH gagal diupdate');
            }
        }

        // return redirect()->route('spph.index')->with('success', 'SPPH berhasil disimpan');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Request  $request)
    // {
    //     $delete_spph = $request->id;
    //     $delete_spph = DB::table('spph')->where('id', $delete_spph)->delete();

    //     if ($delete_spph) {
    //         return redirect()->route('spph.index')->with('success', 'Data SPPH berhasil dihapus');
    //     } else {
    //         return redirect()->route('spph.index')->with('error', 'Data SPPH gagal dihapus');
    //     }
    // }

    //Hapus Data
    public function destroy(Request $request)
    {
        $delete_spph_id = $request->id;
    
        // Ambil data detail_spph yang akan dihapus
        $detail_spph = DB::table('detail_spph')->where('spph_id', $delete_spph_id)->first();
        
        if ($detail_spph) {
            // Ambil data detail_pr terkait
            $detail_pr = DB::table('detail_pr')->where('id', $detail_spph->id_detail_pr)->first();
            
            if ($detail_pr) {
                // Cek apakah ada id_del_spph di detail_spph
                if (!$detail_spph->id_del_spph) {
                    // Jika tidak ada id_del_spph, set qty_spph dengan nilai qty dari detail_pr
                    DB::table('detail_pr')->where('id', $detail_pr->id)->update(['qty_spph' => $detail_pr->qty]);
                } else // Ambil semua data detail_spph dengan spph_id yang akan dihapus
                $detail_spph_list = DB::table('detail_spph')->where('spph_id', $delete_spph_id)->get();
                
                if ($detail_spph_list->isNotEmpty()) {
                    // Kelompokkan data detail_spph berdasarkan id_detail_pr
                    $grouped = $detail_spph_list->groupBy('id_detail_pr');
                
                    foreach ($grouped as $id_detail_pr => $spph_entries) {
                        // Hitung total spph_qty untuk id_detail_pr tersebut
                        $total_spph_qty = $spph_entries->sum('spph_qty');
                        
                        // Ambil data detail_pr terkait
                        $detail_pr = DB::table('detail_pr')->where('id', $id_detail_pr)->first();
                        if ($detail_pr) {
                            $new_qty_spph = ($detail_pr->qty_spph ?? 0) + $total_spph_qty;
                            DB::table('detail_pr')->where('id', $detail_pr->id)->update([
                                'qty_spph' => $new_qty_spph
                            ]);
                        }
                    }
                }
                
            }
        }
    
        // Perbarui kolom id_spph di tabel detail_pr menjadi null
        DB::table('detail_pr')->where('id_spph', $delete_spph_id)->update(['id_spph' => null]);
    
        // Hapus data dari tabel detail_spph yang memiliki id_spph sesuai
        DB::table('detail_spph')->where('spph_id', $delete_spph_id)->delete();
    
        // Setelah memperbarui detail_pr dan menghapus detail_spph, hapus data dari tabel spph
        $delete_spph = DB::table('spph')->where('id', $delete_spph_id)->delete();
    
        if ($delete_spph) {
            return redirect()->route('spph.index')->with('success', 'Data SPPH berhasil dihapus, id_spph pada detail_pr diubah menjadi null, dan detail_spph berhasil dihapus');
        } else {
            return redirect()->route('spph.index')->with('error', 'Data SPPH gagal dihapus');
        }
    }
    
    //End Hapus Data


    // public function hapusMultipleSpph(Request $request)
    // {
    //     if ($request->has('ids')) {
    //         Spph::whereIn('id', $request->input('ids'))->delete();
    //         return response()->json(['success' => true]);
    //     } else {
    //         return response()->json(['success' => false]);
    //     }
    // }


    //hapus yang dipilih
     public function hapusMultipleSpph(Request $request)
{
    if ($request->has('ids')) {
        $ids = $request->input('ids');

        // Ambil semua data detail_spph yang akan dihapus
        $detail_spph_list = DB::table('detail_spph')->whereIn('spph_id', $ids)->get();

        if ($detail_spph_list->isNotEmpty()) {
            // Kelompokkan data detail_spph berdasarkan id_detail_pr
            $grouped = $detail_spph_list->groupBy('id_detail_pr');

            foreach ($grouped as $id_detail_pr => $spph_entries) {
                // Hitung total spph_qty untuk id_detail_pr tersebut
                $total_spph_qty = $spph_entries->sum('spph_qty');

                // Ambil data detail_pr terkait
                $detail_pr = DB::table('detail_pr')->where('id', $id_detail_pr)->first();
                if ($detail_pr) {
                    // Update qty_spph dengan menambahkan kembali total_spph_qty
                    $new_qty_spph = ($detail_pr->qty_spph ?? 0) + $total_spph_qty;
                    DB::table('detail_pr')->where('id', $detail_pr->id)->update([
                        'qty_spph' => $new_qty_spph
                    ]);
                }
            }
        }

        // Perbarui kolom id_spph di tabel detail_pr menjadi null
        DB::table('detail_pr')
            ->whereIn('id_spph', $ids)
            ->update(['id_spph' => null]);

        // Hapus data dari tabel detail_spph yang memiliki id_spph sesuai
        DB::table('detail_spph')
            ->whereIn('spph_id', $ids)
            ->delete();

        // Hapus data dari tabel spph
        Spph::whereIn('id', $ids)->delete();

        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false]);
}
    //End hapus yang dipilih


    //hapus detail SPPH
    // public function destroyDetailSpph(Request $request)
    // {
    //     // Menerima data dari request
    //     $id = $request->id;
    //     $id_spph = $request->id_spph;
    //     $id_detail_pr = $request->id_detail_pr;
    //     $id_detail_spph = $request->id_detail_spph;
        
    //     // Menghapus detail SPPH berdasarkan ID
    //     $delete_detail_spph = DetailSpph::where('id', $id_detail_spph)->delete();

    //     // Menghapus semua referensi spph_id dari tabel detail_spph
    //     $delete_all_details = DetailSpph::where('spph_id', $id_detail_spph)->delete();

    //     // Mengupdate tabel DetailPR untuk menghapus referensi id_spph
    //     $delete_detail_pr = DetailPR::where('id', $id)->update([
    //         'id_spph' => null
    //     ]);

    //     // Jika semua operasi berhasil, ambil data SPPH yang diperbarui
    //     if ($delete_detail_spph) {
    //         $spph = Spph::select('spph.*', 'vendor.nama as nama_vendor', 'keproyekan.nama_proyek as nama_proyek')
    //             ->leftJoin('vendor', 'vendor.id', '=', 'spph.vendor_id')
    //             ->leftJoin('keproyekan', 'keproyekan.id', '=', 'spph.proyek_id')
    //             ->where('spph.id', $request->id_spph)
    //             ->first();

    //         // Mengambil detail SPPH yang diperbarui
    //         $spph->details = DetailSpph::where('detail_spph.spph_id', $spph->id)
    //             ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_spph.id_detail_pr')
    //             ->select(
    //                 'detail_pr.*',
    //                 'detail_spph.id as id_detail_spph'
    //             )
    //             ->get();

    //         // Mengembalikan response JSON dengan data SPPH yang diperbarui
    //         return response()->json([
    //             'spph' => $spph
    //         ]);
    //     } else {
    //         // Mengembalikan response JSON dengan nilai spph null jika operasi gagal
    //         return response()->json([
    //             'spph' => null
    //         ]);
    //     }
    // }
    //End hapus detail SPPH
//tambahan +++
public function destroyDetailSpph(Request $request)
{
    // Menerima data dari request
    $id = $request->id;
    $id_spph = $request->id_spph;
    $id_detail_pr = $request->id_detail_pr;
    $id_detail_spph = $request->id_detail_spph;
    
    // Mengambil data detail_spph dan detail_pr untuk validasi
    $detail_spph = DetailSpph::find($id_detail_spph);
    $detail_pr = DetailPR::find($id);

    // Validasi apakah data ada
    if (!$detail_spph) {
        return response()->json(['error' => 'Detail SPPH tidak ditemukan'], 404);
    }

    if (!$detail_pr) {
        return response()->json(['error' => 'Detail PR tidak ditemukan'], 404);
    }

    // Cek apakah ada id_del_spph di detail_spph
    if (!$detail_spph->id_del_spph) {
        // Jika tidak ada id_del_spph, set qty1 dengan nilai qty dari detail_pr
        $detail_pr->qty_spph = $detail_pr->qty;
    } else {
        // Jika ada id_del_spph, tambahkan qty_spph ke qty1
        $spph_qty = $detail_spph->spph_qty;
        $detail_pr->qty_spph = ($detail_pr->qty_spph ?? 0) + $spph_qty;
    }

    $detail_pr->save();

    // Hapus data di detail_spph
    $delete_detail_spph = $detail_spph->delete();

    // // Jika penghapusan berhasil, hapus referensi id_spph di detail_pr
    // if ($delete_detail_spph) {
    //     DetailPR::where('id', $id)->update(['id_spph' => null]);
    // }

    // Ambil data SPPH yang diperbarui
    $spph = Spph::where('id', $request->id_spph)->first();
    
    if (!$spph) {
        return response()->json(['message' => 'Data SPPH tidak ditemukan'], 404);
    }

    // Mengambil detail SPPH terbaru
    $spph->details = DetailSpph::where('detail_spph.spph_id', $spph->id)
        ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_spph.id_detail_pr')
        ->select(
            'detail_pr.*',
            'detail_spph.id as id_detail_spph',
            'detail_spph.spph_qty'
        )
        ->get();
        
    $spph->details = $spph->details->map(function ($item) use ($spph) {
            $item->id_spph = $spph->id;
            return $item;
        });

    return response()->json([
        'spph' => $spph
    ]);
}


//end+++



    public function getDetailSPPH(Request $request)
    {
        $id = $request->id;
        $spph = Spph::where('id', $id)->first();
        $vendor = json_decode($spph->vendor_id);
        $vendor = Vendor::whereIn('id', $vendor)->get();
        $vendor = $vendor->map(function ($item) {
            return $item->nama;
        });
        $vendor = $vendor->toArray();
        $vendor = implode(', ', $vendor);
        $spph->penerima = $vendor;

        $spph->details = DetailSpph::where('spph_id', $id)
        ->select('detail_spph.*', 'detail_pr.*', 'detail_spph.id as id_detail_spph')
        ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_spph.id_detail_pr','detail_spph.spph_qty')
        ->get();

        $spph->details = $spph->details->map(function ($item) use ($id) {
            $item->id_spph = $id;
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

        return response()->json([
            'spph' => $spph
        ]);
    }


    public function getProductPR(Request $request)
    {
        // dd($request);
        $id_pr = $request->id_pr; // Ambil id_pr dari request
        $proyek = strtolower($request->proyek);

        // Ambil DetailPR yang sesuai dengan id_pr
        $products = DetailPR::whereIn('id_pr', explode(',', $id_pr))->get();

        // Proses setiap produk
        $products = $products->map(function ($item) {
            $item->spek = $item->spek ? $item->spek : '';
            $item->keterangan = $item->keterangan ? $item->keterangan : '';
            $item->kode_material = $item->kode_material ? $item->kode_material : '';
            $item->nomor_spph = Spph::where('id', $item->id_spph)->first()->nomor_spph ?? '';
            $item->pr_no = PurchaseRequest::where('id', $item->id_pr)->first()->no_pr ?? '';
            $item->po_no = Purchase_Order::where('id', $item->id_po)->first()->no_po ?? '';
            $item->nama_pekerjaan = Kontrak::where('id', $item->id_proyek)->first()->nama_pekerjaan ?? ''; // Ambil nama_pekerjaan

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


    // public function getProductPR(Request $request)
    // {
    //     $proyek = $request->proyek;
    //     $proyek = strtolower($proyek);
    //     $products = DetailPR::all();

    //     $products = $products->map(function ($item) {
    //         $item->spek = $item->spek ? $item->spek : '';
    //         $item->keterangan = $item->keterangan ? $item->keterangan : '';
    //         $item->kode_material = $item->kode_material ? $item->kode_material : '';
    //         $item->nomor_spph = Spph::where('id', $item->id_spph)->first()->nomor_spph ?? '';
    //         $item->pr_no = PurchaseRequest::where('id', $item->id_pr)->first()->no_pr ?? '';
    //         $item->po_no = Purchase_Order::where('id', $item->id_po)->first()->no_po ?? '';
    //         $item->nama_proyek = Keproyekan::where('id', $item->id_proyek)->first()->nama_proyek ?? '';
    //         return $item;
    //     });

    //     $products = $products->filter(function ($item) use ($proyek) {
    //         return strpos(strtolower($item->nama_proyek), $proyek) !== false;
    //     });

    //     return response()->json([
    //         'products' => $products
    //     ]);
    // }


    public function spphPrint(Request $request)
    {
        $id = $request->spph_id;
        $spph = Spph::where('id', $id)->first();
        $spph->details = DetailSpph::where('spph_id', $id)->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_spph.id_detail_pr')->get();
        $spph->tanggal_spph = Carbon::parse($spph->tanggal_spph)->isoFormat('D MMMM Y');
        $spph->batas_spph = Carbon::parse($spph->batas_spph)->isoFormat('D MMMM Y');

        $vendor = json_decode($spph->vendor_id);
        $vendor_name = Vendor::whereIn('id', $vendor)->pluck('nama')->toArray();
        $vendor_alamat = Vendor::whereIn('id', $vendor)->pluck('alamat')->toArray();

        $newObjects = [];
        foreach ($vendor_name as $key => $value) {
            $newObject = new \stdClass();
            $newObject->nama = $value;
            $newObject->alamat = $vendor_alamat[$key];
            array_push($newObjects, $newObject);
        }

        $lampiran = SpphLampiran::where('spph_id', $spph->id)->get();
        $spph->lampiran = $lampiran->count();
        $spphs = $newObjects;
        $count = count($spphs);

        // Generate main PDF
        $pdf = PDF::loadview('spph.spph_print', compact('spph', 'spphs', 'count', 'lampiran'));
        $no_spph = $spph->nomor_spph;
        $pdf->setPaper('A4', 'portrait');

        // Simpan PDF utama ke file sementara
        $pdfPath = storage_path('app/temp_spph.pdf');
        $pdf->save($pdfPath);

        // Gabungkan dengan lampiran PDF
        $mergedPdf = new Fpdi();
        $mergedPdf->setSourceFile($pdfPath);
        $tplIdx = $mergedPdf->importPage(1);
        $mergedPdf->addPage();
        $mergedPdf->useTemplate($tplIdx);

        foreach ($lampiran as $file) {
    $filePath = public_path("/lampiran/{$file->file}");
    if (file_exists($filePath)) {
        $pageCount = $mergedPdf->setSourceFile($filePath);
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplIdx = $mergedPdf->importPage($i);
            $size = $mergedPdf->getTemplateSize($tplIdx);

            // Deteksi orientasi berdasarkan ukuran halaman
            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

            $mergedPdf->AddPage($orientation);

            // Hitung scaling agar sesuai dengan halaman A4
            $pageWidth = $orientation === 'L' ? 297 : 210; // A4 Landscape = 297mm, Portrait = 210mm
            $pageHeight = $orientation === 'L' ? 210 : 297; // A4 Landscape = 210mm, Portrait = 297mm
            $scaleX = $pageWidth / $size['width'];
            $scaleY = $pageHeight / $size['height'];
            $scale = min($scaleX, $scaleY); // Ambil skala yang lebih kecil agar tetap proporsional

            // Posisikan gambar agar pas di tengah halaman
            $x = ($pageWidth - ($size['width'] * $scale)) / 2;
            $y = ($pageHeight - ($size['height'] * $scale)) / 2;

            $mergedPdf->useTemplate($tplIdx, $x, $y, $size['width'] * $scale, $size['height'] * $scale);
        }
    }
}


        // Simpan hasil gabungan dan stream ke browser
        $outputPath = storage_path("app/merged_spph.pdf");
        $mergedPdf->Output($outputPath, 'F');

        return response()->file($outputPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="SPPH_' . $no_spph . '.pdf"',
        ]);
    }


    //coding asli spph print
    // public function spphPrint(Request $request)
    // {
    //     $id = $request->spph_id;
    //     $spph = Spph::where('id', $id)->first();
    //     $spph->details = DetailSpph::where('spph_id', $id)
    //         ->leftjoin('detail_pr', 'detail_pr.id', '=', 'detail_spph.id_detail_pr')
    //         ->get();
    //     $spph->tanggal_spph = Carbon::parse($spph->tanggal_spph)->isoFormat('D MMMM Y');
    //     $spph->batas_spph = Carbon::parse($spph->batas_spph)->isoFormat('D MMMM Y');

    //     // dd($spph);

    //     // $page_count = 0;
    //     // $dummy = PDF::loadview('spph_print', compact('spph', 'page_count'));
    //     // $dummy->setPaper('A4', 'Potrait');
    //     // $no_spph = $spph->nomor_spph;
    //     // $dummy->render();
    //     // $page_count = $dummy->get_canvas()->get_page_count();
    //     // $pdf = PDF::loadview('spph_print', compact('spph', 'page_count'));

    //     $vendor = json_decode($spph->vendor_id);
    //     $vendor_name = Vendor::whereIn('id', $vendor)->get();
    //     $vendor_name = $vendor_name->map(function ($item) {
    //         return $item->nama;
    //     });
    //     $vendor_name = $vendor_name->toArray();

    //     $vendor_alamat = Vendor::whereIn('id', $vendor)->get();
    //     $vendor_alamat = $vendor_alamat->map(function ($item) {
    //         return $item->alamat;
    //     });
    //     $vendor_alamat = $vendor_alamat->toArray();

    //     $newObjects = [];

    //     //push to newObject with nama=vendor_name, alamat=vendor_alamat
    //     foreach ($vendor_name as $key => $value) {
    //         $newObject = new \stdClass();
    //         $newObject->nama = $value;
    //         $newObject->alamat = $vendor_alamat[$key];
    //         array_push($newObjects, $newObject);
    //     }

    //     $lampiran = SpphLampiran::where('spph_id', $spph->id)->get();
    //     //sum in tipe column
    //     $spph->lampiran = $lampiran->count();
    //     // dd($spph->all());

    //     // $files = [];
    //     // foreach ($lampiran as $key => $value) {
    //     //     $file = public_path('lampiran/' . $value->file);
    //     //     $page_count = $this->FunctionCountPages($file);
    //     //     $value->page_count = $page_count;
    //     //     array_push($files, $value);
    //     // }

    //     // foreach ($files as $key => $value) {
    //     //     $pdf->prependPDF($value->file);
    //     // }

    //     $spphs = $newObjects;
    //     $count = count($spphs);

    //     $pdf = PDF::loadview('spph.spph_print', compact('spph', 'spphs', 'count'));
    //     $no_spph = $spph->nomor_spph;
    //     $pdf->setPaper('A4', 'Potrait');
    //     return $pdf->stream('SPPH_' . $no_spph . '.pdf');
    // }



    function tambahSpphDetail(Request $request)
    {
        $id = $request->spph_id; // ID dari SPPH
        $selected = $request->selected_id; // Array ID barang yang dipilih

        if (empty($selected)) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih barang terlebih dahulu',
            ]);
        }

        foreach ($selected as $id_barang) {
            // Ambil detail barang dari tabel detail_pr
            $detailPr = DetailPR::where('id', $id_barang)->first();

            if (!$detailPr) {
                return response()->json([
                    'success' => false,
                    'message' => "Barang dengan ID $id_barang tidak ditemukan",
                ]);
            }

            //    $qty1 = 8; // Ambil nilai qty1 dari detail_pr
            $qty2 = $detailPr->qty2;
            $id_del = $detailPr->id_del;

            // Tambahkan data ke tabel detail_spph
            $detailSpph = DetailSpph::create([
                'spph_id' => $id,
                'id_detail_pr' => $id_barang,
                'spph_qty' => $qty2, // Masukkan qty1 ke kolom qty_spph
                'id_del_spph' => $id_del,
            ]);

            // Perbarui tabel detail_pr hanya jika qty sama
            // if ($detailPr->qty == $detailSpph->qty_spph) {
            //     $detailPr->update([
            //         'id_spph' => $id, // Isi kolom id_spph
            //         'status' => 1,    // Update status jika diperlukan
            //     ]);
            // } else {
            //     $detailPr->update([
            //         'id_spph' => null, // Kosongkan id_spph jika qty tidak sama
            //         'status' => 0,     // Update status jika diperlukan
            //         'qty2' => null, 
            //     ]);
            // }
            // $update = DetailPR::where('id', $id_barang)->update([
            //     // 'id_spph' => $id,
            //     'status' => 1,
            //     'qty2' => null,
            // ]);
            // Ambil data dari tabel detail_pr berdasarkan id_barang
            $detailPR = DetailPR::where('id', $id_barang)->first();

            if ($detailPR) { // Pastikan data ditemukan
                $updateData = [
                    'status' => 1,
                    'qty2' => null,
                ];

                // Tambahkan id_spph jika qty_spph bernilai 0
                if ($detailPR->qty_spph == 0) {
                    $updateData['id_spph'] = $id;
                }

                // Lakukan update dengan data yang sudah diatur
                $update = DetailPR::where('id', $id_barang)->update($updateData);
            }
        }

        // Ambil data SPPH beserta detailnya
        $spph = Spph::where('id', $id)->first();

        $spph->details = DetailSpph::where('spph_id', $id)
            ->leftJoin('detail_pr', 'detail_pr.id', '=', 'detail_spph.id_detail_pr')
            ->select('detail_pr.*', 'detail_spph.id as id_detail_spph', 'detail_spph.spph_qty')
            ->get();
        // return response()->json($spph->details);

        $spph->details = $spph->details->map(function ($item) use ($id) {
            $item->id_spph = $id;
            $item->spek = $item->spek ? $item->spek : '';
            $item->keterangan = $item->keterangan ? $item->keterangan : '';
            $item->kode_material = $item->kode_material ? $item->kode_material : '';
            $item->nomor_spph = Spph::where('id', $id)->first()->nomor_spph ?? '';
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil ditambahkan',
            'spph' => $spph,

        ]);
    }

    
public function detailSpphSave(Request $request)
{
    // Validasi array
    $request->validate([
        'data' => 'required|array',
        'data.*.id' => 'required|integer',
        'data.*.qty2' => 'required|numeric'
    ]);

    foreach ($request->data as $item) {
        $spphDetail = DetailPR::find($item['id']);

        if (!$spphDetail) continue;

        // Pastikan qty2 tidak lebih besar dari qty_spph
        if ($spphDetail->qty_spph < $item['qty2']) {
            return response()->json(['error' => 'Qty2 tidak boleh lebih besar dari Qty1'], 400);
        }

        // Update data
        $spphDetail->qty_spph -= $item['qty2'];
        $spphDetail->qty2 = $item['qty2'];
        $spphDetail->save();
    }

    return response()->json(['success' => true]);
}


    public function nopr()
    {
        $data = PurchaseRequest::where('no_pr', 'LIKE', '%' . request('q') . '%')->paginate(10000);
        return response()->json($data);
    }


  

}
