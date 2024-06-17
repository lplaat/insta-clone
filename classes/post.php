<?php

require_once "classes/database.php";
require_once "classes/user.php";
require_once "classes/tools.php";

class Post {
    public $id;
    public $shortId;
    public $text;
    public $likedAmount;
    public $commentAmount;
    public $following;
    public $images;
    public $user;
    public $createdAt;

    function __construct($postId = null) {
        # Sets the post when the id given
        if($postId != null) {
            $this->getById($postId);
        }
    }

    private function getByAny($stmt, $values) {
        # Load the post by a stmt
        $stmt->execute($values);

        $result = $stmt->fetchAll();
        if(count($result) == 0) {
            return null;
        }

        # Set public from post variables
        $this->id = $result[0]['id'];
        $this->shortId = $result[0]['short_id'];
        $this->text = $result[0]['text'];
        $this->likedAmount = $result[0]['liked_amount'];
        $this->commentAmount = $result[0]['comment_amount'];
        $this->createdAt = $result[0]['created_at'];

        # Get the owner of the post
        $this->user = new User($result[0]['user_id']);

        # Get images from post
        $query = "SELECT * FROM images_post WHERE post_id = ?";

        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$this->id]);

        $images = array();
        $result = $stmt->fetchAll();

        # Walk over each image from the post
        foreach($result as $image){
            array_push($images, array(
                "id" => $image['id'],
                "path" => $image['path']
            ));
        }

        # Checks if the session user is following
        if(isset($_SESSION['user'])) {
            $this->following = $this->user->isFollowedBy($_SESSION['user']);
        } else {
            $this->following = null;
        }

        $this->images = $images;
        return true;
    }

    function getById($id) {
        # Loads in the post by id
        $query = "SELECT * FROM posts WHERE id = ?";
        $stmt = $GLOBALS['conn']->prepare($query);
        return $this->getByAny($stmt, array($id));
    }

    function getByShortId($shortId) {
        # Loads in the post by shortId
        $query = "SELECT * FROM posts WHERE short_id = ?";
        $stmt = $GLOBALS['conn']->prepare($query);
        return $this->getByAny($stmt, array($shortId));
    }

    function upload() {
        # Uploads the post to the database
        # ToDo: Image upload and verify that the shortId is not already in use!

        # Generate a random id
        $this->shortId = Tools::generateRandomString(12);

        # Commit to the database
        $query = "INSERT INTO posts (user_id, `text`, short_id) VALUES (?, ?, ?)";

        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$this->user->id, $this->text, $this->shortId]);

        $this->id = $GLOBALS['conn']->lastInsertId();
    }

    function delete() {
        # Deletes the post from the database
        $query = "DELETE FROM posts WHERE id = ?";
        $qryimg = "DELETE FROM images_post WHERE post_id = ?";
        $qrylike = "DELETE FROM users_likes WHERE post_id = ?";

        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$this->id]);

        $stmt = $GLOBALS['conn']->prepare($qryimg);
        $stmt->execute([$this->id]);

        $stmt = $GLOBALS['conn']->prepare($qrylike);
        $stmt->execute([$this->id]);
    }

    function isLikedByUser($user) {
        # Returns a boolean if the post is liked by the user
        $query = "SELECT * FROM users_likes WHERE user_id = ? AND post_id = ?";

        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$user->id, $this->id]);

        $result = $stmt->fetchAll();
        return count($result) != 0;
    }

    function userLiked($user, $likedStatus) {
        # Sets the like status for the user
        $dbStatus = $this->isLikedByUser($user);
        if($dbStatus == $likedStatus) {
            return true;
        }

        $increment = 0;
        if($likedStatus) {
            $query = "INSERT INTO users_likes (user_id, post_id) VALUES (?, ?)";
            $increment++;
        } else {
            $query = "DELETE FROM users_likes WHERE user_id = ? AND post_id = ?";
            $increment--;
        }

        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$user->id, $this->id]);

        # Edited the like amount on post
        $query = "UPDATE `posts` SET `liked_amount` = `liked_amount` + ? WHERE `id` = ?;";
        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$increment, $this->id]);

        return true;
    }

    function linkImage($media) {
        # Link an media path to a post
        $query = "INSERT INTO images_post (post_id, `path`) VALUES (?, ?)";

        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([$this->id, $media->path]);
    }
}
