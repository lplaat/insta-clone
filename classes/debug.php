<?php

class Debug {
    
    public static function enable() {
        # Enables error logging
        ini_set('display_errors', 1); 
        ini_set('display_startup_errors', 1); 
        error_reporting(E_ALL); 
    }
}
