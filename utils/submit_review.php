<?php

require_once __DIR__ . "/../config.php"; // for pdo
require_once __DIR__ . "/functions.php"; // for tokens and validation
require_once __DIR__ . "/../utils/protection.php";

header("Content-Type: application/JSON");

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    /* REFRACTOR funcions - used many times */
    csrf_check();
    check_rate_limit('submit_review');

    $input_name = trim($_POST['input_name']);
    $input_rating = trim($_POST['rating']);
    $input_description = trim($_POST['description']);

    if (!empty($_POST['contact'])) { //probebly bot 
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Form declined',
        ]);
        exit;
    }

    if (check_form_timeout()) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Form timeout.'
        ]);
        exit;
    }

    if (!validate_input_data('username', $input_name)) {
        echo json_encode([
            'success' => false,
            'message' => 'Username is unacceptable.',
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

    if (!check_duplicate_review($input_name)) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Username Already submitted a review.',
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

