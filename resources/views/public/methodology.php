<div class="kc-public-wrap">
    
    <header style="margin-bottom: 60px; border-bottom: 3px solid #111; padding-bottom: 40px;">
        <h1 class="kc-hero-title">Methodology</h1>
        <p class="kc-hero-subtitle" style="margin: 20px 0 0 0;">How we compute, verified, and publish the industry's official metrics.</p>
    </header>

    <div class="kc-bento-grid">
        
        <!-- Platform Integration -->
        <div class="kc-bento-item" style="grid-column: span 6;">
            <h2 class="kc-section-title" style="margin-bottom: 20px;">Platform Integration</h2>
            <p style="color:#666;">Our metrics are ingested directly from verified ingestion sources across primary distribution channels. We normalize data across multiple platforms to ensure that rankings are reflective of true consumption patterns.</p>
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <div class="kc-stat-box"><strong>50+</strong><span>Platforms</span></div>
                <div class="kc-stat-box"><strong>200+</strong><span>Countries</span></div>
            </div>
        </div>

        <!-- Metric Normalization -->
        <div class="kc-bento-item dark" style="grid-column: span 6;">
            <h2 class="kc-section-title" style="color:#fff; margin-bottom: 20px;">Ranking Algorithm</h2>
            <p style="color:#aaa;">We utilize a 'Source-Ranked' model. Unlike simple raw-count models, our algorithm weights platform significance, regional relevance, and engagement velocity to produce a definitive composite score for each track.</p>
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <span class="kc-public-badge same" style="background:#222; color:#fff;">Engagement</span>
                <span class="kc-public-badge same" style="background:#222; color:#fff;">Velocity</span>
                <span class="kc-public-badge same" style="background:#222; color:#fff;">Market Cap</span>
            </div>
        </div>

        <!-- Ingestion Pipe -->
        <div class="kc-bento-item" style="grid-column: span 12;">
            <div class="kc-section-header">
                <h2 class="kc-section-title">The Ingestion Pipeline</h2>
                <p style="color:#888;">Transparency & Precision</p>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; margin-top: 20px;">
                <div>
                    <h3 style="font-weight: 800; margin-bottom: 15px;">01. Raw Data Capture</h3>
                    <p style="color:#666; font-size: 0.9rem;">CSV and API sources are ingested into our isolated sandbox. We maintain 100% of raw source data for audit purposes.</p>
                </div>
                <div>
                    <h3 style="font-weight: 800; margin-bottom: 15px;">02. Entity Resolution</h3>
                    <p style="color:#666; font-size: 0.9rem;">Aliases and misspellings are automatically resolved against our master entity registry to prevent duplicate fragmentation.</p>
                </div>
                <div>
                    <h3 style="font-weight: 800; margin-bottom: 15px;">03. Weekly Publication</h3>
                    <p style="color:#666; font-size: 0.9rem;">Charts are only published after a final manual review pass, ensuring the data is correct before public release.</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Editorial Footer Card -->
    <div style="margin-top: 60px; background: #fafafa; border-radius: 20px; padding: 60px; text-align: center; border: 1px solid #111;">
        <h2 style="font-size: 2.5rem; font-weight: 900; margin-bottom: 20px;">Industry Standard.</h2>
        <p style="color:#666; max-width: 600px; margin: 0 auto 30px auto;">Our data is used by labels, management agencies, and independent artists to track true market growth and competitive performance indicators.</p>
        <a href="<?php echo home_url('/charts/'); ?>" style="display: inline-block; padding: 15px 40px; background: #111; color: #fff; text-decoration: none; font-weight: 800; border-radius: 8px;">Explore Current Charts →</a>
    </div>

</div>
