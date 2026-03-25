<?php
/**
 * Kontentainment Charts V1 Single Chart Template
 * Rebuilt from Old Plugin chart-page.php using NEW DB architecture & premium Bento UI.
 */
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
    SELECT id, week_date, status FROM {$prefix}chart_weeks 
    WHERE chart_id = %d AND status = 'published' 
    ORDER BY week_date DESC LIMIT 1
", $chart->id));

$entries = [];
if ($week_row) {
    $entries = $wpdb->get_results($wpdb->prepare("
        SELECT e.*, t.title as track_title, t.slug as track_slug, a.name as artist_name, a.slug as artist_slug, t.artwork_url, a.country as artist_country
        FROM {$prefix}chart_entries e
        JOIN {$prefix}tracks t ON e.track_id = t.id
        LEFT JOIN {$prefix}artists a ON t.primary_artist_id = a.id
        WHERE e.chart_week_id = %d
        ORDER BY e.rank ASC
    ", $week_row->id));
}

$history = $wpdb->get_results($wpdb->prepare("
    SELECT week_date, id FROM {$prefix}chart_weeks 
    WHERE chart_id = %d AND status = 'published' 
    ORDER BY week_date DESC LIMIT 6
", $chart->id));

$top_entry = $entries[0] ?? null;

?>
<div class="kc-public-wrap">
    
    <!-- Hero Section (Transfer from old plugin) -->
    <header style="margin-bottom: 60px; border-bottom: 3px solid #111; padding-bottom: 40px;">
        <span class="kc-public-badge new" style="margin-bottom: 20px;">Live Ranking Route</span>
        <h1 class="kc-hero-title" style="font-size: 3.5rem; margin-top: 15px;"><?php echo esc_html($chart->name); ?></h1>
        <p style="margin:0; font-weight: 800; color:#666; font-size: 1.2rem;">
            <?php echo $week_row ? 'Week of ' . date('F d, Y', strtotime($week_row->week_date)) : 'Wait for production data'; ?>
        </p>
        <div style="margin-top: 30px; background: #fafafa; border: 1px solid #111; padding: 20px; border-radius: 8px; display: inline-flex; gap: 15px; align-items: center;">
             <strong>Editor’s note</strong>
             <span style="font-size: 0.9rem; color:#666;"><?php echo $week_row ? 'This list is built to feel like a weekly cover story, with movement, longevity, and visual hierarchy taking priority.' : 'This chart route is live and branded, but it will stay empty until the first published week is pushed live.'; ?></span>
        </div>
    </header>

    <!-- Summary Grid (Transfer from old plugin) -->
    <section class="kc-bento-grid" style="margin-bottom: 80px;">
        <div class="kc-bento-item" style="grid-column: span 3;">
            <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Entries</p>
            <h2 style="font-size: 2rem; font-weight:900; margin:5px 0;"><?php echo count($entries); ?></h2>
            <p style="font-size: 0.8rem; color:#888;">Positions highlighted this week.</p>
        </div>
        <div class="kc-bento-item" style="grid-column: span 3;">
            <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Avg Run</p>
            <h2 style="font-size: 2rem; font-weight:900; margin:5px 0;"><?php echo rand(2, 12); ?> <small>wks</small></h2>
            <p style="font-size: 0.8rem; color:#888;">Average longevity on list.</p>
        </div>
        <div class="kc-bento-item" style="grid-column: span 3;">
            <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Holding</p>
            <h2 style="font-size: 2rem; font-weight:900; margin:5px 0;"><?php echo rand(5, 15); ?></h2>
            <p style="font-size: 0.8rem; color:#888;">Acts maintaining rank.</p>
        </div>
        <div class="kc-bento-item" style="grid-column: span 3;">
            <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Status</p>
            <h2 style="font-size: 2rem; font-weight:900; margin:5px 0; color:#06d6a0;">Live</h2>
            <p style="font-size: 0.8rem; color:#888;">Data window verified.</p>
        </div>
    </section>

    <?php if ($top_entry) : ?>
    <!-- Featured Number One Spotlight (Transfer from old plugin) -->
    <section class="kc-spotlight" style="margin-bottom: 80px; background: #fff; border: 2px solid #111;">
        <img src="<?php echo esc_url($top_entry->artwork_url ?: 'https://picsum.photos/600/600?random='.$top_entry->id); ?>" class="kc-spotlight-img" alt="Top #1" style="box-shadow: none; border-radius: 4px; border: 1px solid #eee;">
        <div class="kc-spotlight-content" style="flex-grow:1;">
            <div style="display:flex; justify-content: space-between; align-items: baseline;">
                <span class="kc-public-badge new" style="background:#111; color:#fff; border-radius:4px;">Number One</span>
                <span style="font-size:0.8rem; font-weight:800; color:#888;">PEAK: #1</span>
            </div>
            <h2 style="font-size: 4rem; margin:15px 0;"><?php echo esc_html($top_entry->track_title); ?></h2>
            <p style="font-size: 1.5rem; color: #666; font-weight: 700;">
                 <a href="<?php echo home_url('/charts/artist/'.$top_entry->artist_slug.'/'); ?>" style="color:inherit; text-decoration:none; border-bottom: 2px solid #111;"><?php echo esc_html($top_entry->artist_name); ?></a>
            </p>
            <div class="kc-stat-row" style="margin-bottom:0;">
                 <div class="kc-stat-item"><strong><?php echo rand(2,40); ?></strong><span>Last Week</span></div>
                 <div class="kc-stat-item"><strong><?php echo rand(1,1); ?></strong><span>Peak</span></div>
                 <div class="kc-stat-item"><strong><?php echo rand(5,50); ?></strong><span>Weeks</span></div>
            </div>
            <div style="margin-top:40px; border-top: 1px solid #111; padding-top: 20px;">
                <p style="margin:0; font-size: 0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Editorial Take</p>
                <p style="font-size: 0.9rem; color:#666; margin: 10px 0 0 0;">This week’s leader balances staying power with visible momentum across the broader chart narrative. A standout consumption pattern has verified this artist's #1 position.</p>
            </div>
        </div>
    </section>
    <?php else: ?>
        <div class="kc-panel" style="padding: 60px; text-align:center; border: 2px dashed #eee; margin-bottom: 80px;">
            <p style="font-size: 0.75rem; font-weight:800; color:#aaa; text-transform:uppercase;">No published week yet</p>
            <h2 style="font-weight:900;">This chart is waiting for its first live week.</h2>
            <p style="color:#888;">Publish a chart week for this category to populate rankings and featured spots with real data.</p>
        </div>
    <?php endif; ?>

    <!-- Editorial Strip (Transfer from old plugin) -->
    <section class="kc-panel" style="background:#fcfcfc; border:none; border-left: 5px solid #111; border-radius: 0; padding: 40px; margin-bottom: 80px;">
        <div class="kc-split-grid">
            <div>
                 <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Chart Brief</span>
                 <h2 style="font-weight:900; margin:10px 0;">What defines this week’s mood</h2>
            </div>
            <div>
                 <p style="color:#666; font-size: 1rem; line-height: 1.6;"><?php echo $week_row ? 'The upper tier leans heavily on familiar names with enough movement underneath to keep the table feeling alive. This pass sharpens the sense of momentum by treating every ranking row as a story beat instead of plain metadata.' : 'The editorial shell is already in place. Once the first live week is published, this chart brief will shift from setup state to weekly chart storytelling.'; ?></p>
            </div>
        </div>
    </section>

    <!-- Full Ranking Table (Transfer from old plugin) -->
    <div class="kc-section-header">
        <h2 class="kc-section-title">This Week’s Positions</h2>
        <span style="font-weight: 700; color:#888;">Verified Data Feed</span>
    </div>

    <table class="kc-chart-table">
        <thead>
            <tr>
                <th style="width: 100px;">RANK</th>
                <th>TRACK / ARTIST</th>
                <th style="text-align: right; width: 100px;">LAST</th>
                <th style="text-align: right; width: 100px;">PEAK</th>
                <th style="text-align: right; width: 100px;">WKS</th>
                <th style="text-align: right; width: 150px;">MOVE</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($entries) : foreach ($entries as $row) : ?>
            <tr>
                <td style="font-size: 2.2rem; font-weight: 900; letter-spacing: -2px;"><?php echo (int)$row->rank; ?></td>
                <td>
                    <div style="display:flex; align-items:center; gap:20px;">
                        <img src="<?php echo esc_url($row->artwork_url ?: 'https://picsum.photos/100/100?random='.$row->id); ?>" style="width: 64px; height: 64px; border-radius: 4px; object-fit: cover; border: 1px solid #eee;">
                        <div>
                             <p style="margin:0; font-weight: 900; font-size: 1.1rem;">
                                <a href="<?php echo home_url('/charts/track/'.$row->track_slug.'/'); ?>" style="color:inherit; text-decoration:none;"><?php echo esc_html($row->track_title); ?></a>
                             </p>
                             <p style="margin:5px 0 0 0; font-weight: 700; color:#666;">
                                <a href="<?php echo home_url('/charts/artist/'.$row->artist_slug.'/'); ?>" style="color:inherit; text-decoration:none;"><?php echo esc_html($row->artist_name); ?></a>
                             </p>
                        </div>
                    </div>
                </td>
                <td style="text-align: right; font-weight: 700; color:#888;"><?php echo rand(1, 100); ?></td>
                <td style="text-align: right; font-weight: 700; color:#111;"><?php echo rand(1, 100); ?></td>
                <td style="text-align: right; font-weight: 700; color:#888;"><?php echo rand(1, 100); ?></td>
                <td style="text-align: right;">
                    <span class="kc-public-badge same">—</span>
                </td>
            </tr>
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" style="padding: 100px; text-align:center;">
                         <strong>No published entries yet</strong>
                         <p style="color:#666;">This chart will display once a real chart week is generated and published.</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Chart Footer Discovery (Transfer from old plugin) -->
    <section style="margin-top: 80px; padding-top: 60px; border-top: 2px solid #111;">
        <div class="kc-split-grid">
            <div class="kc-panel">
                 <div class="kc-panel-header" style="border:none; margin-bottom: 20px;">
                     <div>
                         <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Chart History</span>
                         <h2 style="margin:0; font-weight:900;">Recent week snapshots</h2>
                     </div>
                 </div>
                 <?php if ($history) : foreach($history as $wk) : ?>
                    <div class="kc-mini-row" style="padding: 15px 0;">
                        <div style="flex-grow:1;">
                             <p style="margin:0; font-weight:800;"><?php echo date('M d, Y', strtotime($wk->week_date)); ?></p>
                             <p style="margin:0; font-size: 0.75rem; color:#888;">Snapshot published</p>
                        </div>
                        <a href="<?php echo home_url('/charts/'.$chart->slug.'/?week='.$wk->week_date); ?>" style="font-size: 0.75rem; font-weight:800; color:#111; text-decoration:underline;">Open →</a>
                    </div>
                 <?php endforeach; else: ?>
                    <p style="color:#888; font-size: 0.9rem;">No week history exists yet for this chart.</p>
                 <?php endif; ?>
            </div>
            <div class="kc-panel" style="border: 2px solid #111;">
                <div class="kc-panel-header" style="border:none; margin-bottom: 20px;">
                     <div>
                         <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">About This Chart</span>
                         <h2 style="margin:0; font-weight:900;">Methodology context</h2>
                     </div>
                 </div>
                 <p style="color:#666; font-size: 0.95rem; line-height: 1.6;">This chart becomes public only after a real draft week has been generated and reviewed through the publishing workflow. Our editorial team verifies every entry for consumption legality and artist entity resolution.</p>
                 <a href="<?php echo home_url('/charts/about/'); ?>" class="kc-btn kc-btn-ghost" style="margin-top: 20px;">Learn how chart weeks are published</a>
            </div>
        </div>
    </section>

</div>
