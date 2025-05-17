<?php

include "../config.php"; // for pdo
include "functions.php"; // for tokens and validation

header("Content-Type: text/plain");
http_response_code(200);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!validate_csrf_token()) {
        die("CSRF token validation failed.");
    }

    $review_id = $_POST['review_id'] ?? null;
    $action = $_POST['action'] ?? '';

    if ($action === "approve") {
        $sql = "UPDATE reviews SET approved = 1 WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$review_id]);

        echo ($stmt->rowCount() > 0)
            ? "review (id=$review_id) has been approved for posting"
            : "something went wrong.. unable to approve";
        exit;

    } elseif ($action === "decline") {
        $sql = "DELETE FROM reviews WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$review_id]);

        echo ($stmt->rowCount() > 0)
            ? "review (id=$review_id) has been declined for posting"
            : "something went wrong.. unable to decline";
        exit;
    }
}