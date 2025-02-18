<?php
namespace Config;

use PDO;
use PDOException;

class Database
{
    private static $conn;
    private static $defaultConfig = [];
    private static $customConfig = []; // Untuk menyimpan konfigurasi database khusus

    public static function init()
    {
        // Memuat file .env
        self::loadEnv();

        // Inisialisasi default config dari environment
        self::$defaultConfig = [
            'DB_CONNECTION' => $_ENV['DB_CONNECTION'] ?? 'mysql', // default ke MySQL
            'DB_HOST' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'DB_PORT' => $_ENV['DB_PORT'] ?? '3306', // default ke port MySQL
            'DB_DATABASE' => $_ENV['DB_DATABASE'] ?? 'defaultdb',
            'DB_USERNAME' => $_ENV['DB_USERNAME'] ?? 'root',
            'DB_PASSWORD' => $_ENV['DB_PASSWORD'] ?? '',
        ];

        // Memuat database custom jika ada (misalnya database dinamis)
        self::$customConfig = [
            'custom_db' => [
                'DB_CONNECTION' => $_ENV['DB_CONNECTION'] ?? 'mysql',
                'DB_HOST' => $_ENV['DB_HOST'] ?? '127.0.0.1',
                'DB_PORT' => $_ENV['DB_PORT'] ?? '3306',
                'DB_DATABASE' => $_ENV['DB_CUSTOM_DINAMIS'] ?? 'custom_db', // Set sesuai yang dibutuhkan
                'DB_USERNAME' => $_ENV['DB_USERNAME'] ?? 'root',
                'DB_PASSWORD' => $_ENV['DB_PASSWORD'] ?? '',
            ],
        ];
    }

    public static function getConnection()
    {
        if (self::$conn === null) {
            self::connect(self::$defaultConfig);
        }
        return self::$conn;
    }

    private static function connect($config)
    {
        try {
            $dsn = self::getDsn($config);
            self::$conn = new PDO($dsn, $config['DB_USERNAME'] ?? null, $config['DB_PASSWORD'] ?? null);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Penanganan encoding atau konfigurasi khusus per database
            $connection = strtolower($config['DB_CONNECTION']); // Pastikan case-insensitive
            switch ($connection) {
                case 'pgsql':
                    // PostgreSQL: Atur encoding ke UTF-8
                    self::$conn->exec("SET client_encoding TO 'UTF8'");
                    break;

                case 'mysql':
                    // MySQL: Gunakan utf8mb4 untuk mendukung emoji dan karakter multi-byte
                    self::$conn->exec('SET NAMES utf8mb4');
                    break;

                case 'sqlite':
                    // SQLite tidak memerlukan pengaturan khusus pada encoding
                    break;

                case 'sqlsrv':
                    // SQL Server tidak memerlukan pengaturan tambahan secara umum
                    // Namun, Anda bisa menambahkan pengaturan session jika diperlukan
                    break;

                case 'oci':
                    // Oracle: Atur session ke pengaturan bahasa tertentu
                    self::$conn->exec("ALTER SESSION SET NLS_LANGUAGE = 'AMERICAN'");
                    self::$conn->exec("ALTER SESSION SET NLS_TERRITORY = 'AMERICA'");
                    break;

                case 'access':
                    // Microsoft Access: Tidak ada pengaturan tambahan
                    // Pastikan DSN Access dikonfigurasi dengan benar di ODBC
                    break;

                case 'db2':
                    // IBM DB2: Tambahkan pengaturan khusus jika diperlukan
                    self::$conn->exec("SET CURRENT SCHEMA = '{$config['DB_SCHEMA']}'");
                    break;

                case 'firebird':
                    // Firebird: Pastikan karakter set disesuaikan
                    self::$conn->exec('SET NAMES UTF8');
                    break;

                case 'cubrid':
                    // CUBRID: Tambahkan pengaturan encoding jika diperlukan
                    self::$conn->exec('SET NAMES utf8');
                    break;

                case 'informix':
                    // Informix: Atur pengaturan database
                    self::$conn->exec('SET ISOLATION TO DIRTY READ');
                    break;

                default:
                    // Database tidak dikenal
                    throw new PDOException("Unsupported database connection type: {$connection}");
            }
        } catch (PDOException $e) {
            self::handleConnectionError($e);
        }
    }

    private static function getDsn($config)
    {
        $connection = $config['DB_CONNECTION'];

        switch ($connection) {
            case 'mysql': // mysql
                return "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_DATABASE']}";
            case 'pgsql': // pgsql
                return "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_DATABASE']}";
            case 'sqlsrv': // SQL Server
                return "sqlsrv:Server={$config['DB_HOST']},{$config['DB_PORT']};Database={$config['DB_DATABASE']}";
            case 'sqlite': // Sqlite
                return "sqlite:{$config['DB_DATABASE']}"; // Path to SQLite file
            case 'oci': // Oracle
                return "oci:dbname={$config['DB_HOST']}:{$config['DB_PORT']}/{$config['DB_DATABASE']}";
            case 'access': // Microsoft Access
                return "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq={$config['DB_DATABASE']}";
            case 'db2': // DB2
                return "db2:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_DATABASE']}";
            default:
                throw new PDOException("Unsupported database connection type: {$connection}");
        }
    }

    public static function setDatabaseConfig($databaseName)
    {
        // Jika nama database ada dalam custom config, gunakan itu
        if (isset(self::$customConfig[$databaseName])) {
            self::connect(self::$customConfig[$databaseName]);
        } else {
            // Jika tidak, gunakan default config
            self::connect(self::$defaultConfig);
        }
    }

    public static function reconnect($databaseName)
    {
        self::$conn = null; // Tutup koneksi saat ini
        self::setDatabaseConfig($databaseName); // Reconnect dengan database baru
    }

    private static function handleConnectionError($exception)
    {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        error_log('Connection failed: ' . $exception->getMessage(), 3, $logDir . '/error.log');
        self::renderError($exception);
    }

    public static function renderError($exception)
    {
        static $errorDisplayed = false;
        if (!$errorDisplayed) {
            $errorDisplayed = true;
            if (!headers_sent()) {
                http_response_code(500);
            }
            $exceptionData = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
            extract($exceptionData);
            include __DIR__ . '/../app/Handle/errors/page_error.php';
        }
        exit();
    }

    // Fungsi untuk memuat .env
    private static function loadEnv()
    {
        $envFilePath = __DIR__ . '/../.env'; // Sesuaikan path dengan struktur folder Anda
        if (file_exists($envFilePath)) {
            $env = parse_ini_file($envFilePath);
            if ($env !== false) {
                foreach ($env as $key => $value) {
                    $_ENV[$key] = $value; // Menyimpan variabel ke $_ENV
                }
            } else {
                echo 'Error parsing .env file.';
            }
        } else {
            echo 'File .env tidak ditemukan.';
        }
    }
}

Database::init(); // Initialize default config on load

?>
