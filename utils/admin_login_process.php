<?php

require_once __DIR__ . "/../config.php"; 
require_once __DIR__ . "/functions.php"; 
require_once __DIR__ . "/protection.php";

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    if (!Database::$DBconnetion) {
        throw new DomainException("Connection to database failed. Please try again later :(", 401);
    }

    csrf_check();
    check_rate_limit('login');

    if (!empty($_POST['contact'])) { //probebly bot         
        throw new DomainException('Form declined.', 403);
    }

    if (check_form_timeout()) {
        throw new DomainException('Form timeout. please reload the page', 403);
    }

    $ip_address = $_SERVER['REMOTE_ADDR'];

    $input_username = trim($_POST['input_username']);
    $input_password = trim($_POST['input_password']);
    if (
        !validate_input_data('username', $input_username) ||
        !validate_input_data('password', $input_password)
    ) {
        record_login_attemp($input_username, $input_password, $ip_address, false);
        throw new DomainException('Invalid username or password', 401);
    }

    try {
        $sql = "SELECT id,password_hash FROM admins WHERE username = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$input_username]);

        $admin = $stmt->fetch();
    } catch (PDOException $e) {
        throw new DomainException($e->getMessage(), 500);
    }

    if (!$admin || !password_verify($input_password, $admin['password_hash'])) {
        record_login_attemp($input_username, $input_password, $ip_address, false);
        throw new DomainException('Invalid username or password', 401);
    }

    log_admin($admin['id']);
    record_login_attemp($input_username, $admin['password_hash'], $ip_address, true);
    echo json_encode([
        'success' => true,
        'message' => 'Logged in!',
        'redirect' => "admin.php",
        'rid' => $_SERVER['UNIQUE_ID'],
    ]);
    exit;
}

function record_login_attemp($input_username, $input_password, $ip_address, $success) {
    try {
        global $pdo;
        $sql = "INSERT INTO login_attempts (username, password, ip_address, success) VALUE (?, ? ,? , ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$input_username, $input_password, $ip_address, $success]);
    } catch (PDOException $e) {
        throw new DomainException("problem with the login process, please try again later", 403, $e);
    }
}