<?php

namespace App\Imports;

use App\Models\Karyawan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

class KaryawanImport implements ToCollection
{
    /**
     * @param Collection $collection
     */

    function classifyEmployee($code)
    {
        if (substr($code, 0, 2) === "99") {
            return "Organik";
        } elseif (substr($code, 0, 2) === "97") {
            return "Tetap";
        } elseif (substr($code, 0, 2) === "75") {
            return "Capeg";
        } elseif (substr($code, 0, 2) === "64") {
            return "PKWT";
        } else {
            return "Resign";
        }
    }


    public function collection(Collection $rows)
    {
        // dd($rows);
        unset($rows[0]);
        foreach ($rows as $row) {

            if ($row[1] != null) {
                $tanggal_masuk = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['7'])->format('Y-m-d');
                $tanggal_pengangkatan_atau_akhir_kontrak = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['13'])->format('Y-m-d');
                $tanggal_lahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['26'])->format('Y-m-d');
                $mpp = is_numeric($row['59']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['59'])->format('Y-m-d') : NULL;
                $pensiun = is_numeric($row['60']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['60'])->format('Y-m-d') : NULL;
                $vaksin_1 = is_numeric($row['64']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['64'])->format('Y-m-d') : NULL;
                $vaksin_2 = is_numeric($row['65']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['65'])->format('Y-m-d') : NULL;
                $vaksin_3 = is_numeric($row['66']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['66'])->format('Y-m-d') : NULL;
                $jumlahTanggungan = is_numeric($row[46]) ? $row[46] : $this->getCalculatedValue($row[46]);
                $statusPajak = is_numeric($row[47]) ? $row[47] : $this->getCalculatedValue($row[47]);

                $karyawan = Karyawan::where('nip', $row[2])->count();
                if ($karyawan == 0) {
                    Karyawan::create([
                        'nip' => $row[2],
                        'nama' => $row[1],
                        'tanggal_masuk' => $tanggal_masuk,
                        'status_pegawai' => $this->classifyEmployee($row[2]),
                        'rekrutmen' => $row[5],
                        'domisili' => $row[6],
                        'rekening_mandiri' => $row[3],
                        'rekening_bsi' => $row[4] ?? '',
                        'sk_pengangkatan_atau_kontrak' => $row[12] ?? '',
                        'tanggal_pengangkatan_atau_akhir_kontrak' => $tanggal_pengangkatan_atau_akhir_kontrak,
                        'jabatan_inka' => $row[14] ?? '',
                        'jabatan_imss' => $row[15] ?? '',
                        'administrasi_atau_teknisi' => $row[16] ?? '',
                        'lokasi_kerja' => $row[17] ?? '',
                        'bagian_atau_proyek' => $row[18] ?? '',
                        'departemen_atau_subproyek' => $row[19] ?? '',
                        'divisi' => $row[20] ?? '',
                        'direktorat' => $row[21] ?? '',
                        'sertifikat' => $row[22] ?? '',
                        'surat_peringatan' => $row[23] ?? '',
                        'jenis_kelamin' => $row[24] ?? '',
                        'tempat_lahir' => $row[25] ?? '',
                        'tanggal_lahir' => $tanggal_lahir,
                        'nomor_ktp' => $row[28] ?? '',
                        'alamat' => $row[29] ?? '',
                        'nomor_hp' => $row[30] ?? '',
                        'email' => $row[33] ?? '',
                        'bpjs_kesehatan' => $row[34] ?? '',
                        'bpjs_ketenagakerjaan' => $row[35] ?? '',
                        'status_pernikahan' => $row[36] ?? '',
                        'suami_atau_istri' => $row[37] ?? '',
                        'anak_ke_1' => $row[38] ?? '',
                        'anak_ke_2' => $row[39] ?? '',
                        'anak_ke_3' => $row[40] ?? '',
                        'tambahan' => $row[41] ?? '',
                        'ayah_kandung' => $row[42] ?? '',
                        'ibu_kandung' => $row[43] ?? '',
                        'ayah_mertua' => $row[44] ?? '',
                        'ibu_mertua' => $row[45] ?? '',
                        'jumlah_tanggungan' => $jumlahTanggungan,
                        'status_pajak' => $statusPajak,
                        'npwp' => $row[49] ?? '',
                        'agama' => $row[50] ?? '',
                        'pendidikan_diakui' => $row[51] ?? '',
                        'jurusan' => $row[52] ?? '',
                        'almamater' => $row[53] ?? '',
                        'tahun_lulus' => $row[54] ?? '',
                        'pendidikan_terakhir' => $row[55] ?? '',
                        'jurusan_terakhir' => $row[56] ?? '',
                        'almamater_terakhir' => $row[57] ?? '',
                        'tahun_lulus_terakhir' => $row[58] ?? '',
                        'mpp' => $mpp,
                        'pensiun' => $pensiun,
                        'ukuran_baju' => $row[61] ?? '',
                        'ukuran_celana' => $row[62] ?? '',
                        'ukuran_sepatu' => $row[63] ?? '',
                        'vaksin_1' => $vaksin_1,
                        'vaksin_2' => $vaksin_2,
                        'vaksin_3' => $vaksin_3,
                    ]);
                } else {
                    //update data

                    // Karyawan::where('nip', $row[2])
                    //     ->update([
                    //         'nip' => $row[2],
                    //         'nama' => $row[1],
                    //         'tanggal_masuk' => $tanggal_masuk,
                    //         'status_pegawai' => $this->classifyEmployee($row[2]),
                    //         'rekrutmen' => $row[5],
                    //         'domisili' => $row[6],
                    //         'rekening_mandiri' => $row[3],
                    //         'rekening_bsi' => $row[4] ?? '',
                    //         'sk_pengangkatan_atau_kontrak' => $row[11] ?? '',
                    //         'tanggal_pengangkatan_atau_akhir_kontrak' => $tanggal_pengangkatan_atau_akhir_kontrak,
                    //         'jabatan_inka' => $row[13] ?? '',
                    //         'jabatan_imss' => $row[14] ?? '',
                    //         'administrasi_atau_teknisi' => $row[15] ?? '',
                    //         'lokasi_kerja' => $row[16] ?? '',
                    //         'bagian_atau_proyek' => $row[17] ?? '',
                    //         'departemen_atau_subproyek' => $row[18] ?? '',
                    //         'divisi' => $row[19] ?? '',
                    //         'direktorat' => $row[20] ?? '',
                    //         'sertifikat' => $row[21] ?? '',
                    //         'surat_peringatan' => $row[22] ?? '',
                    //         'jenis_kelamin' => $row[23] ?? '',
                    //         'tempat_lahir' => $row[24] ?? '',
                    //         'tanggal_lahir' => $tanggal_lahir,
                    //         'nomor_ktp' => $row[27] ?? '',
                    //         'alamat' => $row[28] ?? '',
                    //         'nomor_hp' => $row[29] ?? '',
                    //         'email' => $row[31] ?? '',
                    //         'bpjs_kesehatan' => $row[32] ?? '',
                    //         'bpjs_ketenagakerjaan' => $row[33] ?? '',
                    //         'status_pernikahan' => $row[34] ?? '',
                    //         'suami_atau_istri' => $row[35] ?? '',
                    //         'anak_ke_1' => $row[36] ?? '',
                    //         'anak_ke_2' => $row[37] ?? '',
                    //         'anak_ke_3' => $row[38] ?? '',
                    //         'tambahan' => $row[39] ?? '',
                    //         'ayah_kandung' => $row[40] ?? '',
                    //         'ibu_kandung' => $row[41] ?? '',
                    //         'ayah_mertua' => $row[42] ?? '',
                    //         'ibu_mertua' => $row[43] ?? '',
                    //         'jumlah_tanggungan' => $row[44] ?? '',
                    //         'status_pajak' => $row[45] ?? '',
                    //         'npwp' => $row[46] ?? '',
                    //         'agama' => $row[47] ?? '',
                    //         'pendidikan_diakui' => $row[48] ?? '',
                    //         'jurusan' => $row[49] ?? '',
                    //         'almamater' => $row[50] ?? '',
                    //         'tahun_lulus' => $row[51] ?? '',
                    //         'pendidikan_terakhir' => $row[52] ?? '',
                    //         'jurusan_terakhir' => $row[53] ?? '',
                    //         'almamater_terakhir' => $row[54] ?? '',
                    //         'tahun_lulus_terakhir' => $row[55] ?? '',
                    //         'mpp' => $row['56'] ? $mpp : NULL,
                    //         'pensiun' => $row['57'] ? $pensiun : NULL,
                    //         'ukuran_baju' => $row[58] ?? '',
                    //         'ukuran_celana' => $row[59] ?? '',
                    //         'ukuran_sepatu' => $row[60] ?? '',
                    //         'vaksin_1' => $row['61'] ? $vaksin_1 : NULL,
                    //         'vaksin_2' => $row['62'] ? $vaksin_2 : NULL,
                    //         'vaksin_3' => $row['63'] ? $vaksin_3 : NULL,

                    //     ]);

                    Karyawan::create([
                        'nip' => $row[2],
                        'nama' => $row[1],
                        'tanggal_masuk' => $tanggal_masuk,
                        'status_pegawai' => $this->classifyEmployee($row[2]),
                        'rekrutmen' => $row[5],
                        'domisili' => $row[6],
                        'rekening_mandiri' => $row[3],
                        'rekening_bsi' => $row[4] ?? '',
                        'sk_pengangkatan_atau_kontrak' => $row[12] ?? '',
                        'tanggal_pengangkatan_atau_akhir_kontrak' => $tanggal_pengangkatan_atau_akhir_kontrak,
                        'jabatan_inka' => $row[14] ?? '',
                        'jabatan_imss' => $row[15] ?? '',
                        'administrasi_atau_teknisi' => $row[16] ?? '',
                        'lokasi_kerja' => $row[17] ?? '',
                        'bagian_atau_proyek' => $row[18] ?? '',
                        'departemen_atau_subproyek' => $row[19] ?? '',
                        'divisi' => $row[20] ?? '',
                        'direktorat' => $row[21] ?? '',
                        'sertifikat' => $row[22] ?? '',
                        'surat_peringatan' => $row[23] ?? '',
                        'jenis_kelamin' => $row[24] ?? '',
                        'tempat_lahir' => $row[25] ?? '',
                        'tanggal_lahir' => $tanggal_lahir,
                        'nomor_ktp' => $row[28] ?? '',
                        'alamat' => $row[29] ?? '',
                        'nomor_hp' => $row[30] ?? '',
                        'email' => $row[33] ?? '',
                        'bpjs_kesehatan' => $row[34] ?? '',
                        'bpjs_ketenagakerjaan' => $row[35] ?? '',
                        'status_pernikahan' => $row[36] ?? '',
                        'suami_atau_istri' => $row[37] ?? '',
                        'anak_ke_1' => $row[38] ?? '',
                        'anak_ke_2' => $row[39] ?? '',
                        'anak_ke_3' => $row[40] ?? '',
                        'tambahan' => $row[41] ?? '',
                        'ayah_kandung' => $row[42] ?? '',
                        'ibu_kandung' => $row[43] ?? '',
                        'ayah_mertua' => $row[44] ?? '',
                        'ibu_mertua' => $row[45] ?? '',
                        'jumlah_tanggungan' => $row[46] ?? '',
                        'status_pajak' => $row[47] ?? '',
                        'npwp' => $row[49] ?? '',
                        'agama' => $row[50] ?? '',
                        'pendidikan_diakui' => $row[51] ?? '',
                        'jurusan' => $row[52] ?? '',
                        'almamater' => $row[53] ?? '',
                        'tahun_lulus' => $row[54] ?? '',
                        'pendidikan_terakhir' => $row[55] ?? '',
                        'jurusan_terakhir' => $row[56] ?? '',
                        'almamater_terakhir' => $row[57] ?? '',
                        'tahun_lulus_terakhir' => $row[58] ?? '',
                        'mpp' => $mpp,
                        'pensiun' => $pensiun,
                        'ukuran_baju' => $row[61] ?? '',
                        'ukuran_celana' => $row[62] ?? '',
                        'ukuran_sepatu' => $row[63] ?? '',
                        'vaksin_1' => $vaksin_1,
                        'vaksin_2' => $vaksin_2,
                        'vaksin_3' => $vaksin_3,
                    ]);
                }
            }
        }
    }
    private function getCalculatedValue($cellValue)
    {
        return (is_numeric($cellValue)) ? $cellValue : (float) filter_var($cellValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
}

//perubahanku


