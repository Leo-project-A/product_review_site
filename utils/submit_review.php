<?php

require_once __DIR__ . "/../config.php"; // for pdo
require_once __DIR__ . "/functions.php"; // for tokens and validation
require_once __DIR__ . "/../utils/protection.php";

header("Content-Type: application/JSON");

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    if (!Database::$DBconnetion) {
        throw new DomainException("Connection to database failed. Please try again later :(", 401);
    }

    csrf_check();
    check_rate_limit('submit_review');

    $input_name = trim($_POST['input_name']);
    $input_rating = trim($_POST['rating']);
    $input_description = trim($_POST['description']);

    $ip_address = $_SERVER['REMOTE_ADDR'];

    if (!empty($_POST['contact'])) { //probebly bot 
        throw new DomainException('Form declined', 403);
    }

    if (check_form_timeout()) {
        throw new DomainException('Form timeout.', 403);
    }

    if (!validate_input_data('username', $input_name)) {
        throw new DomainException('Username is unacceptable.', 403);
    }

    if (!validate_input_data('rating', $input_rating)) {
        throw new DomainException('Rating must be 1-5 Only.', 403);
    }

    if (!validate_input_data('description', $input_description)) {
        throw new DomainException('Description mus be 1-500 characters long', 403);
    }

    if (has_review_by_user($input_name)) {
        throw new DomainException('Username Already submitted a review.', 409);
    }

    try {
        $sql = "INSERT INTO reviews (name, rating, description, ip_address) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$input_name, $input_rating, $input_description, $ip_address]);

        $results = $stmt->rowCount();
    } catch (PDOException $e) {
        throw $e;
    }

    if ($results > 0) { // success to INERST into table
        http_response_code(200); 
        echo json_encode([ // think about reworking a cross code JSON frame. same data.
            'success' => true,
            'message' => 'your review was accepted. it is now pending approval. thank you.'
        ]);
        exit;
    } else {  // failed to INERST into table
        throw new RuntimeException('something happened while trying to send the review.', 500);
    }
}

