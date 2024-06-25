<div class="is-widescreen">
    <div class="columns">
        <aside class="menu is-fullheight sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="icon click-cursor is-light-dark sidebar-button close" onclick="closesidebar()"><path d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"/></svg>
            <a href="/"><b class="title is-3">InstaClone</b></a>
            <div class="menu-list flex-container is-fullheight pt-5">
                <div>
                    <a href="/">Home</a>
                    <a href="/search">Search</a>
                </div>

                <div style="flex:1;"></div>

                <div>
                    <a href="/user/<?php echo $_SESSION['user']->name ?>"><b>Profile</b></a>
                    <a href="/notifications"><b>Notifications</b>
                        <?php
                            $notificationsAmount = $_SESSION['user']->howManyNotifications();
                            if ($notificationsAmount != 0) {
                                $notificationsAmount = $notificationsAmount > 9 ? '9+' : $notificationsAmount;
                                echo "<span class=\"notification-bubble\">$notificationsAmount</span>";
                            }
                        ?>
                    </a>
                    <a href="/settings"><b>Settings</b></a>
                    <a href="/login?logout=true" class="is-fixed-bottom red-text"><b>Logout</b></a>
                </div>
            </div>
        </aside>
        <div class="column content p-5 main-screen head-container">