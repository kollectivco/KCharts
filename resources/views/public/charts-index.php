<?php
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

// 1. Get Latest Global Chart Snapshot
$latest_global = $wpdb->get_row("
    SELECT c.name, c.slug, w.week_date, w.id as week_id
    FROM {$prefix}charts c
    JOIN {$prefix}chart_weeks w ON c.id = w.chart_id
    WHERE w.status = 'published'
    ORDER BY w.week_date DESC
    LIMIT 1
");

// 2. Get Top 3 Tracks Snapshot
$top_tracks = [];
if ($latest_global) {
    $top_tracks = $wpdb->get_results($wpdb->prepare("
        SELECT e.rank, t.title, a.name as artist_name, t.artwork_url, t.slug as track_slug
        FROM {$prefix}chart_entries e
        JOIN {$prefix}tracks t ON e.track_id = t.id
        LEFT JOIN {$prefix}artists a ON t.primary_artist_id = a.id
        WHERE e.chart_week_id = %d
        ORDER BY e.rank ASC
        LIMIT 3
    ", $latest_global->week_id));
}

// 3. Get All Active Charts
$all_charts = $wpdb->get_results("SELECT * FROM {$prefix}charts WHERE status = 'active' ORDER BY name ASC");

// 4. Get Top 5 Artists Snapshot
$featured_artists = $wpdb->get_results("
    SELECT a.*, COUNT(e.id) as total_weeks
    FROM {$prefix}artists a
    JOIN {$prefix}tracks t ON t.primary_artist_id = a.id
    JOIN {$prefix}chart_entries e ON e.track_id = t.id
    GROUP BY a.id
    ORDER BY total_weeks DESC
    LIMIT 5
");

?>
<div class="kc-public-wrap">
    
    <!-- Hero Section -->
    <header style="text-align: center; margin-bottom: 80px;">
        <h1 class="kc-hero-title">The Music Authority</h1>
        <p class="kc-hero-subtitle">Discover the official weekly rankings, verified consumption data, and historical chart performance from across the industry.</p>
        <div style="display: flex; gap: 15px; justify-content: center;">
            <a href="<?php echo home_url('/charts/tracks/'); ?>" class="kc-link-card" style="padding: 12px 24px; background: #111; color: #fff; border:none;">Explore Tracks</a>
            <a href="<?php echo home_url('/charts/artists/'); ?>" class="kc-link-card" style="padding: 12px 24px;">View Artist Directory</a>
        </div>
    </header>

    <?php if ($latest_global) : ?>
    <!-- Featured Chart Hero Spot -->
    <section class="kc-spotlight">
        <?php 
        $top_img = !empty($top_tracks[0]->artwork_url) ? $top_tracks[0]->artwork_url : 'https://picsum.photos/400/400?random=chart'; 
        ?>
        <img src="<?php echo esc_url($top_img); ?>" class="kc-spotlight-img" alt="Top Track">
        <div class="kc-spotlight-content">
            <span class="kc-public-badge new">Current #1</span>
            <p style="margin: 15px 0 0 0; color: #666; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                <?php echo esc_html($latest_global->name); ?> • Week of <?php echo date('M d', strtotime($latest_global->week_date)); ?>
            </p>
            <h2><?php echo esc_html($top_tracks[0]->track_title ?? 'Untitled'); ?></h2>
            <p style="font-size: 1.5rem; color: #666; margin-bottom: 30px;"><?php echo esc_html($top_tracks[0]->artist_name ?? 'Unknown Artist'); ?></p>
            <a href="<?php echo home_url('/charts/'.$latest_global->slug.'/'); ?>" style="font-weight: 800; color: #111; text-decoration: underline; text-underline-offset: 6px;">View Full Ranking →</a>
        </div>
    </section>
    <?php endif; ?>

    <!-- Secondary Bento Section -->
    <div class="kc-bento-grid">
        
        <!-- Active Charts List -->
        <div class="kc-bento-item" style="grid-column: span 8;">
            <div class="kc-section-header">
                <h2 class="kc-section-title">Active Rankings</h2>
                <a href="<?php echo home_url('/charts/about/'); ?>" style="font-size: 0.8rem; font-weight: 700;">Methodology</a>
            </div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <?php if ($all_charts) : foreach($all_charts as $chart) : ?>
                    <a href="<?php echo home_url('/charts/'.$chart->slug.'/'); ?>" class="kc-link-card">
                        <div style="width: 12px; height: 12px; border-radius: 50%; background: #111;"></div>
                        <div>
                            <p style="margin:0; font-weight:700;"><?php echo esc_html($chart->name); ?></p>
                            <p style="margin:0; font-size: 0.8rem; color:#888;"><?php echo esc_html($chart->max_positions); ?> Positions</p>
                        </div>
                    </a>
                <?php endforeach; else: ?>
                    <p style="color:#888;">No active charts found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Artists Snapshot -->
        <div class="kc-bento-item dark" style="grid-column: span 4;">
            <h2 class="kc-section-title" style="color:#fff; margin-bottom: 25px;">Leading Artists</h2>
            <?php if ($featured_artists) : foreach($featured_artists as $artist) : ?>
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: #333 url('<?php echo esc_url($artist->image_url ?: 'https://picsum.photos/100/100?random='.$artist->id); ?>'); background-size: cover;"></div>
                    <div style="flex-grow: 1;">
                        <p style="margin:0; font-weight:700; font-size: 0.9rem;"><?php echo esc_html($artist->name); ?></p>
                        <p style="margin:0; font-size: 0.75rem; color:#aaa;"><?php echo (int)$artist->total_weeks; ?> Wks Charted</p>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <p style="color:#555;">No records indexed.</p>
            <?php endif; ?>
            <a href="<?php echo home_url('/charts/artists/'); ?>" style="display:block; margin-top: 15px; color:#fff; text-decoration:none; font-size: 0.8rem; font-weight: 700;">Artist Directory →</a>
        </div>

    </div>

    <?php if (!$latest_global && !$all_charts) : ?>
    <!-- Empty State -->
    <div class="kc-empty-state">
        <h3>Chartering the Future</h3>
        <p>There are no published charts available at this time. Check back soon for the latest industry data.</p>
        <a href="<?php echo home_url(); ?>" class="kc-link-card" style="display: inline-flex; border-color: #111;">Return to Home</a>
    </div>
    <?php endif; ?>

</div>
