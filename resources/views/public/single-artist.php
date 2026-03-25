<?php
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

$artist = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}artists WHERE slug = %s", $slug));

if (!$artist) {
    echo '<div class="kc-public-wrap"><h1>Artist Not Found</h1></div>';
    return;
}

$history = $wpdb->get_results($wpdb->prepare("
    SELECT e.*, w.week_date, c.name as chart_name, t.title as track_title, t.slug as track_slug
    FROM {$prefix}chart_entries e
    JOIN {$prefix}chart_weeks w ON e.chart_week_id = w.id
    JOIN {$prefix}charts c ON w.chart_id = c.id
    JOIN {$prefix}tracks t ON e.track_id = t.id
    WHERE t.primary_artist_id = %d AND w.status = 'published'
    ORDER BY w.week_date DESC
", $artist->id));

$peak = $wpdb->get_var($wpdb->prepare("
    SELECT MIN(e.rank) 
    FROM {$prefix}chart_entries e 
    JOIN {$prefix}chart_weeks w ON e.chart_week_id = w.id 
    JOIN {$prefix}tracks t ON e.track_id = t.id
    WHERE t.primary_artist_id = %d AND w.status = 'published'
", $artist->id));

$charted_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(DISTINCT id) FROM {$prefix}tracks WHERE primary_artist_id = %d", $artist->id));
?>
<?php
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

$track = $wpdb->get_row($wpdb->prepare("
    SELECT t.*, a.name as artist_name, a.slug as artist_slug 
    FROM {$prefix}tracks t
    LEFT JOIN {$prefix}artists a ON t.primary_artist_id = a.id
    WHERE t.slug = %s
", $slug));

if (!$track) {
    echo '<div class="kc-public-wrap"><h1>Track Not Found</h1></div>';
    return;
}

$history = $wpdb->get_results($wpdb->prepare("
    SELECT e.*, w.week_date, c.name as chart_name
    FROM {$prefix}chart_entries e
    JOIN {$prefix}chart_weeks w ON e.chart_week_id = w.id
    JOIN {$prefix}charts c ON w.chart_id = c.id
    WHERE e.track_id = %d AND w.status = 'published'
    ORDER BY w.week_date DESC
", $track->id));

$peak_pos = $wpdb->get_var($wpdb->prepare("
    SELECT MIN(rank) FROM {$prefix}chart_entries e 
    JOIN {$prefix}chart_weeks w ON e.chart_week_id = w.id 
    WHERE e.track_id = %d AND w.status = 'published'
", $track->id));

$total_weeks = $wpdb->get_var($wpdb->prepare("
    SELECT COUNT(e.id) FROM {$prefix}chart_entries e 
    JOIN {$prefix}chart_weeks w ON e.chart_week_id = w.id 
    WHERE e.track_id = %d AND w.status = 'published'
", $track->id));
?>
<div class="kc-public-wrap">
    <!-- Track Header -->
    <div style="background: #fafafa; border: 1px solid #eaeaea; border-radius: 16px; padding: 40px; margin-bottom: 40px; display:flex; gap: 30px; align-items:center;">
        <div style="width: 200px; height: 200px; border-radius: 8px; background: #eee url('<?php echo esc_url($track->artwork_url ?: 'https://picsum.photos/300/300?random='.$track->id); ?>'); background-size:cover; flex-shrink:0;"></div>
        <div>
            <h1 style="font-size: 3rem; font-weight: 900; margin: 0 0 10px 0; letter-spacing: -1px;"><?php echo esc_html($track->title); ?></h1>
            <p style="font-size: 1.2rem; color: #555; margin: 0; font-weight: 600;">
                <a href="<?php echo home_url('/charts/artist/'.$track->artist_slug.'/'); ?>" style="color:inherit; text-decoration:none; border-bottom:2px solid #000;"><?php echo esc_html($track->artist_name); ?></a>
            </p>
            
            <div style="display:flex; gap: 20px; margin-top: 25px;">
                <div class="kc-stats-grid">
                    <div class="kc-stat-box" style="background:#fff;">
                        <span>Peak Position</span>
                        <strong>#<?php echo (int)$peak_pos; ?></strong>
                    </div>
                    <div class="kc-stat-box" style="background:#fff;">
                        <span>Weeks on Chart</span>
                        <strong><?php echo (int)$total_weeks; ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <h2>Historical Chart Runs</h2>
    <table class="kc-chart-table">
        <thead>
            <tr>
                <th>Week Date</th>
                <th style="text-align:right;">Target Chart</th>
                <th style="text-align:right;">Rank</th>
                <th style="text-align:right;">Metrics Logged</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($history) : foreach($history as $row) : ?>
            <tr>
                <td style="font-weight:700;"><?php echo date('M d, Y', strtotime($row->week_date)); ?></td>
                <td style="text-align:right; color:#888;"><?php echo esc_html($row->chart_name); ?></td>
                <td style="text-align:right; font-weight:800; font-family:monospace; font-size:1.2rem;"><?php echo (int)$row->rank; ?></td>
                <td style="text-align:right; color:#888;">
                    <?php echo number_format($row->metric_primary_value); ?> <?php echo esc_html($row->metric_primary_type); ?>
                </td>
            </tr>
            <?php endforeach; else : ?>
            <tr><td colspan="4" style="text-align:center; padding:40px; color:#888;">No historical data available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="kc-public-wrap">
    <!-- Artist Header Profile -->
    <div style="background: #111; color: #fff; border-radius: 16px; padding: 40px; margin-bottom: 40px; display:flex; gap: 30px; align-items:center;">
        <div style="width: 150px; height: 150px; border-radius: 50%; background: #222 url('<?php echo esc_url($artist->image_url ?: 'https://picsum.photos/200/200?random='.$artist->id); ?>'); background-size:cover; flex-shrink:0;"></div>
        <div>
            <h1 style="font-size: 3rem; font-weight: 900; margin: 0 0 10px 0; letter-spacing: -1px;"><?php echo esc_html($artist->name); ?></h1>
            <p style="font-size: 1.1rem; color: #aaa; margin: 0;">Verified Artist Profile</p>
            
            <div style="display:flex; gap: 20px; margin-top: 20px;">
                <div class="kc-public-badge same" style="background:#333; color:#fff; font-size: 0.9rem; padding: 6px 12px; border-radius: 20px;"><?php echo (int)$charted_count; ?> Charted Tracks</div>
                <div class="kc-public-badge same" style="background:#333; color:#fff; font-size: 0.9rem; padding: 6px 12px; border-radius: 20px;">Peak #<?php echo (int)$peak ?: '-'; ?></div>
            </div>
        </div>
    </div>
    
    <h2>Chart History</h2>
    <table class="kc-chart-table">
        <thead>
            <tr>
                <th>Week</th>
                <th>Track Title</th>
                <th style="text-align:right;">Chart</th>
                <th style="text-align:right;">Rank</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($history) : foreach ($history as $row) : ?>
            <tr>
                <td><?php echo date('M d, Y', strtotime($row->week_date)); ?></td>
                <td style="font-weight:700;">
                    <a href="<?php echo home_url('/charts/track/'.$row->track_slug.'/'); ?>" style="color:inherit; text-decoration:none;">
                        <?php echo esc_html($row->track_title); ?>
                    </a>
                </td>
                <td style="text-align:right; color:#888;"><?php echo esc_html($row->chart_name); ?></td>
                <td style="text-align:right; font-weight:800; font-family:monospace; font-size:1.2rem;"><?php echo (int)$row->rank; ?></td>
            </tr>
            <?php endforeach; else : ?>
            <tr><td colspan="4" style="text-align:center; padding:40px; color:#888;">No charted history available yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
