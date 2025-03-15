<!DOCTYPE html>

<head>
    <title>Surat Jalan {{ __($suratJalan->no_sjn) }}</title>
    <style>
        
        
        body {
            margin-top: 0.1cm;
            margin-left: 0.5cm;
            margin-right: 0.5cm;
            margin-bottom: 0.5cm;
        }

        table {
            border-collapse: collapse;
            /* border: 2px solid rgb(200, 200, 200); */
            /* letter-spacing: 1px; */
            font-size: 0.8rem;
        }

        .table {
            border-collapse: collapse;
            border: 1px solid black;
            letter-spacing: 0px;
            font-size: 0.8rem;
            width: 100%;
        }

        .table2 {
            border-collapse: collapse;
            border: 1px solid black;
            letter-spacing: 1px;
            font-size: 0.8rem;
            width: 100%;
        }

        .table td,
        .table th {
            border: 1px solid black;
            padding: 10px 20px;
            font-size: 11px;
        }

        .table2 td,
        .table2 th {
            padding: 2px 5px;
        }

        .table th {
            background-color: white;
        }


        .td-border {
            border: 1px solid black;
        }

        .w-100 {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .page-break {
            page-break-after: always;
        }
       
        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: left;
            font-size: 11px;
            border-top: 3px solid red;
            padding-top: 10px;
        }
        
        
        
    </style>
</head>

<body onload="window.print()">
    @php
        $path = public_path('img/imss-remove.png');
    @endphp

    {{-- <body> --}}
    <div class="row justify-content-center">
        <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents($path)) }}" alt="image" width="120px">
    </div>
    <div style="margin-top: 1rem">
        <div style="float: left; width: 45%">
            <table>
                <tr>
                    <td colspan="3" style="font-size: 11px">KEPADA YTH&nbsp;:</td>
                </tr>
                <tr>
                    <td colspan="3" style="font-size: 11px">{{ $suratJalan->kepada }}</td>
                </tr>
                {{-- <tr>
                    <td colspan="3">PT. IMSS Madiun</td>
                </tr> --}}
                <tr>
                    <td colspan="3" style="font-size: 11px">{{ $suratJalan->lokasi }}</td>
                </tr>
            </table>
        </div>
        <div style="margin-left: 61%; width: 47%">
            <table class="table2">
                <tr>
                    <td style="font-size: 10px"><b>Nomor</b></td>
                    <td>:</td>
                    <td colspan="2" style="font-size: 10px">
                        <b>{{ $suratJalan->no_sjn }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 10px"><b>Tanggal</b></td>
                    <td>:</td>
                    <td colspan="2" style="font-size: 10px">
                        <b>{{ $suratJalan->formatted_tgl_sjn }}</b>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="d-flex w-100 justify-content-between" style="margin-bottom:1rem;margin-top:1rem">
        {{-- <div>
            <table>
                <tr>
                    <td colspan="3">KEPADA</td>
                </tr>
                <tr>
                    <td colspan="3">Team Perawatan Perkeretaapian</td>
                </tr>
                <tr>
                    <td colspan="3">PT. IMSS Madiun</td>
                </tr>
                <tr>
                    <td colspan="3">Lokasi di Ngrombo</td>
                </tr>
            </table>
        </div>
        <div>
            <table class="table2">
                <tr>
                    <td>No. Surat Jalan</td>
                    <td>:</td>
                    <td colspan="2">
                        {{ $sjn->no_sjn }}
                    </td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td colspan="2">
                        {{ $sjn->datetime }}</td>
                </tr>
            </table>
        </div> --}}
    </div>
    <div class="w-100 text-center"style="margin-top: 3rem">
        <b style="text-decoration: underline; font-size:13px;"></i>SURAT JALAN</b><br />
        <b style="font-size:13px">SJN</b>
    </div>
    @php
        $chunks = $details->chunk(5); // Adjust the chunk size as needed
    @endphp

    @foreach ($chunks as $chunk)
        <table class="table" style="width: 100%; table-layout: fixed; margin-top: 1rem">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Material</th>
                    <th>Nama Barang</th>
                    <th>Spesifikasi</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($chunk as $index => $detail)
                    <tr>
                        <td>{{ $loop->iteration + $loop->parent->index * 5 }}</td>
                        <td style="word-wrap: break-word; white-space: normal; font-size:11px;">
                            {{ $detail->kode_material }}</td>
                        <td style="word-wrap: break-word; white-space: normal; font-size:11px;">{{ $detail->barang }}
                        </td>
                        <td style="word-wrap: break-word; white-space: normal; font-size:11px;">{{ $detail->spek }}</td>
                        <td>{{ $detail->qty }}</td>
                        <td>{{ $detail->satuan }}
                        </td>
                        <td style="word-wrap: break-word; white-space: normal; font-size:11px;">
                            {{ $detail->keterangan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada produk</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <div style="margin-top: 3rem">
        <div style="float: left; width: 50%">
            <table class="w-100">
                <tr>
                    <td class="text-center"style="font-size: 11px">Pengirim</td>
                </tr>
                <tr>
                    <td style="height: 50px"></td>
                </tr>
                <tr>
                    <td class="text-center"><b
                            style="text-decoration: underline; font-size:11px;">{{ __($suratJalan->pengirim) }}</b>
                    </td>
                </tr>
            </table>
        </div>
        <div style="margin-left: 50%; width: 50%">
            <table class="w-100">
                <tr>
                    <td class="text-center" style="font-size: 11px">Penerima</td>
                </tr>
                <tr>
                    <td style="height: 50px"></td>
                </tr>
                <tr>
                    <td class="text-center"><b
                            style="text-decoration: underline"></b>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <table style="margin-top:5rem">
        <tr>
            <td colspan="3"><b style="text-decoration: underline">Note</b>&nbsp;:</td>
        </tr>
        <br>
        <tr>
            <td colspan="3"style="font-size: 11px">1. {{ __($suratJalan->note) }}</td>
        </tr>
        <tr>
            <td colspan="3"style="font-size: 11px">2. <b>Lembar Warna PUTIH & KUNING di kembalikan di PT IMSS,
                    Madiun</b></td>
        </tr>
        
    </table>
    <footer>
        <b>Kantor Pusat&nbsp;&nbsp;&nbsp;&nbsp;: Jl. Salak No 99 Madiun,&nbsp; Telp. (08351)&nbsp;454094,&nbsp; email&nbsp;: imsservice14@gmail.com</b>
    </footer>
</body>

</html>
