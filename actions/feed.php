<?php

# Includes
$jsonResponse = true;
require 'includes/header.php';
require_once 'classes/user.php';
require_once 'classes/action.php';
require_once 'classes/feed.php';

# Check if there is a token given
$token = isset($_GET['token']) ? $_GET['token'] : '';
if(!is_string($token)) {
    Action::response(array(
        'error' => 'Invalid token given!',
    ), 400);
}

# Load feed with the token or create a new one
$feed = Feed::get($token);
if($feed == null) {
    $feed = new Feed();
}

# Returns post given by the feed class
Action::response(array(
    'posts' => $feed->getPosts(),
    'token' => $feed->token
), 200);