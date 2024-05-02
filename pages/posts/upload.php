<?php

# Includes
require 'includes/header.php';
require_once 'classes/user.php';
require_once 'classes/post.php';

$status = '';
if($_SERVER['REQUEST_METHOD'] == "POST") {
    $text = isset($_POST["caption"]) ? $_POST["caption"] : "";

    $post = new Post();
    $post->text = htmlspecialchars($text, ENT_QUOTES);

    $user = New User($_SESSION['userId']);
    $post->user = $user;
    $post->upload();

    header('refresh: 1; url=/post/' . $post->shortId); 

    $status = 'success';
}

?>

<section class="section">
    <div class="container">
        <div class="box">
            <h1 class="title has-text-centered">Upload a Post</h1>
            <div class="file is-centered is-boxed drop-area is-fullwidth" id="dropArea">
                <label class="file-label">
                    <input class="file-input" type="file" name="files[]" multiple id="fileInput">
                    <span class="file-cta has-text-centered">
                        <span class="file-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 512 512"><path fill="#5a5a5a" d="M288 109.3V352c0 17.7-14.3 32-32 32s-32-14.3-32-32V109.3l-73.4 73.4c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l128-128c12.5-12.5 32.8-12.5 45.3 0l128 128c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L288 109.3zM64 352H192c0 35.3 28.7 64 64 64s64-28.7 64-64H448c35.3 0 64 28.7 64 64v32c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V416c0-35.3 28.7-64 64-64zM432 456a24 24 0 1 0 0-48 24 24 0 1 0 0 48z"/></svg>
                        </span>
                        <span class="file-label">
                            Drag and drop images here or click to select
                        </span>
                    </span>
                </label>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="field">
                    <label class="label">Description</label>
                    <div class="control">
                        <textarea class="textarea has-fixed-size" name="caption" placeholder="Enter description"></textarea>
                    </div>
                </div>

                <?php
                    if($status != '') echo '<div class="field">';
                        if($status == "success") echo '<p class="subtitle is-6 has-text-centered green-text">The post was successfully created!</p>';
                        if($status == "error") echo '<p class="subtitle is-6 has-text-centered red-text">A error acquired!</p>';
                    if($status != '') echo '</div>';
                ?>

                <div class="field">
                    <div class="control">
                        <button class="button is-primary" type="submit">Submit</button>
                        <a href="/" class="button" type="submit">cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
# Include footer
require 'includes/footer.php';
