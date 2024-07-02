<?php

# Includes
$jsonResponse = true;
require 'includes/header.php';
require_once 'classes/post.php';
require_once 'classes/action.php';
require_once 'classes/notifications.php';

# Verifies post id
$post = new Post();
$return = $post->getByShortId($GLOBALS['postShortId']);
if($return == null) {
    Action::response(array(
        'error' => 'Post not found!'
    ), 404);
}

# GET method returns liked status
if($_SERVER['REQUEST_METHOD'] == 'GET') {
    Action::response(array(
        'status' => $post->isLikedByUser($_SESSION['user'])
    ));
}

# POST method sets the liked value
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = Action::requestData();
    $likedValue = isset($data['liked']) ? boolval($data['liked']) : '';
    if(!is_bool($likedValue)){
        Action::response(array(
            'error' => 'Invalid liked status given',
        ), 400);
    }

    # Creates notification
    if ($likedValue && !$post->isLocked) {
        $notifications = new Notification();
        $notifications->userId = $post->user->id;
        $notifications->aboutUserId = $_SESSION['user']->id;
        $notifications->aboutId = $post->id;
        $notifications->type = 0;

        if (!$notifications->checkAlreadyExists() && $post->user->likeNotifications) {
            $notifications->create();
        }       
    }
    
    if (!$post->isLocked) {
        $post->userLiked($_SESSION['user'], $likedValue);
    }
    Action::response(array(
        'status' => $likedValue
    ));
}

Action::response(array(
    'error' => 'invalid method!'
), 400);
