<?php
// Logging out of the session from admin access

session_start();

$_SESSION = [];

session_destroy();

header("Location: index.php");
exit;