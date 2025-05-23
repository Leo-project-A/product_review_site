<?php
require_once "partials/header.php";

// REWORK entire form admin login - using AJAX aswell

if (is_admin_logged_in()) {
    redirect("admin.php");
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
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

    $input_username = trim($_POST['input_username']);
    $input_password = trim($_POST['input_password']);
    if(!validate_input_data('username', $input_username) ||
    !validate_input_data('password', $input_password)){
        $_SESSION['err_msg'] = "Invalid username or password";
        exit;
    }

    try {
        $sql = "SELECT * FROM admins WHERE username = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$input_username]);

        if ($admin = $stmt->fetch()) { // found admin
            $admin_password = $admin['password_hash'];
            if (password_verify($input_password, $admin_password)) {
                $_SESSION['logged_in_as_admin'] = true;
                $_SESSION['user'] = $input_username;
                $_SESSION['user_msg'] = "connected successfully: $input_username";
                redirect("admin.php");
                exit;
            } else {
                $_SESSION['err_msg'] = "Invalid username or password";
            }
        } else {
            $_SESSION['err_msg'] = "Invalid username or password";
        }
    } catch (PDOException $e) {
        $_SESSION['err_msg'] = "something went wrong..";
    } catch (Error $e) { // find where to throw this. right now: db down - 0 reviews shown, site working just fine.
        // log err
        // echo $e;
    }
}

?>

<div class="page-container">

    <h1 class="title">Welcome to Admin Login</h1>
    <h2 class="subtitle">Restricted Area: Authorized Personnel Only</h2>

    <div class="form">

        <?php if (isset($_SESSION['user_msg'])): ?>
            <p style="color:green">
                <?php
                echo $_SESSION['user_msg'];
                unset($_SESSION['user_msg']);
                ?>
            </p>
        <?php endif; ?>

        <?php if (isset($_SESSION['err_msg'])): ?>
            <p style="color:red">
                <?php
                echo $_SESSION['err_msg'];
                unset($_SESSION['err_msg']);
                ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="" class="form">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <label for="input_username">Username</label>
            <input type="text" id="input_username" name="input_username" required>

            <label for="input_password">Password</label>
            <input type="password" id="input_password" name="input_password" required>

            <input type="submit" value="Login">
        </form>
    </div>

</div>

<?php
include_once "partials/footer.php";
?>