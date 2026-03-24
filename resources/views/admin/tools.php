<?php 
$page_title = 'System Tools';
$page_subtitle = 'Perform database maintenance and migrations safely.';
include 'partials/header.php'; 
?>

<div class="kc-bento-grid" style="grid-template-columns: 1fr 1fr;">
    <div class="kc-card">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Danger Zone</h3>
        </div>
        <div style="display:flex; flex-direction:column; gap:20px;">
            <div>
                <strong>Re-Run Core DB Migrations</strong>
                <p style="color:#666; font-size:0.85rem;">Force dbDelta to apply table updates directly.</p>
                <button class="kc-btn kc-btn-outline" style="border-color:red; color:red;">Trigger dbDelta</button>
            </div>
            <hr style="border:0; border-top:1px solid #eaeaea; width:100%;">
            <div>
                <strong>Clear ALL Chart Data</strong>
                <p style="color:#666; font-size:0.85rem;">Permanently remove all artists, tracks, and chart history. Useful during staging.</p>
                <button class="kc-btn kc-btn-outline" style="border-color:red; color:red;">Wipe System</button>
            </div>
        </div>
    </div>
    
    <div class="kc-card">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Recalculate Movement</h3>
            <button class="kc-btn kc-btn-outline">Recalculate All</button>
        </div>
        <p style="color:#666; font-size:0.85rem;">
            If you change the history of past weeks, you can manually trigger a full recalculation 
            of the 'Movement' and 'Peak' values for a specific chart.
        </p>
    </div>

    <div class="kc-card" style="grid-column: 1 / -1;">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Update Identity & Status</h3>
            <button id="kc-check-updates-btn" class="kc-btn kc-btn-primary">Check for Updates</button>
        </div>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div>
                <strong>Installed Version</strong>
                <p style="font-size: 1.2rem; font-weight: 700;"><?php echo KC_VERSION; ?></p>
            </div>
            <div>
                <strong>Update Source</strong>
                <p style="color:#666; font-size:0.85rem;">GitHub Releases (kollectivco/KCharts)</p>
            </div>
            <div id="kc-update-result" style="display:none; padding: 15px; border-radius: 8px; border: 1px solid #eee;">
                <strong id="kc-update-status-text"></strong>
                <p id="kc-update-message-text" style="font-size:0.85rem; margin-top:5px;"></p>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
