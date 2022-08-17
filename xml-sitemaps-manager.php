<?php
/*
Plugin Name: XML Sitemaps Manager
Plugin URI: https://status301.net/wordpress-plugins/xml-sitemaps-manager/
Description: Fix some bugs and add new options to manage the WordPress core XML Sitemaps. Happy with the results? Please leave me a <strong><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=XML%20Sitemaps%20Manager">tip</a></strong> for continued development and support. Thanks :)
Text Domain: xml-sitemaps-manager
Version: 0.4-beta1
Requires at least: 5.5
Requires PHP: 5.6
Author: RavanH
Author URI: https://status301.net/
*/

defined( 'WPINC' ) || die;

define( 'WPSM_VERSION', '0.3' );

/**
 * Plugin intitialization.
 *
 * Must run before priority 10 for the wp_sitemaps_add_provider filter to work.
 * Can be disabled with remove_action('init','xmlsm_init',9).
 *
 * @since 0.3
 */
function xmlsm_init() {
	// Load WP core sitemaps manager.
	require_once __DIR__ . '/includes/class.xml-sitemaps-manager.php';
	new XML_Sitemaps_Manager;
}
add_action( 'init', 'xmlsm_init', 9 );

/**
 * Plugin admin intitialization.
 *
 * Can be disabled with remove_action('admin_init','xmlsm_admin_init').
 *
 * @since 0.3
 */
function xmlsm_admin_init() {
	define( 'WPSM_BASENAME', plugin_basename(__FILE__) );

	require_once __DIR__ . '/includes/class.xml-sitemaps-manager-admin.php';
	new XML_Sitemaps_Manager_Admin;
}
add_action( 'admin_init', 'xmlsm_admin_init' );

/**
 * Sitemaps fixes.
 *
 * @since 0.3
 */
add_action(
	'plugins_loaded',
	function() {
		if ( get_option( 'xmlsm_sitemaps_fixes', true ) ) {
			include_once __DIR__ . '/includes/wp-sitemaps-fixes.php';
		}
	},
	0
);

/**
 * Plugin updater.
 *
 * @since 0.3
 */
add_action(
	'init',
	function() {
		// Maybe upgrade or install.
		$db_version = get_option( 'xmlsm_version', null );
		if ( ! version_compare( WPSM_VERSION, $db_version, '=' ) ) {
			include_once __DIR__ . '/upgrade.php';
		}
	},
	9
);

/*
add_action( 'shutdown', function( $provider )
{
	global $wp_filter;
	$filter = 'wp_sitemaps_add_provider';
	error_log( 'checking for filter ' . $filter . ':' );
	if ( isset($wp_filter[$filter]) )
		error_log( print_r( $wp_filter[$filter], true ) );
	else
		error_log( 'filter ' . $filter . ' not found' );

}, 999 );
*/
