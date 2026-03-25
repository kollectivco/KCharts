<?php 
$page_title = 'Artist Database';
$page_subtitle = 'Manage resolved artists, aliases, and identities.';
include 'partials/header.php'; 
?>

<div class="kc-bento-grid">
    <div class="kc-card" style="grid-column: 1 / -1;">
        <div class="kc-card-header" style="flex-wrap: wrap; gap: 15px;">
            <h3 class="kc-card-title">Artist Directory</h3>
            
            <form method="get" style="display: flex; gap: 10px; align-items: center;">
                <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']); ?>">
                <input type="text" name="s" class="kc-input" placeholder="Search artists..." value="<?php echo esc_attr($_GET['s'] ?? ''); ?>" style="margin-bottom:0; min-width: 250px;">
                <button type="submit" class="kc-btn kc-btn-primary">Search</button>
            </form>
        </div>

        <table class="kc-table">
            <thead>
                <tr>
                    <th>Artist Name</th>
                    <th>Genre/Type</th>
                    <th>Total Tracks</th>
                    <th>Last Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                global $wpdb;
                $prefix = $wpdb->prefix . 'kc_';

                $search = isset($_GET['s']) ? $_GET['s'] : '';
                $query = "SELECT a.* FROM {$prefix}artists a";
                if ($search) {
                    $query .= $wpdb->prepare(" WHERE a.name LIKE %s", '%' . $wpdb->esc_like($search) . '%');
                }
                $query .= " ORDER BY a.name ASC LIMIT 100";
                $artists = $wpdb->get_results($query);

                if ( ! empty($artists) ) :
                    foreach ( $artists as $a ) :
                        $track_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM {$prefix}tracks WHERE artist_id = %d", $a->id));
                ?>
                <tr>
                    <td style="font-weight:700; font-size:1.1rem;"><?php echo esc_html($a->name); ?></td>
                    <td><span class="kc-badge kc-badge-same"><?php echo esc_html($a->genre ?? 'Uncategorized'); ?></span></td>
                    <td><strong><?php echo (int)$track_count; ?></strong> <small style="color:#888;">tracks</small></td>
                    <td><?php echo esc_html($a->updated_at); ?></td>
                    <td>
                        <div style="display:flex; gap:8px;">
                            <a href="#" class="kc-btn kc-btn-outline" style="font-size:0.75rem; padding: 4px 12px;">Edit</a>
                            <a href="#" class="kc-btn kc-btn-outline" style="font-size:0.75rem; padding: 4px 12px;">Merge</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 60px;">
                        <div style="margin-bottom:20px; font-size: 2rem;">👤</div>
                        <h3 style="margin-bottom:10px;">No Artists Found</h3>
                        <p style="color:#666; margin-bottom:20px;">Artists are automatically created during data ingestion.</p>
                        <a href="?page=kontentainment-charts-uploads" class="kc-btn kc-btn-primary">Start Ingestion</a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
