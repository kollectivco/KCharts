<?php 
$page_title = 'Review & Mapping';
$page_subtitle = 'Resolve unmatched artists and tracks safely.';
include 'partials/header.php'; 
?>

<?php
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';

$pending = $wpdb->get_results("
    SELECT r.*, u.file_name, u.week_date, u.chart_id, c.name as chart_name
    FROM {$prefix}source_rows r
    JOIN {$prefix}source_uploads u ON r.upload_id = u.id
    LEFT JOIN {$prefix}charts c ON u.chart_id = c.id
    WHERE r.resolution_status = 'needs_review'
    ORDER BY r.id ASC LIMIT 50
");

$artists_list = $wpdb->get_results("SELECT id, name FROM {$prefix}artists ORDER BY name ASC LIMIT 1000");
?>

<div class="kc-bento-grid">
    <div class="kc-card" style="grid-column: 1 / -1;">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Pending Resolutions</h3>
        </div>
        <table class="kc-table">
            <thead>
                <tr>
                    <th>Context</th>
                    <th>Extracted Raw Data</th>
                    <th>Match Suggestion</th>
                    <th>Manual Resolution</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty($pending) ) : ?>
                    <?php foreach ($pending as $row) : 
                        $norm = json_decode($row->normalized_data_json, true);
                        $raw_artist = $norm['raw_artist_names'] ?? 'N/A';
                        
                        // Simple suggestion logic
                        $suggestion = null;
                        if (!empty($raw_artist)) {
                            $norm_search = sanitize_title($raw_artist);
                            foreach ($artists_list as $a) {
                                if (sanitize_title($a->name) === $norm_search) {
                                    $suggestion = $a;
                                    break;
                                }
                            }
                        }
                    ?>
                    <tr data-row-id="<?php echo esc_attr($row->id); ?>">
                        <td>
                            <div style="font-weight:700;"><?php echo esc_html($row->chart_name ?? 'Upload #' . $row->upload_id); ?></div>
                            <small style="color:#888;"><?php echo esc_html($row->week_date); ?> (Row <?php echo esc_html($row->row_number); ?>)</small>
                        </td>
                        <td>
                            <div style="margin-bottom:4px;"><strong style="font-size:0.75rem; color:#888; text-transform:uppercase;">Artist</strong></div>
                            <div style="font-weight:600;"><?php echo esc_html($raw_artist); ?></div>
                            <div style="margin:8px 0 4px 0;"><strong style="font-size:0.75rem; color:#888; text-transform:uppercase;">Track</strong></div>
                            <div style="font-weight:600;"><?php echo esc_html($norm['raw_track_name'] ?? 'N/A'); ?></div>
                        </td>
                        <td>
                            <?php if ($suggestion): ?>
                                <div style="background:#f0f9ff; border:1px solid #bae6fd; padding:10px; border-radius:6px;">
                                    <div style="font-size:0.7rem; color:#0369a1; text-transform:uppercase; font-weight:700; margin-bottom:4px;">Auto Suggestion</div>
                                    <div style="font-weight:700; color:#0c4a6e;"><?php echo esc_html($suggestion->name); ?></div>
                                    <button class="kc-btn kc-btn-outline kc-apply-suggestion" data-artist-id="<?php echo $suggestion->id; ?>" style="margin-top:8px; font-size:0.75rem; padding:4px 8px; border-color:#0ea5e9; color:#0ea5e9;">Apply</button>
                                </div>
                            <?php else: ?>
                                <span style="color:#888; font-style:italic; font-size:0.85rem;">No direct match found</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="margin-bottom:8px;">
                                <label style="font-size:0.75rem; font-weight:700; color:#888; display:block; margin-bottom:4px;">TARGET ARTIST</label>
                                <select class="kc-select kc-resolve-artist" style="margin-bottom: 0; font-size: 0.85rem; padding:6px;">
                                    <option value="">-- Select Existing Artist --</option>
                                    <?php foreach($artists_list as $a): ?>
                                        <option value="<?php echo esc_attr($a->id); ?>" <?php echo ($suggestion && $suggestion->id === $a->id) ? 'selected' : ''; ?>>
                                            <?php echo esc_html($a->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label style="font-size:0.75rem; font-weight:700; color:#888; display:block; margin-bottom:4px;">CORRECT TITLE</label>
                                <input type="text" class="kc-input kc-resolve-track-title" placeholder="Track Title Override" value="<?php echo esc_attr($norm['raw_track_name'] ?? ''); ?>" style="font-size: 0.85rem; padding:6px; margin-bottom:0;">
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; flex-direction:column; gap:8px;">
                                <button class="kc-btn kc-resolve-btn kc-btn-primary" data-id="<?php echo esc_attr($row->id); ?>" style="font-size: 0.8rem; padding: 6px 10px;">Resolve Row</button>
                                <small style="color:#888; display:block; text-align:center;">Updates metric</small>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 60px;">
                            <div style="font-size: 3rem; margin-bottom: 20px;">🎉</div>
                            <h3 style="margin-bottom:10px;">Review Queue is Empty</h3>
                            <p style="color:#666; margin-bottom:20px;">All source rows have been successfully mapped to artists and tracks.</p>
                            <a href="?page=kontentainment-charts-uploads" class="kc-btn kc-btn-primary">Upload More Data</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
