<!DOCTYPE html>

<head>
    <title>Purchase Request-{{ $pr->no_pr }}</title>
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
            <table width="100%">
                <tr style="border: 1px solid black;">
                    <td align="left" style="width: 25%; border: 1px solid black;">
                        <img src="https://inkamultisolusi.co.id/api_cms/public/uploads/editor/20220511071342_LSnL6WiOy67Xd9mKGDaG.png"
                            alt="Logo" width="150" class="logo" /><br>
                    </td>

                    <td align="center" style="width: 85%; border-style: none;">
                        <br><strong style="font-size: 15">PURCHASE REQUEST</strong><br>
                        <strong style="font-size: 15">(PR)</strong><br>
                    </td>
                    <td style= "border-style:none"></td>
                </tr>
                <tr>
                    <td align="left" style="width: 25%;">
                        <br><br>
                        <strong>Kepada Yth.</strong><br>
                        <strong>Dept. Logistik</strong><br>
                    </td>

                    <td align="center">
                        <br><br>
                        <strong>&nbsp;&nbsp;&nbsp;Nomor* : <span>{{ $pr->no_pr }}</span></strong><br>
                        <strong>Tanggal* :
                            {{-- <strong>Tanggal* : <span>{{ $pr->tgl_pr }}</span></strong><br> --}}
                            <span>
                                @if ($pr['tgl_pr'])
                                    {{ \Carbon\Carbon::parse($pr['tgl_pr'])->translatedFormat('d F Y') }}
                                @else
                                    -
                                @endif
                            </span>
                    </td>

                    <td align="right" style="width: 35%;">
                        <br><br>
                        <strong>Proyek : <span>{{ $pr->nama_pekerjaan }}</span></strong><br>
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
                <th>Waktu <br> Penyelesaiaan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pr->purchases as $item)
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
                <th>Waktu <br> Penyelesaiaan</th>
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
                    @if ($item['waktu'])
                        {{ \Carbon\Carbon::parse($item['waktu'])->locale('id')->translatedFormat('d F Y') }}
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
                        Kadiv. {{ $pr->role }}
                        <br><br><br><br><br>
                        <strong>{{ $pr->kadiv }}</strong><br>
                    </td>
                    <td align="center" style="width: 25%;">
                        Diperiksa Oleh<br>
                        Kadep. {{ $pr->role }} <br>
                        <br><br><br><br>
                        <strong>{{ $pr->kadep }}</strong><br>
                    </td>
                    <td align="center" style="width: 25%;">
                        Dibuat Oleh,<br>
                        Staff {{ $pr->role }}
                        <br><br><br><br><br>
                        <strong>{{ $pr->pic }}</strong><br>
                    </td>
                </tr>
            </table>
        </div>
    </div>


    <table class="table2" style="width:100%; margin-top:2rem">
        <tr>
            <td>
                <strong><u>DASAR PR :</u></strong><br>
                <span>{!! nl2br($pr->dasar_pr) !!}</span>

            </td>
        </tr>
    </table>

</body>

</html>
