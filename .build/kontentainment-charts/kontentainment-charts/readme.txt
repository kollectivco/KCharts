=== Kontentainment Charts ===
Contributors: kontentainment
Tags: charts, music-charts, ingestion, music-business
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 8.0
Stable tag: 4.4.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Upload weekly chart spreadsheets, parse, normalize, and publish music chart data.

== Description ==

Kontentainment Charts is a robust, production-ready system to automatically ingest and normalize weekly rank spreadsheets (such as Spotify Weekly Regional CSV, YouTube Top Songs Weekly CSV) into canonical canonical models inside WordPress.

It provides a premium bento-styled internal interface for operators to upload spreadsheets, map ambiguous metadata to permanent entities (Artists, Tracks), update analytics, and cleanly publish Chart Weeks for public display.

### Core Features:
- Custom strict object schema with unique table prefix `wp_kc_`.
- Auto-extract and resolve split artists from string names (e.g. "Sting, Cheb Mami").
- Match tracks safely, or queue for admin Review.
- Save chart source ranking directly per week.
- Generate and safely calculate peak chart history positions.
- Provide a modern responsive shortcode-based public UI.
- Dark theme toggle inside WP admin tailored for long session tasks.

== Installation ==

1. Upload the `kontentainment-charts` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Access "K-Charts" in the WordPress Admin Dashboard.
4. Set your configuration in Settings and start uploading CSV data.

== Frequently Asked Questions ==

= Do I need external dependencies? =
No. The entire charting logic is encapsulated in this plugin.

= How do I render the views on the frontend? =
Use the following shortcodes on your pages:
`[kc_charts_index]`
`[kc_chart_week]`
`[kc_top_artists]`
`[kc_top_tracks]`
`[kc_artist]`
`[kc_track]`
`[kc_methodology]`

== Changelog ==

= 1.0.0 =
* Initial stable release with Spotify and YouTube CSV parsing strategies.
