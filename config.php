<?php

// GLOBAL INPUT VALIDATION RULES
const DATA_RULES = [
    'username' => [
        'pattern' => '[a-zA-Z0-9._\-]{4,50}'
    ],
    'password' => [
        'min' => 8,
        'max' => 255
    ],
    'rating' => [
        'min' => 1,
        'max' => 5
    ],
    'description' => [
        'min' => 1,
        'max' => 500
    ]
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

    public static function connect()
    {
        if (!self::$pdo) {
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=" . self::$charset;

            try {
                self::$pdo = new PDO($dsn, self::$username, self::$password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new PDOException("Connection failed. Please try again later.");
            }
        }
        return self::$pdo;
    }
}

/*** Create new connection to DB and start session for the app ***/
$pdo = null;

try {
    $pdo = Database::connect();
} catch (PDOException $e) {
    http_response_code(500);
    // echo "<h1>500 – Internal Server Error</h1><p>We couldn't connect to the database. Please try again later.</p>"; 
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
