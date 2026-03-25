<?php
/**
 * Kontentainment Charts V1 Home Template
 * Rebuilt from Old Plugin home.php using NEW DB architecture & premium Bento UI.
 */
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

// 1. Get Hero/Lead Chart (Latest Published Week)
$hero_week = $wpdb->get_row("
    SELECT cw.*, c.name as chart_name, c.slug as chart_slug
    FROM {$prefix}chart_weeks cw
    JOIN {$prefix}charts c ON cw.chart_id = c.id
    WHERE cw.status = 'published'
    ORDER BY cw.week_date DESC
    LIMIT 1
");

$top_entry = null;
if ($hero_week) {
    $top_entry = $wpdb->get_row($wpdb->prepare("
        SELECT e.*, t.title as track_title, t.slug as track_slug, a.name as artist_name, a.slug as artist_slug, t.artwork_url
        FROM {$prefix}chart_entries e
        JOIN {$prefix}tracks t ON e.track_id = t.id
        LEFT JOIN {$prefix}artists a ON t.primary_artist_id = a.id
        WHERE e.chart_week_id = %d
        ORDER BY e.rank ASC
        LIMIT 1
    ", $hero_week->id));
}

// 2. Get Featured Charts
$featured_charts = $wpdb->get_results("SELECT * FROM {$prefix}charts WHERE status = 'active' LIMIT 6");

// 3. Get Trending Rows (Heat & Momentum)
$trending_tracks = $wpdb->get_results("
    SELECT t.*, a.name as artist_name, COUNT(e.id) as appearance_count
    FROM {$prefix}tracks t
    JOIN {$prefix}chart_entries e ON e.track_id = t.id
    LEFT JOIN {$prefix}artists a ON t.primary_artist_id = a.id
    GROUP BY t.id
    ORDER BY appearance_count DESC
    LIMIT 5
");

$trending_artists = $wpdb->get_results("
    SELECT a.*, COUNT(e.id) as appearance_count
    FROM {$prefix}artists a
    JOIN {$prefix}tracks t ON t.primary_artist_id = a.id
    JOIN {$prefix}chart_entries e ON e.track_id = t.id
    GROUP BY a.id
    ORDER BY appearance_count DESC
    LIMIT 5
");

?>
<div class="kc-public-wrap">
    
    <!-- Hero Section: Premium chart stories with bold editorial pulse -->
    <header style="margin-bottom: 80px;">
        <div class="kc-split-grid" style="align-items: center;">
            <div>
                <span class="kc-public-badge new" style="margin-bottom: 20px;">
                    <?php echo $hero_week ? 'Week of ' . date('F j, Y', strtotime($hero_week->week_date)) : 'Kontentainment Charts'; ?>
                </span>
                <h1 class="kc-hero-title">Premium chart stories with a bold editorial pulse.</h1>
                <p class="kc-hero-subtitle" style="margin-left:0;">Track the week’s most influential artists, songs, and albums through a cinematic interface built for chart storytelling.</p>
                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <a href="<?php echo home_url('/charts/'); ?>" class="kc-btn">Open Charts</a>
                    <a href="<?php echo home_url('/charts/about/'); ?>" class="kc-btn kc-btn-ghost">About Charts</a>
                </div>
            </div>

            <!-- Featured Lead Item -->
            <div>
                <?php if ($top_entry) : ?>
                    <div class="kc-panel" style="padding:0; overflow:hidden; border: 2px solid #111;">
                        <img src="<?php echo esc_url($top_entry->artwork_url ?: 'https://picsum.photos/600/600?random=1'); ?>" style="width:100%; height: 350px; object-fit:cover; display:block;">
                        <div style="padding: 30px;">
                            <span class="kc-public-badge new" style="background:#111; color:#fff; border-radius:4px; margin-bottom: 10px;">Current #1 • <?php echo esc_html($hero_week->chart_name); ?></span>
                            <h2 style="font-size: 2.5rem; margin:10px 0; font-weight: 900;"><?php echo esc_html($top_entry->track_title); ?></h2>
                            <p style="font-size: 1.2rem; color: #666; font-weight: 700;">by <?php echo esc_html($top_entry->artist_name); ?></p>
                            <a href="<?php echo home_url('/charts/track/'.$top_entry->track_slug.'/'); ?>" style="display:inline-block; margin-top: 20px; font-weight: 800; color:#111; text-decoration: underline;">View Performance Details →</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="kc-empty-state" style="padding: 60px;">
                        <h3>No live chart data yet.</h3>
                        <p>The chart network is ready. Publish the first live week from the dashboard to activate this homepage.</p>
                        <a href="<?php echo home_url('/charts-dashboard/'); ?>" class="kc-btn kc-btn-ghost">Open Dashboard</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Featured Lists Section -->
    <section style="margin-bottom: 80px;">
        <div class="kc-section-header">
            <div>
                <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Chart Navigation</span>
                <h2 class="kc-section-title">Featured Lists</h2>
            </div>
            <a href="<?php echo home_url('/charts/'); ?>" style="font-weight:700; color:#111;">All Charts →</a>
        </div>
        <div class="kc-bento-grid">
            <?php if ($featured_charts) : foreach($featured_charts as $chart) : ?>
                <div class="kc-bento-item" style="grid-column: span 4;">
                    <p style="margin:0; font-size: 0.7rem; font-weight:800; color:#aaa; text-transform:uppercase;"><?php echo esc_html($chart->type); ?></p>
                    <h3 style="margin:10px 0; font-weight:900;"><a href="<?php echo home_url('/charts/'.$chart->slug.'/'); ?>" style="color:inherit; text-decoration:none;"><?php echo esc_html($chart->name); ?></a></h3>
                    <p style="font-size: 0.85rem; color:#666; margin-bottom: 20px;"><?php echo esc_html($chart->max_positions); ?> positions highlighting this week's movement.</p>
                    <a href="<?php echo home_url('/charts/'.$chart->slug.'/'); ?>" style="font-size: 0.75rem; font-weight:800; color:#111; text-decoration:none; border-bottom: 2px solid #111;">Open List →</a>
                </div>
            <?php endforeach; else: ?>
                <p style="grid-column: span 12; color:#888;">No chart categories are active yet.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Trending Split: Streaming Heat & Momentum Leaders -->
    <section style="margin-bottom: 80px;">
        <div class="kc-split-grid">
            
            <!-- Top Tracks Panel -->
            <div class="kc-panel">
                <div class="kc-panel-header">
                    <div>
                        <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Top Tracks</span>
                        <h2 style="margin:0; font-weight:900;">Streaming Heat</h2>
                    </div>
                </div>
                <?php if ($trending_tracks) : foreach($trending_tracks as $i => $track) : ?>
                    <div class="kc-mini-row">
                        <span class="kc-mini-row-rank"><?php echo ($i+1); ?></span>
                        <div style="flex-grow:1;">
                            <p style="margin:0; font-weight:800; font-size: 0.9rem;"><a href="<?php echo home_url('/charts/track/'.$track->slug.'/'); ?>" style="color:inherit; text-decoration:none;"><?php echo esc_html($track->title); ?></a></p>
                            <p style="margin:0; font-size: 0.75rem; color:#888;"><?php echo esc_html($track->artist_name); ?></p>
                        </div>
                        <span class="kc-public-badge same">—</span>
                    </div>
                <?php endforeach; else: ?>
                    <p style="color:#888; font-size: 0.9rem;">No live track chart data published yet.</p>
                <?php endif; ?>
                <a href="<?php echo home_url('/charts/tracks/'); ?>" style="display:block; margin-top:20px; font-size: 0.8rem; font-weight:800; text-align:center; color:#888; text-decoration:none;">View Full Track Directory</a>
            </div>

            <!-- Top Artists Panel -->
            <div class="kc-panel">
                <div class="kc-panel-header">
                    <div>
                        <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Top Artists</span>
                        <h2 style="margin:0; font-weight:900;">Momentum Leaders</h2>
                    </div>
                </div>
                <?php if ($trending_artists) : foreach($i = 0; $trending_artists as $artist) : ?>
                    <div class="kc-mini-row">
                        <span class="kc-mini-row-rank"><?php echo (++$i); ?></span>
                        <div style="flex-grow:1;">
                            <p style="margin:0; font-weight:800; font-size: 0.9rem;"><a href="<?php echo home_url('/charts/artist/'.$artist->slug.'/'); ?>" style="color:inherit; text-decoration:none;"><?php echo esc_html($artist->name); ?></a></p>
                            <p style="margin:0; font-size: 0.75rem; color:#888;"><?php echo esc_html($artist->country); ?></p>
                        </div>
                        <span class="kc-public-badge same">—</span>
                    </div>
                <?php endforeach; else: ?>
                    <p style="color:#888; font-size: 0.9rem;">No live artist chart data published yet.</p>
                <?php endif; ?>
                <a href="<?php echo home_url('/charts/artists/'); ?>" style="display:block; margin-top:20px; font-size: 0.8rem; font-weight:800; text-align:center; color:#888; text-decoration:none;">View Full Artist Directory</a>
            </div>

        </div>
    </section>

    <!-- Footer Discovery Bar -->
    <section>
        <div class="kc-split-grid">
             <div class="kc-panel dark" style="padding: 40px; border:none;">
                 <span style="font-size:0.7rem; font-weight:800; color:#888; text-transform:uppercase;">Discovery</span>
                 <h2 style="color:#fff; font-weight:900; margin:10px 0;">Browse the music library</h2>
                 <p style="color:#aaa; font-size: 0.9rem; margin-bottom: 25px;">Explore real artists and tracks that have been added to Kontentainment Charts across all historical periods.</p>
                 <div style="display:flex; gap: 15px;">
                      <a href="<?php echo home_url('/charts/artists/'); ?>" class="kc-btn" style="background:#fff; color:#111;">All Artists</a>
                      <a href="<?php echo home_url('/charts/tracks/'); ?>" class="kc-btn kc-btn-ghost" style="color:#fff; border-color:#fff;">All Tracks</a>
                 </div>
             </div>
             <div class="kc-panel" style="padding: 40px; border: 2px solid #111;">
                 <span style="font-size:0.7rem; font-weight:800; color:#888; text-transform:uppercase;">Methodology</span>
                 <h2 style="font-weight:900; margin:10px 0;">How the charts are built</h2>
                 <p style="color:#666; font-size: 0.9rem; margin-bottom: 25px;">Read the platform methodology, understand the publishing flow, and see how source data becomes a live chart week.</p>
                 <a href="<?php echo home_url('/charts/about/'); ?>" class="kc-btn kc-btn-ghost">Read Methodology</a>
             </div>
        </div>
    </section>

</div>
