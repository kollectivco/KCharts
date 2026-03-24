<?php
namespace Kontentainment\Charts\Core;

class Container {
    
    /**
     * Map of registered services.
     */
    protected $bindings = [];
    
    /**
     * Map of instantiated instances.
     */
    protected $instances = [];

    /**
     * Register a singleton binding.
     *
     * @param string $key
     * @param callable $resolver
     */
    public function singleton( $key, callable $resolver ) {
        $this->bindings[$key] = $resolver;
    }

    /**
     * Resolve a service.
     *
     * @param string $key
     * @return mixed
     */
    public function get( $key ) {
        if ( ! isset( $this->instances[$key] ) ) {
            if ( isset( $this->bindings[$key] ) ) {
                $this->instances[$key] = call_user_func( $this->bindings[$key], $this );
            } else {
                if ( class_exists( $key ) ) {
                    $this->instances[$key] = new $key();
                } else {
                    throw new \Exception( "Service {$key} not found." );
                }
            }
        }

        return $this->instances[$key];
    }
}
