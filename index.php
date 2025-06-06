<?php
require_once __DIR__ . "/partials/header.php";

generate_csrf_token();
$reviews = [];

try {
    $sql = "SELECT * FROM reviews WHERE approved = 1"; // limit this shit... 10 at a time. think 1000s of reviews.
    $stmt = $pdo->query($sql);
    $reviews = $stmt->fetchAll();
} catch (Exception $e) { // change to Throwable? is it better? when/where.. just default throw(e) and thats it?
    // log err
    // echo $e;
} catch (Error $e) { // find where to throw this. right now: db down - 0 reviews shown, site working just fine.
    // log err
    // echo $e;
}

?>

<div class="page-container">

    <!-- this is STATIC (for now for testing). only one product for now -->
    <!-- later this will be pulled from the database based on the product were viwing -->
    <div class="product-container">
        <h1 class="title">SolarSnap Mini Charger</h1>
        <h2 class="subtitle">A compact solar-powered USB charger that fits in your pocket
            and keeps your devices alive wherever the sun shines.</h2>
        <p class="description">
            Whether you're hiking, commuting, or just enjoying the outdoors,
            the SolarSnap Mini Charger harnesses clean solar energy to power up your smartphone,
            wireless earbuds, or GPS on the go. With its lightweight build and fast USB output,
            it's your sustainable backup battery — no outlets required.
        </p>
    </div><br><br>
    <!-------------------------------------------------------------------->

    <div class="form">
        <h2 class="subtitle">Submit a Review</h2>

        <!-- AJAX response sent from submit_form.php -->
        <div id="response-message"></div>

        <form id="review-form" method="POST" action="" class="form">
            <?= form_hidden_fields(); ?>

            <label for="input_name">Your name please</label>
            <?php $rule = DATA_RULES['username']; ?>
            <input id="input_name" type="text" name="input_name" pattern="<?= $rule['pattern'] ?>" required>

            <label for="rating">How do you rate this product (1–5)</label>
            <select id="rating" name="rating" required>
                <option value="">Choose</option>
                <option value="1">1 - Poor</option>
                <option value="2">2 - Fair</option>
                <option value="3">3 - Good</option>
                <option value="4">4 - Very Good</option>
                <option value="5">5 - Excellent</option>
            </select>

            <label for="description">Your review:</label>
            <?php $rule = DATA_RULES['description']; ?>
            <textarea id="description" name="description" rows="4" minlength="<?= $rule['min'] ?>"
                maxlength="<?= $rule['max'] ?>" required>
            </textarea>

            <input type="submit" value="Submit">
        </form>


    </div>
    <!-------------------------------------------------------------------->

    <h2 class="subtitle">All User Reviews</h2>
    <div class="review-container">

        <?php if (count($reviews) < 1): ?>
            <span> <?= "No reviews currently :(" ?> </span>
        <?php else: ?>
            <?php foreach ($reviews as $cur_review): ?>
                <div class="review">
                    <h3 class="user-name"><?= sanitize_output($cur_review['name']); ?></h3>
                    <span class="review-rating"><?= sanitize_output($cur_review['rating']); ?> stars</span>
                    <p class="review-description">
                        <?= sanitize_output($cur_review['description']); ?>
                    </p>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<?php
include_once "partials/footer.php";
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script> // consider moving all the scripts to their own script file - you could make more helper functions in javascript
    document.addEventListener("DOMContentLoaded", function () {
        $('#review-form').on('submit', function (e) {
            e.preventDefault();
            // form-handling? .always change prop before finishing submiting. bette UX
            $('input[type="submit"]').prop('disabled', true); //stop further pressing
            $('body').css('curser', 'wait');

            const formData = $(this).serialize();

            $.post('utils/submit_review.php', formData, function (response) {
                if (response.success) {
                    $('#response-message').text(response.message);
                    $('#review-form')[0].reset(); 
                } else {
                    $('#response-message').html($('<span>').css('color', 'red').text(response.message));
                }
            }, 'json').fail(function (xhr) {
                var errMessage = 'something went wrong. Please try again later - index';
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