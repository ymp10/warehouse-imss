<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LPPB</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0cm;
        }

        body {
            margin-top: 6.5cm;
            margin-left: 0cm;
            margin-right: 0cm;
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
            padding: 10px;
        }

        td {
            padding-left: 10px;
            padding-right: 10px;
        }

        th {
            padding: 15px 15px 15px 25px;
        }

        .table {
            width: 100%;
            border: 1px solid black;
        }

        .table tr,
        .table th,
        .table td {
            border: 1px solid black;
            font-size: 11px;
        }

        .page-break {
            page-break-after: always;
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

        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            /* margin-bottom: 200px; */
            /* height: 5cm; */
        }

        .header-table th {
            border-bottom: 1px solid black;
        }

        .header-table td {
            padding: 5px;
        }

        .table2 tr {
            border: 1px solid black;
            padding: 5px;
        }

        .alamat {
            white-space: pre-wrap;
        }

        .title-header {
            margin-top: 0;
        }

        .footer {
            position: fixed;
            bottom: 0;
            right: 0;
            text-align: right;
        }

        .signature {
            text-align: center;
        }

        .no-border {
            border: none;
        }
    </style>
</head>

<body>
    <header>
        <table class="header-table" style="width: 100%; border:1px solid black;">
            <tr>
                <td align="center" style="width:33%; border:1px solid black;">
                    <img src="https://inkamultisolusi.co.id/api_cms/public/uploads/editor/20220511071342_LSnL6WiOy67Xd9mKGDaG.png"
                        alt="Logo" class="logo" width="250" />
                </td>
                <td align="center" style="vertical-align: top; width:33%; border:1px solid black;">
                    <br><br>
                    <strong style="font-size: 25px">LAPORAN PEMERIKSAAN & PENERIMAAN<br>BARANG</strong>
                </td>
                <td style="width:33%; border:1px solid black;">
                    <table>
                        <tr>
                            <td>Nomor</td>
                            <td>: {{ $po_asli['nomor_lppb'] }} </td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:
                                @if ($po_asli['tanggal_lppb'])
                                    <?php
                                    $date = new DateTime($data['tanggal_lppb']);
                                    echo $date->format('d M Y');
                                    ?>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td>No PO</td>
                            <td>:
                                @foreach ($poNumbers as $no_po)
                                    {{ $no_po }}<br>
                                @endforeach
                            </td>
                        </tr>

                        <tr>
                            <td>Proyek</td>
                            <td>: {{ $proyek }}</td>
                        </tr>
                        <tr>
                            <td>Halaman</td>
                            <td>: </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </header><br><br><br>
    <div style="margin-top: -1.9cm">
        &nbsp;&nbsp;&nbsp;Diterima dari : <br>&nbsp;&nbsp;&nbsp;PEMBELIAN LANGSUNG <br>
        @foreach ($vendors as $vendor)
            &nbsp;&nbsp;&nbsp;{{ $vendor->nama }}<br>
        @endforeach <br><br><br>
        &nbsp;&nbsp;&nbsp;Barang-barang dengan kualitas dan kuantitas seperti tersebut dibawah

    </div>

    
    @php
    $chunks = $detailpr->chunk(4); // Adjust the chunk size as needed
@endphp

@foreach ($chunks as $chunk)
    <table class="table" style="width: 100%; table-layout: fixed;">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2" style="word-wrap: break-word; white-space: normal;">Nama</th>
                <th rowspan="2">Spesifikasi Barang</th>
                <th rowspan="2" style="word-wrap: break-word; white-space: normal;">Kode<br>Barang</th>
                <th rowspan="2" style="word-wrap: break-word; white-space: normal;">Satuan</th>
                <th colspan="2">Kuantitas</th>
                <th colspan="2">Hasil Pemeriksaan</th>
                <th rowspan="2" style="word-wrap: break-word; white-space: normal;">Sudah<br>Diterima</th>
                <th rowspan="2" style="word-wrap: break-word; white-space: normal;">Belum<br>Diterima</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th>PO</th>
                <th>Penerimaan</th>
                <th>Baik</th>
                <th>Tidak Baik</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($chunk as $item)
                <tr>
                    <td>{{ $loop->iteration + ($loop->parent->index * 4) }}</td>
                    <td style="word-wrap: break-word; white-space: normal;">{{ $item->uraian }}</td>
                    <td style="word-wrap: break-word; white-space: normal;">{{ $item->spek }}</td>
                    <td style="word-wrap: break-word; white-space: normal;">{{ $item->kode_material }}</td>
                    <td style="word-wrap: break-word; white-space: normal;">{{ $item->satuan }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->penerimaan }}</td>
                    <td>{{ $item->hasil_ok }}</td>
                    <td>{{ $item->hasil_nok }}</td>
                    <td>{{ $item->diterima_qc }}</td>
                    <td>{{ $item->belum_diterima_qc }}</td>
                    <td style="word-wrap: break-word; white-space: normal;">
                        Diterima : <br>
                        @if ($item->tgl_diterima)
                            <?php
                            $date = new DateTime($item->tgl_diterima);
                            echo $date->format('d M Y');
                            ?>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if (!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

    
    
    
    

    <div style="margin-left: 70%; width: 50%; margin-top: 5%">
                <table class="w-100">
                    
                    <tr>
                        <td class="text-center" style="text-align: center"><b style="font-size: 12px">KEPALA DIVISI TEKNIK & LOGISTIK</b></td>
                    </tr>
                    <tr>
                        <td style="height: 70px"></td>
                    </tr>
                    <tr>
                        <td class="text-center" style="text-align: center"><b style="text-decoration: underline; font-size: 12px;">AMRON BAITARRIZAQ</b></td>
                    </tr>
                </table>
            </div>
    </div>
</body>

</html>
