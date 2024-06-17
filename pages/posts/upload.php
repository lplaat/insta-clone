<?php

# Includes
require 'includes/header.php';
require_once 'classes/user.php';
require_once 'classes/post.php';
require_once 'classes/media.php';

$status = '';
if($_SERVER['REQUEST_METHOD'] == "POST") {
    $text = isset($_POST["caption"]) ? $_POST["caption"] : "";

    $post = new Post();
    $post->text = htmlspecialchars($text, ENT_QUOTES);

    $post->user = $_SESSION['user'];
    $post->upload();

    foreach ($_FILES["files"]["error"] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
            $image = new Media(array(
                'tmp_name' => $_FILES["files"]["tmp_name"][$key],
                'name' => $_FILES["files"]["name"][$key],
            ));

            $status = $image->saveImage();
            if($status) {
                $post->linkImage($image);
            }
        }
    }

    // header('refresh: 1; url=/post/' . $post->shortId); 

    $status = 'success';
}

?>

<section class="section">
    <div class="container">
        <div class="box">
            <h1 class="title has-text-centered">Upload a Post</h1>

            <form method="post" enctype="multipart/form-data" class="postUpload">
                <div class="field">
                    <div class="control">
                        <textarea class="textarea has-fixed-size" id="postCaption" name="caption" placeholder="Enter you're message"></textarea>
                    </div>
                </div>

                <input type="file" name="files[]" accept="image/*" multiple id="fileInputs" onchange="handleInputFileChange(event)" hidden>
                <div class="columns mt-1">
                    <div class="image-previews"></div>
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

                        <div class="right-side top-0">
                            <a class="button upload-image-button" onclick="openFileInputs()">Upload Image</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
# Include footer
require 'includes/footer.php';
