<?php
require_once "classes/authentication.php";
require_once "classes/feed.php";

class Session {

    public static function start() {
        # Start session
        session_start();

        # Inits the auth arrays inside the session
        Authentication::initSession();
        Feed::initFeedSession();
    }

    public static function end() {
        # Destroy session
        session_unset();
        session_destroy();
    }
}
