<?php

# Includes
$jsonResponse = true;
require 'includes/header.php';
require_once 'classes/action.php';
require_once 'classes/post.php';
require_once 'classes/notifications.php';

# Verify method
if($_SERVER['REQUEST_METHOD'] != 'POST') {
    Action::response(array(
        'error' => 'invalid method!'
    ), 400);
}

# Verifies post id
$mainPost = new Post();
$return = $mainPost->getByShortId($GLOBALS['postShortId']);
if($return == null) {
    Action::response(array(
        'error' => 'Post not found!'
    ), 404);
}

# Check if the comment depth is not higher then 2
if($mainPost->headId != null) {
    $headPost = new Post($mainPost->headId);
    if($headPost->headId != null){
        Action::response(array(
            'error' => 'reached comment depth of 2!'
        ), 400);
    } else {
        $headPost->updateCommentCount(1);
    }
}

# Detect if head post is locked
if ($mainPost->isLocked && !$_SESSION['user']->isAdmin) {
    Action::response(array(
        'error' => 'This post is locked!'
    ), 400);
}

# Creating comment
$data = Action::requestData();
$context = isset($data['context']) ? htmlspecialchars($data['context']) : '';

if (!$mainPost->isLocked) {
    $post = new Post();
    $post->headId = $mainPost->id;
    $post->user = $_SESSION['user'];
    $post->text = $context;
    $post->upload();
    $mainPost->updateCommentCount(1);
} else {
    $post = new Post();
    $post->headId = $mainPost->id;
    $post->user = $_SESSION['user'];
    $post->text = $context;
    $post->isLocked = true;
    $post->upload();
    $mainPost->updateCommentCount(1);
    $post->setLocked();
}



# Create notification
$notification = new Notification();
$notification->type = 4;
$notification->userId = $mainPost->user->id;
$notification->aboutUserId = $_SESSION['user']->id;
$notification->aboutId = $post->id;
if(!$notification->checkAlreadyExists() && $mainPost->user->commentNotifications) {
    $notification->create();
}



Action::response(array(
    'status' => 'Comment created',
    'data' => $post->shortId,
));
