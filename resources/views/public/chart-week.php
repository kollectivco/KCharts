<div class="kc-public-wrap">
    
    <div style="margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px; display:flex; justify-content:space-between; align-items:flex-end;">
        <div>
            <span style="font-weight:700; text-transform:uppercase; color:#ff3b3b; letter-spacing:1px; font-size:0.9rem;">Week of <?php echo esc_html(date('M d, Y', strtotime($week_date))); ?></span>
            <h1 style="font-size: 2.5rem; font-weight: 800; margin: 10px 0 0 0; letter-spacing: -1px;"><?php echo esc_html($chart_name); ?></h1>
        </div>
        <div style="text-align:right;">
            <p style="margin:0; color:#666; font-size:0.9rem;">Powered by Kontentainment Charts</p>
        </div>
    </div>

    <table class="kc-chart-table">
        <thead>
            <tr>
                <th style="width: 80px;">Rnk</th>
                <th style="width: 60px;">Mov</th>
                <th>Track & Artist</th>
                <th style="text-align:right;">Peak</th>
                <th style="text-align:right;">Wks.</th>
                <th style="text-align:right;">Metrics</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty($entries) ) : ?>
                <?php foreach( $entries as $entry ): 
                    $img_style = !empty($entry->artwork_url) ? "background-image:url('".esc_url($entry->artwork_url)."');" : "background-color:#eaeaea;";
                ?>
                <tr>
                    <td class="kc-rank"><?php echo esc_html($entry->rank); ?></td>
                    <td>
                        <?php if ( $entry->is_new ) : ?>
                            <span class="kc-public-badge new">New</span>
                        <?php elseif ( $entry->is_reentry ) : ?>
                            <span class="kc-public-badge new" style="background:#e0f2fe; color:#0369a1;">Re</span>
                        <?php elseif ( $entry->movement_type === 'up' ) : ?>
                            <span class="kc-public-badge up">▲ <?php echo esc_html($entry->movement_value); ?></span>
                        <?php elseif ( $entry->movement_type === 'down' ) : ?>
                            <span class="kc-public-badge down">▼ <?php echo esc_html(abs($entry->movement_value)); ?></span>
                        <?php else : ?>
                            <span class="kc-public-badge same">=</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="kc-img-placeholder" style="<?php echo $img_style; ?> background-size:cover;"></div>
                        <div class="kc-track-meta">
                            <p class="kc-track-title"><?php echo esc_html($entry->track_title); ?></p>
                            <p class="kc-artist-name"><?php echo esc_html($entry->artist_name); ?></p>
                        </div>
                    </td>
                    <td style="text-align:right; font-weight:800;"><?php echo esc_html($entry->peak_position); ?></td>
                    <td style="text-align:right;"><?php echo esc_html($entry->weeks_on_chart); ?></td>
                    <td style="text-align:right; font-family: monospace; font-size: 1.1rem;">
                        <?php echo esc_html(number_format($entry->metric_primary_value)); ?>
                        <span style="display:block; font-size:0.75rem; color:#888; font-family:sans-serif; text-transform:uppercase;"><?php echo esc_html($entry->metric_primary_type); ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding: 40px; color:#888;">No entries found for this week.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>
