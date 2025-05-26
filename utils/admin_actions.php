<?php

require_once __DIR__ . "/../config.php"; // for pdo
require_once __DIR__ . "/functions.php"; // for tokens and validation

header("Content-Type: application/JSON");

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    /* REFRACTOR funcions - used many times */
    csrf_check();

    $review_id = $_POST['review_id'] ?? null;
    if (!validate_input_data('review_id', $review_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid review ID.',
        ]);
        exit;
    }
    $action = $_POST['action'] ?? '';
    $sql = "";
    $user_msg = '';

    if ($action === "approve") {
        $sql = "UPDATE reviews SET approved = 1 WHERE id = ?";
        $user_msg = 'Review have been aproved!';
    } elseif ($action === "decline") {
        $sql = "DELETE FROM reviews WHERE id = ?";
        $user_msg = 'Review have been declined!';
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Iligal action by admin.',
        ]);
        exit;
    }

    try {
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
            'message' => $user_msg,
            'id' => $review_id,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
        exit;
    } else {  // failed to UPDATE table
        echo json_encode([
            'success' => false,
            'message' => 'something happened.. please try again later',
            'id' => $review_id,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
        exit;
    }
}