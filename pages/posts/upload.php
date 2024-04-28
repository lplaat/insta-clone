<?php

# Includes
require './../../includes/header.php';
require_once 'classes/user.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $text = isset($_POST["caption"]) ? $_POST["caption"] : "";

    $post = new Post();
    $post->text = $text;

    $user = New User();
    $user->getById($_SESSION['userId']);
    $post->user = $user;
    $post->upload();
}
?>

<section class="section">
  <div class="container">
    <h1 class="title">Upload a Post</h1>
    <div class="drop-area" id="dropArea">
      <div>Drag and drop files here</div>
      <div>or</div>
      <label class="button is-primary" for="fileInput">Select Files</label>
      <input id="fileInput" type="file" multiple style="display: none;">
    </div>
    <form action="#" method="post" enctype="multipart/form-data" id="uploadForm">
      <div class="field">
        <label class="label">Description</label>
        <div class="control">
          <textarea class="textarea" name="caption" placeholder="Enter description"></textarea>
        </div>
      </div>
      <div class="field">
        <div class="control">
          <button class="button is-primary" type="submit">Submit</button>
        </div>
      </div>
    </form>
  </div>
</section>  


<?php
# Include footer
require './includes/footer.php';
?>
