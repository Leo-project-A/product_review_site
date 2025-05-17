<?php

require '../config.php';

$username = 'admin';
$password = 'admin';
$hash_password = password_hash($password, PASSWORD_DEFAULT);

$sql_check = "SELECT id FROM admins WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($stmt,"s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo "Admin already exists.";
    exit;
}

$sql_insert = "INSERT INTO admins (username, password_hash) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $sql_insert);
mysqli_stmt_bind_param($stmt, "ss", $username, $hash_password);
mysqli_stmt_execute($stmt);

echo "Admin user created successfully!";
