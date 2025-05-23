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
            'id' => $review_id,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
        exit;
    }

    $review_id = $_POST['review_id'] ?? null;
    if (!validate_input_data('review_id', $review_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid review ID.',
        ]);
        exit;
    }
    $action = $_POST['action'] ?? '';
    if ($action !== 'approve' || $action !== 'decline') {
        echo json_encode([
            'success' => false,
            'message' => 'Iligal action by admin.',
        ]);
        exit;
    }

    if ($action === "approve") {
        try {
            $sql = "UPDATE reviews SET approved = 1 WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$review_id]);
        } catch (e) {
            http_response_code(500);
            exit;
        }

        http_response_code(200); // return to success block 
        if ($stmt->rowCount() > 0) { // success to UPDATE table
            echo json_encode([
                'success' => true,
                'message' => 'your review was accepted. it is now pending approval.<br> thank you.',
                'id' => $review_id,
                'timestamp' => date('Y-m-d H:i:s'),
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);
            exit;
        } else {  // failed to UPDATE table
            echo json_encode([
                'success' => false,
                'message' => 'something happened while trying to send the review.',
                'id' => $review_id,
                'timestamp' => date('Y-m-d H:i:s'),
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);
            exit;
        }

    } elseif ($action === "decline") {
        try {
            $sql = "DELETE FROM reviews WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$review_id]);
        } catch (e) {
            http_response_code(500);
            exit;
        }

        http_response_code(200); // return to success block 
        if ($stmt->rowCount() > 0) { // success to DELETE table
            echo json_encode([
                'success' => true,
                'message' => 'your review was accepted. it is now pending approval.<br> thank you.',
                'id' => $review_id,
                'timestamp' => date('Y-m-d H:i:s'),
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);
            exit;
        } else {  // failed to DELETE table
            echo json_encode([
                'success' => false,
                'message' => 'something happened while trying to send the review.',
                'id' => $review_id,
                'timestamp' => date('Y-m-d H:i:s'),
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);
            exit;
        }
    }
}