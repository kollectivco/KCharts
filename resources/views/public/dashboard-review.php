<?php
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

$review_queue = $wpdb->get_results("SELECT * FROM {$prefix}upload_data WHERE status = 'pending_review' ORDER BY id ASC LIMIT 50");
?>
<header style="margin-bottom: 40px;">
    <h1 class="kc-hero-title" style="font-size: 2rem;">Review Queue</h1>
    <p style="color: #666;">Manual resolution required for unresolved entity mappings.</p>
</header>

<div class="kc-bento-item">
    <div class="kc-section-header">
        <h3 class="kc-section-title">Pending Resolutions</h3>
        <span style="font-weight: 700; color: #cc0000;"><?php echo (int)count($review_queue); ?> Pending Items</span>
    </div>
    
    <table class="kc-chart-table">
        <thead>
            <tr>
                <th>Upload ID</th>
                <th>Track Title</th>
                <th>Artist (Raw)</th>
                <th style="text-align:right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($review_queue) : foreach ($review_queue as $row) : ?>
            <tr>
                <td style="font-family: monospace; color:#999;">#<?php echo (int)$row->id; ?></td>
                <td><strong><?php echo esc_html($row->track_title); ?></strong></td>
                <td><?php echo esc_html($row->artist_name); ?></td>
                <td style="text-align:right;">
                    <a href="<?php echo admin_url('admin.php?page=kontentainment-charts-uploads&week='.$row->upload_id); ?>" class="kc-link-card" style="display:inline-flex; padding: 6px 12px; font-size: 0.75rem; background:#111; color:#fff; border:none;">Resolve in Admin</a>
                </td>
            </tr>
            <?php endforeach; else : ?>
                <tr>
                    <td colspan="4" style="text-align:center; padding:80px; color:#888;">
                        <h4 style="margin:0;">Queue Clear</h4>
                        <p style="font-size: 0.9rem;">Excellent work. No pending entities require manual resolution.</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
