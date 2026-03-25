<?php
/**
 * Route Handler Template
 * This file captures specific routes and renders the appropriate shortcode/view
 */

global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

$route = get_query_var('kc_route');
$slug  = get_query_var('kc_slug');
$section = get_query_var('kc_section');

// 1. Start capturing output for the main content area
get_header();

echo '<div id="primary" class="content-area kc-public-main">';
echo '<main id="main" class="site-main">';

switch ($route) {
    case 'index':
        echo do_shortcode('[kc_charts_index]');
        break;

    case 'about':
        echo do_shortcode('[kc_methodology]');
        break;

    case 'tracks':
        echo do_shortcode('[kc_top_tracks]');
        break;

    case 'artists':
        echo do_shortcode('[kc_top_artists]');
        break;

    case 'chart':
        // Render single chart by slug
        if ($slug) {
            echo do_shortcode('[kc_chart_week chart_slug="' . esc_attr($slug) . '"]');
        } else {
            echo '<p>Chart not specified.</p>';
        }
        break;

    case 'track':
        if ($slug) {
            echo do_shortcode('[kc_track slug="' . esc_attr($slug) . '"]');
        } else {
            echo '<p>Track not specified.</p>';
        }
        break;

    case 'artist':
        if ($slug) {
            echo do_shortcode('[kc_artist slug="' . esc_attr($slug) . '"]');
        } else {
            echo '<p>Artist not specified.</p>';
        }
        break;

    case 'dashboard':
        // Handle frontend dashboard
        // [kc_frontend_dashboard section="..."]
        echo '<h2>Charts Dashboard</h2>';
        echo '<p>Dashboard functionality coming soon.</p>';
        break;

    default:
        echo '<h2>404 - Not Found</h2>';
        break;
}

echo '</main></div>';

get_footer();
