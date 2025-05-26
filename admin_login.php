<?php
require_once __DIR__ . "/partials/header.php";

// REWORK entire form admin login - using AJAX aswell

if (is_admin_logged_in()) {
    redirect("admin.php");
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    csrf_check();
    check_rate_limit('login');

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

    $input_username = trim($_POST['input_username']);
    $input_password = trim($_POST['input_password']);
    if (
        !validate_input_data('username', $input_username) ||
        !validate_input_data('password', $input_password)
    ) {
        redirect("admin_login.php");
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
                log_admin($admin['id']);
            } else {
                redirect("admin_login.php");
                $_SESSION['err_msg'] = "Invalid username or password";
            }
        } else {
            redirect("admin_login.php");
            $_SESSION['err_msg'] = "Invalid username or password";
        }
        redirect("admin_login.php");
    } catch (PDOException $e) {
        redirect("admin_login.php");
        set_flash_message('error', "something went wrong..");
    } catch (Error $e) { // find where to throw this. right now: db down - 0 reviews shown, site working just fine.
        redirect("admin_login.php");
        set_flash_message('error', "something went wrong..");
        // log err
        // echo $e;
    }
}

generate_csrf_token();
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

        
        <form id="review-form" method="POST" action="" class="form">
            <?= form_hidden_fields(); ?>

            <label for="input_username">Username</label>
            <?php $rule = DATA_RULES['username']; ?>
            <input type="text" id="input_username" name="input_username" pattern="<?= $rule['pattern'] ?>" required>

            <label for="input_password">Password</label>
            <?php $rule = DATA_RULES['password']; ?>
            <input type="password" id="input_password" name="input_password" minlength="<?= $rule['min'] ?>"
                maxlength="<?= $rule['max'] ?>" required>

            <input type="submit" value="Login">
        </form>
    </div>

</div>

<?php
include_once "partials/footer.php";
?>