<?php
// Auto-bootstrap database schema without external SQL files
// This runs lightweight checks and uses IF NOT EXISTS to avoid altering existing setups.

if (!isset($conn) || !($conn instanceof PDO)) {
    // Database connection not available; nothing to do.
    return;
}

try {
    // Ensure users table exists
    $conn->exec(
        "CREATE TABLE IF NOT EXISTS users (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    // Ensure posts table exists
    $conn->exec(
        "CREATE TABLE IF NOT EXISTS posts (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    // Seed default admin if none exists
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $hasAdmin = (int)$stmt->fetchColumn() > 0;

    if (!$hasAdmin) {
        $insert = $conn->prepare(
            "INSERT INTO users (username, password, email, role, firstname, lastname, avatar)
             VALUES (:username, :password, :email, :role, :firstname, :lastname, :avatar)"
        );
        // Password hash corresponds to 'admin123' (example). Change in pclearroduction.
        $insert->execute([
            ':username'  => 'admin',
            ':password'  => '$2y$12$x0grn5lmS1iXik1UmzH/Cea.SL5NHcCmNoR2GofkmaTwYnBtQAYyu',
            ':email'     => 'admin@example.com',
            ':role'      => 'admin',
            ':firstname' => 'Site',
            ':lastname'  => 'Admin',
            ':avatar'    => 'default.png',
        ]);
    }
} catch (Throwable $e) {
    // Soft-fail: log to error log but don't block page load
    error_log('[DB Bootstrap] ' . $e->getMessage());
}
