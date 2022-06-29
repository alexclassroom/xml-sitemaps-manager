<?php
/**
 * WP Sitemaps Manager uninstallation.
 *
 * @since 0.1
 */

// Exit if uninstall not called from WordPress.
defined('WP_UNINSTALL_PLUGIN') || exit();

/**
 * Class XMLSitemapFeed_Uninstall
 *
 * @since 0.1
 */
class XMLSitemapFeed_Uninstall {

	/**
	 * Constructor: manages uninstall for singe and multisite.
	 *
	 * @since 0.1
	 */
	function __construct()
	{
		global $wpdb;

		// Check if it is a multisite and not a large one.
		if ( is_multisite() ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Clearing XML Sitemap Feeds settings from each site before uninstall:');
			}
			$field = 'blog_id';
			$table = $wpdb->prefix.'blogs';
			$blog_ids = $wpdb->get_col("SELECT {$field} FROM {$table}");
			if ( count( $blog_ids ) > 10000 ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'Aborting multisite uninstall. Too many sites in your network.');
				}
				$this->uninstall();
				return;
			}
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				$this->uninstall( $blog_id);
			}
			restore_current_blog();
		} else {
			$this->uninstall();
		}
	}

	/**
	 * Remove plugin data.
	 *
	 * @since 0.1
	 */
	function uninstall( $blog_id = false )
	{
		global $wpdb;

		/**
		 * Remove metadata.
		 */
	  	// Terms meta.
	  	$wpdb->delete( $wpdb->prefix.'termmeta', array( 'meta_key' => 'term_modified_gmt' ) );
	  	// User meta.
	  	$wpdb->delete( $wpdb->prefix.'usermeta', array( 'meta_key' => 'user_modified_gmt' ) );

		/**
		 * Remove plugin settings.
		 */
		delete_option('wpsm_sitemaps_enabled');
		delete_option('wpsm_sitemaps_fixes');
		delete_option('wpsm_sitemaps_max_urls');
		delete_option('wpsm_sitemaps_lastmod');
		delete_option('wpsm_sitemap_providers');
		delete_option('wpsm_disabled_subtypes');

		// Kilroy was here
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			if ( $blog_id )
				error_log( 'XML Sitemap Feeds settings cleared for blog ID:' . $blog_id );
			else
				error_log( 'XML Sitemap Feeds settings cleared on uninstall.' );
		}
	}
}

new XMLSitemapFeed_Uninstall();