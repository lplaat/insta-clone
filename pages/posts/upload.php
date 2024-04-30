<?php

# Includes
require 'includes/header.php';
require_once 'classes/user.php';
require_once 'classes/post.php';

$status = '';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $text = isset($_POST["caption"]) ? $_POST["caption"] : "";

    $post = new Post();
    $post->text = $text;

    $user = New User();
    $user->getById($_SESSION['userId']);
    $post->user = $user;
    $post->upload();

    header('refresh: 2; url=/'); 

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
                            <i class="fas fa-upload"></i>
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
                    if ($status != '') echo '<div class="field">';
                        if ($status == "success") echo '<p class="subtitle is-6 has-text-centered green-text">The post was successfully created!</p>';
                        if ($status == "error") echo '<p class="subtitle is-6 has-text-centered red-text">A error acquired!</p>';
                    if ($status != '') echo '</div>';
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
