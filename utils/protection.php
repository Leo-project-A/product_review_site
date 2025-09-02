<?php

require_once __DIR__ . '/../config.php';

/***** I/O Dava Validation and cleaup *****/
function validate_input_data($datatype, $data)
{
    if (!defined('DATA_RULES') || !isset(DATA_RULES[$datatype])) {
        return false;
    }

    $rule = DATA_RULES[$datatype];

    return match ($datatype) {
        'username' => preg_match("/^{$rule['pattern']}+$/", $data)
        && strlen($data) >= $rule['min'] && strlen($data) <= $rule['max'],
        'password' => strlen($data) >= $rule['min'] && strlen($data) <= $rule['max'],
        'rating' => filter_var($data, FILTER_VALIDATE_INT) && $data >= $rule['min'] && $data <= $rule['max'],
        'description' => strlen($data) >= $rule['min'] && strlen($data) <= $rule['max'],
        'review_id' => is_numeric($data),
        default => false
    };
}

function sanitize_output($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/***** Rate Limiting *****/

function is_rate_limited($action, $limit, $window)
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $key = "rate_{$action}_{$ip}";
    $now = time();

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [];
    }

    $recent = [];
    foreach ($_SESSION[$key] as $timestamp) {
        if ((time() - $timestamp) <= $window) {
            $recent[] = $timestamp;
        }
    }
    $_SESSION[$key] = $recent;
    $_SESSION[$key][] = $now;

    return count($_SESSION[$key]) > $limit;
}

function check_rate_limit($action)
{
    if (is_rate_limited($action, RATE_LIMIT, RATE_LIMIT_WINDOW)) {
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'message' => "too many requests, please wait and try again later."
        ]);
        exit;
    }
}

/** Spam, Bot protections */

function has_review_by_user(string $username)
{
    global $pdo;                              

    $sql  = 'SELECT 1 FROM reviews WHERE name = ? LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);

    return $stmt->fetchColumn() !== false; 
}

function check_form_timeout(){
    $formLoaded = (int) ($_POST['form_loaded_at'] ?? 0);
    $now = time();
    $elapsed = $now - $formLoaded;

    return ($formLoaded === 0) || ($elapsed < FORM_TIME_MIN) || ($elapsed > FORM_TIME_MAX);
}


