<?php
require_once  __DIR__ . "/partials/header.php";

require_admin_login();
generate_csrf_token();

$waiting_reviews = [];

try {
    $sql = "SELECT * FROM reviews WHERE approved = 0";
    $stmt = $pdo->query($sql);
    $waiting_reviews = $stmt->fetchAll();
} catch (Throwable $e) {
    log_error($e);
    Database::$DBconnetion = false;
}

?>

<div class="page-container">

    <h1 class="title">Welcome ADMIN ! </h1>
    <h2 class="subtitle">Here are reviews waiting for your approval</h2>

    <div class="review-container">

        
        <div id="response-message"></div>

        <?php if (!Database::$DBconnetion): ?>
            <span> <?= "Connection to database failed. Please try again later :(" ?> </span>
        <?php elseif (count($waiting_reviews) < 1): ?>
            <span> <?= "You have no pending reviews currently" ?> </span>
        <?php endif; ?>

        <?php foreach ($waiting_reviews as $cur_review): ?>
            <div class="review">
                <h3 class="user-name"><?= sanitize_output($cur_review['name']); ?></h3>
                <span class="review-rating"><?= sanitize_output($cur_review['rating']); ?> stars</span>
                <p class="review-description">
                    <?= sanitize_output($cur_review['description']); ?>
                </p>

                <div class="button-row">
                    <?php foreach (ADMIN_ACTIONS as $action): ?>
                        <form method="POST" action="" class="form <?= $action ?>-form">
                            <?= form_hidden_fields(); ?>
                            
                            <input type="hidden" name="review_id" value="<?= sanitize_output($cur_review['id']); ?>">
                            <input type="hidden" name="action" value="<?= $action ?>">
                            <input class="<?= $action ?>" type="submit" value="<?= $action ?>">
                        </form>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>

<?php
include_once "partials/footer.php";
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script> // consider moving all the scripts to their own script file - you could make more helper functions in javascript
    
    document.addEventListener("DOMContentLoaded", function () {
        /* 
        approve and decline basictly do the same thing. can i make the function f() 
        and just attach it to both the the bottuns?
        SAME with the actions in admin_actions - rework it. refractor the functions
        */

        // Admin approve the review
        $('.approve-form').on('submit', function (e) {
            e.preventDefault();
            // form-handling? .always change prpo before finishing submiting. bette UX
            $('input[type="submit"]').prop('disabled', true); //stop further pressing
            $('body').css('curser', 'wait');

            const review_box = $(this).closest('.review');
            const form_data = $(this).serialize();
            var errMessage = 'something went wrong. Please try again later. admin-84';

            $.post("utils/admin_actions.php", form_data, function (response) {
                if (response.success) {
                    $('#response-message').text(response.message);
                    review_box.remove();
                } else {
                    $('#response-message').html($('<span>').css('color', 'red').text(response.message));
                }
            }, 'json').fail(function (xhr) {
                try {
                    const json = JSON.parse(xhr.responseText);
                    if (json.message) {
                        errMessage = json.message;
                    }
                } catch (e) { // default error? xhr err?
                    /* this in debug mode? should the user have this info?
                    console.warn("Could not parse error response:", xhr.responseText);
                    */
                }
                $('#response-message').html($('<span>').css('color', 'red').text(errMessage));
            }).always(function () { // form-handling? .always return to nomral after finishing submiting
                $('input[type="submit"]').prop('disabled', false);
                $('body').css('curser', 'default');
            });
        });

        // Admin decline the review
        $('.decline-form').on('submit', function (e) {
            e.preventDefault();

            const review_box = $(this).closest('.review');
            const form_data = $(this).serialize();
            var errMessage = "something went wrong. Please try again later.";

            $.post("utils/admin_actions.php", form_data, function (response) {
                if (response.success) {
                    $('#response-message').text(response.message);
                    review_box.remove();
                } else {
                    $('#response-message').html($('<span>').css('color', 'red').text(response.message));
                }
            }, 'json').fail(function (xhr) {
                try {
                    const json = JSON.parse(xhr.responseText);
                    if (json.message) {
                        errMessage = json.message;
                    }
                } catch (e) { // default error? xhr err?
                    /* this in debug mode? should the user have this info?
                    console.warn("Could not parse error response:", xhr.responseText);
                    */
                }
                $('#response-message').html($('<span>').css('color', 'red').text(errMessage));
            }).always(function () { // form-handling? .always return to nomral after finishing submiting
                $('input[type="submit"]').prop('disabled', false);
                $('body').css('curser', 'default');
            });
        });
    });
</script>