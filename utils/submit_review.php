<?php

include "../config.php"; // for pdo
include "functions.php"; // for tokens and validation

header("Content-Type: text/plain"); // tell jQuery it's plain text, no special code or JSON
http_response_code(200); // force success status so it wont crash the code

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!validate_csrf_token()) {
        echo "CSRF token validation failed.";
        die();
    }

    $input_name = trim($_POST['input_name']);
    $input_rating = trim($_POST['rating']);
    $input_description = trim($_POST['description']);

    $sql = "INSERT INTO reviews (name, rating, description) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$input_name, $input_rating, $input_description]);

    if ($stmt->rowCount() > 0) {
        echo "your review was accepted. it is now pending approval.<br> thank you :)";
        exit;

    } else { //something happened while inserting data
        echo "something happened while trying to send the review";
    }

}

