<?php

require_once "../config.php"; // for pdo
require_once "functions.php"; // for tokens and validation

header("Content-Type: application/JSON");

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    /* REFRACTOR funcions - used many times */
    if (!validate_csrf_token()) {
        http_response_code(403); // Forbidden
        echo json_encode([
            'success' => false,
            'message' => 'Failed to validate CSRF token.',
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
        exit;
    }

    $input_name = trim($_POST['input_name']);
    $input_rating = trim($_POST['rating']);
    $input_description = trim($_POST['description']);

    if (!validate_input_data('username', $input_name)) {
        echo json_encode([
            'success' => false,
            'message' => 'User name is unacceptable.',
        ]);
        exit;
    }
    if (!validate_input_data('rating', $input_rating)) {
        echo json_encode([
            'success' => false,
            'message' => 'Rating must be 1-5 Only.',
        ]);
        exit;
    }
    if (!validate_input_data('description', $input_description)) {
        echo json_encode([
            'success' => false,
            'message' => 'Description mus be 1-500 characters long',
        ]);
        exit;
    }

    try {
        $sql = "INSERT INTO reviews (name, rating, description) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$input_name, $input_rating, $input_description]);
    } catch (Exception $e) { // change to Throwable? is it better? when/where.. just default throw(e) and thats it?
        // log err
        // echo $e;
    } catch (Error $e) { // find where to throw this. right now: db down - 0 reviews shown, site working just fine.
        // log err
        // echo $e;
    }

    if ($stmt->rowCount() > 0) { // success to INERST into table
        http_response_code(200); // return to success block 
        echo json_encode([
            'success' => true,
            'message' => 'your review was accepted. it is now pending approval.<br> thank you.',
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
        exit;
    } else {  // failed to INERST into table
        http_response_code(500); // failed to insert - user dont need any extra info. risky
        /* is it better to leave this? maybe only while debugging? 
        echo json_encode([
            'success' => false,
            'message' => 'something happened while trying to send the review.',
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
        */
        exit;
    }
}

