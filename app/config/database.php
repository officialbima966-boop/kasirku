<?php
class Database {
    private static ?PDO $connection = null;
    public static function connect(): PDO {
        if (self::$connection === null) {
            $host = 'localhost'; $db = 'db_toko'; $user = 'root'; $pass = '';
            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];

            try {
                self::$connection = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                if ((string) $e->getCode() === '1049' || str_contains($e->getMessage(), 'Unknown database')) {
                    throw new RuntimeException(
                        "Database '$db' belum tersedia. Buat atau import database/install.sql melalui phpMyAdmin, lalu muat ulang aplikasi.",
                        0,
                        $e
                    );
                }
                if ((string) $e->getCode() === '1045' || str_contains($e->getMessage(), 'Access denied')) {
                    throw new RuntimeException(
                        "Akses MySQL ditolak. Periksa host, username, dan password pada app/config/database.php.",
                        0,
                        $e
                    );
                }
                throw $e;
            }
        }
        return self::$connection;
    }
}
