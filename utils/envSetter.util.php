<?php
// Verify essential constants are defined
if (!defined('BASE_PATH') || !defined('VENDORS_PATH')) {
    die("❌ Critical: Bootstrap constants not defined. Check bootstrap.php");
}

// Debug output (remove in production)
echo "<!-- ENV SETTER DEBUG -->";
echo "<!-- BASE_PATH: " . htmlspecialchars(BASE_PATH) . " -->";
echo "<!-- VENDORS_PATH: " . htmlspecialchars(VENDORS_PATH) . " -->";

// Verify vendor autoload exists
$autoloadPath = VENDORS_PATH . 'autoload.php';
if (!file_exists($autoloadPath)) {
    die("❌ Vendor autoload not found at: " . htmlspecialchars($autoloadPath));
}
require_once $autoloadPath;

// Load environment variables
try {
    if (!file_exists(BASE_PATH . '/.env')) {
        throw new Exception(".env file not found at: " . BASE_PATH . '/.env');
    }

    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
    $dotenv->required(['MONGO_URI', 'PG_HOST', 'PG_PORT', 'PG_USER', 'PG_PASS', 'PG_DB']);

    // Mongo config
    $mongoConfig = [
        'uri' => $_ENV['MONGO_URI'],
        'db' => $_ENV['MONGO_DB'] ?? 'dulutandb'
    ];

    // PostgreSQL config
    $pgConfig = [
        'host' => $_ENV['PG_HOST'],
        'port' => $_ENV['PG_PORT'],
        'user' => $_ENV['PG_USER'],
        'pass' => $_ENV['PG_PASS'],
        'db'   => $_ENV['PG_DB']
    ];

} catch (Exception $e) {
    die("❌ Environment configuration failed: " . htmlspecialchars($e->getMessage()));
}