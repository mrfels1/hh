<?php
$dbUser = DB_USER;
$dbPassword = DB_PASSWORD;

try {
    $conn = new PDO("mysql:host=db", $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE DATABASE IF NOT EXISTS hhdb";
    $conn->exec($sql);
    $conn->exec("USE hhdb");

    //users
    $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT UNSIGNED NOT NULL PRIMARY KEY)";
    $conn->exec($sql);

    $sql = "INSERT IGNORE INTO users (id)
            VALUES (1),(2),(3)";
    $conn->exec($sql);

    //perm_groups
    $sql = "CREATE TABLE IF NOT EXISTS perm_groups (
        id INT UNSIGNED NOT NULL PRIMARY KEY,
        is_ban_group BOOLEAN NOT NULL
        )";
    $conn->exec($sql);

    $sql = "INSERT IGNORE INTO perm_groups (id,is_ban_group)
            VALUES (1,0),(2,0),(3,0),(4,0),(5,0),(6,0),(7,0),
                (8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1)";
    $conn->exec($sql);

    //users_groups pivot
    $sql = "CREATE TABLE IF NOT EXISTS users_groups (
                user_id INT UNSIGNED NOT NULL,
                group_id INT UNSIGNED NOT NULL,
                PRIMARY KEY (user_id, group_id),
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (group_id) REFERENCES perm_groups(id)
                )";
    $conn->exec($sql);

    //permissions
    $sql = "CREATE TABLE IF NOT EXISTS permissions (
                permission varchar(16) NOT NULL PRIMARY KEY )";
    $conn->exec($sql);

    $sql = "INSERT IGNORE INTO permissions (permission)
            VALUES ('send_messages'),('service_api'),('debug')";
    $conn->exec($sql);

    //groups_permissions pivot
    $sql = "CREATE TABLE IF NOT EXISTS groups_permissions (
                permission varchar(16) NOT NULL,
                group_id INT UNSIGNED NOT NULL,
                PRIMARY KEY (permission, group_id), 
                FOREIGN KEY (permission) REFERENCES permissions(permission),
                FOREIGN KEY (group_id) REFERENCES perm_groups(id)
                )";
    $conn->exec($sql);

    $sql = "INSERT IGNORE INTO groups_permissions (permission, group_id)
            VALUES 
            ('debug', 1),
            ('service_api', 2),
            ('service_api', 3),('debug', 3),
            ('send_messages', 4),
            ('send_messages', 5),('debug', 5),
            ('send_messages', 6),('service_api', 6),
            ('send_messages', 7),('service_api', 7),('debug', 7),
            ('debug', 8),
            ('service_api', 9),
            ('service_api', 10),('debug', 10),
            ('send_messages', 11),
            ('send_messages', 12),('debug', 12),
            ('send_messages', 13),('service_api', 13),
            ('send_messages', 14),('service_api', 14),('debug', 14)";
    $conn->exec($sql);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
$conn = null;
