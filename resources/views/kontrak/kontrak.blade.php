@extends('layouts.main')
@section('title', __('Kontrak'))
@section('custom-css')
    <link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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

                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-kontrak"
                        onclick="addKONTRAK()"><i class="fas fa-plus"></i> Add Kontrak</button>
                    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#import-product" onclick="importProduct()"><i class="fas fa-file-excel"></i> Import Product (Excel)</button> -->
                    <!-- <button type="button" class="btn btn-primary" onclick="download('xls')"><i class="fas fa-file-excel"></i> Export Product (XLS)</button> -->
                    <div class="card-tools">
                        <form>
                            {{-- <div class="input-group input-group">
                                <input type="text" class="form-control" name="q" placeholder="Search">
                                <input type="hidden" name="category" value="{{ Request::get('category') }}">
                                <input type="hidden" name="sort" value="{{ Request::get('sort') }}">
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

                        {{-- Filter by Nomor Pr dan Tanggal --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter-kontrak-no">Filter Nomor Kontrak</label>
                                    <input type="text" class="form-control" id="filter-kontrak-no"
                                        placeholder="Masukkan Nomor Kontrak">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter-kontrak-date">Filter Tanggal Kontrak</label>
                                    <input type="date" class="form-control" id="filter-kontrak-date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-secondary mt-4" id="clear-filter">Clear Filter</button>
                            </div>
                        </div>
                        {{-- End Filter by Nomor Pr dan Tanggal --}}

                        <table id="table" class="table table-sm table-bordered table-hover table-striped">
                            <thead>
                                <tr class="text-center">
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>No.</th>
                                    <th>{{ __('Tanggal') }}</th>
                                    <th>{{ __('Kode Proyek') }}</th>
                                    <th>{{ __('Nomor Kontrak') }}</th>
                                    <th>{{ __('Nama Pekerjaan') }}</th>
                                    <th>{{ __('Nilai Pekerjaan (Rp.)') }}</th>
                                    <th>{{ __('Nama Pelanggan') }}</th>
                                    {{-- <th>{{ __('Nilai (Rp.)') }}</th> --}}
                                    <th>{{ __('Status') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($requests) > 0)
                                    @foreach ($requests as $key => $d)
                                        @php
                                            $data = [
                                                'no' => $requests->firstItem() + $key,
                                                'tanggal' => date('d/m/Y', strtotime($d->tanggal)),
                                                'kode_proyek' => $d->kode_proyek,
                                                'nomor_kontrak' => $d->nomor_kontrak,
                                                'nama_pekerjaan' => $d->nama_pekerjaan,
                                                'nilai_pekerjaan' => $d->nilai_pekerjaan,
                                                'nama_pelanggan' => $d->nama_pelanggan,
                                                // 'nilai' => $d->nilai,
                                                'status' => $d->status,

                                                'id' => $d->id,
                                                'editable' => $d->editable,
                                            ];
                                        @endphp

                                        {{-- backup button tampil semua --}}
                                        {{-- <tr>
                                            <td class="text-center"><input type="checkbox" name="hapus[]"
                                                    value="{{ $d->id }}"></td>
                                            <td class="text-center">{{ $data['no'] }}</td>
                                            <td class="text-center">{{ $data['no_pr'] }}</td>
                                            <td class="text-center">{{ $data['proyek'] }}</td>
                                            <td class="text-center">{{ $data['tanggal'] }}</td>
                                            <td class="text-center">{{ $data['dasar_pr'] }}</td>
                                            <td class="text-center">
                                                <button title="Edit Request" type="button" class="btn btn-success btn-xs"
                                                    data-toggle="modal" data-target="#add-pr"
                                                    onclick="editPR({{ json_encode($data) }})"
                                                    @if ($data['editable'] == 0) disabled @endif><i
                                                        class="fas fa-edit"></i></button>
                                                <button title="Lihat Detail" type="button" data-toggle="modal"
                                                    data-target="#detail-pr" class="btn-lihat btn btn-info btn-xs"
                                                    data-detail="{{ json_encode($data) }}"><i
                                                        class="fas fa-list"></i></button>
                                                @if (Auth::user()->role == 0 || Auth::user()->role == 2 || Auth::user()->role == 3)
                                                    <button title="Hapus Request" type="button"
                                                        class="btn btn-danger btn-xs" data-toggle="modal"
                                                        data-target="#delete-pr"
                                                        onclick="deletePR({{ json_encode($data) }})"
                                                        @if ($data['editable'] == 0) disabled @endif><i
                                                            class="fas fa-trash"></i></button>
                                                @endif
                                            </td>
                                        </tr> --}}
                                        {{-- backup button tampil semua --}}

                                        <tr>

                                            <td class="text-center"><input type="checkbox" name="hapus[]"
                                                    value="{{ $d->id }}"></td>
                                            <td class="text-center">{{ $data['no'] }}</td>
                                            <td class="text-center">{{ $data['tanggal'] }}</td>
                                            <td class="text-center">{{ $data['kode_proyek'] }}</td>
                                            <td class="text-center">{{ $data['nomor_kontrak'] }}</td>
                                            <td class="text-center">{{ $data['nama_pekerjaan'] }}</td>
                                            <td class="text-center">
                                                {{ isset($data['nilai_pekerjaan']) ? number_format((float) $data['nilai_pekerjaan'], 0, ',', '.') : '-' }}
                                            </td>
                                            <td class="text-center">{{ $data['nama_pelanggan'] }}</td>
                                            <td class="text-center">{{ $data['status'] }}</td>
                                            {{-- <td class="text-center">{{ isset($data['nilai']) ? number_format((float) $data['nilai'], 0, ',', '.') : '-' }}</td> --}}
                                            {{-- <td class="text-center">{{ $data['status'] }}</td> --}}
                                            <!-- Kolom status, tampilkan "kontrak" jika nomor_kontrak ada, "-" jika kosong -->
                                            {{-- <td class="text-center">
                                                {{ $data['nomor_kontrak'] ? 'kontrak' : '-' }}
                                            </td> --}}
                                            <td class="text-center">



                                                <!-- Tombol hanya ditampilkan untuk role 2 dan no_pr mengandung 'wil1' -->
                                                <button title="Edit Kontrak" type="button" class="btn btn-success btn-xs"
                                                    data-toggle="modal" data-target="#add-kontrak"
                                                    onclick="editKONTRAK({{ json_encode($data) }})"
                                                    @if ($data['editable'] == 0) disabled @endif>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button title="Detail Kontrak" type="button" data-toggle="modal"
                                                    data-target="#detail-kontrak" class="btn-lihat btn btn-info btn-xs"
                                                    data-detail="{{ json_encode($data) }}">
                                                    <i class="fas fa-list"></i>
                                                </button>
                                                <button title="Hapus Kontrak" type="button" class="btn btn-danger btn-xs"
                                                    data-toggle="modal" data-target="#delete-kontrak"
                                                    onclick="deleteKONTRAK({{ json_encode($data) }})"
                                                    @if ($data['editable'] == 0) disabled @endif>
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="text-center">
                                        <td colspan="10">{{ __('No data.') }}</td>
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
                {{ $requests->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
            </div>
        </div>

        {{-- modal --}}
        <div class="modal fade" id="add-kontrak">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="modal-title" class="modal-title">{{ __('Add Kontrak') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="save" action="{{ route('products.kontrak.store') }}"
                            method="post">
                            @csrf
                            <input type="hidden" id="save_id" name="id">

                            {{-- Tanggal --}}
                            <div class="form-group row">
                                <label for="tanggal" class="col-sm-4 col-form-label">{{ __('Tanggal') }}
                                </label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control" id="tanggal" name="tanggal">
                                </div>
                            </div>

                            {{-- Kode Proyek --}}
                            <div class="form-group row">
                                <label for="kode_proyek" class="col-sm-4 col-form-label">{{ __('Kode Proyek') }}
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="kode_proyek" name="kode_proyek"
                                        autocomplete="off">
                                </div>
                            </div>

                            {{-- Nomor Kontrak --}}
                            <div class="form-group row">
                                <label for="nomor_kontrak" class="col-sm-4 col-form-label">{{ __('Nomor Kontrak') }}
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="nomor_kontrak" name="nomor_kontrak"
                                        autocomplete="off">
                                </div>
                            </div>

                            {{-- Nama Pekerjaan --}}
                            <div class="form-group row">
                                <label for="nama_pekerjaan" class="col-sm-4 col-form-label">{{ __('Nama Pekerjaan') }}
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="nama_pekerjaan" name="nama_pekerjaan"
                                        autocomplete="off">
                                    {{-- <textarea class="form-control" name="dasar_pr" id="dasar_pr" rows="3" readonly></textarea> --}}
                                </div>
                            </div>

                            {{-- Nilai Pekerjaan --}}
                            <div class="form-group row">
                                <label for="nilai_pekerjaan"
                                    class="col-sm-4 col-form-label">{{ __('Nilai Pekerjaan (Rp.)') }}
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="nilai_pekerjaan"
                                        name="nilai_pekerjaan" autocomplete="off">
                                    {{-- <textarea class="form-control" name="dasar_pr" id="dasar_pr" rows="3" readonly></textarea> --}}
                                </div>
                            </div>

                            {{-- Nama Pelanggan --}}
                            <div class="form-group row">
                                <label for="nama_pelanggan" class="col-sm-4 col-form-label">{{ __('Nama Pelanggan') }}
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan"
                                        autocomplete="off">
                                    {{-- <textarea class="form-control" name="dasar_pr" id="dasar_pr" rows="3" readonly></textarea> --}}
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="form-group row">
                                <label for="status" class="col-sm-4 col-form-label">{{ __('Status') }}</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="status" name="status">
                                        <option value="-">-</option>
                                        <option value="Konfirmasi Order">Konfirmasi Order</option>
                                        <option value="Kontrak">Kontrak</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Nilai --}}
                            {{-- <div class="form-group row">
                                <label for="nilai" class="col-sm-4 col-form-label">{{ __('Nilai (Rp.)') }}
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="nilai" name="nilai"
                                        autocomplete="off">
                                    
                                </div>
                            </div> --}}



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


        {{-- modal lihat detail --}}
        <div class="modal fade" id="detail-kontrak">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="modal-title" class="modal-title">{{ __('Detail KONTRAK') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="row">
                                {{-- <form id="cetak-bpm" method="GET" action="{{ route('cetak_bpm') }}" target="_blank">
                                    <input type="hidden" name="id" id="id">
                                </form> --}}
                                <div class="col-12" id="container-form">
                                    {{-- <button id="button-cetak-bpm" type="button" class="btn btn-primary"
                                        onclick="document.getElementById('cetak-bpm').submit();">{{ __('Cetak') }}</button> --}}
                                    <table class="align-top w-100">
                                        <tr>
                                            <td style="width: 3%;"><b>Nama Pekerjaan</b></td>
                                            <td style="width:2%">:</td>
                                            <td style="width: 55%"><span id="pekerjaan"></span></td>
                                        </tr>
                                        <tr>
                                            <td><b>Nomor Kontrak</b></td>
                                            <td>:</td>
                                            <td><span id="no_kontrak"></span></td>
                                        </tr>


                                        <tr>
                                            <td colspan="3">
                                                <button id="button-tambah-produk" type="button"
                                                    class="btn btn-info mb-3"
                                                    onclick="showAddProduct()">{{ __('Tambah Item Detail') }}</button>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead style="text-align: center">
                                                <th>{{ __('NO') }}</th>
                                                <th>{{ __('Nomor Dokumen') }}</th>
                                                <th>{{ __('Tanggal Dokumen') }}</th>
                                                <th>{{ __('Perihal') }}</th>
                                                <th>{{ __('Keterangan') }}</th>
                                                <th>{{ __('Lampiran') }}</th>


                                                <th>{{ __('AKSI') }}</th>
                                            </thead>
                                            <tbody id="table-kontrak">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>




                                <div class="col-0 d-none" id="container-product">
                                    <div class="card">
                                        {{-- <div class="card-body">
                                            
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="customRadio1" name="ptype"
                                                    class="custom-control-input" checked value="inka">
                                                <label class="custom-control-label" for="customRadio1">INKA</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="customRadio2" name="ptype"
                                                    class="custom-control-input" value="imss">
                                                <label class="custom-control-label" for="customRadio2">IMSS</label>
                                            </div>

                                            <div class="input-group input-group-lg">

                                                <input type="text" class="form-control" id="pcode" name="pcode"
                                                    min="0" placeholder="Product Code">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" id="button-check"
                                                        onclick="productCheck()">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                    <div id="loader" class="card">
                                        <div class="card-body text-center">
                                            <div class="spinner-border text-danger" style="width: 3rem; height: 3rem;"
                                                role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="form" class="card">
                                        <div class="card-body">
                                            <form role="form" id="stock-update" method="post"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" id="pid" name="pid">
                                                <input type="hidden" id="type" name="type">
                                                <input type="hidden" id="proyek_id_val" name="proyek_id_val">

                                                <div class="form-group row">
                                                    <label for="nomor_dokumen"
                                                        class="col-sm-4 col-form-label">{{ __('Nomor Dokumen') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="nomor_dokumen">
                                                        <input type="hidden" class="form-control" id="kontrak_id"
                                                            disabled>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="tanggal_dokumen"
                                                        class="col-sm-4 col-form-label">{{ __('Tanggal') }}
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <input type="date" class="form-control" id="tanggal_dokumen"
                                                            name="tanggal_dokumen">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="perihal"
                                                        class="col-sm-4 col-form-label">{{ __('Perihal') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="perihal"
                                                            name="perihal" autocomplete="off">
                                                    </div>
                                                </div>


                                                <div class="form-group row">
                                                    <label for="keterangan"
                                                        class="col-sm-4 col-form-label">{{ __('Keterangan') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="keterangan"
                                                            name="keterangan" autocomplete="off">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="lampiran"
                                                        class="col-sm-4 col-form-label">{{ __('Lampiran') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="file" class="form-control" id="lampiran"
                                                            name="lampiran" />
                                                        <small id="lampiran-info" class="form-text text-muted"
                                                            style="display: none;"></small>
                                                    </div>
                                                </div>
                                                {{-- <div class="form-group row">
                                                    <label for="lampiran" class="col-sm-4 col-form-label">{{ __('Lampiran') }}</label>
                                                    <div class="col-sm-8 position-relative">
                                                        <input type="file" class="form-control" id="lampiran" name="lampiran" onchange="updateLampiranName()" style="display: none;" />
                                                        <label for="lampiran" class="form-control" id="lampiran-label" style="cursor: pointer;">
                                                            <span id="lampiran-filename">Pilih file...</span> <!-- Placeholder awal -->
                                                        </label>
                                                        <small id="lampiran-info" class="form-text text-muted" style="display: none;"></small>
                                                    </div>
                                                </div> --}}
                                                {{-- teslampiran                                                 --}}

                                                {{-- <div class="form-group row">
                                                    <label for="lampiran"
                                                        class="col-sm-4 col-form-label">{{ __('Nota Pembelian') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="file" class="form-control" id="lampiran"
                                                            name="lampiran" />
                                                    </div>
                                                </div> --}}

                                            </form>
                                            <button id="button-update-kontrak" type="button"
                                                class="btn btn-primary w-100">{{ __('Tambahkan') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal delete --}}
        <div class="modal fade" id="delete-kontrak">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="modal-title" class="modal-title">{{ __('Delete Kontrak') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="delete" action="{{ route('kontrak.destroy') }}" method="post">
                            @csrf
                            @method('delete')
                            <input type="hidden" id="delete_id" name="id">
                        </form>
                        <div>
                            <p>Anda yakin ingin menghapus Data Kontrak ini <span id="pcode"
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
    </section>
@endsection

{{-- custom Js --}}
@section('custom-js')
    <script src="/plugins/toastr/toastr.min.js"></script>
    <script src="/plugins/select2/js/select2.full.min.js"></script>
    <script src="/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    {{-- Menampilkan form otomatis Dasar Proyek --}}
    <script>
        $(document).ready(function() {
            $('#proyek_id').change(function() {
                var proyek_id = $(this).val();
                if (proyek_id) {
                    $.ajax({
                        url: '{{ route('get-dasar-proyek') }}',
                        type: 'GET',
                        data: {
                            proyek_id: proyek_id
                        },
                        success: function(response) {
                            $('#dasar_pr').val(response.dasar_proyek);
                        },
                        error: function() {
                            $('#dasar_pr').val('');
                        }
                    });
                } else {
                    $('#dasar_pr').val('');
                }
            });
        });
    </script>
    {{-- End Menampilkan form otomatis Dasar Proyek --}}



    <script>
        $(function() {
            bsCustomFileInput.init();
            var user_id;
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            $('#loader').hide();

            $(".btn-lihat").on('click', function() {
                const code = $(this).attr('code');
                $("#pcode_print").val(code);
                $("#barcode").attr("src", "/products/barcode/" + code);
            });

            $('#product_code').on('change', function() {
                var code = $('#product_code').val();
                if (code != null && code != "") {
                    $("#barcode_preview").attr("src", "/products/barcode/" + code);
                    $('#barcode_preview_container').show();
                }
            });
        });

        $('#sort').on('change', function() {
            $("#sorting").submit();
        });

        function resetForm() {
            $('#save').trigger("reset");
            $('#barcode_preview_container').hide();
        }


        //Filter by Nomor dan tgl PO
        $(document).ready(function() {
            $('#clear-filter').on('click', function() {
                $('#filter-kontrak-no, #filter-kontrak-date').val('');
                filterTable();
            });

            $('#filter-kontrak-no, #filter-kontrak-date').on('keyup change', function() {
                filterTable();
            });

            function filterTable() {
                var filterNoKONTRAK = $('#filter-kontrak-no').val().toUpperCase();
                var filterDateKONTRAK = $('#filter-kontrak-date').val();

                $('table tbody tr').each(function() {
                    var noKONTRAK = $(this).find('td:nth-child(5)').text().toUpperCase();
                    var dateKONTRAK = $(this).find('td:nth-child(3)')
                        .text(); // Ubah indeks kolom ke indeks tgl_pr jika perlu
                    var id = $(this).find('td:nth-child(1)')
                        .text(); // Ubah indeks kolom ke indeks ID PO jika perlu

                    // Ubah string tanggal ke objek Date untuk perbandingan
                    var dateParts = dateKONTRAK.split("/");
                    var kontrakDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[
                        0]); // Format: tahun, bulan, tanggal

                    // Ubah string filterDatePR ke objek Date
                    var filterDateParts = filterDateKONTRAK.split("-");
                    var filterKONTRAKDate = new Date(filterDateParts[0], filterDateParts[1] - 1,
                        filterDateParts[
                            2]); // Format: tahun, bulan, tanggal

                    if ((noKONTRAK.indexOf(filterNoKONTRAK) > -1 || filterNoKONTRAK === '') &&
                        (kontrakDate.getTime() === filterKONTRAKDate.getTime() || filterDateKONTRAK === '')
                    ) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });

        //End Filter by Nomor dan tgl PO

        function addKONTRAK() {
            $('#modal-title').text("Add Kontrak");
            $('#button-save').text("Tambahkan");
            $('#save_id').val("");
            resetForm();
        }


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
                    url: 'kontrak-imss/hapus-multiple',
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

        $('#detail-kontrak').on('hidden.bs.modal', function() {
            $('#container-product').addClass('d-none');
            $('#container-product').removeClass('col-5');
            $('#container-form').addClass('col-12');
            $('#container-form').removeClass('col-7');
            $('#button-tambah-detail').text('Tambah Item Detail');
        });

        function showAddProduct() {
            if ($('#detail-kontrak').find('#container-product').hasClass('d-none')) {
                $('#detail-kontrak').find('#container-product').removeClass('d-none');
                $('#detail-kontrak').find('#container-product').addClass('col-5');
                $('#detail-kontrak').find('#container-form').removeClass('col-12');
                $('#detail-kontrak').find('#container-form').addClass('col-7');
                $('#button-tambah-produk').text('Kembali');
                $('#button-update-kontrak').off('click');
                // Menambahkan event listener baru untuk menghandle klik pada tombol
                $('#button-update-kontrak').text("Simpan").on('click', function() {
                    // Ubah teks tombol menjadi "Loading"
                    // $(this).text("Loading...");

                    // // Nonaktifkan tombol
                    // $(this).prop('disabled', true);

                    // Jalankan fungsi PRinsert()
                    KONTRAKinsert();

                    // Setelah 2 detik, kembalikan teks tombol ke semula, aktifkan kembali tombol, dan tampilkan pesan Toastr
                    // setTimeout(function() {
                    //     $('#button-update-pr').text("Simpan").prop('disabled', false);
                    //     toastr.success('Data Berhasil ditambahkan');
                    // }, 2000); // 2000 milidetik = 2 detik
                });

            } else {
                $('#detail-kontrak').find('#container-product').removeClass('col-5');
                $('#detail-kontrak').find('#container-product').addClass('d-none');
                $('#detail-kontrak').find('#container-form').addClass('col-12');
                $('#detail-kontrak').find('#container-form').removeClass('col-7');
                $('#button-tambah-produk').text('Tambah Item Detail');
                clearForm();

            }
        }

        function editKONTRAK(data) {
            $('#modal-title').text("Edit KONTRAK");
            $('#button-save').text("Simpan");
            resetForm();
            $('#save_id').val(data.id);
            $('#kode_proyek').val(data.kode_proyek);
            $('#nomor_kontrak').val(data.nomor_kontrak);
            // $('#tgl_pr').val(data.tgl_pr);
            // $('#proyek_id').val(data.proyek);
            $('#nama_pekerjaan').val(data.nama_pekerjaan);
            $('#nilai_pekerjaan').val(data.nilai_pekerjaan);
            $('#nama_pelanggan').val(data.nama_pelanggan);
            $('#status').val(data.status);
            // $('#nilai').val(data.nilai);
            var date = data.tanggal.split('/');
            var newDate = date[2] + '-' + date[1] + '-' + date[0];
            $('#tanggal').val(newDate);
            $('#proyek_id').find('option').each(function() {
                if ($(this).val() == data.proyek_id) {
                    console.log($(this).val());
                    $(this).attr('selected', true);
                }
            });
        }

        function emptyTableProducts() {
            $('#table-kontrak').empty();
            $('#pekerjaan').text("");
            $('#no_kontrak').text("");

        }

        function loader(status = 1) {
            if (status == 1) {
                $('#loader').show();
            } else {
                $('#loader').hide();
            }
        }

        // $('#form').hide();

        function productCheck() {
            var pcode = $('#pcode').val();
            var ptype = $('input[name="ptype"]:checked').val();
            if (pcode.length > 0) {
                loader();
                $('#pcode').prop("disabled", true);
                $('#button-check').prop("disabled", true);
                $.ajax({
                    url: "{{ url('materials?type=') }}" + ptype + '&kode=' + pcode,
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
                        if (data.success) {
                            $('#form').show();
                            $('#uraian').val(data.materials.nama_barang);
                            $('#kode_material').val(data.materials.kode_material);
                            $('#spek').val(data.materials.spesifikasi);
                            $('#satuan').val(data.materials.satuan);
                        } else {
                            $('#form').show();
                            toastr.error("Product Code tidak dikenal!");
                        }
                        $('#pcode').prop("disabled", false);
                        $('#button-check').prop("disabled", false);
                    },
                    error: function() {
                        $('#pcode').prop("disabled", false);
                        $('#button-check').prop("disabled", false);
                    }
                });
            } else {
                toastr.error("Product Code belum diisi!");
            }
        }

        function clearForm() {
            $('#nomor_dokumen').val("");
            $('#tanggal_dokumen').val("");
            $('#perihal').val("");
            $('#keterangan').val("");
            $('#lampiran').val("");

            // $('#form').hide();
        }

        function KONTRAKinsert() {
            const id_kontrak = $('#kontrak_id').val()
            // const id = $('#id').val()

            var inputFile = $("#lampiran")[0].files[0];
            // var inputFile = $("#lampiran")[0].files[0];
            var formData = new FormData();
            formData.append('lampiran', inputFile);
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('kontrak_id', id_kontrak);
            // formData.append('id', id);
            formData.append('id_proyek', $('#proyek_id_val').val());
            formData.append('nomor_dokumen', $('#nomor_dokumen').val());
            formData.append('tanggal_dokumen', $('#tanggal_dokumen').val());
            formData.append('perihal', $('#perihal').val());
            formData.append('keterangan', $('#keterangan').val());
            // formData.append('lampiran', $('#lampiran').val());


            // if ($('#tanggal_permintaan').val() == null || $('#tanggal_permintaan').val() == "") {
            //     toastr.error("Tanggal Permintaan belum diisi!");
            //     return
            // }


            // // Menentukan apakah akan melakukan insert atau update berdasarkan keberadaan id
            if (id) {
                // //     // Jika id sudah ada, lakukan update
                createData(formData);
            } else {
                // Jika id belum ada, lakukan insert
                createData(formData);
            }
        }

        function KONTRAKupdate() {
            const id_kontrak = $('#kontrak_id').val()
            const id = $('#id').val()

            var inputFile = $("#lampiran")[0].files[0];
            var formData = new FormData();
            formData.append('lampiran', inputFile);
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('kontrak_id', id_kontrak);
            formData.append('id', id);
            formData.append('id_proyek', $('#proyek_id_val').val());
            formData.append('nomor_dokumen', $('#nomor_dokumen').val());
            formData.append('tanggal_dokumen', $('#tanggal_dokumen').val());
            formData.append('perihal', $('#perihal').val());
            formData.append('keterangan', $('#keterangan').val());
            // formData.append('lampiran', $('#lampiran').val());

            // if ($('#tanggal_permintaan').val() == null || $('#tanggal_permintaan').val() == "") {
            //     toastr.error("Tanggal Permintaan belum diisi!");
            //     return
            // }


            // // Menentukan apakah akan melakukan insert atau update berdasarkan keberadaan id
            if (id) {
                // //     // Jika id sudah ada, lakukan update
                updateData(formData);
            } else {
                // Jika id belum ada, lakukan insert
                createData(formData);
            }
        }




        function createData(formData) {
            $.ajax({
                url: "{{ url('products/update_kontrak_detail') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#table-kontrak').append('<tr><td colspan="15" class="text-center">Loading...</td></tr>');
                    $('#button-cetak-bpm').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                    $('#button-cetak-bpm').attr('disabled', true);
                },

                // success: function(data) {
                //     console.log(data);
                //     $('#id').val(data.kontrak.id);
                //     $('#nama_pekerjaan').text(data.kontrak.nama_pekerjaan);
                //     $('#tanggal').text(data.kontrak.tanggal);

                //     $('#button-cetak-bpm').html('<i class="fas fa-print"></i> Cetak');
                //     $('#button-cetak-bpm').attr('disabled', false);
                //     if ($('#detail-kontrak').find('#container-product').hasClass('d-none')) {
                //         $('#detail-kontrak').find('#container-product').removeClass('d-none');
                //         $('#detail-kontrak').find('#container-product').addClass('col-5');
                //         $('#detail-kontrak').find('#container-form').removeClass('col-12');
                //         $('#detail-kontrak').find('#container-form').addClass('col-7');
                //         $('#button-tambah-produk').text('Kembali');
                //     } else {
                //         $('#detail-kontrak').find('#container-product').removeClass('col-5');
                //         $('#detail-kontrak').find('#container-product').addClass('d-none');
                //         $('#detail-kontrak').find('#container-form').addClass('col-12');
                //         $('#detail-kontrak').find('#container-form').removeClass('col-7');
                //         $('#button-tambah-produk').text('Tambah Item Detail');
                //         clearForm();
                //     }
                //     var no = 1;

                //     if (data.kontrak.details.length == 0) {
                //         $('#table-kontrak').empty();
                //         $('#table-kontrak').append(
                //             '<tr><td colspan="15" class="text-center">Tidak ada produk</td></tr>'
                //         ); // Tambahkan pesan bahwa tidak ada produk
                //     } else {
                //         $('#table-kontrak').empty();
                //         $.each(data.kontrak.details, function(key, value) {
                //             var rowIndex = key + 1;
                //             var editButton =
                //                 '<button type="button" class="btn btn-success btn-xs mr-1" data-row-id="' +
                //                 value.id +
                //                 '" title="Edit" onclick="editRow(\'' + value.id + '\', \'' +
                //                 value.nomor_dokumen + '\', \'' + value.tanggal_dokumen + '\', \'' +
                //                 value.perihal + '\', \'' + value.keterangan + '\', \'' + value
                //                 .lampiran + '\')"><i class="fas fa-edit"></i></button>';

                //             var deleteButton =
                //                 '<button type="button" class="btn btn-danger btn-xs mr-1"' +
                //                 ' onclick="deleteDetail(' + value.id + ', \'' + value.nomor_dokumen
                //                 .toString() + '\')"' +
                //                 ' title="Delete">' +
                //                 '<i class="fas fa-trash"></i>' +
                //                 '</button>';

                //             var urlLampiran = "{{ asset('lampiran') }}";
                //             var lampiran = null;

                //             if (value.lampiran) {
                //                 lampiran = '<a href="' + urlLampiran + '/' + value.lampiran +
                //                     '" target="_blank"><i class="fa fa-eye"></i> Lihat</a>';
                //             } else {
                //                 lampiran = '-';
                //             }

                //             $('#table-kontrak').append('<tr><td>' + rowIndex + '</td><td>' + value
                //                 .nomor_dokumen + '</td><td>' +
                //                 moment(value.tanggal_dokumen).format('DD/MM/YYYY') + '</td><td>' +
                //                 value.perihal + '</td><td>' +
                //                 value.keterangan + '</td><td>' +
                //                 lampiran + '</td><td>' + editButton + deleteButton + '</td></tr>');
                //         });
                //     }
                // }

                //tanggal lebih awal, nomornya naik
                success: function(data) {
                    console.log(data);
                    $('#id').val(data.kontrak.id);
                    $('#pekerjaan').text(data.kontrak.nama_pekerjaan);
                    $('#no_kontrak').text(data.kontrak.nomor_kontrak);

                    $('#button-cetak-bpm').html('<i class="fas fa-print"></i> Cetak');
                    $('#button-cetak-bpm').attr('disabled', false);
                    if ($('#detail-kontrak').find('#container-product').hasClass('d-none')) {
                        $('#detail-kontrak').find('#container-product').removeClass('d-none');
                        $('#detail-kontrak').find('#container-product').addClass('col-5');
                        $('#detail-kontrak').find('#container-form').removeClass('col-12');
                        $('#detail-kontrak').find('#container-form').addClass('col-7');
                        $('#button-tambah-produk').text('Kembali');
                    } else {
                        $('#detail-kontrak').find('#container-product').removeClass('col-5');
                        $('#detail-kontrak').find('#container-product').addClass('d-none');
                        $('#detail-kontrak').find('#container-form').addClass('col-12');
                        $('#detail-kontrak').find('#container-form').removeClass('col-7');
                        $('#button-tambah-produk').text('Tambah Item Detail');
                        clearForm();
                    }
                    var no = 1;


                    // Cek apakah ada detail
                    if (data.kontrak.details.length == 0) {
                        $('#table-kontrak').empty();
                        $('#table-kontrak').append(
                            '<tr><td colspan="15" class="text-center">Tidak ada produk</td></tr>'
                        ); // Tambahkan pesan bahwa tidak ada produk
                    } else {
                        // Urutkan data berdasarkan tanggal_dokumen
                        data.kontrak.details.sort(function(a, b) {
                            return new Date(a.tanggal_dokumen) - new Date(b.tanggal_dokumen);
                        });

                        $('#table-kontrak').empty();
                        $.each(data.kontrak.details, function(key, value) {
                            var rowIndex = key + 1; // Nomor urut

                            var editButton =
                                '<button type="button" class="btn btn-success btn-xs mr-1" data-row-id="' +
                                value.id +
                                '" title="Edit" onclick="editRow(\'' + value.id + '\', \'' +
                                value.nomor_dokumen + '\', \'' + value.tanggal_dokumen + '\', \'' +
                                value.perihal + '\', \'' + value.keterangan + '\', \'' + value
                                .lampiran + '\')"><i class="fas fa-edit"></i></button>';

                            var deleteButton =
                                '<button type="button" class="btn btn-danger btn-xs mr-1"' +
                                ' onclick="deleteDetail(' + value.id + ', \'' + value.nomor_dokumen
                                .toString() + '\')"' +
                                ' title="Delete">' +
                                '<i class="fas fa-trash"></i>' +
                                '</button>';

                            var urlLampiran = "{{ asset('lampiran') }}";
                            var lampiran = null;

                            if (value.lampiran) {
                                lampiran = '<a href="' + urlLampiran + '/' + value.lampiran +
                                    '" target="_blank"><i class="fa fa-eye"></i> Lihat</a>';
                            } else {
                                lampiran = '-';
                            }

                            $('#table-kontrak').append('<tr><td>' + rowIndex + '</td><td>' +
                                (value.nomor_dokumen ? value.nomor_dokumen : '') + '</td><td>' +
                                (value.tanggal_dokumen ? moment(value.tanggal_dokumen).format(
                                    'DD/MM/YYYY') : '') + '</td><td>' +
                                (value.perihal ? value.perihal : '') + '</td><td>' +
                                (value.keterangan ? value.keterangan : '') + '</td><td>' +
                                (lampiran ? lampiran : '') + '</td><td>' + editButton +
                                deleteButton + '</td></tr>');
                        });
                    }
                }
            });
        }


        // function updateData(formData) {
        //     // Lakukan operasi insert data
        //     // Misalnya, Anda dapat menggunakan AJAX untuk mengirim permintaan ke backend
        //     // atau menggunakan fungsi JavaScript lainnya yang sesuai dengan logika aplikasi Anda
        //     $.ajax({
        //         url: "{{ url('products/kontrak/update_detail') }}", // Ganti URL sesuai dengan endpoint untuk operasi insert
        //         type: "POST",
        //         data: formData,
        //         contentType: false,
        //         processData: false,
        //         beforeSend: function() {
        //             $('#table-kontrak').append('<tr><td colspan="15" class="text-center">Loading...</td></tr>');
        //             $('#button-cetak-bpm').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        //             $('#button-cetak-bpm').attr('disabled', true);
        //         },


        //         // success: function(data) {
        //         //     console.log(data);
        //         //     $('#id').val(data.kontrak.id);
        //         //     $('#nama_pekerjaan').text(data.kontrak.nama_pekerjaan);
        //         //     $('#tanggal').text(data.kontrak.tanggal);

        //         //     $('#button-cetak-bpm').html('<i class="fas fa-print"></i> Cetak');
        //         //     $('#button-cetak-bpm').attr('disabled', false);
        //         //     if ($('#detail-kontrak').find('#container-product').hasClass('d-none')) {
        //         //         $('#detail-kontrak').find('#container-product').removeClass('d-none');
        //         //         $('#detail-kontrak').find('#container-product').addClass('col-5');
        //         //         $('#detail-kontrak').find('#container-form').removeClass('col-12');
        //         //         $('#detail-kontrak').find('#container-form').addClass('col-7');
        //         //         $('#button-tambah-produk').text('Kembali');
        //         //     } else {
        //         //         $('#detail-kontrak').find('#container-product').removeClass('col-5');
        //         //         $('#detail-kontrak').find('#container-product').addClass('d-none');
        //         //         $('#detail-kontrak').find('#container-form').addClass('col-12');
        //         //         $('#detail-kontrak').find('#container-form').removeClass('col-7');
        //         //         $('#button-tambah-produk').text('Tambah Item Detail');
        //         //         clearForm();
        //         //     }
        //         //     // $('#detail-pr').find('#container-product').removeClass('d-none');
        //         //     // $('#detail-pr').find('#container-product').addClass('col-5');
        //         //     // $('#detail-pr').find('#container-form').removeClass('col-12');
        //         //     // $('#detail-pr').find('#container-form').addClass('col-7');
        //         //     var no = 1;

        //         //     if (data.kontrak.details.length == 0) {
        //         //         $('#table-kontrak').empty();
        //         //         $('#table-kontrak').append(
        //         //             '<tr><td colspan="15" class="text-center">Tidak ada produk</td></tr>'
        //         //         ); // Tambahkan pesan bahwa tidak ada produk
        //         //     } else {
        //         //         $('#table-kontrak').empty();
        //         //         $.each(data.kontrak.details, function(key, value) {
        //         //             var rowIndex = key + 1;
        //         //             var editButton =
        //         //                 '<button type="button" class="btn btn-success btn-xs mr-1" data-row-id="' +
        //         //                 value.id +
        //         //                 '" title="Edit" onclick="editRow(\'' + value.id + '\', \'' +
        //         //                 value.nomor_dokumen + '\', \'' + value.tanggal_dokumen + '\', \'' +
        //         //                 value.perihal + '\', \'' + value.keterangan + '\', \'' + value
        //         //                 .lampiran + '\')"><i class="fas fa-edit"></i></button>';

        //         //             var deleteButton =
        //         //                 '<button type="button" class="btn btn-danger btn-xs mr-1"' +
        //         //                 ' onclick="deleteDetail(' + value.id + ', \'' + value.nomor_dokumen
        //         //                 .toString() + '\')"' +
        //         //                 ' title="Delete">' +
        //         //                 '<i class="fas fa-trash"></i>' +
        //         //                 '</button>';

        //         //             var urlLampiran = "{{ asset('lampiran') }}";
        //         //             var lampiran = null;

        //         //             if (value.lampiran) {
        //         //                 lampiran = '<a href="' + urlLampiran + '/' + value.lampiran +
        //         //                     '" target="_blank"><i class="fa fa-eye"></i> Lihat</a>';
        //         //             } else {
        //         //                 lampiran = '-';
        //         //             }

        //         //             $('#table-kontrak').append('<tr><td>' + rowIndex + '</td><td>' + value
        //         //                 .nomor_dokumen + '</td><td>' +
        //         //                 moment(value.tanggal_dokumen).format('DD/MM/YYYY') + '</td><td>' +
        //         //                 value.perihal + '</td><td>' +
        //         //                 value.keterangan + '</td><td>' +
        //         //                 lampiran + '</td><td>' + editButton + deleteButton + '</td></tr>');
        //         //         });
        //         //     }
        //         // }


        //         //tanggal lebih awal, nomornya naik
        //         success: function(data) {
        //             console.log(data);
        //             $('#id').val(data.kontrak.id);
        //             $('#pekerjaan').text(data.kontrak.nama_pekerjaan);
        //             $('#no_kontrak').text(data.kontrak.nomor_kontrak);

        //             $('#button-cetak-bpm').html('<i class="fas fa-print"></i> Cetak');
        //             $('#button-cetak-bpm').attr('disabled', false);
        //             var no = 1;

        //             // Cek apakah ada detail
        //             if (data.kontrak.details.length == 0) {
        //                 $('#table-kontrak').empty();
        //                 $('#table-kontrak').append(
        //                     '<tr><td colspan="15" class="text-center">Tidak ada produk</td></tr>'
        //                 ); // Tambahkan pesan bahwa tidak ada produk
        //             } else {
        //                 // Urutkan data berdasarkan tanggal_dokumen
        //                 data.kontrak.details.sort(function(a, b) {
        //                     return new Date(a.tanggal_dokumen) - new Date(b.tanggal_dokumen);
        //                 });

        //                 $('#table-kontrak').empty();
        //                 $.each(data.kontrak.details, function(key, value) {
        //                     var rowIndex = key + 1; // Nomor urut

        //                     var editButton =
        //                         '<button type="button" class="btn btn-success btn-xs mr-1" data-row-id="' +
        //                         value.id +
        //                         '" title="Edit" onclick="editRow(\'' + value.id + '\', \'' +
        //                         value.nomor_dokumen + '\', \'' + value.tanggal_dokumen + '\', \'' +
        //                         value.perihal + '\', \'' + value.keterangan + '\', \'' + value
        //                         .lampiran + '\')"><i class="fas fa-edit"></i></button>';

        //                     var deleteButton =
        //                         '<button type="button" class="btn btn-danger btn-xs mr-1"' +
        //                         ' onclick="deleteDetail(' + value.id + ', \'' + value.nomor_dokumen
        //                         .toString() + '\')"' +
        //                         ' title="Delete">' +
        //                         '<i class="fas fa-trash"></i>' +
        //                         '</button>';

        //                     var urlLampiran = "{{ asset('lampiran') }}";
        //                     var lampiran = null;

        //                     if (value.lampiran) {
        //                         lampiran = '<a href="' + urlLampiran + '/' + value.lampiran +
        //                             '" target="_blank"><i class="fa fa-eye"></i> Lihat</a>';
        //                     } else {
        //                         lampiran = '-';
        //                     }

        //                     $('#table-kontrak').append('<tr><td>' + rowIndex + '</td><td>' +
        //                         (value.nomor_dokumen ? value.nomor_dokumen : '') + '</td><td>' +
        //                         (value.tanggal_dokumen ? moment(value.tanggal_dokumen).format(
        //                             'DD/MM/YYYY') : '') + '</td><td>' +
        //                         (value.perihal ? value.perihal : '') + '</td><td>' +
        //                         (value.keterangan ? value.keterangan : '') + '</td><td>' +
        //                         (lampiran ? lampiran : '') + '</td><td>' + editButton +
        //                         deleteButton + '</td></tr>');
        //                 });
        //             }
        //         }

        //     });
        // }
        function updateData(formData) {
            $.ajax({
                url: "{{ url('products/kontrak/update_detail') }}", // Ganti URL sesuai dengan endpoint untuk operasi insert
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#table-kontrak').append('<tr><td colspan="15" class="text-center">Loading...</td></tr>');
                    $('#button-cetak-bpm').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                    $('#button-cetak-bpm').attr('disabled', true);
                },
                success: function(data) {
                    console.log(data);
                    $('#id').val(data.kontrak.id);
                    $('#pekerjaan').text(data.kontrak.nama_pekerjaan);
                    $('#no_kontrak').text(data.kontrak.nomor_kontrak);


                    $('#button-cetak-bpm').html('<i class="fas fa-print"></i> Cetak');
                    $('#button-cetak-bpm').attr('disabled', false);

                    if ($('#detail-kontrak').find('#container-product').hasClass('d-none')) {
                        $('#detail-kontrak').find('#container-product').removeClass('d-none');
                        $('#detail-kontrak').find('#container-product').addClass('col-5');
                        $('#detail-kontrak').find('#container-form').removeClass('col-12');
                        $('#detail-kontrak').find('#container-form').addClass('col-7');
                        $('#button-tambah-produk').text('Kembali');
                    } else {
                        $('#detail-kontrak').find('#container-product').removeClass('col-5');
                        $('#detail-kontrak').find('#container-product').addClass('d-none');
                        $('#detail-kontrak').find('#container-form').addClass('col-12');
                        $('#detail-kontrak').find('#container-form').removeClass('col-7');
                        $('#button-tambah-produk').text('Tambah Item Detail');
                        clearForm();
                    }
                    var no = 1;

                    // Cek apakah ada detail
                    if (data.kontrak.details.length == 0) {
                        $('#table-kontrak').empty();
                        $('#table-kontrak').append(
                            '<tr><td colspan="15" class="text-center">Tidak ada produk</td></tr>'
                        ); // Tambahkan pesan bahwa tidak ada produk
                    } else {
                        // Urutkan data berdasarkan tanggal_dokumen
                        data.kontrak.details.sort(function(a, b) {
                            return new Date(a.tanggal_dokumen) - new Date(b.tanggal_dokumen);
                        });

                        $('#table-kontrak').empty();
                        $.each(data.kontrak.details, function(key, value) {
                            var rowIndex = key + 1; // Nomor urut

                            var editButton =
                                '<button type="button" class="btn btn-success btn-xs mr-1" data-row-id="' +
                                value.id +
                                '" title="Edit" onclick="editRow(\'' + value.id + '\', \'' +
                                value.nomor_dokumen + '\', \'' + value.tanggal_dokumen + '\', \'' +
                                value.perihal + '\', \'' + value.keterangan + '\', \'' + value
                                .lampiran + '\')"><i class="fas fa-edit"></i></button>';

                            var deleteButton =
                                '<button type="button" class="btn btn-danger btn-xs mr-1"' +
                                ' onclick="deleteDetail(' + value.id + ', \'' + value.nomor_dokumen
                                .toString() + '\')"' +
                                ' title="Delete">' +
                                '<i class="fas fa-trash"></i>' +
                                '</button>';

                            var urlLampiran = "{{ asset('lampiran') }}";
                            var lampiran = null;

                            if (value.lampiran) {
                                lampiran = '<a href="' + urlLampiran + '/' + value.lampiran +
                                    '" target="_blank"><i class="fa fa-eye"></i> Lihat</a>';
                            } else {
                                lampiran = '-';
                            }

                            $('#table-kontrak').append('<tr><td>' + rowIndex + '</td><td>' +
                                (value.nomor_dokumen ? value.nomor_dokumen : '') + '</td><td>' +
                                (value.tanggal_dokumen ? moment(value.tanggal_dokumen).format(
                                    'DD/MM/YYYY') : '') + '</td><td>' +
                                (value.perihal ? value.perihal : '') + '</td><td>' +
                                (value.keterangan ? value.keterangan : '') + '</td><td>' +
                                (lampiran ? lampiran : '') + '</td><td>' + editButton +
                                deleteButton + '</td></tr>');
                        });
                    }

                    // Tutup modal edit detail kontrak setelah pengiriman berhasil
                    $('#detail-kontrak-edit').modal(
                    'hide'); // Pastikan ini adalah ID yang benar untuk modal edit
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Tangani kesalahan jika diperlukan
                    console.error("Terjadi kesalahan:", textStatus, errorThrown);
                }
            });
        }


        // wkwkwkwk

        function deleteDetail(id, nomor_dokumen) {
            if (confirm('Apakah Anda yakin ingin menghapus Data Detail Kontrak: ' + nomor_dokumen + '?')) {
                $.ajax({
                    url: 'detail_kontrak/' + id + '/delete',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Pastikan token CSRF sudah disediakan di dalam template Anda
                    },
                    success: function(result) {
                        // Menghapus baris dari tabel
                        $('button[data-id="' + result.deletedId + '"]').closest('tr').remove();
                        // alert(result.success); // Tampilkan pesan sukses
                        // alert("Nilai id_pr adalah: " + id_pr);
                        // alert(result.id_pr);
                        $.ajax({
                            url: "{{ url('products/kontrak_detail') }}" + "/" + result.kontrak_id,
                            type: "GET",
                            dataType: "json",
                            beforeSend: function() {
                                $('#table-kontrak').append(
                                    '<tr><td colspan="15" class="text-center">Loading...</td></tr>'
                                );
                                $('#button-cetak-bpm').html(
                                    '<i class="fas fa-spinner fa-spin"></i> Loading...');
                                $('#button-cetak-bpm').attr('disabled', true);
                            },

                            // success: function(data) {
                            //     console.log(data);
                            //     $('#id').val(data.kontrak.id);
                            //     $('#nama_pekerjaan').text(data.kontrak.nama_pekerjaan);
                            //     $('#tanggal').text(data.kontrak.tanggal);

                            //     $('#button-cetak-bpm').html('<i class="fas fa-print"></i> Cetak');
                            //     $('#button-cetak-bpm').attr('disabled', false);
                            //     var no = 1;

                            //     if (data.kontrak.details.length == 0) {
                            //         $('#table-kontrak').empty();
                            //         $('#table-kontrak').append(
                            //             '<tr><td colspan="15" class="text-center">Tidak ada produk</td></tr>'
                            //         ); // Tambahkan pesan bahwa tidak ada produk
                            //     } else {
                            //         $('#table-kontrak').empty();
                            //         $.each(data.kontrak.details, function(key, value) {
                            //             var rowIndex = key + 1;
                            //             var editButton =
                            //                 '<button type="button" class="btn btn-success btn-xs mr-1" data-row-id="' +
                            //                 value.id +
                            //                 '" title="Edit" onclick="editRow(\'' + value
                            //                 .id + '\', \'' +
                            //                 value.nomor_dokumen + '\', \'' + value
                            //                 .tanggal_dokumen + '\', \'' +
                            //                 value.perihal + '\', \'' + value.keterangan +
                            //                 '\', \'' + value
                            //                 .lampiran +
                            //                 '\')"><i class="fas fa-edit"></i></button>';

                            //             var deleteButton =
                            //                 '<button type="button" class="btn btn-danger btn-xs mr-1"' +
                            //                 ' onclick="deleteDetail(' + value.id + ', \'' +
                            //                 value.nomor_dokumen
                            //                 .toString() + '\')"' +
                            //                 ' title="Delete">' +
                            //                 '<i class="fas fa-trash"></i>' +
                            //                 '</button>';

                            //             var urlLampiran = "{{ asset('lampiran') }}";
                            //             var lampiran = null;

                            //             if (value.lampiran) {
                            //                 lampiran = '<a href="' + urlLampiran + '/' +
                            //                     value.lampiran +
                            //                     '" target="_blank"><i class="fa fa-eye"></i> Lihat</a>';
                            //             } else {
                            //                 lampiran = '-';
                            //             }

                            //             $('#table-kontrak').append('<tr><td>' + rowIndex +
                            //                 '</td><td>' + value
                            //                 .nomor_dokumen + '</td><td>' +
                            //                 moment(value.tanggal_dokumen).format(
                            //                     'DD/MM/YYYY') + '</td><td>' + value
                            //                 .perihal + '</td><td>' +
                            //                 value.keterangan + '</td><td>' +
                            //                 lampiran + '</td><td>' + editButton +
                            //                 deleteButton + '</td></tr>');
                            //         });
                            //     }
                            // }

                            //tanggal lebih awal, nomornya naik
                            success: function(data) {
                                console.log(data);
                                $('#id').val(data.kontrak.id);
                                $('#pekerjaan').text(data.kontrak.nama_pekerjaan);
                                $('#no_kontrak').text(data.kontrak.nomor_kontrak);

                                $('#button-cetak-bpm').html('<i class="fas fa-print"></i> Cetak');
                                $('#button-cetak-bpm').attr('disabled', false);
                                var no = 1;

                                // Cek apakah ada detail
                                if (data.kontrak.details.length == 0) {
                                    $('#table-kontrak').empty();
                                    $('#table-kontrak').append(
                                        '<tr><td colspan="15" class="text-center">Tidak ada produk</td></tr>'
                                    ); // Tambahkan pesan bahwa tidak ada produk
                                } else {
                                    // Urutkan data berdasarkan tanggal_dokumen
                                    data.kontrak.details.sort(function(a, b) {
                                        return new Date(a.tanggal_dokumen) - new Date(b
                                            .tanggal_dokumen);
                                    });

                                    $('#table-kontrak').empty();
                                    $.each(data.kontrak.details, function(key, value) {
                                        var rowIndex = key + 1; // Nomor urut

                                        var editButton =
                                            '<button type="button" class="btn btn-success btn-xs mr-1" data-row-id="' +
                                            value.id +
                                            '" title="Edit" onclick="editRow(\'' + value
                                            .id + '\', \'' +
                                            value.nomor_dokumen + '\', \'' + value
                                            .tanggal_dokumen + '\', \'' +
                                            value.perihal + '\', \'' + value.keterangan +
                                            '\', \'' + value
                                            .lampiran +
                                            '\')"><i class="fas fa-edit"></i></button>';

                                        var deleteButton =
                                            '<button type="button" class="btn btn-danger btn-xs mr-1"' +
                                            ' onclick="deleteDetail(' + value.id + ', \'' +
                                            value.nomor_dokumen
                                            .toString() + '\')"' +
                                            ' title="Delete">' +
                                            '<i class="fas fa-trash"></i>' +
                                            '</button>';

                                        var urlLampiran = "{{ asset('lampiran') }}";
                                        var lampiran = null;

                                        if (value.lampiran) {
                                            lampiran = '<a href="' + urlLampiran + '/' +
                                                value.lampiran +
                                                '" target="_blank"><i class="fa fa-eye"></i> Lihat</a>';
                                        } else {
                                            lampiran = '-';
                                        }

                                        $('#table-kontrak').append('<tr><td>' + rowIndex +
                                            '</td><td>' + value
                                            .nomor_dokumen + '</td><td>' +
                                            moment(value.tanggal_dokumen).format(
                                                'DD/MM/YYYY') + '</td><td>' +
                                            value.perihal + '</td><td>' +
                                            value.keterangan + '</td><td>' +
                                            lampiran + '</td><td>' + editButton +
                                            deleteButton + '</td></tr>');
                                    });
                                }
                            }


                        });













                    },
                    error: function(err) {
                        alert('Terjadi kesalahan saat menghapus item');
                    }
                });



            }
        }


        $('#detail-kontrak').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var data = button.data('detail');
            console.log(data);
            lihatKONTRAK(data);
        });



        function lihatKONTRAK(data) {
            emptyTableProducts();
            clearForm()
            $('#modal-title').text("Detail KONTRAK");
            $('#button-save').text("Cetak");
            resetForm();
            $('#button-tambah-produk').text('Tambah Item Detail');
            $('#id').val(data.id);
            $('#pekerjaan').text(data.nama_pekerjaan);
            $('#no_kontrak').text(data.nomor_kontrak);

            $('#proyek_id_val').val(data.proyek_id);
            $('#kontrak_id').val(data.id);
            $('#table-kontrak').empty();

            //#button-tambah-produk disabled when editable is false
            if (data.editable == 0) {
                $('#button-tambah-produk').attr('disabled', true);
            } else {
                $('#button-tambah-produk').attr('disabled', false);
            }

            $.ajax({
                url: "{{ url('products/kontrak_detail') }}" + "/" + data.id,
                type: "GET",
                dataType: "json",
                beforeSend: function() {
                    $('#table-kontrak').append('<tr><td colspan="15" class="text-center">Loading...</td></tr>');
                    $('#button-cetak-bpm').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                    $('#button-cetak-bpm').attr('disabled', true);
                },

                // success: function(data) {
                //     console.log(data);
                //     $('#id').val(data.kontrak.id);
                //     $('#nama_pekerjaan').text(data.kontrak.nama_pekerjaan);
                //     $('#tanggal').text(data.kontrak.tanggal);

                //     $('#button-cetak-bpm').html('<i class="fas fa-print"></i> Cetak');
                //     $('#button-cetak-bpm').attr('disabled', false);
                //     var no = 1;

                //     if (data.kontrak.details.length == 0) {
                //         $('#table-kontrak').empty();
                //         $('#table-kontrak').append(
                //             '<tr><td colspan="15" class="text-center">Tidak ada produk</td></tr>'
                //         ); // Tambahkan pesan bahwa tidak ada produk
                //     } else {
                //         $('#table-kontrak').empty();
                //         $.each(data.kontrak.details, function(key, value) {
                //             var rowIndex = key + 1;
                //             var editButton =
                //                 '<button type="button" class="btn btn-success btn-xs mr-1" data-row-id="' +
                //                 value.id +
                //                 '" title="Edit" onclick="editRow(\'' + value.id + '\', \'' +
                //                 value.nomor_dokumen + '\', \'' + value.tanggal_dokumen + '\', \'' +
                //                 value.perihal + '\', \'' + value.keterangan + '\', \'' + value
                //                 .lampiran + '\')"><i class="fas fa-edit"></i></button>';

                //             var deleteButton =
                //                 '<button type="button" class="btn btn-danger btn-xs mr-1"' +
                //                 ' onclick="deleteDetail(' + value.id + ', \'' + value.nomor_dokumen
                //                 .toString() + '\')"' +
                //                 ' title="Delete">' +
                //                 '<i class="fas fa-trash"></i>' +
                //                 '</button>';

                //             var urlLampiran = "{{ asset('lampiran') }}";
                //             var lampiran = null;

                //             if (value.lampiran) {
                //                 lampiran = '<a href="' + urlLampiran + '/' + value.lampiran +
                //                     '" target="_blank"><i class="fa fa-eye"></i> Lihat</a>';
                //             } else {
                //                 lampiran = '-';
                //             }

                //             $('#table-kontrak').append('<tr><td>' + rowIndex + '</td><td>' + value
                //                 .nomor_dokumen + '</td><td>' +
                //                 moment(value.tanggal_dokumen).format('DD/MM/YYYY') + '</td><td>' +
                //                 value.perihal + '</td><td>' +
                //                 value.keterangan + '</td><td>' +
                //                 lampiran + '</td><td>' + editButton + deleteButton + '</td></tr>');
                //         });
                //     }
                // }


                //tanggal lebih awal, nomornya naik
                success: function(data) {
                    console.log(data);
                    $('#id').val(data.kontrak.id);
                    $('#pekerjaan').text(data.kontrak.nama_pekerjaan);
                    $('#no_kontrak').text(data.kontrak.nomor_kontrak);

                    $('#button-cetak-bpm').html('<i class="fas fa-print"></i> Cetak');
                    $('#button-cetak-bpm').attr('disabled', false);
                    var no = 1;

                    // Cek apakah ada detail
                    if (data.kontrak.details.length == 0) {
                        $('#table-kontrak').empty();
                        $('#table-kontrak').append(
                            '<tr><td colspan="15" class="text-center">Tidak ada produk</td></tr>'
                        ); // Tambahkan pesan bahwa tidak ada produk
                    } else {
                        // Urutkan data berdasarkan tanggal_dokumen
                        data.kontrak.details.sort(function(a, b) {
                            return new Date(a.tanggal_dokumen) - new Date(b.tanggal_dokumen);
                        });

                        $('#table-kontrak').empty();
                        $.each(data.kontrak.details, function(key, value) {
                            var rowIndex = key + 1; // Nomor urut

                            var editButton =
                                '<button type="button" class="btn btn-success btn-xs mr-1" data-row-id="' +
                                value.id +
                                '" title="Edit" onclick="editRow(\'' + value.id + '\', \'' +
                                value.nomor_dokumen + '\', \'' + value.tanggal_dokumen + '\', \'' +
                                value.perihal + '\', \'' + value.keterangan + '\', \'' + value
                                .lampiran + '\')"><i class="fas fa-edit"></i></button>';

                            var deleteButton =
                                '<button type="button" class="btn btn-danger btn-xs mr-1"' +
                                ' onclick="deleteDetail(' + value.id + ', \'' + value.nomor_dokumen
                                .toString() + '\')"' +
                                ' title="Delete">' +
                                '<i class="fas fa-trash"></i>' +
                                '</button>';

                            var urlLampiran = "{{ asset('lampiran') }}";
                            var lampiran = null;

                            if (value.lampiran) {
                                lampiran = '<a href="' + urlLampiran + '/' + value.lampiran +
                                    '" target="_blank"><i class="fa fa-eye"></i> Lihat</a>';
                            } else {
                                lampiran = '-';
                            }

                            $('#table-kontrak').append('<tr><td>' + rowIndex + '</td><td>' + value
                                .nomor_dokumen + '</td><td>' +
                                moment(value.tanggal_dokumen).format('DD/MM/YYYY') + '</td><td>' +
                                value.perihal + '</td><td>' +
                                value.keterangan + '</td><td>' +
                                lampiran + '</td><td>' + editButton + deleteButton + '</td></tr>');
                        });
                    }
                }


            });
        }




        function editRow(id, nomor_dokumen, tanggal_dokumen, perihal, keterangan, lampiran) {
            console.log(id, nomor_dokumen, tanggal_dokumen, perihal, keterangan, lampiran);
            resetForm();
            $('#modal-title').text("Edit Detail");
            $('#button-update-kontrak').text("Simpan");
            $('#button-update-kontrak').off('click');
            $('#button-update-kontrak').on('click', function() {
                // Tangani event klik di sini
                KONTRAKupdate();
            });

            $('#id').val(id);
            // $('#kode_tempat').val(data.kode_tempat);
            $('#nomor_dokumen').val(nomor_dokumen) // Mengosongkan nilai input dengan ID 'kode_material'
            $('#tanggal_dokumen').val(tanggal_dokumen) // Mengosongkan nilai input dengan ID 'kode_material'
            $('#perihal').val(perihal); // Mengosongkan nilai input dengan ID 'desc_material'
            $('#keterangan').val(keterangan); // Mengosongkan nilai input dengan ID 'spek'

            // Tampilkan file lampiran jika ada
            if (lampiran) {
                // Tampilkan nama file asli yang tersimpan
                let fileName = lampiran.split('/').pop(); // Ambil nama file dari path
                $('#lampiran-info').text(`Lampiran Sebelumnya: ${fileName}`);
                $('#lampiran-info').show(); // Tampilkan info lampiran
            } else {
                $('#lampiran-info').text('Tidak ada lampiran sebelumnya');
                $('#lampiran-info').show(); // Tampilkan info bahwa tidak ada lampiran
            }

            // $('#lampiran').val(lampiran); // Mengosongkan nilai input dengan ID 'p3'
            // $('#lampiran-label').text(lampiran);
            $('#keterangan').val(keterangan);
            if (keterangan === 'null') {
                $('#keterangan').val('');
                // alert(keterangan);
            }
            if (nomor_dokumen === 'null') {
                $('#nomor_dokumen').val('');
                // alert(keterangan);
            }


            if ($('#detail-kontrak').find('#container-product').hasClass('d-none')) {
                $('#detail-kontrak').find('#container-product').removeClass('d-none');
                $('#detail-kontrak').find('#container-product').addClass('col-5');
                $('#detail-kontrak').find('#container-form').removeClass('col-12');
                $('#detail-kontrak').find('#container-form').addClass('col-7');
                $('#button-tambah-produk').text('Kembali');
            } else {
                $('#detail-kontrak').find('#container-product').removeClass('col-5');
                $('#detail-kontrak').find('#container-product').addClass('d-none');
                $('#detail-kontrak').find('#container-form').addClass('col-12');
                $('#detail-kontrak').find('#container-form').removeClass('col-7');
                $('#button-tambah-produk').text('Tambah Item Detail');
                clearForm();
            }
        }



        // Handler klik tombol Delete
        $(document).on('click', '.btnDelete', function() {
            var id = $(this).data('id');
            if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                $.ajax({
                    url: 'products/kontrak/delete_detail/{id}' + id,
                    type: 'DELETE',
                    success: function(result) {
                        // Menghapus baris dari tabel
                        $('button[data-id="' + id + '"]').closest('tr').remove();
                    },
                    error: function(err) {
                        alert('Terjadi kesalahan saat menghapus item');
                    }
                });
            }
        });

        // $(document).on('submit', '#editForm', function(e) {
        //     e.preventDefault();
        //     var id = $('#editForm').data('id');
        //     var formData = $(this).serialize();

        //     $.ajax({
        //         url: '/products/' + id,
        //         type: 'PUT',
        //         data: formData,
        //         success: function(result) {
        //             // Perbarui baris tabel dengan data baru
        //             var row = $('button[data-id="' + id + '"]').closest('tr');
        //             row.find('td:eq(2)').text($('#editKodeMaterial').val());
        //             row.find('td:eq(3)').text($('#editUraian').val());
        //             row.find('td:eq(4)').text($('#editSpek').val());
        //             row.find('td:eq(5)').text($('#editQty').val());
        //             row.find('td:eq(6)').text($('#editSatuan').val());
        //             row.find('td:eq(7)').text($('#editWaktu').val());
        //             row.find('td:eq(8)').text($('#editLampiran').val());
        //             row.find('td:eq(9)').text($('#editKeterangan').val());

        //             $('#editModal').modal('hide');
        //         },
        //         error: function(err) {
        //             alert('Terjadi kesalahan saat mengubah item');
        //         }
        //     });
        // });

        function detailKONTRAK(data) {
            $('#modal-title').text("Edit Request");
            $('#button-save').text("Simpan");
            resetForm();
            $('#save_id').val(data.id);
            $('#nama_pekerjaan').val(data.nama_pekerjaan);
            $('#tanggal').val(data.tanggal);
            $('#proyek_id').val(data.proyek_id);
            // $('#dasar_bpm').val(data.dasar_bpm);
            // alert(proyek_id)
        }



        function barcode(code) {
            $("#pcode_print").val(code);
            $("#barcode").attr("src", "/products/barcode/" + code);
        }

        function printBarcode() {
            var code = $("#pcode_print").val();
            var url = "/products/barcode/" + code + "?print=true";
            window.open(url, 'window_print', 'menubar=0,resizable=0');
        }

        function deleteKONTRAK(data) {
            $('#delete_id').val(data.id);
        }

        $("#download-template").click(function() {
            $.ajax({
                url: '/downloads/template_import_product.xls',
                type: "GET",
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data) {
                    var a = document.createElement('a');
                    var url = window.URL.createObjectURL(data);
                    a.href = url;
                    a.download = "template_import_product.xls";
                    document.body.append(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                }
            });
        });

        function download(type) {
            window.location.href = "{{ route('products') }}?search={{ Request::get('search') }}&dl=" + type;
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
