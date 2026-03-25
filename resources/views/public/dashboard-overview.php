<?php
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

$total_charts = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}charts");
$total_weeks = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}chart_weeks");
$total_pending = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}upload_data WHERE status = 'pending_review'");
$latest_uploads = $wpdb->get_results("SELECT * FROM {$prefix}uploads ORDER BY created_at DESC LIMIT 5");

?>
<header style="margin-bottom: 40px;">
    <h1 class="kc-hero-title" style="font-size: 2rem;">System Overview</h1>
    <p style="color: #666;">Snapshot of current ingestion pipeline and data state.</p>
</header>

<div class="kc-bento-grid">
    
    <!-- Quick Stats -->
    <div class="kc-bento-item" style="grid-column: span 3;">
        <p style="font-size: 0.75rem; text-transform: uppercase; color: #888; font-weight: 800; margin:0;">Active Charts</p>
        <h2 style="font-size: 2.5rem; margin:0; font-weight: 900;"><?php echo (int)$total_charts; ?></h2>
    </div>

    <div class="kc-bento-item" style="grid-column: span 3;">
        <p style="font-size: 0.75rem; text-transform: uppercase; color: #888; font-weight: 800; margin:0;">Published Weeks</p>
        <h2 style="font-size: 2.5rem; margin:0; font-weight: 900;"><?php echo (int)$total_weeks; ?></h2>
    </div>

    <div class="kc-bento-item" style="grid-column: span 3;">
        <p style="font-size: 0.75rem; text-transform: uppercase; color: #888; font-weight: 800; margin:0;">Pending Review</p>
        <h2 style="font-size: 2.5rem; margin:0; font-weight: 900; color: #cc0000;"><?php echo (int)$total_pending; ?></h2>
    </div>

    <div class="kc-bento-item" style="grid-column: span 3;">
        <p style="font-size: 0.75rem; text-transform: uppercase; color: #888; font-weight: 800; margin:0;">Uptime Status</p>
        <h2 style="font-size: 2.5rem; margin:0; font-weight: 900; color: #06d6a0;">OK</h2>
    </div>

    <!-- Latest Activities -->
    <div class="kc-bento-item" style="grid-column: span 12;">
        <div class="kc-section-header">
            <h3 class="kc-section-title">Recent Ingestion History</h3>
            <a href="<?php echo home_url('/charts-dashboard/uploads/'); ?>" style="font-weight: 700; color: #111;">Full History →</a>
        </div>
        
        <table class="kc-chart-table">
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Chart / Country</th>
                    <th>Target Week</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($latest_uploads) : foreach ($latest_uploads as $up) : ?>
                <tr>
                    <td><strong><?php echo esc_html($up->original_filename); ?></strong></td>
                    <td><?php echo esc_html($up->chart_id ?: 'All'); ?> / <?php echo esc_html($up->country_id ?: 'Global'); ?></td>
                    <td><?php echo esc_html($up->week_date); ?></td>
                    <td><span class="kc-public-badge same"><?php echo esc_html($up->status); ?></span></td>
                </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="4" style="text-align:center; padding:40px; color:#888;">No recent upload activity found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
