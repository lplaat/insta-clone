<?php

require_once "classes/database.php";
require_once "classes/post.php";
require_once "classes/user.php";
require_once "classes/tools.php";
require_once "classes/notifications.php";

class Feed {
    public $token;
    private $seenItemsIds;
    private $itemsId;
    private $itemsType;
    private $itemsTypeValue;

    function __construct($itemsType, $value) {
        # Sets user when the id given
        $this->seenItemsIds = array(-1);
        $this->token = Tools::generateRandomString(12);
        $this->itemsType = $itemsType;
        $this->itemsTypeValue = $value;

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

    private function getItemsFromQuery($query) {
        # Returns item ids from a query
        $stmt = $GLOBALS['conn']->prepare($query);
        $stmt->execute([]);

        $result = $stmt->fetchAll();
        $itemIds = array();
        foreach($result as $row) {
            array_push($itemIds, $row['id']);
        }

        # Added items to the seen item list
        $this->seenItemsIds = array_merge($this->seenItemsIds, $itemIds);

        return $itemIds;
    }

    private function showTrendingPosts($max = 5) {
        # Returns the most recent trending posts
        $seenPosts = implode(',', $this->seenItemsIds);
        $query = "
            SELECT p.id
            FROM posts p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN users_follows uf ON uf.user_id = p.user_id AND uf.follower_id = " . $_SESSION['user']->id . "
            WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            AND p.id NOT IN ($seenPosts)
            AND p.head_id IS NULL
            AND p.is_deleted = 0
            AND u.is_deleted = 0
            AND (
                u.private = 0 OR 
                uf.user_id IS NOT NULL OR 
                p.user_id = " . $_SESSION['user']->id . "
            )
            ORDER BY 
            (p.liked_amount + p.comment_amount) DESC, 
            p.created_at DESC
            LIMIT $max;
        ";
        return $this->getItemsFromQuery($query);
    }
    
    private function showFollowingPosts($max = 5) {
        # Returns the most recent posts from the users the person is following
        $seenPosts = implode(',', $this->seenItemsIds);
        $mainUserId = $_SESSION['user']->id;
        $query = "
            SELECT posts.id
            FROM posts
            JOIN users_follows ON posts.user_id = users_follows.user_id
            JOIN users ON posts.user_id = users.id
            WHERE posts.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            AND posts.id NOT IN ($seenPosts)
            AND posts.head_id IS NULL
            AND posts.is_deleted = 0
            AND users.is_deleted = 0
            AND users_follows.follower_id = $mainUserId
            ORDER BY posts.created_at DESC
            LIMIT $max;
        ";
        return $this->getItemsFromQuery($query);
    }
    
    private function showUserPosts($max = 5, $userId) {
        # Returns the most recent post from the selected user
        $seenPosts = implode(',', $this->seenItemsIds);

        $space = '';
        if (!$_SESSION['user']->isAdmin) {
            $space = "AND posts.is_deleted = 0 AND users.is_deleted = 0";
        }

        $query = "
            SELECT posts.id
            FROM posts
            JOIN users ON posts.user_id = users.id
            WHERE users.id = $userId
            AND posts.id NOT IN ($seenPosts)
            AND posts.head_id IS NULL
            $space
            ORDER BY posts.created_at DESC
            LIMIT 10;
        ";
        return $this->getItemsFromQuery($query);
    }    

    private function getNotifications() {
        # Gets recent notifications from user
        $seenNotifications = implode(',', $this->seenItemsIds);
        $mainUserId = $_SESSION['user']->id;
        $query = "
            SELECT (notifications.id)
            FROM notifications
            WHERE notifications.id NOT IN ($seenNotifications)
            AND notifications.user_id = $mainUserId
            ORDER BY 
            notifications.type DESC,
            notifications.created_at DESC
            LIMIT 10;
        ";
        return $this->getItemsFromQuery($query);
    }

    private function getUsers() {
        # Gets users by search
        $seenUsers = implode(',', $this->seenItemsIds);

        $space = "";
        if (!$_SESSION['user']->isAdmin) {
            $space = "AND users.is_deleted = 0";
        }

        $query = "
        SELECT (users.id)
        FROM users
        WHERE
        users.id NOT IN ($seenUsers)
        AND users.username LIKE '%$this->itemsTypeValue%'
        $space
        ORDER BY
        users.followers DESC
        LIMIT 10;
        ";
        
        return $this->getItemsFromQuery($query);
    }

    private function getComments($max = 5, $headId) {
        # Get comments by post
        $reverseOrder = "";
        $post = new Post($headId);
        if($post->headId == null) $reverseOrder = 'DESC';

        $seenPosts = implode(',', $this->seenItemsIds);
        $query = "
            SELECT posts.id
            FROM posts
            JOIN users ON posts.user_id = users.id
            WHERE posts.id NOT IN ($seenPosts)
            AND posts.head_id = $headId
            AND users.is_deleted = 0
            ORDER BY posts.created_at $reverseOrder
            LIMIT $max;
        ";
        return $this->getItemsFromQuery($query);
    }

    function getItems() {
        $items = array();
        if($this->itemsType == 'any') {
            # Get most recent post of people the user follows
            $items = $this->showFollowingPosts(10);
            $items = array_merge($items, $this->showTrendingPosts(10 - count($items)));
        }else if($this->itemsType == 'user'){
            # Get all the recent post of one user
            $user = new User();
            $user->getByName($this->itemsTypeValue);
         
            if($user->viewingRights){
                $items = $this->showUserPosts(10, $user->id);
            } else {
                $items = array();
            }
        }else if($this->itemsType == 'comments') {
            # Returns the list of comments
            $post = new Post();
            $post->getByShortId($this->itemsTypeValue);

            $items = $this->getComments(10, $post->id);
        }else if($this->itemsType == 'notification') {
            # Get all the recent notification from the main user
            $items = $this->getNotifications();
        } else if ($this->itemsType == 'getUsers') {
            # Get a list of all users
            $items = $this->getUsers();
        }

        # Turn item id's into components
        $components = array();
        foreach($items as $itemsId) {
            ob_start();

            if ($this->itemsType == 'notification') {
                $notification = new Notification($itemsId);
                $notification->setSeen($itemsId);
                include "components/cards/notification.php";
            } else if ($this->itemsType == 'user' || $this->itemsType == 'any' || $this->itemsType == 'comments') {
                $post = new Post($itemsId);
                include 'components/post.php';    
            } else if ($this->itemsType == 'getUsers') {
                $user = new User($itemsId);
                include "components/cards/user.php";
            }

            $itemComponents = ob_get_clean();
            array_push($components, $itemComponents);
        }

        return $components;
    }

    public static function initFeedSession() {
        # Creates a feed array to store the feed instances and token links
        if(!isset($_SESSION['feeds'])){
            $_SESSION['feeds'] = array();
        }
    }
}