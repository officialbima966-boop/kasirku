<?php
session_start();
require_once __DIR__ . '/../app/config/app.php';
require_once APP_PATH . '/config/database.php';
require_once APP_PATH . '/helpers/format.php';
require_once APP_PATH . '/helpers/auth.php';
require_once APP_PATH . '/helpers/validation.php';
require_once APP_PATH . '/models/BaseModel.php';
require_once APP_PATH . '/models/Pelanggan.php';
require_once APP_PATH . '/models/Pesanan.php';
require_once APP_PATH . '/models/Produk.php';
require_once APP_PATH . '/models/Kategori.php';
require_once APP_PATH . '/models/Transaksi.php';
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/LogAktivitas.php';
require_once APP_PATH . '/controllers/AuthController.php';
require_once APP_PATH . '/controllers/PesananController.php';
require_once APP_PATH . '/controllers/DashboardController.php';
require_once APP_PATH . '/controllers/CrudController.php';
require_once APP_PATH . '/controllers/TransaksiController.php';
require_once APP_PATH . '/controllers/LaporanController.php';
require_once APP_PATH . '/controllers/DatabaseController.php';

$page = $_GET['page'] ?? (is_logged_in() ? home_page_for_role() : 'login');
$publicPages = ['login','daftar'];
if (!in_array($page, $publicPages, true)) require_login();

$routes = [
 'login' => [AuthController::class, 'login'],
 'daftar' => [AuthController::class, 'register'],
 'logout' => [AuthController::class, 'logout'],
 'dashboard' => [DashboardController::class, 'index'],
 'pelanggan' => [CrudController::class, 'pelanggan'],
 'produk' => [CrudController::class, 'produk'],
 'kategori' => [CrudController::class, 'kategori'],
 'transaksi' => [TransaksiController::class, 'index'],
 'transaksi_tambah' => [TransaksiController::class, 'create'],
 'transaksi_simpan' => [TransaksiController::class, 'store'],
 'transaksi_detail' => [TransaksiController::class, 'detail'],
 'member' => [PesananController::class, 'member'],
 'member_pesanan' => [PesananController::class, 'memberOrders'],
 'member_pesan' => [PesananController::class, 'createOrder'],
 'member_kartu' => [PesananController::class, 'linkCard'],
 'kasir_pesanan' => [PesananController::class, 'cashierIndex'],
 'kasir_bayar' => [PesananController::class, 'cashierCheckout'],
 'kasir_preview' => [PesananController::class, 'cashierPreview'],
 'kasir_proses' => [PesananController::class, 'cashierPay'],
 'laporan' => [LaporanController::class, 'index'],
 'database' => [DatabaseController::class, 'index'],
 'log' => [DatabaseController::class, 'logs'],
];
if (isset($routes[$page]) && !in_array($page, $publicPages, true) && $page !== 'logout') {
    $role=current_user()['role']??'';
    $allowedRoles=[
        'dashboard'=>['admin'],
        'pelanggan'=>['admin'],
        'produk'=>['admin'],
        'kategori'=>['admin'],
        'transaksi'=>['admin','kasir'],
        'transaksi_tambah'=>['admin','kasir'],
        'transaksi_simpan'=>['admin','kasir'],
        'transaksi_detail'=>['admin','kasir'],
        'member'=>['pelanggan'],
        'member_pesanan'=>['pelanggan'],
        'member_pesan'=>['pelanggan'],
        'member_kartu'=>['pelanggan'],
        'kasir_pesanan'=>['admin','kasir'],
        'kasir_bayar'=>['admin','kasir'],
        'kasir_preview'=>['admin','kasir'],
        'kasir_proses'=>['admin','kasir'],
        'laporan'=>['admin'],
        'database'=>['admin'],
        'log'=>['admin'],
    ];
    if(isset($allowedRoles[$page]) && !in_array($role,$allowedRoles[$page],true)) {
        redirect(url(home_page_for_role($role)));
    }
}
if (!isset($routes[$page])) { http_response_code(404); $page='404'; }
if ($page === '404') { require APP_PATH.'/views/errors/404.php'; exit; }
[$class,$method] = $routes[$page];
$controller = new $class();
$controller->$method();
