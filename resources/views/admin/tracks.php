<?php 
$page_title = 'Track Database';
$page_subtitle = 'Manage resolved tracks, ISRCs, and relations.';
include 'partials/header.php'; 
?>

<div class="kc-bento-grid">
    <div class="kc-card" style="grid-column: 1 / -1;">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Indexed Tracks</h3>
            <div style="display:flex; gap:10px;">
                <input type="text" class="kc-input" placeholder="Search tracks..." style="margin:0; width: 300px;">
                <button class="kc-btn kc-btn-primary">Search</button>
            </div>
        </div>
        <table class="kc-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Track Title</th>
                    <th>Primary Artist</th>
                    <th>Release Date</th>
                    <th>Ext. Refs</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 40px; color:#888;">No tracks found. Upload some data to auto-create them.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
