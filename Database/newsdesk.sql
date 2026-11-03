-- Type New Design SQL Codes Here
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- Users Table
CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  student_id    VARCHAR(12)  NOT NULL UNIQUE,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('admin','student') NOT NULL DEFAULT 'student',
  profile_photo VARCHAR(255) NULL DEFAULT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories table
CREATE TABLE categories (
  id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;









-- -- Users table
-- CREATE TABLE users (
--   id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--   name          VARCHAR(100) NOT NULL,
--   student_id    VARCHAR(12)  NOT NULL UNIQUE,
--   email         VARCHAR(150) NOT NULL UNIQUE,
--   password_hash VARCHAR(255) NOT NULL,
--   role          ENUM('admin','student') NOT NULL DEFAULT 'student',
--   profile_photo VARCHAR(255) NULL DEFAULT NULL,
--   created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -- Categories table
-- CREATE TABLE categories (
--   id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--   name VARCHAR(60) NOT NULL UNIQUE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -- Articles table
-- CREATE TABLE articles (
--   id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--   title       VARCHAR(200) NOT NULL,
--   body        TEXT NOT NULL,
--   image       VARCHAR(255) NULL,
--   category_id INT UNSIGNED NOT NULL,
--   author_id   INT UNSIGNED NOT NULL,
--   status      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
--   is_featured TINYINT(1) NOT NULL DEFAULT 0,
--   is_breaking TINYINT(1) NOT NULL DEFAULT 0,
--   views       INT UNSIGNED NOT NULL DEFAULT 0,
--   created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   INDEX idx_status_date (status, created_at),
--   CONSTRAINT fk_art_cat  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
--   CONSTRAINT fk_art_user FOREIGN KEY (author_id)   REFERENCES users(id)      ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -- Announcements table
-- CREATE TABLE announcements (
--   id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--   title      VARCHAR(200) NOT NULL,
--   body       TEXT NOT NULL,
--   is_active  TINYINT(1) NOT NULL DEFAULT 1,
--   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -- Comments table
-- CREATE TABLE comments (
--   id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--   article_id INT UNSIGNED NOT NULL,
--   user_id    INT UNSIGNED NOT NULL,
--   body       TEXT NOT NULL,
--   status     ENUM('visible','hidden') NOT NULL DEFAULT 'visible',
--   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   CONSTRAINT fk_com_art  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
--   CONSTRAINT fk_com_user FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -- Bookmarks table
-- CREATE TABLE bookmarks (
--   user_id    INT UNSIGNED NOT NULL,
--   article_id INT UNSIGNED NOT NULL,
--   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (user_id, article_id),
--   CONSTRAINT fk_bm_user FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
--   CONSTRAINT fk_bm_art  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -- Subscriptions table
-- CREATE TABLE subscriptions (
--   user_id     INT UNSIGNED NOT NULL,
--   category_id INT UNSIGNED NOT NULL,
--   PRIMARY KEY (user_id, category_id),
--   CONSTRAINT fk_sub_user FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
--   CONSTRAINT fk_sub_cat  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;