<?php

require_once "classes/session.php";

class Authentication {

    public static function verify() {
        # Verifies that the person has the correct permissions to view the page that is requested
        $loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false; 
        $file = str_replace(dirname($_SERVER['PHP_SELF']).'/','',$_SERVER['PHP_SELF']); 

        if ($file != 'login.php' AND $file != 'signup.php') { 
            if (!$loggedIn) { 
                Session::end();
                header("location: /login"); 
                exit;
            } 
        }
    }

    public static function initSession() {
        # Inits some values inside the session
        if (!isset($_SESSION['loggedIn'])) {
            $_SESSION['loggedIn'] = false;
            $_SESSION['user'] = null;
        }
    }

    public static function userLogins($user) {
        # Sets the session values when a user logins
        $_SESSION['loggedIn'] = true;
        $_SESSION['user'] = $user;
    }
}
