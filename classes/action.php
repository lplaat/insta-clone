<?php

class Action {

    public static function response($data, $status = 200) {
        # Prints out the data and stops
        echo json_encode($data);
        http_response_code($status);
        exit();
    }

    public static function isJson($string) {
        # Verifies that the data is json
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public static function requestData() {
        # Returns the json data receive from the client
        $rawData = file_get_contents('php://input');
        if(!Action::isJson($rawData)) {
            Action::response(array(
                'error' => 'Invalid json received!'
            ), 400);
        }

        return json_decode($rawData, true);
    }
}