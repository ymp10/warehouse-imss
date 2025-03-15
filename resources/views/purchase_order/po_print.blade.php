<!DOCTYPE html>

<head>
    <title>Purchase Order {{ $po->nama_proyek ?? '-' }}</title>
    <style>
        @page {
            margin: 0cm;
        }

        body {
            margin-top: 8.4cm;
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
            /* margin-top: 1cm; */
            border: 1px solid black;
        }

        .table tr,
        .table th,
        .table td {
            border: 1px solid black;
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


        .information table {
            /* padding: 10px; */
            margin-bottom: 2cm;
        }

        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 10cm;
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

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
        }

        body {
            margin-bottom: 5cm;
        }
        .page_break {
            page-break-before: always;
        }
    </style>

</head>

<body>
    <header>
        {{-- <div class="information"> --}}
        <table>
            <tr>
                <td style="text-align: left;width:33%;vertical-align:top;padding-top:10px" rowspan="12">
                    <strong>Company</strong><br>
                    <span>{{ $po->nama_vendor }}</span><br>
                    <p class="alamat">{{ $po->alamat_vendor ?? '-' }}</p>
                    <span>Contact</span><br>
                    <span>Telepon&nbsp;&nbsp;&nbsp;&nbsp;: {{ $po->telp_vendor ?? '-' }}</span><br>
                    <span>Fax&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                        {{ $po->fax_vendor ?? '-' }}</span><br>
                    <span>Email&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                        {{ $po->email_vendor ?? '-' }}</span><br>
                </td>
                <td align="center" rowspan="10" style="vertical-align:top;">
                    <img src="https://inkamultisolusi.co.id/api_cms/public/uploads/editor/20220511071342_LSnL6WiOy67Xd9mKGDaG.png"
                        alt="Logo" width="250" class="logo" /><br>
                    <br><br>
                    <strong>PT INKA MULTI SOLUSI SERVICE</strong><br>
                    Jl Salak No. 99 Madiun 63131-Indonesia<br>
                    Telepon +62 812 3456789<br>
                    <br><strong style="font-size: 25">Purchase Order</strong><br>
                </td>
                {{-- <td style="border:1px solid black"> --}}
            <tr>
                <td style="text-align: left;width: 8rem;vertical-align:top;">NO PO</td>
                <td style="text-align: left;vertical-align:top;">: <span>{{ $po->no_po }}</span></td>
            </tr>
            <tr>
                <td style="text-align: left;vertical-align:top;">Tanggal PO</td>
                <td style="text-align: left;vertical-align:top;">: <span>{{ $po->tanggal_po }}</span></td>
            </tr>
            <tr>
                <td style="text-align: left;vertical-align:top;">Incoterm</td>
                <td style="text-align: left;vertical-align:top;">: <span>{{ $po->incoterm }}</span></td>
            </tr>
            <tr>
                <td style="text-align: left;vertical-align:top;">PR NO.</td>
                <td style="text-align: left;vertical-align:top;">: <span
                        style=" white-space: pre-wrap;">{!! nl2br($po->no_pr ?? '-') !!}</span></td>
            </tr>
            <tr>
                <td style="text-align: left;vertical-align:top;">Referensi SPH</td>
                <td style="text-align: left;vertical-align:top;">: <span>{{ $po->ref_sph ?? '-' }}</span></td>
            </tr>
            <tr>
                <td
                    style="text-align: left;vertical-align:top;vertical-align:top;"vertical-align:top;vertical-align:top;>
                    No. Justifikasi</td>
                <td style="text-align: left;vertical-align:top;vertical-align:top;">:
                    <span>{{ $po->no_just ?? '-' }}</span>
                </td>
            </tr>
            <tr>
                <td style="text-align: left;vertical-align:top;">No. Negosiasi</td>
                <td style="text-align: left;vertical-align:top;">: <span>{{ $po->no_nego ?? '-' }}</span></td>
            </tr>
            <tr>
                <td style="text-align: left;vertical-align:top;">Batas Akhir Po</td>
                <td style="text-align: left;vertical-align:top;">: <span>{{ $po->batas_po }}</span></td>
            </tr>
            <tr>
                <td style="text-align: left;vertical-align: top;">Alamat Penagihan</td>
                <td style="text-align: left;">: <span> Direktur Keuangan, SDM, dan Manris PT INKA Multi Solusi
                        Servis Jl Salak No. 59 Madiun <br> N.P.W.P : 70.9607.6574.576.5</span></td>
            </tr>
            {{-- </td> --}}
            </tr>

        </table>
        {{-- </div> --}}

        <div style="margin-top:7.3cm;">
            <table class="table2" style="width:100%;padding:10px">
                <tr>
                    <td style="width: 16%">
                        <span>Referensi PO</span><br>
                        <span>Termin Pembayaran</span><br>
                        <span>Garansi</span><br>
                        <span>Proyek</span><br>
                    </td>
                    <td style="width: 1%">
                        <span>:</span><br>
                        <span>:</span><br>
                        <span>:</span><br>
                        <span>:</span><br>
                    </td>
                    <td>
                        <span>{{ $po->ref_po }}</span><br>
                        <span>{{ $po->term_pay }}</span><br>
                        <span>{{ $po->garansi }}</span><br>
                        <span>{{ $po->proyek }}</span><br>
                    </td>
                </tr>
                <tr>
                    <td style="height: 50px;vertical-align: top;">Catatan</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top">{!! nl2br($po->catatan_vendor) !!}</td>
                </tr>
            </table>
        </div>

    </header>
    {{-- <div style="margin-top: 400px"></div> --}}



    <table class="table" style="width: 100%">
        <thead>
            <tr>
                <th>Item</th>
                <th>Kode Material</th>
                <th>Deskripsi</th>
                <th>Batas Akhir Diterima</th>
                <th>Kuantitas</th>
                <th>Unit</th>
                <th>Harga Per Unit</th>
                <th>Mata Uang</th>
                <th>Vat</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($po->details as $item)
                @php
                    $harga_per_unit = $item->harga_per_unit ?? 0;
                @endphp
                @if ($loop->index % 5 == 0 && $loop->index != 0)
        </tbody>
    </table>
    <div class="page_break"></div>
    <table class="table" style="width: 100%">
        <thead>
            <tr>
                <th>Item</th>
                <th>Kode Material</th>
                <th>Deskripsi</th>
                <th>Batas Akhir Diterima</th>
                <th>Kuantitas</th>
                <th>Unit</th>
                <th>Harga Per Unit</th>
                <th>Mata Uang</th>
                <th>Vat</th>
                <th>Total</th>
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
                    {{ $item->batas ? date('d/m/Y', strtotime($item->batas)) : '-' }}
                </td>
                <td>{{ $item->po_qty }}</td>
                <td>{{ $item->satuan }}</td>
                <td>@rupiah($harga_per_unit)</td>
                <td>{{ $item->mata_uang ?? '-' }}</td>
                <td>{{ $item->vat ?? '-' }}</td>
                <td>@rupiah($item->po_qty * $harga_per_unit)</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center" style="text-align: center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- <table class="table" style="width: 100%;">
        <thead>
            <tr>
                <th>Item</th>
                <th>Kode Material</th>
                <th>Deskripsi</th>
                <th>Batas Akhir Diterima</th>
                <th>Kuantitas</th>
                <th>Unit</th>
                <th>Harga Per Unit</th>
                <th>Mata Uang</th>
                <th>Vat</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody id="table-body">
            @forelse ($po->details as $item)
                @php
                    $harga_per_unit = $item->harga_per_unit ?? 0;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>{{ $item->kode_material }}</td>
                    <td>{{ $item->uraian }}</td>
                    <td style="text-align: center;">{{ $item->batas ? date('d/m/Y', strtotime($item->batas)) : '-' }}</td>
                    <td style="text-align: center;">{{ $item->qty }}</td>
                    <td style="text-align: center;">{{ $item->satuan }}</td>
                    <td style="text-align: center;">@rupiah($harga_per_unit)</td>
                    <td style="text-align: center;">{{ $item->mata_uang ?? '-' }}</td>
                    <td style="text-align: center;">{{ $item->vat ?? '-' }}</td>
                    <td style="text-align: center;">@rupiah($item->po_qty * $harga_per_unit)</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table> --}}
    <div class="total" style="margin-left: 70%; width: 50%; page-break-inside: avoid;">
        <table class="w-100">
            <tr>
                <td>Sub Total</td>
                <td>:</td>
                <td>@rupiah($po->subtotal)</td>
            </tr>
            <tr>
                <td>Ongkos Kirim</td>
                <td>:</td>
                <td>@rupiah($po->ongkos)</td>
            </tr>
            <tr>
                <td>Asuransi</td>
                <td>:</td>
                <td>@rupiah($po->asuransi)</td>
            </tr>
            <tr>
                <td>Total</td>
                <td>:</td>
                <td>@rupiah($po->total)</td>
            </tr>
        </table>
    </div>
    {{-- <div class="page-break"></div> --}}



    {{-- <footer>
        <div style="margin-top:1000x">
            <table class="table2" style="width:100%;padding:10px">
                <tr>
                    <td style="width: 16%">
                        <span>Referensi PO</span><br>
                        <span>Termin Pembayaran</span><br>
                        <span>Garansi</span><br>
                        <span>Proyek</span><br>
                    </td>
                    <td style="width: 1%">
                        <span>:</span><br>
                        <span>:</span><br>
                        <span>:</span><br>
                        <span>:</span><br>
                    </td>
                    <td>
                        <span>{{ $po->ref_po }}</span><br>
                        <span>{{ $po->term_pay }}</span><br>
                        <span>{{ $po->garansi }}</span><br>
                        <span>{{ $po->nama_proyek }}</span><br>
                    </td>
                </tr>
                <tr>
                    <td style="height: 50px;vertical-align: top;">Catatan Untuk Vendor</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top">{!! nl2br($po->catatan_vendor) !!}</td>
                </tr>
            </table>
        </div>

    </footer> --}}



    <div style="margin-top: 1rem; page-break-inside: avoid;">
        <div style="float: left; width: 50%">
            <table class="w-100">
                <tr>
                    <td>Disetujui Oleh,</td>
                </tr>
            </table>
        </div>
        <div style="margin-left: 70%; width: 50%; margin-top: 5%">
            <table class="w-100">
                <tr>
                    <td style="text-align: center;" class="text-center"><b>PT INKA MULTI SOLUSI SERVICE</b></td>
                </tr>
                <tr>
                    <td style="height: 80px"></td>
                </tr>
                <tr>
                    {{-- <td class="text-center"><b style="text-decoration: underline">
                            &ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&emsp;&emsp;&emsp;&emsp;</b>
                    </td> --}}
                    {{-- <td style="text-align: center;vertical-align: bottom"><b style="text-decoration: underline; ">
                            @if ($po->total < 25000000)
                                Rudy Susanto
                        </b><br><b>PLT KADEP LOGISTIK</b>
                    @elseif($po->total >= 35000000 && $po->total < 1000000000)
                        Chandra Agung Sasono</b><br><b>DIREKTUR OPERSI</b>
                    @else
                        Adib Ardhian</b><br><b>DIREKTUR UTAMA</b>
                        @endif
                    </td> --}}
                    <td style="text-align: center; vertical-align: bottom">
                        <b style="text-decoration: underline;">
                            @if ($po->total < 25000000)
                                RUDY SUSANTO
                            </b><br><b>KEPALA DEPARTEMEN LOGISTIK</b>
                        @elseif($po->total >= 25000000 && $po->total < 100000000)
                            AMRON BAITARRIZAQ
                            </b><br><b>KEPALA DIVISI TEKNIK DAN LOGISTIK</b>
                        @elseif($po->total >= 100000000 && $po->total < 10000000000000)
                            ADIB ARDHIAN
                            </b><br><b>DIREKTUR UTAMA</b>
                        @endif
                    </td>
                </tr>
                {{-- <tr style="border: 1px solid black; vertical-align: top">
                    <td style="text-align: center;"><b>PLT KADEP LOGISTIK</b></td>
                </tr> --}}
            </table>
        </div>
    </div>

</body>


</html>
