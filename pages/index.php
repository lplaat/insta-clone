<?php

# Includes
require 'includes/header.php';
?>

<section class="section pb-0">
    <div class="container">
        <h1>Home</h1>
        <div class="right-side right-zero top-0">
            <?php
                if ($_SESSION['user']->isLocked) {
                    echo "<a class=\"button\" disabled><b>Create Post</b></a>";
                } else {
                    echo "<a href=\"/post/upload\" class=\"button\"><b>Create Post</b></a>";
                }
            
            ?>
        </div>
    </div>
</section>

<section class="section is-fullheight">
    <div class="container is-fullheight post-holder">
        <script class="feed-settings" type="application/json">{"type": "any"}</script>
    </div>
</section>

<?php
# Include footer
require 'includes/footer.php';
