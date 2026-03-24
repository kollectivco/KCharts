<?php
namespace Kontentainment\Charts\Services;

use Kontentainment\Charts\Core\Container;
use Kontentainment\Charts\Parsers\SpotifyWeeklyRegionalParser;
use Kontentainment\Charts\Parsers\YouTubeTopSongsParser;

class UploadService {

    protected $container;
    
    public function __construct( Container $container ) {
        $this->container = $container;
    }

    public function get_parser( $key ) {
        switch ( $key ) {
            case 'spotify_regional_csv': return new SpotifyWeeklyRegionalParser();
            case 'youtube_topsongs_csv': return new YouTubeTopSongsParser();
        }
        throw new \Exception( "Unknown parser profile selected." );
    }

    public function process_upload( $file, $parser_key, $week_date, $input_mode, $chart_id, $country_id ) {
        global $wpdb;
        $prefix = $wpdb->prefix . 'kc_';
        
        // Load file securely
        if ( ! is_uploaded_file( $file['tmp_name'] ) ) {
            throw new \Exception( "Invalid file upload." );
        }

        // Parse CSV
        $rows = array_map('str_getcsv', file($file['tmp_name']));
        if ( empty( $rows ) ) {
            throw new \Exception( "File is empty." );
        }
        
        $header = array_shift($rows);
        
        $parser = $this->get_parser( $parser_key );
        $issues = $parser->validate( $header );
        if ( !empty($issues) ) {
            throw new \Exception( "Validation error: " . implode( " ", $issues ) );
        }
        
        $platform_slug = strpos($parser_key, 'spotify') !== false ? 'spotify' : 'youtube';

        // 1. Create upload record
        $upload_data = [
            'file_name' => sanitize_file_name( $file['name'] ),
            'parser_key' => $parser_key,
            'source_platform' => $platform_slug,
            'input_mode' => $input_mode,
            'country_id' => $country_id,
            'chart_id' => $chart_id,
            'week_date' => $week_date,
            'status' => 'processing',
            'created_by' => get_current_user_id()
        ];
        
        $wpdb->insert( $prefix . 'source_uploads', $upload_data );
        $upload_id = $wpdb->insert_id;
        
        // Iterate rows
        $resolver = $this->container->get( EntityResolver::class );
        
        $row_count = 0;
        $valid_count = 0;
        $new_artists = 0;
        $new_tracks = 0;
        
        foreach ( $rows as $idx => $csv_row ) {
            if ( count($header) !== count($csv_row) ) continue; // malformed trailing empty row handling
            $mapped_raw = array_combine( $header, $csv_row );
            
            $normalized_data = $parser->normalizeRow( $mapped_raw );
            
            // Insert source row log
            $db_row = [
                'upload_id' => $upload_id,
                'row_number' => $idx + 2,
                'raw_data_json' => wp_json_encode( $mapped_raw ),
                'normalized_data_json' => wp_json_encode( $normalized_data ),
                'resolution_status' => 'pending'
            ];
            $wpdb->insert( $prefix . 'source_rows', $db_row );
            $source_row_id = $wpdb->insert_id;
            
            // Resolve Entities
            $resolution = $resolver->resolve( $normalized_data, $upload_id, $source_row_id, $chart_id, $country_id, $platform_slug, $week_date );
            
            // Update source row with status
            $wpdb->update( 
                $prefix . 'source_rows', 
                [ 
                    'resolution_status' => $resolution['status'], 
                    'matched_track_id' => $resolution['track_id'] 
                ], 
                [ 'id' => $source_row_id ] 
            );
            
            $new_artists += $resolution['new_artists'];
            $new_tracks += $resolution['new_tracks'];
            
            $row_count++;
            $valid_count++;
        }
        
        // Finalize Upload Tracking
        $wpdb->update(
            $prefix . 'source_uploads',
            [
                'status' => 'completed',
                'total_rows' => $row_count,
                'valid_rows' => $valid_count,
                'created_artists' => $new_artists,
                'created_tracks' => $new_tracks,
                'imported_rows' => $valid_count
            ],
            [ 'id' => $upload_id ]
        );
        
        // Trigger chart generation automatically for source-ranked V1 imports
        if ( $input_mode === 'source_ranked' ) {
            $builder = $this->container->get( ChartBuilder::class );
            $builder->build_week( $chart_id, $week_date, $upload_id );
        }

        return [
            'rows' => $valid_count,
            'upload_id' => $upload_id,
            'artists_created' => $new_artists,
            'tracks_created' => $new_tracks
        ];
    }
}
