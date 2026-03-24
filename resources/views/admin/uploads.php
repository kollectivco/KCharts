<?php 
$page_title = 'Data Ingestion & Uploads';
$page_subtitle = 'Process new CSV sources against standard parsing profiles.';
include 'partials/header.php'; 
?>

<?php
global $wpdb;
$prefix = $wpdb->prefix . 'kc_';
$charts = $wpdb->get_results("SELECT id, name FROM {$prefix}charts WHERE status = 'active' ORDER BY name ASC");
$countries = $wpdb->get_results("SELECT id, name FROM {$prefix}countries WHERE status = 'active' ORDER BY name ASC");

$uploads = $wpdb->get_results("SELECT * FROM {$prefix}source_uploads ORDER BY uploaded_at DESC LIMIT 20");
?>
<div class="kc-bento-grid" style="grid-template-columns: 1fr 2fr;">
    <div class="kc-card">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Upload New Source</h3>
        </div>
        <form id="kc-upload-form" method="post" enctype="multipart/form-data">
            
            <div class="kc-form-group">
                <label>Target Chart</label>
                <select name="chart_id" class="kc-select" required>
                    <option value="">-- Select Chart --</option>
                    <?php foreach($charts as $c): ?>
                        <option value="<?php echo esc_attr($c->id); ?>"><?php echo esc_html($c->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="kc-form-group">
                <label>Country Context</label>
                <select name="country_id" class="kc-select">
                    <option value="">-- Global / No Country --</option>
                    <?php foreach($countries as $c): ?>
                        <option value="<?php echo esc_attr($c->id); ?>"><?php echo esc_html($c->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="kc-form-group">
                <label>Platform Profile</label>
                <select name="parser_key" class="kc-select" required>
                    <option value="spotify_regional_csv">Spotify Weekly Regional CSV</option>
                    <option value="youtube_topsongs_csv">YouTube Top Songs Weekly CSV</option>
                </select>
            </div>

            <div class="kc-form-group">
                <label>Target Chart Week Date</label>
                <input type="date" name="week_date" class="kc-input" required>
            </div>
            
            <div class="kc-form-group">
                <label>Input Mode</label>
                <select name="input_mode" class="kc-select" required>
                    <option value="source_ranked">Source Ranked (Direct Import)</option>
                    <option value="computed" disabled>Computed (Future V2)</option>
                </select>
            </div>

            <div class="kc-form-group">
                <label>Upload File (CSV only)</label>
                <input type="file" name="chart_file" id="chart_file" accept=".csv" class="kc-input" required>
            </div>

            <div style="margin-top: 16px;">
                <button type="submit" class="kc-btn kc-btn-primary">Preview & Import Data</button>
            </div>
        </form>
    </div>

    <div class="kc-card">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Ingestion History</h3>
        </div>
        <table class="kc-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>File</th>
                    <th>Platform</th>
                    <th>Rows</th>
                    <th>Resolved</th>
                    <th>Created</th>
                    <th>State</th>
                </tr>
            </thead>
            <tbody id="kc-ingestion-table">
                <?php if ( ! empty($uploads) ) : ?>
                    <?php foreach ( $uploads as $up ) : ?>
                    <tr>
                        <td style="font-weight:700;">#<?php echo esc_html($up->id); ?></td>
                        <td style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:150px;" title="<?php echo esc_attr($up->file_name); ?>">
                            <?php echo esc_html($up->file_name); ?>
                        </td>
                        <td><?php echo esc_html($up->source_platform); ?></td>
                        <td><?php echo esc_html($up->total_rows); ?></td>
                        <td><?php echo esc_html($up->valid_rows); ?></td>
                        <td>
                            <small>A: <?php echo esc_html($up->created_artists ?? 0); ?></small><br>
                            <small>T: <?php echo esc_html($up->created_tracks ?? 0); ?></small>
                        </td>
                        <td>
                            <span class="kc-public-badge <?php echo $up->status === 'completed' ? 'new' : ''; ?>" style="<?php echo $up->status === 'completed' ? 'background: #dcfce7; color: #166534;' : 'background: #f1f5f9; color: #475569;'; ?>">
                                <?php echo esc_html($up->status); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding: 40px; color:#888;">Upload your first file to see ingestion history.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
