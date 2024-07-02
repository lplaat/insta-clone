<?php
if ($notification->type == 0 || $notification->type == 4) {
    if($notification->aboutPost->headId == null) {
        $name = "post";
    } else {
        $name = "comment";
    }
}
?>
<div class="card post-item mb-5" id="<?php echo $notification->shortId ?>">
    <div class="box">
        <article class="media">
            <div class="media-left">
                <figure class="image is-48x48 mr-1 ml-0 click-cursor" onclick="goTo('user/<?php echo $notification->aboutUser->name;?>')">
                    <?php
                        if($notification->aboutUser->avatarPath == null) {
                            echo '<img class="is-rounded" src="/static/images/avatar-default.svg" alt="Profile image">';
                        } else {
                            echo '<img class="is-rounded" src="/media/' . $notification->aboutUser->avatarPath . '" alt="Profile image">';
                        }
                    ?>
                </figure>
            </div>
            <div class="media-content">
                <p class="title is-5">
                    <a class="" href="user/<?php echo $notification->aboutUser->name?>"><?php echo $notification->aboutUser->name;?></a>
                    <?php
                        if ($notification->type == 0) {
                            echo "has liked your $name";
                        } else if ($notification->type == 1) {
                            echo "is now following you";
                        } else if ($notification->type == 2) {
                            echo "has requested to follow you";
                        } else if ($notification->type == 3) {
                            echo "accepted your follow request";
                        } else if ($notification->type == 4) {
                            echo "has commented on you're post";
                        }
                    ?>
                </p>
                <p class="subtitle is-6">
                    <span class="has-text-grey-light">
                        <b><?php echo Tools::humanReadableDate($notification->createdAt); ?></b>
                    </span>
                </p>
            </div>
            <div class="ride-side top-8">
                <?php
                    if ($notification->type == 0 || $notification->type == 4) {
                        echo "<a class=\"button\" href=/post/" . $notification->aboutPost->shortId . ">View $name</a>";
                    } else if ($notification->type == 2) {
                        echo "<a class=\"button is-success\" onclick=\"acceptFollowButton(event, '" . $notification->aboutUser->name . "')\">Accept</a>";
                        echo "<a class=\"button is-danger ml-1\" onclick=\"declineFollowButton(event, '". $notification->aboutUser->name ."')\">Decline</a>";
                    }
                ?>
            </div>
        </article>
    </div>
</div>