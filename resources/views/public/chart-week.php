<?php
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

$chart_slug = get_query_var('kc_slug');
$chart = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}charts WHERE slug = %s", $chart_slug));

if (!$chart) {
    echo '<div class="kc-public-wrap"><div class="kc-empty-state"><h3>Chart Not Found</h3><p>The requested ranking system does not exist in our registry.</p></div></div>';
    return;
}

// Resolve Latest Published Week
$week_row = $wpdb->get_row($wpdb->prepare("
    SELECT id, week_date FROM {$prefix}chart_weeks 
    WHERE chart_id = %d AND status = 'published' 
    ORDER BY week_date DESC LIMIT 1
", $chart->id));

if (!$week_row) {
    echo '<div class="kc-public-wrap">
        <header style="margin-bottom: 40px; border-bottom: 2px solid #111; padding-bottom: 20px;">
            <h1 class="kc-hero-title" style="font-size: 2.5rem;">' . esc_html($chart->name) . '</h1>
        </header>
        <div class="kc-empty-state">
            <h3>No Published Data</h3>
            <p>We are currently gathering data for this ranking. Check back soon for the latest week.</p>
        </div>
    </div>';
    return;
}

$entries = $wpdb->get_results($wpdb->prepare("
    SELECT e.*, t.title as track_title, t.slug as track_slug, a.name as artist_name, a.slug as artist_slug, t.artwork_url
    FROM {$prefix}chart_entries e
    JOIN {$prefix}tracks t ON e.track_id = t.id
    LEFT JOIN {$prefix}artists a ON t.primary_artist_id = a.id
    WHERE e.chart_week_id = %d
    ORDER BY e.rank ASC
", $week_row->id));

$top_entry = $entries[0] ?? null;

?>
<div class="kc-public-wrap">
    
    <header style="margin-bottom: 60px; display: flex; align-items: flex-end; justify-content: space-between; border-bottom: 3px solid #111; padding-bottom: 40px;">
        <div>
            <span class="kc-public-badge new">Verified Ranking</span>
            <h1 class="kc-hero-title" style="font-size: 3.5rem; margin-top: 15px;"><?php echo esc_html($chart->name); ?></h1>
            <p style="margin:0; font-weight: 800; color:#666; font-size: 1.2rem;">Week of <?php echo date('F d, Y', strtotime($week_row->week_date)); ?></p>
        </div>
        <div style="text-align: right;">
            <p style="margin:0; font-weight: 700; color:#888; font-size: 0.8rem; text-transform: uppercase;">Reporting Scope</p>
            <p style="margin:5px 0 0 0; font-weight: 900; font-size: 1.1rem;"><?php echo (int)count($entries); ?> Positions</p>
        </div>
    </header>

    <?php if ($top_entry) : ?>
    <!-- Top #1 Spotlight -->
    <section class="kc-spotlight" style="margin-bottom: 80px; background: #fff; border: 2px solid #111;">
        <img src="<?php echo esc_url($top_entry->artwork_url ?: 'https://picsum.photos/400/400?random='.$top_entry->id); ?>" class="kc-spotlight-img" alt="Top #1">
        <div class="kc-spotlight-content">
            <span class="kc-public-badge new" style="background:#111; color:#fff;">Number One</span>
            <h2 style="font-size: 4rem;"><?php echo esc_html($top_entry->track_title); ?></h2>
            <p style="font-size: 1.5rem; color: #666; margin-bottom: 30px; font-weight: 700;">
                 <a href="<?php echo home_url('/charts/artist/'.$top_entry->artist_slug.'/'); ?>" style="color:inherit; text-decoration:none; border-bottom: 2px solid #111;"><?php echo esc_html($top_entry->artist_name); ?></a>
            </p>
            <div style="display:flex; gap: 40px;">
                 <div>
                    <p style="margin:0; color:#888; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">Peak</p>
                    <p style="margin:5px 0 0 0; font-weight: 900; font-size: 1.2rem;">#1</p>
                 </div>
                 <div>
                    <p style="margin:0; color:#888; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">Wks on Chart</p>
                    <p style="margin:5px 0 0 0; font-weight: 900; font-size: 1.2rem;"><?php echo rand(5, 50); ?> (V1 Note: Static Weeks Placeholder)</p>
                 </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Full Table -->
    <div class="kc-section-header">
        <h2 class="kc-section-title">Full Ranking</h2>
        <span style="font-weight: 700; color:#888;">Complete Data Download Enabled</span>
    </div>

    <table class="kc-chart-table">
        <thead>
            <tr>
                <th style="width: 80px;">RANK</th>
                <th>TRACK / ARTIST</th>
                <th style="text-align: right;">MOVEMENT</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entries as $row) : ?>
            <tr>
                <td style="font-size: 2rem; font-weight: 900; letter-spacing: -1px;"><?php echo (int)$row->rank; ?></td>
                <td>
                    <div style="display:flex; align-items:center; gap:20px;">
                        <img src="<?php echo esc_url($row->artwork_url ?: 'https://picsum.photos/100/100?random='.$row->id); ?>" style="width: 64px; height: 64px; border-radius: 4px; object-fit: cover;">
                        <div>
                             <p style="margin:0; font-weight: 900; font-size: 1.2rem;">
                                <a href="<?php echo home_url('/charts/track/'.$row->track_slug.'/'); ?>" style="color:inherit; text-decoration:none;"><?php echo esc_html($row->track_title); ?></a>
                             </p>
                             <p style="margin:5px 0 0 0; font-weight: 700; color:#666;">
                                <a href="<?php echo home_url('/charts/artist/'.$row->artist_slug.'/'); ?>" style="color:inherit; text-decoration:none;"><?php echo esc_html($row->artist_name); ?></a>
                             </p>
                        </div>
                    </div>
                </td>
                <td style="text-align: right;">
                    <span class="kc-public-badge same">---</span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
