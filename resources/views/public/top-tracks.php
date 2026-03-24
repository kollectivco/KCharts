<div class="kc-public-wrap">
    <h1 style="font-size: 2.5rem; font-weight: 800; margin: 0 0 20px 0; letter-spacing: -1px;">Top Tracks Directory</h1>
    <p style="color:#666; font-size:1.1rem; margin-bottom: 40px;">Highest performing songs recorded in our database.</p>
    
    <div class="kc-public-grid">
        <?php for($i=1; $i<=6; $i++): ?>
        <div class="kc-public-card" style="display:flex; align-items:center; padding: 20px; gap: 15px;">
            <div style="width: 60px; height: 60px; background: url('https://picsum.photos/100/100?random=<?php echo $i+10; ?>'); background-size:cover; border-radius: 6px;"></div>
            <div>
                <h3 style="font-size: 1.1rem; margin: 0 0 5px 0;">Track Title Here</h3>
                <p style="color:#888; font-size:0.85rem; margin:0;">Artist Name • Peak: #<?php echo rand(1, 10); ?></p>
            </div>
            <div style="margin-left:auto;">
                <span class="kc-public-badge up" style="font-size: 0.7rem; padding: 4px 6px;">140 Weeks</span>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</div>
