<div class="card post-item mb-5" id="<?php echo $user->name?>">
    <div class="box">
        <article class="media">
            <div class="media-left">
                <figure class="image is-48x48 mr-1 ml-0 click-cursor" onclick="goTo('user/<?php echo $user->name?>')">
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
                <a class="title is-4" href="user/<?php echo $user->name?>"><?php echo $user->realName ?></a>
                <p class="subtitle is-6">@<?php echo $user->name ?></p>
            </div>
            <div class="ride-side top-8">
                          
            </div>
        </article>
    </div>
</div>