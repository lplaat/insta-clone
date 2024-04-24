<?php

require_once "classes/database.php";
require_once "classes/user.php";

class Post {
    public $id;
    public $text;
    public $likedAmount;
    public $commentAmount;
    public $images;
    public $user;
    public $createdAt;

    function getById($id) {
        # Load the post by id
        $query = "SELECT * FROM posts WHERE id = ?";

        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$id]);

        $result = $stmt->fetchAll();
        if (count($result) == 0) {
            return null;
        }

        # Set public from post variables
        $this->id = $result[0]['id'];
        $this->text = $result[0]['text'];
        $this->likedAmount = $result[0]['liked_amount'];
        $this->commentAmount = $result[0]['comment_amount'];
        $this->createdAt = $result[0]['created_at'];

        # Get the owner of the post
        $this->user = new User();
        $this->user->getById($result[0]['user_id']);

        # Get images from post
        $query = "SELECT * FROM images_post WHERE post_id = ?";

        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$id]);

        $images = array();
        $result = $stmt->fetchAll();

        # Walk over each image and it public
        foreach($result as $image){
            array_push($images, array(
                "id" => $image['id'],
                "path" => $image['path']
            ));
        }

        $this->images = $images;
        return true;
    }

    function upload() {
        # Todo: Uploads the post to the database
    }

    function delete() {
        # Todo: Deletes the post from the database
    }

    function isLikedByUser($userId) {
        # Returns a boolean if the post is liked by the user
        $query = "SELECT * FROM users_likes WHERE user_id = ? AND post_id = ?";

        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$userId, $this->$id]);

        $result = $stmt->fetchAll();
        return count($result) != 0;
    }
}
