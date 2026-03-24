<div class="kc-public-wrap" style="max-width: 800px;">
    
    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 3.5rem; font-weight: 900; margin: 0; letter-spacing: -2px;">How We Chart</h1>
        <p style="font-size: 1.2rem; color: #666; margin-top: 15px; line-height: 1.6;">
            Kontentainment Charts uses a robust ingestion pipeline to measure normalized weekly song popularity. Here is how our methodology works.
        </p>
    </div>

    <div class="kc-public-card" style="margin-bottom: 30px;">
        <h3 style="margin-bottom: 10px;">1. Weekly Source Tracking</h3>
        <p style="color:#444; line-height: 1.6;">We ingest weekly source spreadsheets from leading platforms including Spotify and YouTube Music. These CSVs provide raw regional and global performance data including rank, previous rank, metrics (streams/views) and URLs.</p>
    </div>
    
    <div class="kc-public-card" style="margin-bottom: 30px;">
        <h3 style="margin-bottom: 10px;">2. Entity Resolution Engine</h3>
        <p style="color:#444; line-height: 1.6;">Every raw string is parsed, sanitized, and matched against our canonical artist and track repository. We split complex artist fields (e.g. <code>Lege-Cy, Ghaliaa</code>) into unique indexed artists and bind them via relation tables to prevent duplication and ensure an accurate chart history for each specific person.</p>
    </div>
    
    <div class="kc-public-card" style="margin-bottom: 30px;">
        <h3 style="margin-bottom: 10px;">3. Movement & Peak Calculation</h3>
        <p style="color:#444; line-height: 1.6;">Chart movement is calculated by finding the difference between a song's current week rank and previous rank. A track is assigned a status of <code>New</code>, <code>Re-Entry</code>, <code>Up</code>, <code>Down</code>, or <code>Same</code>. We continually update the overall peak position directly in the generated weekly entries.</p>
    </div>
    
    <div class="kc-public-card dark" style="margin-bottom: 30px;">
        <h3 style="margin-bottom: 10px;">4. Publication</h3>
        <p style="color:#aaa; line-height: 1.6;">A chart week remains strictly in draft mode during review. Only fully reviewed and approved weeks are marked as <em>Published</em>, which prevents partial or faulty data from rendering on the public pages.</p>
    </div>

</div>
