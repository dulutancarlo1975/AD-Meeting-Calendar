<?php
// Define important paths
define('BASE_PATH', realpath(__DIR__));
define('UTILS_PATH', BASE_PATH . '/utils/');
define('VENDORS_PATH', BASE_PATH . '/vendor/');
define('HANDLERS_PATH', BASE_PATH . '/handlers/');
define('STATICDATAS_PATH', BASE_PATH . '/staticDatas/');
define('DUMMIES_PATH', STATICDATAS_PATH . 'dummies/');

// Change current working directory
chdir(BASE_PATH);
