<?php 
$page_title = 'Overview Dashboard';
$page_subtitle = 'System performance and chart status at a glance.';
include 'partials/header.php'; 
?>

<?php 
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

// Live Stats
$total_artists = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$prefix}artists");
$total_tracks = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$prefix}tracks");
$published_weeks = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$prefix}chart_weeks WHERE status = 'published'");
$pending_reviews = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$prefix}source_rows WHERE resolution_status = 'needs_review'");

// New this week (simulated time window or real created_at)
$new_artists_week = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$prefix}artists WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$new_tracks_week = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$prefix}tracks WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");

// Recent Activity
$recent_uploads = $wpdb->get_results("
    SELECT u.*, c.name as chart_name
    FROM {$prefix}source_uploads u
    LEFT JOIN {$prefix}charts c ON u.chart_id = c.id
    ORDER BY u.created_at DESC LIMIT 5
");
?>

<div class="kc-bento-grid">
    
    <div class="kc-stat-card">
        <div>Total Tracks Indexed</div>
        <div class="kc-stat-value"><?php echo number_format($total_tracks); ?></div>
        <?php if ($new_tracks_week > 0): ?>
            <div style="color: green; font-size: 0.85rem; margin-top: 5px;">+<?php echo $new_tracks_week; ?> this week</div>
        <?php else: ?>
            <div style="color: #888; font-size: 0.85rem; margin-top: 5px;">No new additions</div>
        <?php endif; ?>
    </div>

    <div class="kc-stat-card">
        <div>Published Chart Weeks</div>
        <div class="kc-stat-value"><?php echo number_format($published_weeks); ?></div>
        <div style="font-size: 0.85rem; margin-top: 5px; color: #888;">Active on frontend</div>
    </div>

    <div class="kc-stat-card" style="<?php echo $pending_reviews > 0 ? 'border-left: 4px solid #f59e0b;' : ''; ?>">
        <div>Pending Reviews</div>
        <div class="kc-stat-value"><?php echo number_format($pending_reviews); ?> Rows</div>
        <?php if ($pending_reviews > 0): ?>
            <div style="font-size: 0.85rem; margin-top: 5px; color: #f59e0b; font-weight: 700;">Manual matching required</div>
        <?php else: ?>
            <div style="font-size: 0.85rem; margin-top: 5px; color: #888;">Queue is clear</div>
        <?php endif; ?>
    </div>
</div>

<div class="kc-bento-grid" style="grid-template-columns: 2fr 1fr;">
    <div class="kc-card">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Recent Uploads Activity</h3>
        </div>
        <table class="kc-table">
            <thead>
                <tr>
                    <th>Target Chart</th>
                    <th>Week Date</th>
                    <th>Platform</th>
                    <th>Success</th>
                    <th>Review</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recent_uploads)): ?>
                    <?php foreach ($recent_uploads as $upload): ?>
                        <tr>
                            <td><?php echo esc_html($upload->chart_name ?? 'Custom Chart'); ?></td>
                            <td><?php echo esc_html($upload->week_date); ?></td>
                            <td><?php echo esc_html(ucfirst($upload->source_platform)); ?></td>
                            <td><span class="kc-public-badge" style="background: #e0f2fe; color: #0369a1;"><?php echo (int)$upload->rows_processed; ?> Rows</span></td>
                            <td>
                                <?php if ($upload->rows_skipped > 0): ?>
                                    <span class="kc-badge kc-badge-down"><?php echo (int)$upload->rows_skipped; ?> Issues</span>
                                <?php else: ?>
                                    <span class="kc-badge kc-badge-up">Clean</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 40px; color:#888;">
                            No upload activity yet. 
                            <a href="?page=kontentainment-charts-uploads" style="color: #000; font-weight: 700; display: block; margin-top: 10px;">Upload your first file &rarr;</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="kc-card">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Quick Actions</h3>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <a href="?page=kontentainment-charts-uploads" class="kc-btn kc-btn-primary">Upload New CSV</a>
            <a href="?page=kontentainment-charts-review" class="kc-btn kc-btn-outline" style="<?php echo $pending_reviews > 0 ? 'border-color: #000; font-weight: 700;' : ''; ?>">
                Review Queue (<?php echo $pending_reviews; ?>)
            </a>
            <a href="?page=kontentainment-charts-charts" class="kc-btn kc-btn-outline">Release New Weeks</a>
            <a href="?page=kontentainment-charts-settings" class="kc-btn kc-btn-outline">System Configuration</a>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
