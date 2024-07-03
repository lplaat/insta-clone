<?php

# Includes
require 'includes/header.php';
require_once 'classes/post.php';

$mainPost = new Post();
$return = $mainPost->getByShortId($GLOBALS['postShortId']);

$adminSettings = isset($_GET['adminSettings']) ?  $_GET['adminSettings'] : false;

$unlockPost = isset($_POST['unlockPost']) ? $_POST['unlockPost'] : false;
$locked = isset($_POST["locked"]) ? $_POST["locked"] : false;
$deleted = isset($_POST['deleted']) ? $_POST['deleted'] : false;
$undeleted = isset($_POST['undeleted']) ? $_POST['undeleted'] : false;

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
if ((!$_SESSION['user']->isAdmin && $adminSettings) && $_SESSION['user']->name != $mainPost->user->name) {
    header("location: /");
}

if ($_SESSION['user']->checkBlocked($mainPost->user)) {
    header("location: /");
}

# set POST request to GET request
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    header("location: /post/$mainPost->shortId");
}
?>

<section class="section pb-0 mobile-visible">
    <h1 class="mt-4"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="icon back-button click-cursor is-light-dark" onclick="goBack()"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg>Post</h1>
</section>

<section class="section is-fullheight">
    <?php
        if ($adminSettings) {
            echo "<form method=\"POST\" class=\"has-text-centered\">
            <div>";
            if (!$mainPost->isDeleted) {
                echo "<button type=\"submit\" name=\"deleted\"class=\"button is-danger mr-4\" value=\"true\">Delete post</button>";
            } else if ($mainPost->isDeleted && $_SESSION['user']->isAdmin) {
                echo "<button type=\"submit\" name=\"undeleted\"class=\"button is-danger mr-4\" value=\"true\">Undelete post</button>";
            } 
            
            if (!$mainPost->isLocked && $_SESSION['user']->isAdmin) {
                echo "<button type=\"submit\" name=\"locked\"class=\"button is-warning\" value=\"true\">Lock post</button>";
            } else if (!$commentComment && $_SESSION['user']->isAdmin) {
                echo "<button type=\"submit\" name=\"unlockPost\" class=\"button is-warning\" value=\"true\">Unlock post</button>";
            }
            
            echo "</div>
            </form>";
        }
    ?>
    <div class="container is-fullheight">
        <?php 
            if($mainPost->headId != null) {
                $post = new Post($mainPost->headId);
                require 'components/post.php';
            }

            $post = $mainPost;
            require 'components/post.php';

            $textareaDiv = '
                <div class="container mb-5">
                    <textarea class="comment-field textarea has-fixed-size replaceMe" id="content" class="textarea comment" placeholder="Your comment" maxlength="250"></textarea>
                    <button class="button right-side bottom-16 is-primary" onclick="commentIt(event, \'' . $post->shortId . '\')">Comment</button>
                </div>';

            $scriptDiv = '
                <div class="container is-fullheight post-holder">
                    <script class="feed-settings" type="application/json">{"type": "comments", "data": "' . $post->shortId . '", "error": false}</script>
                </div>';

            if ($mainPost->headId == null) {
                if ((!$mainPost->isLocked || !$_SESSION['user']->isLocked) && !($_SESSION['user']->checkBlocked($mainPost->user)) || $_SESSION['user']->isAdmin) {
                    if (!$commentComment) {
                        echo $textareaDiv;
                    }
                }
                echo $scriptDiv;
            } else {
                echo $scriptDiv;
                if ((!$mainPost->isLocked || !$_SESSION['user']->isLocked) && !($_SESSION['user']->checkBlocked($mainPost->user)) || $_SESSION['user']->isAdmin && !$commentComment) {
                    echo str_replace('replaceMe', 'comment-comment-margin', $textareaDiv);
                } 
            }
        ?>
    </div>
</section>

<?php
# Include footer
require 'includes/footer.php';
