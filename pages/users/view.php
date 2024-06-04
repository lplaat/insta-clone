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
?>

<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-half">
                <div class="card">
                    <div class="card-content">
                        <div class="content min-full-width">
                            <div class="media">
                                <div class="media-left">
                                    <figure class="image is-48x48 mr-1">
                                        <?php
                                            if($user->avatarPath == null) {
                                                echo '<img class="is-rounded" src="/static/images/avatar-default.svg" alt="Profile image">';
                                            } else {
                                                echo '<img class="is-rounded" src="/media/' . $user->avatarPath . '" alt="Profile image">';
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
                                            if(!$isFollowing) echo '<button class="button follow-button is-success" onclick="followUserButton(\'' . $user->name . '\')"><b>follow</b></button>';
                                            if($isFollowing) echo '<button class="button follow-button is-danger" onclick="followUserButton(\'' . $user->name . '\')"><b>unfollow</b></button>';
                                        }else {
                                            echo '<button class="button follow-button edit-profile-button" onclick="editProfile()"><b>Edit Profile</b></button>';
                                            echo '<button class="button follow-button is-success update-profile-button display-none" onclick="uploadProfileEdit(\'' . $user->name .'\')"><b>Update</b></button>';
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
