<?php

require_once __DIR__ . '/../bootstrap.php';
require_once UTILS_PATH . 'envSetter.util.php';

echo "🔁 Running PostgreSQL Migration…\n";

try {
    // Connect to PostgreSQL
    $dsn = "pgsql:host={$_ENV['PG_HOST']};port={$_ENV['PG_PORT']};dbname={$_ENV['PG_DB']}";
    $pdo = new PDO($dsn, $_ENV['PG_USER'], $_ENV['PG_PASS'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Drop existing tables
    echo "🧹 Dropping old tables…\n";
    foreach ([
        'projects',
        'users'
    ] as $table) {
        $pdo->exec("DROP TABLE IF EXISTS {$table} CASCADE;");
    }

    // Apply schema files
    $models = ['users']; // You can add more here later: 'tasks', 'meetings', etc.
    foreach ($models as $model) {
        $path = BASE_PATH . "/database/{$model}.model.sql";
        echo "📄 Applying schema from database/{$model}.model.sql…\n";

        $sql = file_get_contents($path);

        if ($sql === false) {
            echo "❌ Could not read {$path}\n";
        } else {
            $pdo->exec($sql);
            echo "✅ Created table from {$model}.model.sql\n";
        }
    }

    echo "🎉 Migration complete!\n";

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
}
