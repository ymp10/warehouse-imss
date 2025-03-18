<?php

use App\Http\Controllers\BpmController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\DetailsjnController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\KontrakController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\PurchaseRequestSppjpController;
use App\Http\Controllers\NotificationController;
use App\Models\Kontrak;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::get('/', [App\Http\Controllers\HomeController::class, 'indexAwal'])->name('homeAwal');
Route::get('/div/{tipe}', [App\Http\Controllers\HomeController::class, 'appType'])->name('appType');
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/search', [App\Http\Controllers\ProductController::class, 'search'])->name('search');

//rute baru
Route::prefix('apps')->group(function () {
    Route::get('purchase_orders', [App\Http\Controllers\PurchaseOrderController::class, 'indexApps'])->name('apps.purchase_orders')->middleware('logistikAuth');
    Route::get('spph', [App\Http\Controllers\SpphController::class, 'indexApps'])->name('apps.spph')->middleware('logistikAuth');
    Route::get('surat_jalan', [App\Http\Controllers\SjnController::class, 'indexApps'])->name('apps.surat_jalan')->middleware('gudangAuth');
    Route::get('purchase_request', [App\Http\Controllers\PurchaseRequestController::class, 'indexApps'])->name('apps.purchase_request')->middleware('wilayahAuth');
    Route::get('surat-keluar', [App\Http\Controllers\SuratKeluarController::class, 'direksi'])->name('apps.surat_keluar.direksi');
    Route::get('kode-aset', [App\Http\Controllers\KodeAsetController::class, 'index'])->name('apps.kode_aset')->middleware('sdmAuth');
    Route::get('products', [App\Http\Controllers\ProductController::class, 'products'])->name('apps.products')->middleware('gudangAuth');
});
Route::get('unauthorized', [App\Http\Controllers\HomeController::class, 'unauthorized'])->name('unauthorized');

Route::get('surat-keluar', [App\Http\Controllers\SuratKeluarController::class, 'index'])->name('surat_keluar.index');
Route::post('surat-keluar', [App\Http\Controllers\SuratKeluarController::class, 'create'])->name('surat_keluar.save');
Route::delete('surat-keluar', [App\Http\Controllers\SuratKeluarController::class, 'delete'])->name('surat_keluar.delete');
Route::post('suratkeluar-imss/hapus-multiple', [App\Http\Controllers\SuratKeluarController::class, 'hapusMultipleSuratKeluar'])->name('hapus-multiple');


Route::get('kode-aset', [App\Http\Controllers\KodeAsetController::class, 'index'])->name('kode_aset.index');
Route::post('kode-aset', [App\Http\Controllers\KodeAsetController::class, 'create'])->name('kode_aset.save');
Route::delete('kode-aset', [App\Http\Controllers\KodeAsetController::class, 'destroy'])->name('kode_aset.delete');
Route::post('kodeaset-warehouse-imss/hapus-multiple', [App\Http\Controllers\KodeAsetController::class, 'hapusMultipleAset'])->name('hapus-multiple');


Route::get('aset', [App\Http\Controllers\AsetController::class, 'index'])->name('aset.index');
Route::post('aset', [App\Http\Controllers\AsetController::class, 'store'])->name('aset.save');
Route::delete('aset', [App\Http\Controllers\AsetController::class, 'destroy'])->name('aset.delete');
Route::post('warehouse-imss/hapus-multiple', [App\Http\Controllers\AsetController::class, 'hapusMultiple'])->name('hapus-multiple');
Route::get('penghapusan-aset', [App\Http\Controllers\PenghapusanAsetController::class, 'index'])->name('penghapusan_aset.index');

Route::prefix('products')->group(function () {
    Route::get('', [App\Http\Controllers\ProductController::class, 'products'])->name('products');
    Route::post('', [App\Http\Controllers\ProductController::class, 'product_save'])->name('products.save');
    Route::delete('', [App\Http\Controllers\ProductController::class, 'product_delete'])->name('products.delete')->middleware('adminRole');
    Route::get('wip', [App\Http\Controllers\ProductController::class, 'products_wip'])->name('products.wip');
    Route::post('wip', [App\Http\Controllers\ProductController::class, 'product_wip_save'])->name('products.wip.save');
    Route::delete('wip', [App\Http\Controllers\ProductController::class, 'product_wip_delete'])->name('products.wip.delete');
    Route::post('wip/complete', [App\Http\Controllers\ProductController::class, 'product_wip_complete'])->name('products.wip.complete');
    Route::get('wipHistory', [App\Http\Controllers\ProductController::class, 'products_wip_history'])->name('products.wip.history');
    Route::get('/check/{pcode}', [App\Http\Controllers\ProductController::class, 'product_check'])->name('products.check');
    Route::post('/stockUpdate', [App\Http\Controllers\ProductController::class, 'product_stock'])->name('products.stock');
    Route::get('/stockHistory', [App\Http\Controllers\ProductController::class, 'product_stock_history'])->name('products.stock.history');
    Route::get('categories', [App\Http\Controllers\ProductController::class, 'categories'])->name('products.categories');
    Route::post('categories', [App\Http\Controllers\ProductController::class, 'categories_save'])->name('products.categories.save')->middleware('adminRole');
    Route::delete('categories', [App\Http\Controllers\ProductController::class, 'categories_delete'])->name('products.categories.delete')->middleware('adminRole');
    Route::get('shelf', [App\Http\Controllers\ProductController::class, 'shelf'])->name('products.shelf');
    Route::post('shelf', [App\Http\Controllers\ProductController::class, 'shelf_save'])->name('products.shelf.save')->middleware('adminRole');
    Route::delete('shelf', [App\Http\Controllers\ProductController::class, 'shelf_delete'])->name('products.shelf.delete')->middleware('adminRole');
    Route::get('barcode/{code}', [App\Http\Controllers\ProductController::class, 'generateBarcode'])->name('products.barcode');
    Route::post('import', [App\Http\Controllers\ProductController::class, 'product_import'])->name('products.import');
    Route::post('wipImport', [App\Http\Controllers\ProductController::class, 'product_wip_import'])->name('products.wip.import');
    Route::get('sjn', [App\Http\Controllers\ProductController::class, 'sjn'])->name('sjn');
    Route::post('sjn', [App\Http\Controllers\SjnController::class, 'store'])->name('products.sjn.store');
    Route::delete('sjn', [App\Http\Controllers\SjnController::class, 'destroy'])->name('sjn.delete');
    Route::get('sjn_print', [App\Http\Controllers\ProductController::class, 'sjn_print'])->name('sjn.print');
    Route::get('po_print', [App\Http\Controllers\ProductController::class, 'po_print'])->name('po.print');
    // Route::get('pr_print', [App\Http\Controllers\PurchaseRequestController::class, 'pr_print'])->name('pr.print');
    Route::get('pr', [App\Http\Controllers\ProductController::class, 'pr'])->name('pr');
    Route::post('pr', [App\Http\Controllers\PurchaseRequestController::class, 'store'])->name('products.pr.store');
    Route::get('pr_print', [App\Http\Controllers\ProductController::class, 'pr_print'])->name('pr.print');
    Route::get('spph_print', [App\Http\Controllers\SpphController::class, 'spphPrint'])->name('spph.print');
    Route::post('upload-file', [App\Http\Controllers\PurchaseRequestController::class, 'uploadFile'])->name('upload_file');


    //keproyekan
    Route::resource('keproyekan', App\Http\Controllers\KeproyekanController::class)->except(['destroy']);
    Route::delete('keproyekan', [App\Http\Controllers\KeproyekanController::class, 'destroy'])->name('keproyekan.destroy');
    Route::delete('/stockHistory', [App\Http\Controllers\ProductController::class, 'product_stock_history_delete'])->name('products.stock.history.delete');
    Route::get('/get-dasar-proyek', [App\Http\Controllers\KontrakController::class, 'getDasarProyek'])->name('get-dasar-proyek');


    //detail sjn
    Route::get('detail_sjn/{id}', [App\Http\Controllers\SjnController::class, 'getDetailSjn'])->name('detail_sjn');
    Route::get('cetak_sjn', [App\Http\Controllers\SjnController::class, 'cetakSjn'])->name('cetak_sjn');
    Route::post('update_detail_sjn', [App\Http\Controllers\SjnController::class, 'updateDetailSjn'])->name('detail_sjn.update');
    Route::post('sjn-imss/hapus-multiple', [App\Http\Controllers\SjnController::class, 'hapusMultipleSjn'])->name('hapus-multiple');

    //vendor
    Route::resource('vendor', App\Http\Controllers\VendorController::class)->except(['destroy']);
    Route::delete('vendor', [App\Http\Controllers\VendorController::class, 'destroy'])->name('vendor.destroy');
    Route::post('vendor-imss/hapus-multiple', [App\Http\Controllers\VendorController::class, 'hapusMultipleVendor'])->name('hapus-multiple');

    //purchase order
    Route::resource('purchase_order', App\Http\Controllers\PurchaseOrderController::class)->except(['destroy']);
    Route::get('cetak_po', [App\Http\Controllers\PurchaseOrderController::class, 'cetakPo'])->name('cetak_po');
    Route::post('detail_po_save', [App\Http\Controllers\PurchaseOrderController::class, 'detailPrSave'])->name('detail_po_save');
    Route::delete('detail_po_delete', [App\Http\Controllers\PurchaseOrderController::class, 'destroyDetailPo'])->name('detail_po_delete'); //baru delete detail po
    Route::post('detail_pr_save', [App\Http\Controllers\PurchaseRequestController::class, 'detailPrSave'])->name('detail_pr_save');
    Route::post('tambah_detail_po', [App\Http\Controllers\PurchaseOrderController::class, 'tambahDetailPo'])->name('tambah_detail_po');
    Route::get('tracking', [App\Http\Controllers\PurchaseOrderController::class, 'tracking'])->name('product.tracking');
    Route::get('trackingwil', [App\Http\Controllers\PurchaseOrderController::class, 'trackingwil'])->name('product.trackingwil');
    Route::get('showPOPL', [App\Http\Controllers\PurchaseOrderController::class, 'showPOPL'])->name('product.showPOPL');
    Route::get('approvedPO', [App\Http\Controllers\PurchaseOrderController::class, 'aprrovedPO'])->name('product.approvedPO');
    Route::get('aprrovedPO_PL', [App\Http\Controllers\PurchaseOrderController::class, 'aprrovedPO_PL'])->name('product.aprrovedPO_PL');
    Route::post('storePOPL', [App\Http\Controllers\PurchaseOrderController::class, 'storePOPL'])->name('product.storePOPL');
    Route::post('po-imss/hapus-multiple', [App\Http\Controllers\PurchaseOrderController::class, 'hapusMultiplePo'])->name('hapus-multiple');
    Route::post('tracking-imss/hapus-multiple', [App\Http\Controllers\PurchaseOrderController::class, 'hapusMultipleTracking'])->name('hapus-multiple');
    Route::get('/nopr/getByIds', [App\Http\Controllers\PurchaseOrderController::class, 'getByIds'])->name('nopr.getByIds');
    Route::post('qty_po_save', [App\Http\Controllers\PurchaseOrderController::class, 'QtyPoSave'])->name('qty_po_save');

    Route::get('test_pr', [App\Http\Controllers\PurchaseOrderController::class, 'test_pr'])->name('test_pr');

    //justifikasi
    Route::get('justifikasi', [App\Http\Controllers\JustifikasiController::class, 'index'])->name('product.justifikasi');
    Route::post('justifikasi', [App\Http\Controllers\JustifikasiController::class, 'store'])->name('product.justifikasi.save');
    Route::delete('justifikasi', [App\Http\Controllers\JustifikasiController::class, 'destroy'])->name('product.justifikasi.delete');

    //drawing schematic
    Route::get('drawing-schematic', [App\Http\Controllers\DrawingSchematicController::class, 'index'])->name('product.drawing.schematic');
    Route::post('drawing-schematic', [App\Http\Controllers\DrawingSchematicController::class, 'store'])->name('product.drawing.schematic.save');
    Route::delete('drawing-schematic', [App\Http\Controllers\DrawingSchematicController::class, 'destroy'])->name('product.drawing.schematic.delete');

    //purchase request
    Route::resource('purchase_request', App\Http\Controllers\PurchaseRequestController::class)->except(['destroy']);
    Route::get('cetak_pr', [App\Http\Controllers\PurchaseRequestController::class, 'cetakPr'])->name('cetak_pr');
    Route::get('cetak_sppjp', [App\Http\Controllers\PurchaseRequestController::class, 'cetakSppjp'])->name('cetak_sppjp');
    Route::delete('purchase_request', [App\Http\Controllers\PurchaseRequestController::class, 'destroy'])->name('purchase_request.destroy');
    Route::post('detail_purchase_request/{id}/delete', [PurchaseRequestController::class, 'hapusDetail'])->name('detail_purchase_request.delete');
    Route::post('pr-imss/hapus-multiple', [App\Http\Controllers\PurchaseRequestController::class, 'hapusMultiplePr'])->name('hapus-multiple');
    Route::get('purchase_request_detail/{id}', [App\Http\Controllers\PurchaseRequestController::class, 'getDetailPr'])->name('purchase_request_detail');
    Route::get('penerimaan_barang_detail/{id}', [App\Http\Controllers\PurchaseRequestController::class, 'getDetailBarang'])->name('penerimaan_barang_detail');
    Route::post('update_purchase_request_detail', [App\Http\Controllers\PurchaseRequestController::class, 'updateDetailPr'])->name('purchase_request_detail.update');
    Route::post('purchase_request/update_detail', [App\Http\Controllers\PurchaseRequestController::class, 'editDetail'])->name('detail.update'); //nambah baru
    Route::delete('pr-imss/delete_detail', [App\Http\Controllers\PurchaseRequestController::class, 'deleteDetail'])->name('purchase_request.delete_detail');
    Route::post('lppb/editlppb', [App\Http\Controllers\PurchaseRequestController::class, 'editlppb'])->name('lppb.update'); //nambah baru
    Route::post('lppb/editpenerimaan', [App\Http\Controllers\PurchaseRequestController::class, 'editpenerimaan'])->name('lppb.update'); //nambah baru
    Route::get('/purchase-requests', [App\Http\Controllers\PurchaseRequestController::class, 'indexPr'])->middleware('auth');
    Route::get('products/purchase_request_detail/completed/{id}', [App\Http\Controllers\PurchaseRequestController::class, 'getCompletedDetailPr'])->name('get.completed.pr');
    Route::get('lppb_detail/{id}', [App\Http\Controllers\PurchaseRequestController::class, 'getDetailLppb'])->name('lppb_detail');
    Route::get('/cetak-dokumen', [App\Http\Controllers\PurchaseRequestController::class, 'cetakDokumen'])->name('cetak_dokumen');


    

    

    //bpm
    Route::resource('bpm', App\Http\Controllers\BpmController::class)->except(['destroy']);
    Route::delete('bpm', [App\Http\Controllers\BpmController::class, 'destroy'])->name('bpm.destroy');
    Route::get('cetak_bpm', [App\Http\Controllers\BpmController::class, 'cetakBpm'])->name('cetak_bpm');
    Route::get('bpm', [App\Http\Controllers\BpmController::class, 'index'])->name('bpm.index');
    Route::post('bpm', [App\Http\Controllers\BpmController::class, 'store'])->name('products.bpm.store');
    Route::post('update_bpm_detail', [App\Http\Controllers\BpmController::class, 'updateDetailBpm'])->name('bpm_detail.update');
    Route::get('bpm_detail/{id}', [App\Http\Controllers\BpmController::class, 'getDetailBpm'])->name('bpm_detail');
    Route::post('detail_bpm_save', [App\Http\Controllers\BpmController::class, 'detailBpmSave'])->name('detail_bpm_save');
    Route::post('bpm/update_detail', [App\Http\Controllers\BpmController::class, 'editDetail'])->name('detail.update'); //nambah baru
    Route::post('bpm-imss/hapus-multiple', [App\Http\Controllers\BpmController::class, 'hapusMultipleBpm'])->name('hapus-multiple');

    Route::post('detail_bpm/{id}/delete', [BpmController::class, 'hapusDetail'])->name('detail_purchase_request.delete');


    //Surat Jalan

    Route::resource('surat_jalan', App\Http\Controllers\SuratJalanController::class)->except(['destroy']);
    Route::delete('surat_jalan', [App\Http\Controllers\SuratJalanController::class, 'destroy'])->name('surat_jalan.destroy');
    Route::get('cetak_sjn', [App\Http\Controllers\SuratJalanController::class, 'cetakSjn'])->name('cetak_sjn');
    Route::get('surat_jalan', [App\Http\Controllers\SuratJalanController::class, 'index'])->name('surat_jalan.index');
    Route::post('surat_jalan', [App\Http\Controllers\SuratJalanController::class, 'store'])->name('products.surat_jalan.store');
    Route::post('update_surat_jalan_detail', [App\Http\Controllers\SuratJalanController::class, 'updateDetailSjn'])->name('surat_jalan_detail.update');
    Route::get('surat_jalan_detail/{id}', [App\Http\Controllers\SuratJalanController::class, 'getDetailSjn'])->name('surat_jalan_detail');
    Route::post('detail_surat_jalan_save', [App\Http\Controllers\SuratJalanController::class, 'detailSjnSave'])->name('detail_surat_jalan_save');
    Route::post('surat_jalan/update_detail', [App\Http\Controllers\SuratJalanController::class, 'editDetail'])->name('detail.update'); //nambah baru
    Route::post('surat_jalan-imss/hapus-multiple', [App\Http\Controllers\SuratJalanController::class, 'hapusMultipleSjn'])->name('hapus-multiple');

    Route::post('detail_surat_jalan/{id}/delete', [SuratJalanController::class, 'hapusDetail'])->name('detail_surat_jalan.delete');


    //history
    Route::get('/history', [HistoryController::class, 'index']);
    Route::delete('/history', [HistoryController::class, 'deleteAll'])->name('history.delete');
    Route::delete('purchase_order', [App\Http\Controllers\PurchaseOrderController::class, 'destroy'])->name('purchase_order.destroy');
    Route::get('purchase_order_detail/{id}', [App\Http\Controllers\PurchaseOrderController::class, 'getDetailPo'])->name('purchase_order_detail');
    Route::post('update_purchase_order_detail', [App\Http\Controllers\PurchaseOrderController::class, 'updateDetailPo'])->name('purchase_order_detail.update');

    //PO_PL
    Route::get("purchase_order_pl", [App\Http\Controllers\PurchaseOrderController::class, 'getDetailPoPL'])->name('purchase_order_pl');
    // Route::post("purchase_order_pl", [App\Http\Controllers\PurchaseOrderController::class, 'storeDetailPoPL'])->name('purchase_order_pl.store');
    Route::delete('delete_po_pl', [App\Http\Controllers\PurchaseOrderController::class, 'destroyPoPL'])->name('purchase_order_pl.destroy');
    Route::post('po-pl-imss/hapus-multiple', [App\Http\Controllers\PurchaseOrderController::class, 'hapusMultiplePo_Pl'])->name('hapus-multiple');

    //kode material
    Route::resource('kode_material', App\Http\Controllers\KodeMaterialController::class)->except(['destroy']);
    Route::delete('kode_material', [App\Http\Controllers\KodeMaterialController::class, 'destroy'])->name('kode_material.destroy');

    //SPPH
    Route::resource('spph', App\Http\Controllers\SpphController::class)->except(['destroy']);
    Route::delete('spph', [App\Http\Controllers\SpphController::class, 'destroy'])->name('spph.destroy');
    Route::get('spph_detail/{id}', [App\Http\Controllers\SpphController::class, 'getDetailSpph'])->name('spph_detail');
    Route::post('update_spph_detail', [App\Http\Controllers\SpphController::class, 'updateDetailSpph'])->name('spph_detail.update');
    Route::get('products_pr/{id_pr}', [App\Http\Controllers\SpphController::class, 'getProductPR'])->name('products_pr');
    // Route::get('products_pr', [App\Http\Controllers\SpphController::class, 'getProductPR'])->name('products_pr');
    Route::post('tambah_spph_detail', [App\Http\Controllers\SpphController::class, 'tambahSpphDetail'])->name('tambah_spph_detail');
    Route::post('spph-imss/hapus-multiple', [App\Http\Controllers\SpphController::class, 'hapusMultipleSpph'])->name('hapus-multiple');
    Route::get('selectnopr', [App\Http\Controllers\SpphController::class, 'nopr'])->name('nopr.index');
    Route::delete('/detail-spph-delete', [App\Http\Controllers\SpphController::class, 'destroyDetailSpph'])->name('detail_spph_delete');
    Route::post('detail_spph_save', [App\Http\Controllers\SpphController::class, 'detailSpphSave'])->name('detail_spph_save');
    Route::get('/get-spph-keterangan/{spph_id}', [App\Http\Controllers\SpphController::class, 'getSpphKeterangan']);

    //kang
    // //SPPH
    // Route::resource('spph', App\Http\Controllers\SpphController::class)->except(['destroy']);
    // Route::delete('spph', [App\Http\Controllers\SpphController::class, 'destroy'])->name('spph.destroy');
    // Route::get('spph_detail/{id}', [App\Http\Controllers\SpphController::class, 'getDetailSpph'])->name('spph_detail');
    // Route::post('update_spph_detail', [App\Http\Controllers\SpphController::class, 'updateDetailSpph'])->name('spph_detail.update');
    // Route::get('products_pr_spph/{id_pr}', [App\Http\Controllers\SpphController::class, 'getProductPR'])->name('products_pr_spph');
    // // Route::get('products_pr', [App\Http\Controllers\SpphController::class, 'getProductPR'])->name('products_pr');
    // Route::post('tambah_spph_detail', [App\Http\Controllers\SpphController::class, 'tambahSpphDetail'])->name('tambah_spph_detail');
    // Route::post('spph-imss/hapus-multiple', [App\Http\Controllers\SpphController::class, 'hapusMultipleSpph'])->name('hapus-multiple');
    // Route::get('selectnopr', [App\Http\Controllers\SpphController::class, 'nopr'])->name('nopr.index');
    // Route::delete('/detail-spph-delete', [App\Http\Controllers\SpphController::class, 'destroyDetailSpph'])->name('detail_spph_delete');
    // Route::post('detail_spph_save', [App\Http\Controllers\SpphController::class, 'detailSpphSave'])->name('detail_spph_save');






    //Notif
    Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::post('notifications/read/{id}', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/notification/open/{id}', [App\Http\Controllers\NotificationController::class, 'openNotification'])->name('notification.open');
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('notifications.index');
    // Route::get('/notifications', 'NotificationController@getNotifications')->name('notifications.index');

    //NEGOSIASI
    //resource digunakan untuk memanggil semuanya yg ada di controller kecuali destroy. contoh : nego.store
    Route::resource('nego', App\Http\Controllers\NegoController::class)->except(['destroy']);
    //End Resource
    Route::delete('nego', [App\Http\Controllers\NegoController::class, 'destroy'])->name('nego.destroy');
    Route::get('nego', [App\Http\Controllers\NegoController::class, 'index'])->name('nego.index');
    Route::get('nego_detail/{id}', [App\Http\Controllers\NegoController::class, 'getDetailNego'])->name('nego_detail');
    Route::post('update_nego_detail', [App\Http\Controllers\NegoController::class, 'updateDetailNego'])->name('nego_detail.update');
    Route::get('products_pr_nego/{id_pr}', [App\Http\Controllers\NegoController::class, 'getProductPR'])->name('products_pr_nego');
    Route::post('tambah_nego_detail', [App\Http\Controllers\NegoController::class, 'tambahNegoDetail'])->name('tambah_nego_detail');
    Route::post('nego-imss/hapus-multiple', [App\Http\Controllers\NegoController::class, 'hapusMultipleNego'])->name('hapus-multiple');
    Route::get('selectnopr', [App\Http\Controllers\NegoController::class, 'nopr'])->name('nopr.index');
    Route::get('nego_print', [App\Http\Controllers\NegoController::class, 'negoPrint'])->name('nego.print');
    Route::delete('/detail-nego-delete', [App\Http\Controllers\NegoController::class, 'destroyDetailNego'])->name('detail_nego_delete');
    Route::post('detail_nego_save', [App\Http\Controllers\NegoController::class, 'detailNegoSave'])->name('detail_nego_save');
    Route::post('qty_nego_save', [App\Http\Controllers\NegoController::class, 'QtyNegoSave'])->name('qty_nego_save');


    //LOI
    //resource digunakan untuk memanggil semuanya yg ada di controller kecuali destroy. contoh : nego.store
    Route::resource('loi', App\Http\Controllers\LoiController::class)->except(['destroy']);
    //End Resource
    Route::delete('loi', [App\Http\Controllers\LoiController::class, 'destroy'])->name('loi.destroy');
    Route::get('loi', [App\Http\Controllers\LoiController::class, 'index'])->name('loi.index');
    Route::get('loi_detail/{id}', [App\Http\Controllers\LoiController::class, 'getDetailLoi'])->name('loi_detail');
    Route::post('update_loi_detail', [App\Http\Controllers\LoiController::class, 'updateDetailLoi'])->name('loi_detail.update');
    Route::get('products_pr_loi/{id_pr}', [App\Http\Controllers\LoiController::class, 'getProductPR'])->name('products_pr_loi');
    Route::post('tambah_loi_detail', [App\Http\Controllers\LoiController::class, 'tambahLoiDetail'])->name('tambah_loi_detail');
    Route::post('loi-imss/hapus-multiple', [App\Http\Controllers\LoiController::class, 'hapusMultipleLoi'])->name('hapus-multiple');
    Route::get('selectnopr', [App\Http\Controllers\LoiController::class, 'nopr'])->name('nopr.index');
    Route::get('loi_print', [App\Http\Controllers\LoiController::class, 'loiPrint'])->name('loi.print');
    Route::delete('/detail-loi-delete', [App\Http\Controllers\LoiController::class, 'destroyDetailLoi'])->name('detail_loi_delete');
    Route::post('detail_loi_save', [App\Http\Controllers\LoiController::class, 'detailLoiSave'])->name('detail_loi_save');
    Route::post('qty_loi_save', [App\Http\Controllers\LoiController::class, 'QtyLoiSave'])->name('qty_loi_save');



    //KONTRAK
    //resource digunakan untuk memanggil semuanya yg ada di controller kecuali destroy. contoh : nego.store
    Route::resource('kontrak', App\Http\Controllers\KontrakController::class)->except(['destroy']);
    Route::delete('kontrak', [App\Http\Controllers\KontrakController::class, 'destroy'])->name('kontrak.destroy');
    // Route::get('cetak_bpm', [App\Http\Controllers\BpmController::class, 'cetakBpm'])->name('cetak_bpm');
    Route::get('kontrak', [App\Http\Controllers\KontrakController::class, 'index'])->name('kontrak.index');
    Route::post('kontrak', [App\Http\Controllers\KontrakController::class, 'store'])->name('products.kontrak.store');
    Route::post('update_kontrak_detail', [App\Http\Controllers\KontrakController::class, 'updateDetailKontrak'])->name('kontrak_detail.update');
    Route::get('kontrak_detail/{id}', [App\Http\Controllers\KontrakController::class, 'getDetailKontrak'])->name('kontrak_detail');
    Route::post('detail_kontrak_save', [App\Http\Controllers\KontrakController::class, 'detailKontrakSave'])->name('detail_kontrak_save');
    Route::post('kontrak/update_detail', [App\Http\Controllers\KontrakController::class, 'editDetail'])->name('detail.update'); //nambah baru
    Route::post('kontrak-imss/hapus-multiple', [App\Http\Controllers\KontrakController::class, 'hapusMultipleKontrak'])->name('hapus-multiple');
    Route::post('upload-file', [App\Http\Controllers\KontrakController::class, 'uploadFile'])->name('upload_file');
    Route::post('detail_kontrak/{id}/delete', [KontrakController::class, 'hapusDetail'])->name('detail_kontrak.delete');


    //BA JUSTIFIKASI
    //resource digunakan untuk memanggil semuanya yg ada di controller kecuali destroy. contoh : nego.store
    // Route::resource('justi', App\Http\Controllers\JustiController::class)->except(['destroy']);
    // //End Resource
    // Route::delete('nego', [App\Http\Controllers\NegoController::class, 'destroy'])->name('nego.destroy');
    // Route::get('justi', [App\Http\Controllers\JustiController::class, 'index'])->name('justi.index');
    // Route::get('nego_detail/{id}', [App\Http\Controllers\NegoController::class, 'getDetailNego'])->name('nego_detail');
    // Route::post('update_nego_detail', [App\Http\Controllers\NegoController::class, 'updateDetailNego'])->name('nego_detail.update');
    // Route::get('products_pr/{id_pr}', [App\Http\Controllers\NegoController::class, 'getProductPR'])->name('products_pr');
    // Route::post('tambah_nego_detail', [App\Http\Controllers\NegoController::class, 'tambahNegoDetail'])->name('tambah_nego_detail');
    // Route::post('nego-imss/hapus-multiple', [App\Http\Controllers\NegoController::class, 'hapusMultipleNego'])->name('hapus-multiple');
    // Route::get('selectnopr', [App\Http\Controllers\JustiController::class, 'nopr'])->name('nopr.index');
    // Route::get('nego_print', [App\Http\Controllers\NegoController::class, 'negoPrint'])->name('nego.print');
    // Route::delete('/detail-nego-delete', [App\Http\Controllers\NegoController::class, 'destroyDetailNego'])->name('detail_nego_delete');
    // Route::post('detail_nego_save', [App\Http\Controllers\NegoController::class, 'detailNegoSave'])->name('detail_nego_save');





    //logistik
    Route::get('logistik', [App\Http\Controllers\LogistikController::class, 'index'])->name('products.logistik');

    // engineering edit pr
    Route::get('showEditPr', [App\Http\Controllers\PurchaseRequestController::class, 'showEditPr'])->name('eng.purchase_request');
    Route::post('edit_purchase_request', [App\Http\Controllers\PurchaseRequestController::class, 'editPrEng'])->name('edit_purchase_request');
});

Route::prefix('users')->group(function () {
    Route::get('', [App\Http\Controllers\UserController::class, 'users'])->name('users')->middleware('adminRole');
    Route::delete('', [App\Http\Controllers\UserController::class, 'user_delete'])->name('users.delete')->middleware('adminRole');
    Route::post('', [App\Http\Controllers\UserController::class, 'user_save'])->name('users.save')->middleware('adminRole');
});

Route::prefix('warehouse')->group(function () {
    Route::get('', [App\Http\Controllers\ProductController::class, 'warehouse'])->name('warehouse')->middleware('adminRole');
    Route::delete('', [App\Http\Controllers\ProductController::class, 'warehouse_delete'])->name('warehouse.delete')->middleware('adminRole');
    Route::post('', [App\Http\Controllers\ProductController::class, 'warehouse_save'])->name('warehouse.save')->middleware('adminRole');
    Route::get('change/{warehouse_id}', [App\Http\Controllers\ProductController::class, 'warehouse_select'])->name('warehouse.select');
});

Route::prefix('account')->group(function () {
    Route::get('', [App\Http\Controllers\UserController::class, 'myaccount'])->name('myaccount');
    Route::post('profile', [App\Http\Controllers\UserController::class, 'myaccount_update'])->name('myaccount.update');
    Route::post('password', [App\Http\Controllers\UserController::class, 'myaccount_update_password'])->name('myaccount.updatePassword');
});


Route::get('test_sheet', [App\Http\Controllers\SheetController::class, 'getDataSheet'])->name('test_sheet');
Route::get('get_sheets', [App\Http\Controllers\SheetController::class, 'getDataSheet'])->name('get_sheets');
Route::get('sync_sheets', [App\Http\Controllers\SheetController::class, 'sync'])->name('sync_sheets');
Route::get('test_komat', [App\Http\Controllers\SheetController::class, 'test_komat'])->name('test_komat');
Route::get('materials', [App\Http\Controllers\KodeMaterialController::class, 'apiKodeMaterial'])->name('komat');

Route::get('pdf_lain', [App\Http\Controllers\PdfController::class, 'pdf_lain'])->name('pdf_lain');

Route::get('testt', function () {
    echo Hash::make('admin123');
});

Route::get('voucher', function () {
    return view('keuangan.voucher');
})->name('voucher');
Route::get('ppk', function () {
    return view('keuangan.ppk');
});

Route::get('penerimaan-barang', [App\Http\Controllers\PurchaseRequestController::class, 'penerimaan_barang'])->name('penerimaan_barang');
Route::get('penerimaan_barang/{id}', [App\Http\Controllers\PurchaseRequestController::class, 'getDetailPenerimaanBarang'])->name('getDetailPenerimaanBarang');
Route::post('registrasi-barang', [App\Http\Controllers\PurchaseRequestController::class, 'registrasi_barang'])->name('registrasi_barang.save');
Route::put('edit-registrasi-barang', [App\Http\Controllers\PurchaseRequestController::class, 'edit_registrasi_barang'])->name('registrasi_barang.edit');

Route::get('lppb', [App\Http\Controllers\PurchaseRequestController::class, 'lppb'])->name('lppb');
Route::post('save-lppb', [App\Http\Controllers\PurchaseRequestController::class, 'tambah_lppb'])->name('lppb.save');
Route::put('edit-nomor-lppb', [App\Http\Controllers\PurchaseRequestController::class, 'edit_nomor_lppb'])->name('nomor_lppb.edit');
Route::get('cetak_lppb', [App\Http\Controllers\PurchaseRequestController::class, 'cetakLPPB'])->name('cetak_lppb');
Route::get('lppb_print', function () {
    return view('lppb.print');
})->name('lppb.print');


//Riwayat Pembelian
Route::get('riwayat-pembelian', [App\Http\Controllers\RiwayatpembelianController::class, 'riwayat_pembelian'])->name('riwayat_pembelian');
Route::get('riwayat_barang/{kode_material}', [App\Http\Controllers\RiwayatpembelianController::class, 'getDetailRiwayatPembelian'])->name('getDetailRiwayatPembelian');



Route::post('karyawan_import', [App\Http\Controllers\KaryawanController::class, 'import'])->name('karyawan.import');
Route::get('karyawan_export', [App\Http\Controllers\KaryawanController::class, 'export'])->name('karyawan.export');
Route::get('karyawan', [App\Http\Controllers\KaryawanController::class, 'index'])->name('karyawan.index');
Route::post('karyawan', [App\Http\Controllers\KaryawanController::class, 'store'])->name('karyawan.store');
Route::delete('karyawan', [App\Http\Controllers\KaryawanController::class, 'destroy'])->name('karyawan.destroy');
Route::post('karyawan-warehouse-imss/hapus-multiple', [App\Http\Controllers\KaryawanController::class, 'hapusMultipleKaryawan'])->name('hapus-multiple');

Route::get('proyek', [App\Http\Controllers\ProyekController::class, 'index'])->name('proyek.index');
Route::post('proyek', [App\Http\Controllers\ProyekController::class, 'store'])->name('proyek.store');
Route::delete('proyek', [App\Http\Controllers\ProyekController::class, 'destroy'])->name('proyek.destroy');
Route::put('proyek', [App\Http\Controllers\ProyekController::class, 'update'])->name('proyek.update');

Route::get('service', [App\Http\Controllers\ServiceController::class, 'index'])->name('service.index');
Route::post('service', [App\Http\Controllers\ServiceController::class, 'store'])->name('service.store');
Route::delete('service', [App\Http\Controllers\ServiceController::class, 'destroy'])->name('service.destroy');
Route::delete('detail_sr_delete', [App\Http\Controllers\ServiceController::class, 'deleteService'])->name('service.delete');
Route::put('service', [App\Http\Controllers\ServiceController::class, 'update'])->name('service.update');
Route::get('service/service_detail/{id}', [App\Http\Controllers\ServiceController::class, 'getDetailSr'])->name('service_detail');
Route::post('service/update_service_detail', [App\Http\Controllers\ServiceController::class, 'updateDetailSr'])->name('service_detail.update');
Route::post('service/update_detail', [App\Http\Controllers\ServiceController::class, 'editDetail'])->name('detail.update');
Route::get('cetak_sr', [App\Http\Controllers\ServiceController::class, 'cetakSr'])->name('cetak_sr');

Route::get('jadwal', [App\Http\Controllers\JadwalController::class, 'index'])->name('jadwal.index');
Route::post('jadwal', [App\Http\Controllers\JadwalController::class, 'store'])->name('jadwal.store');
Route::delete('jadwal', [App\Http\Controllers\JadwalController::class, 'destroy'])->name('jadwal.destroy');
Route::put('jadwal', [App\Http\Controllers\JadwalController::class, 'update'])->name('jadwal.update');

Route::get('gangguan', [App\Http\Controllers\GangguanController::class, 'index'])->name('gangguan.index');
Route::post('gangguan', [App\Http\Controllers\GangguanController::class, 'store'])->name('gangguan.store');
Route::delete('gangguan', [App\Http\Controllers\GangguanController::class, 'destroy'])->name('gangguan.destroy');
Route::put('gangguan', [App\Http\Controllers\GangguanController::class, 'update'])->name('gangguan.update');
Route::get('gangguan_detail/{id}', [App\Http\Controllers\GangguanController::class, 'getDetailGangguan'])->name('gangguan_detail');


Route::get('trainset', [App\Http\Controllers\TrainsetController::class, 'index'])->name('trainset.index');
Route::post('trainset', [App\Http\Controllers\TrainsetController::class, 'store'])->name('trainset.store');
Route::delete('trainset', [App\Http\Controllers\TrainsetController::class, 'destroy'])->name('trainset.destroy');
Route::put('trainset', [App\Http\Controllers\TrainsetController::class, 'update'])->name('trainset.update');

Route::get('bom', [App\Http\Controllers\BomController::class, 'index'])->name('bom.index');
Route::post('bom', [App\Http\Controllers\BomController::class, 'store'])->name('bom.store');
Route::get('bom_detail/{id}', [App\Http\Controllers\BomController::class, 'getDetailBom'])->name('bom_detail');
Route::post('update_bom_detail', [App\Http\Controllers\BomController::class, 'updateDetailBom'])->name('bom_detail.update');


Route::post('aset_import', [App\Http\Controllers\AsetController::class, 'import'])->name('aset.import');

Route::get('master_gaji', [App\Http\Controllers\MasterGajiPokokController::class, 'index'])->name('master.gaji.index');





//Route Keuangan Kasbon
Route::get('kasbon', [App\Http\Controllers\KasbonController::class, 'index'])->name('kasbon.kasbon');
Route::post('/keuangan_kasbon/store', [App\Http\Controllers\KasbonController::class, 'store'])->name('keuangan_kasbon.store');
Route::delete('/keuangan-kasbon/{id}', [App\Http\Controllers\KasbonController::class, 'destroy'])->name('keuangan_kasbon.destroy');
Route::get('keuangan_kasbon/{id}/edit', [App\Http\Controllers\KasbonController::class, 'edit'])->name('keuangan_kasbon.edit');
Route::post('keuangan_kasbon/{id}/update', [App\Http\Controllers\KasbonController::class, 'update'])->name('keuangan_kasbon.update');
Route::get('/kasbon/filter', [App\Http\Controllers\KasbonController::class, 'filterKasbon'])->name('kasbon.filter');
Route::get('kasbon/export', [App\Http\Controllers\KasbonController::class, 'export'])->name('kasbon.export');
Route::post('kasbon/import', [App\Http\Controllers\KasbonController::class, 'import'])->name('kasbon.import');
Route::post('kasbon/hapus-multiple', [App\Http\Controllers\KasbonController::class, 'hapusMultiple'])->name('hapus.multiple');
Route::get('kasbon/total', [App\Http\Controllers\KasbonController::class, 'totalKasbon'])->name('kasbon.total');
Route::get('kasbon_print', [App\Http\Controllers\KasbonController::class, 'kasbonPrint'])->name('kasbon.print');

//Route Print_Kasbon
Route::post('memo_kasbon', [App\Http\Controllers\KasbonController::class, 'memoKasbon'])->name('cetak.memo');
Route::get('memo_show', [App\Http\Controllers\KasbonController::class, 'showMemo'])->name('memo.show');
