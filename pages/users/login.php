<?php

# Includes
$emptyMain = true;
require 'includes/header.php';
require_once 'classes/user.php';
require_once 'classes/session.php';

$status = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') { 
    # Login process
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $user = new User();
    $success = $user->login($username, $password);
    if($success) {
        $status = 'success';
        header('refresh: 1; url=/'); 
    } else {
        $status = 'error';
        Session::end();
    }
} else {
    if(isset($_GET['logout'])) {
        # Logout process
        $status = 'logout';
        header("Refresh: 2; url=/login");
        Session::end();
    }
}

?>

<section class="section is-fullheight">
    <div class="container is-fullheight">
        <div class="columns is-centered is-vcentered is-fullheight">
            <div class="column is-half">
                <div class="box login-container">
                    <h1 class="title has-text-centered">Login to InstaClone</h1>
                    <form method="POST">
                        <div class="field">
                            <label class="label">Username</label>
                            <div class="control">
                                <input class="input" name="username" type="text" placeholder="Your username">
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Password</label>
                            <div class="control">
                                <input class="input" name="password" type="password" placeholder="Your password">
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                                <input class="button is-primary is-fullwidth" type="submit" value="login">
                            </div>
                        </div>

                        <div class="field">
                            <?php
                                if($status == "success") echo '<p class="subtitle is-6 has-text-centered green-text">You\'re successfully logged in!</p>';
                                if($status == "error") echo '<p class="subtitle is-6 has-text-centered red-text">You\'re username or password is wrong!</p>';
                                if($status == "logout") echo '<p class="subtitle is-6 has-text-centered red-text">You\'re now logging out!</p>';
                            ?>
                        </div> 
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
# Include footer
require './includes/footer.php';
