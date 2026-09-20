-- Forum Phase 1: Moderation & Subscriptions
-- Idempotent schema changes for thread pinning, locking, answer marking, and subscriptions

ALTER TABLE `forums_threads`
  ADD COLUMN IF NOT EXISTS `thread_pinned` TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `thread_locked` TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE `forums_replies`
  ADD COLUMN IF NOT EXISTS `reply_is_answer` TINYINT(1) NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS `forums_moderation_log` (
  `log_id`       INT(11)      NOT NULL AUTO_INCREMENT,
  `moderator_id` INT(11)      NOT NULL,
  `action_type`  VARCHAR(50)  NOT NULL,
  `target_type`  VARCHAR(20)  NOT NULL,
  `target_id`    INT(11)      NOT NULL,
  `reason`       TEXT,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_mod_log_moderator` (`moderator_id`),
  KEY `idx_mod_log_target`    (`target_type`, `target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `forums_subscriptions` (
  `subscription_id`   INT(11)     NOT NULL AUTO_INCREMENT,
  `user_id`           INT(11)     NOT NULL,
  `forum_id`          INT(11),
  `thread_id`         INT(11),
  `subscription_type` ENUM('forum','thread') NOT NULL,
  `notify_email`      TINYINT(1)  NOT NULL DEFAULT 1,
  `created_at`        TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`subscription_id`),
  UNIQUE KEY `uq_sub_forum`  (`user_id`, `forum_id`, `subscription_type`),
  UNIQUE KEY `uq_sub_thread` (`user_id`, `thread_id`, `subscription_type`),
  KEY `idx_sub_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
