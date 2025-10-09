-- Auto-init schema for MySQL (runs only on first start when data dir is empty)
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100) DEFAULT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  firstname VARCHAR(50) DEFAULT NULL,
  lastname VARCHAR(50) DEFAULT NULL,
  avatar VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Posts table
CREATE TABLE IF NOT EXISTS posts (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  thumbnail VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_user_id (user_id),
  CONSTRAINT fk_posts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default admin if none exists
INSERT INTO users (username, password, email, role, firstname, lastname, avatar)
SELECT 'admin', '$2y$12$x0grn5lmS1iXik1UmzH/Cea.SL5NHcCmNoR2GofkmaTwYnBtQAYyu', 'admin@example.com', 'admin', 'Site', 'Admin', 'default.png'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE role = 'admin');
