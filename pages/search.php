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
        <input class="input" type="text" placeholder="Search for users" name="q">
        <input class="button is-success" type="submit" value="search">
    </form>
    <div class="container is-fullheight post-holder">
        <script class="feed-settings" type="application/json">{"type": "getUsers", "data": "<?php echo $searchValue ?>"}</script>
    </div>
</section>


<?php
# Include footer
require 'includes/footer.php';