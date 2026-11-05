<?php
/**
 * Router for PHP's built-in web server (local development only):
 *   php -S localhost:8092 setup/router.php
 *
 * Real files (CSS, JS, images, wp-admin scripts) are served as-is; every other
 * URL goes to WordPress so pretty permalinks (/jobs/) work without Apache/nginx.
 */

if ( PHP_SAPI !== 'cli-server' ) {
	http_response_code( 404 );
	exit;
}

$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

if ( '/' !== $path && file_exists( $_SERVER['DOCUMENT_ROOT'] . urldecode( $path ) ) ) {
	return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
require $_SERVER['DOCUMENT_ROOT'] . '/index.php';
