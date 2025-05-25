<?php
require_once "config.php";
require_once "utils/functions.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Review Site</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <!-- Notification Container -->
    <?php if (isset($_SESSION['flash_messages'])): ?>
        <div class="notification-container">
            <?php $messages = $_SESSION['flash_messages']; ?>
            <?php unset($_SESSION['flash_messages']); ?>
            <?php foreach ($messages as $flash_message): ?>
                <div class="notification <?= $flash_message['type'] ?? '' ?>">
                    <?= $flash_message['message']; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>