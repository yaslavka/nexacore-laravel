<?php

/**
 * Local development router for the PHP built-in server.
 * Usage: php -S 127.0.0.1:8090 -t public router.php
 * Production deployments should use nginx + php-fpm (see deploy/nginx.conf).
 *
 * NOTE: theme assets are exposed through symlinks
 * (public/themes/{slug} → themes/{slug}/public), therefore we must NOT use
 * realpath()+prefix checks here — realpath() would resolve the symlink and
 * place the file outside the public root. Instead we reject traversal
 * attempts textually and let is_file() follow the symlinks.
 */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$path = rawurldecode($path);

if ($path !== '/' && $path !== ''
    && ! str_contains($path, "\0")
    && ! preg_match('#(^|/)\.\.(/|$)#', $path)
    && is_file(__DIR__.'/public'.$path)
) {
    return false; // let the built-in server handle the static file (follows symlinks)
}

require __DIR__.'/public/index.php';
