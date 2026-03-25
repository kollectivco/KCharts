<?php
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

$route = get_query_var('kc_route');
$slug = get_query_var('kc_slug');
$section = get_query_var('kc_section');

// 1. Get Global Context (Latest Week, Active Charts)
$active_charts = $wpdb->get_results("SELECT * FROM {$prefix}charts WHERE status = 'active' ORDER BY name ASC");

get_header();

switch ($route) {
    case 'index':
        echo do_shortcode('[kc_charts_index]');
        break;

    case 'chart':
        // Resolve slug to latest published week if only slug provided
        echo do_shortcode('[kc_chart_week chart_slug="' . esc_attr($slug) . '"]');
        break;

    case 'tracks':
        echo do_shortcode('[kc_top_tracks]');
        break;

    case 'artists':
        echo_shortcode('[kc_top_artists]'); // Need to fix this function call typo
        break;
    
    // Actually using proper calls below:
    case 'artist':
        echo do_shortcode('[kc_artist slug="' . esc_attr($slug) . '"]');
        break;

    case 'track':
        echo do_shortcode('[kc_track slug="' . esc_attr($slug) . '"]');
        break;

    case 'about':
        echo do_shortcode('[kc_methodology]');
        break;

    case 'dashboard':
        include KC_DIR . 'resources/views/public/dashboard-shell.php';
        break;

    default:
        echo '<div class="kc-public-wrap"><div class="kc-empty-state"><h3>Page Not Found</h3><p>The requested chart resource could not be located.</p></div></div>';
        break;
}

get_footer();
