<?php 
$page_title = 'Review & Mapping';
$page_subtitle = 'Resolve unmatched artists and tracks safely.';
include 'partials/header.php'; 
?>

<?php
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

$pending = $wpdb->get_results("
    SELECT r.*, u.file_name, u.week_date, u.chart_id
    FROM {$prefix}source_rows r
    JOIN {$prefix}source_uploads u ON r.upload_id = u.id
    WHERE r.resolution_status = 'needs_review'
    ORDER BY r.id ASC LIMIT 50
");

$artists = $wpdb->get_results("SELECT id, name FROM {$prefix}artists ORDER BY name ASC LIMIT 500");
?>

<div class="kc-bento-grid">
    <div class="kc-card" style="grid-column: 1 / -1;">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Pending Resolutions</h3>
        </div>
        <table class="kc-table">
            <thead>
                <tr>
                    <th>Upload #</th>
                    <th>Row</th>
                    <th>Extracted Raw Data</th>
                    <th>Resolution Context</th>
                    <th>Manual Resolution</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty($pending) ) : ?>
                    <?php foreach ($pending as $row) : 
                        $norm = json_decode($row->normalized_data_json, true);
                    ?>
                    <tr data-row-id="<?php echo esc_attr($row->id); ?>">
                        <td>#<?php echo esc_html($row->upload_id); ?></td>
                        <td><?php echo esc_html($row->row_number); ?></td>
                        <td>
                            <strong>Artist:</strong> <?php echo esc_html($norm['raw_artist_names'] ?? 'N/A'); ?><br>
                            <strong>Track:</strong> <?php echo esc_html($norm['raw_track_name'] ?? 'N/A'); ?>
                        </td>
                        <td>
                            <small>Chart ID: <?php echo esc_html($row->chart_id); ?></small><br>
                            <small>Date: <?php echo esc_html($row->week_date); ?></small>
                        </td>
                        <td>
                            <select class="kc-select kc-resolve-artist" style="margin-bottom: 5px; font-size: 0.8rem;">
                                <option value="">-- Assign Artist --</option>
                                <?php foreach($artists as $a): ?>
                                    <option value="<?php echo esc_attr($a->id); ?>"><?php echo esc_html($a->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" class="kc-input kc-resolve-track-title" placeholder="Track Title Override" value="<?php echo esc_attr($norm['raw_track_name'] ?? ''); ?>" style="font-size: 0.8rem;">
                        </td>
                        <td>
                            <button class="kc-btn kc-resolve-btn" data-id="<?php echo esc_attr($row->id); ?>" style="font-size: 0.8rem; padding: 5px 10px;">Save & Create</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 40px; color:#888;">No pending reviews. Excellent!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
