<?php
/**
 * Public route registration and template resolution.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AMC_Routing {
	/**
	 * Register rewrite rules.
	 *
	 * @return void
	 */
	public static function register_routes() {
		$base   = trim( (string) AMC_ROUTE_BASE, '/' );
		$legacy = trim( (string) AMC_LEGACY_ROUTE_BASE, '/' );

		// Canonical routes
		add_rewrite_rule( '^' . preg_quote( $base, '/' ) . '/?$', 'index.php?amc_page=home', 'top' );
		add_rewrite_rule( '^' . preg_quote( $base, '/' ) . '/charts/?$', 'index.php?amc_page=charts', 'top' );
		add_rewrite_rule( '^' . preg_quote( $base, '/' ) . '/tracks/?$', 'index.php?amc_page=tracks', 'top' );
		add_rewrite_rule( '^' . preg_quote( $base, '/' ) . '/artists/?$', 'index.php?amc_page=artists', 'top' );
		add_rewrite_rule( '^' . preg_quote( $base, '/' ) . '/about/?$', 'index.php?amc_page=about', 'top' );
		add_rewrite_rule( '^' . preg_quote( $base, '/' ) . '/charts/([^/]+)/?$', 'index.php?amc_page=chart&amc_chart=$matches[1]', 'top' );
		add_rewrite_rule( '^' . preg_quote( $base, '/' ) . '/track/([^/]+)/?$', 'index.php?amc_page=track&amc_track=$matches[1]', 'top' );
		add_rewrite_rule( '^' . preg_quote( $base, '/' ) . '/artist/([^/]+)/?$', 'index.php?amc_page=artist&amc_artist=$matches[1]', 'top' );

		// Dashboard routes (Canonical)
		add_rewrite_rule( '^charts-dashboard/?$', 'index.php?amc_page=dashboard&amc_dashboard=dashboard', 'top' );
		add_rewrite_rule( '^charts-dashboard/([^/]+)/?$', 'index.php?amc_page=dashboard&amc_dashboard=$matches[1]', 'top' );

		// Legacy aliases kept temporarily so old URLs still resolve before redirect
		add_rewrite_rule( '^' . preg_quote( $legacy, '/' ) . '/?$', 'index.php?amc_legacy=1&amc_page=home', 'top' );
		add_rewrite_rule( '^' . preg_quote( $legacy, '/' ) . '/tracks/?$', 'index.php?amc_legacy=1&amc_page=tracks', 'top' );
		add_rewrite_rule( '^' . preg_quote( $legacy, '/' ) . '/artists/?$', 'index.php?amc_legacy=1&amc_page=artists', 'top' );
		add_rewrite_rule( '^' . preg_quote( $legacy, '/' ) . '/about/?$', 'index.php?amc_legacy=1&amc_page=about', 'top' );
		add_rewrite_rule( '^' . preg_quote( $legacy, '/' ) . '/track/([^/]+)/?$', 'index.php?amc_legacy=1&amc_page=track&amc_track=$matches[1]', 'top' );
		add_rewrite_rule( '^' . preg_quote( $legacy, '/' ) . '/artist/([^/]+)/?$', 'index.php?amc_legacy=1&amc_page=artist&amc_artist=$matches[1]', 'top' );
		add_rewrite_rule( '^' . preg_quote( $legacy, '/' ) . '/([^/]+)/?$', 'index.php?amc_legacy=1&amc_page=chart&amc_chart=$matches[1]', 'top' );
	}

	/**
	 * Register query vars.
	 *
	 * @param array $vars Query vars.
	 * @return array
	 */
	public static function register_query_vars( $vars ) {
		$vars[] = 'amc_page';
		$vars[] = 'amc_chart';
		$vars[] = 'amc_track';
		$vars[] = 'amc_artist';
		$vars[] = 'amc_dashboard';
		$vars[] = 'amc_legacy';

		return $vars;
	}

	/**
	 * Whether current request belongs to plugin.
	 *
	 * @return bool
	 */
	public static function is_plugin_route() {
		return (bool) get_query_var( 'amc_page' );
	}

	/**
	 * Route metadata for title/body state.
	 *
	 * @return array
	 */
	public static function get_route_context() {
		$route = get_query_var( 'amc_page' );

		switch ( $route ) {
			case 'home':
				return array( 'title' => 'Kontentainment Charts' );
			case 'charts':
				return array( 'title' => 'Charts Index' );
			case 'tracks':
				return array( 'title' => 'All Tracks' );
			case 'artists':
				return array( 'title' => 'All Artists' );
			case 'about':
				return array( 'title' => 'About Charts' );
			case 'chart':
				$chart = AMC_Data::get_chart( get_query_var( 'amc_chart' ) );
				return array( 'title' => $chart ? $chart['title'] : 'Chart' );
			case 'track':
				$track = AMC_Data::get_track_by_slug( get_query_var( 'amc_track' ) );
				return array( 'title' => $track ? $track['name'] : 'Track' );
			case 'artist':
				$artist = AMC_Data::get_artist_by_slug( get_query_var( 'amc_artist' ) );
				return array( 'title' => $artist ? $artist['name'] : 'Artist' );
			case 'dashboard':
				$sections = AMC_Admin_Data::dashboard_sections();
				$key      = get_query_var( 'amc_dashboard' ) ? get_query_var( 'amc_dashboard' ) : 'dashboard';
				return array( 'title' => ! empty( $sections[ $key ]['title'] ) ? 'Kontentainment Charts - ' . $sections[ $key ]['title'] : 'Kontentainment Charts - Dashboard' );
			default:
				return array( 'title' => get_bloginfo( 'name' ) );
		}
	}

	/**
	 * Swap in plugin templates for plugin routes.
	 *
	 * @param string $template Existing template.
	 * @return string
	 */
	public static function template_include( $template ) {
		$route = get_query_var( 'amc_page' );

		if ( ! $route ) {
			return $template;
		}

		switch ( $route ) {
			case 'home':
				return AMC_PLUGIN_DIR . 'templates/home.php';
			case 'charts':
				return AMC_PLUGIN_DIR . 'templates/charts-index.php';
			case 'tracks':
				return AMC_PLUGIN_DIR . 'templates/tracks-index.php';
			case 'artists':
				return AMC_PLUGIN_DIR . 'templates/artists-index.php';
			case 'about':
				return AMC_PLUGIN_DIR . 'templates/about.php';
			case 'chart':
				return AMC_PLUGIN_DIR . 'templates/chart-page.php';
			case 'track':
				return AMC_PLUGIN_DIR . 'templates/track-single.php';
			case 'artist':
				return AMC_PLUGIN_DIR . 'templates/artist-single.php';
			case 'dashboard':
				return AMC_PLUGIN_DIR . 'templates/dashboard.php';
			default:
				return $template;
		}
	}

	/**
	 * Safe legacy-to-canonical redirect.
	 *
	 * @return void
	 */
	public static function maybe_redirect_legacy_routes() {
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		$legacy_flag = (int) get_query_var( 'amc_legacy' );
		if ( ! $legacy_flag ) {
			return;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( '' === $request_uri ) {
			return;
		}

		$legacy = '/' . trim( (string) AMC_LEGACY_ROUTE_BASE, '/' );
		$base   = '/' . trim( (string) AMC_ROUTE_BASE, '/' );

		if ( 0 !== strpos( $request_uri, $legacy ) ) {
			return;
		}

		$target = $base . substr( $request_uri, strlen( $legacy ) );
		$target = home_url( $target );

		wp_safe_redirect( $target, 301 );
		exit;
	}
}

