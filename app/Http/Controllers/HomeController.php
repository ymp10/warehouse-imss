<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\DetailPR;
use App\Models\Keproyekan;
// use App\Models\SuratMasuk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function getWarehouse()
    {
        $controller = new ProductController;
        return $controller->getWarehouse();
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $warehouse = $this->getWarehouse();

        // Menghitung jumlah data pada tanggal yang sama dengan tanggal hari ini
        // $jumlahDataHariIni = SuratMasuk::whereDate('tanggalMasuk', Carbon::today())->count();

        // // Mengambil 5 data terbaru SuratMasuk
        // $suratMasuks = SuratMasuk::orderBy('id', 'desc')->take(10)->get();

        // Mengambil seluruh data PurchaseRequest beserta nama_proyek menggunakan join
        // Ambil semua id_pr dari DetailPr yang memiliki status 1
        $details = DetailPr::where('status', 1)->pluck('id_pr')->unique();

        // Ambil purchase_request yang id-nya ada di dalam $details
        $purchaseRequests = PurchaseRequest::join('kontrak', 'purchase_request.proyek_id', '=', 'kontrak.id')
            ->whereIn('purchase_request.id', $details)
            ->select('purchase_request.*', 'kontrak.nama_pekerjaan')
            ->orderBy('purchase_request.id', 'desc')
            ->get();

        // Hitung jumlah purchase_request yang sesuai
        $totalPurchaseRequests = $purchaseRequests->count();

        // Debug untuk memastikan hasilnya
        // dd($totalPurchaseRequests, $purchaseRequests);

        // Mengambil seluruh data DetailPr
        $detailPrs = DetailPr::all();

        // Mengambil seluruh data Keproyekan
        $keproyekans = Keproyekan::all();


        return View::make("home")->with(compact('warehouse', 'purchaseRequests', 'detailPrs', 'keproyekans', 'totalPurchaseRequests'));
    }

    public function unauthorized()
    {
        return view('home.unauthorized');
    }

    public function indexAwal()
    {
        $menus = [
            [
                'name' => 'Logistik',
                // 'route' => 'apps/spph',
                'route' => 'div/logistik',
                'bgcolor' => 'sagegreen',
                'icon' => 'box',
                'img' => asset('img/logistik.png')

            ],
            [
                'name' => 'Wilayah 1',
                // 'route' => 'apps/purchase_request',
                'route' => 'div/wilayah1',
                'bgcolor' => 'red',
                'icon' => 'map-marker-alt',
                'img' => asset('img/wilayah1.png')
            ],

            [
                'name' => 'Wilayah 2',
                // 'route' => 'apps/purchase_request',
                'route' => 'div/wilayah2',
                'bgcolor' => 'goldenrod',
                'icon' => 'map',
                'img' => asset('img/wilayah2.png')
            ],


            [
                'name' => 'Gudang',
                'route' => 'div/gudang',
                'bgcolor' => 'blue',
                'icon' => 'warehouse',
                'img' => asset('img/warehouse.png')
            ],


            // [
            //     'name' => 'Engineer',
            //     // 'route' => 'apps/purchase_request',
            //     'route' => 'div/eng',
            //     'bgcolor' => 'violet',
            //     'icon' => 'wrench',
            //     'img' => asset('img/eng.png')
            // ],

            // [
            //     'name' => 'Surat Keluar',
            //     'route' => 'apps/surat-keluar',
            //     'bgcolor' => 'green',
            //     'icon' => 'envelope',
            //     'img' => asset('public/img/suratkeluar.png')
            // ],
        ];

        // $menus2 = [
        //     [
        //         'name' => 'Surat Keluar',
        //         'route' => 'apps/surat-keluar',
        //         'bgcolor' => 'green',
        //         'icon' => 'envelope'
        //     ],
        //     // [
        //     //     'name' => 'Peraturan Direksi',
        //     //     // 'route' => 'apps/spph',
        //     //     'route' => 'apps/peraturan-direksi',
        //     //     'bgcolor' => 'violet',
        //     //     'icon' => 'gavel'
        //     // ],
        // ];

        return view('home.dashboard', compact('menus'));
    }

    public function appType($type)
    {
        if ($type == "logistik") {
            $menus = [
                [
                    'name' => 'Purchase Request',
                    'route' => 'apps/purchase_request',
                    'bgcolor' => 'sagegreen',
                    'icon' => 'cart-arrow-down'
                ],
                [
                    'name' => 'SPPH',
                    'route' => 'apps/spph',
                    'bgcolor' => 'orange',
                    'icon' => 'mail-bulk'
                ],
                [
                    'name' => 'Tracking Purchase Request',
                    'route' => 'apps/purchase_request',
                    'bgcolor' => 'red',
                    'icon' => 'route',
                    'font-size' => '1px'
                ],
                [
                    'name' => 'Purchase Order',
                    'route' => 'apps/purchase_orders',
                    'bgcolor' => 'chocolate',
                    'icon' => 'hand-holding-usd'
                ],
            ];
            $title = "Logistik";
        } else if ($type == "gudang") {
            $menus = [
                // [
                //     'name' => 'Purchase Order',
                //     'route' => 'apps/purchase_orders',
                //     'bgcolor' => 'sagegreen',
                //     'icon' => 'hand-holding-usd'
                // ],
                [
                    'name' => 'Surat Jalan',
                    'route' => 'apps/surat_jalan',
                    'bgcolor' => 'orange',
                    'icon' => 'mail-bulk'
                ],

                [
                    'name' => 'Stok Barang',
                    'route' => 'apps/products',
                    'bgcolor' => 'orange',
                    'icon' => 'warehouse'
                ],
                // [
                //     'name' => 'Stock IN',
                //     'route' => 'apps/products/stockUpdate',
                //     'bgcolor' => 'blue',
                //     'icon' => 'warehouse'
                // ],
                // [
                //     'name' => 'Stock OUT',
                //     'route' => 'apps/stock_out',
                //     'bgcolor' => 'red',
                //     'icon' => 'map-marker-alt'
                // ],
                // [
                //     'name' => 'Retur',
                //     'route' => 'apps/retur',
                //     'bgcolor' => 'goldenrod',
                //     'icon' => 'retweet'
                // ]
            ];
            $title = "Gudang";
        } else if ($type == "wilayah1") {
            $menus = [
                [
                    'name' => 'Purchase Request',
                    'route' => 'apps/purchase_request',
                    'bgcolor' => 'sagegreen',
                    'icon' => 'cart-arrow-down'
                ],
                [
                    'name' => 'Tracking Purchase Request',
                    'route' => 'apps/purchase_request',
                    'bgcolor' => 'red',
                    'icon' => 'route'
                ]
            ];
            $title = "Wilayah 1";
        } else if ($type == "wilayah2") {
            $menus = [
                [
                    'name' => 'Purchase Request',
                    'route' => 'apps/purchase_request',
                    'bgcolor' => 'sagegreen',
                    'icon' => 'cart-arrow-down'
                ],
                [
                    'name' => 'Tracking Purchase Request',
                    'route' => 'apps/purchase_request',
                    'bgcolor' => 'red',
                    'icon' => 'route'
                ]
            ];
            $title = "Wilayah 2";
        } else if ($type == 'eng') {
            $menus = [
                [
                    'name' => 'Justifikasi',
                    'route' => 'apps/justifikasi',
                    'bgcolor' => 'sagegreen',
                    'icon' => 'folder-open'
                ],
                [
                    'name' => 'Tracking Purchase Request',
                    'route' => 'apps/purchase_request',
                    'bgcolor' => 'red',
                    'icon' => 'route'
                ]
            ];
            $title = "Engineer";
        }

        return view('home.tipe', compact('menus', 'title'));
    }
}
