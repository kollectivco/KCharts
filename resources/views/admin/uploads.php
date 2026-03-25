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
            <h3 class="kc-card-title">New Ingestion</h3>
            <span class="kc-badge kc-badge-same">Step 1 of 3</span>
        </div>
        <form id="kc-upload-form" method="post" enctype="multipart/form-data">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem; color: #475569;">
                Configure the source and target below. You will be able to <strong>preview</strong> the detected rows before confirming the final import.
            </div>
            
            <div class="kc-form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
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
                    <label>Country context</label>
                    <select name="country_id" class="kc-select">
                        <option value="">-- Global / No Country --</option>
                        <?php foreach($countries as $c): ?>
                            <option value="<?php echo esc_attr($c->id); ?>"><?php echo esc_html($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="kc-form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="kc-form-group">
                    <label>Platform Profile</label>
                    <select name="parser_key" class="kc-select" required>
                        <option value="spotify_regional_csv">Spotify Weekly Regional</option>
                        <option value="youtube_topsongs_csv">YouTube Top Songs</option>
                    </select>
                </div>

                <div class="kc-form-group">
                    <label>Chart Week Date</label>
                    <input type="date" name="week_date" class="kc-input" required value="<?php echo date('Y-m-d', strtotime('last friday')); ?>">
                </div>
            </div>
            
            <div class="kc-form-group">
                <label>Input Mode</label>
                <select name="input_mode" class="kc-select" required>
                    <option value="source_ranked">Source Ranked (Identity Matching)</option>
                    <option value="computed" disabled>Market Weighted (Future)</option>
                </select>
            </div>

            <div class="kc-form-group" style="margin-bottom:0;">
                <label>Source File (CSV)</label>
                <div class="kc-upload-area" id="kc-drop-zone" style="padding: 20px; margin-bottom:0;">
                    <input type="file" name="chart_file" id="chart_file" accept=".csv" required style="display:none;">
                    <div id="kc-file-display">
                        <strong>Click to select</strong> or drag CSV here
                    </div>
                </div>
            </div>

            <div style="margin-top: 24px; border-top: 1px solid #f0f0f0; padding-top: 20px;">
                <button type="submit" class="kc-btn kc-btn-primary" style="width:100%;">Upload & Preview &rarr;</button>
            </div>
        </form>
    </div>

    <!-- Step 2 Modal: Preview (Hidden by default) -->
    <div id="kc-upload-preview-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; display:none; align-items:center; justify-content:center; padding:40px;">
        <div class="kc-card" style="width:100%; max-width:900px; max-height:80vh; overflow:hidden;">
            <div class="kc-card-header">
                <h3 class="kc-card-title">Step 2: Analysis & Preview</h3>
                <span class="kc-badge kc-badge-same">Reviewing File</span>
            </div>
            <div id="kc-preview-content" style="overflow-y:auto; padding-right:10px;">
                <!-- Content injected via JS -->
            </div>
            <div style="margin-top:20px; display:flex; gap:12px; justify-content:flex-end; border-top:1px solid #f0f0f0; padding-top:20px;">
                <button class="kc-btn kc-btn-outline" id="kc-cancel-upload">Cancel</button>
                <button class="kc-btn kc-btn-primary" id="kc-confirm-import">Process Final Import</button>
            </div>
        </div>
    </div>

    <!-- Step 3 Modal: Result (Hidden by default) -->
    <div id="kc-upload-result-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:10000; display:none; align-items:center; justify-content:center; padding:40px;">
        <div class="kc-card" style="width:100%; max-width:500px; text-align:center;">
            <div id="kc-result-content">
                <!-- Content injected via JS -->
            </div>
            <div style="margin-top:24px;">
                <button class="kc-btn kc-btn-primary" onclick="window.location.reload()">Return to Dashboard</button>
            </div>
        </div>
    </div>

    <div class="kc-card">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Ingestion History</h3>
        </div>
        <table class="kc-table">
            <thead>
                <tr>
                    <th>Identity</th>
                    <th>Chart Context</th>
                    <th>Processing stats</th>
                    <th>Entities</th>
                    <th>State</th>
                </tr>
            </thead>
            <tbody id="kc-ingestion-table">
                <?php if ( ! empty($uploads) ) : ?>
                    <?php foreach ( $uploads as $up ) : 
                        $chart = null;
                        if ($up->chart_id) {
                            foreach($charts as $c) if($c->id == $up->chart_id) $chart = $c;
                        }
                    ?>
                    <tr>
                        <td>
                            <div style="font-weight:700;">#<?php echo esc_html($up->id); ?></div>
                            <small style="color:#888; display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;"><?php echo esc_html($up->file_name); ?></small>
                        </td>
                        <td>
                            <div style="font-weight:600;"><?php echo esc_html($chart ? $chart->name : 'N/A'); ?></div>
                            <div style="font-size:0.8rem; color:#666;"><?php echo esc_html($up->week_date); ?> (<?php echo esc_html(ucfirst($up->source_platform)); ?>)</div>
                        </td>
                        <td>
                            <div style="font-size:0.85rem;"><strong><?php echo (int)$up->rows_processed; ?></strong> valid</div>
                            <?php if($up->rows_skipped > 0): ?>
                                <div style="font-size:0.75rem; color:#dc2626;"><strong><?php echo (int)$up->rows_skipped; ?></strong> review needed</div>
                            <?php else: ?>
                                <div style="font-size:0.75rem; color:#166534;">Clean transfer</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-size:0.8rem;">Artists: <strong><?php echo (int)$up->created_artists; ?></strong></div>
                            <div style="font-size:0.8rem;">Tracks: <strong><?php echo (int)$up->created_tracks; ?></strong></div>
                        </td>
                        <td>
                            <span class="kc-public-badge" style="<?php echo $up->status === 'completed' ? 'background: #dcfce7; color: #166534;' : 'background: #f1f5f9; color: #475569;'; ?>">
                                <?php echo ucfirst(esc_html($up->status)); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 60px;">
                            <p style="color:#888; margin-bottom:15px;">No ingestion history found.</p>
                            <span style="font-size:0.85rem; background:#f1f5f9; padding:5px 10px; border-radius:4px; color:#475569;">History will appear after your first upload</span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
