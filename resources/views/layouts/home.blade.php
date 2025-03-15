<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} | @yield('title')</title>
    <link rel="icon" href="{{ asset('img/logoimss.png') }}" type="image/png">
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
    <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">

    @hasSection('custom-css')
        @yield('custom-css')
    @endif
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper" style="margin-left: 0">
        <nav class="main-header navbar navbar-expand navbar-white navbar-dark" style="margin-left: 0; background-color: firebrick;">
            <ul class="navbar-nav flex-grow-1 justify-content-between align-items-center flex-wrap">
                
                <!-- Logo IMSS -->
                <li class="nav-item">
                    <img src="{{ asset('img/IMSS.jpg') }}" class="d-block mx-auto img-fluid" height="55" alt="IMSS Logo" style="object-fit: contain; max-height: 55px;">
                </li>
        
                <!-- Kontainer untuk menu responsif -->
                <div class="d-flex align-items-center flex-wrap justify-content-center">
                    
                    <!-- Link Dashboard -->
                    <li class="nav-item mx-0">
                        @if (Auth::check())
                            <a href="{{ route('home') }}" class="nav-link text-center">Dashboard</a>
                        @endif
                    </li>
        
                    <!-- Nama Pengguna atau Tombol Login -->
                    <li class="nav-item mx-1">
                        @if (Auth::check())
                            <span class="nav-link text-center" style="{{ strlen(Auth::user()->name) > 10 ? 'font-size: 15px;' : 'font-size: 16px;' }}">
                                Halo, {{ Auth::user()->name }}!
                            </span>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-light btn-sm text-center">Login</a>
                        @endif
                    </li>
        
                    <!-- Tombol Logout -->
                    <li class="nav-item mx-2">
                        @if (Auth::check())
                            <form id="logout-form" action="{{ route('logout') }}" method="post" class="d-inline">@csrf</form>
                            <a href="javascript:;" onclick="document.getElementById('logout-form').submit();" class="btn btn-light btn-sm text-center">Logout</a>
                        @endif
                    </li>
                </div>
            </ul>
        </nav>
        
        {{-- <nav class="main-header navbar navbar-expand navbar-white navbar-dark"
            style="margin-left: 0;background-color: firebrick">
            <ul class="navbar-nav d-flex justify-content-between w-100 align-items-center">
                <li class="nav-item d-none d-sm-inline-block">
                    <img src="{{ asset('img/IMSS.jpg') }}" class="d-block w-100" height="55"
                                    alt="" style="object-fit: contain">
                </li>
                <div>
                    <li class="nav-item d-none d-sm-inline-block">
                        @if (Auth::check())
                            <a href="{{ route('home') }}" class="nav-link">Dashboard</a>
                        @endif
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <span class="nav-link">
                            @if (Auth::check())
                                Halo, {{ Auth::user()->name }}!
                            @else
                                <a href="{{ route('login') }}" class="btn btn-light mb-4">Login</a>
                            @endif
                        </span>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <span class="nav-link">
                            @if (Auth::check())
                                <form id="logout" action="{{ route('logout') }}" method="post">@csrf</form>
                                <a href="javascript:;" onclick="document.getElementById('logout').submit();"
                                    class="btn btn-light">Logout</a>
                            @endif
                        </span>
                    </li>
                </div>
            </ul>
        </nav> --}}
        {{-- <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: maroon;">
            <a href="/" class="brand-link text-center" style="background-color: black;">
                <span class="brand-text font-weight-bold">PT.IMSS</span>
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
                            <li class="nav-header">Product</li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::current()->getName() == 'products' ? 'active' : '' }}"
                                    href="{{ route('products') }}">
                                    <i class="nav-icon fas fa-boxes"></i>
                                    <p class="text">{{ __('Stok Barang') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::current()->getName() == 'sjn' ? 'active' : '' }}"
                                    href="{{ route('sjn') }}">
                                    <i class="nav-icon fas fa-envelope"></i>
                                    <p class="text">{{ __('Surat Jalan') }}</p>
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
                                <a class="nav-link {{ Route::current()->getName() == 'vendor.index' ? 'active' : '' }}"
                                    href="{{ route('vendor.index') }}">
                                    <i class="nav-icon fas fa-user-cog"></i>
                                    <p class="text">{{ __('Vendor') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::current()->getName() == 'kode_material.index' ? 'active' : '' }}"
                                    href="{{ url('products/kode_material') }}">
                                    <i class="nav-icon fas fa-pallet"></i>
                                    <p class="text">{{ __('Kode Material') }}</p>
                                </a>
                            </li>
                            @if (Auth::user()->role == 0)
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
                                </li>
                            @endif
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
        </aside> --}}

        <div class="content-wrapper" style="margin-left: 0">
            @yield('content')
        </div>

        <footer class="main-footer" style="margin-left: 0">
            <b>PT</b> {{ config('app.version') }}
            <img src="{{ asset('img/garis.jpg')}}" style="width: 100%;" />
        </footer>

        <aside class="control-sidebar control-sidebar-dark">
        </aside>
    </div>

    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
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
    @hasSection('custom-js')
        @yield('custom-js')
    @endif
    <script>
        let table = new DataTable('#datatable', {
            responsive: true
        });
    </script>
</body>

</html>
