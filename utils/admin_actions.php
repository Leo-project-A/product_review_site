<?php

require_once __DIR__ . "/../config.php"; // for pdo
require_once __DIR__ . "/functions.php"; // for tokens and validation
require_once __DIR__ . "/protection.php";

header("Content-Type: application/JSON");

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    /* REFRACTOR funcions - used many times */
    csrf_check();
    // check_rate_limit('review_update');

    $review_id = $_POST['review_id'] ?? null;
    if (!validate_input_data('review_id', $review_id)) {
        throw new DomainException("Invalid review ID ($review_id)", 403);
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
        throw new DomainException('Iligal action by admin.', 403);
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$review_id]);
        $results = $stmt->rowCount();
    } catch (PDOException $e) {
        throw $e;
    }

    http_response_code(200); // return to success block 
    if ($results > 0) { // success to UPDATE table
        echo json_encode([
            'success' => true,
            'message' => $user_msg,
            'id' => $review_id,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR']
        ]);
        exit;
    } else {  // failed to UPDATE table
        throw new RuntimeException('something went wrong. Please try again later.', 500);
    }
}