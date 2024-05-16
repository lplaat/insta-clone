<div class="is-widescreen">
    <div class="columns">
        <aside class="menu is-fullheight">
            <a href="/"><b class="title is-3">InstaClone</b></a>
            <div class="menu-list flex-container is-fullheight pt-5">
                <div>
                    <a href="/">Home</a>
                    <a href="/search">Search</a>
                </div>

                <div style="flex:1;"></div>

                <div>
                    <a href="/user/<?php echo $_SESSION['user']->name ?>"><b>Profile</b></a>
                    <a href="/settings"><b>Settings</b></a>

                    <a href="/login?logout=true" class="is-fixed-bottom red-text"><b>Logout</b></a>
                </div>
            </div>
        </aside>
        <div class="column content p-5 main-screen">