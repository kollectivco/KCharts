<div class="kc-public-wrap">
    <h1 style="font-size: 2.5rem; font-weight: 800; margin: 0 0 20px 0; letter-spacing: -1px;">Top Artists Archive</h1>
    <p style="color:#666; font-size:1.1rem; margin-bottom: 40px;">Explore the most charted entities from our parsed data.</p>
    
    <div class="kc-public-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
        <?php
        global $wpdb;
        $prefix = $wpdb->prefix . 'kc_';
        $artists = $wpdb->get_results("
            SELECT a.*, COUNT(e.id) as total_weeks
            FROM {$prefix}artists a
            JOIN {$prefix}tracks t ON t.primary_artist_id = a.id
            JOIN {$prefix}chart_entries e ON e.track_id = t.id
            WHERE e.rank IS NOT NULL
            GROUP BY a.id
            ORDER BY total_weeks DESC
            LIMIT 24
        ");
        
        if ($artists) : foreach($artists as $artist): ?>
        <div class="kc-public-card" style="text-align:center; padding: 20px;">
            <a href="<?php echo home_url('/charts/artist/'.$artist->slug.'/'); ?>" style="text-decoration:none; color:inherit;">
                <div style="width: 100px; height: 100px; background: #eee url('<?php echo esc_url($artist->image_url ?: 'https://picsum.photos/100/100?random='.$artist->id); ?>'); background-size:cover; border-radius: 50%; margin: 0 auto 15px auto;"></div>
                <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?php echo esc_html($artist->name); ?></h3>
                <p style="color:#888; font-size:0.85rem; margin:0;"><?php echo (int)$artist->total_weeks; ?> Weeks on Chart</p>
            </a>
        </div>
        <?php endforeach; else : ?>
            <p style="text-align:center; grid-column:span 3; color:#888;">No artists found.</p>
        <?php endif; ?>
    </div>
</div>
