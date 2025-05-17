<?php
include "partials/header.php";

if (isset($_SESSION['logged_in_as_admin']) && ($_SESSION['logged_in_as_admin'] === true)) {
    redirect("admin.php");
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if(!validate_csrf_token()){
        die("CSRF token validation failed.");
    }
    
    $input_username = trim($_POST['input_username']);
    $input_password = trim($_POST['input_password']);

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
                $_SESSION['err_msg'] = "incorrect password";
            }
        } else {
            $_SESSION['err_msg'] = "admin not found";
        }
    } catch (PDOException $e) {
        $_SESSION['err_msg'] = "something went wrong..";
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
include "partials/footer.php";
?>