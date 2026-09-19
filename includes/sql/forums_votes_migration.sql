-- Forums Votes Migration
-- Run this on alphaome_nkhojfm database
-- Adds upvote/downvote support for forum threads and replies

CREATE TABLE IF NOT EXISTS `forums_votes` (
  `vote_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `item_type` enum('thread','reply') NOT NULL DEFAULT 'thread',
  `vote_type` enum('up','down') NOT NULL DEFAULT 'up',
  `time` datetime NOT NULL,
  PRIMARY KEY (`vote_id`),
  UNIQUE KEY `unique_vote` (`user_id`,`item_id`,`item_type`),
  KEY `idx_item` (`item_id`,`item_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
