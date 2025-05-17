<?php
// DATABASE stuff 

class Database {
    private static $host = "localhost";
    private static $username = "root";
    private static $password = "";
    private static $db_name = "product_review";
    private static $charset = "utf8mb4";

    private static $pdo = null;

    public static function connect(){
        if (!self::$pdo){
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=" . self::$charset;

            try{
                self::$pdo = new PDO($dsn, self::$username, self::$password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            } catch (PDOException $e){
                die("Connection failed. Please try again later.");
            }
        }
        return self::$pdo;
    }
}

/*** Create new connection to DB and start session for the app ***/
$pdo = Database::connect();

if(session_status() === PHP_SESSION_NONE){
    session_start();
}
