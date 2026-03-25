<?php
namespace Kontentainment\Charts\Services;

use Kontentainment\Charts\Core\Container;

class EntityResolver {

    protected $container;
    
    public function __construct( Container $container ) {
        $this->container = $container;
    }

    public function resolve( $normalized, $upload_id, $source_row_id, $chart_id, $country_id, $platform_slug, $week_date, $manual_artist_id = null, $manual_track_title = null ) {
        global $wpdb;
        $prefix = $wpdb->prefix . 'kc_';
        
        $raw_artist_names = $normalized['raw_artist_names'];
        $raw_track_title = $normalized['raw_track_name'];
        
        $new_artists = 0;
        $new_tracks = 0;

        // 1. Resolve Artists (split by comma or common delimiters)
        $artist_parts = preg_split('/(,|&|feat\.|ft\.)/i', $raw_artist_names);
        $artist_ids = [];
        $primary_artist_id = null;
        
        foreach ( $artist_parts as $idx => $raw_name ) {
            $name = trim($raw_name);
            if ( empty($name) ) continue;
            
            $norm_name = sanitize_title($name);
            if ( empty($norm_name) ) continue;

            // Search DB by normalized name
            $artist_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$prefix}artists WHERE normalized_name = %s LIMIT 1", $norm_name));
            
            if ( !$artist_id ) {
                // Auto-create artist
                $wpdb->insert( "{$prefix}artists", [
                    'name' => $name,
                    'normalized_name' => $norm_name,
                    'slug' => $norm_name . '-' . uniqid(),
                    'status' => 'active'
                ]);
                $artist_id = $wpdb->insert_id;
                $new_artists++;
            }
            
            $artist_ids[] = [
                'id' => $artist_id,
                'role' => $idx === 0 ? 'main' : 'featured'
            ];
            
            if ( $idx === 0 ) {
                $primary_artist_id = $artist_id;
            }
        }
        
        // 2. Resolve Track
        $track_status = 'auto_matched';
        $track_id = null;
        
        // Manual override case
        if ( $manual_artist_id && !empty($manual_track_title) ) {
            $primary_artist_id = $manual_artist_id;
            $raw_track_title = $manual_track_title;
            $track_status = 'manually_resolved';
        }

        $track_norm = sanitize_title($raw_track_title);
        
        if ( !empty($track_norm) && $primary_artist_id ) {
            $track_id = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$prefix}tracks WHERE normalized_title = %s AND primary_artist_id = %d LIMIT 1",
                $track_norm, $primary_artist_id
            ));
            
            if ( !$track_id ) {
                $wpdb->insert( "{$prefix}tracks", [
                    'title' => $raw_track_title,
                    'normalized_title' => $track_norm,
                    'slug' => $track_norm . '-' . uniqid(),
                    'primary_artist_id' => $primary_artist_id,
                    'status' => 'active'
                ]);
                $track_id = $wpdb->insert_id;
                $new_tracks++;
                if ($track_status !== 'manually_resolved') $track_status = 'auto_created';
                
                // insert to bridge table for authors
                if (!$manual_artist_id) {
                    foreach ( $artist_ids as $k => $art ) {
                        $wpdb->insert( "{$prefix}track_artists", [
                            'track_id' => $track_id,
                            'artist_id' => $art['id'],
                            'role' => $art['role'],
                            'sort_order' => $k
                        ]);
                    }
                } else {
                    $wpdb->insert( "{$prefix}track_artists", [
                        'track_id' => $track_id,
                        'artist_id' => $manual_artist_id,
                        'role' => 'main',
                        'sort_order' => 0
                    ]);
                }
            }
            
            // 3. Save weekly metric safely
            $query = "INSERT INTO {$prefix}track_week_metrics 
                (track_id, chart_id, country_id, platform_slug, week_date, source_rank, source_previous_rank, source_peak_rank, source_weeks_on_chart, metric_primary_type, metric_primary_value, growth_percent, external_url, external_uri, upload_id, source_row_id) 
                VALUES (%d, %d, %d, %s, %s, %d, %d, %d, %d, %s, %f, %f, %s, %s, %d, %d) 
                ON DUPLICATE KEY UPDATE 
                source_rank = VALUES(source_rank),
                source_previous_rank = VALUES(source_previous_rank),
                source_peak_rank = VALUES(source_peak_rank),
                source_weeks_on_chart = VALUES(source_weeks_on_chart),
                metric_primary_type = VALUES(metric_primary_type),
                metric_primary_value = VALUES(metric_primary_value),
                growth_percent = VALUES(growth_percent),
                external_url = VALUES(external_url),
                external_uri = VALUES(external_uri),
                upload_id = VALUES(upload_id),
                source_row_id = VALUES(source_row_id),
                updated_at = CURRENT_TIMESTAMP";

            $wpdb->query($wpdb->prepare($query,
                $track_id,
                $chart_id,
                $country_id,
                $platform_slug,
                $week_date,
                $normalized['raw_rank'],
                $normalized['raw_previous_rank'],
                $normalized['raw_peak_rank'] ?? null,
                $normalized['raw_weeks_on_chart'] ?? null,
                $normalized['metric_primary_type'],
                $normalized['metric_primary_value'],
                $normalized['raw_growth_value'],
                $normalized['external_url'],
                $normalized['external_uri'],
                $upload_id,
                $source_row_id
            ));
            
        } else {
            $track_status = 'needs_review';
        }
        
        return [
            'status' => $track_status,
            'track_id' => $track_id,
            'new_artists' => $new_artists,
            'new_tracks' => $new_tracks
        ];
    }
}
