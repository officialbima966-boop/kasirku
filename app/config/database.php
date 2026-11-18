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
                    try {
                        $initPdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, $options);
                        $initPdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        self::$connection = new PDO($dsn, $user, $pass, $options);
                        $sqlFile = dirname(__DIR__, 2) . '/database/db_toko.sql';
                        if (file_exists($sqlFile)) {
                            $sql = file_get_contents($sqlFile);
                            self::$connection->exec($sql);
                        }
                        return self::$connection;
                    } catch (Throwable) {
                        throw new RuntimeException(
                            "Database '$db' belum tersedia. Import database/db_toko.sql melalui phpMyAdmin, lalu muat ulang aplikasi.",
                            0,
                            $e
                        );
                    }
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
