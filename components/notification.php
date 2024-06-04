<div class="card post-item mb-5" id="<?php echo $notification->shortId ?>">
    <script class="post-data" type="application/json"><?php echo json_encode($rawPostData); ?></script>
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
                <p class="title is-5"><a class="" href="user/<?php echo $notification->aboutUser->name?>"><?php echo $notification->aboutUser->name;?></a> has liked your post</p>
                <p class="subtitle is-6">
                    <span class="has-text-grey-light">
                        <b><?php echo Tools::humanReadableDate($notification->createdAt); ?></b>
                    </span>
                </p>
            </div>
            <div class="ride-side top-8">
                <a class="button" href="/post/<?php echo $notification->aboutPost->shortId?>">View post</a>
            </div>
        </article>
    </div>
</div>