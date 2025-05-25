<?php
// Logging out of the session from admin access
// ADD "logged out" message, using flash()

require_once 'utils/functions.php';

if (!is_admin_logged_in()) {
    redirect("index.php");
}

require_once "utils/functions.php";

session_start();
$_SESSION = [];
session_destroy();

session_start();
set_flash_message("info", "Logouted successfully");

header("Location: index.php");
exit;