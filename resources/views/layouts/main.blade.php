<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet"
        href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css"> --}}
    {{-- <script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script> --}}

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    @hasSection('custom-css')
        @yield('custom-css')
    @endif

    <style>
        .notifi-container {
            max-height: 240px;
            /* Sesuaikan dengan tinggi yang diinginkan */
            overflow-y: auto;
        }

        .notifi-item {
            display: flex;
            border-top: 1px solid #eee;
            padding: 5px 10px;
            margin-bottom: 0px;
            cursor: pointer;
        }

        .notifi-item:hover {
            background-color: #eee;
        }

        .notifi-item .text h4 {
            color: #777;
            font-size: 16px;
            margin-top: 1px;
        }

        .notifi-item .text p {
            color: #aaa;
            font-size: 12px;
        }
    </style>


</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <span class="nav-link">@yield('title')</span>
                </li>
            </ul>




            @if (!empty($warehouse) && !Request::is('dashboard'))
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                            @if (Session::has('selected_warehouse_name'))
                                <i class="fas fa-warehouse"></i>
                                <span>{{ Session::get('selected_warehouse_name') }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right"
                            style="left: inherit; right: 0px;">
                            <span class="dropdown-item dropdown-header">Warehouse</span>
                            @foreach ($warehouse as $w)
                                <a href="{{ route('warehouse') }}/change/{{ $w->warehouse_id }}"
                                    class="dropdown-item">
                                    {{ $w->warehouse_name }}
                                </a>
                            @endforeach
                        </div>
                    </li>
                </ul>
            @endif


            @if (!empty($warehouse) && Request::is('dashboard'))

                <ul class="navbar-nav ml-auto">
                    @if (Auth::user()->role == 5 || Auth::user()->role == 7)
                        <button type="button" class="btn btn-link" data-toggle="dropdown" href="#"
                            aria-expanded="false">
                            <i class="material-icons text-secondary">notifications</i> <span
                                class="badge badge-light text-danger font-weight-bold">{{ $jumlahDataHariIni }}</span>

                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right"
                            style="left: inherit; right: 0px;">
                            <a href="" class="dropdown-item dropdown-header">Surat Masuk <span
                                    class="text-danger font-weight-bold">{{ $jumlahDataHariIni }}</span>
                            </a>

                            <div class="notifi-container">
                                @foreach ($suratMasuks as $suratMasuk)
                                    <div class="notifi-item">
                                        <a href="{{ asset('sk/' . $suratMasuk['file']) }}" download>
                                            <div class="text">
                                                <h4>Asal: {{ $suratMasuk['asal'] }}</h4>
                                                <p>Waktu Masuk:
                                                    {{ date('d M Y H:i', strtotime($suratMasuk['tanggalMasuk'])) }}</p>
                                                <p>Uraian: {{ $suratMasuk['uraian'] }}</p>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- @if (Auth::user()->role == 1)
                        <button type="button" class="btn btn-link" data-toggle="dropdown" href="#"
                            aria-expanded="false">
                            <i class="material-icons text-secondary">notifications</i> <span
                                class="badge badge-light text-danger font-weight-bold">{{ $totalPurchaseRequests }}</span>

                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right"
                            style="left: inherit; right: 0px;">
                            <a href="{{ route('purchase_request.index') }}"
                                class="dropdown-item dropdown-header">Purchase Request <span
                                    class="text-danger font-weight-bold">{{ $totalPurchaseRequests }}</span>
                            </a>

                            <div class="notifi-container">
                                @foreach ($purchaseRequests as $data)
                                    <div class="notifi-item">
                                        <a title="Lihat Detail" data-toggle="modal" data-target="#detail-pr"
                                            data-detail="{{ json_encode($data) }}">
                                            <div class="text">
                                                <h4>{{ $data->nama_proyek }}</h4>
                                                <p>Tanggal: {{ $data->tgl_pr }}</p>
                                                <p>Nomor PR: {{ $data->no_pr }}</p>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif --}}

                    @if (Auth::user()->role == 2 || Auth::user()->role == 3 || Auth::user()->role == 0 || Auth::user()->role == 1)
                        <button type="button" class="btn btn-link" data-toggle="dropdown" href="#"
                            aria-expanded="false">
                            <i class="material-icons text-secondary">notifications</i> <span
                                class="badge badge-light text-danger font-weight-bold">{{ $totalPurchaseRequests }}</span>

                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right"
                            style="left: inherit; right: 0px;">
                            <a href="{{ route('product.trackingwil') }}" class="dropdown-item dropdown-header">Tracking
                                Purchase Request <span
                                    class="text-danger font-weight-bold">{{ $totalPurchaseRequests }}</span>
                            </a>

                            <div class="notifi-container">
                                @foreach ($purchaseRequests as $data)
                                    <div class="notifi-item">
                                        <a title="Lihat Detail" data-toggle="modal" data-target="#detail-track-pr"
                                            data-detail="{{ json_encode($data) }}">
                                            <div class="text">
                                                <h4>{{ $data->nama_pekerjaan }}</h4>
                                                <p>Tanggal: {{ $data->tgl_pr }}</p>
                                                <p>Nomor PR: {{ $data->no_pr }}</p>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif





                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                            @if (Session::has('selected_warehouse_name'))
                                <i class="fas fa-warehouse"></i>
                                <span>{{ Session::get('selected_warehouse_name') }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right"
                            style="left: inherit; right: 0px;">
                            <span class="dropdown-item dropdown-header">Warehouse</span>
                            @foreach ($warehouse as $w)
                                <a href="{{ route('warehouse') }}/change/{{ $w->warehouse_id }}"
                                    class="dropdown-item">
                                    {{ $w->warehouse_name }}
                                </a>
                            @endforeach
                        </div>
                    </li>
                </ul>
            @endif




        </nav>
        <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: maroon;">
            <a href="/" class="brand-link text-center" style="background-color: rgb(255, 253, 253);">
                <img src="{{ asset('img/imss-remove.png') }}" class="d-block w-100" height="30" alt=""
                    style="object-fit: contain">
                <!--  <span class="brand-text font-weight-bold">{{ config('app.name', 'Warehouse') }}</span> -->
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        @if (Auth::check())
                            <li class="nav-item">
                                <a class="nav-link {{ Route::current()->getName() == 'home' ? 'active' : '' }}"
                                    href="{{ route('home') }}">
                                    <i class="nav-icon fas fa-home"></i>
                                    <p class="text">{{ __('Dashboard') }}</p>
                                </a>
                            </li>
                            @if (Auth::user()->role == 0 || Auth::user()->role == 4)
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() == 'products.wip' ? 'active' : '' }}"
                                        href="{{ route('products.wip') }}">
                                        <i class="nav-icon fas fa-spinner"></i>
                                        <p class="text">{{ __('Work In Progress (WIP)') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() == 'products.wip.history' ? 'active' : '' }}"
                                        href="{{ route('products.wip.history') }}">
                                        <i class="nav-icon fas fa-history"></i>
                                        <p class="text">{{ __('WIP History') }}</p>
                                    </a>
                                </li>
                            @endif
                            @if (Auth::user()->role == 0 || Auth::user()->role == 1 || Auth::user()->role == 4)
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() == 'vendor.index' ? 'active' : '' }}"
                                        href="{{ route('vendor.index') }}">
                                        <i class="nav-icon fas fa-user-cog"></i>
                                        <p class="text">{{ __('Vendor') }}</p>
                                    </a>
                                </li>
                            @endif
                            @if (Auth::user()->role == 0 || Auth::user()->role == 4)
                                <li class="nav-header">Product</li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() == 'products' ? 'active' : '' }}"
                                        href="{{ route('products') }}">
                                        <i class="nav-icon fas fa-boxes"></i>
                                        <p class="text">{{ __('Stok Barang') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() == 'products.categories' ? 'active' : '' }}"
                                        href="{{ route('products.categories') }}">
                                        <i class="nav-icon fas fa-project-diagram"></i>
                                        <p class="text">{{ __('Kategori') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() == 'products.shelf' ? 'active' : '' }}"
                                        href="{{ route('products.shelf') }}">
                                        <i class="nav-icon fas fa-cubes"></i>
                                        <p class="text">{{ __('Lokasi Penyimpanan') }}</p>
                                    </a>
                                </li>
                            @endif
                            @if (Auth::user()->role == 0 || Auth::user()->role == 2 || Auth::user()->role == 3 || Auth::user()->role == 4)
                                {{-- <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() == 'keproyekan.index' ? 'active' : '' }}"
                                        href="{{ route('keproyekan.index') }}">
                                        <i class="nav-icon fas fa-hard-hat"></i>
                                        <p class="text">{{ __('Keproyekan') }}</p>
                                    </a>
                                </li> --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() == 'kode_material.index' ? 'active' : '' }}"
                                        href="{{ url('products/kode_material') }}">
                                        <i class="nav-icon fas fa-pallet"></i>
                                        <p class="text">{{ __('Kode Material') }}</p>
                                    </a>
                                </li>
                            @endif
                            {{-- @if (Auth::user()->role == 0)
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() == 'keproyekan.index' ? 'active' : '' }}"
                                        href="{{ route('keproyekan.index') }}">
                                        <i class="nav-icon fas fa-hard-hat"></i>
                                        <p class="text">{{ __('Keproyekan') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() == 'products.shelf' ? 'active' : '' }}"
                                        href="{{ route('products.shelf') }}">
                                        <i class="nav-icon fas fa-cubes"></i>
                                        <p class="text">{{ __('Lokasi Penyimpanan') }}</p>
                                    </a>
                                </li> --}}
                            {{-- <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() == 'products.logistik' ? 'active' : '' }}"
                                        href="{{ route('products.logistik') }}">
                                        <i class="nav-icon fas fa-cubes"></i>
                                        <p class="text">{{ __('Tes Tracking Logistik') }}</p>
                                    </a>
                                </li> --}}
                            {{-- @endif --}}
                            <li class="nav-header">Settings</li>
                            @if (Auth::user()->role == 0)
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() == 'warehouse' ? 'active' : '' }}"
                                        href="{{ route('warehouse') }}">
                                        <i class="nav-icon fas fa-warehouse"></i>
                                        <p class="text">{{ __('Warehouse') }}</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ Route::current()->getName() == 'users' ? 'active' : '' }}"
                                        href="{{ route('users') }}">
                                        <i class="nav-icon fas fa-users"></i>
                                        <p class="text">{{ __('Users') }}</p>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link {{ Route::current()->getName() == 'myaccount' ? 'active' : '' }}"
                                    href="{{ route('myaccount') }}">
                                    <i class="nav-icon fas fa-user-cog"></i>
                                    <p class="text">{{ __('My Account') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <form id="logout" action="{{ route('logout') }}" method="post">@csrf</form>
                                <a class="nav-link" href="javascript:;"
                                    onclick="document.getElementById('logout').submit();">
                                    <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                                    <p class="text">{{ __('Logout') }} ({{ Auth::user()->username }})</p>
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                                    <p class="text">{{ __('Login') }}</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            @yield('content')
        </div>

        <footer class="main-footer">
            <b>PT</b> {{ config('app.version') }}
            <img src="{{ asset('img/garis.jpg') }}" style="width: 100%;" />
        </footer>

        <aside class="control-sidebar control-sidebar-dark">
        </aside>
    </div>


    {{-- modal lihat detail --}}
    <div class="modal fade" id="detail-pr">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modal-title" class="modal-title">{{ __('Detail Purchase Request') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="row">
                            <form id="cetak-pr" method="GET" action="{{ route('cetak_pr') }}" target="_blank">
                                <input type="hidden" name="id" id="id">
                            </form>
                            <div class="col-12" id="container-form">
                                <button id="button-cetak-pr" type="button" class="btn btn-primary"
                                    onclick="document.getElementById('cetak-pr').submit();">{{ __('Cetak') }}</button>
                                <table class="align-top w-100">
                                    <tr>
                                        <td style="width: 3%;"><b>No PR</b></td>
                                        <td style="width:2%">:</td>
                                        <td style="width: 55%"><span id="no_surat"></span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Tanggal</b></td>
                                        <td>:</td>
                                        <td><span id="tgl_surat"></span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Proyek</b></td>
                                        <td>:</td>
                                        <td><span id="proyek"></span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Produk</b></td>
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
                                            <th>{{ __('Kode Material') }}</th>
                                            <th>{{ __('Uraian Barang/Jasa') }}</th>
                                            <th>{{ __('Spesifikasi') }}</th>
                                            <th>{{ __('QTY') }}</th>
                                            <th>{{ __('SAT') }}</th>
                                            <th>{{ __('Waktu Penyelesaian') }}</th>
                                            <th>{{ __('Nota Pembelian') }}</th>
                                            <th>{{ __('Keterangan') }}</th>
                                            {{-- <th>{{ __('SPPH') }}</th>
                                                <th>{{ __('PO') }}</th> --}}
                                            <th>{{ __('STATUS') }}</th>
                                        </thead>
                                        <tbody id="table-prs">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-0 d-none" id="container-product">
                                <div class="card">
                                    <div class="card-body">
                                        {{-- //radio button with label INKA or IMSS option --}}
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

                                            <input type="text" class="form-control" id="pcodes" name="pcodes"
                                                min="0" placeholder="Product Code">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" id="button-check"
                                                    onclick="productChecks()">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="loaders" class="card">
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
                                                <label for="material_kode"
                                                    class="col-sm-4 col-form-label">{{ __('Kode Material') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="material_kode">
                                                    <input type="hidden" class="form-control" id="pr_id"
                                                        disabled>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="pname"
                                                    class="col-sm-4 col-form-label">{{ __('Nama Barang') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="pname">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="spek"
                                                    class="col-sm-4 col-form-label">{{ __('Spesifikasi') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="spek">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="no_nota"
                                                    class="col-sm-4 col-form-label">{{ __('QTY') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="stock"
                                                        name="stock">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="satuan"
                                                    class="col-sm-4 col-form-label">{{ __('Satuan') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="satuan"
                                                        name="satuan">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="waktu"
                                                    class="col-sm-4 col-form-label">{{ __('Waktu Penyelesaian') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="date" class="form-control" id="waktu"
                                                        name="waktu">
                                                </div>
                                            </div>
                                            <div class="form-group row">
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
                                            </div>

                                        </form>
                                        <button id="button-update-pr" type="button" class="btn btn-primary w-100"
                                            onclick="PRupdates()">{{ __('Tambahkan') }}</button>
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
    <div class="modal fade" id="delete-pr">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modal-title" class="modal-title">{{ __('Delete Purchase Request') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form role="form" id="delete" action="{{ route('purchase_request.destroy') }}"
                        method="post">
                        @csrf
                        @method('delete')
                        <input type="hidden" id="delete_id" name="id">
                    </form>
                    <div>
                        <p>Anda yakin ingin menghapus request ini <span id="pcodes"
                                class="font-weight-bold"></span>?</p>
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

    {{-- modal lihat detail --}}
    <div class="modal fade" id="detail-track-pr">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modal-title-tracking" class="modal-title-tracking">{{ __('Detail Purchase Request') }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="row">
                            <form id="cetak-pr" method="GET" action="{{ route('cetak_pr') }}" target="_blank">
                                <input type="hidden" name="id" id="id">
                            </form>
                            <div class="col-12" id="container-form">
                                {{-- <button id="button-cetak-pr" type="button" class="btn btn-primary"
                                        onclick="document.getElementById('cetak-pr').submit();">{{ __('Cetak') }}</button> --}}
                                <table class="align-top w-100">
                                    <tr>
                                        <td style="width: 3%;"><b>No PR</b></td>
                                        <td style="width:2%">:</td>
                                        <td style="width: 55%"><span id="no_surat_tracking"></span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Tanggal</b></td>
                                        <td>:</td>
                                        <td><span id="tgl_surat_tracking"></span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Proyek</b></td>
                                        <td>:</td>
                                        <td><span id="proyek_tracking"></span></td>
                                    </tr>
                                    <tr>
                                        <td><b>Produk</b></td>
                                    </tr>
                                    {{-- <tr>
                                            <td colspan="3">
                                                <button id="button-tambah-produk" type="button" class="btn btn-info mb-3"
                                                    onclick="showAddProduct()">{{ __('Tambah Produk') }}</button>
                                            </td>
                                        </tr> --}}
                                </table>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead style="text-align: center">
                                            <th>{{ __('NO') }}</th>
                                            <th>{{ __('Kode Material') }}</th>
                                            <th>{{ __('Uraian Barang/Jasa') }}</th>
                                            <th>{{ __('Spesifikasi') }}</th>
                                            <th>{{ __('QTY') }}</th>
                                            <th>{{ __('SAT') }}</th>
                                            <th>{{ __('Waktu Penyelesaian') }}</th>
                                            <th>{{ __('Countdown') }}</th>
                                            <th>{{ __('Keterangan') }}</th>
                                            <th>{{ __('STATUS') }}</th>
                                            <th>{{ __('Ekspedisi') }}</th>
                                            <th>{{ __('QC') }}</th>
                                        </thead>
                                        <tbody id="table-tracking-pr">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-0 d-none" id="container-product">
                                <div class="card">
                                    <div class="card-body">
                                        {{-- //radio button with label INKA or IMSS option --}}
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

                                        </div>
                                    </div>
                                </div>
                                <div id="form" class="card">
                                    <div class="card-body">
                                        <form role="form" id="stock-update" method="post">
                                            @csrf
                                            <input type="hidden" id="pid" name="pid">
                                            <input type="hidden" id="type" name="type">
                                            <div class="form-group row">
                                                <label for="material_kode"
                                                    class="col-sm-4 col-form-label">{{ __('Kode Material') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="material_kode">
                                                    <input type="hidden" class="form-control" id="pr_id"
                                                        disabled>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="pname"
                                                    class="col-sm-4 col-form-label">{{ __('Nama Barang') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="pname">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="spek"
                                                    class="col-sm-4 col-form-label">{{ __('Spesifikasi') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="spek">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="no_nota"
                                                    class="col-sm-4 col-form-label">{{ __('QTY') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="stock"
                                                        name="stock">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="satuan"
                                                    class="col-sm-4 col-form-label">{{ __('Satuan') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="satuan"
                                                        name="satuan">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="waktu"
                                                    class="col-sm-4 col-form-label">{{ __('Waktu Penyelesaian') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="date" class="form-control" id="waktu"
                                                        name="waktu">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="countdown"
                                                    class="col-sm-4 col-form-label">{{ __('Countdown') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="date" class="form-control" id="countdown"
                                                        name="countdown">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="keterangan"
                                                    class="col-sm-4 col-form-label">{{ __('Keterangan') }}</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="keterangan"
                                                        name="keterangan">
                                                </div>
                                            </div>
                                        </form>
                                        <button id="button-update-pr" type="button" class="btn btn-primary w-100"
                                            onclick="PRupdates()">{{ __('Tambahkan') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery.inputmask.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
    <script src="{{ asset('js/adminlte.js') }}"></script>
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/inputmask/min/jquery.inputmask.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @hasSection('custom-js')
        @yield('custom-js')
    @endif
    <script>
        let table = new DataTable('#datatable', {
            responsive: true
        });
    </script>
    <script>
        function resetForm() {
            $('#save').trigger("reset");
            $('#barcode_preview_container').hide();
        }

        $('#detail-pr').on('hidden.bs.modal', function() {
            $('#container-product').addClass('d-none');
            $('#container-product').removeClass('col-5');
            $('#container-form').addClass('col-12');
            $('#container-form').removeClass('col-7');
            $('#button-tambah-detail').text('Tambah Item Detail');
        });


        $('#detail-track-pr').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var data = button.data('detail');
            $('#modal-title-tracking').text("Detail Request");
            resetForm();
            $('#id').val(data.id);
            $('#no_surat_tracking').text(data.no_pr);
            $('#tgl_surat_tracking').text(data.tgl_pr);
            $('#proyek_tracking').text(data.nama_pekerjaan);
            $('#proyek_id_val_tracking').val(data.proyek_id);
            $('#pr_id_tracking').val(data.id);
            $('#table-prs').empty();
            console.log(data);
            $.ajax({
                url: "{{ url('/products/purchase_request_detail') }}" + "/" + data.id,
                type: "GET",
                dataType: "json",
                beforeSend: function() {
                    $('#table-tracking-pr').append(
                        '<tr><td colspan="19" class="text-center">Loading...</td></tr>');
                    $('#button-cetak-pr').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                    $('#button-cetak-pr').attr('disabled', true);
                },
                success: function(data) {
                    console.log(data);

                    if (data.pr.details.length == 0) {
                        $('#table-tracking-pr').empty();
                        $('#table-tracking-pr').append(
                            '<tr><td colspan="19" class="text-center">Tidak ada produk</td></tr>');
                    } else {
                        $('#table-tracking-pr').empty();

                        $.each(data.pr.details, function(key, value) {

                            var id = value.id;
                            var status, spph, po;
                            if (!value.id_spph) {
                                spph = '-';
                            } else {
                                spph = value.nomor_spph
                            }

                            if (!value.id_po) {
                                po = '-';
                            } else {
                                po = value.no_po
                            }

                            // alert(value.no_sph)
                            var hasSPPH = data.pr.details.some(function(item) {
                                return item.id_spph !== null;
                            });
                            if (value.batas_akhir == null) {
                                value.batas_akhir = '-';
                            } else {
                                value.batas_akhir = value.batas_akhir;
                            }
                            if (hasSPPH) {
                                $('#edit_pr_save').prop('disabled', false);
                            } else {
                                $('#edit_pr_save').prop('disabled', true);
                            }
                            if (!value.id_spph && !value.nomor_spph) {
                                status = 'Sedang proses SPPH';
                            } else if (value.id_spph && value.nomor_spph && !value.id_nego) {
                                status = 'PROSES NEGO';
                            } else if (value.id_spph && value.nomor_spph && value.id_nego && !
                                value.id_po) {
                                status = 'PROSES PO';
                            } else if (value.id_spph && value.nomor_spph && value.id_nego &&
                                value.id_po && value.no_po) {
                                status = 'COMPLETED';
                            }
                            var date;
                            var msg = '';

                            if (value.batas_akhir == null) {
                                date = '-';
                                msg = '-';
                            } else {
                                msg = 'batas penerimaan barang : ';
                                date = value.batas_akhir;
                            }
                            const ekspedisi = value.ekspedisi ? value.ekspedisi : '-';

                            const qc = value?.qc

                            let content = ''

                            if (qc) {
                                content = `<p class="mt-2 mb-0">Penerimaan : ${qc.penerimaan}</p>
                                <p class="mt-2 mb-0">OK : ${qc.hasil_ok}</p>
                                <p class="mt-2 mb-0">NOK : ${qc.hasil_nok}</p>
                                <p class="mt-2 mb-0">${qc.tanggal_qc}</p>`
                            } else {
                                content = '-'
                            }

                            $('#table-tracking-pr').append('<tr><td>' + (key + 1) +
                                '</td><td>' + value
                                .kode_material + '</td><td>' + value.uraian + '</td><td>' +
                                value
                                .spek + '</td><td>' + value.qty + '</td><td>' + value
                                .satuan + '</td><td>' + value.waktu +
                                '</td><td style="color:' +
                                value.backgroundcolor + '">' + value.countdown +
                                '</td><td>' + value
                                .keterangan + '</td>' + '<td><b>' + status +
                                '</b><br><br><b>' +
                                msg + date + '</b></td><td style="min-width:200px">' +
                                ekspedisi +
                                '</td><td style="min-width:200px">' + content +
                                '</td></tr>'
                            );
                        });
                    }
                }
            });
        });
    </script>
</body>

</html>
