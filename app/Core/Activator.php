<?php
namespace Kontentainment\Charts\Core;

class Activator {

    /**
     * Fired during plugin activation.
     */
    public static function activate() {
        // Run database migrations on activation.
        if ( class_exists( '\Kontentainment\Charts\Database\Migrator' ) ) {
            \Kontentainment\Charts\Database\Migrator::run();
        }
        
        // Add default capabilities.
        if ( class_exists( '\Kontentainment\Charts\Core\Capabilities' ) ) {
            \Kontentainment\Charts\Core\Capabilities::add();
        }

        // Trigger a flush of rewrite rules right after activation to ensure endpoints work.
        flush_rewrite_rules();
    }
}
