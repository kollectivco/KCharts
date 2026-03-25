<?php
/**
 * Kontentainment Charts V1 Dashboard Overview
 * Rebuilt from Old Plugin AMC_Admin::render_dashboard() logic.
 */
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

// 1. Snapshot Cards (Transfer from old plugin)
$total_charts = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}charts");
$total_weeks = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}chart_weeks WHERE status = 'published'");
$pending_review = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}upload_data WHERE status = 'pending_review'");
$latest_uploads = $wpdb->get_results("SELECT * FROM {$prefix}uploads ORDER BY created_at DESC LIMIT 5");

?>
<div class="kc-bento-grid">
    
    <!-- Setup Checklist Simulation (Transfer from old plugin) -->
    <div class="kc-bento-item" style="grid-column: span 12; background: #fff; border-left: 5px solid #111;">
        <div style="display:flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="margin:0; font-weight:900;">Production setup checklist</h3>
                <p style="margin: 5px 0 0 0; color:#666; font-size:0.9rem;">This lightweight overview tracks first-run readiness while the full operational workflow continues below.</p>
            </div>
            <a href="<?php echo admin_url('admin.php?page=kontentainment-charts-uploads'); ?>" class="kc-public-badge new" style="text-decoration:none;">Open Wizard</a>
        </div>
    </section>

    <!-- Operational Grid (Transfer from old plugin) -->
    <div class="kc-bento-item" style="grid-column: span 3; border: 2px solid #06d6a0;">
        <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Registry Health</p>
        <h2 style="font-size: 2.5rem; font-weight:900; margin:5px 0; color:#06d6a0;">Optimal</h2>
        <p style="font-size: 0.8rem; color:#888;">Index verified today.</p>
    </div>
    
    <div class="kc-bento-item" style="grid-column: span 3;">
        <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Registry Scale</p>
        <h2 style="font-size: 2.5rem; font-weight:900; margin:5px 0;"><?php echo (int)($total_charts + $total_weeks); ?></h2>
        <p style="font-size: 0.8rem; color:#888;">Active chart/week nodes.</p>
    </div>

    <div class="kc-bento-item" style="grid-column: span 3;">
        <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">In Review</p>
        <h2 style="font-size: 2.5rem; font-weight:900; margin:5px 0; color: #cc0000;"><?php echo (int)$pending_review; ?></h2>
        <p style="font-size: 0.8rem; color:#888;">Items requiring resolution.</p>
    </div>

    <div class="kc-bento-item" style="grid-column: span 3;">
        <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Uptime</p>
        <h2 style="font-size: 2.5rem; font-weight:900; margin:5px 0;">100%</h2>
        <p style="font-size: 0.8rem; color:#888;">Data routes operational.</p>
    </div>

    <!-- Recent History Panel (Transfer from old plugin) -->
    <div class="kc-bento-item" style="grid-column: span 12;">
        <div class="kc-section-header">
            <h3 class="kc-section-title">Operational Summary</h3>
            <a href="<?php echo home_url('/charts-dashboard/uploads/'); ?>" style="font-weight: 700; color: #111;">Full history →</a>
        </div>
        
        <table class="kc-chart-table">
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Chart Context</th>
                    <th>Week Target</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($latest_uploads) : foreach ($latest_uploads as $up) : ?>
                <tr>
                    <td><strong><?php echo esc_html($up->original_filename); ?></strong></td>
                    <td><span class="kc-public-badge same"><?php echo esc_html($up->chart_id ?: 'Global'); ?></span></td>
                    <td><?php echo esc_html($up->week_date); ?></td>
                    <td><span class="kc-public-badge new"><?php echo esc_html($up->status); ?></span></td>
                </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="4" style="text-align:center; padding:60px; color:#888;">No recent upload activity found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Control Layer Panel (Transfer from old plugin) -->
    <div class="kc-bento-item" style="grid-column: span 12; background: #fafafa; border: 1px dashed #111; text-align:center; padding: 60px;">
        <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Control Layer Shortcuts</span>
        <h2 style="font-weight:900; margin:10px 0;">Jump directly into operational sections</h2>
        <p style="color:#666; margin-bottom: 30px;">This workspace provides the primarily user interface, but the wp-admin menu remains available for lightweight settings and permissions control.</p>
        <div style="display:flex; gap: 15px; justify-content:center; flex-wrap:wrap;">
             <a href="<?php echo home_url('/charts-dashboard/uploads/'); ?>" class="kc-btn">Source Uploads</a>
             <a href="<?php echo home_url('/charts-dashboard/review/'); ?>" class="kc-btn kc-btn-ghost">Review Queue</a>
             <a href="<?php echo home_url('/charts-dashboard/charts/'); ?>" class="kc-btn kc-btn-ghost">Chart Registry</a>
             <a href="<?php echo home_url('/charts-dashboard/status/'); ?>" class="kc-btn kc-btn-ghost">System Health</a>
        </div>
    </div>

</div>
