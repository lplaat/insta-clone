<?php

# Includes
require 'includes/header.php';
require_once 'classes/user.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $searchValue = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : "";
}

?>
<h1 class="mt-4">Search</h1>

<section class="section is-fullheight">
    <form method="GET">
        <div class="control has-icons-right mb-4">
            <input class="input" type="text" placeholder="Search for users" name="q">
            <button type="submit" class="button icon is-right pointer-events">
                <i class="fa-solid fa-magnifying-glass"></i>
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