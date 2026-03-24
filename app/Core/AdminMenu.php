<?php
namespace Kontentainment\Charts\Core;

class AdminMenu {

    protected $container;

    public function __construct( Container $container ) {
        $this->container = $container;
    }

    public function init() {
        add_action( 'admin_menu', [ $this, 'register_menus' ] );
    }

    public function register_menus() {
        add_menu_page(
            'Kontentainment Charts',
            'K-Charts',
            'manage_kc_charts',
            'kontentainment-charts-dashboard',
            [ $this, 'render_dashboard' ],
            'dashicons-chart-area', // We will replace this with custom icon if needed
            25
        );

        add_submenu_page(
            'kontentainment-charts-dashboard',
            'Dashboard',
            'Dashboard',
            'manage_kc_charts',
            'kontentainment-charts-dashboard',
            [ $this, 'render_dashboard' ]
        );

        add_submenu_page(
            'kontentainment-charts-dashboard',
            'Uploads',
            'Uploads',
            'upload_kc_chart_data',
            'kontentainment-charts-uploads',
            [ $this, 'render_uploads' ]
        );

        add_submenu_page(
            'kontentainment-charts-dashboard',
            'Review',
            'Review',
            'review_kc_chart_data',
            'kontentainment-charts-review',
            [ $this, 'render_review' ]
        );

        add_submenu_page(
            'kontentainment-charts-dashboard',
            'Charts',
            'Charts',
            'manage_kc_charts',
            'kontentainment-charts-charts',
            [ $this, 'render_charts' ]
        );

        add_submenu_page(
            'kontentainment-charts-dashboard',
            'Artists',
            'Artists',
            'manage_kc_charts',
            'kontentainment-charts-artists',
            [ $this, 'render_artists' ]
        );

        add_submenu_page(
            'kontentainment-charts-dashboard',
            'Tracks',
            'Tracks',
            'manage_kc_charts',
            'kontentainment-charts-tracks',
            [ $this, 'render_tracks' ]
        );

        add_submenu_page(
            'kontentainment-charts-dashboard',
            'Notifications',
            'Notifications',
            'manage_kc_charts',
            'kontentainment-charts-notifications',
            [ $this, 'render_notifications' ]
        );

        add_submenu_page(
            'kontentainment-charts-dashboard',
            'Settings',
            'Settings',
            'manage_kc_settings',
            'kontentainment-charts-settings',
            [ $this, 'render_settings' ]
        );

        add_submenu_page(
            'kontentainment-charts-dashboard',
            'Tools',
            'Tools',
            'manage_kc_charts',
            'kontentainment-charts-tools',
            [ $this, 'render_tools' ]
        );
    }

    public function render_dashboard() {
        $this->load_view( 'dashboard' );
    }

    public function render_uploads() {
        $this->load_view( 'uploads' );
    }

    public function render_review() {
        $this->load_view( 'review' );
    }

    public function render_charts() {
        $this->load_view( 'charts' );
    }

    public function render_artists() {
        $this->load_view( 'artists' );
    }

    public function render_tracks() {
        $this->load_view( 'tracks' );
    }

    public function render_notifications() {
        $this->load_view( 'notifications' );
    }

    public function render_tools() {
        $this->load_view( 'tools' );
    }

    public function render_settings() {
        $this->load_view( 'settings' );
    }

    protected function load_view( $view_name, $args = [] ) {
        $view_file = KC_DIR . '/resources/views/admin/' . $view_name . '.php';

        if ( file_exists( $view_file ) ) {
            extract( $args );
            include $view_file;
        } else {
            echo '<div class="wrap"><h1>View Not Found</h1></div>';
        }
    }
}
