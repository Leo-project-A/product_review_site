<?php
require_once  __DIR__ . '/../config.php';
require_once  __DIR__ . '/functions.php';

/** ********* Admin auth *********/
function log_admin($admin_id)
{
    session_regenerate_id(true);

    $_SESSION['admin_id'] = $admin_id;
    $_SESSION['admin_ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['admin_ua'] = $_SERVER['HTTP_USER_AGENT'];
    set_flash_message("info", "Admin Logged-in successfully");
    redirect("admin.php");
}

function is_admin_logged_in(): bool
{
    if (!isset($_SESSION['admin_id'], $_SESSION['admin_ip'], $_SESSION['admin_ua'])) {
        return false;
    }

    if (($_SESSION['admin_ip'] !== $_SERVER['REMOTE_ADDR']) || ($_SESSION['admin_ua'] !== $_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    if (!admin_id_exists($_SESSION['admin_id'])) {
        return false;
    }

    return true;
}

function admin_id_exists($admin_id): bool
{
    global $pdo;
    try {
        $sql = "SELECT COUNT(*) FROM admins WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$admin_id]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        return false; // give more feedback maybe
    } catch (Error $e) {
        return false; // give more feedback maybe
    }
}

function force_logout(): void
{
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    set_flash_message("info", "Logged Out Successfully");

    session_destroy();
    redirect("admin_login.php");
    exit;
}

function require_admin_login(): void
{
    if (!is_admin_logged_in()) {
        set_flash_message('error', 'Access denied. Please login again.');
        force_logout();
    }
}

/** ********* CSRF Tokens *********/
function generate_csrf_token(){
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
}

function get_csrf_token(): string {
    return $_SESSION['csrf_token'] ?? generate_csrf_token();
}

function validate_csrf_token(){
    if(isset($_SESSION['csrf_token'], $_POST['csrf_token'])
    && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){
        return true;
    }
    return false;
}

function csrf_check(){
    if (!validate_csrf_token()) {
        http_response_code(403); // Forbidden
        echo json_encode([
            'success' => false,
            'message' => 'Failed to validate CSRF token.',
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
        exit;
    }
}
