<?php
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

$total_records = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}chart_entries");
$total_artists = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}artists");
$total_tracks = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}tracks");

$php_version = phpversion();
$wp_version = get_bloginfo('version');
$db_version = $wpdb->db_version();

?>
<header style="margin-bottom: 40px;">
    <h1 class="kc-hero-title" style="font-size: 2rem;">System Status</h1>
    <p style="color: #666;">Core infrastructure health and dataset metrics.</p>
</header>

<div class="kc-bento-grid">
    
    <!-- Dataset Size -->
    <div class="kc-bento-item" style="grid-column: span 12;">
        <h3 class="kc-section-title" style="margin-bottom: 30px;">Dataset Footprint</h3>
        <div style="display: flex; gap: 60px;">
            <div>
                <p style="margin:0; font-size: 0.75rem; text-transform: uppercase; color: #888; font-weight: 800;">Total Rankings Indexed</p>
                <h2 style="font-size: 3rem; margin:10px 0; font-weight: 900; letter-spacing: -2px;"><?php echo number_format($total_records); ?></h2>
                <p style="margin:0; font-size: 0.8rem; color: #666;">Historical chart entries</p>
            </div>
            <div>
                <p style="margin:0; font-size: 0.75rem; text-transform: uppercase; color: #888; font-weight: 800;">Entity Registry</p>
                <h2 style="font-size: 3rem; margin:10px 0; font-weight: 900; letter-spacing: -2px;"><?php echo number_format($total_artists + $total_tracks); ?></h2>
                <p style="margin:0; font-size: 0.8rem; color: #666;">Unique Artists & Tracks</p>
            </div>
        </div>
    </div>

    <!-- Infrastructure -->
    <div class="kc-bento-item dark" style="grid-column: span 6;">
        <h3 class="kc-section-title" style="color:#fff; margin-bottom: 25px;">Core Stack</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <span style="display:block; font-size: 0.7rem; color: #888; text-transform: uppercase;">PHP</span>
                <span style="font-weight: 700; font-size: 1.1rem; color:#fff;"><?php echo $php_version; ?></span>
            </div>
            <div>
                <span style="display:block; font-size: 0.7rem; color: #888; text-transform: uppercase;">WordPress</span>
                <span style="font-weight: 700; font-size: 1.1rem; color:#fff;"><?php echo $wp_version; ?></span>
            </div>
            <div style="grid-column: span 2;">
                <span style="display:block; font-size: 0.7rem; color: #888; text-transform: uppercase;">Database Engine</span>
                <span style="font-weight: 700; font-size: 1.1rem; color:#fff;"><?php echo $db_version; ?></span>
            </div>
        </div>
    </div>

    <!-- Health Check Placeholder -->
    <div class="kc-bento-item" style="grid-column: span 6; display: flex; align-items: center; justify-content: center; text-align: center;">
        <div>
            <div style="width: 80px; height: 80px; background: #e6fffa; color:#0694a2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
               <svg viewBox="0 0 24 24" width="40" height="40" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            </div>
            <h3 style="margin:0; font-weight: 900;">All Systems Normal</h3>
            <p style="margin:5px 0 0 0; color: #666; font-size: 0.9rem;">No infrastructure alerts detected.</p>
        </div>
    </div>

</div>
