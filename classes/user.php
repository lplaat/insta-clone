<?php

require_once "classes/database.php";
require_once "classes/authentication.php";

class User {
    public $id;
    public $name;
    public $password;

    function __construct($userId = null) {
        # Sets user with the id given
        $this->getById($userId);
    }

    private function getByAny($stmt, $values){
        # Returns the user by name
        $stmt->execute($values);

        $result = $stmt->fetchAll();
        if(count($result) == 0) {
            return null;
        }

        # Set values and return a success
        $this->id = $result[0]['id'];
        $this->name = $result[0]['username'];
        $this->password = $result[0]['password'];

        return true;
    }

    function getByName($name){
        # Returns the user by username
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $GLOBALS['conn']->prepare($query);
        return $this->getByAny($stmt, array($name));
    }

    function getById($id){
        # Returns the user by id
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $GLOBALS['conn']->prepare($query);
        return $this->getByAny($stmt, array($id));
    }

    function create(){
        # Create account in the database
        $return = $this->getByName($this->name);
        if($return != null) {
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
        if($success == null) {
            return null;
        }

        if(!password_verify($rawPassword, $this->password)) {
            return null;
        }

        # Passes the user information to the authentication and then return
        Authentication::userLogins($this);

        return $this;
    }

    function isFollowedBy($userId) {
        # Returns a boolean ifthe user is following this user
        $query = "SELECT * FROM users_follows WHERE follower_id = ? AND user_id = ?";

        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$userId, $this->id]);

        $result = $stmt->fetchAll();
        return count($result) != 0;
    }

    function userFollowedByStatus($userId, $status) {
        # Sets the following status for the user
        $dbStatus = $this->isFollowedBy($userId);
        if($dbStatus == $status) {
            return true;
        }

        if($status) {
            $query = "INSERT INTO users_follows (follower_id, user_id) VALUES (?, ?)";
        } else {
            $query = "DELETE FROM users_follows WHERE follower_id = ? AND user_id = ?";
        }

        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$userId, $this->id]);
        return true;
    }
}
