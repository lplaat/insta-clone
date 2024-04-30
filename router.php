<?php

// Routes
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$GLOBALS['path'] = $path;

if ($path == '/') {
    include 'pages/index.php';
} elseif ($path == '/login') {
    include 'pages/users/login.php';
} elseif ($path == '/signup') {
    include 'pages/users/signup.php';
} elseif ($path == '/post/upload') {
    include 'pages/posts/upload.php';
} else {
    header("HTTP/1.0 404 Not Found");
    include 'pages/errors/404.php';
    exit();
}
