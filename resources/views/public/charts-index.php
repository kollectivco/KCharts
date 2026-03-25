<div class="kc-public-wrap">
    
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-size: 3rem; font-weight: 900; margin: 0; letter-spacing: -1px;">Kontentainment Charts</h1>
        <p style="font-size: 1.2rem; color: #666; margin-top: 10px;">The official weekly music metrics and rankings.</p>
    </div>

    <div class="kc-public-grid">
        <?php
        global $wpdb;
        $prefix = $wpdb->prefix . 'kc_';
        $charts = $wpdb->get_results("SELECT * FROM {$prefix}charts WHERE status = 'active' ORDER BY name ASC");
        
        if ($charts) :
            foreach ($charts as $chart) :
                $latest_week = $wpdb->get_var($wpdb->prepare("SELECT week_date FROM {$prefix}chart_weeks WHERE chart_id = %d AND status = 'published' ORDER BY week_date DESC LIMIT 1", $chart->id));
        ?>
        <div class="kc-public-card <?php echo $chart->slug === 'global-top-50' ? 'dark' : ''; ?>">
            <h3><?php echo esc_html($chart->name); ?></h3>
            <p style="color:<?php echo $chart->slug === 'global-top-50' ? '#aaa' : '#666'; ?>;">The weekly <?php echo esc_html($chart->chart_type); ?> rankings.</p>
            <?php if ($latest_week) : ?>
                <a href="<?php echo home_url('/charts/' . $chart->slug . '/'); ?>" style="display:inline-block; margin-top:15px; color:<?php echo $chart->slug === 'global-top-50' ? '#fff' : '#000'; ?>; font-weight:bold; text-decoration:none;">View Latest Week →</a>
            <?php else : ?>
                <span style="display:inline-block; margin-top:15px; color:#999; font-size:0.8rem;">Coming Soon</span>
            <?php endif; ?>
        </div>
        <?php endforeach; else : ?>
        <p style="text-align:center; grid-column:span 3; color:#888;">No active charts found.</p>
        <?php endif; ?>
    </div>

</div>
