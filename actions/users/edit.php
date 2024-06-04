<?php

# Includes
$jsonResponse = true;
require 'includes/header.php';
require_once 'classes/action.php';

# POST method sets the biography value
if($_SERVER['REQUEST_METHOD'] != 'POST') {
    Action::response(array(
        'error' => 'invalid method!'
    ), 400);
}

$data = Action::requestData();
$biography = isset($data['biography']) ? htmlspecialchars($data['biography'], ENT_QUOTES, 'UTF-8') : null;
if($biography == '') $biography = null;

$_SESSION['user']->biography = $biography;
$_SESSION['user']->update();

Action::response(array(
    'status' => 'success'
));