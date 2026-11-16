<?php
session_start();
require_once __DIR__ . '/../app/config/app.php';
require_once APP_PATH . '/config/database.php';
require_once APP_PATH . '/helpers/format.php';
require_once APP_PATH . '/helpers/auth.php';
require_once APP_PATH . '/helpers/validation.php';
require_once APP_PATH . '/models/BaseModel.php';
require_once APP_PATH . '/models/Pelanggan.php';
require_once APP_PATH . '/models/Produk.php';
require_once APP_PATH . '/models/Kategori.php';
require_once APP_PATH . '/models/Transaksi.php';
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/LogAktivitas.php';
require_once APP_PATH . '/controllers/AuthController.php';
require_once APP_PATH . '/controllers/DashboardController.php';
require_once APP_PATH . '/controllers/CrudController.php';
require_once APP_PATH . '/controllers/TransaksiController.php';
require_once APP_PATH . '/controllers/LaporanController.php';
require_once APP_PATH . '/controllers/DatabaseController.php';

$page = $_GET['page'] ?? (is_logged_in() ? 'dashboard' : 'login');
$publicPages = ['login'];
if (!in_array($page, $publicPages, true)) require_login();

$routes = [
 'login' => [AuthController::class, 'login'],
 'logout' => [AuthController::class, 'logout'],
 'dashboard' => [DashboardController::class, 'index'],
 'pelanggan' => [CrudController::class, 'pelanggan'],
 'produk' => [CrudController::class, 'produk'],
 'kategori' => [CrudController::class, 'kategori'],
 'transaksi' => [TransaksiController::class, 'index'],
 'transaksi_tambah' => [TransaksiController::class, 'create'],
 'transaksi_simpan' => [TransaksiController::class, 'store'],
 'transaksi_detail' => [TransaksiController::class, 'detail'],
 'laporan' => [LaporanController::class, 'index'],
 'database' => [DatabaseController::class, 'index'],
 'log' => [DatabaseController::class, 'logs'],
];
if (!isset($routes[$page])) { http_response_code(404); $page='404'; }
if ($page === '404') { require APP_PATH.'/views/errors/404.php'; exit; }
[$class,$method] = $routes[$page];
$controller = new $class();
$controller->$method();
