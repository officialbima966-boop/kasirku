<?php
define('APP_PATH', dirname(__DIR__));

if (!defined('BASE_URL')) {
    $scriptDir = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])) : '';
    $scriptDir = rtrim($scriptDir, '/');
    if (empty($scriptDir) || $scriptDir === '/' || $scriptDir === '.') {
        $baseUrl = '/kasirku/public';
    } else {
        $baseUrl = $scriptDir;
    }
    define('BASE_URL', $baseUrl);
}

define('APP_NAME', 'KasirKu');
date_default_timezone_set('Asia/Jakarta');
