<?php

# Includes
require 'includes/header.php';
require_once 'classes/post.php';

$post = new Post();
$return = $post->getByShortId($GLOBALS['postShortId']);
if($return == null) {
    header("location: /"); 
}

?>

<section class="section is-fullheight">
    <div class="container is-fullheight">
        <?php require 'components/post.php'?>
    </div>
</section>

<?php
# Include footer
require 'includes/footer.php';
