<?php
declare(strict_types=1);

// 1. Load dependencies with proper path resolution
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap.php';

// 2. Load environment configuration
require_once UTILS_PATH . 'envSetter.util.php';

// 3. Enhanced connection function
function createPDOConnection() {
    $connectionAttempts = [
        [
            'host' => 'localhost', 
            'port' => $_ENV['PG_PORT'] ?? '5112',
            'desc' => 'Localhost with configured port'
        ],
        [
            'host' => 'postgresql', // Docker service name
            'port' => '5432',
            'desc' => 'Docker internal network'
        ]
    ];

    $dbname = $_ENV['PG_DB'] ?? 'dulutandb';
    $user = $_ENV['PG_USER'] ?? 'user';
    $pass = $_ENV['PG_PASS'] ?? 'password';

    foreach ($connectionAttempts as $attempt) {
        try {
            $dsn = "pgsql:host={$attempt['host']};port={$attempt['port']};dbname=$dbname";
            echo "Attempting connection to {$attempt['host']}:{$attempt['port']}...";
            
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 3
            ]);
            
            echo "✅ Success\n";
            return $pdo;
        } catch (PDOException $e) {
            echo "❌ Failed: " . $e->getMessage() . "\n";
            continue;
        }
    }
    
    throw new RuntimeException("Could not establish database connection");
}

try {
    // 4. Establish database connection
    $pdo = createPDOConnection();

    // 5. Define and verify schema files
    $databaseDir = BASE_PATH . 'database' . DIRECTORY_SEPARATOR;
    $schemaFiles = [
        'user.model.sql',
        'meeting.model.sql',
        'meeting_users.model.sql',
        'tasks.model.sql'
    ];

    // Create database directory if it doesn't exist
    if (!is_dir($databaseDir)) {
        mkdir($databaseDir, 0755, true);
        echo "Created database directory: $databaseDir\n";
    }

    foreach ($schemaFiles as $file) {
        $fullPath = $databaseDir . $file;
        if (!file_exists($fullPath)) {
            // Create empty schema file if missing
            file_put_contents($fullPath, "-- $file\nCREATE TABLE IF NOT EXISTS placeholder (id SERIAL);");
            echo "⚠ Created placeholder schema: $fullPath\n";
        }

        $sql = file_get_contents($fullPath);
        $pdo->exec($sql);
        echo "✔ Applied schema: $file\n";
    }

    // 6. Truncate tables
    $tables = ['meeting_users', 'tasks', 'meetings', 'users'];
    foreach ($tables as $table) {
        try {
            $pdo->exec("TRUNCATE TABLE $table RESTART IDENTITY CASCADE;");
            echo "✔ Truncated table: $table\n";
        } catch (PDOException $e) {
            echo "⚠ Table $table doesn't exist yet (this is normal on first run)\n";
        }
    }

    echo "✅ Database reset completed successfully\n";
    exit(0);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}