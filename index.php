<?php
include "partials/header.php";

$sql = "SELECT * FROM reviews WHERE approved = 1";
$stmt = $pdo->query($sql);
$reviews = $stmt->fetchAll();

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

    <!-- this is the new review form -->
    <div class="form">
        <h2 class="subtitle">Submit a Review</h2>

        <!-- AJAX response -->
        <div id="response-message"></div>

        <form id="review-form" method="POST" action="" class="form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">

            <label for="input_name">Your name please</label>
            <input id="input_name" type="text" name="input_name" required>

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
            <textarea id="description" name="description" rows="4" required></textarea>

            <input type="submit" value="Submit">
        </form>

        
    </div>
    <!------------------------------------>

    <h2 class="subtitle">All User Reviews</h2>
    <div class="review-container">

        <?php if (count($reviews) < 1): ?>
            <span> <?php echo "No reviews currently :(" ?> </span>
        <?php else: ?>
            <?php foreach ($reviews as $cur_review): ?>
                <div class="review">
                    <h3 class="user-name"><?php echo htmlspecialchars($cur_review['name']); ?></h3>
                    <span class="review-rating"><?php echo htmlspecialchars($cur_review['rating']); ?> stars</span>
                    <p class="review-description">
                        <?php echo htmlspecialchars($cur_review['description']); ?>
                    </p>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<?php
include "partials/footer.php";
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        $('#review-form').on('submit', function (e) {
            e.preventDefault(); // STOP form from reloading the page

            const formData = $(this).serialize();

            $.post('utils/submit_review.php', formData, function (response) {
                $('#response-message').html(response);
                $('#review-form')[0].reset();
            }).fail(function () {
                $('#response-message').html("<span style='color:red;'>AJAX request failed.</span>");
            });
        });
    });
</script>
