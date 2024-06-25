<?php

# Includes
require 'includes/header.php';
require_once 'classes/post.php';

$mainPost = new Post();
$return = $mainPost->getByShortId($GLOBALS['postShortId']);
if($return == null) {
    header("location: /"); 
}

if($mainPost->headId != null) {
    $headPost = new Post($mainPost->headId);
    if($headPost->headId != null){
        header("location: /post/" . $headPost->shortId); 
    }
}
?>

<section class="section pb-0 mobile-visible">
    <h1 class="mt-4"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="icon back-button click-cursor" onclick="goBack()"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg>Post</h1>
</section>

<section class="section is-fullheight">
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
                echo $textareaDiv;
                echo $scriptDiv;
            } else {
                echo $scriptDiv;
                echo str_replace('replaceMe', 'comment-comment-margin', $textareaDiv);
            }
        ?>
    </div>
</section>

<?php
# Include footer
require 'includes/footer.php';
