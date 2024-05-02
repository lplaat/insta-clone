<?php

# Includes
$emptyMain = true;
require 'includes/header.php';
require_once 'classes/user.php';

$status = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') { 
    # Signup process
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';

    if($password != $confirmPassword) {
        $status = 'passwordNotTheSame';
    } elseif(strlen($password) < 8 && strlen($password) > 18) {
        $status = 'passwordNotValid';
    } elseif(strlen($username) < 3 && strlen($username) > 12) {
        $status = 'usernameNotValid';
    } else {
        $user = New User();
        $user->name = htmlspecialchars($username);
        $user->password = $password;

        $success = $user->create();
        if($success) {
            $status = 'accountCreated';
            header("Refresh: 2; url=/login");
        }else {
            $status = 'usernameTaken';
        }
    }
}

?>

<section class="section is-fullheight">
    <div class="container is-fullheight">
        <div class="columns is-centered is-vcentered is-fullheight">
            <div class="column is-half">
                <div class="box login-container">
                    <h1 class="title has-text-centered">Signup to InstaClone</h1>
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
                            <label class="label">Confirm Password</label>
                            <div class="control">
                                <input class="input" name="confirmPassword" type="password" placeholder="Please confirm your'e password">
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                                <input class="button is-primary is-fullwidth" type="submit" value="Signup">
                            </div>
                        </div>

                        <div class="field">
                            <?php
                                if($status == "accountCreated") echo '<p class="subtitle is-6 has-text-centered green-text">You\'re account is created!</p>';
                                if($status == "passwordNotTheSame") echo '<p class="subtitle is-6 has-text-centered red-text">You\'re passwords are not the same!</p>';
                                if($status == "passwordNotValid") echo '<p class="subtitle is-6 has-text-centered red-text">You\'re passwords needs to be longer than 8 characters and shorter then 18!</p>';
                                if($status == "usernameNotValid") echo '<p class="subtitle is-6 has-text-centered red-text">You\'re username needs to be longer than 3 characters and shorter then 12!</p>';
                                if($status == "usernameTaken") echo '<p class="subtitle is-6 has-text-centered red-text">This username is already taken!</p>';
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
require 'includes/footer.php';
