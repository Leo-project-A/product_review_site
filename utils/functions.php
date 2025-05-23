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

function validate_input_data($datatype, $data){
    $limit_username_min = 4;
    $limit_username_max = 50;
    $limit_username_char = 'a-zA-Z0-9._-';
    $username_regex = "/^[$limit_username_char]{{$limit_username_min},{$limit_username_max}}$/";;

    $limit_password_min = 8;
    $limit_password_max = 255;

    $limit_rating_min = 1;
    $limit_rating_max = 5;
    
    $limit_description_min = 1;
    $limit_description_max = 500;

    return match($datatype){
        "username" => preg_match($username_regex, $data),
        "password" =>  strlen($data) >= $limit_password_min && strlen($data) <= $limit_password_max,
        "rating" => is_numeric($data) && $data >= $limit_rating_min && $data <= $limit_rating_max,
        "description" => strlen($data) >= $limit_description_min && strlen($data) <= $limit_description_max,
        "review_id" => is_numeric($data),
        default => false
    };
}