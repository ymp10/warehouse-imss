@extends('layouts.main')
@section('title', 'Bill Of Material')
@section('custom-css')
    <link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
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
                    @auth
                        @if (Auth::user()->role == 0 || Auth::user()->role == 7)
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-sr"
                                onclick="addSR()"><i class="fas fa-plus"></i> Add New Bill Of Material</button>
                        @endif
                    @endauth
                    <div class="card-tools">
                        <form>
                            <div class="input-group input-group">
                                <input type="text" class="form-control" name="q" placeholder="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">


                        <table id="table" class="table table-sm table-bordered table-hover table-striped">
                            <thead>
                                <tr class="text-center">
                                    <th>No.</th>
                                    <th width="13%">{{ __('Nomor') }}</th>
                                    <th>{{ __('Nama Tempat') }}</th>
                                    <th>{{ __('Lokasi') }}</th>
                                    <th>{{ __('Nama Proyek') }}</th>
                                    <th>{{ __('Trainset') }}</th>
                                    <th>{{ __('Car') }}</th>
                                    <th>{{ __('Perawatan') }}</th>
                                    <th>{{ __('Perawatan Mulai') }}</th>
                                    <th>{{ __('Perawatan Selesai') }}</th>
                                    <th>{{ __('PIC') }}</th>
                                    <th>{{ __('Keterangan') }}</th>
                                    <th>{{ __('Aksi') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $key => $d)
                                    @php
                                        $data = $d->toArray();
                                    @endphp

                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $d->no_sr }}</td>
                                        <td>{{ $d->nama_tempat }}</td>
                                        <td>{{ $d->lokasi }}</td>
                                        <td>{{ $d->nama_proyek }}</td>
                                        <td>{{ $d->trainset }}</td>
                                        <td>{{ $d->car }}</td>
                                        <td>{{ $d->perawatan }}</td>
                                        <td>{{ $d->perawatan_mulai }}</td>
                                        <td>{{ $d->perawatan_selesai }}</td>
                                        <td>{{ $d->pic }}</td>
                                        <td>{{ $d->keterangan }}</td>
                                        {{-- <td><img src="{{ asset('/storage/photo/' . $d->file) }}" alt=""
                                                height="100px" width="100px"> </td> --}}

                                        <td class="text-center">
                                            @if (Auth::user()->role == 0 || Auth::user()->role == 7)
                                                <button title="Edit Shelf" type="button" class="btn btn-success btn-xs"
                                                    data-toggle="modal" data-target="#add-sr"
                                                    onclick="editSR({{ json_encode($data) }})"><i
                                                        class="fas fa-edit"></i></button>

                                                <button title="Lihat Detail" type="button" data-toggle="modal"
                                                    data-target="#detail-sr" class="btn-lihat btn btn-info btn-xs"
                                                    data-detail="{{ json_encode($data) }}"><i
                                                        class="fas fa-list"></i></button>

                                                <button title="Hapus Produk" type="button" class="btn btn-danger btn-xs"
                                                    data-toggle="modal" data-target="#delete-suratkeluar"
                                                    onclick="deleteSR({{ json_encode($data) }})"><i
                                                        class="fas fa-trash"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="text-center">
                                        <td colspan="11">{{ __('No data.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div>
                {{ $items->links('pagination::bootstrap-4') }}
            </div>
        </div>

        {{-- modal detail --}}
        <div class="modal fade" id="detail-sr">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="modal-title" class="modal-title">{{ __('Detail Service') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="row">
                                <form id="cetak-sr" method="GET" action="{{ route('cetak_sr') }}" target="_blank">
                                    <input type="hidden" name="id" id="id">
                                </form>
                                <div class="col-12" id="container-form">
                                    <button id="button-cetak-sr" type="button" class="btn btn-primary"
                                        onclick="document.getElementById('cetak-sr').submit();">{{ __('Cetak') }}</button>
                                    <table class="align-top w-100">
                                        <tr>
                                            <td style="width: 10%;"><b>Nama Tempat</b></td>
                                            <td style="width:2%">:</td>
                                            <td style="width: 55%"><span id="nama_tempat"></span></td>
                                        </tr>
                                        <tr>
                                            <td><b>Lokasi</b></td>
                                            <td>:</td>
                                            <td><span id="lokasi"></span></td>
                                        </tr>
                                        <tr>
                                            <td><b>Nama Proyek</b></td>
                                            <td>:</td>
                                            <td><span id="nama_proyek"></span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <button id="button-tambah-produk" type="button" class="btn btn-info mb-3"
                                                    onclick="showAddProduct()">{{ __('Tambah Item Detail') }}</button>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead style="text-align: center">
                                                <tr>
                                                    <th rowspan="2">{{ __('NO') }}</th>
                                                    <th rowspan="2">{{ __('Kode Material') }}</th>
                                                    <th rowspan="2">{{ __('Deskripsi Material') }}</th>
                                                    <th rowspan="2">{{ __('Spesifikasi') }}</th>
                                                    <th colspan="8">{{ __('Volume Perawatan') }}</th>
                                                    <th rowspan="2">{{ __('Volume (Protective) Part Untuk 1 ts') }}
                                                    </th>
                                                    <th rowspan="2">{{ __('UoM (Sat)') }}</th>
                                                    <th rowspan="2">{{ __('Aksi') }}</th>

                                                </tr>
                                                <tr>
                                                    <th>{{ __('P1') }}</th>
                                                    <th>{{ __('P3') }}</th>
                                                    <th>{{ __('P6') }}</th>
                                                    <th>{{ __('P12') }}</th>
                                                    <th>{{ __('P24') }}</th>
                                                    <th>{{ __('P48') }}</th>
                                                    <th>{{ __('P60') }}</th>
                                                    <th>{{ __('P72') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table-sr">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-0 d-none" id="container-product">
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
                                                action="{{ route('service_detail.update') }}"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" id="pid" name="pid">
                                                <input type="hidden" id="type" name="type">
                                                <input type="hidden" id="proyek_id_val" name="proyek_id_val">
                                                <div class="form-group row">
                                                    <label for="kode_material"
                                                        class="col-sm-4 col-form-label">{{ __('Kode Material') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="kode_material"
                                                            name="kode_material">
                                                        <input type="hidden" class="form-control" id="sr_id"
                                                            disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="desc_material"
                                                        class="col-sm-4 col-form-label">{{ __('Deskripsi Material') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="desc_material"
                                                            name="desc_material">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="spek"
                                                        class="col-sm-4 col-form-label">{{ __('Spesifikasi') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="spek"
                                                            name="spek">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="p1"
                                                        class="col-sm-4 col-form-label">{{ __('P1') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="p1"
                                                            name="p1">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="p3"
                                                        class="col-sm-4 col-form-label">{{ __('P3') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="p3"
                                                            name="p3">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="p6"
                                                        class="col-sm-4 col-form-label">{{ __('P6') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="p6"
                                                            name="p6">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="p12"
                                                        class="col-sm-4 col-form-label">{{ __('P12') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="p12"
                                                            name="p12">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="p24"
                                                        class="col-sm-4 col-form-label">{{ __('P24') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="p24"
                                                            name="p24">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="p48"
                                                        class="col-sm-4 col-form-label">{{ __('P48') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="p48"
                                                            name="p48">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="p60"
                                                        class="col-sm-4 col-form-label">{{ __('P60') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="p60"
                                                            name="p60">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="p72"
                                                        class="col-sm-4 col-form-label">{{ __('P72') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="p72"
                                                            name="p72">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="vol_protective"
                                                        class="col-sm-4 col-form-label">{{ __('Volume Protective') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="vol_protective"
                                                            name="vol_protective">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="satuan"
                                                        class="col-sm-4 col-form-label">{{ __('UoM (Sat)') }}</label>
                                                    <div class="col-sm-8">
                                                        <select class="form-control" id="satuan" name="satuan">
                                                            <option value="" disabled selected>Pilih Satuan</option>
                                                            <option value="Liter">Liter</option>
                                                            <option value="Pcs">Pcs</option>
                                                            <option value="Set">Set</option>
                                                        </select>
                                                    </div>
                                                </div>


                                                {{-- <div class="form-group row">
                                                    <label for="keterangan"
                                                        class="col-sm-4 col-form-label">{{ __('Keterangan') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="keterangan"
                                                            name="keterangan">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="lampiran"
                                                        class="col-sm-4 col-form-label">{{ __('Nota Pembelian') }}</label>
                                                    <div class="col-sm-8">
                                                        <input type="file" class="form-control" id="lampiran"
                                                            name="lampiran" />
                                                    </div>
                                                </div> --}}

                                            </form>
                                            <button id="button-update-sr" type="button" class="btn btn-primary w-100"
                                                onclick="SRupdate()">{{ __('Tambahkan') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @auth
            @if (Auth::user()->role == 0 || Auth::user()->role == 7)
                <div class="modal fade" id="add-sr">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 id="modal-title1" class="modal-title">{{ __('Add New Service') }}</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form role="form" id="save" action="{{ route('service.store') }}" method="post"
                                    enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    {{-- @method('put') --}}
                                    <input type="hidden" id="id" name="id">


                                    {{-- <div class="form-group row">
                                        <label for="nomor_aset" class="col-sm-4 col-form-label">{{ __('Nomor Aset') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="nomor_aset" name="nomor_aset">
                                        </div>
                                    </div> --}}
                                    <div class="form-group row">
                                        <label for="no_sr" class="col-sm-4 col-form-label">{{ __('Nomor') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Masukkan Nomor" class="form-control" id="no_sr" name="no_sr">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="nama_tempat" class="col-sm-4 col-form-label">{{ __('Nama Tempat') }}
                                        </label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="nama_tempat" id="nama_tempat1">
                                                <option value="">Pilih Tempat</option>
                                                @foreach ($proyeks as $nama_tempats)
                                                    <option value="{{ $nama_tempats->nama_tempat }}">
                                                        {{ $nama_tempats->nama_tempat }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="lokasi" class="col-sm-4 col-form-label">{{ __('Lokasi') }} </label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="lokasi" id="lokasi1">
                                                <option value="">Pilih Lokasi</option>
                                                @foreach ($proyeks as $lokasis)
                                                    <option value="{{ $lokasis->lokasi }}">{{ $lokasis->lokasi }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    {{-- <div class="form-group row">
                                        <label for="kondisi" class="col-sm-4 col-form-label">{{ __('Kondisi') }} </label>
                                        <div class="col-sm-8">
                                            <select class="form-control" id="kondisi" name="kondisi">
                                                <option value="Baik">Baik</option>
                                                <option value="Rusak">Rusak</option>
                                                <option value="Hilang">Hilang</option>
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="form-group row">
                                        <label for="nama_proyek" class="col-sm-4 col-form-label">{{ __('Nama Proyek') }}
                                        </label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="nama_proyek" id="nama_proyek1">
                                                <option value="">Pilih Nama Proyek</option>
                                                @foreach ($proyeks as $nama_proyeks)
                                                    <option value="{{ $nama_proyeks->nama_proyek }}">
                                                        {{ $nama_proyeks->nama_proyek }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="trainset" class="col-sm-4 col-form-label">{{ __('Trainset') }} </label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="trainset" id="trainset">
                                                <option value="">Pilih Trainset</option>
                                                @foreach ($trainsets as $trainsetss)
                                                    <option value="{{ $trainsetss->trainset_nama }}">
                                                        {{ $trainsetss->trainset_nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="car" class="col-sm-4 col-form-label">{{ __('Car') }} </label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="car" id="car">
                                                <option value="">Pilih Car</option>
                                                @foreach ($trainsets as $cars)
                                                    <option value="{{ $cars->car_nomor }}">{{ $cars->car_nomor }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="perawatan" class="col-sm-4 col-form-label">{{ __('Perawatan') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Jenis Perawatan" class="form-control" id="perawatan" name="perawatan">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="perawatan_mulai"
                                            class="col-sm-4 col-form-label">{{ __('Perawatan Mulai') }}</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control" id="perawatan_mulai"
                                                name="perawatan_mulai">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="perawatan_selesai"
                                            class="col-sm-4 col-form-label">{{ __('Perawatan Selesai') }}</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control" id="perawatan_selesai"
                                                name="perawatan_selesai">
                                        </div>
                                    </div>

                                    {{-- <div class="form-group row">
                                        <label for="proyek_status" class="col-sm-4 col-form-label">{{ __('Proyek Status') }}
                                        </label>
                                        <div class="col-sm-8">
                                            <select class="form-control" id="proyek_status" name="proyek_status">
                                                <option value=""></option>
                                                <option value="Open">Open</option>
                                                <option value="Close">Close</option>

                                            </select>
                                        </div>
                                    </div> --}}

                                    {{-- <div class="form-group row">
                                        <label for="komponen_diganti"
                                            class="col-sm-4 col-form-label">{{ __('Komponen Yang diganti') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="komponen_diganti"
                                                name="komponen_diganti">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="tanggal_komponen"
                                            class="col-sm-4 col-form-label">{{ __('Tanggal Komponen') }}</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control" id="tanggal_komponen"
                                                name="tanggal_komponen">
                                        </div>
                                    </div> --}}
                                    <div class="form-group row">
                                        <label for="pic" class="col-sm-4 col-form-label">{{ __('PIC') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Nama PIC" class="form-control" id="pic" name="pic">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="keterangan"
                                            class="col-sm-4 col-form-label">{{ __('Keterangan') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" placeholder="Keterangan" class="form-control" id="keterangan" name="keterangan">
                                        </div>
                                    </div>
                                    {{-- <div class="form-group row">
                                        <label for="file" class="col-sm-4 col-form-label">{{ __('File') }}</label>
                                        <div class="col-sm-8">
                                            <input type="file" class="form-control" id="file" name="file">
                                        </div>
                                    </div> --}}




                                </form>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default"
                                    data-dismiss="modal">{{ __('Cancel') }}</button>
                                <button id="button-save" type="button" class="btn btn-primary"
                                    onclick="$('#save').submit();">{{ __('Add') }}</button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="delete-sr">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 id="modal-title" class="modal-title">{{ __('Delete Detail') }}</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form role="form" id="delete-detail" action="{{ route('service.delete') }}"
                                    method="post">
                                    @csrf
                                    @method('delete')
                                    <input type="hidden" id="delete_detail" name="delete_detail_id">
                                </form>
                                <div>
                                    <p>Anda yakin ingin menghapus service <span id="kodeMaterial"
                                            class="font-weight-bold"></span>?</p>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default"
                                    data-dismiss="modal">{{ __('Batal') }}</button>
                                <button id="button-save-detail" type="button" class="btn btn-danger"
                                    onclick="$('#delete-detail').submit();">{{ __('Ya, hapus') }}</button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="delete-suratkeluar">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 id="modal-title" class="modal-title">{{ __('Delete Service') }}</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form role="form" id="delete-suratkeluar-form" action="{{ route('service.destroy') }}"
                                    method="post">
                                    @csrf
                                    @method('delete')
                                    <input type="hidden" id="delete_suratkeluar_id" name="delete_id">
                                </form>
                                <div>
                                    <p>Anda yakin ingin menghapus service <span id="delete_name"
                                            class="font-weight-bold"></span>?</p>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default"
                                    data-dismiss="modal">{{ __('Batal') }}</button>
                                <button id="button-save-suratkeluar" type="button" class="btn btn-danger"
                                    onclick="$('#delete-suratkeluar-form').submit();">{{ __('Ya, hapus') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
    </section>
@endsection
{{-- custom Js --}}
@section('custom-js')
    <script src="/plugins/toastr/toastr.min.js"></script>
    <script src="/plugins/select2/js/select2.full.min.js"></script>
    <script src="/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
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
            $('#kode').val('');
            $('#keterangan').val('');
        }

        function addSR() {
            $('#modal-title1').text("Add Service");
            $('#button-save').text("Tambahkan");
            $('#save_id').val("");
            resetForm();
        }

        $('#detail-sr').on('hidden.bs.modal', function() {
            $('#container-product').addClass('d-none');
            $('#container-product').removeClass('col-5');
            $('#container-form').addClass('col-12');
            $('#container-form').removeClass('col-7');
            $('#button-tambah-detail').text('Tambah Item Detail');
        });

        function showAddProduct() {
            $('#id').val("")
            if ($('#detail-sr').find('#container-product').hasClass('d-none')) {
                $('#detail-sr').find('#container-product').removeClass('d-none');
                $('#detail-sr').find('#container-product').addClass('col-5');
                $('#detail-sr').find('#container-form').removeClass('col-12');
                $('#detail-sr').find('#container-form').addClass('col-7');
                $('#button-tambah-produk').text('Kembali');
            } else {
                $('#detail-sr').find('#container-product').removeClass('col-5');
                $('#detail-sr').find('#container-product').addClass('d-none');
                $('#detail-sr').find('#container-form').addClass('col-12');
                $('#detail-sr').find('#container-form').removeClass('col-7');
                $('#button-tambah-produk').text('Tambah Item Detail');
                clearForm();
            }
        }

        function editSR(data) {
            console.log(data)
            resetForm();
            $('#modal-title1').text("Edit Service");
            $('#button-save').text("Simpan");
            $('#id').val(data.id);
            // $('#kode_tempat').val(data.kode_tempat);
            $('#no_sr').val(data.no_sr);
            $('#nama_tempat1').val(data.nama_tempat);
            $('#lokasi1').val(data.lokasi);
            $('#nama_proyek1').val(data.nama_proyek);
            $('#trainset').val(data.trainset);
            $('#car').val(data.car);
            $('#perawatan').val(data.perawatan);
            $('#perawatan_mulai').val(data.perawatan_mulai);
            $('#perawatan_selesai').val(data.perawatan_selesai);
            $('#pic').val(data.pic);
            $('#keterangan').val(data.keterangan);
            // $('#update-kode-aset').modal('show');
        }

        function emptyTableProducts() {
            $('#table-sr').empty();
            $('#no_surat').text("");
            $('#tgl_surat').text("");
            $('proyek').text("");
        }

        function loader(status = 1) {
            if (status == 1) {
                $('#loader').show();
            } else {
                $('#loader').hide();
            }
        }


        function clearForm() {
            $('#kode_material').val(""); // Mengosongkan nilai input dengan ID 'kode_material'
            $('#desc_material').val(""); // Mengosongkan nilai input dengan ID 'desc_material'
            $('#spek').val(""); // Mengosongkan nilai input dengan ID 'spek'
            $('#p1').val(""); // Mengosongkan nilai input dengan ID 'p1'
            $('#p3').val(""); // Mengosongkan nilai input dengan ID 'p3'
            $('#p6').val(""); // Mengosongkan nilai input dengan ID 'p6'
            $('#p12').val(""); // Mengosongkan nilai input dengan ID 'p12'
            $('#p24').val(""); // Mengosongkan nilai input dengan ID 'p24'
            $('#p48').val(""); // Mengosongkan nilai input dengan ID 'p48'
            $('#p60').val(""); // Mengosongkan nilai input dengan ID 'p60'
            $('#p72').val(""); // Mengosongkan nilai input dengan ID 'p72'
            $('#vol_protective').val(""); // Mengosongkan nilai input dengan ID 'vol_protective'
            $('#satuan').val(
            ""); // Mengatur kembali nilai input dengan ID 'satuan' menjadi nilai default atau string kosong
            // $('#form').hide(); // Menggantikan nilai dari elemen dengan ID 'form' dengan mengubah properti display menjadi none
        }

        function SRupdate() {
            const id = $('#sr_id').val(); // Mendapatkan id
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', $('#id').val());
            formData.append('id_sr', id);
            formData.append('kode_material', $('#kode_material').val());
            formData.append('desc_material', $('#desc_material').val());
            formData.append('spek', $('#spek').val());
            formData.append('p1', $('#p1').val());
            formData.append('p3', $('#p3').val());
            formData.append('p6', $('#p6').val());
            formData.append('p12', $('#p12').val());
            formData.append('p24', $('#p24').val());
            formData.append('p48', $('#p48').val());
            formData.append('p60', $('#p60').val());
            formData.append('p72', $('#p72').val());
            formData.append('vol_protective', $('#vol_protective').val());
            formData.append('satuan', $('#satuan').val());

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
            // Lakukan operasi update data
            // Misalnya, Anda dapat menggunakan AJAX untuk mengirim permintaan ke backend
            // atau menggunakan fungsi JavaScript lainnya yang sesuai dengan logika aplikasi Anda
            $.ajax({
                url: "{{ url('service/update_service_detail') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#button-update-sr').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                    $('#button-update-sr').attr('disabled', true);
                },
                success: function(data) {
                    if (!data.success) {
                        toastr.error(data.message);
                        $('#button-update-sr').html('Tambahkan');
                        $('#button-update-sr').attr('disabled', false);
                        return;
                    }
                    $('#id').val(data.sr.id);
                    $('#no_surat').text(data.sr.no_sr);
                    $('#tgl_surat').text(data.sr.tanggal);
                    $('#proyek').text(data.sr.proyek);
                    $('#button-update-sr').html('Tambahkan');
                    $('#button-update-sr').attr('disabled', false);
                    clearForm();
                    if (data.sr.details.length == 0) {
                        $('#table-sr').append(
                            '<tr><td colspan="15" class="text-center">Tidak ada produk</td></tr>');
                    } else {
                        $('#table-sr').empty();
                        $.each(data.sr.details, function(key, value) {
                            console.log(value)
                            var rowIndex = key + 1;
                            var editButton =
                                '<button type="button" class="btn btn-success btn-xs mr-1" data-row-id="' +
                                value.id + '" title="Edit" onclick="editRow(\'' + value.id + '\', \'' +
                                value.kode_material + '\', \'' + value.desc_material + '\', \'' + value
                                .spek + '\', \'' + value.p1 +
                                '\', \'' + value.p3 + '\', \'' + value.p6 + '\', \'' + value.p12 +
                                '\', \'' + value.p24 + '\', \'' + value.p48 +
                                '\', \'' + value.p60 + '\', \'' + value.p72 + '\', \'' + value
                                .vol_protective + '\', \'' + value.satuan +
                                '\')"><i class="fas fa-edit"></i></button>';

                            var deleteButton =
                                '<button type="button" class="btn btn-danger btn-xs mr-1"' +
                                ' onclick="deleteDetail(' + value.id + ', \'' + value.kode_material
                                .toString() + '\')"' +
                                ' title="Delete">' +
                                '<i class="fas fa-trash"></i>' +
                                '</button>';

                            $('#table-sr').append('<tr><td>' + rowIndex + '</td><td>' + value
                                .kode_material + '</td><td>' +
                                value.desc_material + '</td><td>' + value.spek + '</td><td>' + value
                                .p1 + '</td><td>' + value.p3 +
                                '</td><td>' + value.p6 + '</td><td>' + value.p12 + '</td><td>' +
                                value.p24 + '</td><td>' + value.p48 + '</td><td>' + value.p60 +
                                '</td><td>' + value.p72 +
                                '</td><td>' + value.vol_protective + '</td><td>' + value.satuan +
                                '</td><td>' + editButton + deleteButton +
                                '</td></tr>');
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error jika diperlukan
                    console.error(error);
                }
            });
        }

        function updateData(formData) {
            // Lakukan operasi insert data
            // Misalnya, Anda dapat menggunakan AJAX untuk mengirim permintaan ke backend
            // atau menggunakan fungsi JavaScript lainnya yang sesuai dengan logika aplikasi Anda
            $.ajax({
                url: "{{ url('service/update_detail') }}", // Ganti URL sesuai dengan endpoint untuk operasi insert
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#button-update-sr').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                    $('#button-update-sr').attr('disabled', true);
                },
                success: function(data) {
                    if (!data.success) {
                        toastr.error(data.message);
                        $('#button-update-sr').html('Tambahkan');
                        $('#button-update-sr').attr('disabled', false);
                        return;
                    }
                    $('#id').val(data.sr.id);
                    $('#no_surat').text(data.sr.no_sr);
                    $('#tgl_surat').text(data.sr.tanggal);
                    $('#proyek').text(data.sr.proyek);
                    $('#button-update-sr').html('Tambahkan');
                    $('#button-update-sr').attr('disabled', false);
                    clearForm();
                    if (data.sr.details.length == 0) {
                        $('#table-sr').append(
                            '<tr><td colspan="15" class="text-center">Tidak ada produk</td></tr>');
                    } else {
                        $('#table-sr').empty();
                        $.each(data.sr.details, function(key, value) {
                            console.log(value)
                            var rowIndex = key + 1;
                            var editButton =
                                '<button type="button" class="btn btn-success btn-xs mr-1" data-row-id="' +
                                value.id + '" title="Edit" onclick="editRow(\'' + value.id + '\', \'' +
                                value.kode_material + '\', \'' + value.desc_material + '\', \'' + value
                                .spek + '\', \'' + value.p1 +
                                '\', \'' + value.p3 + '\', \'' + value.p6 + '\', \'' + value.p12 +
                                '\', \'' + value.p24 + '\', \'' + value.p48 +
                                '\', \'' + value.p60 + '\', \'' + value.p72 + '\', \'' + value
                                .vol_protective + '\', \'' + value.satuan +
                                '\')"><i class="fas fa-edit"></i></button>';

                            var deleteButton =
                                '<button type="button" class="btn btn-danger btn-xs mr-1"' +
                                ' onclick="deleteDetail(' + value.id + ', \'' + value.kode_material
                                .toString() + '\')"' +
                                ' title="Delete">' +
                                '<i class="fas fa-trash"></i>' +
                                '</button>';

                            $('#table-sr').append('<tr><td>' + rowIndex + '</td><td>' + value
                                .kode_material + '</td><td>' +
                                value.desc_material + '</td><td>' + value.spek + '</td><td>' + value
                                .p1 + '</td><td>' + value.p3 +
                                '</td><td>' + value.p6 + '</td><td>' + value.p12 + '</td><td>' +
                                value.p24 + '</td><td>' + value.p48 + '</td><td>' + value.p60 +
                                '</td><td>' + value.p72 +
                                '</td><td>' + value.vol_protective + '</td><td>' + value.satuan +
                                '</td><td>' + editButton + deleteButton +
                                '</td></tr>');
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error jika diperlukan
                    console.error(error);
                }
            });
        }

        // Veri UPDATE AJA
        // function SRupdate() {
        //     const id = $('#sr_id').val();
        //     var formData = new FormData();
        //     formData.append('_token', '{{ csrf_token() }}');
        //     formData.append('id_sr', id);
        //     formData.append('kode_material', $('#kode_material').val());
        //     formData.append('desc_material', $('#desc_material').val());
        //     formData.append('spek', $('#spek').val());
        //     formData.append('p1', $('#p1').val());
        //     formData.append('p3', $('#p3').val());
        //     formData.append('p6', $('#p6').val());
        //     formData.append('p12', $('#p12').val());
        //     formData.append('p24', $('#p24').val());
        //     formData.append('p48', $('#p48').val());
        //     formData.append('p60', $('#p60').val());
        //     formData.append('p72', $('#p72').val());
        //     formData.append('vol_protective', $('#vol_protective').val());
        //     formData.append('satuan', $('#satuan').val());

        //     // Mengirimkan permintaan AJAX
        //     $.ajax({
        //         url: "{{ url('service/update_service_detail') }}",
        //         type: "POST",
        //         data: formData,
        //         contentType: false,
        //         processData: false,
        //         beforeSend: function() {
        //             $('#button-update-sr').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        //             $('#button-update-sr').attr('disabled', true);
        //         },
        //         success: function(data) {
        //             if (!data.success) {
        //                 toastr.error(data.message);
        //                 $('#button-update-sr').html('Tambahkan');
        //                 $('#button-update-sr').attr('disabled', false);
        //                 return;
        //             }
        //             $('#id').val(data.sr.id);
        //             $('#no_surat').text(data.sr.no_sr);
        //             $('#tgl_surat').text(data.sr.tanggal);
        //             $('#proyek').text(data.sr.proyek);
        //             $('#button-update-sr').html('Tambahkan');
        //             $('#button-update-sr').attr('disabled', false);
        //             clearForm();
        //             if (data.sr.details.length == 0) {
        //                 $('#table-sr').append(
        //                     '<tr><td colspan="15" class="text-center">Tidak ada produk</td></tr>');
        //             } else {
        //                 $('#table-sr').empty();
        //                 $.each(data.sr.details, function(key, value) {
        //                     console.log(value)
        //                     var rowIndex = key + 1;
        //                     var editButton =
        //                         '<button type="button" class="btn btn-success btn-xs mr-1" data-row-id="' +
        //                         value.id + '" title="Edit" onclick="editRow(\'' + value.id + '\', \'' +
        //                         value.kode_material + '\', \'' + value.desc_material + '\', \'' + value
        //                         .spek + '\', \'' + value.p1 +
        //                         '\', \'' + value.p3 + '\', \'' + value.p6 + '\', \'' + value.p12 +
        //                         '\', \'' + value.p24 + '\', \'' + value.p48 +
        //                         '\', \'' + value.p60 + '\', \'' + value.p72 + '\', \'' + value
        //                         .vol_protective + '\', \'' + value.satuan +
        //                         '\')"><i class="fas fa-edit"></i></button>';

        //                     var deleteButton =
        //                         '<button type="button" class="btn btn-danger btn-xs mr-1"' +
        //                         ' onclick="deleteDetail(' + value.id + ', \'' + value.kode_material
        //                         .toString() + '\')"' +
        //                         ' title="Delete">' +
        //                         '<i class="fas fa-trash"></i>' +
        //                         '</button>';

        //                     $('#table-sr').append('<tr><td>' + rowIndex + '</td><td>' + value
        //                         .kode_material + '</td><td>' +
        //                         value.desc_material + '</td><td>' + value.spek + '</td><td>' + value
        //                         .p1 + '</td><td>' + value.p3 +
        //                         '</td><td>' + value.p6 + '</td><td>' + value.p12 + '</td><td>' +
        //                         value.p24 + '</td><td>' + value.p48 + '</td><td>' + value.p60 +
        //                         '</td><td>' + value.p72 +
        //                         '</td><td>' + value.vol_protective + '</td><td>' + value.satuan +
        //                         '</td><td>' + editButton + deleteButton +
        //                         '</td></tr>');
        //                 });
        //             }
        //         }
        //     });
        // }

        // on modal #detail-sr open
        $('#detail-sr').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var data = button.data('detail');
            console.log(data);
            lihatSR(data);
        });

        function lihatSR(data) {
            console.log(data)
            emptyTableProducts()
            clearForm(); // Kosongkan formulir
            $('#modal-title').text("Detail Service"); // Atur judul modal
            $('#button-save').text("Tambahkan"); // Atur teks tombol simpan
            resetForm(); // Reset formulir
            $('#nama_tempat').text(data.nama_tempat);
            $('#nama_proyek').text(data.nama_proyek);
            $('#lokasi').text(data.lokasi);
            $('#button-tambah-produk').text('Tambah Item Detail'); // Atur teks tombol tambah produk
            $('#id').val(data.id); // Set nilai ID
            $('#no_surat').text(data.no_sr); // Set nomor surat
            $('#tgl_surat').text(data.tanggal); // Set tanggal surat
            $('#sr_id').val(data.id); // Set nilai ID PR
            $('#table-sr').empty(); // Kosongkan tabel PR

            // Nonaktifkan tombol tambah produk jika editable adalah false
            if (data.editable == 0) {
                $('#button-tambah-produk').attr('disabled', true);
            } else {
                $('#button-tambah-produk').attr('disabled', false);
            }

            // Kirim permintaan AJAX untuk mendapatkan detail PR
            $.ajax({
                url: "{{ url('service/service_detail') }}" + "/" + data.id,
                type: "GET",
                dataType: "json",
                beforeSend: function() {
                    $('#table-sr').append('<tr><td colspan="15" class="text-center">Loading...</td></tr>');
                    $('#button-cetak-sr').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                    $('#button-cetak-sr').attr('disabled', true);
                },

                success: function(data) {
                    console.log(data);
                    $('#id').val(data.sr.id); // Set nilai ID PR
                    $('#no_surat').text(data.sr.no_sr); // Set nomor surat PR
                    $('#tgl_surat').text(data.sr.tanggal); // Set tanggal surat PR
                    $('#proyek').text(data.sr.proyek); // Set proyek PR
                    $('#button-cetak-sr').html('<i class="fas fa-print"></i> Cetak');
                    $('#button-cetak-sr').attr('disabled', false);
                    var no = 1;

                    if (data.sr.details.length == 0) {
                        $('#table-sr').empty();
                        $('#table-sr').append(
                            '<tr><td colspan="15" class="text-center">Tidak ada produk</td></tr>'
                        ); // Tambahkan pesan bahwa tidak ada produk
                    } else {
                        $('#table-sr').empty();
                        $.each(data.sr.details, function(key, value) {
                            console.log(value)
                            var rowIndex = key + 1;
                            var editButton =
                                '<button type="button" class="btn btn-success btn-xs mr-1" data-row-id="' +
                                value.id + '" title="Edit" onclick="editRow(\'' + value.id + '\', \'' +
                                value.kode_material + '\', \'' + value.desc_material + '\', \'' + value
                                .spek + '\', \'' + value.p1 +
                                '\', \'' + value.p3 + '\', \'' + value.p6 + '\', \'' + value.p12 +
                                '\', \'' + value.p24 + '\', \'' + value.p48 +
                                '\', \'' + value.p60 + '\', \'' + value.p72 + '\', \'' + value
                                .vol_protective + '\', \'' + value.satuan +
                                '\')"><i class="fas fa-edit"></i></button>';

                            var deleteButton =
                                '<button type="button" class="btn btn-danger btn-xs mr-1"' +
                                ' onclick="deleteDetail(' + value.id + ', \'' + value.kode_material
                                .toString() + '\')"' +
                                ' title="Delete">' +
                                '<i class="fas fa-trash"></i>' +
                                '</button>';

                            $('#table-sr').append('<tr><td>' + rowIndex + '</td><td>' + value
                                .kode_material + '</td><td>' +
                                value.desc_material + '</td><td>' + value.spek + '</td><td>' + value
                                .p1 + '</td><td>' + value.p3 +
                                '</td><td>' + value.p6 + '</td><td>' + value.p12 + '</td><td>' +
                                value.p24 + '</td><td>' + value.p48 + '</td><td>' + value.p60 +
                                '</td><td>' + value.p72 +
                                '</td><td>' + value.vol_protective + '</td><td>' + value.satuan +
                                '</td><td>' + editButton + deleteButton +
                                '</td></tr>');
                        });
                    }
                }
            });
        }


        function editRow(id, kode_material, desc_material, spek, p1, p3, p6, p12, p24, p48, p60, p72, vol_protective,
            satuan) {
            console.log(id, kode_material, desc_material, spek, p1, p3, p6, p12, p24, p48, p60, p72, vol_protective, satuan)
            resetForm();
            $('#modal-title').text("Edit Detail");
            $('#button-update-sr').text("Simpan");
            $('#id').val(id);
            // $('#kode_tempat').val(data.kode_tempat);
            $('#kode_material').val(kode_material) // Mengosongkan nilai input dengan ID 'kode_material'
            $('#desc_material').val(desc_material); // Mengosongkan nilai input dengan ID 'desc_material'
            $('#spek').val(spek); // Mengosongkan nilai input dengan ID 'spek'
            $('#p1').val(p1); // Mengosongkan nilai input dengan ID 'p1'
            $('#p3').val(p3); // Mengosongkan nilai input dengan ID 'p3'
            $('#p6').val(p6); // Mengosongkan nilai input dengan ID 'p6'
            $('#p12').val(p12); // Mengosongkan nilai input dengan ID 'p12'
            $('#p24').val(p24); // Mengosongkan nilai input dengan ID 'p24'
            $('#p48').val(p48); // Mengosongkan nilai input dengan ID 'p48'
            $('#p60').val(p60); // Mengosongkan nilai input dengan ID 'p60'
            $('#p72').val(p72); // Mengosongkan nilai input dengan ID 'p72'
            $('#vol_protective').val(vol_protective); // Mengosongkan nilai input dengan ID 'vol_protective'
            $('#satuan').val(
                satuan); // Mengatur kembali nilai input dengan ID 'satuan' menjadi nilai default atau string kosong

            if ($('#detail-sr').find('#container-product').hasClass('d-none')) {
                $('#detail-sr').find('#container-product').removeClass('d-none');
                $('#detail-sr').find('#container-product').addClass('col-5');
                $('#detail-sr').find('#container-form').removeClass('col-12');
                $('#detail-sr').find('#container-form').addClass('col-7');
                $('#button-tambah-produk').text('Kembali');
            } else {
                $('#detail-sr').find('#container-product').removeClass('col-5');
                $('#detail-sr').find('#container-product').addClass('d-none');
                $('#detail-sr').find('#container-form').addClass('col-12');
                $('#detail-sr').find('#container-form').removeClass('col-7');
                $('#button-tambah-produk').text('Tambah Item Detail');
                clearForm();
            }
        }


        function deleteDetail(id, kode_material) {
            console.log(kode_material);
            $('#delete_detail').val(id);
            $('#kodeMaterial').text(kode_material);
            $('#delete-sr').modal('show');
        }

        function detailSR(data) {
            $('#modal-title').text("Edit Service"); // Atur judul modal
            $('#button-save').text("Simpan"); // Atur teks tombol simpan
            resetForm(); // Reset formulir
            $('#save_id').val(data.id); // Set nilai ID untuk disimpan
            $('#no_sr').val(data.no_sr); // Set nomor PR
            $('#tgl_sr').val(data.tgl_sr); // Set tanggal PR
            $('#proyek_id').val(data.proyek_id); // Set ID proyek
            $('#dasar_sr').val(data.dasar_sr); // Set dasar PR
            // alert(proyek_id) // Kode ini tampaknya hanya untuk debugging, tidak perlu dimasukkan di sini
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


        function deleteSR(data) {
            $('#delete_suratkeluar_id').val(data.id);
            $('#delete_name').text(data.nama_proyek);
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
    {{-- @if (Session::has('show_detail_modal'))
        <script>
        console.log('hoaks')
            $('#detail-sr').modal('show');
        </script>
        @endif --}}
@endsection
