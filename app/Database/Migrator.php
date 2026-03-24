<?php
namespace Kontentainment\Charts\Database;

class Migrator {

    /**
     * Run all migrations.
     */
    public static function run() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $prefix = $wpdb->prefix . 'kc_';

        $tables = \Kontentainment\Charts\Database\Schema::get_schema( $charset_collate, $prefix );

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        foreach ( $tables as $sql ) {
            dbDelta( $sql );
        }
        
        // Ensure default settings or initial seed data, like built-in platforms, can be run here
        self::seed_initial_data($prefix);
    }
    
    protected static function seed_initial_data($prefix) {
        global $wpdb;
        
        // Seed Platforms
        $table_name = $prefix . 'platforms';
        $platforms = [
            [ 'name' => 'Spotify', 'slug' => 'spotify', 'type' => 'streaming' ],
            [ 'name' => 'YouTube', 'slug' => 'youtube', 'type' => 'video' ],
        ];
        
        foreach($platforms as $platform) {
            $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE slug = %s", $platform['slug']));
            if (!$exists) {
                $wpdb->insert($table_name, $platform);
            }
        }
    }
}
