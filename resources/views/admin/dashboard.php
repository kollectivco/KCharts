<?php 
$page_title = 'Overview Dashboard';
$page_subtitle = 'System performance and chart status at a glance.';
include 'partials/header.php'; 
?>

<div class="kc-bento-grid">
    <!-- Quick Stats -->
    <div class="kc-stat-card">
        <div>Total Artists Indexed</div>
        <div class="kc-stat-value">1,245</div>
        <div style="color: green; font-size: 0.85rem; margin-top: 5px;">+14 this week</div>
    </div>
    
    <div class="kc-stat-card">
        <div>Total Tracks Indexed</div>
        <div class="kc-stat-value">3,482</div>
        <div style="color: green; font-size: 0.85rem; margin-top: 5px;">+31 this week</div>
    </div>

    <div class="kc-stat-card">
        <div>Published Chart Weeks</div>
        <div class="kc-stat-value">8</div>
        <div style="font-size: 0.85rem; margin-top: 5px; color: #888;">Across 3 platforms</div>
    </div>

    <div class="kc-stat-card" style="border-left: 4px solid #f59e0b;">
        <div>Pending Reviews</div>
        <div class="kc-stat-value">12 Rows</div>
        <div style="font-size: 0.85rem; margin-top: 5px;">Require manual artist matching</div>
    </div>
</div>

<div class="kc-bento-grid" style="grid-template-columns: 2fr 1fr;">
    <div class="kc-card">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Recent Uploads Activity</h3>
        </div>
        <table class="kc-table">
            <thead>
                <tr>
                    <th>Target Chart</th>
                    <th>Week Date</th>
                    <th>Platform</th>
                    <th>Status</th>
                    <th>Items Imported</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Global Top 50</td>
                    <td>Oct 12, 2024</td>
                    <td>Spotify</td>
                    <td><span class="kc-badge kc-badge-up">Processed</span></td>
                    <td>50 / 0 err</td>
                </tr>
                <tr>
                    <td>US Top Trending</td>
                    <td>Oct 12, 2024</td>
                    <td>YouTube</td>
                    <td><span class="kc-badge kc-badge-down">Review Needed</span></td>
                    <td>98 / 2 err</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="kc-card">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Quick Actions</h3>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <a href="?page=kontentainment-charts-uploads" class="kc-btn kc-btn-primary">Upload New CSV</a>
            <a href="?page=kontentainment-charts-review" class="kc-btn kc-btn-outline">Clear Review Queue</a>
            <a href="?page=kontentainment-charts-settings" class="kc-btn kc-btn-outline">Mapping Settings</a>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
