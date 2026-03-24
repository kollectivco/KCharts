<?php 
$page_title = 'System Settings';
$page_subtitle = 'Manage global rules and configuration options.';
include 'partials/header.php'; 
?>

<div class="kc-bento-grid" style="grid-template-columns: 1fr 2fr;">
    <div class="kc-card">
        <div class="kc-card-header">
            <h3 class="kc-card-title">General Preferences</h3>
        </div>
        <form id="kc-settings-form">
            <div class="kc-form-group">
                <label>Fuzzy Match Threshold (%)</label>
                <input type="number" class="kc-input" value="85" min="50" max="100">
            </div>

            <div class="kc-form-group">
                <label>Auto-create Missing Artists?</label>
                <select class="kc-select">
                    <option value="1">Yes (Default)</option>
                    <option value="0">No, queue for review</option>
                </select>
            </div>

            <div class="kc-form-group">
                <label>Split Multiple Artists</label>
                <input type="text" class="kc-input" value=", & feat. ft." placeholder="Delimiters">
                <small style="color: #666; display: block; margin-top: -10px; margin-bottom: 20px;">Use spaces or characters used as separators in raw data.</small>
            </div>

            <button class="kc-btn kc-btn-primary" type="submit">Save Changes</button>
        </form>
    </div>

    <div class="kc-card">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Platform Configs</h3>
        </div>
        <table class="kc-table">
            <thead>
                <tr>
                    <th>Slug</th>
                    <th>Display Name</th>
                    <th>Metrics Handled</th>
                    <th>Active</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>spotify</td>
                    <td>Spotify</td>
                    <td>streams, rank</td>
                    <td>Yes</td>
                </tr>
                <tr>
                    <td>youtube</td>
                    <td>YouTube</td>
                    <td>views, growth</td>
                    <td>Yes</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
