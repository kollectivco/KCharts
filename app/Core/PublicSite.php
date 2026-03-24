<?php
namespace Kontentainment\Charts\Core;

class PublicSite {

    protected $container;

    public function __construct( Container $container ) {
        $this->container = $container;
    }

    public function init() {
        // Register Shortcodes
        add_shortcode( 'kc_charts_index', [ $this, 'render_charts_index' ] );
        add_shortcode( 'kc_chart_week', [ $this, 'render_chart_week' ] );
        add_shortcode( 'kc_top_artists', [ $this, 'render_top_artists' ] );
        add_shortcode( 'kc_top_tracks', [ $this, 'render_top_tracks' ] );
        add_shortcode( 'kc_artist', [ $this, 'render_single_artist' ] );
        add_shortcode( 'kc_track', [ $this, 'render_single_track' ] );
        add_shortcode( 'kc_methodology', [ $this, 'render_methodology' ] );

        // Enqueue styles
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
    }

    public function enqueue_styles() {
        wp_enqueue_style( 'kc-public-css', KC_URL . 'resources/css/public.css', [], KC_VERSION );
    }

    public function render_charts_index( $atts ) {
        ob_start();
        $this->load_view( 'charts-index', $atts );
        return ob_get_clean();
    }

    public function render_chart_week( $atts ) {
        global $wpdb;
        $prefix = $wpdb->prefix . 'kc_';

        $atts = shortcode_atts([
            'chart_id'   => 0,
            'chart_slug' => '',
            'week_date'  => '',
            'limit'      => 100
        ], $atts);

        $chart_id = intval($atts['chart_id']);
        $chart_slug = sanitize_title($atts['chart_slug']);
        $week_date = sanitize_text_field($atts['week_date']);
        $limit = intval($atts['limit']);

        // Resolve Chart ID if slug provided
        if ( ! $chart_id && ! empty($chart_slug) ) {
            $chart_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$prefix}charts WHERE slug = %s", $chart_slug));
        }

        if ( ! $chart_id ) {
            // Default to first chart if nothing specified?
            $chart_id = $wpdb->get_var("SELECT id FROM {$prefix}charts ORDER BY id ASC LIMIT 1");
        }

        if ( ! $chart_id ) {
            return '<div class="kc-public-error">Configuration Error: No charts found.</div>';
        }

        // Resolve Week
        if ( empty($week_date) ) {
            $week_row = $wpdb->get_row($wpdb->prepare("
                SELECT id, week_date FROM {$prefix}chart_weeks 
                WHERE chart_id = %d AND status = 'published' 
                ORDER BY week_date DESC LIMIT 1
            ", $chart_id));
        } else {
            $week_row = $wpdb->get_row($wpdb->prepare("
                SELECT id, week_date FROM {$prefix}chart_weeks 
                WHERE chart_id = %d AND week_date = %s AND status = 'published' LIMIT 1
            ", $chart_id, $week_date));
        }

        if ( ! $week_row ) {
            return '<div class="kc-public-wrap"><h2>Chart Not Available</h2><p>No published data found for this selection.</p></div>';
        }

        $chart_info = $wpdb->get_row($wpdb->prepare("SELECT name FROM {$prefix}charts WHERE id = %d", $chart_id));

        $entries = $wpdb->get_results($wpdb->prepare("
            SELECT e.*, t.title as track_title, a.name as artist_name, t.artwork_url
            FROM {$prefix}chart_entries e
            JOIN {$prefix}tracks t ON e.track_id = t.id
            LEFT JOIN {$prefix}artists a ON t.primary_artist_id = a.id
            WHERE e.chart_week_id = %d
            ORDER BY e.rank ASC
            LIMIT %d
        ", $week_row->id, $limit));

        ob_start();
        $this->load_view( 'chart-week', [
            'entries'   => $entries,
            'week_date'  => $week_row->week_date,
            'chart_name' => $chart_info ? $chart_info->name : 'Global Chart'
        ]);
        return ob_get_clean();
    }

    public function render_top_artists( $atts ) {
        ob_start();
        $this->load_view( 'top-artists', $atts );
        return ob_get_clean();
    }

    public function render_top_tracks( $atts ) {
        ob_start();
        $this->load_view( 'top-tracks', $atts );
        return ob_get_clean();
    }

    public function render_single_artist( $atts ) {
        ob_start();
        $this->load_view( 'single-artist', $atts );
        return ob_get_clean();
    }

    public function render_single_track( $atts ) {
        ob_start();
        $this->load_view( 'single-track', $atts );
        return ob_get_clean();
    }

    public function render_methodology( $atts ) {
        ob_start();
        $this->load_view( 'methodology', $atts );
        return ob_get_clean();
    }

    protected function load_view( $view_name, $args = [] ) {
        $view_file = KC_DIR . '/resources/views/public/' . $view_name . '.php';

        if ( file_exists( $view_file ) ) {
            extract( $args );
            include $view_file;
        } else {
            echo '<div class="kc-public-error">View Not Found: ' . esc_html($view_name) . '</div>';
        }
    }
}
