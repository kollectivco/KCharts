<div class="kc-public-wrap">
    <h1 style="font-size: 2.5rem; font-weight: 800; margin: 0 0 20px 0; letter-spacing: -1px;">Top Artists Archive</h1>
    <p style="color:#666; font-size:1.1rem; margin-bottom: 40px;">Explore the most charted entities from our parsed data.</p>
    
    <div class="kc-public-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <?php for($i=1; $i<=6; $i++): ?>
        <div class="kc-public-card" style="text-align:center; padding: 20px;">
            <div style="width: 100px; height: 100px; background: url('https://picsum.photos/100/100?random=<?php echo $i; ?>'); background-size:cover; border-radius: 50%; margin: 0 auto 15px auto;"></div>
            <h3 style="font-size: 1.1rem; margin-bottom: 5px;">Artist Name</h3>
            <p style="color:#888; font-size:0.85rem; margin:0;">42 Weeks on Chart</p>
        </div>
        <?php endfor; ?>
    </div>
</div>
