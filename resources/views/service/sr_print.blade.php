<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        @page {
            margin: 0cm;
        }

        body {
            margin-top: 2cm;
            /* Sesuaikan margin atas jika diperlukan */
            margin-left: 0.25cm;
            margin-right: 0.25cm;
            margin-bottom: 0.5cm;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
            font-size: 0.95rem;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        th, td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }

        header {
            /* position: fixed; */
            top: 0.1cm;
            left: 0.5cm;
            right: 0.5cm;
        }

        .table {
            width: 100%;
        }

        .information .logo {
            margin: 5px;
        }

        .info-table {
            width: 100%;
            /* border: 1px solid black; */
            border: none; /* Menghapus border tabel */
            border-collapse: collapse;
        }

        .info-table td {
            border: none;
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }

        .info-header {
            width: 25%;
        }

        .info-middle {
            width: 50%;
            text-align: center;
        }

        .info-right {
            width: 25%;
        }
    </style>
</head>

<body>
    <header>
        <div class="information">
            <table width="100%">
                <tr>
                    <td class="info-header" align="left">
                        <img src="https://inkamultisolusi.co.id/api_cms/public/uploads/editor/20220511071342_LSnL6WiOy67Xd9mKGDaG.png" alt="Logo" width="150" class="logo" />
                    </td>
                    <td class="info-middle" align="center">
                        <br><strong style="font-size: 25">Bill Of Material</strong><br>
                    </td>
                    <td class="info-right" align="right">
                        <table class="info-table">
                            <tr>
                                <td><strong>Nomor:</strong></td>
                                <td>{{ $no_sr }}</td>
                            </tr>
                            <tr>
                                <td><strong>PIC:</strong></td>
                                <td>{{ $pic }}</td>
                            </tr>
                            <tr>
                                <td><strong>Proyek:</strong></td>
                                <td>{{ $nama_proyek }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal:</strong></td>
                                <td>{{ $tgl }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </header>

    <table class="table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 3%;">No</th>
                <th rowspan="2" style="width: 10%;">Kode Material</th>
                <th rowspan="2" style="width: 15%;">Deskripsi Material</th>
                <th rowspan="2" style="width: 10%;">Spesifikasi</th>
                <th colspan="8" style="width: 40%;">Volume Perawatan (Preventive) Part Per TS</th>
                <th rowspan="2" style="width: 5%;">Volume (Protective) Part Untuk 1 TS</th>
                <th rowspan="2" style="width: 4%;">UoM (Sat)</th>
            </tr>
            <tr>
                <th style="width: 5%;">P1</th>
                <th style="width: 5%;">P3</th>
                <th style="width: 5%;">P6</th>
                <th style="width: 5%;">P12</th>
                <th style="width: 5%;">P24</th>
                <th style="width: 5%;">P48</th>
                <th style="width: 5%;">P60</th>
                <th style="width: 5%;">P72</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($komponen as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->kode_material }}</td>
                    <td style="text-align: left">{{ $item->desc_material }}</td>
                    <td style="word-wrap: break-word;text-align: left">{{ $item->spek }}</td>
                    <td>{{ $item->p1 }}</td>
                    <td>{{ $item->p3 }}</td>
                    <td>{{ $item->p6 }}</td>
                    <td>{{ $item->p12 }}</td>
                    <td>{{ $item->p24 }}</td>
                    <td>{{ $item->p48 }}</td>
                    <td>{{ $item->p60 }}</td>
                    <td>{{ $item->p72 }}</td>
                    <td>{{ $item->vol_protective }}</td>
                    <td>{{ $item->satuan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="15" class="text-center" style="text-align: center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
