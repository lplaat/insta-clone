<?php

# Includes
require 'includes/header.php';
require_once 'classes/user.php';

# Load in user
$user = new User();
$return = $user->getByName($GLOBALS['username']);
if($return == null) {
    header("location: /"); 
}

if ($user->isDeleted && !$_SESSION['user']->isAdmin) {
    header("location: /");
    exit();
}

$adminSettings = isset($_GET['adminSettings']) ? $_GET['adminSettings'] : false;

$locked = isset($_POST['locked']) ? $_POST['locked'] : false;
$unlocked = isset($_POST['unlocked']) ? $_POST['unlocked']  : false;
$deleted = isset($_POST['deleted']) ? $_POST['deleted'] : false;
$undeleted = isset($_POST['undeleted']) ? $_POST['undeleted'] : false;
$blocked = isset($_POST['blocked']) ? $_POST['blocked'] : false;
$unblocked = isset($_POST['unblocked']) ? $_POST['unblocked'] : false;


if ($locked) {
    $user->isLocked = true;
    $user->setLocked();
} else if ($unlocked) {
    $user->isLocked = false;
    $user->setLocked();
} else if ($deleted) {
    $user->isDeleted = true;
    $user->setDeleted();
} else if ($undeleted) {
    $user->isDeleted = false;
    $user->setDeleted();
} else if ($blocked) {
    $blockedStatus = true;
    $user->setBlocked($blockedStatus);
} else if ($unblocked) {
    $blockedStatus = false;
    $user->setBlocked($blockedStatus);
}

# Validates if user is admin
if ((!$_SESSION['user']->isAdmin && $adminSettings) && $_SESSION['user']->name == $user->name) {
    header("location: /");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    header("location: /user/$user->name");
}
?>

<section class="section pb-0 mobile-visible">
    <h1 class="mt-4"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="icon back-button click-cursor is-light-dark" onclick="goBack()"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg>User</h1>
</section>

<section class="section">
    <?php
        if ($adminSettings) {
            echo "<form method=\"POST\" class=\"has-text-centered\">
            <div>";
            if (!$user->isDeleted && $_SESSION['user']->isAdmin) {
                echo "<button type=\"submit\" name=\"deleted\"class=\"button is-danger mr-4\" value=\"true\">Delete user</button>";
            } else if ($user->isDeleted && $_SESSION['user']->isAdmin) {
                echo "<button type=\"submit\" name=\"undeleted\"class=\"button is-danger mr-4\" value=\"true\">Undelete user</button>";
            }
            if (!$user->isLocked && $_SESSION['user']->isAdmin) {
                echo "<button type=\"submit\" name=\"locked\"class=\"button is-warning mr-4\" value=\"true\">Lock user</button>";
            } else if ($user->isLocked && $_SESSION['user']->isAdmin) {
                echo "<button type=\"submit\" name=\"unlocked\" class=\"button is-warning mr-4\" value=\"true\">Unlock user</button>";
            }
            if (!$user->checkBlocked($_SESSION['user'])) {
                echo "<button type=\"submit\" name=\"blocked\"class=\"button is-danger\" value=\"true\">Block user</button>";
            } else {
                echo "<button type=\"submit\" name=\"unblocked\"class=\"button is-danger\" value=\"true\">Unblock user</button>";
            }
            
            echo "</div>
            </form>";
        }
    ?>
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-half">
                <div class="card">
                    <div class="card-content">
                        <div class="content min-full-width">
                            <div class="media">
                                <div class="media-left">
                                    <div class="lock-icon">
                                    <?php
                                        if ($user->isLocked) {
                                            echo "<svg class=\"image is-16x16 is-light-dark\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path d=\"M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z\"/></svg>";
                                        }
                                        if ($user->isDeleted && $_SESSION['user']->isAdmin) {
                                            echo "<svg class=\"image is-16x16 is-light-dark\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 384 512\"><path d=\"M376.6 84.5c11.3-13.6 9.5-33.8-4.1-45.1s-33.8-9.5-45.1 4.1L192 206 56.6 43.5C45.3 29.9 25.1 28.1 11.5 39.4S-3.9 70.9 7.4 84.5L150.3 256 7.4 427.5c-11.3 13.6-9.5 33.8 4.1 45.1s33.8 9.5 45.1-4.1L192 306 327.4 468.5c11.3 13.6 31.5 15.4 45.1 4.1s15.4-31.5 4.1-45.1L233.7 256 376.6 84.5z\"/></svg>";
                                        }
                                    ?>  
                                    </div>
                                    <figure class="image is-48x48 mr-1">
                                        <?php
                                            if($user->avatarPath == null) {
                                                echo '<img class="is-rounded max-sizes-image" src="/static/images/avatar-default.svg" alt="Profile image">';
                                            } else {
                                                echo '<img class="is-rounded max-sizes-image" src="/media/' . $user->avatarPath . '" alt="Profile image">';
                                            }
                                        ?>
                                    </figure>
                                </div>
                                <div class="media-content">
                                    <p class="title is-4"><?php echo $user->realName; ?></p>
                                    <p class="subtitle is-6">@<?php echo $user->name; ?></p>
                                </div>
                                <div class="right-side">
                                    <?php
                                        if($_SESSION['user']->name != $user->name){
                                            $isFollowing = $user->isFollowedBy($_SESSION['user']);
                                            if($user->checkExistsFollowRequest($_SESSION['user'])) {
                                                echo '<button class="button follow-button" onclick="followUserButton(\'' . $user->name . '\',' . boolval($user->private) . ')"><b>requested</b></button>';
                                            } else if (!$isFollowing && $user->followRequests) {
                                                echo '<button class="button follow-button is-success" onclick="followUserButton(\'' . $user->name . '\',' . boolval($user->private).  ')"><b>follow</b></button>';
                                            } else if($isFollowing) {
                                                echo '<button class="button follow-button is-danger" onclick="followUserButton(\'' . $user->name . '\',' . boolval($user->private) . ')"><b>unfollow</b></button>';  
                                            } else if (!$user->followRequests) {
                                                echo '<button class="button follow-button" disabled>cannot follow user<button>';
                                            }
                                        }else {
                                            echo '<button class="button follow-button edit-profile-button" onclick="editProfile()"><b>Edit Profile</b></button>';
                                            echo '<button class="button follow-button is-success update-profile-button display-none" onclick="uploadProfileEdit(\'' . $user->name .'\')"><b>Update</b></button>';
                                        }

                                        
                                        if ($_SESSION['user']->isAdmin) {
                                            echo "<a class=\"icon\" href=\"/user/$user->name?adminSettings=true\">
                                                <svg class=\"image is-16x16 is-light-dark icon-transform\"xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 128 512\"><path d=\"M64 360a56 56 0 1 0 0 112 56 56 0 1 0 0-112zm0-160a56 56 0 1 0 0 112 56 56 0 1 0 0-112zM120 96A56 56 0 1 0 8 96a56 56 0 1 0 112 0z\"/></svg>
                                                </a>";
                                        }
                                    ?>
                                </div>
                            </div>
                            <b class="user-biography">
                                <?php
                                    if($user->biography != null){
                                        echo '<p>' . $user->biography . '</p>';
                                    }
                                ?>
                            </b>
                            <br>
                            <nav class="level is-mobile has-text-centered">
                                <div class="level-item has-text-centered">
                                    <div>
                                        <p class="heading">Followers</p>
                                        <p class="title follower-count"><?php echo $user->followers; ?></p>
                                    </div>
                                </div>
                                <div class="level-item has-text-centered">
                                    <div>
                                        <p class="heading">Following</p>
                                        <p class="title"><?php echo $user->following; ?></p>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section is-fullheight">
    <?php if($user->viewingRights){ ?>
            <div class="container is-fullheight post-holder">
                <script class="feed-settings" type="application/json">{"type": "user", "data": "<?php echo $user->name; ?>"}</script>
            </div>
        </section>
    <?php } else { ?>
            <div class="container is-fullheight has-text-centered">
                <h1>This profile is set to private!</h1>
            </div>
    <?php } ?>
</section>

<?php
# Include footer
require 'includes/footer.php';
