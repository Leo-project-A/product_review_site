<?php
require_once __DIR__ . "/partials/header.php";

if (is_admin_logged_in()) {
    redirect("admin.php");
}
generate_csrf_token();
?>

<div class="page-container">

    <h1 class="title">Welcome to Admin Login</h1>
    <h2 class="subtitle">Restricted Area: Authorized Personnel Only</h2>

    <!-- AJAX response sent from submit_form.php -->
    <div id="response-message"></div>

    <div class="form">

        <form id="admin-form" method="POST" action="" class="form">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        const $form = $('#admin-form');
        const $user_msg = $('#response-message');

        $form.on('submit', function (e) {
            e.preventDefault();
            $user_msg.text('');
            const formData = $form.serialize();
            lockForm();

            $.post("utils/admin_login_process.php", formData, function (response) {
                $user_msg.text(response.message).css('color', response.success ? 'green' : 'red');
                if (response.success) {
                    window.location.href = response.redirect;
                }
            }, 'json')
                .fail(function (xhr) {
                    var errMessage = 'something went wrong. Please try again later';
                    try {
                        const json = JSON.parse(xhr.responseText);
                        if (json.message) {
                            errMessage = json.message;
                        }
                        $user_msg.html($('<span>').css('color', 'red').text(errMessage));
                    } catch (e) { }
                    $user_msg.text(errMessage).css('color', 'red');
                }).always(function (response) {
                    unlockForm();
                    if (response.success) $form[0].reset();
                });
        });

        function lockForm() {
            $form.find(':input:not([type="hidden"])').prop('disabled', true);
            $('body').css('cursor', 'wait');
        }

        function unlockForm() {
            $form.find(':input').prop('disabled', false);
            $('body').css('cursor', 'default');
        }

    });
</script>