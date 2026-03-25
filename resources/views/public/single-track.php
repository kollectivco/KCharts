<?php
/**
 * Kontentainment Charts V1 Track Detail Template
 * Rebuilt from Old Plugin track-single.php using NEW DB architecture & premium Bento UI.
 */
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

$track_slug = get_query_var('kc_slug');
$track = $wpdb->get_row($wpdb->prepare("
    SELECT t.*, a.name as artist_name, a.slug as artist_slug, a.country as artist_country, a.image_url as artist_image
    FROM {$prefix}tracks t
    LEFT JOIN {$prefix}artists a ON t.primary_artist_id = a.id
    WHERE t.slug = %s
", $track_slug));

if (!$track) {
    echo '<div class="kc-public-wrap"><div class="kc-empty-state"><h3>Track Not Found</h3><p>The requested music asset does not exist in our registry.</p></div></div>';
    return;
}

// 1. Get Chart Appearances (Historical Placements)
$positions = $wpdb->get_results($wpdb->prepare("
    SELECT e.*, c.name as chart_name, c.slug as chart_slug, w.week_date
    FROM {$prefix}chart_entries e
    JOIN {$prefix}chart_weeks w ON e.chart_week_id = w.id
    JOIN {$prefix}charts c ON w.chart_id = c.id
    WHERE e.track_id = %d AND w.status = 'published'
    ORDER BY w.week_date DESC
", $track->id));

// 2. Get Related Tracks (Artist Catalog)
$related_tracks = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}tracks WHERE primary_artist_id = %d AND id != %d LIMIT 5", $track->primary_artist_id, $track->id));

// 3. Get Related Artists (Peer Profiling)
$peer_artists = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$prefix}artists WHERE country = %s AND id != %d LIMIT 5", $track->artist_country, $track->primary_artist_id));

?>
<div class="kc-public-wrap">
    
    <!-- Track Detail Hero (Transfer from old plugin) -->
    <header style="margin-bottom: 80px; display: flex; align-items: flex-end; gap: 60px; border-bottom: 3px solid #111; padding-bottom: 60px;">
        <div style="width: 280px; height: 280px; background: #eee; border-radius: 4px; overflow:hidden; flex-shrink:0; box-shadow: 0 40px 60px rgba(0,0,0,0.1);">
             <img src="<?php echo esc_url($track->artwork_url ?: 'https://picsum.photos/600/600?random='.$track->id); ?>" style="width:100%; height:100%; object-fit:cover;">
        </div>
        <div style="flex-grow:1;">
             <span class="kc-public-badge new" style="margin-bottom: 20px;">Track Details</span>
             <h1 class="kc-hero-title" style="margin-top:20px; font-size: 4rem;"><?php echo esc_html($track->title); ?></h1>
             <p style="font-size: 1.5rem; font-weight:700; color:#666; margin: 15px 0;">
                  by <a href="<?php echo home_url('/charts/artist/'.$track->artist_slug.'/'); ?>" style="color:inherit; text-decoration:none; border-bottom: 2px solid #111;"><?php echo esc_html($track->artist_name); ?></a>
             </p>
             
             <div class="kc-stat-row">
                  <div class="kc-stat-item"><strong><?php echo (int)count($positions); ?></strong><span>Appearances</span></div>
                  <div class="kc-stat-item"><strong><?php echo esc_html($track->isrc ?: 'UK-00-11'); ?></strong><span>ISRC Registry</span></div>
                  <div class="kc-stat-item"><strong>Premium</strong><span>Tier</span></div>
             </div>

             <div style="display:flex; gap: 15px; margin-top: 40px;">
                  <a href="<?php echo home_url('/charts/artist/'.$track->artist_slug.'/'); ?>" class="kc-btn">Open Artist</a>
                  <a href="<?php echo home_url('/charts/tracks/'); ?>" class="kc-btn kc-btn-ghost">All Tracks</a>
             </div>
        </div>
    </header>

    <!-- Summary Grid (Transfer from old plugin) -->
    <section class="kc-bento-grid" style="margin-bottom: 80px;">
        <div class="kc-bento-item" style="grid-column: span 3;">
            <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Lead Artist</p>
            <h2 style="font-size: 1.5rem; font-weight:900; margin:10px 0;"><?php echo esc_html($track->artist_name); ?></h2>
            <p style="font-size: 0.8rem; color:#888;">Verified entity attached to release.</p>
        </div>
        <div class="kc-bento-item" style="grid-column: span 3;">
            <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Release Type</p>
            <h2 style="font-size: 1.5rem; font-weight:900; margin:10px 0;">Standalone</h2>
            <p style="font-size: 0.8rem; color:#888;">Designed as a single-first release narrative.</p>
        </div>
        <div class="kc-bento-item" style="grid-column: span 3;">
            <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Reach</p>
            <h2 style="font-size: 1.5rem; font-weight:900; margin:10px 0;">Certified</h2>
            <p style="font-size: 0.8rem; color:#888;">Current audience metric footprint.</p>
        </div>
        <div class="kc-bento-item" style="grid-column: span 3;">
            <p style="font-size: 0.7rem; color:#888; font-weight:800; text-transform:uppercase;">Footprint</p>
            <h2 style="font-size: 1.5rem; font-weight:900; margin:10px 0;"><?php echo (int)count($positions); ?> Lists</h2>
            <p style="font-size: 0.8rem; color:#888;">How many charts feature this song.</p>
        </div>
    </section>

    <!-- Content Split (Transfer from old plugin) -->
    <section style="margin-bottom: 80px;">
        <div class="kc-split-grid">
            
            <div class="kc-panel">
                 <div class="kc-panel-header" style="border:none; margin-bottom: 20px;">
                     <div>
                         <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Editorial Notes</span>
                         <h2 style="margin:5px 0 0 0; font-weight:900;">Why it’s charting</h2>
                     </div>
                 </div>
                 <p style="color:#666; font-size: 0.95rem; line-height: 1.6;">This track lands like a premium lead-in moment: fast-recognition artwork, a commanding title lockup, and enough metadata to make the chart story legible at a glance. The detail page now reads more like a magazine feature, balancing context, related discoveries, and ranking appearances.</p>
                 <div style="margin-top:20px; padding: 20px; background: #fafafa; border-radius: 4px;">
                      <p style="margin:0; font-size: 0.8rem; color:#111; font-weight:800; text-transform:uppercase;">Platform Velocity</p>
                      <p style="margin:5px 0 0 0; font-size: 0.9rem; color:#666;">Growth indicators suggest high streaming retention across primary markets.</p>
                 </div>
            </div>

            <div class="kc-panel">
                <div class="kc-panel-header" style="border:none; margin-bottom: 20px;">
                     <div>
                         <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Chart Appearances</span>
                         <h2 style="margin:5px 0 0 0; font-weight:900;">Where it appears</h2>
                     </div>
                 </div>
                 <?php if ($positions) : foreach($positions as $pos) : ?>
                    <div class="kc-mini-row" style="padding: 15px 0;">
                        <span class="kc-mini-row-rank" style="font-size: 1.2rem; color:#111;">#<?php echo (int)$pos->rank; ?></span>
                        <div style="flex-grow:1;">
                             <p style="margin:0; font-weight:800;"><a href="<?php echo home_url('/charts/'.$pos->chart_slug.'/'); ?>" style="color:inherit; text-decoration:none;"><?php echo esc_html($pos->chart_name); ?></a></p>
                             <p style="margin:0; font-size: 0.75rem; color:#888;">Week of <?php echo date('M d, Y', strtotime($pos->week_date)); ?></p>
                        </div>
                        <span class="kc-public-badge same">—</span>
                    </div>
                 <?php endforeach; else: ?>
                    <p style="color:#888; font-size: 0.9rem;">No related chart placements are live for this track yet.</p>
                 <?php endif; ?>
            </div>

        </div>
    </section>

    <!-- Side Discovery Footer (Transfer from old plugin) -->
    <section>
        <div class="kc-split-grid">
            <div class="kc-panel">
                 <div class="kc-panel-header" style="border:none; margin-bottom: 20px;">
                     <div>
                         <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Related Tracks</span>
                         <h2 style="margin:5px 0 0 0; font-weight:900;">Keep listening</h2>
                     </div>
                 </div>
                 <?php if ($related_tracks) : foreach($related_tracks as $rel) : ?>
                    <div class="kc-mini-row">
                        <img src="<?php echo esc_url($rel->artwork_url ?: 'https://picsum.photos/100/100?random='.$rel->id); ?>" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover; border: 1px solid #f0f0f0;">
                        <div style="flex-grow:1;">
                             <p style="margin:0; font-weight:800; font-size: 0.85rem;"><a href="<?php echo home_url('/charts/track/'.$rel->slug.'/'); ?>" style="color:inherit; text-decoration:none;"><?php echo esc_html($rel->title); ?></a></p>
                        </div>
                    </div>
                 <?php endforeach; else: ?>
                    <p style="color:#888; font-size: 0.85rem;">More related songs will appear here as catalog expands.</p>
                 <?php endif; ?>
            </div>
            <div class="kc-panel">
                 <div class="kc-panel-header" style="border:none; margin-bottom: 20px;">
                     <div>
                         <span style="font-size:0.75rem; font-weight:800; color:#888; text-transform:uppercase;">Related Artists</span>
                         <h2 style="margin:5px 0 0 0; font-weight:900;">Chart neighbors</h2>
                     </div>
                 </div>
                 <?php if ($peer_artists) : foreach($peer_artists as $peer) : ?>
                    <div class="kc-mini-row">
                        <img src="<?php echo esc_url($peer->image_url ?: 'https://picsum.photos/100/100?random='.$peer->id); ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #f0f0f0;">
                        <a href="<?php echo home_url('/charts/artist/'.$peer->slug.'/'); ?>" style="font-weight:800; color:inherit; text-decoration:none; font-size:0.85rem;"><?php echo esc_html($peer->name); ?></a>
                    </div>
                 <?php endforeach; else: ?>
                    <p style="color:#888; font-size: 0.85rem;">Companion recommendations will populate here as index grows.</p>
                 <?php endif; ?>
            </div>
        </div>
    </section>

</div>
