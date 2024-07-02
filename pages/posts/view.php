<?php

# Includes
require 'includes/header.php';
require_once 'classes/post.php';

$post = new Post();
$return = $post->getByShortId($GLOBALS['postShortId']);
if($return == null) {
    header("location: /"); 
}

$headPost = new Post($mainPost->headId);

$commentComment = false;
if($mainPost->headId != null) {
    if($headPost->headId != null){
        if(!$_SESSION['user']->isAdmin) {
            header("location: /post/" . $headPost->shortId); 
        } else {
            $commentComment = true;
        }
    }
}

# Set post locked
if(!$commentComment) {
    if ($locked) {
        $mainPost->isLocked = 1;
        $headPost->isLocked = 1;
        $mainPost->setLocked();
        $headPost->setLocked();
        header("location: /post/$mainPost->shortId");
    } else if ($unlockPost) {
        $mainPost->isLocked = 0;
        $headPost->isLocked = 0;
        $mainPost->setLocked();
        $headPost->setLocked();
    }
}

# Set post deleted
if ($deleted) {
    $mainPost->isDeleted = true;
    $mainPost->setDeleted();
    header("location: /");
} else if ($undeleted) {
    $mainPost->isDeleted = false;
    $mainPost->setDeleted();
    header("location: /post/$mainPost->shortId");
}

# Validates if user is admin
if (!$_SESSION['user']->isAdmin && $adminSettings) {
    header("location: /");
}

# set POST request to GET request
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    header("location: /post/$mainPost->shortId");
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
