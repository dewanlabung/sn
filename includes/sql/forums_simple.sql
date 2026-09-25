-- Simple one-statement-per-line migration

-- Add columns to forums_threads
ALTER TABLE forums_threads ADD COLUMN thread_pinned TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE forums_threads ADD COLUMN thread_locked TINYINT(1) NOT NULL DEFAULT 0;

-- Add column to forums_replies
ALTER TABLE forums_replies ADD COLUMN reply_is_answer TINYINT(1) NOT NULL DEFAULT 0;

-- Create moderation log table
CREATE TABLE forums_moderation_log (
  log_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  moderator_id INT(11) NOT NULL,
  action_type VARCHAR(50) NOT NULL,
  target_type VARCHAR(20) NOT NULL,
  target_id INT(11) NOT NULL,
  reason TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_mod_log_moderator (moderator_id),
  KEY idx_mod_log_target (target_type, target_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create subscriptions table
CREATE TABLE forums_subscriptions (
  subscription_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id INT(11) NOT NULL,
  forum_id INT(11),
  thread_id INT(11),
  subscription_type ENUM('forum','thread') NOT NULL,
  notify_email TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_sub_user (user_id),
  UNIQUE KEY uq_sub_forum (user_id, forum_id, subscription_type),
  UNIQUE KEY uq_sub_thread (user_id, thread_id, subscription_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
