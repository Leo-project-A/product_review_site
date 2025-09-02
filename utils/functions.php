<?php

require_once  __DIR__ . "/../utils/auth.php";

function redirect($location){
    header("Location: $location");
    exit;
}

function form_hidden_fields()
{
    $csrf_token = get_csrf_token();
    $timestamp = time();

    return <<<html
        <input type="hidden" name="csrf_token" value="$csrf_token">
        <input type="text" name="contact" value="" style="display: none;">
        <input type="hidden" name="form_loaded_at" value="$timestamp">
    html;
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