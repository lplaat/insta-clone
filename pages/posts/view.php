<?php

# Includes
require 'includes/header.php';
require 'classes/post.php';

$post = new Post();
$return = $post->getByShortId($GLOBALS['postShortId']);
if($return == null) {
    header("location: /"); 
}

# Include post
require 'components/post.php';

?>

<?php
# Include footer
require 'includes/footer.php';
