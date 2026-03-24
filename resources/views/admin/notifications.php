<?php 
$page_title = 'System Notifications';
$page_subtitle = 'Alerts and errors from background and batch processes.';
include 'partials/header.php'; 
?>

<div class="kc-bento-grid">
    <div class="kc-card" style="grid-column: 1 / -1;">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Recent Logs & Alerts</h3>
            <button class="kc-btn kc-btn-outline">Clear All</button>
        </div>
        <table class="kc-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Level</th>
                    <th>Source Process</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" style="text-align:center; padding: 40px; color:#888;">No notifications generated yet.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
