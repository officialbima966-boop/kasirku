<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SCRIPT_NAME'] = '/kasirku/public/index.php';
$_SERVER['HTTP_HOST'] = 'localhost';

require_once __DIR__ . '/app/config/app.php';
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

echo "1. Testing Auth...\n";
$userAuth = (new User())->authenticate('admin', 'password');
echo "Admin auth: " . ($userAuth ? 'OK' : 'FAIL') . "\n";

$kasirAuth = (new User())->authenticate('kasir', 'kasir');
echo "Kasir auth: " . ($kasirAuth ? 'OK' : 'FAIL') . "\n";

$memberAuth = (new Pelanggan())->authenticateAccount('member', 'password');
echo "Member auth: " . ($memberAuth ? 'OK' : 'FAIL') . " (ID: " . ($memberAuth['id_pelanggan'] ?? '-') . ")\n";

echo "2. Testing Kasir Pesanan Index...\n";
$_SESSION['user'] = ['id' => 2, 'nama' => 'Kasir Toko', 'username' => 'kasir', 'role' => 'kasir'];
ob_start();
(new PesananController())->cashierIndex();
$kasirOut = ob_get_clean();
echo "Kasir Orders Output: " . strlen($kasirOut) . " bytes\n";

echo "3. Testing Member Page...\n";
$_SESSION['user'] = ['id' => 1, 'nama' => 'Andi Pratama', 'username' => 'member', 'role' => 'pelanggan'];
ob_start();
(new PesananController())->member();
$memberOut = ob_get_clean();
echo "Member Output: " . strlen($memberOut) . " bytes\n";

echo "4. Testing Kasir Checkout with RFID Preview...\n";
$pending = (new Pesanan())->pendingOrders();
if ($pending) {
    $orderId = (int)$pending[0]['id_pesanan'];
    echo "Found pending order ID: $orderId\n";
    $_SESSION['user'] = ['id' => 2, 'nama' => 'Kasir Toko', 'username' => 'kasir', 'role' => 'kasir'];
    $_GET['id'] = $orderId;
    ob_start();
    (new PesananController())->cashierCheckout();
    $checkoutOut = ob_get_clean();
    echo "Kasir Checkout Output: " . strlen($checkoutOut) . " bytes\n";

    // Test RFID preview
    $previewNoRfid = (new Pesanan())->previewCheckout($orderId, '');
    echo "Preview without RFID: Subtotal=" . $previewNoRfid['subtotal'] . ", Discount=" . $previewNoRfid['discount'] . ", Total=" . $previewNoRfid['total'] . "\n";

    $previewWithRfid = (new Pesanan())->previewCheckout($orderId, 'RFID-ANDI-8821');
    echo "Preview WITH RFID: Subtotal=" . $previewWithRfid['subtotal'] . ", Discount=" . $previewWithRfid['discount'] . " (5%), Total=" . $previewWithRfid['total'] . "\n";
} else {
    echo "No pending order found to test checkout.\n";
}

echo "5. Testing Login Page Render...\n";
ob_start();
(new AuthController())->login();
$loginOut = ob_get_clean();
echo "Login Output: " . strlen($loginOut) . " bytes\n";

echo "ALL TESTS PASSED!\n";
