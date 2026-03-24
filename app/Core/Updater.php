<?php
namespace Kontentainment\Charts\Core;

class Updater {

    /**
     * @var \YahnisElsts\PluginUpdateChecker\v5\Vcs\PluginUpdateChecker|null
     */
    private $update_checker = null;

    public function init() {
        $library_file = KC_DIR . 'includes/plugin-update-checker/plugin-update-checker.php';

        if ( ! file_exists( $library_file ) ) {
            error_log('KCharts: Plugin Update Checker library missing at ' . $library_file);
            return;
        }

        try {
            require_once $library_file;

            // Use the v5 factory as requested
            if ( ! class_exists( 'YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
                error_log('KCharts: PucFactory v5 class not found.');
                return;
            }

            $this->update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
                'https://github.com/kollectivco/KCharts/',
                KC_FILE,
                'kontentainment-charts'
            );

            if ( $this->update_checker ) {
                // Set the branch to check (default is master/main)
                $this->update_checker->setBranch('main');

                // Enable release assets if available (checks for .zip files in releases)
                if ( method_exists($this->update_checker, 'getVcsApi') ) {
                    $vcs_api = $this->update_checker->getVcsApi();
                    if ($vcs_api && method_exists($vcs_api, 'enableReleaseAssets')) {
                        $vcs_api->enableReleaseAssets('/\.zip($|[?&#])/i');
                    }
                }
                
                // Add manual check action
                add_action( 'wp_ajax_kc_check_updates', [ $this, 'handle_manual_check' ] );
            }

        } catch ( \Exception $e ) {
            error_log( 'KCharts: Updater initialization failed: ' . $e->getMessage() );
            $this->update_checker = null;
        }
    }

    public function handle_manual_check() {
        check_ajax_referer( 'kc_ajax_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized.' ] );
        }

        if ( ! $this->update_checker ) {
            wp_send_json_error( [ 'message' => 'Updater not initialized or library missing.' ] );
        }

        try {
            $update = $this->update_checker->requestUpdate();
            
            if ( $update ) {
                wp_send_json_success([
                    'update_available' => true,
                    'version' => $update->version,
                    'message' => sprintf( 'New version %s is available!', $update->version )
                ]);
            } else {
                wp_send_json_success([
                    'update_available' => false,
                    'message' => 'You are running the latest version.'
                ]);
            }
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => 'Update check failed: ' . $e->getMessage() ] );
        }
    }

    public function get_status() {
        if ( ! $this->update_checker ) {
            return [ 
                'status' => 'Not initialized',
                'last_check' => 'N/A',
                'version_installed' => KC_VERSION,
                'update_available' => false
            ];
        }

        $state = $this->update_checker->getUpdateState();
        return [
            'last_check' => $state ? $state->getLastCheck() : 'Never',
            'version_installed' => KC_VERSION,
            'update_available' => $this->update_checker->getUpdate() !== null
        ];
    }
}
