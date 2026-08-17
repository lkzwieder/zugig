<?php

// Zugig Framework - Bootstrap
// Require minimum PHP 8.0
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    die("Zugig requires PHP 8.0 or higher. Current: " . PHP_VERSION);
}

// Define ROOT if not already set
if (!defined('ROOT')) {
    define('ROOT', __DIR__);
}

// Load .env file if exists
$envFile = ROOT . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_contains($line, '=') && !str_starts_with($line, '#')) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Default constants
defined('APP_ROOT') ?: define('APP_ROOT', ROOT);
defined('DATABASES') ?: define('DATABASES', APP_ROOT . '/config/database.ini');
defined('SFTP') ?: define('SFTP', APP_ROOT . '/config/sftp.ini');

// Default minifier settings
if (!defined('CSS_MINIFIER')) {
    define('CSS_MINIFIER', $_ENV['CSS_MINIFIER'] ?? true);
}
if (!defined('JS_MINIFIER')) {
    define('JS_MINIFIER', $_ENV['JS_MINIFIER'] ?? true);
}
if (!defined('ENVIROMENT')) {
    define('ENVIROMENT', $_ENV['APP_ENV'] ?? 'production');
}

// Register autoloader
$loader = new Autoload();
$loader->addPath(APP_ROOT . '/app')
       ->addPath(APP_ROOT . '/src')
       ->addPath(APP_ROOT . '/Zugig/Classes')
       ->addPath(APP_ROOT . '/Zugig/Interfaces')
       ->addPath(APP_ROOT . '/Zugig/Traits')
       ->addPath(APP_ROOT . '/Zugig/Lib')
       ->register();
