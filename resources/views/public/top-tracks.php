<div class="kc-public-wrap">
    <h1 style="font-size: 2.5rem; font-weight: 800; margin: 0 0 20px 0; letter-spacing: -1px;">Top Tracks Directory</h1>
    <p style="color:#666; font-size:1.1rem; margin-bottom: 40px;">Highest performing songs recorded in our database.</p>
    
    <div class="kc-public-grid">
        <?php
        global $wpdb;
        $prefix = $wpdb->prefix . 'kc_';
        $tracks = $wpdb->get_results("
            SELECT t.*, a.name as artist_name, COUNT(e.id) as total_weeks, MIN(e.rank) as peak_pos
            FROM {$prefix}tracks t
            LEFT JOIN {$prefix}artists a ON t.primary_artist_id = a.id
            JOIN {$prefix}chart_entries e ON e.track_id = t.id
            GROUP BY t.id
            ORDER BY total_weeks DESC
            LIMIT 20
        ");
        
        if ($tracks) : foreach($tracks as $track): ?>
        <div class="kc-public-card" style="padding: 20px;">
            <a href="<?php echo home_url('/charts/track/'.$track->slug.'/'); ?>" style="display:flex; align-items:center; text-decoration:none; color:inherit; gap: 15px;">
                <div style="width: 60px; height: 60px; background: #eee url('<?php echo esc_url($track->artwork_url ?: 'https://picsum.photos/100/100?random='.$track->id+100); ?>'); background-size:cover; border-radius: 6px;"></div>
                <div>
                    <h3 style="font-size: 1.1rem; margin: 0 0 5px 0;"><?php echo esc_html($track->title); ?></h3>
                    <p style="color:#888; font-size:0.85rem; margin:0;"><?php echo esc_html($track->artist_name); ?> • Peak: #<?php echo (int)$track->peak_pos; ?></p>
                </div>
                <div style="margin-left:auto;">
                    <span class="kc-public-badge up" style="font-size: 0.7rem; padding: 4px 6px;"><?php echo (int)$track->total_weeks; ?> Weeks</span>
                </div>
            </a>
        </div>
        <?php endforeach; else : ?>
            <p>No tracks found.</p>
        <?php endif; ?>
    </div>
</div>
