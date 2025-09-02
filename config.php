<?php

define('APP_ENV', 'dev');
define('LOG_FILE', __DIR__ . '/logs/app.log');
date_default_timezone_set("Asia/Jerusalem");

if (APP_ENV === 'dev'){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else { // when you change to 'prod' this will never be reached.. is this ok?
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
}

// GLOBAL INPUT VALIDATION RULES
const ADMIN_ACTIONS = ['approve', 'decline'];

const RATE_LIMIT = 3;
const RATE_LIMIT_WINDOW = 30;

const FORM_TIME_MIN = 2;
const FORM_TIME_MAX = 3600;

const DATA_RULES = [
    'username' => [
        'min' => 4,
        'max' => 50,
        'pattern' => '[a-zA-Z0-9._\-]{4,50}'
    ],
    'password' => [
        'min' => 5,
        'max' => 255
    ],
    'rating' => [
        'min' => 1,
        'max' => 5
    ],
    'description' => [
        'min' => 1,
        'max' => 500
    ],
    'review_id' => []
];

// DATABASE stuff 

class Database
{
    private static $host = "localhost";
    private static $username = "root";
    private static $password = "";
    private static $db_name = "product_review";
    private static $charset = "utf8mb4";

    private static $pdo = null;

    public static $DBconnetion = false;

    public static function connect()
    {
        if (!self::$pdo) {
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=" . self::$charset;

            try {
                self::$pdo = new PDO($dsn, self::$username, self::$password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                self::$DBconnetion = true;
            } catch (PDOException $e) {
                // throw new RuntimeException("Connection failed. Please try again later.", 500, $e);
            }
        }
        return self::$pdo;
    }
}

/*** Start session for the app and Create new connection to DB ***/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SERVER['UNIQUE_ID'])){
    $_SERVER['UNIQUE_ID'] = bin2hex(random_bytes(8));
}

$pdo = null;

// try to connect to the DB, if not successful- get null, DBconnection is false 
$pdo = Database::connect();

header('X-request-ID: ' .$_SERVER['UNIQUE_ID']);


function log_error(Throwable $e, array $extra = []){
    $entry = [
        'rid' => $_SERVER['UNIQUE_ID'],
        'time' => date('c'),
        'type' => get_class($e),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'msg' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ] + $extra;

    error_log(json_encode($entry).PHP_EOL, 3, LOG_FILE);
}

set_error_handler(function ($sev, $msg, $file, $line) {
    log_error(new ErrorException($msg, 0 , $sev, $file, $line,));
    return APP_ENV === 'prod';
});

set_exception_handler(function (Throwable $e) {
    log_error($e);
    http_response_code($e->getCode() ?: 500);
    
    if ($e instanceof DomainException) {
        $json_response = $e->getMessage();
    } else {
        if ( APP_ENV === 'dev') {
            $json_response = $e->getMessage();
        } else {
            $json_response = "Server Error";
        }
    }

    echo json_encode([
        'success' => false,
        'message' => $json_response,
        'rid' => $_SERVER['UNIQUE_ID']
    ]);
    exit;
});
