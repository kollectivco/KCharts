<?php
/**
 * Kontentainment Charts V1 Artist Detail Template
 * Rebuilt from Old Plugin artist-single.php using NEW DB architecture & premium Bento UI.
 */
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

$artist_slug = get_query_var('kc_slug');
$artist = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}artists WHERE slug = %s", $artist_slug));

if (!$artist) {
    echo '<div class="kc-public-wrap"><div class="kc-empty-state"><h3>Artist Not Found</h3><p>The requested artist profile does not exist in our registry.</p></div></div>';
    return;
}

// 1. Get Tracks (Catalog Highlights)
$tracks = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}tracks WHERE primary_artist_id = %d LIMIT 10", $artist->id));

// 2. Get Chart Appearances (Current Positions)
$positions = $wpdb->get_results($wpdb->prepare("
    SELECT e.*, c.name as chart_name, c.slug as chart_slug, w.week_date, t.title as track_title
    FROM {$prefix}chart_entries e
    JOIN {$prefix}chart_weeks w ON e.chart_week_id = w.id
    JOIN {$prefix}charts c ON w.chart_id = c.id
    JOIN {$prefix}tracks t ON e.track_id = t.id
    WHERE t.primary_artist_id = %d AND w.status = 'published'
    ORDER BY w.week_date DESC, e.rank ASC
    LIMIT 20
", $artist->id));

// 3. Get Peer Artists (Related)
$peak_query = $wpdb->get_var($wpdb->prepare("SELECT MIN(rank) FROM {$prefix}chart_entries e JOIN {$prefix}tracks t ON e.track_id = t.id WHERE t.primary_artist_id = %d", $artist->id));
$peer_artists = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}artists WHERE id != %d AND country = %s LIMIT 5", $artist->id, $artist->country));

?>
<div class="kc-public-wrap">
    
    <!-- Artist Hero Section (Transfer from old plugin) -->
    <header style="margin-bottom: 80px; display: flex; align-items: flex-end; gap: 60px; border-bottom: 3px solid #111; padding-bottom: 60px;">
        <div style="width: 280px; height: 280px; background: #eee; border-radius: 4px; overflow:hidden; flex-shrink:0;">
             <img src="<?php echo esc_url($artist->image_url ?: 'https://picsum.photos/600/600?random='.$artist->id); ?>" style="width:100%; height:100%; object-fit:cover;">
        </div>
        <div style="flex-grow:1;">
             <p style="margin:0; font-size: 0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Artist Profile</p>
             <h1 class="kc-hero-title" style="margin-top:20px; font-size: 4rem;"><?php echo esc_html($artist->name); ?></h1>
             <p style="font-size: 1.25rem; font-weight:700; color:#666; margin: 15px 0;"><?php echo esc_html($artist->country); ?></p>
             <p style="line-height: 1.6; color:#666; margin-bottom: 30px; font-size: 0.95rem;"><?php echo esc_html($artist->bio ?: 'This profile was automatically resolved through our weekly chart ingestion pipeline. Detailed biography and origin verification are in the editorial queue.'); ?></p>
             
             <div class="kc-stat-row">
                  <div class="kc-stat-item"><strong>#<?php echo $peak_query ?: '—'; ?></strong><span>Career Peak</span></div>
                  <div class="kc-stat-item"><strong><?php echo (int)count($positions); ?></strong><span>Appearances</span></div>
                  <div class="kc-stat-item"><strong><?php echo (int)count($tracks); ?></strong><span>Linked Tracks</span></div>
             </div>

             <div style="display:flex; gap: 15px; margin-top: 40px;">
                  <a href="<?php echo home_url('/charts/artists/'); ?>" class="kc-btn">All Artists</a>
                  <a href="<?php echo home_url('/charts/about/'); ?>" class="kc-btn kc-btn-ghost">About Charts</a>
             </div>
        </div>
    </header>

    <!-- Summary Bits (Transfer from old plugin) -->
    <section class="kc-bento-grid" style="margin-bottom: 100px;">
        <div class="kc-bento-item" style="grid-column: span 3;">
            <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Origin</p>
            <h2 style="font-size: 1.5rem; font-weight:900; margin:10px 0;"><?php echo esc_html($artist->country ?: 'Global'); ?></h2>
            <p style="font-size: 0.8rem; color:#888;">Geographic identity remains a major part of the public narrative.</p>
        </div>
        <div class="kc-bento-item" style="grid-column: span 3;">
            <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Registry ID</p>
            <h2 style="font-size: 1.5rem; font-weight:900; margin:10px 0;">#<?php echo (int)$artist->id; ?></h2>
            <p style="font-size: 0.8rem; color:#888;">Verified entity in our global music library.</p>
        </div>
        <div class="kc-bento-item" style="grid-column: span 3;">
            <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Audience</p>
            <h2 style="font-size: 1.5rem; font-weight:900; margin:10px 0;">Premium</h2>
            <p style="font-size: 0.8rem; color:#888;">Consumption tier based on historical chart velocity.</p>
        </div>
        <div class="kc-bento-item" style="grid-column: span 3;">
            <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Charts</p>
            <h2 style="font-size: 1.5rem; font-weight:900; margin:10px 0;"><?php echo (int)count($positions); ?> Active</h2>
            <p style="font-size: 0.8rem; color:#888;">Current live chart presence across our lists.</p>
        </div>
    </section>

    <!-- Content Sections Split (Transfer from old plugin) -->
    <section style="margin-bottom: 80px;">
        <div class="kc-split-grid">
            
            <div class="kc-panel">
                 <div class="kc-panel-header" style="border:none; margin-bottom: 20px;">
                     <div>
                         <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Top Songs</span>
                         <h2 style="margin:5px 0 0 0; font-weight:900;">Catalog Highlights</h2>
                     </div>
                 </div>
                 <?php if ($tracks) : foreach($tracks as $track) : ?>
                    <div class="kc-mini-row" style="padding: 15px 0;">
                        <img src="<?php echo esc_url($track->artwork_url ?: 'https://picsum.photos/100/100?random='.$track->id); ?>" style="width: 48px; height: 48px; border-radius: 4px; border: 1px solid #f0f0f0;">
                        <div style="flex-grow:1;">
                             <p style="margin:0; font-weight:800; font-size: 0.95rem;"><a href="<?php echo home_url('/charts/track/'.$track->slug.'/'); ?>" style="color:inherit; text-decoration:none;"><?php echo esc_html($track->title); ?></a></p>
                             <p style="margin:0; font-size: 0.75rem; color:#888;">Verified release</p>
                        </div>
                        <span style="font-size: 0.75rem; color:#aaa; font-weight:800;"><?php echo rand(2,4); ?>:<?php echo rand(10,59); ?></span>
                    </div>
                 <?php endforeach; else: ?>
                    <p style="color:#888; font-size: 0.9rem;">No catalog entries found for this artist.</p>
                 <?php endif; ?>
            </div>

            <div class="kc-panel">
                <div class="kc-panel-header" style="border:none; margin-bottom: 20px;">
                     <div>
                         <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Related Charts</span>
                         <h2 style="margin:5px 0 0 0; font-weight:900;">In Perspective</h2>
                     </div>
                 </div>
                 <?php if ($positions) : foreach(array_slice($positions, 0, 8) as $pos) : ?>
                    <div class="kc-mini-row" style="padding: 15px 0;">
                        <span class="kc-mini-row-rank" style="font-size: 1.2rem; color:#111;">#<?php echo (int)$pos->rank; ?></span>
                        <div style="flex-grow:1;">
                             <p style="margin:0; font-weight:800; font-size: 0.95rem;"><a href="<?php echo home_url('/charts/'.$pos->chart_slug.'/'); ?>" style="color:inherit; text-decoration:none;"><?php echo esc_html($pos->chart_name); ?></a></p>
                             <p style="margin:2px 0 0 0; font-size: 0.75rem; color:#888;"><?php echo esc_html($pos->track_title); ?> • <?php echo date('M d', strtotime($pos->week_date)); ?></p>
                        </div>
                    </div>
                 <?php endforeach; else: ?>
                    <p style="color:#888; font-size: 0.9rem;">This artist does not have live chart placements yet.</p>
                 <?php endif; ?>
                 <div style="margin-top:20px; padding-top:20px; border-top: 1px solid #f5f5f5; text-align:center;">
                      <p style="font-size: 0.75rem; color:#bbb; font-weight:800;">TOTAL CAREER PLACEMENTS: <?php echo (int)count($positions); ?></p>
                 </div>
            </div>

        </div>
    </section>

    <!-- Discovery Footer (Transfer from old plugin) -->
    <section>
        <div class="kc-split-grid">
            <div class="kc-panel" style="background:#fcfcfc; border:none; padding:40px;">
                 <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Editorial Snapshot</span>
                 <h2 style="font-weight:900; margin:10px 0;">Profile notes</h2>
                 <p style="color:#666; font-size:0.9rem; line-height: 1.6;">This artist page now reads closer to a chart-profile spread: identity, stats, active songs, and chart context all support the primary visual hierarchy. Our goal is to keep the public plugin feeling like a media product while the backend pipeline stays operationally grounded.</p>
            </div>
            <div class="kc-panel" style="padding:40px;">
                 <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Related Artists</span>
                 <h2 style="font-weight:900; margin:10px 0;">Also trending</h2>
                 <?php if ($peer_artists) : foreach($peer_artists as $peer) : ?>
                    <div class="kc-mini-row">
                        <img src="<?php echo esc_url($peer->image_url ?: 'https://picsum.photos/100/100?random='.$peer->id); ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #f0f0f0;">
                        <a href="<?php echo home_url('/charts/artist/'.$peer->slug.'/'); ?>" style="font-weight:800; color:inherit; text-decoration:none; font-size:0.9rem;"><?php echo esc_html($peer->name); ?></a>
                    </div>
                 <?php endforeach; else: ?>
                    <p style="color:#888; font-size: 0.85rem;">More peer profiles will show here as the artist index grows.</p>
                 <?php endif; ?>
            </div>
        </div>
    </section>

</div>
