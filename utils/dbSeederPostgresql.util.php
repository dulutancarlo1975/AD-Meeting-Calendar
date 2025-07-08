<?php

require_once __DIR__ . '/../bootstrap.php';
require_once UTILS_PATH . 'envSetter.util.php';

echo "Seeding users…\n";

try {
    // Connect to PostgreSQL
    $dsn = "pgsql:host={$_ENV['PG_HOST']};port={$_ENV['PG_PORT']};dbname={$_ENV['PG_DB']}";
    $pdo = new PDO($dsn, $_ENV['PG_USER'], $_ENV['PG_PASS'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Load dummy data
    $users = require_once DUMMIES_PATH . 'users.staticData.php';

    // Prepare insert statement
    $stmt = $pdo->prepare("
        INSERT INTO users (username, role, full_name, email, password)
        VALUES (:username, :role, :full_name, :email, :password)
    ");

    foreach ($users as $u) {
        $stmt->execute([
            ':username' => $u['username'],
            ':role' => $u['role'],
            ':full_name' => $u['full_name'],
            ':email' => $u['email'],
            ':password' => password_hash($u['password'], PASSWORD_DEFAULT),
        ]);
    }

    echo "✅ PostgreSQL seeding complete!\n";

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
}
