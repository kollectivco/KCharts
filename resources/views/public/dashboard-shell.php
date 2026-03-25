<?php
/**
 * Kontentainment Charts V1 Dashboard Shell
 * Rebuilt from Old Plugin dashboard.php and AMC_Admin::render_custom_dashboard() logic.
 */

global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

if ( ! is_user_logged_in() || ! current_user_can('edit_kc_chart_data') ) {
    wp_die('Unauthorized. This dashboard requires an operator account.');
}

$section = get_query_var('kc_section', 'overview');
$user = wp_get_current_user();
$pending_count = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}upload_data WHERE status = 'pending_review'");

// Dashboard Sections (Transfer from old logic)
$dash_sections = [
    'overview' => 'Main Workspace',
    'uploads'  => 'Recent Uploads',
    'review'   => 'Review Queue',
    'charts'   => 'Chart Status',
    'status'   => 'System Status'
];

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        body.kc-dash-body { background: #f8f8f8; color: #111; margin:0; padding:0; }
        .kc-dash-shell { display: flex; min-height: 100vh; }
        
        /* Sidebar: Transfer from old amc-custom-dashboard__sidebar */
        .kc-dash-sidebar { 
            width: 280px; 
            background: #fff; 
            border-right: 1px solid #eee; 
            padding: 40px 30px; 
            display: flex; 
            flex-direction: column; 
            position: fixed;
            height: 100vh;
        }
        
        .kc-dash-brand { display: flex; align-items: center; gap: 15px; margin-bottom: 50px; }
        .kc-dash-brand span { width:32px; height:32px; background:#111; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:900; border-radius:4px; font-size: 0.8rem;}
        .kc-dash-brand strong { display:block; font-size: 0.9rem; letter-spacing: -0.5px; }
        .kc-dash-brand small { font-size: 0.7rem; color: #888; font-weight: 800; text-transform: uppercase;}

        .kc-dash-nav-item { 
            display: flex; align-items: center; padding: 12px 15px; color: #555; text-decoration: none; border-radius: 8px; margin-bottom: 5px; font-weight: 700; font-size: 0.9rem;
        }
        .kc-dash-nav-item:hover { background: #f5f5f5; color: #111; }
        .kc-dash-nav-item.active { background: #111; color: #fff; }
        
        .kc-dash-badge { margin-left: auto; background: #eee; color: #111; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; }
        .kc-dash-nav-item.active .kc-dash-badge { background: #333; color: #fff; }

        /* Main Content */
        .kc-dash-main { flex-grow: 1; margin-left: 280px; padding: 0; display: flex; flex-direction: column; }
        
        /* Topbar: Transfer from old amc-admin-topbar */
        .kc-dash-topbar { 
            background:#fff; border-bottom:1px solid #eee; padding: 40px 60px; display: flex; justify-content: space-between; align-items: flex-end; sticky: top; z-index: 100;
        }
        .kc-dash-topbar h1 { margin: 10px 0 0 0; font-size: 2.2rem; font-weight: 900; letter-spacing: -1.5px; }
        .kc-dash-kicker { margin:0; font-size: 0.7rem; color: #888; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .kc-dash-subcopy { margin: 10px 0 0 0; color: #666; font-size: 0.95rem; max-width: 600px; line-height: 1.5;}

        .kc-dash-content-inner { padding: 60px; max-width: 1400px; }
        
        @media (max-width: 1024px) {
            .kc-dash-sidebar { width: 100%; height: auto; position: relative; border-right: none; border-bottom: 1px solid #eee; }
            .kc-dash-main { margin-left: 0; }
            .kc-dash-topbar { padding: 30px; flex-direction: column; align-items: flex-start; gap: 20px; }
        }
    </style>
</head>
<body class="kc-dash-body <?php echo esc_attr($section); ?>-dash-page">

<div class="kc-dash-shell">
    
    <!-- Sidebar -->
    <aside class="kc-dash-sidebar">
        <div class="kc-dash-brand">
            <span>KC</span>
            <div>
                <strong>Kontentainment Charts</strong>
                <small>Main workspace</small>
            </div>
        </div>

        <nav>
            <?php foreach ($dash_sections as $key => $label) : ?>
                <a href="<?php echo home_url('/charts-dashboard/'.$key.'/'); ?>" class="kc-dash-nav-item <?php echo ($section === $key || ($section === 'overview' && $key === 'overview')) ? 'active' : ''; ?>">
                    <?php echo esc_html($label); ?>
                    <?php if ($key === 'review' && $pending_count) : ?>
                        <span class="kc-dash-badge"><?php echo (int)$pending_count; ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div style="margin-top: auto; padding-top: 40px; border-top: 1px solid #eee;">
             <a class="kc-dash-nav-item" href="<?php echo admin_url('admin.php?page=kontentainment-charts'); ?>" style="font-size: 0.75rem; color: #888;">Open wp-admin control layer</a>
             <div style="margin-top: 20px; display:flex; align-items:center; gap: 12px;">
                  <div style="width: 32px; height:32px; background: #eee; border-radius: 50%;"></div>
                  <div style="flex-grow:1;">
                       <p style="font-weight: 700; margin: 0; font-size: 0.85rem;"><?php echo esc_html($user->display_name); ?></p>
                       <a href="<?php echo wp_logout_url(home_url('/charts/')); ?>" style="font-size: 0.7rem; text-decoration: none; color: #cc0000; font-weight: 800; text-transform: uppercase;">Logout</a>
                  </div>
             </div>
        </div>
    </aside>

    <!-- Main Workspace -->
    <main class="kc-dash-main">
        
        <header class="kc-dash-topbar">
            <div>
                <p class="kc-dash-kicker">Kontentainment Charts</p>
                <h1><?php echo esc_html($dash_sections[$section] ?? 'Main Workspace'); ?></h1>
                <p class="kc-dash-subcopy">This is the main working experience for managing charts, weekly entries, tracks, artists, uploads, and publishing.</p>
            </div>
            <div style="display:flex; gap: 12px;">
                 <a href="<?php echo admin_url('admin.php?page=kontentainment-charts-uploads'); ?>" class="kc-btn kc-btn-ghost" style="padding: 10px 20px; font-size:0.8rem;">WP-Admin Tools</a>
                 <button type="button" class="kc-btn" style="padding: 10px 20px; font-size:0.8rem;">New Working Draft</button>
            </div>
        </header>

        <div class="kc-dash-content-inner">
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
        </div>
    </main>

</div>

<?php wp_footer(); ?>
</body>
</html>
