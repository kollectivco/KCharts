<?php
namespace Kontentainment\Charts\Database;

class Schema {

    /**
     * Get all tables structure for dbDelta
     * 
     * @param string $charset_collate The db charset/collate string 
     * @param string $prefix          The db table prefix (e.g. wp_kc_)
     * @return array
     */
    public static function get_schema( $charset_collate, $prefix ) {
        $tables = [];

        $tables[] = "CREATE TABLE {$prefix}platforms (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            type varchar(100) NOT NULL DEFAULT '',
            status varchar(50) NOT NULL DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}countries (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            code varchar(10) NOT NULL,
            name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY code (code),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}charts (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            chart_type varchar(100) NOT NULL DEFAULT 'songs',
            country_id bigint(20) unsigned DEFAULT NULL,
            max_positions int(11) NOT NULL DEFAULT 100,
            status varchar(50) NOT NULL DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}chart_weeks (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            chart_id bigint(20) unsigned NOT NULL,
            week_date date NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'draft',
            source_upload_id bigint(20) unsigned DEFAULT NULL,
            generated_at datetime DEFAULT NULL,
            published_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY chart_week (chart_id, week_date)
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}chart_entries (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            chart_week_id bigint(20) unsigned NOT NULL,
            rank int(11) NOT NULL,
            previous_rank int(11) DEFAULT NULL,
            movement_value int(11) DEFAULT NULL,
            movement_type varchar(50) NOT NULL DEFAULT 'new',
            track_id bigint(20) unsigned DEFAULT NULL,
            score decimal(15,4) DEFAULT NULL,
            metric_primary_type varchar(100) DEFAULT NULL,
            metric_primary_value decimal(30,4) DEFAULT NULL,
            peak_position int(11) DEFAULT NULL,
            weeks_on_chart int(11) DEFAULT NULL,
            is_new tinyint(1) NOT NULL DEFAULT 0,
            is_reentry tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY week_rank (chart_week_id, rank)
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}artists (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            normalized_name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            country_id bigint(20) unsigned DEFAULT NULL,
            image_url text DEFAULT NULL,
            metadata_json longtext DEFAULT NULL,
            status varchar(50) NOT NULL DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY normalized_name (normalized_name(191)),
            UNIQUE KEY slug (slug(191))
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}artist_aliases (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            artist_id bigint(20) unsigned NOT NULL,
            alias_name varchar(255) NOT NULL,
            normalized_alias_name varchar(255) NOT NULL,
            source varchar(100) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY normalized_alias_name (normalized_alias_name(191))
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}tracks (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            normalized_title varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            primary_artist_id bigint(20) unsigned DEFAULT NULL,
            artwork_url text DEFAULT NULL,
            release_date date DEFAULT NULL,
            metadata_json longtext DEFAULT NULL,
            status varchar(50) NOT NULL DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY normalized_title (normalized_title(191)),
            UNIQUE KEY slug (slug(191))
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}track_artists (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            track_id bigint(20) unsigned NOT NULL,
            artist_id bigint(20) unsigned NOT NULL,
            role varchar(50) NOT NULL DEFAULT 'main',
            sort_order int(11) NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY track_artist_role (track_id, artist_id, role)
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}track_external_refs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            track_id bigint(20) unsigned NOT NULL,
            platform_slug varchar(100) NOT NULL,
            ref_type varchar(100) NOT NULL,
            ref_value varchar(255) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY ref_unique (platform_slug, ref_type, ref_value)
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}source_uploads (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            file_name varchar(255) NOT NULL,
            parser_key varchar(100) NOT NULL,
            source_platform varchar(100) NOT NULL,
            input_mode varchar(100) NOT NULL DEFAULT 'source_ranked',
            country_id bigint(20) unsigned DEFAULT NULL,
            chart_id bigint(20) unsigned DEFAULT NULL,
            week_date date NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'pending',
            total_rows int(11) NOT NULL DEFAULT 0,
            valid_rows int(11) NOT NULL DEFAULT 0,
            invalid_rows int(11) NOT NULL DEFAULT 0,
            created_artists int(11) NOT NULL DEFAULT 0,
            updated_artists int(11) NOT NULL DEFAULT 0,
            created_tracks int(11) NOT NULL DEFAULT 0,
            updated_tracks int(11) NOT NULL DEFAULT 0,
            review_rows int(11) NOT NULL DEFAULT 0,
            imported_rows int(11) NOT NULL DEFAULT 0,
            created_by bigint(20) unsigned DEFAULT NULL,
            notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}source_rows (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            upload_id bigint(20) unsigned NOT NULL,
            row_number int(11) NOT NULL,
            raw_data_json longtext NOT NULL,
            normalized_data_json longtext NOT NULL,
            resolution_status varchar(50) NOT NULL DEFAULT 'pending',
            matched_track_id bigint(20) unsigned DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY upload_id (upload_id)
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}track_week_metrics (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            track_id bigint(20) unsigned NOT NULL,
            chart_id bigint(20) unsigned NOT NULL,
            country_id bigint(20) unsigned DEFAULT NULL,
            platform_slug varchar(100) NOT NULL,
            week_date date NOT NULL,
            source_rank int(11) DEFAULT NULL,
            source_previous_rank int(11) DEFAULT NULL,
            source_peak_rank int(11) DEFAULT NULL,
            source_weeks_on_chart int(11) DEFAULT NULL,
            metric_primary_type varchar(100) DEFAULT NULL,
            metric_primary_value decimal(30,4) DEFAULT NULL,
            growth_percent decimal(10,4) DEFAULT NULL,
            external_url varchar(2000) DEFAULT NULL,
            external_uri varchar(255) DEFAULT NULL,
            upload_id bigint(20) unsigned DEFAULT NULL,
            source_row_id bigint(20) unsigned DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY track_metric_unique (track_id, chart_id, week_date, platform_slug, country_id)
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}notifications (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            type varchar(100) NOT NULL,
            level varchar(50) NOT NULL DEFAULT 'info',
            title text NOT NULL,
            message text NOT NULL,
            group_key varchar(100) DEFAULT NULL,
            related_entity_type varchar(100) DEFAULT NULL,
            related_entity_id bigint(20) unsigned DEFAULT NULL,
            is_read tinyint(1) NOT NULL DEFAULT 0,
            is_dismissed tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}settings (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            setting_key varchar(100) NOT NULL,
            setting_value longtext DEFAULT NULL,
            autoload tinyint(1) NOT NULL DEFAULT 1,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY setting_key (setting_key)
        ) $charset_collate;";

        $tables[] = "CREATE TABLE {$prefix}audit_logs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned DEFAULT NULL,
            action varchar(255) NOT NULL,
            entity_type varchar(100) NOT NULL,
            entity_id bigint(20) unsigned DEFAULT NULL,
            context_json longtext DEFAULT NULL,
            result varchar(50) DEFAULT 'success',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        return $tables;
    }
}
