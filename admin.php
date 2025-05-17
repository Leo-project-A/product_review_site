<?php
include "partials/header.php";

if (!is_admin_logged_in()) {
    redirect("admin_login.php");
}

$sql = "SELECT * FROM reviews WHERE approved = 0";
$stmt = $pdo->query($sql);
$waiting_reviews = $stmt->fetchAll();

?>

<div class="page-container">

    <h1 class="title">Welcome ADMIN ! </h1>
    <h2 class="subtitle">Here are reviews waiting for your approval</h2>

    <div class="review-container">

        <!-- AJAX response -->
        <div id="response-message"></div>

        <?php if (count($waiting_reviews) < 1): ?>
            <span> <?php echo "You have no pending reviews currently" ?> </span>
        <?php endif; ?>

        <?php foreach ($waiting_reviews as $cur_review): ?>
            <div class="review">
                <h3 class="user-name"><?php echo htmlspecialchars($cur_review['name']); ?></h3>
                <span class="review-rating"><?php echo htmlspecialchars($cur_review['rating']); ?> stars</span>
                <p class="review-description">
                    <?php echo htmlspecialchars($cur_review['description']); ?>
                </p>

                <div class="button-row">
                    <form method="POST" action="" class="form approve-form">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($cur_review['id']); ?>">
                        <input type="hidden" name="action" value="approve">
                        <input class="approve" type="submit" value="approve">
                    </form>

                    <form method="POST" action="" class="form decline-form"
                        onsubmit="return confirm('are you sure you want to delete?');">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($cur_review['id']); ?>">
                        <input type="hidden" name="action" value="decline">
                        <input class="decline" type="submit" value="decline">
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>

<?php
include "partials/footer.php";
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Admin approve the review
        $('.approve-form').on('submit', function (e) {
            e.preventDefault();

            const review_box = $(this).closest('.review');
            const form_data = $(this).serialize();

            $.post("utils/admin_actions.php", form_data, function (response) {
                $('#response-message').html(response);
                review_box.remove();
            }).fail(function () {
                $('#response-message').html("<span style='color:red;'>AJAX request failed.</span>");
            });
        });

        // Admin decline the review
        $('.decline-form').on('submit', function (e) {
            e.preventDefault();

            const review_box = $(this).closest('.review');
            const form_data = $(this).serialize();

            $.post("utils/admin_actions.php", form_data, function (response) {
                $('#response-message').html(response);
                review_box.remove();
            }).fail(function () {
                $('#response-message').html("<span style='color:red;'>AJAX request failed.</span>");
            });
        });
    });

</script>