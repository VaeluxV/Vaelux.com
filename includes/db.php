<?php
$db_file = __DIR__ . '/../data/auth.sqlite';

$init_needed = !file_exists($db_file);

try {
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($init_needed) {
        // Create users table
        $db->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                uuid TEXT NOT NULL UNIQUE,
                username TEXT NOT NULL UNIQUE,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                display_name TEXT NOT NULL,
                profile_picture TEXT DEFAULT NULL,
                display_name_changes INTEGER DEFAULT 0,
                display_name_last_changed TEXT DEFAULT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                profile_picture_changes INTEGER DEFAULT 0,
                profile_picture_last_changed TEXT DEFAULT NULL,
                email_verified INTEGER DEFAULT 0,
                verification_token TEXT DEFAULT NULL
            );
        ");
    } else {
        // Add missing columns if needed
        $columns = [];
        foreach ($db->query("PRAGMA table_info(users)") as $col) {
            $columns[] = $col['name'];
        }
        if (!in_array('profile_picture', $columns)) {
            $db->exec("ALTER TABLE users ADD COLUMN profile_picture TEXT DEFAULT NULL;");
        }
        if (!in_array('display_name_changes', $columns)) {
            $db->exec("ALTER TABLE users ADD COLUMN display_name_changes INTEGER DEFAULT 0;");
        }
        if (!in_array('display_name_last_changed', $columns)) {
            $db->exec("ALTER TABLE users ADD COLUMN display_name_last_changed TEXT DEFAULT NULL;");
        }
        if (!in_array('profile_picture_changes', $columns)) {
            $db->exec("ALTER TABLE users ADD COLUMN profile_picture_changes INTEGER DEFAULT 0;");
        }
        if (!in_array('profile_picture_last_changed', $columns)) {
            $db->exec("ALTER TABLE users ADD COLUMN profile_picture_last_changed TEXT DEFAULT NULL;");
        }
        if (!in_array('email_verified', $columns)) {
            $db->exec("ALTER TABLE users ADD COLUMN email_verified INTEGER DEFAULT 0;");
        }
        if (!in_array('verification_token', $columns)) {
            $db->exec("ALTER TABLE users ADD COLUMN verification_token TEXT DEFAULT NULL;");
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    die('Database connection failed: ' . $e->getMessage());
}
