<?php

function redirect($location){
    header("Location: $location");
    exit;
}

function is_admin_logged_in() {
    return isset($_SESSION['logged_in_as_admin']) && $_SESSION['logged_in_as_admin'] === true;
}

function generate_csrf_token(){
    if(!isset($_SESSION['csrf_token'])){
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token(){
    if(isset($_SESSION['csrf_token']) && isset($_POST['csrf_token']) 
    && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){
        return true;
    }
    return false;
}

function validate_input_data($datatype, $data) {
    if (!defined('DATA_RULES') || !isset(DATA_RULES[$datatype])){
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

function sanitize_output($data){
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function set_flash_message($type, $message){
    if(!isset($_SESSION['flash_messages'])){
        $_SESSION['flash_messages'] = [];
    }

    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message
    ];
}

function flash_meesages(){
    return; // NEEDS rework - maybe add notification partial
}

function log_error() {
    // ADD: logging system for error. 
    // make a global system reuse for logging bugs + logging user actions + logging login attempts + logging user abuse
    return;
}