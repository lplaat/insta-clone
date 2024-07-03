
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `real_name` text NOT NULL,
  `biography` text NULL DEFAULT NULL,
  `email` text NOT NULL,
  `avatar_path` text NULL DEFAULT NULL,
  `private` tinyint(1) NOT NULL DEFAULT 0,
  `theme` tinyint(4) NOT NULL DEFAULT 0,
  `following` int(11) NOT NULL DEFAULT 0,
  `followers` int(11) NOT NULL DEFAULT 0,
  `like_notifications` BOOLEAN NOT NULL DEFAULT TRUE,
  `comment_notifications` BOOLEAN NOT NULL DEFAULT TRUE,
  `follow_notifications` BOOLEAN NOT NULL DEFAULT TRUE,
  `follow_requests` BOOLEAN NOT NULL DEFAULT TRUE,
  `is_admin` BOOLEAN NOT NULL DEFAULT 0,
  `is_locked` BOOLEAN NOT NULL DEFAULT 0,
  `is_deleted` BOOLEAN NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `username`, `password`, `real_name`, `email`, `is_admin`) VALUES
(1, 'admin', '$2y$10$vfZep2kwQIvsIYoghgx5iu8E8OIoBLTVm4O5NvJJ/ABq.Y9IyPIHu', 'Administrator', 'admin@example.com', 1);

CREATE TABLE `images_post` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `path` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `short_id` varchar(12) NOT NULL,
  `head_id` int(11) DEFAULT NULL,
  `text` text NOT NULL,
  `liked_amount` int(11) NOT NULL DEFAULT 0,
  `comment_amount` int(11) NOT NULL DEFAULT 0,
  `is_pinned` tinyint(1) NOT NULL DEFAULT FALSE,
  `is_locked` tinyint(1) NOT NULL DEFAULT FALSE,
  `is_deleted` BOOLEAN NOT NULL DEFAULT FALSE,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users_likes` (
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users_follows` (
  `follower_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users_blocked` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `blocked_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `short_id` text NOT NULL,
  `type` tinyint(4) NOT NULL,
  `user_id` int(11) NOT NULL,
  `about_user_id` int(11) NOT NULL,
  `about_id` int(11) NULL,
  `seen` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `images_post`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `images_post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;