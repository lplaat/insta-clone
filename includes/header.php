<?php

# Including classes
require_once "classes/database.php";
require_once "classes/session.php";
require_once "classes/debug.php";
require_once "classes/authentication.php";

# Start session, database and maybe error logging
$GLOBALS['conn'] = Database::connect();
Session::start();
Authentication::verify();


# Theme loader
if(!isset($_SESSION['user'])) {
    $theme = 0;
}else {
    $theme = $_SESSION['user']->theme;
}
if($theme == 0) $theme = '';
if($theme == 1) $theme = 'dark';
if($theme == 2) $theme = 'light';

# Disable the header if needed
if(!isset($jsonResponse)) {?>
    <!DOCTYPE html>
    <html lang="en" data-theme="<?php echo $theme ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <script defer src="/static/script.js"></script>
        <script defer src="/static/bulma-carousel.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
        <link rel="stylesheet" href="/static/styles/bulma-carousel.min.css">
        <link rel="stylesheet" href="/static/styles/bulma-switch.min.css">
        <link rel="stylesheet" href="/static/styles/app.css">
        <title>InstaClone</title>
    </head>
    <body>

    <?php
    if(!isset($emptyMain)) {
        include "components/sidebar.php";
    }
} else {
    header("Content-Type: application/json");
}