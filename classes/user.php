<?php

require_once "classes/database.php";
require_once "classes/authentication.php";

class User {
    public $id;
    public $name;
    public $password;

    function getByName($username){
        # Returns the user by name
        $query = "SELECT * FROM users WHERE username = ?";

        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$username]);

        $result = $stmt->fetchAll();
        if (count($result) == 0) {
            return null;
        }

        # Set values and return a success
        $this->id = $result[0]['id'];
        $this->password = $result[0]['password'];

        return true;
    }

    function getById($id){
        # Returns the user by id
        $query = "SELECT * FROM users WHERE id = ?";

        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$id]);

        $result = $stmt->fetchAll();
        if (count($result) == 0) {
            return null;
        }

        # Set values and return a success
        $this->id = $id;
        $this->password = $result[0]['password'];

        return true;
    }

    function create(){
        # Create account in the database
        $return = $this->getByName($this->name);
        if ($return != null) {
            return false;
        }

        $query = "INSERT INTO users (username, `password`) VALUES (?, ?)";

        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$this->name, password_hash($this->password, PASSWORD_DEFAULT)]);

        return true;
    }

    function login($username, $rawPassword){
        # Checks credentials and load in the user information
        $success = $this->getByName($username);
        if ($success == null) {
            return null;
        }

        if (!password_verify($rawPassword, $this->password)) {
            return null;
        }

        # Passes the user information to the authentication and then return
        Authentication::userLogins($this);

        return $this;
    }
}
