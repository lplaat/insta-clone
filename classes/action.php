<?php
require_once "classes/tools.php";

class Action {

    public static function response($data, $status = 200) {
        # Prints out the data and stops
        http_response_code($status);
        exit();
    }

    public static function requestData() {
        # Returns the json data receive from the client
        $rawData = file_get_contents('php://input');
        if(!Tools::isJson($rawData)) {
            Action::response(array(
                'error' => 'Invalid json received!'
            ), 400);
        }

        return json_decode($rawData, true);
    }
}