-- =================================================================
-- NSBM NewsDesk — Missing Tables Migration (Idempotent)
-- Run this if comments, announcements, or subscriptions tables
-- are missing from your 'newsdesk' database.
-- Safe to run multiple times (uses IF NOT EXISTS).
-- =================================================================

SET NAMES utf8mb4;

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  article_id INT UNSIGNED NOT NULL,
  user_id    INT UNSIGNED NOT NULL,
  body       TEXT NOT NULL,
  status     ENUM('visible','hidden') NOT NULL DEFAULT 'visible',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_com_art  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
  CONSTRAINT fk_com_user FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Announcements table
CREATE TABLE IF NOT EXISTS announcements (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title      VARCHAR(200) NOT NULL,
  body       TEXT NOT NULL,
  is_active  TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Subscriptions table
CREATE TABLE IF NOT EXISTS subscriptions (
  user_id     INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (user_id, category_id),
  CONSTRAINT fk_sub_user FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
  CONSTRAINT fk_sub_cat  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bookmarks table (also may be missing)
CREATE TABLE IF NOT EXISTS bookmarks (
  user_id    INT UNSIGNED NOT NULL,
  article_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, article_id),
  CONSTRAINT fk_bm_user FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
  CONSTRAINT fk_bm_art  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ensure profile_photo column exists on users
-- (Safe: ALTER TABLE will error if column exists, wrapped in a procedure)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'profile_photo');
SET @sql = IF(@col_exists = 0,
  'ALTER TABLE users ADD COLUMN profile_photo VARCHAR(255) NULL DEFAULT NULL AFTER role',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
