<?php

# Includes
require 'includes/header.php';
require_once 'classes/media.php';

$status = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') { 
    if($_POST['method'] == 'changeAvatar') {
        # Changes the avatar and saves it
        $avatar = new Media($_FILES["avatar"]);
        $valid = $avatar->saveImage();
        if(is_bool($valid) == true) {
            $status = 'successfullySavedAvatar';
        }else {
            $status = $valid;
        }

        $_SESSION['user']->avatarPath = $avatar->path;
        $_SESSION['user']->update();
        header("Refresh: 1; url=/settings");
    }else if($_POST['method'] == 'userSettings') {
        # Changes the user properties
        $realName = isset($_POST['displayName']) ? htmlspecialchars($_POST['displayName']) : '';
        $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
        $password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';
        $private = isset($_POST['privacy']) ? $_POST['privacy'] : 'public';
        $theme = isset($_POST['theme']) ? $_POST['theme'] : 0;

        $passwordRegex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

        if(strlen($realName) < 3 || strlen($realName) > 24) {
            $status = 'realNameNotValid';
        } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $status = 'emailNotValid';
        }elseif(!preg_match($passwordRegex, $password) && $password != '') {
            $status = 'passwordNotValid';
        }
        
        if($theme == 'system') $theme = 0;
        if($theme == 'dark') $theme = 1;
        if($theme == 'light') $theme = 2;

        if($status == '') {
            $_SESSION['user']->realName = $realName;
            $_SESSION['user']->email = $email;
            $_SESSION['user']->theme = $theme;
            $_SESSION['user']->private = $private == 'private';

            if($password != '') {
                $_SESSION['user']->password = password_hash($password, PASSWORD_DEFAULT);
            }

            $_SESSION['user']->update();
            $status = 'successfullySavedSettings';
            header("Refresh: 1; url=/settings");
        } else {
            header("Refresh: 3; url=/settings");
        }
    } else if ($_POST['method'] == 'notificationSettings') {
        # Changes notification settings
        $likeNotifcations = isset($_POST['likeNotifications']) ? $_POST['likeNotifications'] : '';
        $commentNotifications = isset($_POST['commentNotifications']) ? $_POST['commentNotifications'] : '';
        $followNotifications = isset($_POST['followNotifications']) ? $_POST['followNotifications'] : '';
        $followRequests = isset($_POST['followRequests']) ? $_POST['followRequests'] : '';

        $_SESSION['user']->likeNotifications = $likeNotifcations;
        $_SESSION['user']->commentNotifications = $commentNotifications;
        $_SESSION['user']->followNotifications = $followNotifications;
        $_SESSION['user']->followRequests = $followRequests;

        $_SESSION['user']->update();
        $status = 'successfullySavedNotificationSettings';
        header("Refresh: 1; url=/settings");
    }
}
?>

<div class="box user-settings">
    <form action="#" method="post">
        <h1 class="has-text-centered">User Settings</h1>

        <input type="input" name="method" value="userSettings" hidden>

        <div class="field">
            <label for="displayName" class="label">Display Name:</label>
            <div class="control">
                <input class="input" type="text" id="displayName" name="displayName" value="<?php echo $_SESSION['user']->realName; ?>" required>
            </div>
        </div>

        <label for="email" class="label">E-mailadress:</label>
        <div class="control">
            <input class="input" type="email" id="email" name="email" value="<?php echo $_SESSION['user']->email; ?>" required>
        </div>

        <label for="email" class="label">Password: (leave empty to not change)</label>
        <div class="control">
            <input class="input" type="password" id="password" name="password" value="">
        </div>

        <div class="field is-horizontal">
            <div class="field-body">
                <div class="field">
                    <label for="privacy" class="label">Account Type:</label>
                    <div class="control is-half">
                        <div class="select is-fullwidth">
                            <select id="privacy" name="privacy">
                                <option value="public">Public</option>
                                <option value="private" <?php echo $_SESSION['user']->private ? 'selected' : '' ?>>Private</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label for="theme" class="label">Theme:</label>
                    <div class="control is-half">
                        <div class="select is-fullwidth">
                            <select id="theme" name="theme">
                                <option value="system">System</option>
                                <option value="dark" <?php echo $_SESSION['user']->theme == 1 ? 'selected' : '' ?>>Dark</option>
                                <option value="light" <?php echo $_SESSION['user']->theme == 2 ? 'selected' : '' ?>>Light</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
            if($status != '') echo '<div class="field">';
            if($status == "successfullySavedSettings") echo '<p class="subtitle is-6 has-text-centered green-text">Settings successfully updated!</p>';
            if($status == "emailNotValid") echo '<p class="subtitle is-6 has-text-centered red-text">You\'re email is not valid!</p>';
            if($status == "passwordNotValid") echo '<p class="subtitle is-6 has-text-centered red-text">You\'re passwords needs to be minimal 8 characters, atleast one uppercase, one lowercase, one digit and one special character</p>';
            if($status == "realNameNotValid") echo '<p class="subtitle is-6 has-text-centered red-text">You\'re display name needs to be longer than 3 characters and shorter then 12!</p>';
            if($status != '') echo '</div>';
        ?>

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
                    <span class="file-cta has-text-centered">
                        <span class="file-label">
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
    <form method="POST">
        <h2 class="has-text-centered">Notification settings</h2>

        <input type="input" name="method" value="notificationSettings" hidden>

        <div class="has-text-centered mb-4">
            <div class="columns">
                <div class="column">
                    <div class="field">
                        <input id="likeNotifications" type="checkbox" name="likeNotifications" class="switch" <?php if($_SESSION['user']->likeNotifications) echo 'checked="checked"' ?>>
                        <label for="likeNotifications">like notifications</label>
                    </div> 
                    <div class="field">
                        <input id="commentNotifications" type="checkbox" name="commentNotifications" class="switch" <?php if($_SESSION['user']->commentNotifications) echo 'checked="checked"' ?>>
                        <label for="commentNotifications">comment notifications</label>
                    </div>
                </div>
                <div class="column">
                    <div class="field">
                        <input id="followNotifitcations" type="checkbox" name="followNotifications" class="switch" <?php if($_SESSION['user']->followNotifications) echo 'checked="checked"' ?>>
                        <label for="followNotifitcations">follow notifications</label>
                    </div>
                    <div class="field">
                        <input id="followRequests" type="checkbox" name="followRequests" class="switch" <?php if($_SESSION['user']->followRequests) echo 'checked="checked"' ?>>
                        <label for="followRequests">follow requests</label>
                    </div>
                </div>
            </div>

        </div>
        <?php
            if($status != '') echo '<div class="field">';
            if($status == 'successfullySavedNotificationSettings') echo '<p class="subtitle is-6 has-text-centered green-text">Notification settings are saved successfully!</p>';
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
