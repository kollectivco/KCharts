<?php
namespace Kontentainment\Charts\Http;

use Kontentainment\Charts\Core\Container;
use Kontentainment\Charts\Services\UploadService;

class Ajax {
    
    protected $container;

    public function __construct( Container $container ) {
        $this->container = $container;
    }

    public function init() {
        add_action( 'wp_ajax_kc_upload_chart_data', [ $this, 'handle_upload' ] );
        add_action( 'wp_ajax_kc_publish_week', [ $this, 'handle_publish' ] );
        add_action( 'wp_ajax_kc_unpublish_week', [ $this, 'handle_unpublish' ] );
        add_action( 'wp_ajax_kc_rebuild_week', [ $this, 'handle_rebuild' ] );
        add_action( 'wp_ajax_kc_resolve_row', [ $this, 'handle_resolve' ] );
    }

    public function handle_resolve() {
        check_ajax_referer( 'kc_ajax_nonce', 'nonce' );

        if ( ! current_user_can( 'review_kc_chart_data' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized capability.' ] );
        }

        $row_id = isset($_POST['row_id']) ? intval($_POST['row_id']) : 0;
        $artist_id = isset($_POST['artist_id']) ? intval($_POST['artist_id']) : 0;
        $track_title = sanitize_text_field($_POST['track_title'] ?? '');

        if ( ! $row_id || ! $artist_id || empty($track_title) ) {
            wp_send_json_error( [ 'message' => 'Missing resolution data.' ] );
        }

        try {
            global $wpdb;
            $prefix = $wpdb->prefix . 'kc_';

            $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}source_rows WHERE id = %d", $row_id));
            if ( ! $row ) throw new \Exception("Row not found.");

            $upload = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}source_uploads WHERE id = %d", $row->upload_id));
            if ( ! $upload ) throw new \Exception("Upload parent not found.");

            $norm = json_decode($row->normalized_data_json, true);

            // 1. Ensure Track exists for this artist/title
            $track_norm = sanitize_title($track_title);
            $track_id = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$prefix}tracks WHERE normalized_title = %s AND primary_artist_id = %d LIMIT 1",
                $track_norm, $artist_id
            ));

            if ( ! $track_id ) {
                $wpdb->insert( "{$prefix}tracks", [
                    'title' => $track_title,
                    'normalized_title' => $track_norm,
                    'slug' => $track_norm . '-' . uniqid(),
                    'primary_artist_id' => $artist_id,
                    'status' => 'active'
                ]);
                $track_id = $wpdb->insert_id;
                
                $wpdb->insert( "{$prefix}track_artists", [
                    'track_id' => $track_id,
                    'artist_id' => $artist_id,
                    'role' => 'main',
                    'sort_order' => 0
                ]);
            }

            // 2. Update Metric
            $query = "INSERT INTO {$prefix}track_week_metrics 
                (track_id, chart_id, country_id, platform_slug, week_date, source_rank, source_previous_rank, source_peak_rank, source_weeks_on_chart, metric_primary_type, metric_primary_value, growth_percent, external_url, external_uri, upload_id, source_row_id) 
                VALUES (%d, %d, %d, %s, %s, %d, %d, %d, %d, %s, %f, %f, %s, %s, %d, %d) 
                ON DUPLICATE KEY UPDATE 
                track_id = VALUES(track_id),
                updated_at = CURRENT_TIMESTAMP";

            $wpdb->query($wpdb->prepare($query,
                $track_id,
                $upload->chart_id,
                $upload->country_id,
                $upload->source_platform,
                $upload->week_date,
                $norm['raw_rank'],
                $norm['raw_previous_rank'],
                $norm['raw_peak_rank'] ?? null,
                $norm['raw_weeks_on_chart'] ?? null,
                $norm['metric_primary_type'],
                $norm['metric_primary_value'],
                $norm['raw_growth_value'],
                $norm['external_url'],
                $norm['external_uri'],
                $row->upload_id,
                $row->id
            ));

            // 3. Update Row status
            $wpdb->update($prefix . 'source_rows', [
                'resolution_status' => 'resolved',
                'matched_track_id' => $track_id
            ], [ 'id' => $row_id ]);

            wp_send_json_success( [ 'message' => 'Row resolved successfully.' ] );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }
    }

    public function handle_upload() {
        check_ajax_referer( 'kc_ajax_nonce', 'nonce' );

        if ( ! current_user_can( 'upload_kc_chart_data' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized capability.' ] );
        }

        if ( empty( $_FILES['chart_file']['tmp_name'] ) ) {
            wp_send_json_error( [ 'message' => 'No file provided.' ] );
        }

        $parser_key = sanitize_text_field( $_POST['parser_key'] ?? '' );
        $week_date = sanitize_text_field( $_POST['week_date'] ?? '' );
        $input_mode = sanitize_text_field( $_POST['input_mode'] ?? 'source_ranked' );
        $chart_id = isset($_POST['chart_id']) ? intval($_POST['chart_id']) : 0;
        $country_id = isset($_POST['country_id']) ? intval($_POST['country_id']) : null;

        if ( ! $chart_id ) {
            wp_send_json_error( [ 'message' => 'Chart ID is required.' ] );
        }

        try {
            $service = $this->container->get( UploadService::class );
            $result = $service->process_upload( $_FILES['chart_file'], $parser_key, $week_date, $input_mode, $chart_id, $country_id );
            wp_send_json_success( $result );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }
    }

    public function handle_publish() {
        check_ajax_referer( 'kc_ajax_nonce', 'nonce' );

        if ( ! current_user_can( 'publish_kc_chart_weeks' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized capability.' ] );
        }

        $week_id = isset($_POST['week_id']) ? intval($_POST['week_id']) : 0;
        if ( ! $week_id ) {
            wp_send_json_error( [ 'message' => 'Invalid week ID.' ] );
        }

        try {
            $builder = $this->container->get( \Kontentainment\Charts\Services\ChartBuilder::class );
            $builder->publish_week( $week_id );
            wp_send_json_success( [ 'message' => 'Chart published successfully.' ] );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }
    }

    public function handle_unpublish() {
        check_ajax_referer( 'kc_ajax_nonce', 'nonce' );

        if ( ! current_user_can( 'publish_kc_chart_weeks' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized capability.' ] );
        }

        $week_id = isset($_POST['week_id']) ? intval($_POST['week_id']) : 0;
        if ( ! $week_id ) {
            wp_send_json_error( [ 'message' => 'Invalid week ID.' ] );
        }

        try {
            $builder = $this->container->get( \Kontentainment\Charts\Services\ChartBuilder::class );
            $builder->unpublish_week( $week_id );
            wp_send_json_success( [ 'message' => 'Chart unpublished successfully.' ] );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }
    }

    public function handle_rebuild() {
        check_ajax_referer( 'kc_ajax_nonce', 'nonce' );

        if ( ! current_user_can( 'upload_kc_chart_data' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized capability.' ] );
        }

        $week_id = isset($_POST['week_id']) ? intval($_POST['week_id']) : 0;
        if ( ! $week_id ) {
            wp_send_json_error( [ 'message' => 'Invalid week ID.' ] );
        }

        try {
            global $wpdb;
            $week = $wpdb->get_row($wpdb->prepare("SELECT chart_id, week_date FROM {$wpdb->prefix}kc_chart_weeks WHERE id = %d", $week_id));
            if ( ! $week ) {
                throw new \Exception("Week record not found.");
            }
            $builder = $this->container->get( \Kontentainment\Charts\Services\ChartBuilder::class );
            $builder->build_week( $week->chart_id, $week->week_date, null );
            wp_send_json_success( [ 'message' => 'Chart rebuilt successfully.' ] );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }
    }
}
