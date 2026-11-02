<?php

/**
 * Local development router for the PHP built-in server.
 * Usage: php -S 127.0.0.1:8090 -t public router.php
 * Production deployments should use nginx + php-fpm (see deploy/nginx.conf).
 */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Serve real static assets directly from public/.
if ($path !== '/' && $path !== '') {
    $file = realpath(__DIR__.'/public'.$path);
    $root = realpath(__DIR__.'/public');
    if ($file !== false && $root !== false && str_starts_with($file, $root.DIRECTORY_SEPARATOR) && is_file($file)) {
        return false; // let the built-in server handle the static file
    }
}

require __DIR__.'/public/index.php';
