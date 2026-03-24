<?php
namespace Kontentainment\Charts\Core;

class Deactivator {

    /**
     * Fired during plugin deactivation.
     */
    public static function deactivate() {
        // We typically do NOT drop tables on deactivation, let users uninstall if needed.
        
        // Remove capabilities setup during activation.
        if ( class_exists( '\Kontentainment\Charts\Core\Capabilities' ) ) {
            \Kontentainment\Charts\Core\Capabilities::remove();
        }
        
        // Flush rules gracefully to reset standard WP URL routing.
        flush_rewrite_rules();
    }
}
