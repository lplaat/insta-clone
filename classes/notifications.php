<?php

class Notification {
    public $id;
    public $shortId;
    public $type;
    public $userId;
    public $aboutUserId;
    public $aboutId;
    public $seen;
    public $createdAt;
    public $user;
    public $aboutUser;
    public $aboutPost;

    function __construct($id = null) {
        # loads in notification
        $qry = "SELECT * FROM notifications WHERE id = ?";
        $stmt = $GLOBALS["conn"]->prepare($qry);
        $stmt->execute([$id]);

        $result = $stmt->fetchAll();
        if(count($result) == 0) {
            return null;
        }
        
        # set public variables
        $this->user = new User($result[0]["user_id"]);
        $this->aboutUser = new User($result[0]["about_user_id"]);
        $this->aboutPost = new Post($result[0]["about_id"]);

        $this->id = $result[0]["id"];
        $this->shortId = $result[0]["short_id"];
        $this->type = $result[0]["type"];
        $this->userId = $result[0]["user_id"];
        $this->aboutUserId = $result[0]["about_user_id"];
        $this->aboutId = $result[0]["about_id"];
        $this->seen = $result[0]["seen"];
        $this->createdAt = $result[0]["created_at"];
    }

    function setSeen($id = null) {
        # sets notification to seen when it loads in
        $this->seen = 1;
        $qry = "UPDATE notifications SET `seen`=? WHERE id=?";
        $stmt = $GLOBALS["conn"]->prepare($qry);
        $stmt->execute([$this->seen, $id]);
    }

    function create() {
        # generate random id
        $this->shortId = Tools::generateRandomString(12);

        # Pushes notification to the database
        $qry = "INSERT INTO notifications (`user_id`, `short_id`, `type`, `about_user_id`, `about_id`) VALUES (?, ?, ?, ?, ?);";

        $stmt = $GLOBALS["conn"]->prepare($qry);
        $stmt->execute([$this->userId, $this->shortId, $this->type, $this->aboutUserId, $this->aboutId]);
    }

    function checkAlreadyExists() {
        # Checks if notifications exists
        $qry = "SELECT COUNT(*) FROM notifications WHERE `user_id` = ? AND `type` = ? AND `about_user_id` = ? AND `about_id` = ?";

        $stmt = $GLOBALS["conn"]->prepare($qry);
        $stmt->execute([$this->userId, $this->type, $this->aboutUserId, $this->aboutId]);
        return $stmt->fetchColumn() != 0;
    }
}
