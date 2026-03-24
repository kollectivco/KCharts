<?php
/**
 * Plugin Name:       Kontentainment Charts
 * Plugin URI:        https://kontentainment.com
 * Description:       Upload weekly chart spreadsheets, parse, normalize, and publish music chart data.
 * Version:           1.0.1
 * Author:            Kontentainment
 * Author URI:        https://kontentainment.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://github.com/kollectivco/KCharts
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Constants
 */
define( 'KC_VERSION', '1.0.1' );
define( 'KC_FILE', __FILE__ );
define( 'KC_PATH', plugin_dir_path( __FILE__ ) );
define( 'KC_DIR', KC_PATH );
define( 'KC_URL', plugin_dir_url( __FILE__ ) );

/**
 * Custom PSR-4 Autoloader
 */
spl_autoload_register(function ( $class ) {
    $prefix = 'Kontentainment\\Charts\\';
    $base_dir = KC_DIR . 'app/';

    $len = strlen( $prefix );
    if ( strncmp( $prefix, $class, $len ) !== 0 ) {
        return; // No, move to the next registered autoloader
    }

    $relative_class = substr( $class, $len );
    $file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

    if ( file_exists( $file ) ) {
        require $file;
    }
});

/**
 * Activation / Deactivation Hooks
 */
function activate_kontentainment_charts() {
    if ( class_exists( 'Kontentainment\Charts\Core\Activator' ) ) {
        \Kontentainment\Charts\Core\Activator::activate();
    }
}

function deactivate_kontentainment_charts() {
    if ( class_exists( 'Kontentainment\Charts\Core\Deactivator' ) ) {
        \Kontentainment\Charts\Core\Deactivator::deactivate();
    }
}

register_activation_hook( __FILE__, 'activate_kontentainment_charts' );
register_deactivation_hook( __FILE__, 'deactivate_kontentainment_charts' );

/**
 * Boot the plugin
 */
add_action( 'plugins_loaded', function() {
    if ( class_exists( 'Kontentainment\Charts\Core\Plugin' ) ) {
        $plugin = new \Kontentainment\Charts\Core\Plugin();
        $plugin->run();
    }
} );
