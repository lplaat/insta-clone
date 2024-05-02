<?php

# Includes
$jsonResponse = true;
require 'includes/header.php';
require 'classes/post.php';
require 'classes/action.php';

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
        'status' => $post->isLikedByUser($_SESSION['userId'])
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

    $post->userLiked($_SESSION['userId'], $likedValue);
    Action::response(array(
        'status' => $likedValue
    ));
}

Action::response(array(
    'error' => 'invalid method!'
), 400);
