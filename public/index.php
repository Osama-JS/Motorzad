<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Normalize SCRIPT_NAME casing to match REQUEST_URI on Windows (e.g. /Motorzad vs /motorzad)
if (isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    if ($scriptDir !== '/' && $scriptDir !== '.') {
        $len = strlen($scriptDir);
        if (strncasecmp($_SERVER['REQUEST_URI'], $scriptDir, $len) === 0) {
            $_SERVER['SCRIPT_NAME'] = substr($_SERVER['REQUEST_URI'], 0, $len) . substr($_SERVER['SCRIPT_NAME'], $len);
        }
    }
}

$app->handleRequest(Request::capture());

