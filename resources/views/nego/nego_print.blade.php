{{-- <!DOCTYPE html>
<html>

<head>
    <title>Negosiasi-{{ $nego->nomor_nego }}</title>
    <style type="text/css">
        @page {
            margin: 0px;
        }

        body {
            margin-top: 3cm;
            margin-left: 2.54cm;
            margin-right: 2.54cm;
            margin-bottom: 2cm;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
            font-size: 0.9rem;
        }

        header {
            position: fixed;
            top: 0.7cm;
            left: 2.54cm;
            right: 2.54cm;
            height: 6cm;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .logo {
            width: 150px;
            height: auto;
            margin-right: 10px;
        }

        .header h2 {
            font-size: 24px;
            margin: 0;
        }

        .line {
            border-top: 3px solid #000;
            margin: 10px 0;
        }

        .address {
            float: left;
            width: 50%;
        }

        .address p {
            margin: 0;
            word-wrap: break-word;
        }

        .date {
            text-align: right;
        }

        .info-surat {
            clear: both;
            text-align: left;
        }

        .info-surat p {
            margin: 0;
        }

        .judul-konten {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
        }

        .content {
            margin-top: 10px;
            line-height: 1.5;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .table-2 {
            width: 100%;
            border: 1px solid #000;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <header>
        <div class="header">
            <table style="width: 100%">
                <tr>
                    <td style="width: 10%">
                        <img src="https://inkamultisolusi.co.id/api_cms/public/uploads/editor/20220511071342_LSnL6WiOy67Xd9mKGDaG.png"
                            alt="Logo IMSS" class="logo">
                    </td>
                    <td style="width: 75%">
                        <h2>PT INKA MULTI SOLUSI SERVICE</h2>
                        <p style="margin: 0;">
                            <b>SERVICE - MAINTENANCE - LOGISTICS - GENERAL CONTRACTOR</b>
                        </p>
                        <p style="margin: 0;">Jl. Salak No. 59 Madiun - 63131</p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="line"></div>
    </header>

    <div class="container">

        @foreach ($negos as $neg)
            <div class="date">
                <p>Madiun,{{ $nego->tanggal_nego }}</p>
            </div>
            <div class="info-surat">
                <p><span class="label">Nomor Surat &nbsp;&nbsp;: {{ $nego->nomor_nego }}</span></p>
                <p><span class="label">Lampiran&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                        {{ __($nego->lampiran) > 0 ? $nego->lampiran . ' Lembar' : '-' }}</span></p>
                </p>
                <p><span class="label">Perihal &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                    </span><b>{{ $nego->perihal }}</b></p>
            </div>
            <div class="address">
                <br>
                <p>Kepada Yth,</p>
                <p>
                    <b>{!! nl2br($neg->nama) !!}</b>
                </p>
                <p>{{ $neg->alamat }}</p>
            </div>
            <div style="clear: both;"></div>
            
            <div class="content">
                <p>Dengan Hormat,</p>
                <p style="text-align: justify">
                    Menunjuk penawaran harga {{$neg->nama}} No.<b>{{$nego->no_jawaban_vendor}}</b> dengan ini kami bermaksud melakukan negosiasi dengan uraian sebagai berikut:
                </p>

                @foreach ($nego->details->chunk(4) as $chunk)
                    <table class="table" align="center">
                        <thead>
                            <tr>
                                <th style="text-align: center" rowspan="2">NO</th>
                                <th style="text-align: center" rowspan="2">Nama Barang</th>
                                <th style="text-align: center" rowspan="2">Spesifikasi</th>
                                <th style="text-align: center" rowspan="2">QTY</th>
                                <th style="text-align: center" rowspan="2">Satuan</th>
                                <th style="text-align: center" colspan="2">Penawaran Vendor</th>
                                <th style="text-align: center" colspan="2">Negosiasi PT.IMSS</th>
                            </tr>
                            <tr>
                                <th>Harga Satuan (Rp.)</th>
                                <th>Harga Total (Rp.)</th>
                                <th>Harga Satuan (Rp.)</th>
                                <th>Harga Total (Rp.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_penawaran = 0;
                                $total_negosiasi = 0;
                            @endphp
                    
                            @foreach ($chunk as $item)
                                @php
                                    $harga_per_unit = $item->harga_per_unit ?? 0;
                                    $harga_per_unit_imss = $item->harga_per_unit_imss ?? 0;
                                    $total = $item->qty * $harga_per_unit;
                                    $total_imss = $item->qty * $harga_per_unit_imss;
                                    
                                    $total_penawaran += $total;
                                    $total_negosiasi += $total_imss;
                                @endphp
                                <tr>
                                    <td style="text-align: center">{{ $loop->iteration }}</td>
                                    <td style="word-wrap: break-word; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                                        {{ $item->uraian }}
                                    </td>
                                    <td style="word-wrap: break-word; max-width: 200px; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                                        {{ $item->spek }}
                                    </td>
                                    <td style="text-align: center">{{ $item->qty }}</td>
                                    <td style="text-align: center">{{ $item->satuan }}</td>
                                    <td style="word-wrap: break-word; max-width: 200px; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                                        @rupiah($harga_per_unit)
                                    </td>
                                    <td style="word-wrap: break-word; max-width: 200px; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                                        @rupiah($total)
                                    </td>
                                    <td style="word-wrap: break-word; max-width: 200px; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                                        @rupiah($harga_per_unit_imss)
                                    </td>
                                    <td style="word-wrap: break-word; max-width: 200px; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                                        @rupiah($total_imss)
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-center" style="text-align: center; font-weight: bold;">Total</td>
                                <td style="text-align: left; font-weight: bold;">
                                    @rupiah($total_penawaran)
                                </td>
                                <td>
                                </td>
                                <td style="text-align: left; font-weight: bold;">
                                    @rupiah($total_negosiasi)
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    @if (!$loop->last)
                        <div class="page-break"></div>
                    @endif
                @endforeach

                <p style="text-align: justify">
                    Harga tersebut diatas dengan catatan sebagai berikut :
                </p>

                <ol>
                    <li>Harga belum termasuk PPN 11%</li>
                    <li>Harga Franco {{ $nego->franco }}</li>
                    <li>Termin Pembayaran : TT 30 Hari setelah pekerjaan selesai dan dokumen penagihan diterima
                        dengan benar dan lengkap oleh PT IMSS</li>
                    <li>Jawaban Negosisasi paling lambat tanggal <b>{{ $nego->batas_nego }}</b></li>
                </ol>

                <p>Demikian kami sampaikan, atas kerjasamanya diucapkan terima kasih.</p>
            </div>

            <div style="margin-left: 65%; width: 50%; margin-top: 5%">
                <table class="w-100">
                    <tr>
                        <td class="text-center" style="text-align: center"><b>PT INKA MULTI SOLUSI SERVICE</b></td>
                    </tr>
                    <tr>
                        <td class="text-center" style="text-align: center"><b>KEPALA DIVISI TEKNIK & LOGISTIK</b></td>
                    </tr>
                    <tr>
                        <td style="height: 70px"></td>
                    </tr>
                    <tr>
                        <td class="text-center" style="text-align: center"><b style="text-decoration: underline; ">AMRON
                                BAITARRIZAQ</b>
                        </td>
                    </tr>
                </table>
            </div>

            @if ($count > 1 && $loop->iteration < $count)
                <div class="page-break"></div>
            @endif
        @endforeach

    </div>
</body>

</html> --}}
<!DOCTYPE html>
<html>

<head>
    <title>Negosiasi-{{ $nego->nomor_nego }}</title>
    <style type="text/css">
        @page {
            margin: 0px;
        }

        body {
            margin-top: 3cm;
            margin-left: 2.54cm;
            margin-right: 2.54cm;
            margin-bottom: 2cm;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
            font-size: 0.9rem;
        }

        header {
            position: fixed;
            top: 0.7cm;
            left: 2.54cm;
            right: 2.54cm;
            height: 6cm;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .logo {
            width: 150px;
            height: auto;
            margin-right: 10px;
        }

        .header h2 {
            font-size: 24px;
            margin: 0;
        }

        .line {
            border-top: 3px solid #000;
            margin: 10px 0;
        }

        .address {
            float: left;
            width: 50%;
        }

        .address p {
            margin: 0;
            word-wrap: break-word;
        }

        .date {
            text-align: right;
        }

        .info-surat {
            clear: both;
            text-align: left;
        }

        .info-surat p {
            margin: 0;
        }

        .judul-konten {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
        }

        .content {
            margin-top: 10px;
            line-height: 1.5;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .table-2 {
            width: 100%;
            border: 1px solid #000;
        }

        .page-break {
            page-break-after: always;
        }

        .container {
            font-size: 3px; /* Atur ukuran font secara global untuk seluruh container */
        }

        .info-surat, .content, .address, .date, .label {
            font-size: 10px; /* Mengatur ukuran font pada elemen-elemen penting */
        }

        table.table th, table.table td {
            font-size: 9px; /* Mengatur ukuran font di dalam tabel */
        }

        ol li {
            font-size: 10px; /* Ukuran font untuk daftar */
        }

        .w-100 {
            font-size: 10px; /* Mengatur ukuran font pada elemen yang menggunakan kelas w-100 */
        }

        .text-center {
            font-size: 10px; /* Untuk elemen-elemen yang memiliki teks tengah */
        }
        
    </style>
</head>

<body>
    <header>
        <div class="header">
            <table style="width: 100%">
                <tr>
                    <td style="width: 10%">
                        <img src="https://inkamultisolusi.co.id/api_cms/public/uploads/editor/20220511071342_LSnL6WiOy67Xd9mKGDaG.png"
                            alt="Logo IMSS" class="logo">
                    </td>
                    <td style="width: 75%">
                        <h2>PT INKA MULTI SOLUSI SERVICE</h2>
                        <p style="margin: 0;">
                            <b>SERVICE - MAINTENANCE - LOGISTICS - GENERAL CONTRACTOR</b>
                        </p>
                        <p style="margin: 0;">Jl. Salak No. 59 Madiun - 63131</p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="line"></div>
    </header>

    <div class="container">

        @foreach ($negos as $neg)
            <div class="date">
                <p style="font-size: 10px">Madiun,{{ $nego->tanggal_nego }}</p>
            </div>
            <div class="info-surat">
                <p><span style="font-size: 10px" class="label">Nomor Surat &nbsp;&nbsp;: {{ $nego->nomor_nego }}</span></p>
                <p><span class="label">Lampiran&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                        {{ __($nego->lampiran) > 0 ? $nego->lampiran . ' Lembar' : '-' }}</span></p>
                </p>
                <p><span class="label">Perihal &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                    </span><b style="font-size: 10px">{{ $nego->perihal }}</b></p>
            </div>
            <div class="address">
                <br>
                <p style="font-size: 10px">Kepada Yth,</p>
                <p>
                    <b style="font-size: 10px">{!! nl2br($neg->nama) !!}</b>
                </p>
                <p style="font-size: 10px">{{ $neg->alamat }}</p>
            </div>
            <div style="clear: both;"></div>
            
            <div class="content">
                <p style="font-size: 10px">Dengan Hormat,</p>
                <p style="text-align: justify; font-size:11px;">
                    Menunjuk penawaran harga {{$neg->nama}} No.<b style="font-size: 11px">{{$nego->no_jawaban_vendor}}</b> dengan ini kami bermaksud melakukan negosiasi dengan uraian sebagai berikut:
                </p>

                @php
                    $total_penawaran = 0;
                    $total_negosiasi = 0;
                    $nomor = 1; // Inisialisasi nomor urut di luar loop chunk
                @endphp

                @foreach ($nego->details->chunk(3) as $chunk)
                    <table class="table" align="center">
                        <thead>
                            <tr>
                                <th style="text-align: center" rowspan="2">NO</th>
                                <th style="text-align: center" rowspan="2">Nama Barang</th>
                                <th style="text-align: center" rowspan="2">Spesifikasi</th>
                                <th style="text-align: center" rowspan="2">QTY</th>
                                <th style="text-align: center" rowspan="2">Satuan</th>
                                <th style="text-align: center" colspan="2">Penawaran Vendor</th>
                                <th style="text-align: center" colspan="2">Negosiasi PT.IMSS</th>
                            </tr>
                            <tr>
                                <th>Harga Satuan (Rp.)</th>
                                <th>Harga Total (Rp.)</th>
                                <th>Harga Satuan (Rp.)</th>
                                <th>Harga Total (Rp.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($chunk as $item)
                                @php
                                    $harga_per_unit = $item->harga_per_unit ?? 0;
                                    $harga_per_unit_imss = $item->harga_per_unit_imss ?? 0;
                                    $total = $item->nego_qty * $harga_per_unit;
                                    $total_imss = $item->nego_qty * $harga_per_unit_imss;
                                    
                                    $total_penawaran += $total;
                                    $total_negosiasi += $total_imss;
                                @endphp
                                <tr>
                                    <td style="text-align: center">{{ $nomor++ }}</td> <!-- Increment nomor urut -->
                                    <td style="word-wrap: break-word; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                                        {{ $item->uraian }}
                                    </td>
                                    <td style="word-wrap: break-word; max-width: 200px; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                                        {{ $item->spek }}
                                    </td>
                                    <td style="text-align: center">{{ $item->nego_qty }}</td>
                                    <td style="text-align: center">{{ $item->satuan }}</td>
                                    <td style="word-wrap: break-word; max-width: 200px; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                                        @rupiah($harga_per_unit)
                                    </td>
                                    <td style="word-wrap: break-word; max-width: 200px; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                                        @rupiah($total)
                                    </td>
                                    <td style="word-wrap: break-word; max-width: 200px; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                                        @rupiah($harga_per_unit_imss)
                                    </td>
                                    <td style="word-wrap: break-word; max-width: 200px; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                                        @rupiah($total_imss)
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        @if ($loop->last)
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-center" style="text-align: center; font-weight: bold;">Total</td>
                                    <td style="text-align: left; font-weight: bold;">
                                        @rupiah($total_penawaran)
                                    </td>
                                    <td>
                                    </td>
                                    <td style="text-align: left; font-weight: bold;">
                                        @rupiah($total_negosiasi)
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>

                    @if (!$loop->last)
                        <div class="page-break"></div>
                    @endif
                @endforeach

                <p style="text-align: justify; font-size:10px;">
                    Mohon dipertimbangkan negosiasi harga tersebut dengan catatan sebagai berikut :
                </p>

                {{-- Menggunakan angka --}}
                {{-- <table class="tabel-2" style="width:100%; font-size: 8px !important;">
                    <tr>
                        <td>
                            @php
                                $data = $nego->keterangan_nego ?? '';
                                $items = explode("\n", $data);
                            @endphp
                
                            <table style="width: 100%; border-collapse: collapse; font-size: 8px !important;">
                                @foreach ($items as $index => $item)
                                    @php
                                        $parts = explode(':', $item, 2); // Pisahkan label dan isi berdasarkan titik dua
                                        $label = trim($parts[0] ?? ''); // Ambil bagian kiri (label)
                                        $value = trim($parts[1] ?? ''); // Ambil bagian kanan (isi)
                                    @endphp
                                    <tr>
                                        <td style="width: 5%; vertical-align: top; font-size: 10px !important;">{{ $index + 1 }}.</td>
                                        <td style="width: 20%; vertical-align: top; font-size: 10px !important;">{{ $label }}</td>
                                        <td style="width: 2%; vertical-align: top; white-space: nowrap; font-size: 10px !important;">:</td>
                                        <td style="width: 70%; font-size: 10px !important;">{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                </table> --}}
                 {{-- Menggunakan Angka --}}

                 {{-- dipisah dengan : antara kiri dan kanan --}}
                {{-- <table class="tabel-2" style="width:100%; font-size: 10px !important;">
                    <tr>
                        <td>
                            @php
                                $data = $nego->keterangan_nego ?? '';
                                $items = explode("\n", $data);
                            @endphp
                
                            <table style="width: 100%; border-collapse: collapse; font-size: 10px !important;">
                                @foreach ($items as $item)
                                    @php
                                        $parts = explode(':', $item, 2); // Pisahkan label dan isi berdasarkan titik dua
                                        $label = trim($parts[0] ?? ''); // Ambil bagian kiri (label)
                                        $value = trim($parts[1] ?? ''); // Ambil bagian kanan (isi)
                                    @endphp
                                    <tr>
                                        <td style="width: 5%; vertical-align: top; font-size: 10px !important;">-</td>
                                        <td style="width: 20%; vertical-align: top; font-size: 10px !important;">{{ $label }}</td>
                                        <td style="width: 2%; vertical-align: top; white-space: nowrap; font-size: 10px !important;">:</td>
                                        <td style="width: 70%; font-size: 10px !important;">{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                </table> --}}


                {{-- Jika teks mengandung :, maka akan dipisahkan menjadi label dan isi, Jika teks tidak mengandung :, maka seluruh teks ditampilkan dalam satu kolom --}}
                <table class="tabel-2" style="width:100%; font-size: 10px !important;">
                    <tr>
                        <td>
                            @php
                                $data = $nego->keterangan_nego ?? '';
                                $items = explode("\n", $data);
                            @endphp
                
                            <table style="width: 100%; border-collapse: collapse; font-size: 10px !important;">
                                @foreach ($items as $item)
                                    @php
                                        // Cek apakah teks mengandung tanda ":" untuk pemisahan label dan isi
                                        if (strpos($item, ':') !== false) {
                                            $parts = explode(':', $item, 2);
                                            $label = trim($parts[0] ?? '');
                                            $value = trim($parts[1] ?? '');
                                        } else {
                                            $label = '';
                                            $value = trim($item); // Jika tidak ada ":", tampilkan seluruh teks
                                        }
                                    @endphp
                                    <tr>
                                        <td style="width: 5%; vertical-align: top; font-size: 10px !important;">-</td>
                                        @if ($label)
                                            <td style="width: 20%; vertical-align: top; font-size: 10px !important;">{{ $label }}</td>
                                            <td style="width: 2%; vertical-align: top; white-space: nowrap; font-size: 10px !important;">:</td>
                                            <td style="width: 70%; font-size: 10px !important;">{{ $value }}</td>
                                        @else
                                            <td colspan="3" style="width: 92%; vertical-align: top; font-size: 10px !important;">{{ $value }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                </table>
                
                
                

                <p style="font-size: 10px">Demikian kami sampaikan, atas kerjasamanya diucapkan terima kasih.</p>
            </div>
            

            <div style="margin-left: 70%; width: 50%; margin-top: 5%">
                <table class="w-100">
                   
                    <tr>
                        <td class="text-center" style="text-align: center"><b style="font-size: 12px">PT. INKA MULTI SOLUSI SERVICE</b>
                        
                            <b style="text-decoration: underline;">
                                @if ($total_negosiasi < 25000000)      
                                </b><br><b style="font-size: 10px">KEPALA DEPARTEMEN LOGISTIK</b>
                            @elseif($total_negosiasi >= 25000000 && $total_negosiasi < 100000000)
                                </b><br><b style="font-size: 10px">KEPALA DIVISI TEKNIK DAN LOGISTIK</b>
                            @elseif($total_negosiasi >= 100000000 && $total_negosiasi < 1000000000)
                                </b><br><b b style="font-size: 10px">DIREKTUR UTAMA</b>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 70px"></td>
                    </tr>
                    <tr>
                        <td style="text-align: center; vertical-align: bottom">
                            <b style="text-decoration: underline;">
                                @if ($total_negosiasi < 25000000)
                                    RUDY SUSANTO
                                
                            @elseif($total_negosiasi >= 25000000 && $total_negosiasi < 100000000)
                                AMRON BAITARRIZAQ
                                
                            @elseif($total_negosiasi >= 100000000 && $total_negosiasi < 1000000000)
                                ADIB ARDHIAN
                                
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div style="position: fixed; bottom: 10; width: 91%; text-align: left; right: 4%">
                <div style="border-bottom: 3px solid red; padding-top: 5px;">
                    <b style="font-size: 11px">PT INKA MULTI SOLUSI SERVICE</b>
                    <div style="font-size: 9px; margin-top: 2px;">
                        Kantor Pusat : Jl. Salak No.59 Madiun, Telp (0351) 454094, Website : www.imsservice.co.id, Email : imss.log@gmail.com
                    </div>
                </div>
                
            </div>
            

            @if ($count > 1 && $loop->iteration < $count)
                <div class="page-break"></div>
            @endif
        @endforeach

    </div>
</body>

</html>