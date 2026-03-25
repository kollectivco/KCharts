<?php
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

$uploads = $wpdb->get_results("SELECT * FROM {$prefix}uploads ORDER BY created_at DESC LIMIT 20");
?>
<header style="margin-bottom: 40px;">
    <h1 class="kc-hero-title" style="font-size: 2rem;">Upload Management</h1>
    <p style="color: #666;">Ingested source file audit trail and processing logs.</p>
</header>

<div class="kc-bento-item">
    <div class="kc-section-header">
        <h3 class="kc-section-title">All System Ingestions</h3>
        <span style="font-weight: 700; color: #888;">Showing last 20 records</span>
    </div>
    
    <table class="kc-chart-table">
        <thead>
            <tr>
                <th>System ID</th>
                <th>File Status</th>
                <th>Target Context</th>
                <th>Processed At</th>
                <th>Rows Logged</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($uploads) : foreach ($uploads as $up) : ?>
            <tr>
                <td style="font-family: monospace; font-weight: 800;">#<?php echo (int)$up->id; ?></td>
                <td>
                    <strong><?php echo esc_html($up->original_filename); ?></strong>
                    <div style="font-size: 0.7rem; color: #999;"><?php echo esc_html($up->status); ?></div>
                </td>
                <td>
                    <span class="kc-public-badge same"><?php echo esc_html($up->chart_id ?: 'All'); ?></span>
                    <span class="kc-public-badge same"><?php echo esc_html($up->country_id ?: 'Global'); ?></span>
                </td>
                <td style="color:#888;"><?php echo date('M d, H:i', strtotime($up->created_at)); ?></td>
                <td><?php echo (int)$up->total_rows; ?></td>
            </tr>
            <?php endforeach; else : ?>
                <tr><td colspan="5" style="text-align:center; padding:60px; color:#888;">The ingestion log is empty. No files have been uploaded.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
