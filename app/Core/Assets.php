<?php
namespace Kontentainment\Charts\Core;

class Assets {
    
    protected $container;

    public function __construct( Container $container ) {
        $this->container = $container;
    }

    public function init() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_public_assets' ] );
    }

    /**
     * @param string $hook
     */
    public function enqueue_admin_assets( $hook ) {
        if ( strpos( $hook, 'kontentainment-charts' ) === false ) {
            return; // only load on our pages
        }

        wp_enqueue_style(
            'kc-admin-css',
            KC_URL . 'resources/css/admin.css',
            [],
            KC_VERSION
        );

        wp_enqueue_script(
            'kc-admin-js',
            KC_URL . 'resources/js/admin.js',
            [ 'jquery' ],
            KC_VERSION,
            true
        );

        wp_localize_script( 'kc-admin-js', 'kcObject', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'kc_ajax_nonce' )
        ] );
    }

    public function enqueue_public_assets() {
        // load public assets when shortcodes are used
    }
}
