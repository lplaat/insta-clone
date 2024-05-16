<?php

# Includes
require 'includes/header.php';
require_once 'classes/media.php';

$status = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') { 
    if($_POST['method'] == 'changeAvatar') {
        // Changes the avatar and saves it
        $avatar = new Media($_FILES["avatar"]);
        $valid = $avatar->saveImage();
        if(is_bool($valid) == true) {
            $status = 'successfullySavedAvatar';
        }else {
            $status = $valid;
        }

        $_SESSION['user']->avatarPath = $avatar->path;
        $_SESSION['user']->update();
        header("Refresh: 3; url=/settings");
    }
}
?>

<div class="box user-settings">
    <form action="#" method="post">
        <h1 class="has-text-centered">User Settings</h1>
        <div class="field">
            <label for="username">Username:</label>
            <div class="control">
                <input class="input" type="text" id="username" name="username" value="<?php echo $_SESSION['user']->name; ?>" required>
            </div>
        </div>

        <label for="email">E-mailadress:</label>
        <div class="control">
            <input class="input" type="email" id="email" name="email" value="<?php echo $_SESSION['user']->email; ?>" required>
        </div>

        <div class="field is-horizontal">
            <div class="field-body">
                <div class="field">
                    <label for="privacy" class="label">Privacy:</label>
                    <div class="control is-half">
                        <div class="select is-fullwidth">
                            <select id="privacy" name="privacy">
                                <option value="public">Public</option>
                                <option value="private">Private</option>
                                <option value="friends">Friends only</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label for="notifications" class="label">Notifications:</label>
                    <div class="control is-half">
                        <div class="select is-fullwidth">
                            <select id="notifications" name="notifications">
                                <option value="on">On</option>
                                <option value="off">Off</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input class="has-text-centered button is-fullwidth is-success" type="submit" value="Save Changes">
    </form>
</div>

<div class="box">
    <form action="" method="post" enctype="multipart/form-data">
        <h2 class="has-text-centered">Change avatar</h2>
        
        <input type="input" name="method" value="changeAvatar" hidden>

        <div class="field">
            <div class="file is-centered">
                <label class="file-label">
                    <input class="file-input" type="file" name="avatar" require>
                    <span class="file-cta">
                        <span class="file-icon">
                            <i class="fas fa-upload"></i>
                        </span>
                        <span class="file-label is-fullwidth">
                            Choose a file
                        </span>
                    </span>
                </label>
            </div>
        </div>
        
        <?php
            if($status != '') echo '<div class="field">';
                if($status == "successfullySavedAvatar") echo '<p class="subtitle is-6 has-text-centered green-text">You\'re avatar is saved successfully!</p>';
                if($status == "invalidFileFormat") echo '<p class="subtitle is-6 has-text-centered red-text">This is a invalid file!</p>';
                if($status == "invalidImageFormat") echo '<p class="subtitle is-6 has-text-centered red-text">This image format is not supported!</p>';
                if($status != '') echo '</div>';
        ?>

        <input class="has-text-centered button is-fullwidth is-success" type="submit" value="Save Changes">
    </form>
</div>

<div class="box">
    <h2 class="has-text-centered">Delete Account</h2>
    <button class="button is-danger is-fullwidth">Delete Account</button>
</div>

<?php

# Include footer
require './includes/footer.php';
