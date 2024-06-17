<?php

# Includes
$jsonResponse = true;
require_once 'includes/header.php';
require_once 'classes/user.php';
require_once 'classes/action.php';
require_once 'classes/notifications.php';

# Verifies user id
$user = new User();
$return = $user->getByName($GLOBALS['username']);
if($return == null) {
    Action::response(array(
        'error' => 'User not found!'
    ), 404);
}

# GET method returns liked status
if($_SERVER['REQUEST_METHOD'] == 'GET') {
    Action::response(array(
        'status' => $user->isFollowedBy($_SESSION['user'])
    ));
}

# POST method sets the follow status
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = Action::requestData();
    $followingStatus = isset($data['following']) ? boolval($data['following']) : false;
    $requested = isset($data['requested']) ? boolval($data['requested']) : false;
    $accepted = isset($data['accepted']) ? boolval($data['accepted']) : false;
    $declined = isset($data['declined']) ? boolval($data['declined']) : false;

    # Checks if the user is not following him self
    if ($user->id == $_SESSION['user']->id) {
        Action::response(array(
            'error' => "Cant follow you're self!",
        ), 400);
    }

    if ($user->private && !$requested && !$accepted && $followingStatus) {
        Action::response(array(
            'error' => 'Cant directly follow private user!',
        ),400);
    }

    # make notification when following
    if ($followingStatus) {
        $notification = new Notification();
        $notification->userId = $user->id;
        $notification->aboutUserId = $_SESSION['user']->id;
        $notification->aboutId = null;
        $notification->type = 1;

        if (!$notification->checkAlreadyExistsFollow()) {
            $notification->create();
        }
    }

    # make follow request notification
    if ($requested) {
        $notification = new Notification();
        $notification->userId = $user->id;
        $notification->aboutUserId = $_SESSION['user']->id;
        $notification->aboutId = null;
        $notification->type = 2;

        if (!$notification->checkAlreadyExistsFollow()) {
            $notification->create();
        }

        # return followrequeststatus
        Action::response(array(
            'status' => $requested
        ));
    } else {
        Notification::deleteNotification($user->id, 2,$_SESSION["user"]->id);
    }

    if ($accepted) {
        if ($_SESSION['user']->checkExistsFollowRequest($user)){
            $_SESSION['user']->userFollowedByStatus($user, true);

            # delete accept notification
            Notification::deleteNotification($_SESSION['user']->id, 2, $user->id);

            # create follow accepted notification
            $notification = new Notification();
            $notification->userId = $user->id;
            $notification->aboutUserId = $_SESSION['user']->id;
            $notification->aboutId = null;
            $notification->type = 3;

            if (!$notification->checkAlreadyExistsFollow()) {
                $notification->create();
            }

            # return accept status
            Action::response(array(
                'status' => $accepted
            )); 
        } else {
             Action::response(array(
                'error' => "No follow request to accept!"
            ), 400); 
        }
    }

    if ($declined) {
        if ($_SESSION['user']->checkExistsFollowRequest($user)) {
            # delete notification when declined
            Notification::deleteNotification($_SESSION['user']->id, 2, $user->id);

            # Return decline status
            Action::response(array(
                'status' => $declined
            ));
        } else {
            Action::response(array(
                'error' => "No follow request to decline!"
            ), 400);
        }
    }

    # Save new following status
    $user->userFollowedByStatus($_SESSION['user'], $followingStatus);
    Action::response(array(
        'status' => $followingStatus
    ));
}

Action::response(array(
    'error' => 'invalid method!'
), 400);