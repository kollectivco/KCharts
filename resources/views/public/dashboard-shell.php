<?php
/**
 * Kontentainment Charts External Dashboard Shell
 * This view provides an operator-facing admin experience OUTSIDE of wp-admin.
 */

global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

if ( ! is_user_logged_in() || ! current_user_can('edit_kc_chart_data') ) {
    wp_die('Unauthorized. This dashboard requires an operator account.');
}

$section = get_query_var('kc_section', 'overview');

// 1. Snapshot Data for Sidebar/Top Nav
$user = wp_get_current_user();
$pending_count = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}upload_data WHERE status = 'pending_review'");
$latest_upload = $wpdb->get_row("SELECT * FROM {$prefix}uploads ORDER BY id DESC LIMIT 1");

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        .kc-dash-nav-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #555;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .kc-dash-nav-item:hover, .kc-dash-nav-item.active {
            background: #111;
            color: #fff;
        }
        .kc-dash-badge {
            margin-left: auto;
            background: #eee;
            color: #111;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
        }
        .kc-dash-nav-item.active .kc-dash-badge {
            background: #333;
            color: #fff;
        }
    </style>
</head>
<body <?php body_class(); ?>>

<div class="kc-dash-shell">
    
    <!-- Sidebar -->
    <aside class="kc-dash-sidebar">
        <div style="margin-bottom: 40px;">
            <h2 style="font-weight: 900; letter-spacing: -1px; margin:0;">K-Charts</h2>
            <p style="font-size: 0.7rem; color: #888; text-transform: uppercase; font-weight: 800;">External Dash v1.1.1</p>
        </div>

        <nav>
            <a href="<?php echo home_url('/charts-dashboard/'); ?>" class="kc-dash-nav-item <?php echo ($section==='overview' || !$section)?'active':''; ?>">Overview</a>
            
            <a href="<?php echo home_url('/charts-dashboard/uploads/'); ?>" class="kc-dash-nav-item <?php echo ($section==='uploads')?'active':''; ?>">
                Recent Uploads
            </a>
            
            <a href="<?php echo home_url('/charts-dashboard/review/'); ?>" class="kc-dash-nav-item <?php echo ($section==='review')?'active':''; ?>">
                Review Queue
                <?php if ($pending_count) : ?>
                    <span class="kc-dash-badge"><?php echo (int)$pending_count; ?></span>
                <?php endif; ?>
            </a>
            
            <a href="<?php echo home_url('/charts-dashboard/charts/'); ?>" class="kc-dash-nav-item <?php echo ($section==='charts')?'active':''; ?>">Chart Status</a>
            
            <a href="<?php echo home_url('/charts-dashboard/status/'); ?>" class="kc-dash-nav-item <?php echo ($section==='status')?'active':''; ?>">System Status</a>
        </nav>

        <div style="margin-top: auto; padding-top: 40px; border-top: 1px solid #eee;">
            <p style="font-size: 0.8rem; color: #888;">Logged in as:</p>
            <p style="font-weight: 700; margin: 0;"><?php echo esc_html($user->display_name); ?></p>
            <a href="<?php echo wp_logout_url(home_url('/charts/')); ?>" style="font-size: 0.7rem; text-decoration: underline; color: #cc0000; margin-top: 5px; display: block;">Logout</a>
        </div>
    </aside>

    <!-- Content Area -->
    <main class="kc-dash-content">
        <?php
        switch ($section) {
            case 'uploads':
                include KC_DIR . 'resources/views/public/dashboard-uploads.php';
                break;
            case 'review':
                include KC_DIR . 'resources/views/public/dashboard-review.php';
                break;
            case 'charts':
                include KC_DIR . 'resources/views/public/dashboard-charts.php';
                break;
            case 'status':
                include KC_DIR . 'resources/views/public/dashboard-status.php';
                break;
            case 'overview':
            default:
                include KC_DIR . 'resources/views/public/dashboard-overview.php';
                break;
        }
        ?>
    </main>

</div>

<?php wp_footer(); ?>
</body>
</html>
