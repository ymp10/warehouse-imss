<?php

namespace App\Http\Controllers;

use data;
use Carbon\Carbon;
use App\Models\Kasbon;
use App\Models\MemoKasbon;
use App\Models\PrintKasbon;
use Illuminate\Http\Request;
use App\Exports\KasbonExport;
use App\Imports\KasbonImport;
use App\Models\KasbonLampian;
use App\Models\KasbonLampiran;
use Barryvdh\DomPDF\Facade\Pdf;
use CreateMemoKasbonTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KasbonController extends Controller
{
    public function index()
    {
        $kasbons = Kasbon::orderBy('created_at', 'desc')->get()->map(function ($kasbon) {
            // Konversi string ke Carbon jika ada tanggal yang tidak null
            $kasbon->tanggal_verifikasi_setelah_cair = $kasbon->tanggal_verifikasi_setelah_cair ? Carbon::parse($kasbon->tanggal_verifikasi_setelah_cair) : null;
            $kasbon->tanggal_waktu_close_kasbon = $kasbon->tanggal_waktu_close_kasbon ? Carbon::parse($kasbon->tanggal_waktu_close_kasbon) : null;
            $kasbon->perpanjangan_batas_waktu_pertanggungjawaban_1 = $kasbon->perpanjangan_batas_waktu_pertanggungjawaban_1 ? Carbon::parse($kasbon->perpanjangan_batas_waktu_pertanggungjawaban_1) : null;
            $kasbon->perpanjangan_batas_waktu_pertanggungjawaban_2 = $kasbon->perpanjangan_batas_waktu_pertanggungjawaban_2 ? Carbon::parse($kasbon->perpanjangan_batas_waktu_pertanggungjawaban_2) : null;
    
            // Hitung jangka waktu otomatis berdasarkan tanggal hari ini
            if ($kasbon->tanggal_waktu_close_kasbon) {
                // Pastikan kedua tanggal di-set ke awal hari (00:00:00)
                $tanggalHariIni = Carbon::now()->copy()->startOfDay();
                $tanggalCloseKasbon = $kasbon->tanggal_waktu_close_kasbon->copy()->startOfDay();
    
                // Hitung selisih hari antara tanggal close kasbon dan hari ini
                $selisihHari = $tanggalCloseKasbon->diffInDays($tanggalHariIni, false);
    
                // Simpan hasil negatif karena perhitungan sebaliknya
                $kasbon->jangka_waktu = -$selisihHari;

                 // Jika jangka waktu negatif, ubah status menjadi overdue
            if ($kasbon->jangka_waktu < 0) {
                $kasbon->status = 'overdue';
            }
    
                // Simpan perubahan ke database
                $kasbon->save();
            }
            return $kasbon;
        });
    
        return view('kasbon.kasbon', compact('kasbons'));
    }
    






    public function store(Request $request)
    {
        // Validasi data input dengan pesan khusus
        $request->validate([
            'nama' => 'required',
            'jumlah_kasbon' => 'nullable',
            'pekerjaan_proyek_kasbon' => 'required',
            'tanggal_verifikasi_setelah_cair' => 'required|date',
            'realisasi' => 'nullable',
            'no_ppk' => 'nullable|string',
            'perpanjangan_batas_waktu_pertanggungjawaban_1' => 'nullable|date',
            'perpanjangan_batas_waktu_pertanggungjawaban_2' => 'nullable|date',
            'divisi' => 'nullable|string',
            'kategori' => 'nullable|string',
            'lampiran_foto' => 'nullable|file',
        ], [
            'nama.required' => 'Nama harus diisi.',
            'pekerjaan_proyek_kasbon.required' => 'Pekerjaan proyek kasbon harus diisi.',
            'tanggal_verifikasi_setelah_cair.required' => 'Tanggal verifikasi setelah cair harus diisi.',
            'lampiran_foto.file' => 'Lampiran harus berupa file.',
            'jumlah_kasbon.numeric' => 'Jumlah kasbon harus berupa angka.',
            'realisasi' => 'Realisasi harus berupa angka.',
        ]);

        // Cek apakah kasbon dengan nama dan jumlah yang sama sudah ada
        $existingKasbon = Kasbon::where('nama', $request->nama)
            ->where('jumlah_kasbon', $request->jumlah_kasbon)
            ->first();

        if ($existingKasbon) {
            return redirect()->back()->with('error', 'Kasbon dengan nama dan jumlah yang sama sudah ada.');
        }

        // Ambil Tanggal Verifikasi dan Jangka Waktu dari Input Pengguna
        $tanggalVerifikasi = $request->tanggal_verifikasi_setelah_cair;
        $jangkaWaktu = $request->jangka_waktu; // Ambil jangka waktu dari input pengguna

        // Hitung Tanggal Close Kasbon Berdasarkan Jangka Waktu
        $tanggalCloseKasbon = date('Y-m-d', strtotime($tanggalVerifikasi . " + $jangkaWaktu days"));

        // Isi data ke dalam model Kasbon
        $kasbon = new Kasbon;
        $kasbon->nama = $request->nama;
        $kasbon->jumlah_kasbon = $request->jumlah_kasbon;
        $kasbon->pekerjaan_proyek_kasbon = $request->pekerjaan_proyek_kasbon;
        $kasbon->tanggal_verifikasi_setelah_cair = $tanggalVerifikasi;
        $kasbon->jangka_waktu = $jangkaWaktu;
        $kasbon->status = $request->status ?? 'pending';
        $kasbon->tanggal_waktu_close_kasbon = $tanggalCloseKasbon;
        $kasbon->realisasi = $request->realisasi ? (float) str_replace('.', '', $request->realisasi) : null;
        $kasbon->no_ppk = $request->no_ppk;
        $kasbon->perpanjangan_batas_waktu_pertanggungjawaban_1 = $request->perpanjangan_batas_waktu_pertanggungjawaban_1;
        $kasbon->perpanjangan_batas_waktu_pertanggungjawaban_2 = $request->perpanjangan_batas_waktu_pertanggungjawaban_2;
        $kasbon->divisi = $request->divisi;
        $kasbon->kategori = $request->kategori;

        // Simpan objek Kasbon ke database
        $kasbon->save();


        // Logika untuk menyimpan lampiran
        if ($request->hasFile('lampiran_foto')) {
            try {
                $file = $request->file('lampiran_foto');
                $file_name = rand() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('lampiran/baru'), $file_name);
                $kasbon->lampiran_foto = $file_name;
            } catch (\Exception $e) {
                // Tampilkan pesan error jika upload gagal
                return redirect()->back()->with('error', 'Gagal mengunggah lampiran: ' . $e->getMessage());
            }
        }

        // Simpan objek Kasbon ke database
        $kasbon->save();

        // Redirect dengan notifikasi sukses
        return redirect()->back()->with('success', 'Data kasbon berhasil disimpan.');
    }



    public function destroy($id)
    {
        $kasbon = Kasbon::findOrFail($id);
        $filePath = $kasbon->lampiran_foto;
        // Tentukan path lengkap file di penyimpanan
        $fullPath = public_path('lampiran/baru/' . $filePath);

        // Hapus file dari storage 
        if ($filePath && file_exists($fullPath)) {
            unlink($fullPath); // Hapus file dari server
        }
        $kasbon->delete();
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'nama' => 'required',
        'pekerjaan_proyek_kasbon' => 'required',
        'tanggal_verifikasi_setelah_cair' => 'required|date',
        'status' => 'required',
        'perpanjangan_batas_waktu_pertanggungjawaban_1' => 'nullable|integer|min:1',
        'perpanjangan_batas_waktu_pertanggungjawaban_2' => 'nullable|integer|min:1',
    ]);

    $kasbon = Kasbon::findOrFail($id);

    $tanggalVerifikasi = \Carbon\Carbon::parse($request->tanggal_verifikasi_setelah_cair);
    $tanggalHariIni = \Carbon\Carbon::now();
    $pesanPerpanjangan = '';

    // Logika untuk menghitung tanggal close berdasarkan jangka waktu
    $jangkaWaktu = $request->jangka_waktu;
    $tanggalCloseKasbon = $tanggalVerifikasi->copy()->addDays($jangkaWaktu)->format('Y-m-d');

    // Logika jika kasbon status diubah ke 'close'
    if (!empty($request->pertanggung_jawaban) || $request->status === 'close') {
        $kasbon->status = 'close';
        $kasbon->jangka_waktu = null;
        $kasbon->tanggal_waktu_close_kasbon = $tanggalHariIni->format('Y-m-d H:i:s');
    } else {
        // Logika perpanjangan batas waktu pertanggungjawaban 1
        if ($request->filled('perpanjangan_batas_waktu_pertanggungjawaban_1')) {
            // Jika sudah ada nilai sebelumnya, pertahankan nilainya
            if (empty($kasbon->perpanjangan_batas_waktu_pertanggungjawaban_1)) {
                $kasbon->perpanjangan_batas_waktu_pertanggungjawaban_1 = $tanggalHariIni->format('Y-m-d');
                $kasbon->jangka_waktu = $request->perpanjangan_batas_waktu_pertanggungjawaban_1;
                $kasbon->tanggal_waktu_close_kasbon = $tanggalHariIni->copy()->addDays($kasbon->jangka_waktu)->format('Y-m-d');
                $pesanPerpanjangan = 'Perpanjangan batas waktu pertanggungjawaban 1 berhasil diterapkan.';
            }
        }

        // Logika perpanjangan batas waktu pertanggungjawaban 2
        if ($request->filled('perpanjangan_batas_waktu_pertanggungjawaban_2')) {
            $kasbon->perpanjangan_batas_waktu_pertanggungjawaban_2 = $tanggalHariIni->format('Y-m-d');
            $kasbon->jangka_waktu = $request->perpanjangan_batas_waktu_pertanggungjawaban_2;
            $kasbon->tanggal_waktu_close_kasbon = $tanggalHariIni->copy()->addDays($kasbon->jangka_waktu)->format('Y-m-d');
            $pesanPerpanjangan = 'Perpanjangan batas waktu pertanggungjawaban 2 berhasil diterapkan.';
        }

        // Cek apakah kasbon sudah melewati batas waktu atau belum
        if (\Carbon\Carbon::parse($kasbon->tanggal_waktu_close_kasbon)->isPast()) {
            $selisihHariLewat = $tanggalHariIni->diffInDays(\Carbon\Carbon::parse($kasbon->tanggal_waktu_close_kasbon), false);
            $kasbon->jangka_waktu = $selisihHariLewat;
            $kasbon->status = 'overdue';
        } elseif ($kasbon->jangka_waktu === 0) {
            $kasbon->status = 'overdue';
        } elseif ($kasbon->jangka_waktu > 0) {
            $kasbon->status = 'open';
        }
    }

    // Update data kasbon lainnya
    $kasbon->nama = $request->nama;
    $kasbon->jumlah_kasbon = $request->jumlah_kasbon;
    $kasbon->pekerjaan_proyek_kasbon = $request->pekerjaan_proyek_kasbon;
    $kasbon->pertanggung_jawaban = $request->pertanggung_jawaban;
    $kasbon->tanggal_verifikasi_setelah_cair = $request->tanggal_verifikasi_setelah_cair;
    $kasbon->realisasi = $request->realisasi;
    $kasbon->no_ppk = $request->no_ppk;
    $kasbon->divisi = $request->divisi;
    $kasbon->kategori = $request->kategori;
   

    // Logika untuk upload lampiran foto
    if ($request->hasFile('lampiran_foto')) {
        $file = $request->file('lampiran_foto');
        if ($file->isValid() && in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'pdf'])) {
            $file_name = rand() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('lampiran/baru'), $file_name);
            $kasbon->lampiran_foto = $file_name;
        }
    }

    $kasbon->save();

    return redirect()->back()->with('success', 'Data kasbon berhasil diupdate.')->with('pesanPerpanjangan', $pesanPerpanjangan);
}



    








    public function export(Request $request)
    {
        // Ambil status dari request, default ke 'open' jika tidak ada
        $status = $request->get('status', 'all');

        // Validasi status, hanya menerima 'open' atau 'close'
        if (!in_array($status, ['all', 'open', 'close', 'overdue'])) {
            return redirect()->back()->withErrors('Status tidak valid.');
        }

        // Ekspor data berdasarkan status
        return Excel::download(new KasbonExport($status), 'kasbon_' . $status . '.xlsx');
    }


    public function import(Request $request)
    {

        $this->validate($request, [
            'file' => 'required|mimes:xls,xlsx'
        ]);

        $file = $request->file('file');

        $nama_file = rand() . $file->getClientOriginalName();

        $file->move(public_path('temp'), $nama_file);

        // $file = public_path('karyawan.xlsx');
        Excel::import(new KasbonImport, public_path('temp/' . $nama_file));

        return redirect()->back()->with('success', 'berhasil di import');
    }


    public function hapusMultiple(Request $request)
    {
        if ($request->has('ids')) {
            $ids = $request->input('ids');

            // Mulai transaksi
            DB::beginTransaction();
            try {
                // Hapus data dari tabel keuangan_kasbon
                DB::table('keuangan_kasbon')->whereIn('id', $ids)->delete();

                // Hapus data dari tabel Kasbon
                Kasbon::whereIn('id', $ids)->delete();

                // Commit transaksi
                DB::commit();

                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                // Rollback transaksi jika terjadi kesalahan
                DB::rollback();
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function totalKasbon()
    {
        // Mengambil total jumlah kasbon berdasarkan divisi
        $totalKasbonDivisi = Kasbon::select('divisi', DB::raw('SUM(jumlah_kasbon) as total'))
            ->groupBy('divisi')
            ->get();
    
        // Mengembalikan response JSON
        return response()->json($totalKasbonDivisi);
    }

 

    public function kasbonPrint()
    {
        $kasbonData = MemoKasbon::all();
        $tanggalHariIni = Carbon::now()->isoFormat('D MMMM Y'); // Format: 4 November 2024
    
        $pdf = Pdf::loadView('kasbon.kasbon_print', compact( 'tanggalHariIni' , 'kasbonData'));
    
        // Set ukuran dan orientasi PDF
        $pdf->setPaper('A4', 'portrait');
        
        // Nama file PDF
        $fileName = 'MEMO_KASBON.pdf';
    
        // Tampilkan hasil PDF di browser
        return $pdf->stream($fileName);
    }
    public function memoKasbon(Request $request)
{
    // Validasi input jika diperlukan
    $request->validate([
        'nomor' => 'required|string|max:255',
        'divisi' => 'required|array', // Pastikan divisi adalah array
        'divisi.*' => 'string|max:255', // Setiap item dalam divisi harus berupa string
        'minggu' => 'required|string|max:255',
    ], [
        'nomor.required' => 'Nomor harus diisi',
        'divisi.required' => 'Divisi harus diisi',
        'minggu.required' => 'Minggu harus diisi',
    ]);

    // Mengambil data request
    $id_memo = $request->id_memo;
    $nomor = $request->nomor;
    $divisiList = $request->divisi; // 'divisi' adalah array
    $minggu = $request->minggu;

    // Menggabungkan array divisi menjadi string
    $divisiString = implode('. ', $divisiList);

    // Update atau buat baru jika belum ada
    MemoKasbon::updateOrCreate(
        ['id_memo' => $id_memo], // Kondisi untuk mengecek apakah data ada
        [
            'nomor' => $nomor,
            'divisi' => $divisiString,
            'minggu' => $minggu,
        ] // Data yang akan diperbarui
    );

    return response()->json([
        'success' => true,
        'message' => 'Memo Kasbon berhasil diperbarui!',
    ]);
}
  
// KasbonController.php
public function showMemo()
{
    // Ambil data dari tabel keuangan_kasbon (misalnya data memo)
    $kasbonData = MemoKasbon::all(); // Atau sesuaikan query dengan kebutuhan

    // Mengembalikan data dalam format JSON
    return response()->json($kasbonData);
}



    
}
