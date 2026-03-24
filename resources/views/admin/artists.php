<?php 
$page_title = 'Artist Database';
$page_subtitle = 'Manage resolved artists, aliases, and identities.';
include 'partials/header.php'; 
?>

<div class="kc-bento-grid">
    <div class="kc-card" style="grid-column: 1 / -1;">
        <div class="kc-card-header">
            <h3 class="kc-card-title">Indexed Artists</h3>
            <div style="display:flex; gap:10px;">
                <input type="text" class="kc-input" placeholder="Search artists..." style="margin:0; width: 300px;">
                <button class="kc-btn kc-btn-primary">Search</button>
            </div>
        </div>
        <table class="kc-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Normalized Slug</th>
                    <th>Aliases</th>
                    <th>Tracks</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 40px; color:#888;">No artists found. Upload some data to auto-create them.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
