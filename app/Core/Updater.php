<?php
namespace Kontentainment\Charts\Core;

class Updater {

    protected $update_checker;

    public function init() {
        if ( ! file_exists( KC_DIR . 'includes/plugin-update-checker/plugin-update-checker.php' ) ) {
            return;
        }

        require KC_DIR . 'includes/plugin-update-checker/plugin-update-checker.php';
        
        $this->update_checker = \Puc_v4_Factory::buildUpdateChecker(
            'https://github.com/kollectivco/KCharts/',
            KC_FILE,
            'kontentainment-charts'
        );

        // Optional: If the repository is private, you can set an access token.
        // $this->update_checker->setAuthentication('your-token-here');

        // Set the branch to check (default is master/main)
        $this->update_checker->setBranch('main');
        
        // Add manual check action
        add_action( 'wp_ajax_kc_check_updates', [ $this, 'handle_manual_check' ] );
    }

    public function handle_manual_check() {
        check_ajax_referer( 'kc_ajax_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Unauthorized.' ] );
        }

        if ( ! $this->update_checker ) {
            wp_send_json_error( [ 'message' => 'Updater not initialized.' ] );
        }

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
    }

    public function get_status() {
        if ( ! $this->update_checker ) {
            return [ 'status' => 'Not initialized' ];
        }

        $state = $this->update_checker->getUpdateState();
        return [
            'last_check' => $state->getLastCheck(),
            'version_installed' => KC_VERSION,
            'update_available' => $this->update_checker->getUpdate() !== null
        ];
    }
}
