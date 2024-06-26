<?php

# Includes
require 'includes/header.php';
?>

<section class="section pb-0 home-text">
    <div class="container">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="icon click-cursor is-light-dark sidebar-button" onclick="opensidebar()"><path d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"/></svg>
        <h1 class="home-text is-hidden-mobile">Home</h1>
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
