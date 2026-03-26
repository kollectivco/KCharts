<?php
/**
 * Plugin Name: Kontentainment Charts
 * Plugin URI: https://github.com/kollectivco/KCharts
 * Description: Public-facing charts platform and control center for Kontentainment Charts.
 * Version: 1.0.0
 * Author: Kollectiv
 * License: GPL2+
 * Text Domain: kontentainment-charts
 * Update URI: https://github.com/kollectivco/KCharts
 */

if (!defined('ABSPATH')) {
	exit;
}

add_action( 'init', function() {
	if ( isset( $_GET['amc_reset_destructive_baseline'] ) && current_user_can( 'manage_options' ) ) {
		global $wpdb;
		$tables = array(
			'amc_charts', 'amc_chart_weeks', 'amc_chart_entries', 'amc_artists', 'amc_tracks', 
			'amc_albums', 'amc_platform_settings', 'amc_source_uploads', 'amc_source_rows', 
			'amc_matching_queue', 'amc_scoring_rules', 'amc_ingestion_logs', 'amc_jobs'
		);
		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . $table );
		}
		$options = array(
			'amc_db_version', 'amc_needs_rewrite_flush', 'amc_legacy_demo_cleaned', 'amc_operator_notifications'
		);
		foreach ( $options as $option ) {
			delete_option( $option );
		}
		AMC_DB::install();
		wp_die( 'Project has been destructively reset to 1.0.0. All tables cleared. <a href="' . admin_url('admin.php?page=kontentainment-charts-dashboard') . '">Return to Dashboard</a>' );
	}
});

if ( ! defined( 'AMC_LEGACY_ROUTE_BASE' ) ) {
	define( 'AMC_LEGACY_ROUTE_BASE', 'music-charts' );
}

define( 'AMC_PLUGIN_VERSION', '1.0.0' );
define( 'AMC_PLUGIN_SLUG', 'kontentainment-charts' );
define( 'AMC_PLUGIN_FILE', __FILE__ );
define( 'AMC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AMC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once AMC_PLUGIN_DIR . 'includes/admin/class-amc-admin-data.php';
require_once AMC_PLUGIN_DIR . 'includes/admin/class-amc-admin.php';
require_once AMC_PLUGIN_DIR . 'includes/class-amc-capabilities.php';
require_once AMC_PLUGIN_DIR . 'includes/class-amc-db.php';
require_once AMC_PLUGIN_DIR . 'includes/class-amc-data.php';
require_once AMC_PLUGIN_DIR . 'includes/vendor/SimpleXLSX.php';
require_once AMC_PLUGIN_DIR . 'includes/vendor/SimpleXLS.php';
require_once AMC_PLUGIN_DIR . 'includes/class-amc-ingestion.php';
require_once AMC_PLUGIN_DIR . 'includes/class-amc-seeder.php';
require_once AMC_PLUGIN_DIR . 'includes/class-amc-routing.php';
require_once AMC_PLUGIN_DIR . 'includes/class-amc-updater.php';
require_once AMC_PLUGIN_DIR . 'includes/template-tags.php';
require_once AMC_PLUGIN_DIR . 'includes/class-amc-plugin.php';

register_activation_hook(
	AMC_PLUGIN_FILE,
	function () {
		AMC_Plugin::activate();
		update_option( 'amc_needs_rewrite_flush', 1 );
	}
);

register_deactivation_hook( AMC_PLUGIN_FILE, array( 'AMC_Plugin', 'deactivate' ) );

add_action(
	'admin_init',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( get_option( 'amc_needs_rewrite_flush' ) ) {
			flush_rewrite_rules( false );
			delete_option( 'amc_needs_rewrite_flush' );
		}
	}
);

AMC_Plugin::instance();