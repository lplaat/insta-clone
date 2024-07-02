<?php

require_once "classes/session.php";
require_once "classes/user.php";

class Authentication {

    public static function verify() {
        # Verifies that the person has the correct permissions to view the page that is requested
        $loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false; 
        if($GLOBALS['path'] != '/login' AND $GLOBALS['path'] != '/signup') { 
            if(!$loggedIn) { 
                Session::end();
                header("location: /login"); 
                exit;
            } 
        }

        # Check if user is deleted or locked
        if ($_SESSION['user'] != null) {
            $user = new User($_SESSION['user']->id);
            if ($user->isLocked) {
                $_SESSION['user'] = $user;
            }
            if ($user->isDeleted) {
                Session::end();
                header("location: /login"); 
                exit;
            }
        }
    }

    public static function initSession() {
        # Inits some values inside the session
        if(!isset($_SESSION['loggedIn'])) {
            $_SESSION['loggedIn'] = false;
            $_SESSION['user'] = null;
        }
    }

    public static function userLogins($user) {
        # Sets the session values when a user logins
        $_SESSION['loggedIn'] = true;
        $_SESSION['user'] = new User($user->id);
    }
}
