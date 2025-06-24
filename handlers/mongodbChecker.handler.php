<?php
// Ensure bootstrap constants are defined
if (!defined('BASE_PATH') || !defined('UTILS_PATH')) {
    die("❌ Bootstrap constants not defined. Check bootstrap.php inclusion.");
}

// Debug paths (remove in production)
echo "<!-- DEBUG PATHS -->";
echo "<!-- BASE_PATH: " . htmlspecialchars(BASE_PATH) . " -->";
echo "<!-- UTILS_PATH: " . htmlspecialchars(UTILS_PATH) . " -->";

// Try multiple possible locations for envSetter
$possiblePaths = [
    UTILS_PATH . 'envSetter.util.php',
    BASE_PATH . '/utils/envSetter.util.php',
    __DIR__ . '/../utils/envSetter.util.php'
];

$found = false;
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $found = true;
        break;
    }
}

if (!$found) {
    die("❌ envSetter.util.php not found in:<br>" . 
        implode("<br>", array_map('htmlspecialchars', $possiblePaths)));
}

try {
    if (!isset($mongoConfig['uri'])) {
        throw new Exception("MongoDB configuration not loaded");
    }

    $mongo = new MongoDB\Driver\Manager($mongoConfig['uri']);
    $command = new MongoDB\Driver\Command(["ping" => 1]);
    $mongo->executeCommand("admin", $command);

    echo "✅ Connected to MongoDB successfully.<br>";
    echo "<!-- Using URI: " . htmlspecialchars($mongoConfig['uri']) . " -->";
} catch (Exception $e) {
    echo "❌ MongoDB connection failed: " . htmlspecialchars($e->getMessage()) . "<br>";
    if (isset($mongoConfig['uri'])) {
        echo "<!-- Attempted URI: " . htmlspecialchars($mongoConfig['uri']) . " -->";
    }
}