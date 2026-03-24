<?php 
$page_title = 'Generated Charts';
$page_subtitle = 'Review and publish generated chart weeks.';
include 'partials/header.php'; 
?>

<?php
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

// Handle Filters
$filter_chart = isset($_GET['chart_id']) ? intval($_GET['chart_id']) : 0;
$filter_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$filter_date = isset($_GET['week_date']) ? sanitize_text_field($_GET['week_date']) : '';

$where = ["1=1"];
if ($filter_chart) $where[] = $wpdb->prepare("cw.chart_id = %d", $filter_chart);
if ($filter_status) $where[] = $wpdb->prepare("cw.status = %s", $filter_status);
if ($filter_date) $where[] = $wpdb->prepare("cw.week_date = %s", $filter_date);

$where_sql = implode(" AND ", $where);

$weeks = $wpdb->get_results("
    SELECT cw.*, c.name as chart_name
    FROM {$prefix}chart_weeks cw
    LEFT JOIN {$prefix}charts c ON cw.chart_id = c.id
    WHERE {$where_sql}
    ORDER BY cw.week_date DESC
");

$charts = $wpdb->get_results("SELECT id, name FROM {$prefix}charts WHERE status = 'active' ORDER BY name ASC");
?>

<div class="kc-bento-grid">
    <div class="kc-card" style="grid-column: 1 / -1;">
        <div class="kc-card-header" style="flex-wrap: wrap; gap: 15px;">
            <h3 class="kc-card-title">Chart History</h3>
            
            <form method="get" style="display: flex; gap: 10px; align-items: center;">
                <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']); ?>">
                
                <select name="chart_id" class="kc-select" style="min-width: 150px;">
                    <option value="">-- All Charts --</option>
                    <?php foreach($charts as $c): ?>
                        <option value="<?php echo esc_attr($c->id); ?>" <?php selected($filter_chart, $c->id); ?>><?php echo esc_html($c->name); ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="status" class="kc-select">
                    <option value="">-- All Status --</option>
                    <option value="draft" <?php selected($filter_status, 'draft'); ?>>Draft</option>
                    <option value="published" <?php selected($filter_status, 'published'); ?>>Published</option>
                </select>

                <input type="date" name="week_date" class="kc-input" value="<?php echo esc_attr($filter_date); ?>">

                <button type="submit" class="kc-btn" style="background: #333; color: #fff;">Filter</button>
                <?php if($filter_chart || $filter_status || $filter_date): ?>
                    <a href="?page=<?php echo esc_attr($_GET['page']); ?>" class="kc-btn" style="border:1px solid #ccc; background:#fff; color:#333; height: 38px; line-height: 38px; padding: 0 15px; text-decoration: none; border-radius: 8px;">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <table class="kc-table">
            <thead>
                <tr>
                    <th>Chart Date</th>
                    <th>Target Chart</th>
                    <th>Status</th>
                    <th>Ranked Entries</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ( ! empty($weeks) ) :
                    foreach ( $weeks as $week ) :
                        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM {$prefix}chart_entries WHERE chart_week_id = %d", $week->id));
                        $status_class = $week->status === 'published' ? 'new' : ''; // Reuse 'new' class styling or inline
                        $status_style = $week->status === 'published' ? 'background: #dcfce7; color: #166534;' : 'background: #fef9c3; color: #854d0e;';
                ?>
                <tr>
                    <td style="font-weight:700;"><?php echo esc_html($week->week_date); ?></td>
                    <td><?php echo esc_html($week->chart_name ?? 'Global Top 50'); ?></td>
                    <td>
                        <span class="kc-public-badge" style="<?php echo $status_style; ?>">
                            <?php echo ucfirst(esc_html($week->status)); ?>
                        </span>
                    </td>
                    <td><?php echo esc_html($count); ?> Entries</td>
                    <td>
                        <?php if ( $week->status !== 'published' ) : ?>
                            <button class="kc-btn kc-publish-btn" data-id="<?php echo esc_attr($week->id); ?>" style="font-size:0.8rem; padding: 4px 10px; background: #000; color: #fff;">Publish</button>
                        <?php else : ?>
                            <button class="kc-btn kc-unpublish-btn" data-id="<?php echo esc_attr($week->id); ?>" style="font-size:0.8rem; padding: 4px 10px; background: #fff; color: #dc2626; border: 1px solid #dc2626;">Unpublish</button>
                        <?php endif; ?>
                        <button class="kc-btn kc-rebuild-btn" data-id="<?php echo esc_attr($week->id); ?>" style="font-size:0.8rem; padding: 4px 12px; background: #fff; color: #333; border: 1px solid #ccc;">Rebuild</button>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 40px; color:#888;">No generated charts matching these filters.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
