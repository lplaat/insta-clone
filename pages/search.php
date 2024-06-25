<?php

# Includes
require 'includes/header.php';
require_once 'classes/user.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $searchValue = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : "";
}

?>
<section class="section pb-0">
    <h1 class="mt-4"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="icon back-button click-cursor" onclick="goBack()"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg>Search</h1>
</section>

<section class="section is-fullheight">
    <form method="GET">
        <div class="control has-icons-right mb-4">
            <input class="input" type="text" placeholder="Search for users" name="q">
            <button type="submit" class="button icon is-right pointer-events">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>
            </button>
        </div>
    </form>
    <div class="container is-fullheight post-holder">
        <script class="feed-settings" type="application/json">{"type": "getUsers", "data": "<?php echo $searchValue ?>"}</script>
    </div>
</section>


<?php
# Include footer
require 'includes/footer.php';