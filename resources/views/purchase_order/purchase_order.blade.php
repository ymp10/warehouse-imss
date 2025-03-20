@extends('layouts.main')
@section('title', __('Purchase Order'))
@section('custom-css')
    <link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <style>
        .modal-dialog {
            overflow-y: initial !important
        }

        .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
    </style>
@endsection
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-po"
                        onclick="addPo()"><i class="fas fa-plus"></i> Add New PO</button>
                    <div class="card-tools">
                        <form>
                            {{-- <div class="input-group input-group">
                                <input type="text" class="form-control" name="q" placeholder="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div> --}}
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">

                        {{-- Filter by Nomor Po dan Tanggal --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter-po-no">Filter Nomor PO</label>
                                    <input type="text" class="form-control" id="filter-po-no"
                                        placeholder="Masukkan Nomor PO">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter-po-date">Filter Tanggal PO</label>
                                    <input type="date" class="form-control" id="filter-po-date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-secondary mt-4" id="clear-filter">Clear Filter</button>
                            </div>
                        </div>
                        {{-- End Filter by Nomor Po dan Tanggal --}}




                        <table id="table" class="table table-sm table-bordered table-hover table-striped">
                            <thead>
                                <tr class="text-center">
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>No.</th>
                                    <th>{{ __('No PO') }}</th>
                                    {{-- <th>{{ __('No PR') }}</th> --}}
                                    <th>{{ __('Proyek') }}</th>
                                    <th>{{ __('Vendor') }}</th>
                                    <th>{{ __('Tanggal PO') }}</th>
                                    <th>{{ __('Batas Akhir PO') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($purchases) > 0)
                                    @foreach ($purchases as $key => $d)
                                        @php
                                            $data = [
                                                'id' => $d->id,
                                                'no' => $purchases->firstItem() + $key,
                                                'vid' => $d->vendor_id,
                                                'nama_vendor' => $d->vendor_name,
                                                'nama_proyek' => $d->proyek_name,
                                                'no_po' => $d->no_po,
                                                'tgpo' => date('d/m/Y', strtotime($d->tanggal_po)),
                                                'btpo' => date('d/m/Y', strtotime($d->batas_po)),
                                                'incoterm' => $d->incoterm,
                                                'pr_no' => $d->pr_no,
                                                'ref_sph' => $d->ref_sph,
                                                'no_just' => $d->no_just,
                                                'no_nego' => $d->no_nego,
                                                'ref_po' => $d->ref_po,
                                                'term_pay' => $d->term_pay,
                                                'garansi' => $d->garansi,
                                                'catatan_vendor' => $d->catatan_vendor,
                                                'ongkos' => $d->ongkos,
                                                'asuransi' => $d->asuransi,
                                                'proyek_id' => $d->proyek_id,
                                                'vendor_id' => $d->vendor_id,
                                                'detail' => $d->detail,
                                                'pr_id' => $d->pr_id,
                                                'pr_no' => $d->pr_no,
                                            ];
                                        @endphp
                                        <tr>
                                            <td class="text-center"><input type="checkbox" name="hapus[]"
                                                    value="{{ $d->id }}"></td>
                                            <td class="text-center">{{ $data['no'] }}</td>
                                            <td>{{ $data['no_po'] }}</td>
                                            {{-- <td>{{ $data['pr_no'] }}</td> --}}
                                            <td class="text-center">{{ $data['nama_proyek'] }}</td>
                                            <td class="text-center">{{ $data['nama_vendor'] }}</td>
                                            <td class="text-center">{{ $data['tgpo'] }}</td>
                                            <td class="text-center">{{ $data['btpo'] }}</td>
                                            <td class="text-center">
                                                <button title="Edit PO" type="button" class="btn btn-success btn-xs"
                                                    data-toggle="modal" data-target="#add-po"
                                                    onclick="editPo({{ json_encode($data) }})"><i
                                                        class="fas fa-edit"></i></button>

                                                <button title="Lihat Detail" type="button" data-toggle="modal"
                                                    data-target="#detail-po" class="btn-lihat btn btn-info btn-xs"
                                                    data-detail="{{ json_encode($data) }}"><i
                                                        class="fas fa-list"></i></button>

                                                @if ((Auth::user() && Auth::user()->role == 0) || Auth::user()->role == 1)
                                                    <button title="Hapus PO" type="button" class="btn btn-danger btn-xs"
                                                        data-toggle="modal" data-target="#delete-po"
                                                        onclick="deletePo({{ json_encode($data) }})"><i
                                                            class="fas fa-trash"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="text-center">
                                        <td colspan="7">{{ __('No data.') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-danger" id="delete-selected"
                            data-token="{{ csrf_token() }}">Hapus yang dipilih</button>
                    </div>
                </div>
            </div>
            <div>
                {{ $purchases->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </section>

    {{-- modal tambah --}}
    <div class="modal fade" id="add-po">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modal-title" class="modal-title">{{ __('Add New PO') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form role="form" id="save" action="{{ route('purchase_order.store') }}" method="post">
                        @csrf
                        <input type="hidden" id="save_id" name="id">
                        <input type="hidden" id="pr_id" name="pr_id">
                        <div class="form-group row">
                            <label for="no_po" class="col-sm-4 col-form-label">{{ __('Nomor PO') }} </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="no_po" name="no_po">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nomor_pr" class="col-sm-4 col-form-label">{{ __('Nomor PR') }} </label>
                            <div class="col-sm-8">
                                <select class="form-control" name="nomor_pr[]" id="nomor_pr">
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="vendor_id" class="col-sm-4 col-form-label">{{ __('Vendor') }} </label>
                            <div class="col-sm-8">
                                {{-- <input type="text" class="form-control" id="vendor_id" name="vendor_id"> --}}
                                <select class="form-control" id="vendor_id" name="vendor_id">
                                    <option value="">Pilih Vendor</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="proyek_id" class="col-sm-4 col-form-label">{{ __('Proyek') }} </label>
                            <div class="col-sm-8">
                                <input type="hidden" id="proyeks" name="proyeks">
                                <select class="form-select" name="proyek_id[]" id="proyek_id" multiple>
                                    {{-- <option>Pilih Proyek</option> --}}
                                    @foreach ($proyeks as $proyek)
                                        <option value="{{ $proyek->id }}">{{ $proyek->nama_pekerjaan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tanggal_po" class="col-sm-4 col-form-label w-50">{{ __('Tanggal PO') }} </label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control w-50" id="tanggal_po" name="tanggal_po">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="batas_po" class="col-sm-4 col-form-label">{{ __('Batas Akhir PO') }} </label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control w-50" id="batas_po" name="batas_po">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="incoterm" class="col-sm-4 col-form-label">{{ __('Incoterm') }} </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="incoterm" name="incoterm">
                            </div>
                        </div>
                        {{-- <div class="form-group row">
    <label for="pr_id" class="col-sm-4 col-form-label">{{ __('PR') }} </label>
    <div class="col-sm-8">
        <!-- Input tersembunyi untuk menyimpan ID PR yang dipilih -->
        <input type="hidden" id="pr_id" name="pr_id" value="">

        <select class="form-control" name="pr_id_select" id="pr_id_select" multiple>
            <option value="">Pilih Purchase Request</option>
            @foreach ($prs as $pr)
                <option value="{{ $pr->id }}">{{ $pr->no_pr }}</option>
            @endforeach
        </select>
    </div>
</div> --}}
                        <div class="form-group row">
                            <label for="ref_sph" class="col-sm-4 col-form-label">{{ __('Referensi SPH') }} </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="ref_sph" name="ref_sph">
                            </div>
                        </div>
                        {{-- <div class="form-group row">
                            <label for="no_just" class="col-sm-4 col-form-label">{{ __('No Justifikasi') }} </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="no_just" name="no_just">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="no_nego" class="col-sm-4 col-form-label">{{ __('No Nego') }} </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="no_nego" name="no_nego">
                            </div>
                        </div> --}}
                        <div class="form-group row">
                            <label for="ref_po" class="col-sm-4 col-form-label">{{ __('Refernsi Po') }} </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="ref_po" name="ref_po">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="term_pay" class="col-sm-4 col-form-label">{{ __('Termin Pembayaran') }} </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="term_pay" name="term_pay">
                                {{-- <select class="form-control" id="term_pay" name="term_pay">
                                    <option value="">Pilih Termin Pembayaran</option>
                                    <option value="0">Cash</option>
                                    <option value="1">Credit</option>
                                </select> --}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="garansi" class="col-sm-4 col-form-label">{{ __('Garansi') }} </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="garansi" name="garansi">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="catatan_vendor" class="col-sm-4 col-form-label">{{ __('Catatan Vendor') }}
                            </label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="catatan_vendor" id="catatan_vendor" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="ongkos" class="col-sm-4 col-form-label">{{ __('Ongkos') }} </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="ongkos" name="ongkos">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="asuransi" class="col-sm-4 col-form-label">{{ __('Asuransi') }} </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="asuransi" name="asuransi">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="no_just" class="col-sm-4 col-form-label">{{ __('No Justifikasi') }} </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="no_just" name="no_just">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="no_nego" class="col-sm-4 col-form-label">{{ __('No Nego') }} </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="no_nego" name="no_nego">
                            </div>
                        </div>


                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button id="button-save" type="button" class="btn btn-primary"
                        onclick="document.getElementById('save').submit();">{{ __('Tambahkan') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal detail --}}
    <div class="modal fade" id="detail-po">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modal-title" class="modal-title">{{ __('Detail Purchase Order') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-12" id="container-form">
                                <form id="cetak-po" method="GET" action="{{ route('cetak_po') }}" target="_blank">
                                    <input type="hidden" name="id_po" id="id_po">
                                </form>
                                <button id="button-cetak-po" type="button" class="btn btn-primary"
                                    onclick="document.getElementById(
                                        'cetak-po').submit();">{{ __('Cetak') }}</button>
                                <table class="align-top w-100">
                                    <tr>
                                        <td style="width: 8%;"><b>ID PR</b></td>
                                        <td style="width:2%">:</td>
                                        <td style="width: 55%"><span id="pr_id2"></span></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 8%;"><b>No Surat</b></td>
                                        <td style="width:2%">:</td>
                                        <td style="width: 55%"><span id="po_no"></span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Proyek</b></td>
                                        <td>:</td>
                                        <td><span id="id_proyek"></span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Vendor</b></td>
                                        <td>:</td>
                                        <td><span id="id_vendor"></span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Tanggal PO</b></td>
                                        <td>:</td>
                                        <td><span id="po_tanggal"></span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Batas PO</b></td>
                                        <td>:</td>
                                        <td><span id="po_batas"></span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Detail</b></td>
                                        <input type="hidden" name="id" id="id">
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <button id="button-tambah-detail" type="button"
                                                class="btn btn-info">{{ __('Tambah Item Detail') }}</button>
                                        </td>
                                    </tr>
                                </table>
                                <div class="table-responsive mt-2">
                                    <table class="table table-bordered">
                                        <thead>
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
                                            <th>Aksi</th>
                                        </thead>

                                        <tbody id="tabel-po">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-0 d-none" id="container-product">
                                <div id="form" class="card">
                                    <div class="card-body">
                                        <!-- <button type="button" class="btn btn-primary mb-3"
                                                    onclick="addToDetails()"></i>Tambah Pilihan</button> -->
                                        <button id="btn-save-then-add" type="button" class="btn btn-primary mb-3">Tambah
                                            Pilihan</button>


                                        {{-- <div class="input-group input-group-lg">
                                            <input type="text" class="form-control" id="proyek_name"
                                                name="proyek_name" placeholder="Search By Proyek">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" id="check-proyek"
                                                    onclick="productCheck()">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div> --}}
                                    </div>
                                    <div class="table-responsive card-body">

                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Pilih</th>
                                                    <th>No</th>
                                                    <th>Deskripsi</th>
                                                    <th>Spesifikasi</th>
                                                    <th>QTY</th>
                                                    <th>QTY</th>
                                                    <th>Sat</th>
                                                    <th>No PR</th>
                                                    <th>No SPPH</th>
                                                    <th>No PO</th>
                                                    <th>Proyek</th>
                                                </tr>
                                            </thead>
                                            <tbody id='detail-material'>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                {{-- <div class="card">
                                    <div class="card-body">
                                        <div class="input-group input-group-lg">
                                            <input type="text" class="form-control" id="pcode" name="pcode"
                                                min="0" placeholder="Product Code">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" id="button-check"
                                                    onclick="productCheck()">
                                                    <i class="fas fa-add"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                <div id="loader" class="card">
                                    <div class="card-body text-center">
                                        <div class="spinner-border text-danger" style="width: 3rem; height: 3rem;"
                                            role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div id="form" class="card">
                                    <div class="card-body">
                                        <form role="form" id="material-update" method="post">
                                            @csrf
                                            <input type="hidden" id="pid" name="pid">
                                            <input type="hidden" id="type" name="type">
                                            <div class="form-group row">
                                                <label for="deskripsi"
                                                    class="col-sm-4 col-form-label">{{ __('Deskripsi Barang') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="deskripsi"
                                                        name="deskripsi">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="batas"
                                                    class="col-sm-4 col-form-label">{{ __('Batas Akhir Diterima') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="date" class="form-control" id="batas"
                                                        name="batas">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="qty"
                                                    class="col-sm-4 col-form-label">{{ __('Kuantitas') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="qty"
                                                        name="qty">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="unit"
                                                    class="col-sm-4 col-form-label">{{ __('Unit') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="unit"
                                                        name="unit">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="hunit"
                                                    class="col-sm-4 col-form-label">{{ __('Harga Per Unit') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="hunit"
                                                        name="hunit">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="mata-uang"
                                                    class="col-sm-4 col-form-label">{{ __('Mata Uang') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="mata-uang"
                                                        name="mata-uang">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="vat"
                                                    class="col-sm-4 col-form-label">{{ __('VAT') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="vat"
                                                        name="vat">
                                                </div>
                                            </div>
                                        </form>
                                        <button id="button-update-sjn" type="button" class="btn btn-primary w-100"
                                            onclick="PoUpdate()">{{ __('Tambahkan') }}</button>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal delete --}}
    <div class="modal fade" id="delete-po">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modal-title" class="modal-title">{{ __('Delete PO') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form role="form" id="delete" action="{{ route('purchase_order.destroy') }}" method="post">
                        @csrf
                        @method('delete')
                        <input type="hidden" id="delete_id" name="id">
                    </form>
                    <div>
                        <p>Anda yakin ingin menghapus purchase order <span id="pcode"
                                class="font-weight-bold"></span>?
                        </p>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Batal') }}</button>
                    <button id="button-save" type="button" class="btn btn-danger"
                        onclick="document.getElementById('delete').submit();">{{ __('Ya, hapus') }}</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('custom-js')
    <script src="/plugins/toastr/toastr.min.js"></script>
    <script src="/plugins/select2/js/select2.full.min.js"></script>
    <script>
        $('#select-all').change(function() {
            var checkboxes = $(this).closest('table').find(':checkbox');
            checkboxes.prop('checked', $(this).is(':checked'));
        });

        // Function to handle delete selected items
        $('#delete-selected').click(function() {
            var ids = [];
            $('input[name="hapus[]"]:checked').each(function() {
                ids.push($(this).val());
            });

            if (ids.length > 0) {
                var token = $(this).data('token');
                $.ajax({
                    url: 'po-imss/hapus-multiple',
                    type: 'POST',
                    data: {
                        _token: token,
                        ids: ids
                    },
                    success: function(response) {
                        if (response.success) {
                            // Menghapus status checked dari semua checkbox
                            $('input[name="hapus[]"]').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            // Memuat ulang halaman setelah berhasil menghapus data
                            location.reload();
                            alert('Data berhasil dihapus');
                        } else {
                            alert('Gagal menghapus data');
                        }
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan saat menghapus data');
                    }
                });
            } else {
                alert('Pilih setidaknya satu item untuk dihapus');
            }
        });

        var selectedDataProyek = [];

        //Filter by Nomor dan tgl PO
        $(document).ready(function() {
            //init multiselect
            // sessionStorage.removeItem('proyek_id');

            // var dataProyekId = JSON.parse(sessionStorage.getItem('proyek_id'));


            $("#proyek_id").val('').trigger('change')

            $('#proyek_id').select2({
                multiple: true,
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
                    'style',
                placeholder: "Pilih Proyek",
                closeOnSelect: false,
            }).on('select2:select', function(e) {
                // var carValue = $("#proyek_id").val();
                var tsValue = e.params.data.id;

                // Jika 'Pilih Semua' dipilih, tambahkan nilai ts sebanyak jumlah koma dalam id
                // if (e.params.data.text === 'Pilih Semua') {
                //     var numberOfCommas = e.params.data.id.split(',').length;
                //     for (var i = 0; i < numberOfCommas; i++) {
                //         trainsetValues.push(tsValue);
                //     }
                // } else {
                selectedDataProyek.push(tsValue);
                // }
                console.log(selectedDataProyek)
                $('#proyeks').val(selectedDataProyek);

                // $("#car3").val(carValue);
                // $("#trainset_kode3").val(trainsetValues);
            })

            // // set and show the previous wrote keywords
            // var dataProyekIdLength = dataProyekId.length;
            // for (var i =0; i < dataProyekIdLength; i++) {
            //     $('#category').val(dataProyekId).trigger('change');

            // }

            // var selectedData = [];
            //         var trainsetValues = []; // Array untuk menyimpan nilai ts

            //         $("#proyek_id").select2({
            //             multiple: true,
            //             closeOnSelect: false,
            //             dropdownAutoHeight: true,
            //         }).on('select2:select', function(e) {
            //             var carValue = $("#proyek_id").val();
            //             var tsValue = e.params.data.ts;

            //             // Jika 'Pilih Semua' dipilih, tambahkan nilai ts sebanyak jumlah koma dalam id
            //             if (e.params.data.text === 'Pilih Semua') {
            //                 var numberOfCommas = e.params.data.id.split(',').length;
            //                 for (var i = 0; i < numberOfCommas; i++) {
            //                     trainsetValues.push(tsValue);
            //                 }
            //             } else {
            //                 trainsetValues.push(tsValue);
            //             }

            //             $("#car3").val(carValue);
            //             $("#trainset_kode3").val(trainsetValues);
            //         }).on('select2:unselect', function(e) {
            //             var tsValue = e.params.data.ts;
            //             var carValue = $("#car").val();
            //             $("#car3").val(carValue);
            //             // Temukan indeks pertama dari nilai ts yang cocok dan hapus hanya satu nilai
            //             var index = trainsetValues.indexOf(tsValue);
            //             if (index !== -1) {
            //                 trainsetValues.splice(index, 1);
            //             }
            //             $("#trainset_kode3").val(trainsetValues);
            //         });

            $('#clear-filter').on('click', function() {
                $('#filter-po-no, #filter-po-date').val('');
                filterTable();
            });

            $('#filter-po-no, #filter-po-date').on('keyup change', function() {
                filterTable();
            });

            function filterTable() {
                var filterNoPO = $('#filter-po-no').val().toUpperCase();
                var filterDatePO = $('#filter-po-date').val();

                $('table tbody tr').each(function() {
                    var noPO = $(this).find('td:nth-child(3)').text().toUpperCase();
                    var datePO = $(this).find('td:nth-child(6)').text();
                    var id = $(this).find('td:nth-child(1)')
                        .text(); // Ubah indeks kolom ke indeks ID PO jika perlu

                    // Ubah string tanggal ke objek Date untuk perbandingan
                    var dateParts = datePO.split("/");
                    var poDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[
                        0]); // Format: tahun, bulan, tanggal

                    // Ubah string filterDatePO ke objek Date
                    var filterDateParts = filterDatePO.split("-");
                    var filterPODate = new Date(filterDateParts[0], filterDateParts[1] - 1, filterDateParts[
                        2]); // Format: tahun, bulan, tanggal

                    if ((noPO.indexOf(filterNoPO) > -1 || filterNoPO === '') &&
                        (poDate.getTime() === filterPODate.getTime() || filterDatePO === '')) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }



            let selectedDataArray = []; // Array untuk menyimpan ID yang dipilih

            $("#nomor_pr").select2({
                placeholder: 'Pilih Tempat',
                width: '100%',
                data: [{
                    id: 'all',
                    text: 'Semua'
                }],
                multiple: true, // Menambahkan properti multiple untuk mendukung banyak pilihan
                ajax: {
                    url: "{{ route('nopr.index') }}",
                    processResults: function({
                        data
                    }) {
                        // Menggabungkan opsi "Semua" dengan data dari database
                        let results = $.map(data, function(item) {
                            return {
                                id: item.no_pr,
                                ids: item.id, // ID sebenarnya
                                text: item.no_pr // Teks untuk dropdown
                            };
                        });
                        return {
                            results: results
                        };
                    }
                }
            });

            // Saat opsi dipilih
            $('#nomor_pr').on('select2:select', function(e) {
                let selectedData = e.params.data;

                // Tambahkan ID ke array jika belum ada
                if (!selectedDataArray.includes(selectedData.ids)) {
                    selectedDataArray.push(selectedData.ids);
                }

                // Update input tersembunyi dengan string ID dipisahkan koma
                $("#pr_id").val(selectedDataArray.join(','));
                console.log("Selected IDs (String):", selectedDataArray.join(','));
            });

            // Saat opsi batal dipilih
            $('#nomor_pr').on('select2:unselect', function(e) {
                let unselectedData = e.params.data;

                // Hapus ID dari array
                selectedDataArray = selectedDataArray.filter(id => id !== unselectedData.ids);

                // Update input tersembunyi dengan string ID dipisahkan koma
                $("#pr_id").val(selectedDataArray.join(','));
                console.log("Updated Selected IDs (String):", selectedDataArray.join(','));
            });

        });
        //End Filter by Nomor dan tgl PO




        function resetForm() {
            $('#save').trigger("reset");
            //remove the selected select option all
            $('#vendor_id').find('option').each(function() {
                $(this).attr('selected', false);
            });
            $('#pr_id').find('option').each(function() {
                $(this).attr('selected', false);
            });
            $('#proyek_id').find('option').each(function() {
                $(this).attr('selected', false);
            });
            $('#barcode_preview_container').hide();
        }

        function addPo() {
            $('#modal-title').text("Add Purchase Order");
            $('#button-save').text("Tambahkan");
            resetForm();
        }


        function loader(status = 1) {
            if (status == 1) {
                $('#loader').show();
            } else {
                $('#loader').hide();
            }
        }

        function emptyTablePo() {
            $('#tabel-po').empty();
            $('#po_tanggal').text("");
            $('#po_batas').text("");
            $('#po_no').text("");
            $('#id_proyek').text("");
            $('#id_vendor').text("");

        }

        function editPo(data) {
            console.log(data);
            $('#modal-title').text("Edit PO");
            $('#button-save').text("Simpan");
            resetForm();
            $('#save_id').val(data.id);
            $('#no_po').val(data.no_po);
            $('#vendor_id').val(data.vendor_id);
            $('#vendor_id').find('option').each(function() {
                if ($(this).val() == data.vid) {
                    console.log($(this).val());
                    $(this).attr('selected', true);
                } else {
                    $(this).attr('selected', false);
                }
            });
            var date = data.tgpo.split('/');
            var newDate = date[2] + '-' + date[1] + '-' + date[0];
            $('#tanggal_po').val(newDate);
            var date = data.btpo.split('/');
            var newDate = date[2] + '-' + date[1] + '-' + date[0];
            $('#batas_po').val(newDate);
            $('#incoterm').val(data.incoterm);
            $('#pr_id').find('option').each(function() {
                if ($(this).val() == data.pr_id) {
                    console.log('pr', $(this).val());
                    $(this).attr('selected', true);
                } else {
                    $(this).attr('selected', false);
                }
            });
            $('#ref_sph').val(data.ref_sph);
            $('#no_just').val(data.no_just);
            $('#no_nego').val(data.no_nego);
            $('#ref_po').val(data.ref_po);
            $('#term_pay').val(data.term_pay);
            $('#garansi').val(data.garansi);
            // $('#proyek_id').find('option').each(function() {
            //     if ($(this).val() == data.proyek_id) {
            //         console.log('proyek', $(this).val());
            //         $(this).attr('selected', true);
            //     } else {
            //         $(this).attr('selected', false);
            //     }
            // });
            const valProyeks = data.proyek_id
            const split = valProyeks.split(',')
            split.forEach(function(item) {
                selectedDataProyek.push(item)
            })
            $("#proyek_id").val(split).change();
            $('#catatan_vendor').val(data.catatan_vendor);
            $('#ongkos').val(data.ongkos);
            $('#asuransi').val(data.asuransi);


            // Ambil `no_pr` berdasarkan `pr_id` dari database
            $.ajax({
                url: "{{ route('nopr.getByIds') }}", // Pastikan route ini dibuat di backend untuk mengembalikan daftar no_pr
                type: "GET",
                data: {
                    pr_ids: data.pr_id
                },
                success: function(response) {
                    let prOptions = response.map(item => ({
                        id: item.id,
                        text: item.no_pr
                    }));

                    $('#nomor_pr').empty().select2({
                        placeholder: 'Pilih Nomor PR',
                        width: '100%',
                        multiple: true,
                        data: prOptions,
                        ajax: {
                            url: "{{ route('nopr.index') }}",
                            processResults: function({
                                data
                            }) {
                                return {
                                    results: $.map(data, function(item) {
                                        return {
                                            id: item.id,
                                            text: item.no_pr
                                        };
                                    })
                                };
                            }
                        }
                    });

                    // Set nilai yang sudah dipilih
                    let selectedIds = prOptions.map(item => item.id);
                    $('#nomor_pr').val(selectedIds).trigger('change');

                    // Simpan ke dalam array
                    selectedDataArray = [...selectedIds];
                    $("#pr_id").val(selectedDataArray.join(','));
                },
                error: function(xhr) {
                    console.log("Gagal mengambil data PR", xhr);
                }
            });



        }






        $('#detail-po').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var data = button.data('detail');
            console.log('d', data);
            lihatPo(data);
        });



        function lihatPo(data) {
            console.log('PRRRR', data.pr_id)
            emptyTablePo();
            const pr = data.pr_id.split(',').map(item => item.trim());
            $('modal-title').text("Detail PO");
            $('#button-save').text("Simpan");
            resetForm();
            // $('#pr_id2').text(data.pr_id);
            $('#pr_id2').text(data.pr_no);
            $('#button-tambah-detail').val(data.pr_id);
            $('#button-tambah-detail').attr('onclick', `showAddItem(${data.pr_id}); getPODetail(${JSON.stringify(pr)});`);
            $('#po_no').text(data.no_po);
            $('#id_proyek').text(data.proyek_name);
            $('#id_vendor').text(data.vendor_name);
            $('#po_tanggal').text(data.tgpo);
            $('#po_batas').text(data.btpo);
            $('#tabel-po').empty();
            console.log(data);

            $.ajax({
                url: "{{ url('products/purchase_order_detail') }}" + "/" + data.id,
                type: "GET",
                data: {
                    id: data.id
                },
                dataType: "json",
                beforeSend: function() {
                    $('#tabel-po').append('<tr><td colspan="11" class="text-center">Loading...</td></tr>');
                    $('#button-cetak-po').html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                    );
                    $('#button-cetak-po').attr('disabled', true);
                },

                success: function(data) {
                    console.log('f', data);
                    $('#no_po').text(data.po.no_po);
                    $('#id_proyek').text(data.po.nama_proyek);
                    $('#id_vendor').text(data.po.nama_vendor);
                    $('#po_tanggal').text(data.po.tgpo);
                    $('#po_batas').text(data.po.btpo);
                    $('#id_po').val(data.po.id);
                    $('#button-cetak-po').html('<i class="fas fa-print"></i> Cetak');
                    $('#button-cetak-po').attr('disabled', false);
                    var no = 1;
                    var id_po = data.po.id;

                    if (data?.po?.details?.length == 0) {
                        $('#tabel-po').append(
                            '<tr><td colspan="11" class="text-center">Tidak ada data</td></tr>');
                    } else {
                        $.each(data?.po?.details, function(index, value) {
                            var id = value.id_detail_po;
                            var id_detail_pr = value.id
                            var kode_material = value.kode_material;
                            var deskripsi = value.uraian;
                            var batas = value.batas ?? '-';
                            var date = value.batas_po?.split('/') ?? '-';
                            // var newDate = date[2] + '/' + date[1] + '/' + date[0];
                            var newDate = batas;
                            var po_qty = value.po_qty;
                            // var total = value.qty * value.harga_per_unit ?? 0;
                            var satuan = value.satuan;
                            var harga_per_unit = value.harga_per_unit ?? 0;
                            var mata_uang = value.mata_uang ?? '-';
                            var vat = value.vat ?? '-';
                            var total = po_qty * harga_per_unit;
                            console.log({
                                kode_material,
                                deskripsi,
                                batas,
                                newDate,
                                po_qty,
                                total,
                                vat,
                                satuan,
                                harga_per_unit,
                                mata_uang,
                                id_detail_pr,
                            })
                            var html = '<tr>' +
                                '<td>' + no + '</td>' +
                                '<td>' + kode_material + '</td>' +
                                '<td>' + deskripsi + '</td>' +
                                '<td><input type="date" value="' + newDate +
                                '" class="form-control" id="batas' + id + '" name="batas' + id +
                                '"></td>' +
                                '<td>' + po_qty + '</td>' +
                                '<td>' + satuan + '</td>' +
                                '<td><input type="text" value="' + harga_per_unit +
                                '" class="form-control" id="harga_per_unit' + id +
                                '" name="harga_per_unit' + id + '"></td>' +
                                '<td><input type="text" value="' + mata_uang +
                                '" class="form-control" id="mata_uang' + id + '" name="mata_uang' + id +
                                '"></td>' +
                                '<td><input type="text" value="' + vat +
                                '" class="form-control" id="vat' + id + '" name="vat' + id + '"></td>' +
                                '<td>' + total + '</td>' +
                                '<td><button title="simpan" id="edit_po_save" type="button" class="btn btn-success btn-xs" data-id="' +
                                id + '" data-idpo="' + id_po + '" ><i class="fas fa-save"></i>' +
                                '</button>' +
                                '<button title="hapus" id="delete_po_save" type="button" class="btn btn-danger btn-xs" data-id="' +
                                id +
                                '" data-idpo="' + id_po + '" data-id_detail_pr="' + id_detail_pr +
                                '" ><i class="fas fa-trash"></i>' +
                                '</button>' +
                                '</tr>';
                            $('#tabel-po').append(html);
                            no++;
                        });
                    }
                    //remove loading
                    $('#tabel-po').find('tr:first').remove();
                }
            })

        }

        //action edit_po_save
        $(document).on('click', '#edit_po_save', function() {
            var id = $(this).data('id');
            var id_po = $(this).data('idpo');
            //get the batas{id} input
            var batas = $('#batas' + id).val();
            var harga_per_unit = $('#harga_per_unit' + id).val();
            var mata_uang = $('#mata_uang' + id).val();
            var vat = $('#vat' + id).val();
            var form = {
                id,
                id_po,
                batas,
                harga_per_unit,
                mata_uang,
                vat
            };

            console.log(form);
            $('#tabel-po').empty();

            //ajax post to products/detail_pr_save

            $.ajax({
                url: "{{ route('detail_po_save') }}",
                type: "POST",
                data: {
                    id,
                    id_po,
                    batas,
                    harga_per_unit,
                    mata_uang,
                    vat,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                beforeSend: function() {
                    $('#tabel-po').append(
                        '<tr><td colspan="11" class="text-center">Loading...</td></tr>');
                    $('#button-cetak-po').html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                    );
                    $('#button-cetak-po').attr('disabled', true);
                },
                success: function(data) {
                    console.log(data);
                    $('#no_po').text(data.po.no_po);
                    $('#id_proyek').text(data.po.nama_proyek);
                    $('#id_vendor').text(data.po.nama_vendor);
                    $('#po_tanggal').text(data.po.tgpo);
                    $('#po_batas').text(data.po.btpo);
                    $('#id_po').val(data.po.id);
                    $('#button-cetak-po').html('<i class="fas fa-print"></i> Cetak');
                    $('#button-cetak-po').attr('disabled', false);
                    var no = 1;
                    var id_po = data.po.id;

                    if (data?.po?.details?.length == 0) {
                        $('#tabel-po').append(
                            '<tr><td colspan="11" class="text-center">Tidak ada data</td></tr>');
                    } else {
                        $.each(data?.po?.details, function(index, value) {
                            var id = value.id_detail_po;
                            var id_detail_pr = value.id
                            var kode_material = value.kode_material;
                            var deskripsi = value.uraian;
                            var batas = value.batas ?? '-';
                            var date = value.batas_po?.split('/') ?? '-';
                            // var newDate = date[2] + '/' + date[1] + '/' + date[0];
                            var newDate = batas;
                            var po_qty = value.po_qty;
                            // var total = value.qty * value.harga_per_unit ?? 0;
                            var satuan = value.satuan;
                            var harga_per_unit = value.harga_per_unit ?? 0;
                            var mata_uang = value.mata_uang ?? '-';
                            var vat = value.vat ?? '-';
                            var total = po_qty * harga_per_unit;
                            console.log({
                                kode_material,
                                deskripsi,
                                batas,
                                newDate,
                                po_qty,
                                total,
                                vat,
                                satuan,
                                harga_per_unit,
                                mata_uang,
                                id_detail_pr,
                            })
                            var html = '<tr>' +
                                '<td>' + no + '</td>' +
                                '<td>' + kode_material + '</td>' +
                                '<td>' + deskripsi + '</td>' +
                                '<td><input type="date" value="' + newDate +
                                '" class="form-control" id="batas' + id + '" name="batas' + id +
                                '"></td>' +
                                '<td>' + po_qty + '</td>' +
                                '<td>' + satuan + '</td>' +
                                '<td><input type="text" value="' + harga_per_unit +
                                '" class="form-control" id="harga_per_unit' + id +
                                '" name="harga_per_unit' + id + '"></td>' +
                                '<td><input type="text" value="' + mata_uang +
                                '" class="form-control" id="mata_uang' + id +
                                '" name="mata_uang' + id +
                                '"></td>' +
                                '<td><input type="text" value="' + vat +
                                '" class="form-control" id="vat' + id + '" name="vat' + id +
                                '"></td>' +
                                '<td>' + total + '</td>' +
                                '<td><button title="simpan" id="edit_po_save" type="button" class="btn btn-success btn-xs" data-id="' +
                                id + '" data-idpo="' + id_po +
                                '" ><i class="fas fa-save"></i>' +
                                '</button>' +
                                '<button title="hapus" id="delete_po_save" type="button" class="btn btn-danger btn-xs" data-id="' +
                                id +
                                '" data-idpo="' + id_po + '" data-id_detail_pr="' +
                                id_detail_pr + '" ><i class="fas fa-trash"></i>' +
                                '</button>' +
                                '</tr>';
                            $('#tabel-po').append(html);
                            no++;
                        });
                    }
                    //remove loading
                    $('#tabel-po').find('tr:first').remove();
                }
            })

        });



        //action delete_po_save
        $(document).on('click', '#delete_po_save', function() {
            var id = $(this).data('id');
            var no_po = $(this).data('no_po');
            var id_po = $(this).data('idpo');
            var id_detail_pr = $(this).data('id_detail_pr');
            //get the batas{id} input
            var batas = $('#batas' + id).val();
            var harga_per_unit = $('#harga_per_unit' + id).val();
            var mata_uang = $('#mata_uang' + id).val();
            var vat = $('#vat' + id).val();
            var form = {
                id,
                id_po,
                batas,
                harga_per_unit,
                mata_uang,
                vat,
                id_detail_pr
            };

            console.log(form);
            $('#tabel-po').empty();

            $.ajax({
                url: "{{ route('detail_po_delete') }}",
                type: "DELETE",
                data: {
                    id,
                    id_po,
                    batas,
                    harga_per_unit,
                    mata_uang,
                    vat,
                    id_detail_pr,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                beforeSend: function() {
                    $('#tabel-po').append(
                        '<tr><td colspan="11" class="text-center">Loading...</td></tr>');
                    $('#button-cetak-po').html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                    );
                    $('#button-cetak-po').attr('disabled', true);
                },
                success: function(data) {
                    console.log(data);
                    // $('#no_po').text(data.po.no_po);
                    // $('#id_proyek').text(data.po.nama_proyek);
                    // $('#id_vendor').text(data.po.nama_vendor);
                    // $('#po_tanggal').text(data.po.tgpo);
                    // $('#po_batas').text(data.po.btpo);
                    // $('#id_po').val(data.po.id);
                    $('#button-cetak-po').html('<i class="fas fa-print"></i> Cetak');
                    $('#button-cetak-po').attr('disabled', false);
                    $('#detail-po').find('#container-product').addClass('d-none');
                    $('#detail-po').find('#container-product').removeClass('col-5');
                    $('#detail-po').find('#container-form').addClass('col-12');
                    $('#detail-po').find('#container-form').removeClass('col-7');
                    $('#button-tambah-detail').text('Tambah Item Detail');
                    var no = 1;
                    // var id_po = data.po.id;

                    if (data?.po?.details?.length == 0 || data?.po?.details == null) {
                        $('#tabel-po').append(
                            '<tr><td colspan="11" class="text-center">Tidak ada data</td></tr>');
                    } else {
                        $.each(data?.po?.details, function(index, value) {
                            var id = value.id_detail_po;
                            var id_detail_pr = value.id
                            var kode_material = value.kode_material;
                            var deskripsi = value.uraian;
                            var batas = value.batas ?? '-';
                            var date = value.batas_po?.split('/') ?? '-';
                            // var newDate = date[2] + '/' + date[1] + '/' + date[0];
                            var newDate = batas;
                            var po_qty = value.po_qty;
                            // var total = value.qty * value.harga_per_unit ?? 0;
                            var satuan = value.satuan;
                            var harga_per_unit = value.harga_per_unit ?? 0;
                            var mata_uang = value.mata_uang ?? '-';
                            var vat = value.vat ?? '-';
                            var total = po_qty * harga_per_unit;
                            console.log({
                                kode_material,
                                deskripsi,
                                batas,
                                newDate,
                                po_qty,
                                total,
                                vat,
                                satuan,
                                harga_per_unit,
                                mata_uang,
                                id_detail_pr,
                            })
                            var html = '<tr>' +
                                '<td>' + no + '</td>' +
                                '<td>' + kode_material + '</td>' +
                                '<td>' + deskripsi + '</td>' +
                                '<td><input type="date" value="' + newDate +
                                '" class="form-control" id="batas' + id + '" name="batas' + id +
                                '"></td>' +
                                '<td>' + po_qty + '</td>' +
                                '<td>' + satuan + '</td>' +
                                '<td><input type="text" value="' + harga_per_unit +
                                '" class="form-control" id="harga_per_unit' + id +
                                '" name="harga_per_unit' + id + '"></td>' +
                                '<td><input type="text" value="' + mata_uang +
                                '" class="form-control" id="mata_uang' + id +
                                '" name="mata_uang' + id +
                                '"></td>' +
                                '<td><input type="text" value="' + vat +
                                '" class="form-control" id="vat' + id + '" name="vat' + id +
                                '"></td>' +
                                '<td>' + total + '</td>' +
                                '<td><button title="simpan" id="edit_po_save" type="button" class="btn btn-success btn-xs" data-id="' +
                                id + '" data-idpo="' + id_po +
                                '" ><i class="fas fa-save"></i>' +
                                '</button>' +
                                '<button title="hapus" id="delete_po_save" type="button" class="btn btn-danger btn-xs" data-id="' +
                                id +
                                '" data-idpo="' + id_po + '" data-id_detail_pr="' +
                                id_detail_pr + '" ><i class="fas fa-trash"></i>' +
                                '</button>' +
                                '</tr>';
                            $('#tabel-po').append(html);
                            no++;
                        });
                    }
                    //remove loading
                    $('#tabel-po').find('tr:first').remove();
                }
            })

        });

        $('#detail-po').on('hidden.bs.modal', function() {
            $('#container-product').addClass('d-none');
            $('#container-product').removeClass('col-4');
            $('#container-form').addClass('col-12');
            $('#container-form').removeClass('col-8');
            $('#button-tambah-detail').text('Tambah Item Detail');
        });

        function showAddItem(pr_id) {
            //detect #detail-po where id container-product has class d-none
            if ($('#detail-po').find('#container-product').hasClass('d-none')) {
                $('#detail-po').find('#container-product').removeClass('d-none');
                $('#detail-po').find('#container-product').addClass('col-5');
                $('#detail-po').find('#container-form').removeClass('col-12');
                $('#detail-po').find('#container-form').addClass('col-7');
                $('#button-tambah-detail').text('Kembali');
            } else {
                $('#detail-po').find('#container-product').addClass('d-none');
                $('#detail-po').find('#container-product').removeClass('col-5');
                $('#detail-po').find('#container-form').addClass('col-12');
                $('#detail-po').find('#container-form').removeClass('col-7');
                $('#button-tambah-detail').text('Tambah Item Detail');
                $('#proyek_name').val("");
            }

            // getPODetail();
        }

        // function getPODetail() {

        //     loader();
        //     $('#button-check').prop("disabled", true);
        //     $.ajax({
        //         url: "{{ url('products/products_pr') }}",
        //         type: "GET",
        //         data: {
        //             "format": "json"
        //         },
        //         dataType: "json",
        //         beforeSend: function() {
        //             $('#loader').show();
        //             $('#form').hide();
        //         },
        //         success: function(data) {
        //             loader(0);
        //             $('#form').show();
        //             //append to #detail-material
        //             $('#detail-material').empty();
        //             $.each(data.products, function(key, value) {
        //                 console.table('a', value)
        //                 var no_spph
        //                 if (!value.id_spph) {
        //                     no_spph = '-'
        //                 } else {
        //                     no_spph = value.nomor_spph
        //                 }

        //                 var no_pr
        //                 if (!value.id_pr) {
        //                     no_pr = '-'
        //                 } else {
        //                     no_pr = value.pr_no
        //                 }

        //                 var no_po
        //                 if (!value.id_po) {
        //                     no_po = '-'
        //                 } else {
        //                     no_po = value.po_no
        //                 }

        //                 var checkbox
        //                 if (value.id_spph && !value.id_po) {
        //                     checkbox = '<input type="checkbox" id="addToDetails" value="' + value.id +
        //                         '" onclick="addToDetailsJS(' + value.id + ')" >'
        //                 } else {
        //                     checkbox = '<input type="checkbox" id="addToDetails" value="' + value.id +
        //                         '" onclick="addToDetailsJS(' + value.id + ')" disabled>'
        //                 }


        //                 $('#detail-material').append(
        //                     '<tr><td>' + checkbox + '</td><td>' + (key + 1) + '</td><td>' + value.uraian +
        //                     '</td><td>' + value.spek + '</td><td>' + value.qty + '</td><td>' + value
        //                     .satuan + '</td><td>' + value.nama_proyek + '</td><td>' + no_spph +
        //                     '</td><td>' + no_pr + '</td><td>' +
        //                     no_po + '</td></tr>'
        //                 );
        //             });
        //         },
        //         error: function() {
        //             $('#pcode').prop("disabled", false);
        //             $('#button-check').prop("disabled", false);
        //         }
        //     });
        // // }

        // Mengaktifkan tombol jika ada checkbox yang dicentang
        $(document).on('change', '.row-checkbox', function() {
            let anyChecked = $('.row-checkbox:checked').length > 0;
            $('#btn-save-then-add').prop('disabled', !anyChecked);
        });

        $('#btn-save-then-add').on('click', function() {
            var dataToSend = [];
            var selectedRows = 0; // Hitung jumlah baris yang dicentang

            $('#detail-material tr').each(function() { // Loop semua baris
                var $row = $(this);
                var id = $row.data('id');
                var qty_po1 = $row.find('.qty_po1-input').val();
                var isChecked = $row.find('.row-checkbox').prop('checked'); // Cek checkbox

                // if (isChecked) {
                //     selectedRows++; // Hitung jumlah yang dicentang
                //     if (qty_po1 !== '' && !isNaN(qty_po1)) { // Pastikan qty2 valid
                //         dataToSend.push({
                //             id: id,
                //             qty_po1: qty_po1
                //         });
                //     }
                // }

                if (isChecked) {
                    selectedRows++; // Hitung jumlah yang dicentang
                    if (qty_po1 !== '' && !isNaN(qty_po1)) { // Pastikan qty2 valid
                        dataToSend.push({
                            id: id,
                            qty_po1: qty_po1,
                            id_po: $('#id_po').val()
                        });
                    }
                }




            });

            if (selectedRows === 0) { // Jika tidak ada checkbox yang dicentang
                alert('Pilih minimal 1 baris untuk disimpan!');
                return;
            }

            if (dataToSend.length === 0) {
                alert('Pastikan qty2 terisi dengan angka yang valid!');
                return;
            }

            //     // Kirim ke server
            //     $.ajax({
            //         url: "{{ route('qty_po_save') }}",
            //         type: "POST",
            //         data: {
            //             id: id,
            //             qty_po1: qty_po1,
            //             _token: '{{ csrf_token() }}'
            //         },
            //         dataType: "json",
            //         beforeSend: function() {
            //             $('#btn-save-then-add').prop('disabled', true).text('Menyimpan...');
            //         },
            //         success: function(response) {
            //             if (response.success) {
            //                 alert('Data berhasil disimpan!');
            //                 addToDetails(); // Setelah berhasil, tambah baris baru
            //             } else {
            //                 alert('Gagal menyimpan data');
            //             }
            //             $('#btn-save-then-add').prop('disabled', false).text('Tambah Pilihan');
            //         },
            //         error: function(xhr) {
            //             alert('Terjadi kesalahan saat menyimpan data!');
            //             console.log(xhr.responseText);
            //             $('#btn-save-then-add').prop('disabled', false).text('Tambah Pilihan');
            //         }
            //     });
            // });
            $.ajax({
                url: "{{ route('qty_po_save') }}", // Sesuaikan dengan route
                type: "POST",
                data: {
                    data: dataToSend,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                beforeSend: function() {
                    $('#btn-save-then-add').prop('disabled', true).text('Menyimpan...');
                },
                success: function(response) {
                    if (response.success) {
                        alert('Data berhasil disimpan!');

                        // Nonaktifkan checkbox setelah sukses
                        $('#detail-material tr').each(function() {
                            var $row = $(this);
                            if ($row.find('.row-checkbox').prop('checked')) {
                                $row.find('.row-checkbox').prop('disabled',
                                    true); // Tidak bisa dicentang ulang
                            }
                        });

                        addToDetails(); // Tambah baris baru
                    } else {
                        alert('Gagal menyimpan data');
                    }
                    $('#btn-save-then-add').prop('disabled', false).text('Tambah Pilihan');
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menyimpan data!');
                    console.log(xhr.responseText);
                    $('#btn-save-then-add').prop('disabled', false).text('Tambah Pilihan');
                }
            });
        });

        //logika untuk menghitung otomatis
        $(document).on('input', '.qty_po1-input', function() {
            var $row = $(this).closest('tr');
            var qtyPoCell = $row.find('td:eq(4)');
            var initialQtyPo = parseFloat(qtyPoCell.data('original-qty')) || 0;
            var inputQty_po1 = parseFloat($(this).val()) || 0;

            if (inputQty_po1 > initialQtyPo) {
                alert("Qty tidak boleh lebih besar dari Qty");
                $(this).val(initialQtyPo);
                inputQty_po1 = initialQtyPo;
            }

            var newQtyPo = initialQtyPo - inputQty_po1;

            qtyPoCell.text(newQtyPo);
        });

        function getPODetail(pr_id) {
            console.log("PRRRR XXX", pr_id)
            // alert(pr_id);
            loader();

            $('#button-check').prop("disabled", true);
            $.ajax({
                url: "{{ url('products/products_pr_po/') }}/" + pr_id,
                type: "GET",
                data: {
                    "format": "json"
                },
                dataType: "json",
                beforeSend: function() {
                    $('#loader').show();
                    $('#form').hide();
                },
                success: function(data) {
                    loader(0);
                    $('#form').show();
                    //append to #detail-material
                    $('#detail-material').empty();
                    $('#btn-save-then-add').prop('disabled', true);
                    $.each(data.products, function(key, value) {
                        console.log(value);
                        var no_spph
                        if (!value.id_spph) {
                            no_spph = '-'
                        } else {
                            no_spph = value.nomor_spph
                        }

                        var no_pr
                        if (!value.pr_no) {
                            no_pr = '-'
                        } else {
                            no_pr = value.pr_no
                        }
                        var no_po
                        if (!value.p_no) {
                            no_po = '-'
                        } else {
                            no_po = value.po_no
                        }

                        var checkbox;
                        if (value.qty_po && value.qty_po > 0) {
                            checkbox = '<input type="checkbox" id="addToDetails-' + value.id +
                                '" class="row-checkbox" value="' + value.id +
                                '" onclick="addToDetailsJS(' + value.id + ')">';
                        } else {
                            checkbox = '<input type="checkbox" id="addToDetails-' + value.id +
                                '" class="row-checkbox" value="' + value.id +
                                '" onclick="addToDetailsJS(' + value.id + ')" disabled>';
                        }



                        $('#detail-material').append(
                            '<tr id="row-' + key + '" data-id="' + value.id + '">' +
                            '<td>' + checkbox + '</td>' +
                            '<td>' + (key + 1) + '</td>' +
                            '<td>' + value.uraian + '</td>' +
                            '<td>' + value.spek + '</td>' +
                            '<td data-original-qty="' + value.qty_po + '">' + value.qty_po +
                            // '<td><input type="text" class="form-control qty_po1-input" style="width: 50px;" value="' + value.qty_po1 + '" data-qty="' + value.qty_po1 + '"></td>' +
                            '<td>' +
                            '<div style="display: block;">' +
                            // Menggunakan block untuk menata vertikal
                            '<input type="text" class="form-control qty_po1-input" style="width: 50px;" value="' +
                            value.qty_po1 + '" data-qty="' + value.qty_po1 + '">' +


                            '</td>' +
                            '<td>' + value.satuan + '</td>' +
                            '<td>' + no_pr + '</td>' +
                            '<td>' + no_spph + '</td>' +
                            '<td>' + no_po + '</td>' +
                            '<td>' + value.nama_pekerjaan + '</td>' +
                            '</tr>'
                        );

                    });
                },
                error: function() {
                    $('#pcode').prop("disabled", false);
                    $('#button-check').prop("disabled", false);
                }
            });
        }

        var selected = []

        function addToDetailsJS(id) {
            if (selected.includes(id)) {
                selected = selected.filter(item => item !== id)
            } else {
                selected.push(id)
            }
            console.log(selected)
        }

        function addToDetails() {
            $.ajax({
                url: "{{ url('products/tambah_detail_po') }}",
                type: "POST",
                data: {
                    "id_po": $('#id_po').val(),
                    "selected": selected,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: "json",
                beforeSend: function() {
                    $('#loader').show();
                    $('#form').hide();
                },
                success: function(data) {
                    var pr_id = data.po.pr_id;
                    getPODetail(pr_id);
                    console.log(data);
                    $('#no_po').text(data.po.no_po);
                    $('#id_proyek').text(data.po.nama_proyek);
                    $('#id_vendor').text(data.po.nama_vendor);
                    $('#po_tanggal').text(data.po.tgpo);
                    $('#po_batas').text(data.po.btpo);
                    $('#id_po').val(data.po.id_po);
                    $('#button-cetak-po').html('<i class="fas fa-print"></i> Cetak');
                    $('#button-cetak-po').attr('disabled', false);
                    $('#tabel-po').empty();
                    var no = 1;
                    var id_po = data.po.id_po;
                    selected = [];

                    if (data?.po?.details?.length == 0) {
                        $('#tabel-po').append(
                            '<tr><td colspan="11" class="text-center">Tidak ada data</td></tr>');
                    } else {
                        $.each(data?.po?.details, function(index, value) {
                            var id = value.id_detail_po;
                            var id_detail_pr = value.id
                            var kode_material = value.kode_material;
                            var deskripsi = value.uraian;
                            var batas = value.batas ?? '-';
                            var date = value.batas_po?.split('/') ?? '-';
                            // var newDate = date[2] + '/' + date[1] + '/' + date[0];
                            var newDate = batas;
                            var po_qty = value.po_qty;
                            // var total = value.qty * value.harga_per_unit ?? 0;
                            var satuan = value.satuan;
                            var harga_per_unit = value.harga_per_unit ?? 0;
                            var mata_uang = value.mata_uang ?? '-';
                            var vat = value.vat ?? '-';
                            var total = po_qty * harga_per_unit;
                            console.table({
                                kode_material,
                                deskripsi,
                                batas,
                                newDate,
                                po_qty,
                                total,
                                vat,
                                satuan,
                                harga_per_unit,
                                mata_uang,
                                id_detail_pr,
                            })
                            var html = '<tr>' +
                                '<td>' + no + '</td>' +
                                '<td>' + kode_material + '</td>' +
                                '<td>' + deskripsi + '</td>' +
                                '<td><input type="date" value="' + newDate +
                                '" class="form-control" id="batas' + id + '" name="batas' + id +
                                '"></td>' +
                                '<td>' + value.po_qty + '</td>' +
                                '<td>' + satuan + '</td>' +
                                '<td><input type="text" value="' + harga_per_unit +
                                '" class="form-control" id="harga_per_unit' + id +
                                '" name="harga_per_unit' + id + '"></td>' +
                                '<td><input type="text" value="' + mata_uang +
                                '" class="form-control" id="mata_uang' + id + '" name="mata_uang' + id +
                                '"></td>' +
                                '<td><input type="text" value="' + vat +
                                '" class="form-control" id="vat' + id + '" name="vat' + id + '"></td>' +
                                '<td>' + total + '</td>' +
                                '<td><button title="simpan" id="edit_po_save" type="button" class="btn btn-success btn-xs" data-id="' +
                                id + '" data-idpo="' + id_po + '" ><i class="fas fa-save"></i>' +
                                '</button>' +
                                '<button title="hapus" id="delete_po_save" type="button" class="btn btn-danger btn-xs" data-id="' +
                                id +
                                '" data-idpo="' + id_po + '" data-id_detail_pr="' + id_detail_pr +
                                '" ><i class="fas fa-trash"></i>' +
                                '</button>' +
                                '</tr>';
                            $('#tabel-po').append(html);
                            no++;
                        });
                    }
                    //remove loading
                    // if(data?.po?.details?.length > 1){
                    //     $('#tabel-po').find('tr:first').remove();
                    // }
                    $('#loader').hide();
                    $('#form').show();
                    // getPODetail();
                },
                error: function() {
                    $('#pcode').prop("disabled", false);
                    $('#button-check').prop("disabled", false);
                }


            });

        }

        function productCheck() {
            var proyek_name = $('#proyek_name').val();
            if (proyek_name.length > 0) {
                loader();
                $('#proyek_code').prop("disabled", true);
                $('#button-check').prop("disabled", true);
                $.ajax({
                    url: "{{ url('products/products_pr?proyek=') }}" + proyek_name,
                    type: "GET",
                    data: {
                        "format": "json"
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $('#loader').show();
                        $('#form').hide();

                    },
                    success: function(data) {
                        loader(0);
                        $('#form').show();
                        //append to #detail-material
                        $('#detail-material').empty();
                        $.each(data.products, function(key, value) {
                            console.table('a', value)
                            var no_spph
                            if (!value.id_spph) {
                                no_spph = '-'
                            } else {
                                no_spph = value.nomor_spph
                            }

                            var no_pr
                            if (!value.id_pr) {
                                no_pr = '-'
                            } else {
                                no_pr = value.pr_no
                            }

                            var no_po
                            if (!value.id_po) {
                                no_po = '-'
                            } else {
                                no_po = value.po_no
                            }

                            var checkbox
                            if (value.id_spph && !value.id_po) {
                                checkbox = '<input type="checkbox" id="addToDetails" value="' + value
                                    .id +
                                    '" onclick="addToDetailsJS(' + value.id + ')" >'
                            } else {
                                checkbox = '<input type="checkbox" id="addToDetails" value="' + value
                                    .id +
                                    '" onclick="addToDetailsJS(' + value.id + ')" disabled>'
                            }

                            $('#detail-material').append(

                                '<tr><td>' + (key + 1) + '</td><td>' + value.uraian +
                                '</td><td>' + value.spek + '</td><td>' + value.po_qty +
                                '</td><td>' +
                                value
                                .satuan + '</td><td>' + value.nama_pekerjaan + '</td><td>' +
                                no_spph +
                                '</td><td>' + no_pr + '</td><td>' +
                                no_po + '</td><td>' + checkbox + '</td></tr>'
                            );
                        });
                        $('#detail-material').append(
                            '<tr><td colspan="8" class="text-center">Tidak ada produk</td></tr>');
                    },
                    error: function() {
                        $('#pcode').prop("disabled", false);
                        $('#button-check').prop("disabled", false);
                    }
                });
            } else {
                toastr.error("Nama Proyek tidak ditemukan");
            }
        }

        function PoUpdate() {
            var id = $('#id').val();
            var pid = $('#pid').val();
            var type = $('#type').val();
            var deskripsi = $('#pname').val();
            var batas = $('#batas').val();
            var po_qty = $('#qty').val();
            var unit = $('#unit').val();
            var token = $('input[name=_token]').val();
            var url = "{{ url('products/purchase_order_detail/update') }}";
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    id: id,
                    pid: pid,
                    type: type,
                    deskripsi: deskripsi,
                    batas: batas,
                    po_qty: po_qty,
                    unit: unit,
                    _token: token
                },
                dataType: "json",

                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        toastr.success(data.message);
                        $('#detail-po').modal('hide');
                        location.reload();
                    } else {
                        toastr.error(data.message);
                    }
                }
            })
        }

        function deletePo(data) {
            $('#delete_id').val(data.id);
        }

        function download(type) {
            window.location.href = "{{ route('products.wip.history') }}?search={{ Request::get('search') }}&dl=" + type;
        }
    </script>
    @if (Session::has('success'))
        <script>
            toastr.success('{!! Session::get('success') !!}');
        </script>
    @endif
    @if (Session::has('error'))
        <script>
            toastr.error('{!! Session::get('error') !!}');
        </script>
    @endif
    @if (!empty($errors->all()))
        <script>
            toastr.error('{!! implode('', $errors->all('<li>:message</li>')) !!}');
        </script>
    @endif
@endsection
