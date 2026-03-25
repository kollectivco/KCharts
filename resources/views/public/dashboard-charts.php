<?php
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

$charts = $wpdb->get_results("
    SELECT c.*, 
    (SELECT COUNT(*) FROM {$prefix}chart_weeks cw WHERE cw.chart_id = c.id AND cw.status = 'published') as week_count,
    (SELECT week_date FROM {$prefix}chart_weeks cw WHERE cw.chart_id = c.id AND cw.status = 'published' ORDER BY week_date DESC LIMIT 1) as latest_week
    FROM {$prefix}charts c
");
?>
<header style="margin-bottom: 40px;">
    <h1 class="kc-hero-title" style="font-size: 2rem;">Chart Status</h1>
    <p style="color: #666;">Monitoring active rankings and published data windows.</p>
</header>

<div class="kc-bento-grid">
    <?php if ($charts) : foreach ($charts as $c) : ?>
    <div class="kc-bento-item" style="grid-column: span 12;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h3 style="margin:0; font-weight: 900;"><?php echo esc_html($c->name); ?></h3>
                <p style="margin:5px 0 0 0; color:#888; font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">
                    <?php echo (int)$c->max_positions; ?> Positions • <?php echo esc_html($c->status); ?>
                </p>
            </div>
            <div style="text-align: right;">
                <p style="margin:0; font-weight: 800; font-size: 1.2rem;"><?php echo (int)$c->week_count; ?></p>
                <p style="margin:0; color:#888; font-size: 0.75rem;">Total Weeks</p>
            </div>
        </div>
        
        <div style="margin-top:20px; padding-top:20px; border-top: 1px solid #eee; display: flex; gap: 40px;">
            <div>
                <p style="margin:0; font-size: 0.7rem; color: #aaa; text-transform: uppercase; font-weight: 800;">Latest Publication</p>
                <p style="margin:5px 0 0 0; font-weight: 700;"><?php echo $c->latest_week ?: 'NONE'; ?></p>
            </div>
            <div>
                <p style="margin:0; font-size: 0.7rem; color: #aaa; text-transform: uppercase; font-weight: 800;">Registry ID</p>
                <p style="margin:5px 0 0 0; font-family: monospace; font-weight: 700;">#<?php echo (int)$c->id; ?></p>
            </div>
            <div style="margin-left:auto;">
                 <a href="<?php echo home_url('/charts/'.$c->slug.'/'); ?>" style="font-size: 0.8rem; font-weight: 800; color:#111; text-decoration: underline;">View Public Page</a>
            </div>
        </div>
    </div>
    <?php endforeach; else : ?>
        <p style="color:#888; text-align:center; grid-column:span 12; padding:80px; border:2px dashed #eee; border-radius:20px;">No charts configured in registry.</p>
    <?php endif; ?>
</div>
