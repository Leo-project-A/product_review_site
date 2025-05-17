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
    if(isset($_POST['csrf_token']) && ($_POST['csrf_token'] == $_SESSION['csrf_token'])){
        return true;
    }
    return false;
}