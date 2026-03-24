<?php
namespace Kontentainment\Charts\Core;

class Capabilities {

    /**
     * Map of roles and the capabilities they should receive.
     */
    protected static $role_caps = [
        'administrator' => [
            'manage_kc_charts',
            'upload_kc_chart_data',
            'review_kc_chart_data',
            'publish_kc_chart_weeks',
            'manage_kc_settings'
        ],
        'editor' => [
            'manage_kc_charts',
            'upload_kc_chart_data',
            'review_kc_chart_data'
        ]
    ];

    /**
     * Add roles / caps on activation.
     */
    public static function add() {
        foreach ( self::$role_caps as $role_slug => $caps ) {
            $role = get_role( $role_slug );
            if ( $role ) {
                foreach ( $caps as $cap ) {
                    $role->add_cap( $cap );
                }
            }
        }
    }

    /**
     * Remove roles / caps on deactivation.
     */
    public static function remove() {
        foreach ( self::$role_caps as $role_slug => $caps ) {
            $role = get_role( $role_slug );
            if ( $role ) {
                foreach ( $caps as $cap ) {
                    $role->remove_cap( $cap );
                }
            }
        }
    }
}
