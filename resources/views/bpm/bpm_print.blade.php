<!DOCTYPE html>

<head>
    <title>BPM-{{ $bpm->no_bpm }}</title>
    <style>
        @page {
            margin: 0cm;
        }

        body {
            margin-top: 5.5cm;
            margin-left: 0.5cm;
            margin-right: 0.5cm;
            margin-bottom: 0.5cm;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
            font-size: 0.95rem;
        }

        a {
            color: #fff;
            text-decoration: none;
        }

        table {
            border-collapse: collapse;
        }

        table,
        td,
        th {
            /* border: 1px solid black; */
        }

        td {
            padding-left: 15px;
            padding-right: 15px;
        }

        /* thead {
            background-color: #f2f2f2;
        } */

        th {
            padding: 15px 15px 15px 25px;
        }

        .page_break {
            page-break-before: always;
        }

        .td-no-top-border {
            border-top: 1px solid transparent !important;
        }

        .td-no-left-right-border {
            border-left: 1px solid transparent !important;
            border-right: 1px solid transparent !important;
        }

        .td-no-left-border {
            border-left: 1px solid transparent !important;
        }

        .pagenum:before {
            content: counter(page);
        }

        .invoice table {
            margin: 15px;
        }

        .invoice h3 {
            margin-left: 15px;
        }

        .information {
            color: #000000;
        }

        .information .logo {
            margin: 5px;
        }

        .information table {
            padding: 10px;
        }

        header {
            position: fixed;
            top: 0.3cm;
            left: 0.5cm;
            right: 0.5cm;
            /* height: 5.5cm; */
            /* margin-bottom: 400px; */
            border: 1px solid black;
        }

        .table {
            width: 100%;
            border: 1px solid black;
            text-align: center;
        }

        .table tr,
        .table td,
        .table th {
            border: 1px solid black;
            /* padding: 5px; */
        }

        .table2 tr {
            border: 1px solid black;
            /* padding: 5px; */
        }

        body {
            border: 1px solid black;
            padding: 15px;
        }
    </style>

</head>

<body>
    <header>
        <div class="information">

            <!-- Tabel untuk Logo dan Teks -->
            <table style="width: 100%; border-collapse: collapse; border: 1px solid black;">
                <tr>
                    <!-- Kolom Pertama untuk Logo -->
                    <td align="left" style="width: 25%; border-right: 1px solid black; padding-right: 10px;">
                        <img src="https://inkamultisolusi.co.id/api_cms/public/uploads/editor/20220511071342_LSnL6WiOy67Xd9mKGDaG.png"
                            alt="Logo" width="150" class="logo" />
                    </td>

                    <!-- Kolom Kedua untuk Teks -->
                    <td align="center" style="width: 75%; padding-left: 10px;">
                        <strong style="font-size: 25px;">BON PERMINTAAN MATERIAL</strong><br>
                        <strong style="font-size: 25px;">(BPM)</strong>
                    </td>
                </tr>
            </table>

            <!-- Tabel untuk Informasi dengan Penggabungan Kolom Kedua dan Ketiga -->
            <table style="width: 100%; border-collapse: collapse; border: 1px solid black; padding-top:0px">
                <tr>
                    <!-- Kolom Pertama (Kepada Yth.) -->
                    <td align="left" style="width: 25%; padding-left: 10px; border-right: 1px solid black;">
                        <strong>Kepada Yth.</strong><br>
                        <strong>Bagian Pengendalian & Gudang</strong><br>
                    </td>

                    <!-- Kolom Kedua (Nomor* dan Tanggal*) digabung dengan Kolom Ketiga (Proyek) -->
                    <td style="width: 75%; padding-left: 10px; padding-right: 10px; vertical-align: top;">
                        <table style="width: 100%;">
                            <tr>
                                <td><strong>Nomor</strong></td>
                                <td>:</td>
                                <td><span>{{ $bpm->no_bpm }}</span></td>
                                <!-- Proyek ditempatkan di sebelah kanan -->
                                <td style="text-align: right;"><strong>Proyek :
                                        <span>{{ $bpm->nama_proyek }}</span></strong></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal</strong></td>
                                <td>:</td>
                                <td colspan="2">
                                    <span>
                                        @if ($bpm['tgl_bpm'])
                                            <?php
                                            $date = new DateTime($bpm['tgl_bpm']);
                                            echo $date->format('d F Y');
                                            ?>
                                        @else
                                            -
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

        </div>
    </header>

    {{--
    <div class="w-100 text-center">
        <b style="text-decoration: underline"></i>PURCHASE ORDER</b><br />
    </div> --}}
    <table class="table" style="width: 100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Material</th>
                <th>Uraian Barang/Jasa</th>
                <th>Spesifikasi</th>
                <th>Qty</th>
                <th>Sat</th>
                <th>Tanggal <br> Permintaan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bpm->bpmes as $item)
                @if ($loop->index % 8 == 0 && $loop->index != 0)
        </tbody>
    </table>
    <div class="page_break"></div>
    <table class="table" style="width: 100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Material</th>
                <th>Uraian Barang/Jasa</th>
                <th>Spesifikasi</th>
                <th>Qty</th>
                <th>Sat</th>
                <th>Tanggal <br> Permintaan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @endif
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->kode_material }}</td>
                <td style="word-wrap: break-word; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                    {{ $item->uraian }}</td>
                {{-- <td style="word-wrap: break-word;text-align: left">{{ $item->spek }}</td> --}}
                <td
                    style="word-wrap: break-word; max-width: 200px; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                    {{ $item->spek }}
                </td>
                <td>{{ $item->qty }}</td>
                <td>{{ $item->satuan }}</td>
                {{-- <td>{{ $item->waktu }}</td> --}}
                <td>
                    @if ($item['tanggal_permintaan'])
                        <?php
                        $date = new DateTime($item['tanggal_permintaan']);
                        echo $date->format('d F Y');
                        ?>
                    @else
                        -
                    @endif
                </td>
                <td
                    style="word-wrap: break-word; max-width: 200px; overflow: hidden; text-overflow: ellipsis; text-align: left;">
                    {{ $item->keterangan }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center" style="text-align: center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1rem">
        <div>
            <table style="width: 100%">
                <tr>
                    <td align="center" style="width: 25%;">
                        Menyetujui,<br>
                        Kabag. {{ $bpm->role }}
                        <br><br><br><br><br>
                        <strong>{{ $bpm->kabag }}</strong><br>
                    </td>
                    <td align="center" style="width: 25%;">
                        Diperiksa Oleh<br>
                        Kadep. Rendal {{ $bpm->role }} <br>
                        <br><br><br><br><br>
                    </td>
                    <td align="center" style="width: 25%;">
                        Dibuat Oleh,<br>
                        Rendal {{ $bpm->role }}
                        <br><br><br><br><br>
                        <strong>{{ $bpm->pic }}</strong><br>
                    </td>
                </tr>
            </table>
        </div>
    </div>


    <table class="table2" style="width:100%; margin-top:2rem">
        <tr>
            <td>
                <strong><u>DASAR BPM :</u></strong><br>
                <span>{!! nl2br($bpm->dasar_bpm) !!}</span>

            </td>
        </tr>
    </table>

</body>

</html>
