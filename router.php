<?php

# Error routes
function pageNotFound() {
    header("HTTP/1.0 404 Not Found");
    include 'pages/errors/404.php';
    exit();
}

# Routes
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode("/", $path);
$GLOBALS['path'] = $path;

# Home page route
if($path == '/') {
    include 'pages/index.php';
    exit();
} 

# Login route
if($path == '/login') {
    include 'pages/users/login.php';
    exit();
} 

# Signup route
if($path == '/signup') {
    include 'pages/users/signup.php';
    exit();
}

# Feed action route
if($path == '/feed') {
    include 'actions/feed.php';
    exit();
}

# For multiple part routes
if(count($parts) < 2) {
    pageNotFound();
}

# Notifications routes
if($path == '/notifications') {
    include 'pages/users/notifications.php';
    exit();
}

# User routes
if($parts[1] == 'user') {
    if(count($parts) >= 3 AND $parts[2] != '') {
        # User specific actions and pages
        $GLOBALS['username'] = $parts[2];

        if($parts[3] == 'follow'){
            # Post like action
            include 'actions/users/follow.php';
            exit();
        }

        pageNotFound();
    }

    pageNotFound();
}


# Post routes
if($parts[1] == 'post') {
    if($parts[2] == 'upload'){
        # Post upload page
        include 'pages/posts/upload.php';
        exit();
    } 

    if(count($parts) >= 3 AND $parts[2] != '') {
        # Post specific actions and pages
        $GLOBALS['postShortId'] = $parts[2];

        if($parts[3] == 'like'){
            # Post like action
            include 'actions/posts/like.php';
            exit();
        }

        # View post
        include 'pages/posts/view.php';
        exit();
    }
}

# Static routes
if($path == '/static/styles.css') {
    header("Content-Type: text/css");
    include 'static/styles.css';
    exit();
}

if($path == '/static/script.js') {
    header("Content-Type: text/javascript");
    include 'static/script.js';
    exit();
}

pageNotFound();
