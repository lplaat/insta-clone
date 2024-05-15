<?php

class Notification {
    public $id;
    public $type;
    public $userId;
    public $aboutUserId;
    public $aboutId;
    public $seen;
    public $createdAt;
    
    function create() {
        # Pushes notification to the database
        $qry = "INSERT INTO notifications (`user_id`, `type`, `about_user_id`, `about_id`) VALUES (?, ?, ?, ?);";

        $stmt = $GLOBALS["conn"]->prepare($qry);
        $stmt->execute([$this->userId, $this->type, $this->aboutUserId, $this->aboutId]);
    }

    function checkAlreadyExists() {
        # Checks if notifications exists
        $qry = "SELECT COUNT(*) FROM notifications WHERE `user_id` = ? AND `type` = ? AND `about_user_id` = ? AND `about_id` = ?";

        $stmt = $GLOBALS["conn"]->prepare($qry);
        $stmt->execute([$this->userId, $this->type, $this->aboutUserId, $this->aboutId]);
        return $stmt->fetchColumn() != 0;
    }
}
