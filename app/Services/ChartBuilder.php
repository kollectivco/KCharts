<?php
namespace Kontentainment\Charts\Services;

use Kontentainment\Charts\Core\Container;

class ChartBuilder {

    protected $container;
    
    public function __construct( Container $container ) {
        $this->container = $container;
    }

    /**
     * Builds or rebuilds a Chart Week based on source metrics.
     */
    public function build_week( $chart_id, $week_date, $upload_id = null ) {
        global $wpdb;
        $prefix = $wpdb->prefix . 'kc_';
        
        // 1. Ensure chart week exists
        $week_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$prefix}chart_weeks WHERE chart_id = %d AND week_date = %s",
            $chart_id, $week_date
        ));
        
        if ( ! $week_id ) {
            $wpdb->insert( "{$prefix}chart_weeks", [
                'chart_id' => $chart_id,
                'week_date' => $week_date,
                'status' => 'draft',
                'source_upload_id' => $upload_id,
                'generated_at' => current_time('mysql')
            ]);
            $week_id = $wpdb->insert_id;
        } else {
            // clear existing entries for rebuild
            $wpdb->delete("{$prefix}chart_entries", ['chart_week_id' => $week_id]);
            $wpdb->update("{$prefix}chart_weeks", [
                'generated_at' => current_time('mysql'),
                'source_upload_id' => $upload_id
            ], ['id' => $week_id]);
        }
        
        // 2. Query metrics for this week and chart
        // Assuming V1 logic: we rely directly on the 'source_rank' for direct source-ranked charts
        // The most recently uploaded metrics for that chart/week will be used
        $metrics = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM {$prefix}track_week_metrics
            WHERE chart_id = %d AND week_date = %s
            ORDER BY source_rank ASC
        ", $chart_id, $week_date));
        
        // 3. Create chart entries
        foreach ( $metrics as $metric ) {
            if ( ! $metric->source_rank ) continue;
            
            $current_rank = (int) $metric->source_rank;
            $previous_rank = (int) $metric->source_previous_rank;
            
            // Movement Logic
            $movement_value = null;
            $movement_type = 'new';
            $is_new = 1;
            $is_reentry = 0;
            
            if ( $previous_rank > 0 ) {
                $is_new = 0;
                $movement_value = $previous_rank - $current_rank;
                if ( $movement_value > 0 ) {
                    $movement_type = 'up';
                } elseif ( $movement_value < 0 ) {
                    $movement_type = 'down';
                } else {
                    $movement_type = 'same';
                }
            } else {
                // If it doesn't have a source_previous_rank, we should check our internal history for re-entry.
                // For V1, we'll assume it's new if the source didn't provide a previous rank. 
                // But let's check internal DB just in case:
                $prior_existence = $wpdb->get_var($wpdb->prepare("
                    SELECT id FROM {$prefix}chart_entries 
                    WHERE track_id = %d AND chart_week_id IN (
                        SELECT id FROM {$prefix}chart_weeks WHERE chart_id = %d AND week_date < %s
                    ) LIMIT 1
                ", $metric->track_id, $chart_id, $week_date));
                
                if ( $prior_existence ) {
                    $is_new = 0;
                    $is_reentry = 1;
                    $movement_type = 'reentry';
                }
            }
            
            // Peak Position Logic
            $peak = $metric->source_peak_rank;
            if ( ! $peak ) {
                // Determine lowest numerical rank from past
                $past_peak = $wpdb->get_var($wpdb->prepare("
                    SELECT MIN(rank) FROM {$prefix}chart_entries 
                    WHERE track_id = %d AND chart_week_id IN (
                        SELECT id FROM {$prefix}chart_weeks WHERE chart_id = %d AND week_date < %s AND status = 'published'
                    )
                ", $metric->track_id, $chart_id, $week_date));
                
                if ( $past_peak && $past_peak < $current_rank ) {
                    $peak = $past_peak;
                } else {
                    $peak = $current_rank;
                }
            }
            
            // Weeks on chart logic
            $woc = $metric->source_weeks_on_chart;
            if ( ! $woc ) {
                $past_woc = $wpdb->get_var($wpdb->prepare("
                    SELECT COUNT(id) FROM {$prefix}chart_entries 
                    WHERE track_id = %d AND chart_week_id IN (
                        SELECT id FROM {$prefix}chart_weeks WHERE chart_id = %d AND week_date < %s AND status = 'published'
                    )
                ", $metric->track_id, $chart_id, $week_date));
                $woc = $past_woc + 1;
            }
            
            // Insert
            $wpdb->insert( "{$prefix}chart_entries", [
                'chart_week_id' => $week_id,
                'rank' => $current_rank,
                'previous_rank' => $previous_rank ?: null,
                'movement_value' => $movement_value,
                'movement_type' => $movement_type,
                'track_id' => $metric->track_id,
                'score' => null,
                'metric_primary_type' => $metric->metric_primary_type,
                'metric_primary_value' => $metric->metric_primary_value,
                'peak_position' => $peak,
                'weeks_on_chart' => $woc,
                'is_new' => $is_new,
                'is_reentry' => $is_reentry
            ]);
        }
        
        return $week_id;
    }

    /**
     * Publish a chart week.
     */
    public function publish_week( $week_id ) {
        global $wpdb;
        $prefix = $wpdb->prefix . 'kc_';

        $week = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}chart_weeks WHERE id = %d", $week_id));
        if ( ! $week ) {
            throw new \Exception("Week not found.");
        }
        
        // Prevent duplicate published weeks for the same chart and date
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$prefix}chart_weeks WHERE chart_id = %d AND week_date = %s AND status = 'published' AND id != %d",
            $week->chart_id, $week->week_date, $week_id
        ));
        
        if ( $existing ) {
            throw new \Exception("A published week already exists for this chart and date.");
        }
        
        $wpdb->update("{$prefix}chart_weeks", [
            'status' => 'published',
            'published_at' => current_time('mysql')
        ], ['id' => $week_id]);
        
        // Insert audit log
        $wpdb->insert("{$prefix}audit_logs", [
            'user_id' => get_current_user_id(),
            'action' => 'publish_chart_week',
            'entity_type' => 'chart_week',
            'entity_id' => $week_id,
            'result' => 'success'
        ]);
        
        return true;
    }

    /**
     * Unpublish a chart week.
     */
    public function unpublish_week( $week_id ) {
        global $wpdb;
        $prefix = $wpdb->prefix . 'kc_';

        $week = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}chart_weeks WHERE id = %d", $week_id));
        if ( ! $week ) {
            throw new \Exception("Week not found.");
        }
        
        $wpdb->update("{$prefix}chart_weeks", [
            'status' => 'draft',
            'published_at' => null
        ], ['id' => $week_id]);
        
        // Insert audit log
        $wpdb->insert("{$prefix}audit_logs", [
            'user_id' => get_current_user_id(),
            'action' => 'unpublish_chart_week',
            'entity_type' => 'chart_week',
            'entity_id' => $week_id,
            'result' => 'success'
        ]);
        
        return true;
    }
}
