<?php

require_once "classes/database.php";
require_once "classes/post.php";
require_once "classes/user.php";
require_once "classes/tools.php";

class Feed {
    public $token;
    private $showTrendingPosts;
    private $seenPostsIds;
    private $postsId;
    private $postsType;
    private $postsTypeValue;

    function __construct($postsType, $value) {
        # Sets user when the id given
        $this->seenPostsIds = array(-1);
        $this->token = Tools::generateRandomString(12);
        $this->postsType = $postsType;
        $this->postsTypeValue = $value;

        # Set feed in session
        $_SESSION['feeds'][$this->token] = $this;
    }

    public static function get($token) {
        # Returns a feed object when it exist
        if(isset($_SESSION['feeds'][$token])) {
            return $_SESSION['feeds'][$token];
        }

        return null;
    }

    private function getPostsFromQuery($query) {
        # Returns posts ids from a query
        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([]);

        $result = $stmt->fetchAll();
        $postIds = array();
        foreach($result as $row) {
            array_push($postIds, $row['id']);
        }

        # Added posts to the seen post list
        $this->seenPostsIds = array_merge($this->seenPostsIds, $postIds);

        return $postIds;
    }

    private function showTrendingPosts($max = 5) {
        # Returns the most recent trending posts
        $seenPosts = implode(',', $this->seenPostsIds);
        $query = "
            SELECT (id) 
            FROM posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
            AND id NOT IN ($seenPosts) 
            ORDER BY (liked_amount + comment_amount) DESC, created_at DESC 
            LIMIT $max;
        ";
        return $this->getPostsFromQuery($query);
    }

    private function showFollowingPosts($max = 5) {
        # Returns the most recent posts from the users the person is following
        $seenPosts = implode(',', $this->seenPostsIds);
        $mainUserId = $_SESSION['user']->id;
        $query = "
            SELECT (posts.id)
            FROM posts
            JOIN users_follows ON posts.user_id = users_follows.user_id
            WHERE posts.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            AND posts.id NOT IN ($seenPosts)
            AND users_follows.follower_id = $mainUserId
            ORDER BY posts.created_at DESC
            LIMIT 10;
        ";
        return $this->getPostsFromQuery($query);
    }

    private function showUserPosts($max = 5, $userId) {
        # Returns the most recent post from the selected user
        $seenPosts = implode(',', $this->seenPostsIds);
        $mainUserId = $_SESSION['user']->id;
        $query = "
            SELECT (posts.id)
            FROM posts
            JOIN users ON posts.user_id = users.id
            WHERE users.id = $userId
            AND posts.id NOT IN ($seenPosts)
            ORDER BY posts.created_at DESC
            LIMIT 10;
        ";
        return $this->getPostsFromQuery($query);
    }

    function getPosts() {
        if($this->postsType == 'any') {
            # Get most recent post of people the user follows
            $posts = $this->showFollowingPosts(10);
            $posts = array_merge($posts, $this->showTrendingPosts(10 - count($posts)));
        }else if($this->postsType == 'user'){
            # Get all the recent post of one user
            $user = new User();
            $user->getByName($this->postsTypeValue);
            $posts = $this->showUserPosts(10, $user->id);
        }

        # Turn post id to post components
        $postComponents = array();
        foreach($posts as $postId) {
            ob_start();

            $post = new Post($postId);
            include 'components/post.php';

            $postComponent = ob_get_clean();
            array_push($postComponents, $postComponent);
        }

        return $postComponents;
    }

    public static function initFeedSession() {
        # Creates a feed array to store the feed instances and token links
        if(!isset($_SESSION['feeds'])){
            $_SESSION['feeds'] = array();
        }
    }
}