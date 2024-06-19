<?php

# Includes
$jsonResponse = true;
require 'includes/header.php';
require_once 'classes/user.php';
require_once 'classes/action.php';
require_once 'classes/feed.php';

# Check if there is a token given
$token = isset($_GET['token']) ? $_GET['token'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'any';
$data = isset($_GET['data']) ? $_GET['data'] : '';
if(!is_string($token) || !is_string($type) || !is_string($data)) {
    Action::response(array(
        'error' => 'Invalid arguments!',
    ), 400);
}

# Load feed with the token or create a new one
$feed = Feed::get($token);
if($feed == null) {
    $feed = new Feed($type, $data);
}

# Returns post given by the feed class
Action::response(array(
    'posts' => $feed->getItems(),
    'token' => $feed->token
), 200);