<?php
namespace Kontentainment\Charts\Core;

class Plugin {

    /**
     * @var Container
     */
    protected $container;

    public function __construct() {
        $this->container = new Container();
        $this->bind_services();
    }

    /**
     * Bind all core services.
     */
    protected function bind_services() {
        // Core Singletons
        $this->container->singleton( AdminMenu::class, function( $c ) {
            return new AdminMenu( $c );
        } );
        
        $this->container->singleton( Assets::class, function( $c ) {
            return new Assets( $c );
        } );

        $this->container->singleton( \Kontentainment\Charts\Services\EntityResolver::class, function( $c ) {
            return new \Kontentainment\Charts\Services\EntityResolver( $c );
        } );

        $this->container->singleton( \Kontentainment\Charts\Services\ChartBuilder::class, function( $c ) {
            return new \Kontentainment\Charts\Services\ChartBuilder( $c );
        } );

        $this->container->singleton( \Kontentainment\Charts\Services\UploadService::class, function( $c ) {
            return new \Kontentainment\Charts\Services\UploadService( $c );
        } );

        $this->container->singleton( \Kontentainment\Charts\Core\PublicSite::class, function( $c ) {
            return new \Kontentainment\Charts\Core\PublicSite( $c );
        } );

        $this->container->singleton( \Kontentainment\Charts\Http\Ajax::class, function( $c ) {
            return new \Kontentainment\Charts\Http\Ajax( $c );
        } );

        $this->container->singleton( \Kontentainment\Charts\Core\Updater::class, function( $c ) {
            return new \Kontentainment\Charts\Core\Updater();
        } );
    }

    /**
     * Run the plugin.
     */
    public function run() {
        // Initialize Assets
        $this->container->get( Assets::class )->init();

        // Admin-only components
        if ( is_admin() ) {
            $this->container->get( AdminMenu::class )->init();
            $this->container->get( \Kontentainment\Charts\Http\Ajax::class )->init();
            $this->container->get( \Kontentainment\Charts\Core\Updater::class )->init();
        }

        // Action hooks for public routing, shortcodes, etc.
        $this->container->get( \Kontentainment\Charts\Core\PublicSite::class )->init();
    }
}
