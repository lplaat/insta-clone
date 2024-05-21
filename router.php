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

# Settings page
if($path == '/settings') {
    include 'pages/users/settings.php';
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
            # User follow action
            include 'actions/users/follow.php';
            exit();
        }

        # User view profile page
        include 'pages/users/view.php';
        exit();
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

# Static routes and media routes
$mimeTypes = [
    'static' => [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'svg' => 'image/svg+xml'
    ],
    'media' => [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif'
    ]
];

# Check if the requested path starts with '/static/' or '/media/' and extract the directory and file extension
if (preg_match('/^\/(static|media)\/(.+)\.(css|js|svg|jpg|jpeg|png|gif)$/', $path, $matches)) {
    $directory = $matches[1];
    $extension = $matches[3];

    # Check if the directory is supported and the file extension is valid
    if (isset($mimeTypes[$directory]) && array_key_exists($extension, $mimeTypes[$directory])) {
        $file = __DIR__ . "/{$directory}/{$matches[2]}.{$extension}";
        if (file_exists($file)) {
            # Set the appropriate MIME type and output the file content
            header("Content-Type: {$mimeTypes[$directory][$extension]}");
            readfile($file);
            exit();
        }
    }
}

pageNotFound();
