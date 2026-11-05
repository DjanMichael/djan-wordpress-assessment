<?php
/**
 * One-step local install: downloads WordPress, creates the database and
 * wp-config.php, installs the site, activates the theme and adds the demo content.
 *
 *   php setup/install.php
 *   php setup/install.php --db-user=root --db-pass=secret --db-port=3306
 *
 * Then start the site from this folder with:
 *   php -S localhost:8092 setup/router.php
 *
 * Options (all optional):
 *   --db-host=127.0.0.1   --db-port=3306   --db-user=root   --db-pass=
 *   --db-name=wpa_backend   --url=http://localhost:8092
 *   --admin-user=admin   --admin-pass=<random>   --admin-email=admin@example.com
 *   (on a site that is already installed, --admin-pass sets a new password for --admin-user)
 *   --zip=path/to/wordpress.zip   (use a downloaded WordPress zip instead of fetching it)
 *
 * Safe to re-run: existing WordPress files, wp-config.php and an installed site are kept.
 */

if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 404 );
	exit;
}

const INSTALLER_THEME   = 'jobs-board';
const INSTALLER_TITLE   = 'Essex Digital Careers';
const INSTALLER_DB_NAME = 'wpa_backend';
const INSTALLER_URL     = 'http://localhost:8092';

/**
 * Prints a progress line.
 *
 * @param string $message Text.
 */
function installer_say( $message ) {
	fwrite( STDOUT, $message . PHP_EOL );
}

/**
 * Prints an error and stops.
 *
 * @param string $message Text.
 */
function installer_fail( $message ) {
	fwrite( STDERR, PHP_EOL . 'Error: ' . $message . PHP_EOL );
	exit( 1 );
}

/**
 * Command-line options merged over the defaults.
 *
 * @return array<string, string>
 */
function installer_options() {
	$long = array( 'help' );
	foreach ( array( 'db-host', 'db-port', 'db-user', 'db-pass', 'db-name', 'url', 'admin-user', 'admin-pass', 'admin-email', 'zip' ) as $key ) {
		$long[] = $key . ':';
	}
	$given = getopt( '', $long );

	if ( isset( $given['help'] ) ) {
		$source = file_get_contents( __FILE__ );
		preg_match( '#/\*\*(.*?)\*/#s', $source, $doc );
		fwrite( STDOUT, preg_replace( '/^\s*\* ?/m', '', $doc[1] ) );
		exit( 0 );
	}

	return array_merge(
		array(
			'db-host'     => '127.0.0.1',
			'db-port'     => '3306',
			'db-user'     => 'root',
			'db-pass'     => '',
			'db-name'     => INSTALLER_DB_NAME,
			'url'         => INSTALLER_URL,
			'admin-user'  => 'admin',
			'admin-pass'  => '',
			'admin-email' => 'admin@example.com',
			'zip'         => '',
		),
		array_map( 'strval', $given )
	);
}

/**
 * Downloads a URL to a file (PHP streams first, then cURL).
 *
 * @param string $url  Source URL.
 * @param string $file Destination path.
 * @return bool
 */
function installer_download( $url, $file ) {
	$context = stream_context_create( array( 'http' => array( 'user_agent' => 'wordpress-assessment-installer' ) ) );
	if ( ini_get( 'allow_url_fopen' ) && @copy( $url, $file, $context ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- falls back to cURL below.
		return true;
	}

	if ( function_exists( 'curl_init' ) ) {
		$handle = fopen( $file, 'wb' );
		$curl   = curl_init( $url );
		curl_setopt_array(
			$curl,
			array(
				CURLOPT_FILE           => $handle,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_FAILONERROR    => true,
				CURLOPT_USERAGENT      => 'wordpress-assessment-installer',
			)
		);
		$ok = curl_exec( $curl );
		curl_close( $curl );
		fclose( $handle );

		return (bool) $ok;
	}

	return false;
}

/**
 * Puts WordPress core into $root: everything in the zip except wp-content/.
 *
 * @param string $root WordPress root (the project folder).
 * @param string $zip  Optional local WordPress zip.
 */
function installer_install_core( $root, $zip ) {
	if ( ! class_exists( 'ZipArchive' ) ) {
		installer_fail( 'PHP\'s zip extension is missing. Enable "extension=zip" in php.ini, or copy WordPress core in by hand (see README.md).' );
	}

	if ( '' === $zip ) {
		installer_say( '- Downloading WordPress from wordpress.org...' );
		$zip      = tempnam( sys_get_temp_dir(), 'wp' );
		$checksum = tempnam( sys_get_temp_dir(), 'wp' );

		if ( ! installer_download( 'https://wordpress.org/latest.zip', $zip ) || ! installer_download( 'https://wordpress.org/latest.zip.sha1', $checksum ) ) {
			installer_fail( 'Could not download WordPress. Download https://wordpress.org/latest.zip yourself and run again with --zip=path/to/latest.zip' );
		}

		if ( sha1_file( $zip ) !== trim( file_get_contents( $checksum ) ) ) {
			installer_fail( 'The downloaded WordPress zip failed its SHA-1 check. Please try again.' );
		}
		unlink( $checksum );
		$downloaded = true;
	} elseif ( ! is_file( $zip ) ) {
		installer_fail( "No file at --zip={$zip}" );
	}

	$archive = new ZipArchive();
	if ( true !== $archive->open( $zip ) ) {
		installer_fail( 'Could not open the WordPress zip.' );
	}

	installer_say( '- Copying WordPress core files (your theme in wp-content is left alone)...' );
	for ( $i = 0; $i < $archive->numFiles; $i++ ) {
		$name     = $archive->getNameIndex( $i );
		$relative = substr( $name, strlen( 'wordpress/' ) );

		if ( 0 !== strpos( $name, 'wordpress/' ) || '' === $relative || 0 === strpos( $relative, 'wp-content/' ) || false !== strpos( $relative, '..' ) ) {
			continue;
		}

		$target = $root . '/' . $relative;
		if ( '/' === substr( $name, -1 ) ) {
			is_dir( $target ) || mkdir( $target, 0755, true );
			continue;
		}

		is_dir( dirname( $target ) ) || mkdir( dirname( $target ), 0755, true );
		file_put_contents( $target, $archive->getFromIndex( $i ) );
	}
	$archive->close();

	if ( ! empty( $downloaded ) ) {
		unlink( $zip );
	}
}

/**
 * Creates the database if needed.
 *
 * @param array<string, string> $options Installer options.
 */
function installer_create_database( $options ) {
	if ( ! extension_loaded( 'mysqli' ) ) {
		installer_fail( 'PHP\'s mysqli extension is missing. Enable "extension=mysqli" in php.ini.' );
	}

	if ( ! preg_match( '/^[A-Za-z0-9_]+$/', $options['db-name'] ) ) {
		installer_fail( '--db-name may only use letters, numbers and underscores.' );
	}

	mysqli_report( MYSQLI_REPORT_OFF );
	$db = @new mysqli( $options['db-host'], $options['db-user'], $options['db-pass'], '', (int) $options['db-port'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- reported below.

	if ( $db->connect_errno ) {
		installer_fail( "Can't connect to MySQL at {$options['db-host']}:{$options['db-port']} as \"{$options['db-user']}\" ({$db->connect_error}).\nIs MySQL running? Pass your details, e.g. --db-user=root --db-pass=root --db-port=8889" );
	}

	if ( ! $db->query( "CREATE DATABASE IF NOT EXISTS `{$options['db-name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" ) ) {
		installer_fail( "Could not create the database {$options['db-name']}: {$db->error}" );
	}
	$db->close();
}

/**
 * Writes wp-config.php for this local install.
 *
 * @param string                $root    WordPress root.
 * @param array<string, string> $options Installer options.
 */
function installer_write_config( $root, $options ) {
	$url    = rtrim( $options['url'], '/' );
	$values = array(
		'DB_NAME'     => $options['db-name'],
		'DB_USER'     => $options['db-user'],
		'DB_PASSWORD' => $options['db-pass'],
		'DB_HOST'     => $options['db-host'] . ':' . $options['db-port'],
		'DB_CHARSET'  => 'utf8mb4',
		'DB_COLLATE'  => '',
	);
	foreach ( array( 'AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT' ) as $key ) {
		$values[ $key ] = bin2hex( random_bytes( 32 ) );
	}
	$values += array(
		'WP_HOME'             => $url,
		'WP_SITEURL'          => $url,
		'WP_ENVIRONMENT_TYPE' => 'local',
		'WP_DEBUG'            => true,
		'WP_DEBUG_LOG'        => true,
		'WP_DEBUG_DISPLAY'    => false,
	);

	$config = "<?php\n// Local install created by setup/install.php. Not for production.\n\n";
	foreach ( $values as $name => $value ) {
		$config .= sprintf( "define( '%s', %s );\n", $name, var_export( $value, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export -- writes PHP source.
	}
	$config .= "\n\$table_prefix = 'wp_';\n\nif ( ! defined( 'ABSPATH' ) ) {\n\tdefine( 'ABSPATH', __DIR__ . '/' );\n}\n\nrequire_once ABSPATH . 'wp-settings.php';\n";

	file_put_contents( $root . '/wp-config.php', $config );
}

// Don't try to email the "new site" notice from a command-line install.
function wp_new_blog_notification() {}

// ---------------------------------------------------------------------------

if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
	installer_fail( 'PHP 7.4 or newer is needed; this is PHP ' . PHP_VERSION . '.' );
}

$installer_root       = dirname( __DIR__ );
$installer_options    = installer_options();
$installer_pass_given = '' !== $installer_options['admin-pass'];
if ( ! $installer_pass_given ) {
	$installer_options['admin-pass'] = bin2hex( random_bytes( 6 ) );
}
$installer_url     = parse_url( rtrim( $installer_options['url'], '/' ) );

if ( empty( $installer_url['host'] ) ) {
	installer_fail( "--url must look like http://localhost:8092 (got {$installer_options['url']})." );
}
$installer_host = $installer_url['host'] . ( isset( $installer_url['port'] ) ? ':' . $installer_url['port'] : '' );

installer_say( 'Installing ' . INSTALLER_TITLE . " into {$installer_root}" );

if ( ! is_file( $installer_root . '/wp-load.php' ) ) {
	installer_install_core( $installer_root, $installer_options['zip'] );
} else {
	installer_say( '- WordPress core is already here.' );
}

if ( ! is_file( $installer_root . '/wp-config.php' ) ) {
	installer_say( "- Creating database {$installer_options['db-name']} and wp-config.php..." );
	installer_create_database( $installer_options );
	installer_write_config( $installer_root, $installer_options );
} else {
	installer_say( '- Using the existing wp-config.php (the --db-* and --url options are ignored).' );
}

// Load WordPress in install mode.
define( 'WP_INSTALLING', true );
$_SERVER['HTTP_HOST']   = $installer_host;
$_SERVER['SERVER_NAME'] = $installer_url['host'];
$_SERVER['REQUEST_URI'] = '/';
require $installer_root . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

if ( is_blog_installed() && $installer_pass_given ) {
	$installer_user = get_user_by( 'login', $installer_options['admin-user'] );
	if ( ! $installer_user ) {
		installer_fail( "There is no user called \"{$installer_options['admin-user']}\". Use --admin-user=NAME to pick the account." );
	}
	wp_set_password( $installer_options['admin-pass'], $installer_user->ID );
	installer_say( "- WordPress is already installed; set a new password for {$installer_options['admin-user']}." );
} elseif ( is_blog_installed() ) {
	installer_say( '- WordPress is already installed (admin login unchanged; add --admin-pass=NEW to change it).' );
	$installer_options['admin-pass'] = '(unchanged)';
} else {
	installer_say( '- Installing WordPress...' );
	// Visible to search engines: the site only runs locally, and SEO audits (e.g. Lighthouse) then see the real setup.
	$installer_result = wp_install( INSTALLER_TITLE, $installer_options['admin-user'], $installer_options['admin-email'], true, '', wp_slash( $installer_options['admin-pass'] ) );
	if ( empty( $installer_result['user_id'] ) || is_wp_error( $installer_result['user_id'] ) ) {
		installer_fail( 'WordPress could not be installed.' );
	}
}

if ( ! wp_get_theme( INSTALLER_THEME )->exists() ) {
	installer_fail( 'Theme folder wp-content/themes/' . INSTALLER_THEME . ' is missing.' );
}
switch_theme( INSTALLER_THEME );
installer_say( '- Activated the ' . wp_get_theme( INSTALLER_THEME )->get( 'Name' ) . ' theme.' );

// The demo content needs the theme loaded, so it runs in a fresh PHP process.
installer_say( '- Adding demo content...' );
$installer_process = proc_open( array( PHP_BINARY, __DIR__ . '/demo-content.php' ), array( 1 => STDOUT, 2 => STDERR ), $installer_pipes );
if ( ! is_resource( $installer_process ) || 0 !== proc_close( $installer_process ) ) {
	installer_fail( 'Adding the demo content failed (see above). You can retry with: php setup/demo-content.php' );
}

// Serve on the site's real address (the one in wp-config.php on a re-run).
$installer_site  = untrailingslashit( home_url() );
$installer_parts = wp_parse_url( $installer_site );
$installer_host  = $installer_parts['host'] . ( isset( $installer_parts['port'] ) ? ':' . $installer_parts['port'] : '' );
installer_say(
	PHP_EOL . 'Done! Start the site from this folder:' . PHP_EOL
	. "    cd \"{$installer_root}\"" . PHP_EOL
	. "    php -S {$installer_host} setup/router.php" . PHP_EOL . PHP_EOL
	. "Then open {$installer_site}/" . PHP_EOL
	. "WP Admin:  {$installer_site}/wp-admin/  (user: {$installer_options['admin-user']}, password: {$installer_options['admin-pass']})"
);
