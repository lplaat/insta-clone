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
        $mainPost->isLocked = true;
        $headPost->isLocked = true;
        $mainPost->setLocked();
        $headPost->setLocked();
        header("location: /post/$mainPost->shortId");
    } else if ($unlockPost) {
        $mainPost->isLocked = false;
        $headPost->isLocked = false;
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
    <?php
        if ($adminSettings) {
            echo "<form method=\"POST\" class=\"has-text-centered\">
            <div>";
            if (!$mainPost->isDeleted) {
                echo "<button type=\"submit\" name=\"deleted\"class=\"button is-danger mr-4\" value=\"true\">Delete post</button>";
            } else {
                echo "<button type=\"submit\" name=\"undeleted\"class=\"button is-danger mr-4\" value=\"true\">Undelete post</button>";
            } 
            
            if (!$mainPost->isLocked) {
                echo "<button type=\"submit\" name=\"locked\"class=\"button is-warning\" value=\"true\">Lock post</button>";
            } else if (!$commentComment) {
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
                if (!$mainPost->isLocked || $_SESSION['user']->isAdmin) {
                    if (!$commentComment) {
                        echo $textareaDiv;
                    }
                }
                echo $scriptDiv;
            } else {
                echo $scriptDiv;
                if ((!$mainPost->isLocked || $_SESSION['user']->isAdmin) && !$commentComment) {
                    echo str_replace('replaceMe', 'comment-comment-margin', $textareaDiv);
                } 
            }
        ?>
    </div>
</section>

<?php
# Include footer
require 'includes/footer.php';
