<?php
/**
 * Actions to run when the module gets deactivated.
 *
 * @since n.e.x.t
 * @package performance-lab
 */

/**
 * Deletes the db.php file, and deactivates the module in the SQLite database.
 *
 * @since n.e.x.t
 */
return function() {
	if ( ! file_exists( WP_CONTENT_DIR . '/db.php' ) ) {
		return;
	}

	global $wp_filesystem;
	if ( ! $wp_filesystem ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
	}
	$wp_filesystem->delete( WP_CONTENT_DIR . '/db.php' );

	// Run an action on `shutdown`, to deactivate the option in the MySQL database.
	add_action(
		'shutdown',
		function() {
			global $table_prefix;

			// Get credentials for the MySQL database.
			$dbuser     = defined( 'DB_USER' ) ? DB_USER : '';
			$dbpassword = defined( 'DB_PASSWORD' ) ? DB_PASSWORD : '';
			$dbname     = defined( 'DB_NAME' ) ? DB_NAME : '';
			$dbhost     = defined( 'DB_HOST' ) ? DB_HOST : '';

			// Init a connection to the MySQL database.
			$wpdb_mysql = new wpdb( $dbuser, $dbpassword, $dbname, $dbhost );
			$wpdb_mysql->set_prefix( $table_prefix );

			// Get the perflab options, remove the database/sqlite module and update the option.
			$alloptions = $wpdb_mysql->get_results( "SELECT option_name, option_value FROM $wpdb_mysql->options WHERE autoload = 'yes'" );
			$value      = array();
			foreach ( $alloptions as $o ) {
				if ( PERFLAB_MODULES_SETTING === $o->option_name ) {
					$value = maybe_unserialize( $o->option_value );
					break;
				}
			}
			unset( $value['database/sqlite'] );
			$wpdb_mysql->update(
				$wpdb_mysql->options,
				array( 'option_value' => maybe_serialize( $value ) ),
				array( 'option_name' => PERFLAB_MODULES_SETTING )
			);
		}
	);
};
