@extends('layouts.main')
@section('title', __('NEGO'))
@section('custom-css')
    <style>
        /* Important part */
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
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-NEGO"
                        onclick="addNEGO()"><i class="fas fa-plus"></i> Add New NEGO</button>
                    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#import-product" onclick="importProduct()"><i class="fas fa-file-excel"></i> Import Product (Excel)</button> -->
                    <!-- <button type="button" class="btn btn-primary" onclick="download('xls')"><i class="fas fa-file-excel"></i> Export Product (XLS)</button> -->
                    {{-- <div class="card-tools">
                        <form>
                            <div class="input-group input-group">
                                <input type="text" class="form-control" name="q" placeholder="Search">
                                <input type="hidden" name="category" value="{{ Request::get('category') }}">
                                <input type="hidden" name="sort" value="{{ Request::get('sort') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div> --}}
                </div>

                <div class="card-body">
                    <div class="table-responsive">

                        {{-- Filter by Nomor Po dan Tanggal --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter-nego-no">Filter Nomor Negosiasi</label>
                                    <input type="text" class="form-control" id="filter-nego-no"
                                        placeholder="Masukkan Nomor nego">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter-nego-date">Filter Tanggal Negosiasi</label>
                                    <input type="date" class="form-control" id="filter-nego-date">
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
                                    <th>{{ __('Nomor NEGO') }}</th>
                                    <th>{{ __('Nomor PR') }}</th>
                                    <th>{{ __('Lampiran') }}</th>
                                    <th>{{ __('Perihal') }}</th>
                                    <th>{{ __('Tanggal NEGO') }}</th>
                                    <th>{{ __('Batas NEGO') }}</th>
                                    <th>{{ __('Vendor') }}</th>
                                    <th>{{ __('No Jawaban Vendor') }}</th>
                                    <th>{{ __('Harga Franco') }}</th>
                                    <th>{{ __('Aksi') }}</th>
                                    {{-- <th>{{ __('Penerima') }}</th> --}}

                                </tr>
                            </thead>
                            <tbody>
                                @if (count($negoes) > 0)
                                    @foreach ($negoes as $key => $d)
                                        @php
                                            // $penerima = $d->penerima;
                                            // $penerima = json_decode($penerima);
                                            // $penerima = implode(', ', $penerima);
                                            $vendor = $d->vendor;
                                            $data = [
                                                'no' => $negoes->firstItem() + $key,
                                                'nomor_nego' => $d->nomor_nego,
                                                'id_pr' => $d->id_pr,
                                                'nomor_pr' => $d->nomor_pr,
                                                'lampiran' => $d->lampiran,
                                                'vendor_id' => $d->vendor_id,
                                                'vendor' => $vendor,
                                                'perihal' => $d->perihal,
                                                'tanggal' => date('d/m/Y', strtotime($d->tanggal_nego)),
                                                'batas' => date('d/m/Y', strtotime($d->batas_nego)),
                                                'penerima' => $d->penerima,
                                                'alamat' => $d->alamat,
                                                'no_jawaban_vendor' => $d->no_jawaban_vendor,
                                                'franco' => $d->franco,
                                                'keterangan_nego' => $d->keterangan_nego,
                                                'id' => $d->id,
                                                'penerima_asli' => $d->penerima,
                                                'alamat_asli' => $d->alamat,
                                            ];
                                        @endphp

                                        <tr>
                                            <td class="text-center"><input type="checkbox" name="hapus[]"
                                                    value="{{ $d->id }}"></td>
                                            <td class="text-center">{{ $data['no'] }}</td>
                                            <td class="text-center">{{ $data['nomor_nego'] }}</td>
                                            <td class="text-center">{{ $data['nomor_pr'] }}</td>

                                            {{-- membuat lampiran lebih dari 1 --}}
                                            <td class="text-center">
                                                @php
                                                    // Memisahkan lampiran berdasarkan koma
                                                    $lampiran = explode(',', $d->lampiran);
                                                @endphp

                                                @if (!empty($lampiran) && is_array($lampiran) && count($lampiran) > 0)
                                                    @foreach ($lampiran as $index => $file)
                                                        @if (!empty($file))
                                                            <a href="{{ asset('public/lampiran/' . trim($file)) }}"
                                                                target="_blank">
                                                                <i class="fa fa-eye"></i> Lihat
                                                            </a>
                                                            @if ($index < count($lampiran) - 1)
                                                                <br>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            {{-- membuat lampiran lebih dari 1 --}}


                                            <td class="text-center">{{ $data['perihal'] }}</td>
                                            <td class="text-center">{{ $data['tanggal'] }}</td>
                                            <td class="text-center">{{ $data['batas'] }}</td>
                                            <td class="text-center">{{ $data['vendor'] }}</td>
                                            <td class="text-center">{{ $data['no_jawaban_vendor'] }}</td>
                                            <td class="text-center">{{ $data['franco'] }}</td>
                                            {{-- <td class="text-center">{{ $data['penerima'] }}</td> --}}
                                            <td class="text-center">
                                                <button title="Edit NEGO" type="button" class="btn btn-success btn-xs"
                                                    data-toggle="modal" data-target="#add-NEGO"
                                                    onclick="editNEGO({{ json_encode($data) }})"><i
                                                        class="fas fa-edit"></i></button>

                                                <button title="Lihat Detail" type="button" data-toggle="modal"
                                                    data-target="#detail-nego" class="btn-lihat btn btn-info btn-xs"
                                                    data-detail="{{ json_encode($data) }}"><i
                                                        class="fas fa-list"></i></button>
                                                @if (Auth::user()->role == 0 || Auth::user()->role == 1)
                                                    <button title="Hapus NEGO" type="button" class="btn btn-danger btn-xs"
                                                        data-toggle="modal" data-target="#delete-nego"
                                                        onclick="deletenego({{ json_encode($data) }})"><i
                                                            class="fas fa-trash"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="text-center">
                                        <td colspan="12">{{ __('No data.') }}</td>
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
                {{-- {{ $sjn->appends(request()->except('page'))->links('pagination::bootstrap-4') }} --}}
            </div>
        </div>

        {{-- modal --}}
        <div class="modal fade" id="add-NEGO">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="modal-title" class="modal-title">{{ __('Add New NEGO') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="save" action="{{ route('nego.store') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="save_id" name="id">
                            <input type="hidden" id="id_pr" name="id_pr">
                            <input type="hidden" id="lampiran_awal" name="lampiran_awal">
                            <input type="hidden" id="nama_lampiran" name="nama_lampiran">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="nomor_nego"
                                            class="col-sm-4 col-form-label">{{ __('Nomor Nego') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="nomor_nego"
                                                name="nomor_nego">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="nomor_pr"
                                            class="col-sm-4 col-form-label">{{ __('Nomor PR') }}</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="nomor_pr" id="nomor_pr">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="perihal" class="col-sm-4 col-form-label">{{ __('Perihal') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="perihal" name="perihal">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="tanggal_nego"
                                            class="col-sm-4 col-form-label">{{ __('Tanggal Nego') }}</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control" id="tanggal_nego"
                                                name="tanggal_nego">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="batas_nego"
                                            class="col-sm-4 col-form-label">{{ __('Batas Nego') }}</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control" id="batas_nego"
                                                name="batas_nego">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">

                                    <div class="form-group row">
                                        <label for="no_jawaban_vendor"
                                            class="col-sm-4 col-form-label">{{ __('Nomor Jawaban Vendor') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="no_jawaban_vendor"
                                                name="no_jawaban_vendor">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="franco" class="col-sm-4 col-form-label">{{ __('Franco') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="franco" name="franco">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="keterangan_nego"
                                            class="col-sm-4 col-form-label">{{ __('Keterangan') }}</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" id="keterangan_nego" name="keterangan_nego" rows="4"
                                                placeholder="contoh penulisan                            Delivery:2(dua) minggu setelah PO setelah itu enter untuk nomor selanjutnya"></textarea>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <hr>

                            {{-- <h6>Penerima -- </h6>

                            <div id="penerima-row">

                            </div>

                            <a id="tambah" style="cursor: pointer">Tambah Penerima</a> --}}

                            <input type="text" id="data_lampiran" value="--" style="display: none">
                            <input type="text" id="data_vendor" value="--" style="display: none">
                            <h6 id="lampiran_text">Lampiran</h6>

                            <div id="lampiran-row">

                            </div>

                            <a id="tambah-lampiran" style="cursor: pointer">Tambah Lampiran</a>
                            <hr>

                            <h6 id="vendor_text">Vendor -- </h6>

                            <div id="vendor-row">

                            </div>

                            <a id="tambah" style="cursor: pointer">Tambah vendor</a>

                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button id="button-save" type="button" class="btn btn-primary"
                            onclick="setSaveIdAndSubmit();">{{ __('Tambahkan') }}</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal lihat detail --}}
        <div class="modal fade" id="detail-nego">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="modal-title" class="modal-title">{{ __('Detail NEGO') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="row">
                                <form id="cetak-nego" method="GET" action="{{ route('nego.print') }}"
                                    target="_blank">
                                    <input type="hidden" name="nego_id" id="nego_id">
                                </form>
                                <div class="col-12" id="container-form">
                                    <button id="button-cetak-nego" type="button" class="btn btn-primary"
                                        onclick="document.getElementById('cetak-nego').submit();">{{ __('Cetak') }}</button>
                                    <table class="align-top w-100">
                                        {{-- <tr>
                                            <td style="width: 3%;"><b>ID PR</b></td>
                                            <td style="width:2%">:</td>
                                            <td style="width: 55%"><span id="id_pr2"></span></td>
                                        </tr> --}}
                                        <tr>
                                            <td style="width: 3%;"><b>No.NEGO</b></td>
                                            <td style="width:2%">:</td>
                                            <td style="width: 55%"><span id="no_surat"></span></td>
                                        </tr>
                                        <tr>
                                            <td><b>Penerima</b></td>
                                            <td>:</td>
                                            <td><span id="nama_penerima"></span></td>
                                        </tr>
                                        <tr>
                                            <td><b>tanggal</b></td>
                                            <td>:</td>
                                            <td><span id="tgl_nego"></span></td>
                                        </tr>
                                        <tr>
                                            <td><b>Produk</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <button id="button-tambah-produk" type="button"
                                                    class="btn btn-info mb-3">{{ __('Tambah Produk') }}</button>
                                            </td>
                                            {{-- <button title="Edit SPPH" type="button" class="btn btn-success btn-xs"
                                            data-toggle="modal" data-target="#add-SPPH"
                                            onclick="editSPPH({{ json_encode($data) }})"> --}}
                                        </tr>
                                    </table>
                                    <div class="table-responsive">
                                        {{-- <table class="table table-bordered">
                                            <thead>
                                                <th>NO</th>
                                                <th>Nama Barang</th>
                                                <th>Spesifikasi</th>
                                                <th>QTY</th>
                                                <th>Satuan</th>
                                                <th>Harga Satuan Rp.</th>
                                                <th>Harga Total</th>
                                                <th>Aksi</th>
                                            </thead>

                                            <tbody id="table-nego">
                                            </tbody>
                                        </table> --}}
                                        <table class="table table-bordered" style="text-align: center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">NO</th>
                                                    <th rowspan="2">Nama Barang</th>
                                                    <th rowspan="2">Spesifikasi</th>
                                                    <th rowspan="2">QTY</th>
                                                    <th rowspan="2">Satuan</th>
                                                    <th colspan="2">Penawaran Vendor</th>
                                                    <th colspan="2">Negosiasi PT.IMSS</th>
                                                    <th rowspan="2">Aksi</th>
                                                </tr>
                                                <tr>
                                                    <th>Harga Satuan Rp.</th>
                                                    <th>Harga Total</th>
                                                    <th>Harga Satuan Rp.</th>
                                                    <th>Harga Total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table-nego">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-0 d-none" id="container-product">
                                    {{-- <div class="card">
                                        <div class="card-body">
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
                                    <div id="form" class="card">
                                        <div class="card-body">
                                            <!-- <button type="button" class="btn btn-primary mb-3"
                                                onclick="addToDetails()"></i>Tambah Pilihan</button> -->
                                                <button id="btn-save-then-add" type="button" class="btn btn-primary mb-3">Tambah Pilihan</button>

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
                                                        <th>No</th>
                                                        <th>Pilih</th>
                                                        <th>Deskripsi</th>
                                                        <th>Spesifikasi</th>
                                                        <th>QTY</th>
                                                        <th>QTY</th>
                                                        <th>Sat</th>
                                                        <th>NO PR</th>
                                                        <th>No SPPH</th>
                                                        <th>Proyek</th>

                                                    </tr>
                                                </thead>
                                                <tbody id='detail-material'>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal delete sjn --}}
        <div class="modal fade" id="delete-nego">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="modal-title" class="modal-title">{{ __('Delete NEGO') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="delete" action="{{ route('nego.destroy') }}" method="post">
                            @csrf
                            @method('delete')
                            <input type="hidden" id="delete_id" name="id">
                        </form>
                        <div>
                            <p>Anda yakin ingin menghapus NEGOSIASI <span id="pcode" class="font-weight-bold"></span>?
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

        //function delete checkbox
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
                    url: 'nego-imss/hapus-multiple',
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

            //Filter by Nomor dan tgl Nego
            $(document).ready(function() {

                $('#clear-filter').on('click', function() {
                    $('#filter-nego-no, #filter-nego-date').val('');
                    filterTable();
                });


                $("#nomor_pr").select2({
                    placeholder: 'Pilih PR',
                    width: '100%',
                    data: [{
                        id: 'all',
                        text: 'Semua'
                    }],
                    ajax: {
                        url: "{{ route('nopr.index') }}",
                        processResults: function({
                            data
                        }) {
                            // Menggabungkan opsi "Semua" dengan data dari database
                            let results = $.map(data, function(item) {
                                return {
                                    id: item.no_pr,
                                    ids: item.id,
                                    text: item.no_pr,
                                }
                            });
                            return {
                                results: results
                            }
                        }
                    }
                })
                $('#nomor_pr').on('select2:select', function(e) {
                    var selectedData = e.params.data;
                    $("#id_pr").val(selectedData.ids);
                    // alert($("#id_pr").val());
                });


                $('#filter-nego-no, #filter-nego-date').on('keyup change', function() {
                    filterTable();
                });

                function filterTable() {
                    var filterNoNEGO = $('#filter-nego-no').val().toUpperCase();
                    var filterDateNEGO = $('#filter-nego-date').val();

                    $('table tbody tr').each(function() {
                        var noNEGO = $(this).find('td:nth-child(3)').text().toUpperCase();
                        var dateNEGO = $(this).find('td:nth-child(7)').text();
                        var id = $(this).find('td:nth-child(1)')
                            .text(); // Ubah indeks kolom ke indeks ID PO jika perlu

                        // Ubah string tanggal ke objek Date untuk perbandingan
                        var dateParts = dateNEGO.split("/");
                        var negoDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[
                            0]); // Format: tahun, bulan, tanggal

                        // Ubah string filterDatePO ke objek Date
                        var filterDateParts = filterDateNEGO.split("-");
                        var filterNEGODate = new Date(filterDateParts[0], filterDateParts[1] - 1,
                            filterDateParts[
                                2]); // Format: tahun, bulan, tanggal

                        if ((noNEGO.indexOf(filterNoNEGO) > -1 || filterNoNEGO === '') &&
                            (negoDate.getTime() === filterNEGODate.getTime() || filterDateNEGO === '')) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
            });
        //End Filter by Nomor dan tgl SPPH


        function resetForm() {
            $('#save').trigger("reset");
            $('#barcode_preview_container').hide();
        }

        function addNEGO() {
            $('#modal-title').text("Add New NEGO");
            $('#button-save').text("Tambahkan");
            $('#save_id').val("");
            resetForm();
        }

        //fungsi generate alamat

        // function generateNamaAlamat(data) {
        //     if (data) {
        //         $('#penerima-row').empty();
        //         var length = data.length;

        //         data.map((item, index) => {
        //             const counter = index + 1
        //             var formGroup =
        //                 '<div class="group">' +
        //                 '<div class="form-group row">' +
        //                 '<label for="penerima' + counter + '" class="col-sm-4 col-form-label">Penerima ' + counter +
        //                 '</label>' +
        //                 '<div class="col-sm-8 d-flex align-items-center">' +
        //                 '<input type="text" class="form-control" id="penerima' + counter +
        //                 '" name="penerima[]" value="' + item.penerima + '">' +
        //                 //remove button
        //                 '<button type="button" class="ml-2 btn btn-danger btn-sm" onclick="removeNamaAlamat(' +
        //                 counter +
        //                 ')"><i class="fas fa-trash"></i></button>' +
        //                 '</div>' +
        //                 '</div>' +
        //                 '<div class="form-group row">' +
        //                 '<label for="alamat' + counter + '" class="col-sm-4 col-form-label">Alamat ' + counter +
        //                 '</label>' +
        //                 '<div class="col-sm-8">' +
        //                 '<textarea class="form-control" id="alamat' + counter +
        //                 '" name="alamat[]" rows="3">' + item.alamat + '</textarea>' +
        //                 '</div>' +
        //                 '</div>' +
        //                 '<hr/>' +
        //                 '</div>';
        //             $("#penerima-row").append(formGroup);
        //         })
        //     } else {
        //         var length = $("#penerima-row").children().length;
        //         var counter = length + 1;

        //         var formGroup =
        //             '<div class="group">' +
        //             '<div class="form-group row">' +
        //             '<label for="penerima' + counter + '" class="col-sm-4 col-form-label">Penerima ' + counter +
        //             '</label>' +
        //             '<div class="col-sm-8 d-flex align-items-center">' +
        //             '<input type="text" class="form-control" id="penerima' + counter + '" name="penerima[]">' +
        //             //remove button
        //             '<button type="button" class="ml-2 btn btn-danger btn-sm" onclick="removeNamaAlamat(' + counter +
        //             ')"><i class="fas fa-trash"></i></button>' +
        //             '</div>' +
        //             '</div>' +
        //             '<div class="form-group row">' +
        //             '<label for="alamat' + counter + '" class="col-sm-4 col-form-label">Alamat ' + counter + '</label>' +
        //             '<div class="col-sm-8">' +
        //             '<textarea class="form-control" id="alamat' + counter + '" name="alamat[]"></textarea>' +
        //             '</div>' +
        //             '</div>' +
        //             '<hr/>' +
        //             '</div>';
        //         $("#penerima-row").append(formGroup);
        //     }
        // }


        //Fungsi tambah lampiran & Vendor
        function generateLampiranList(data) {
            if (data) {
                $('#lampiran-row').empty();
                var length = data.length;
                data.map((item, index) => {
                    const counter = index + 1
                    var formGroup =
                        '<div class="group">' +
                        '<div class="form-group custom-file row">' +
                        '<label for="lampiran' + counter + '" class="col-sm-4 col-form-label">Lampiran ' + counter +
                        '</label>' +
                        '<div class="col-sm-8 d-flex align-items-center ">' +
                        '<input type="file" class="form-control custom-file-input" id="lampiran' + counter +
                        '" name="lampiran[]" value="' + item + '">' +
                        '<button type="button" class="ml-2 btn btn-danger btn-sm" onclick="removeLampiran(' +
                        counter + ')"><i class="fas fa-trash"></i></button>' +
                        '</div>' +
                        '</div>' +
                        // '<hr/>' +
                        '</div>';
                    $("#lampiran-row").append(formGroup);
                })
            } else {
                var length = $("#lampiran-row").children().length;
                var counter = length + 1;

                var formGroup =
                    '<div class="group">' +
                    '<div class="form-group row">' +
                    '<label for="lampiran' + counter + '" class="col-sm-4 col-form-label">Lampiran ' + counter +
                    '</label>' +
                    '<div class="col-sm-8 d-flex align-items-center">' +
                    '<input type="file" class="form-control" id="lampiran' + counter + '" name="lampiran[]">' +
                    //remove button
                    '<button type="button" class="ml-2 btn btn-danger btn-sm" onclick="removeLampiran(' + counter +
                    ')"><i class="fas fa-trash"></i></button>' +
                    '</div>' +
                    '</div>' +
                    // '<hr/>' +
                    '</div>';
                $("#lampiran-row").append(formGroup);
            }
        }

        function generateVendorList(data) {
            if (data) {
                $('#vendor-row').empty();
                var length = data.length;

                data.map((item, index) => {
                    const counter = index + 1;
                    var formGroup =
                        '<div class="group">' +
                        '<div class="form-group row">' +
                        '<label for="vendor' + counter + '" class="col-sm-4 col-form-label">Vendor ' + counter +
                        '</label>' +
                        '<div class="col-sm-8 d-flex align-items-center">' +
                        '<select class="form-control" id="vendor' + counter + '" name="vendor[]">' +
                        '<option value="">Pilih Vendor</option>' +
                        '@foreach ($vendors as $vendor)' +
                        '<option value="{{ $vendor->nama }}">{{ $vendor->nama }}</option>' +
                        // Use vendor name for both value and text
                        '@endforeach' +
                        '</select>' +
                        '<button type="button" class="ml-2 btn btn-danger btn-sm" onclick="removeNamaAlamat(' +
                        counter + ')"><i class="fas fa-trash"></i></button>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    $("#vendor-row").append(formGroup);

                    // Set the selected value after appending the form group
                    $('#vendor' + counter).val(item);
                });
            } else {
                var length = $("#vendor-row").children().length;
                var counter = length + 1;

                var formGroup =
                    '<div class="group">' +
                    '<div class="form-group row">' +
                    '<label for="vendor' + counter + '" class="col-sm-4 col-form-label">Vendor ' + counter + '</label>' +
                    '<div class="col-sm-8 d-flex align-items-center">' +
                    '<select class="form-control" id="vendor' + counter + '" name="vendor[]">' +
                    '<option value="">Pilih Vendor</option>' +
                    '@foreach ($vendors as $vendor)' +
                    '<option value="{{ $vendor->nama }}">{{ $vendor->nama }}</option>' +
                    // Use vendor name for both value and text
                    '@endforeach' +
                    '</select>' +
                    '<button type="button" class="ml-2 btn btn-danger btn-sm" onclick="removeNamaAlamat(' + counter +
                    ')"><i class="fas fa-trash"></i></button>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                $("#vendor-row").append(formGroup);
            }
        }


        function removeNamaAlamat(counter) {
            // $('#penerima' + counter).closest('.group').remove();
            $('#vendor' + counter).closest('.group').remove();
        }

        // function removeLampiran(counter) {
        //     $('#lampiran' + counter).closest('.group').remove();
        // }

        function generateLampiranList(data) {
            if (data) {
                $('#lampiran-row').empty();
                data.forEach((item, index) => {
                    const counter = index + 1;
                    var formGroup =
                        '<div class="form-group row">' +
                        '<label for="lampiran' + counter + '" class="col-sm-4 col-form-label">Lampiran ' + counter +
                        '</label>' +
                        '<div class="col-sm-8">' +
                        '<div class="custom-file">' +
                        '<input type="file" class="custom-file-input" id="lampiran' + counter +
                        '" name="lampiran[]" onchange="showFileName(this, ' + counter + ')">' +
                        '<label class="custom-file-label" for="lampiran' + counter + '">Pilih file</label>' +
                        '</div>' +
                        '<small id="file-name' + counter + '" class="form-text text-muted">' + item + '</small>' +
                        '<button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeLampiran(' +
                        counter + ')"><i class="fas fa-trash"></i> Hapus</button>' +
                        '</div>' +
                        '</div>';
                    $("#lampiran-row").append(formGroup);
                });
            } else {
                var length = $("#lampiran-row").children().length;
                var counter = length + 1;

                var formGroup =
                    '<div class="form-group row">' +
                    '<label for="lampiran' + counter + '" class="col-sm-4 col-form-label">Lampiran ' + counter +
                    '</label>' +
                    '<div class="col-sm-8">' +
                    '<div class="custom-file">' +
                    '<input type="file" class="custom-file-input" id="lampiran' + counter +
                    '" name="lampiran[]" onchange="showFileName(this, ' + counter + ')">' +
                    '<label class="custom-file-label" for="lampiran' + counter + '">Pilih file</label>' +
                    '</div>' +
                    '<small id="file-name' + counter + '" class="form-text text-muted"></small>' +
                    '<button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeLampiran(' + counter +
                    ')"><i class="fas fa-trash"></i> Hapus</button>' +
                    '</div>' +
                    '</div>';
                $("#lampiran-row").append(formGroup);
            }
        }


        function showFileName(input, counter) {
            var fileName = input.files[0].name;
            $('#file-name' + counter).text(fileName);
            // Update the label with the selected file's name
            $(input).next('.custom-file-label').html(fileName);
        }

        function removeLampiran(index) {
            $('#lampiran' + index).closest('.form-group').remove();
        }

        $(document).ready(function() {
            $('#add-lampiran').click(function() {
                generateLampiranList();
            });

            // Example of initializing with data
            // var initialData = ["file1.pdf", "file2.jpg"];
            // generateLampiranList(initialData);
        });



        $(document).ready(function() {
            $("#tambah").click(function() {
                // generateNamaAlamat(null);
                generateVendorList(null);
            });
        });
        $(document).ready(function() {
            $("#tambah-lampiran").click(function() {
                generateLampiranList(null);
            });
        });

        function showAddProduct(data) {
            //if .modal-dialog in #detail-spph has class modal-lg, change to modal-xl, otherwise change to modal-lg
            if ($('#container-product').hasClass('d-none')) {
                $('#detail-nego').find('.modal-dialog').removeClass('modal-xl');
                $('#detail-nego').find('.modal-dialog').addClass('modal-xl');
                $('#button-tambah-produk').text('Kembali');
                $('#container-form').removeClass('col-12');
                $('#container-form').addClass('col-6');
                $('#container-product').removeClass('col-0');
                $('#container-product').addClass('col-6');
                $('#container-product').removeClass('d-none');
                // console.log(data);
            } else {
                $('#detail-nego').find('.modal-dialog').removeClass('modal-xl');
                $('#detail-nego').find('.modal-dialog').addClass('modal-xl');
                $('#button-tambah-produk').text('Tambah Produk');
                $('#container-form').removeClass('col-6');
                $('#container-form').addClass('col-12');
                $('#container-product').removeClass('col-6');
                $('#container-product').addClass('col-0');
                $('#container-product').addClass('d-none');
                $('#proyek_name').val("");
            }

            // getSpphDetail(data);


        }


        //Edit Nego *Lampiran yang membuat edit error
        // function editNEGO(data) {
        //     $('#modal-title').text("Edit NEGO");
        //     $('#button-save').text("Simpan");
        //     console.log(data);
        //     resetForm();
        //     var lampiranArray = data.lampiran.split(", ");
        //     // Mengambil nilai dari elemen input
        //     $('#lampiran_awal').val(data.lampiran).length;
        //     var nilaiLampiran = lampiranArray.length;

        //     $('#nama_lampiran').val(data.lampiran).length;

        //     // alert($('#nama_lampiran').val());

        //     var vendorArray = data.vendor.split(", ");
        //     // Mengambil nilai dari elemen input
        //     $('#data_vendor').val(data.lampiran).length;
        //     var nilaiVendor = vendorArray.length;

        //     // Menambahkan nilai dari elemen input ke teks elemen <h6>
        //     document.getElementById('lampiran_text').innerHTML = 'Total Lampiran <b>' + nilaiLampiran + '</b>';
        //     generateLampiranList(lampiranArray);

        //     // Menambahkan nilai dari elemen input ke teks elemen <h6>
        //     document.getElementById('vendor_text').innerHTML = 'Total Vendor <b>' + nilaiVendor + '</b>';
        //     generateVendorList(vendorArray);
        //     // alert(vendorArray);

        //     $('#save_id').val(data.id);
        //     $('#id_pr').val(data.id_pr);
        //     $('#nomor_nego').val(data.nomor_nego);
        //     // $('#nomor_pr').val(data.nomor_pr);
        //     var pr = data.nomor_pr; // edit nomor pr biar muncul di form
        //     $('#lampiran').val(data.lampiran);
        //     $('#vendor').val(data.vendor);
        //     $('#penerima').val(data.penerima);
        //     $('#alamat').val(data.alamat);
        //     $('#perihal').val(data.perihal);
        //     $('#no_jawaban_vendor').val(data.no_jawaban_vendor);
        //     $('#franco').val(data.franco);
        //     // Ensure the komponen option is present in Select2
        //     // data edit nomor_pr
        //     if ($("#nomor_pr option[value='" + pr + "']").length == 0) {
        //         var newOption = new Option(pr, pr, true, true);
        //         $('#nomor_pr').append(newOption).trigger('change');
        //     } else {
        //         $('#nomor_pr').val(pr).trigger('change');
        //     }
        //     // $('#tanggal_spph').val(data.tanggal);
        //     var date = data.tanggal.split('/');
        //     var newDate = date[2] + '-' + date[1] + '-' + date[0];
        //     $('#tanggal_nego').val(newDate)
        //     // $('#batas_spph').val(data.batas);
        //     var date = data.batas.split('/');
        //     var newDate = date[2] + '-' + date[1] + '-' + date[0];
        //     $('#batas_nego').val(newDate)
        //     const penerima = JSON.parse(data.penerima_asli);
        //     const alamat = JSON.parse(data.alamat_asli);
        //     const dataPenerima = penerima.map((item, index) => {
        //         return {
        //             penerima: item,
        //             alamat: alamat[index]
        //         }
        //     })
        //     generateNamaAlamat(dataPenerima);
        // }
        //END Edit Nego *Lampiran yang membuat edit error

        
        //Edit Nego
        function editNEGO(data) {
            $('#modal-title').text("Edit NEGO");
            $('#button-save').text("Simpan");
            console.log(data);
            resetForm();

            // Periksa apakah data.lampiran tidak kosong sebelum memprosesnya
            if (data.lampiran && data.lampiran.trim() !== "") {
                var lampiranArray = data.lampiran.split(", ");
                var nilaiLampiran = lampiranArray.length;

                $('#lampiran_awal').val(data.lampiran);
                $('#nama_lampiran').val(data.lampiran);

                document.getElementById('lampiran_text').innerHTML = 'Total Lampiran <b>' + nilaiLampiran + '</b>';
                generateLampiranList(lampiranArray);
            } else {
                $('#lampiran_awal').val("");
                $('#nama_lampiran').val("");
                document.getElementById('lampiran_text').innerHTML = 'Total Lampiran <b>0</b>';
            }

            var vendorArray = data.vendor.split(", ");
            var nilaiVendor = vendorArray.length;

            $('#data_vendor').val(data.vendor);
            document.getElementById('vendor_text').innerHTML = 'Total Vendor <b>' + nilaiVendor + '</b>';
            generateVendorList(vendorArray);

            $('#save_id').val(data.id);
            $('#id_pr').val(data.id_pr);
            $('#nomor_nego').val(data.nomor_nego);

            var pr = data.nomor_pr; // edit nomor pr biar muncul di form
            $('#vendor').val(data.vendor);
            $('#penerima').val(data.penerima);
            $('#alamat').val(data.alamat);
            $('#perihal').val(data.perihal);
            $('#no_jawaban_vendor').val(data.no_jawaban_vendor);
            $('#franco').val(data.franco);
            $('#keterangan_nego').val(data.keterangan_nego);

            if ($("#nomor_pr option[value='" + pr + "']").length == 0) {
                var newOption = new Option(pr, pr, true, true);
                $('#nomor_pr').append(newOption).trigger('change');
            } else {
                $('#nomor_pr').val(pr).trigger('change');
            }

            var date = data.tanggal.split('/');
            var newDate = date[2] + '-' + date[1] + '-' + date[0];
            $('#tanggal_nego').val(newDate);

            date = data.batas.split('/');
            newDate = date[2] + '-' + date[1] + '-' + date[0];
            $('#batas_nego').val(newDate);

            const penerima = JSON.parse(data.penerima_asli);
            const alamat = JSON.parse(data.alamat_asli);
            const dataPenerima = penerima.map((item, index) => {
                return {
                    penerima: item,
                    alamat: alamat[index]
                }
            });
            generateNamaAlamat(dataPenerima);
        }

        //End Edit Nego



        function emptyTableNego() {
            $('#table-nego').empty();
            $('#no_surat').text("");
            $('#tanggal_nego').text("");
            $('#nama_penerima').text("");
        }

        function loader(status = 1) {
            if (status == 1) {
                $('#loader').show();
            } else {
                $('#loader').hide();
            }
        }



        // $('#form').hide();



        //SUMBER MASALAH HARI KAMIS BUAT HARI JUMAT 

        //Pilih Item SPPH
        function getDetailNego(id_pr) {
    // Menampilkan loader sebelum proses ajax dimulai
    loader();

    $('#button-check').prop("disabled", true);

    $.ajax({
        url: "{{ url('products/products_pr_nego/') }}/" + id_pr,
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
            
            // Mengosongkan elemen sebelum diisi dengan data baru
            $('#detail-material').empty();
            $('#btn-save-then-add').prop('disabled', true);

            // Pastikan data.products ada dan merupakan array
            if (data.products && Array.isArray(data.products)) {
                $.each(data.products, function(key, value) {
                    console.log(value);

                    // Menyiapkan nilai untuk nomor spph, nego, dan pr jika tidak ada
                    var no_spph = value.id_spph ? value.nomor_spph : '-';
                    var no_nego = value.id_nego ? value.nomor_nego : '-';
                    var no_pr = value.pr_no ? value.pr_no : '-';

                    // Menyiapkan checkbox yang aktif atau tidak berdasarkan id_nego
                    var checkbox;
                        if (value.qty_nego && value.qty_nego > 0) { 
                            checkbox = '<input type="checkbox" id="addToDetails-' + value.id + '" class="row-checkbox" value="' + value.id + '" onclick="addToDetailsJs(' + value.id + ')">';
                        } else {
                            checkbox = '<input type="checkbox" id="addToDetails-' + value.id + '" class="row-checkbox" value="' + value.id + '" onclick="addToDetailsJs(' + value.id + ')" disabled>';
                        }


                    // Menambahkan data ke tabel
                    $('#detail-material').append(
                        '<tr id="row-' + key + '" data-id="' + value.id + '">' +
                        '<td>' + (key + 1) + '</td>' +
                        '<td>' + checkbox + '</td>' +
                        '<td>' + value.uraian + '</td>' +
                        '<td>' + value.spek + '</td>' +
                        '<td data-original-qty="' + value.qty_nego + '">' + value.qty_nego + 
                        // '<td><input type="text" class="form-control qty_nego1-input" style="width: 50px;" value="' + value.qty_nego1 + '" data-qty="' + value.qty_nego1 + '"></td>' +
                        '<td>' +
        '<div style="display: block;">' +  // Menggunakan block untuk menata vertikal
            '<input type="text" class="form-control qty_nego1-input" style="width: 50px;" value="' + value.qty_nego1 + '" data-qty="' + value.qty_nego1 + '">' +
           
        '</div>' +

                        '<td>' + value.satuan + '</td>' +
                        '<td>' + no_pr + '</td>' +
                        '<td>' + no_spph + '</td>' +
                        '<td>' + value.nama_pekerjaan + '</td>' +
                        '</tr>'
                    );
                });

                // Memaksa pembaruan tampilan setelah data di-append
                setTimeout(function() {
                    // Gunakan jQuery untuk melakukan refresh secara manual pada elemen
                    $('#detail-material').html($('#detail-material').html());
                }, 0);  // Waktu delay yang sangat singkat untuk memaksa pembaruan tampilan
            } else {
                console.log("Tidak ada data produk.");
            }
        },
        error: function() {
            // Mengaktifkan kembali tombol jika terjadi error
            $('#pcode').prop("disabled", false);
            $('#button-check').prop("disabled", false);
        }
    });
}

        //End Pilih item SPPH

        var selected = [];

        function addToDetailsJs(id) {
            if (selected.includes(id)) {
                selected = selected.filter(item => item !== id)
            } else {
                selected.push(id)
            }

            console.log(selected);
        }

        function clearForm() {
            $('#product_id').val("");
            $('#pname').val("");
            $('#stock').val("");
            $('#pcode').val("");
            $('#form').hide();
        }

        //Tambah Pilihan
        function addToDetails() {
            $.ajax({
                url: "{{ url('products/tambah_nego_detail') }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "selected_id": selected,
                    "nego_id": $('#nego_id').val(),
                    "nego_id": $('#nego_id').val(),
                },
                dataType: "json",
                beforeSend: function() {
                    $('#loader').show();
                    $('#form').hide();
                },
                success: function(data) {
                    loader(0);
                    $('#form').show();
                    var id_pr = data.nego.id_pr;
                    getDetailNego(id_pr);
                    console.log(data);
                    // alert(id_pr);
                    // var selected = [];

                    // function addToDetailsJs(id) {
                    //     if (selected.includes(id)) {
                    //         selected = selected.filter(item => item !== id)
                    //     } else {
                    //         selected.push(id)
                    //     }

                    //     console.log(selected);
                    // }

                    if (!data.success) {
                        toastr.error(data?.message);
                        return
                    }

                    // Clear the form fields here
                    var no = 1;
                    selected = [];

                    // Append to #detail-material
                    $('#table-nego').empty();
                    $.each(data.nego.details, function(key, value) {
                        var nego_qty = value.nego_qty || 0; // Pastikan nego_qty didefinisikan, jika tidak gunakan 0
                        var id = value.id;
                        var id_nego = value.id_nego;
                        var id_detail_nego = value.id_detail_nego;
                        var harga_per_unit = value.harga_per_unit ?? 0;
                        var harga_per_unit_imss = value.harga_per_unit_imss ?? 0;
                        var total = nego_qty * harga_per_unit;
                        var total_imss = nego_qty * harga_per_unit_imss;
    
                        var totalFormatted = total.toLocaleString('id-ID', { minimumFractionDigits: 0 });
                        var totalImssFormatted = total_imss.toLocaleString('id-ID', { minimumFractionDigits: 0 });
    
                        $('#table-nego').append(
                            '<tr>' +
                            '<td>' + (key + 1) + '</td>' +
                            '<td>' + value.uraian + '</td>' +
                            '<td>' + value.spek + '</td>' +
                            '<td>' + value.nego_qty + '</td>' +
                            '<td>' + value.satuan + '</td>' +
                            '<td><input type="text" value="' + harga_per_unit +
                            '" class="form-control harga-per-unit" id="harga_per_unit' + id +
                            '" name="harga_per_unit' + id + '" data-id="' + id +
                            '" data-qty="' + nego_qty + '"></td>' +
                            '<td class="total">' + totalFormatted + '</td>' +
                            '<td><input type="text" value="' + harga_per_unit_imss +
                            '" class="form-control harga-per-unit-imss" id="harga_per_unit_imss' +
                            id + '" name="harga_per_unit_imss' + id + '" data-id="' + id +
                            '" data-qty="' + nego_qty + '"></td>' +
                            '<td class="total-imss">' + totalImssFormatted + '</td>' +
                            '<td>' +
                            '<button type="button" class="btn btn-danger btn-delete" data-id="' +
                            value.id + '" data-id_nego="' + id_nego +
                            '" data-id_detail_nego="' + id_detail_nego +
                            '" data-id_detail_pr="' + value.id_detail_pr +
                            '" style="margin-bottom: 10px;">Hapus</button>' +
                            '<button type="button" class="btn btn-success btn-save" data-id="' +
                            value.id + '" data-id_nego="' + id_nego +
                            '" data-id_detail_nego="' + id_detail_nego +
                            '" data-id_detail_pr="' + value.id_detail_pr + '">Simpan</button>' +
                            '</td>' +
                            '</tr>'
                        );
                    });
    
                    
    
                    // Event listener untuk menghitung total secara otomatis
                    $('.harga-per-unit, .harga-per-unit-imss').on('input', function() {
                        var id = $(this).data('id');
                        var nego_qty = $(this).data('qty'); // Mengambil data-qty yang valid dari elemen input
                        var hargaPerUnit = parseFloat($('#harga_per_unit' + id).val()) || 0;
                        var hargaPerUnitImss = parseFloat($('#harga_per_unit_imss' + id).val()) || 0;
    
                        // Hitung total berdasarkan harga dan qty
                        var total = nego_qty * hargaPerUnit;
                        var totalImss = nego_qty * hargaPerUnitImss;
    
                        // Update tampilan total di tabel
                        $('#harga_per_unit' + id).closest('tr').find('.total').text(
                            total.toLocaleString('id-ID', { minimumFractionDigits: 0 })
                        );
                        $('#harga_per_unit_imss' + id).closest('tr').find('.total-imss').text(
                            totalImss.toLocaleString('id-ID', { minimumFractionDigits: 0 })
                        );
                    });
                },
                error: function() {
                    $('#pcode').prop("disabled", false);
                    $('#button-check').prop("disabled", false);
                }
            });
        }
        //End Item Tambah Pilihan


        //Tampilan di dalam tambah pilihan
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
                            if (!value.spph_id) {
                                no_spph = '-'
                            } else {
                                no_spph = value.nomor_spph
                            }

                            var no_nego
                            if (!value.id_nego) {
                                no_nego = '-'
                            } else {
                                no_nego = value.nomor_nego
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

                            var checkbox;
                            if (value.id_spph && !value.id_nego) {
                                checkbox = '<input type="checkbox" id="addToDetails" value="' + value
                                    .id +
                                    '" onclick="addToDetailsJs(' + value.id + ')">'
                            } else {
                                checkbox = '<input type="checkbox" id="addToDetails" value="' + value
                                    .id +
                                    '" onclick="addToDetailsJs(' + value.id + ')" disabled>'
                            }

                            $('#detail-material').append(

                                '<tr><td>' + (key + 1) + '</td><td>' + value.uraian +
                                '</td><td>' + value.spek + '</td><td>' + value.nego_qty + '</td><td>' +
                                value
                                .satuan + '</td><td>' + value.nama_pekerjaan + '</td><td>' + no_nego +
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

        function sjnProductUpdate() {
            const id = $('#product_id').val();
            $.ajax({
                url: "{{ url('products/update_detail_sjn/') }}",
                type: "POST",
                dataType: "json",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "product_id": id,
                    "stock": $('#stock').val(),
                    "nego_id": $('#nego_id').val(),
                },
                beforeSend: function() {
                    $('#button-update-nego').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                    $('#button-update-nego').attr('disabled', true);
                },
                success: function(data) {
                    if (!data.success) {
                        toastr.error(data.message);
                        $('#button-update-nego').html('Tambahkan');
                        $('#button-update-nego').attr('disabled', false);
                        return
                    }
                    $('#no_surat').text(data.sjn.no_sjn);
                    $('#tgl_surat').text(data.sjn.datetime);
                    $('#nego_id').val(data.sjn.nego_id);
                    $('#button-update-nego').html('Tambahkan');
                    $('#button-update-nego').attr('disabled', false);
                    clearForm();
                    if (data.sjn.products.length == 0) {
                        $('#table-nego').append(
                            '<tr><td colspan="7" class="text-center">Tidak ada produk</td></tr>');
                    } else {
                        $('#table-nego').empty();
                        $.each(data.sjn.products, function(key, value) {
                            $('#table-nego').append('<tr><td>' + (key + 1) + '</td><td>' + value
                                .uraian + '</td><td>' + value.spek + '</td><td>' + value.nego_qty +
                                '</td><td>' + value
                                .satuan +
                                '</td><td>' + value.nama_pekerjaan + '</td></tr>');
                        });
                    }
                }
            });
        }

        // on modal #detail-nego open
        $('#detail-nego').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var data = button.data('detail');
            console.log(data);
            lihatSjn(data);
            $('#detail-nego').find('.modal-dialog').removeClass('modal-xl');
            $('#detail-nego').find('.modal-dialog').addClass('modal-xl');
            $('#button-tambah-produk').text('Tambah Produk');
            $('#container-form').removeClass('col-6');
            $('#container-form').addClass('col-12');
            $('#container-product').removeClass('col-6');
            $('#container-product').addClass('col-0');
            $('#container-product').addClass('d-none');
            $('#proyek_name').val("");
        });



        //Lihat Detail
        function lihatSjn(data) {
    emptyTableNego();
    $('#modal-title').text("Detail NEGO");
    $('#button-save').text("Cetak");
    resetForm();
    $('#save_id').val(data.id);
    $('#button-tambah-produk').val(data.id_pr);
    $('#button-tambah-produk').attr('onclick', `showAddProduct(${data.id_pr}); getDetailNego(${data.id_pr});`);
    $('#id_pr2').text(data.id_pr);
    $('#no_surat').text(data.nomor_nego);
    $('#nama_penerima').text(data.penerima);
    $('#tgl_nego').text(data.tanggal);
    $('#table-nego').empty();

    $.ajax({
        url: "{{ url('products/nego_detail') }}" + "/" + data.id,
        type: "GET",
        dataType: "json",
        beforeSend: function() {
            $('#table-nego').append('<tr><td colspan="7" class="text-center">Loading...</td></tr>');
            $('#button-cetak-nego').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
            $('#button-cetak-nego').attr('disabled', true);
        },
        success: function(response) {
            $('#no_surat').text(response.nego.no_nego);
            $('#nama_penerima').text(response.nego.penerima);
            $('#tgl_nego').text(response.nego.tanggal_nego);
            $('#nego_id').val(response.nego.id);
            $('#button-cetak-nego').html('<i class="fas fa-print"></i> Cetak');
            $('#button-cetak-nego').attr('disabled', false);

            if (response.nego.details.length === 0) {
                $('#table-nego').append('<tr><td colspan="10" class="text-center">Tidak ada produk</td></tr>');
            } else {
                $.each(response.nego.details, function(key, value) {
                    var nego_qty = value.nego_qty || 0; // Pastikan nego_qty didefinisikan, jika tidak gunakan 0
                    var id = value.id;
                    var id_nego = value.id_nego;
                    var id_detail_nego = value.id_detail_nego;
                    var harga_per_unit = value.harga_per_unit ?? 0;
                    var harga_per_unit_imss = value.harga_per_unit_imss ?? 0;
                    var total = nego_qty * harga_per_unit;
                    var total_imss = nego_qty * harga_per_unit_imss;

                    var totalFormatted = total.toLocaleString('id-ID', { minimumFractionDigits: 0 });
                    var totalImssFormatted = total_imss.toLocaleString('id-ID', { minimumFractionDigits: 0 });

                    $('#table-nego').append(
                        '<tr>' +
                        '<td>' + (key + 1) + '</td>' +
                        '<td>' + value.uraian + '</td>' +
                        '<td>' + value.spek + '</td>' +
                        '<td>' + value.nego_qty + '</td>' +
                        '<td>' + value.satuan + '</td>' +
                        '<td><input type="text" value="' + harga_per_unit +
                        '" class="form-control harga-per-unit" id="harga_per_unit' + id +
                        '" name="harga_per_unit' + id + '" data-id="' + id +
                        '" data-qty="' + nego_qty + '"></td>' +
                        '<td class="total">' + totalFormatted + '</td>' +
                        '<td><input type="text" value="' + harga_per_unit_imss +
                        '" class="form-control harga-per-unit-imss" id="harga_per_unit_imss' +
                        id + '" name="harga_per_unit_imss' + id + '" data-id="' + id +
                        '" data-qty="' + nego_qty + '"></td>' +
                        '<td class="total-imss">' + totalImssFormatted + '</td>' +
                        '<td>' +
                        '<button type="button" class="btn btn-danger btn-delete" data-id="' +
                        value.id + '" data-id_nego="' + id_nego +
                        '" data-id_detail_nego="' + id_detail_nego +
                        '" data-id_detail_pr="' + value.id_detail_pr +
                        '" style="margin-bottom: 10px;">Hapus</button>' +
                        '<button type="button" class="btn btn-success btn-save" data-id="' +
                        value.id + '" data-id_nego="' + id_nego +
                        '" data-id_detail_nego="' + id_detail_nego +
                        '" data-id_detail_pr="' + value.id_detail_pr + '">Simpan</button>' +
                        '</td>' +
                        '</tr>'
                    );
                });

                

                // Event listener untuk menghitung total secara otomatis
                $('.harga-per-unit, .harga-per-unit-imss').on('input', function() {
                    var id = $(this).data('id');
                    var nego_qty = $(this).data('qty'); // Mengambil data-qty yang valid dari elemen input
                    var hargaPerUnit = parseFloat($('#harga_per_unit' + id).val()) || 0;
                    var hargaPerUnitImss = parseFloat($('#harga_per_unit_imss' + id).val()) || 0;

                    // Hitung total berdasarkan harga dan qty
                    var total = nego_qty * hargaPerUnit;
                    var totalImss = nego_qty * hargaPerUnitImss;

                    // Update tampilan total di tabel
                    $('#harga_per_unit' + id).closest('tr').find('.total').text(
                        total.toLocaleString('id-ID', { minimumFractionDigits: 0 })
                    );
                    $('#harga_per_unit_imss' + id).closest('tr').find('.total-imss').text(
                        totalImss.toLocaleString('id-ID', { minimumFractionDigits: 0 })
                    );
                });
            }

            // Remove loading
            $('#table-nego').find('tr:first').remove();
        }
    });
}


        // Action save_nego

        $(document).on('click', '.btn-save', function() {
            var id = $(this).data('id');
            var id_nego = $(this).data('id_nego');
            var id_detail_nego = $(this).data('id_detail_nego'); //ggwp
            var id_detail_pr = parseInt($(this).data('id_detail_pr'), 10); // Konversi ke integer

            var harga_per_unit = $('#harga_per_unit' + id).val();
            var harga_per_unit_imss = $('#harga_per_unit_imss' + id).val();

            console.log(id_detail_pr); // Untuk debug

            $.ajax({
                url: "{{ route('detail_nego_save') }}",
                type: "POST",
                data: {
                    id: id,
                    id_nego: id_nego,
                    id_detail_nego: id_detail_nego, //ggwp
                    id_detail_pr: id_detail_pr,
                    harga_per_unit: harga_per_unit,
                    harga_per_unit_imss: harga_per_unit_imss,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                beforeSend: function() {
                    $('#table-nego').append(
                        '<tr><td colspan="6" class="text-center">Loading...</td></tr>');
                },
                success: function(data) {
                    if (data.nego.details.length > 0) {
                        $('#table-nego').empty();
                        $.each(data.nego.details, function(key, value) {
                            var id = value.id;
                            var id_nego = value.id_nego;
                            var nego_qty = value.nego_qty;
                            var id_detail_nego = value.id_detail_nego; //ggwp
                            var id_detail_pr = value
                                .id_detail_pr; // Pastikan data id_detail_pr ada di sini

                            console.log(value)
                            var harga_per_unit = value.harga_per_unit ?? 0;
                            var harga_per_unit_imss = value.harga_per_unit_imss ?? 0;
                            var total = nego_qty * harga_per_unit;
                            var total_imss = nego_qty * harga_per_unit_imss;
                            harga_per_unit_imss = harga_per_unit_imss.toString()

                            // alert();

                            $('#table-nego').append(
                                '<tr>' +
                                '<td>' + (key + 1) + '</td>' +
                                '<td>' + value.uraian + '</td>' +
                                '<td>' + value.spek + '</td>' +
                                '<td>' + value.nego_qty + '</td>' +
                                '<td>' + value.satuan + '</td>' +
                                '<td><input type="text" value="' + harga_per_unit +
                                '" class="form-control harga-per-unit" id="harga_per_unit' +
                                id +
                                '" name="harga_per_unit' + id + '" data-id="' + id +
                                '" data-qty="' + nego_qty + '"></td>' +
                                '<td class="total">' + total + '</td>' +

                                '<td><input type="text" value="' + harga_per_unit_imss +
                                '" class="form-control harga-per-unit-imss" id="harga_per_unit_imss' +
                                id + '" name="harga_per_unit_imss' + id + '" data-id="' +
                                id +
                                '" data-qty="' + nego_qty + '"></td>' +
                                '<td class="total-imss">' + total_imss + '</td>' +

                                '<td>' +
                                '<button type="button" class="btn btn-danger btn-delete" data-id="' +
                                value.id + '" data-id_nego="' + value.id_nego +
                                '" data-id_detail_nego="' + id_detail_nego + //ggwp
                                '" data-id_detail_pr="' + value.id_detail_pr +
                                '" style="margin-bottom: 10px;">Hapus</button>' +
                                '<button type="button" class="btn btn-success btn-save" data-id="' +
                                value.id + '" data-id_nego="' + value.id_nego +
                                '" data-id_detail_nego="' + id_detail_nego + //ggwp
                                '" data-id_detail_pr="' + value.id_detail_pr +
                                '">Simpan</button>' +
                                '</td>' +
                                '</tr>'
                            );
                        });

                        // Event listener untuk menghitung total secara otomatis
                        $('.harga-per-unit, .harga-per-unit-imss').on('input', function() {
    var id = $(this).data('id');
    var nego_qty = $(this).data('qty');
    var hargaPerUnit = $('#harga_per_unit' + id).val();
    var hargaPerUnitImss = $('#harga_per_unit_imss' + id).val();
    var total = nego_qty * hargaPerUnit;
    var totalImss = nego_qty * hargaPerUnitImss;
        
    console.log(nego_qty, hargaPerUnit, hargaPerUnitImss, total, totalImss)
    
    $('#harga_per_unit' + id).closest('tr').find('.total').text(total);
    $('#harga_per_unit_imss' + id).closest('tr').find('.total-imss').text(totalImss);
});

                    } else {
                        $('#table-nego').empty();
                        $('#table-nego').append(
                            '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                },
                complete: function() {
                    $('#table-nego').find('tr:contains("Loading...")').remove();
                }
            });
        });
//detail qty
        //logika untuk mematikan button tambah pilihan
        $(document).on('change', '.row-checkbox', function() {
    var anyChecked = $('.row-checkbox:checked').length > 0;
    $('#btn-save-then-add').prop('disabled', !anyChecked);
});


$('#btn-save-then-add').on('click', function() {
    var dataToSend = [];
    var selectedRows = 0; // Hitung jumlah baris yang dicentang

    $('#detail-material tr').each(function() { // Loop semua baris
        var $row = $(this);
        var id = $row.data('id');
        var qty_nego1 = $row.find('.qty_nego1-input').val();
        var isChecked = $row.find('.row-checkbox').prop('checked'); // Cek checkbox

        if (isChecked) { 
            selectedRows++; // Hitung jumlah yang dicentang
            if (qty_nego1 !== '' && !isNaN(qty_nego1)) { // Pastikan qty2 valid
                dataToSend.push({ id: id, qty_nego1: qty_nego1 });
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
//         url: "{{ route('qty_nego_save') }}", 
//         type: "POST",
//         data: {
//             id: id,
//             qty_nego1: qty_nego1,
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
        url: "{{ route('qty_nego_save') }}", // Sesuaikan dengan route
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
                        $row.find('.row-checkbox').prop('disabled', true); // Tidak bisa dicentang ulang
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
$(document).on('input', '.qty_nego1-input', function() {
    var $row = $(this).closest('tr'); 
    var qtyNegoCell = $row.find('td:eq(4)'); 
    var initialQtyNego = parseFloat(qtyNegoCell.data('original-qty')) || 0; 
    var inputQty_nego1 = parseFloat($(this).val()) || 0;

    if (inputQty_nego1 > initialQtyNego) {
        alert("Qty2 tidak boleh lebih besar dari Qty SPPH!");
        $(this).val(initialQtyNego); 
        inputQty_nego1 = initialQtyNego;
    }

    var newQtyNego = initialQtyNego - inputQty_nego1;

    qtyNegoCell.text(newQtyNego);
});


    //detail qty
        //action delete_nego
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var id_nego = $(this).data('id_nego');
            var id_detail_pr = $(this).data('id_detail_pr');
            var id_detail_nego = $(this).data('id_detail_nego'); //ggwp

            $.ajax({
                url: "{{ route('detail_nego_delete') }}",
                type: "DELETE",
                data: {
                    id: id,
                    id_nego: id_nego,
                    id_detail_pr: id_detail_pr,
                    id_detail_nego: id_detail_nego, //ggwp
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                beforeSend: function() {
                    $('#table-nego').append(
                        '<tr><td colspan="12" class="text-center">Loading...</td></tr>'
                    );
                },
                success: function(data) {
                    if (data.nego.details.length > 0) {
                        $('#table-nego').empty();
                        $.each(data.nego.details, function(key, value) {
                            var id = value.id;
                            var id_nego = value.id_nego;
                            var nego_qty = value.nego_qty;
                            var id_detail_nego = value.id_detail_nego; //ggwp
                            var id_detail_pr = value
                                .id_detail_pr; // Pastikan data id_detail_pr ada di sini

                            console.log(value)
                            var harga_per_unit = value.harga_per_unit ?? 0;
                            var harga_per_unit_imss = value.harga_per_unit_imss ?? 0;
                            var total = nego_qty * harga_per_unit;
                            var total_imss = nego_qty * harga_per_unit_imss;
                            harga_per_unit_imss = harga_per_unit_imss.toString()

                            // alert();

                            $('#table-nego').append(
                                '<tr>' +
                                '<td>' + (key + 1) + '</td>' +
                                '<td>' + value.uraian + '</td>' +
                                '<td>' + value.spek + '</td>' +
                                '<td>' + value.nego_qty + '</td>' +
                                '<td>' + value.satuan + '</td>' +
                                '<td><input type="text" value="' + harga_per_unit +
                                '" class="form-control harga-per-unit" id="harga_per_unit' +
                                id +
                                '" name="harga_per_unit' + id + '" data-id="' + id +
                                '" data-qty="' + nego_qty + '"></td>' +
                                '<td class="total">' + total + '</td>' +

                                '<td><input type="text" value="' + harga_per_unit_imss +
                                '" class="form-control harga-per-unit-imss" id="harga_per_unit_imss' +
                                id + '" name="harga_per_unit_imss' + id + '" data-id="' +
                                id +
                                '" data-qty="' + nego_qty + '"></td>' +
                                '<td class="total-imss">' + total_imss + '</td>' +

                                '<td>' +
                                '<button type="button" class="btn btn-danger btn-delete" data-id="' +
                                value.id + '" data-id_nego="' + value.id_nego +
                                '" data-id_detail_nego="' + id_detail_nego + //ggwp
                                '" data-id_detail_pr="' + value.id_detail_pr +
                                '" style="margin-bottom: 10px;">Hapus</button>' +
                                '<button type="button" class="btn btn-success btn-save" data-id="' +
                                value.id + '" data-id_nego="' + value.id_nego +
                                '" data-id_detail_nego="' + id_detail_nego + //ggwp
                                '" data-id_detail_pr="' + value.id_detail_pr +
                                '">Simpan</button>' +
                                '</td>' +
                                '</tr>'
                            );
                        });

                        // Event listener untuk menghitung total secara otomatis
                        $('.harga-per-unit, .harga-per-unit-imss').on('input', function() {
                            var id = $(this).data('id');
                            var nego_qty = $(this).data('qty');
                            var hargaPerUnit = $('#harga_per_unit' + id).val();
                            var hargaPerUnitImss = $('#harga_per_unit_imss' + id).val();
                            var total = nego_qty * hargaPerUnit;
                            var totalImss = nego_qty * hargaPerUnitImss;

                            $('#harga_per_unit' + id).closest('tr').find('.total').text(total);
                            $('#harga_per_unit_imss' + id).closest('tr').find('.total-imss')
                                .text(totalImss);
                        });
                    } else {
                        $('#table-nego').empty();
                        $('#table-nego').append(
                            '<tr><td colspan="12" class="text-center">Tidak ada data</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                },
                complete: function() {
                    $('#table-nego').find('tr:contains("Loading...")').remove();
                }
            });
        });






        //Agar ketika klik simpan, dapat submit
        function setSaveIdAndSubmit() {
            // Submit the form
            var allFileNames = getAllFileNames();
            $('#nama_lampiran').val(allFileNames);
            // alert($('#nama_lampiran').val());
            // alert($('#lampiran_awal').val());
            document.getElementById('save').submit();
        }

        //Mengambil semua nama file (lampiran)
        function getAllFileNames() {
            var fileNames = [];
            var counter = 1;
            var maxTries = 100; // Batas atas untuk menghentikan loop jika terlalu banyak percobaan

            while (counter <= maxTries) {
                var element = $("#file-name" + counter);
                if (element.length) {
                    var fileName = element.text().trim();
                    fileNames.push(fileName);
                }
                counter++;
            }

            return fileNames.join(", ");
        }





        //End Lihat Detail

        function detailSjn(data) {
            $('#modal-title').text("Edit NEGO");
            $('#button-save').text("Simpan");
            resetForm();
            $('#save_id').val(data.nego_id);
            $('#no_sjn').val(data.no_sjn);
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

        function deletenego(data) {
            $('#delete_id').val(data.id);
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
