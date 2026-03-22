<?php

namespace PerformanceOptimizer;

/**
 * PerformanceOptimizer Main Class
 * 
 * Central orchestrator for all performance optimization features.
 * Manages initialization, configuration, and coordination of all components.
 */
class PerformanceOptimizer
{
    /**
     * Singleton instance
     * 
     * @var self
     */
    private static $instance = null;

    /**
     * Global configuration
     * 
     * @var array
     */
    private $config = [];

    /**
     * Database connection
     * 
     * @var \PDO
     */
    private $db = null;

    /**
     * Registered components
     * 
     * @var array
     */
    private $components = [];

    /**
     * Private constructor for singleton pattern
     */
    private function __construct()
    {
        $this->loadConfiguration();
    }

    /**
     * Get singleton instance
     * 
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Load configuration from config file
     * 
     * @return void
     */
    private function loadConfiguration()
    {
        $configFile = __DIR__ . '/config.php';
        if (file_exists($configFile)) {
            $this->config = require $configFile;
        }
    }

    /**
     * Initialize the performance optimizer system
     * 
     * @param \PDO $db Database connection
     * @return bool True if initialization successful
     */
    public function initialize($db)
    {
        $this->db = $db;

        try {
            $initializer = new DatabaseInitializer($db);
            if (!$initializer->initialize()) {
                error_log('Performance Optimizer: Failed to initialize database tables');
                return false;
            }

            $this->createCacheDirectories();
            return true;
        } catch (\Exception $e) {
            error_log('Performance Optimizer: Initialization failed - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create required cache directories
     * 
     * @return void
     */
    private function createCacheDirectories()
    {
        $cacheDir = $this->getConfig('cache.directory', __DIR__ . '/../../cache');
        $directories = [
            $cacheDir,
            $cacheDir . '/pages',
            $cacheDir . '/queries',
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * Get configuration value
     * 
     * @param string $key Configuration key (dot notation supported)
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value
     */
    public function getConfig($key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (is_array($value) && array_key_exists($k, $value)) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Get database connection
     * 
     * @return \PDO
     */
    public function getDatabase()
    {
        return $this->db;
    }

    /**
     * Register a component
     * 
     * @param string $name Component name
     * @param OptimizationComponent $component Component instance
     * @return void
     */
    public function registerComponent($name, OptimizationComponent $component)
    {
        $this->components[$name] = $component;
    }

    /**
     * Get a registered component
     * 
     * @param string $name Component name
     * @return OptimizationComponent|null
     */
    public function getComponent($name)
    {
        return $this->components[$name] ?? null;
    }

    /**
     * Get all registered components
     * 
     * @return array
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * Check if a feature is enabled
     * 
     * @param string $feature Feature name (e.g., 'cache', 'image', 'assets')
     * @return bool
     */
    public function isFeatureEnabled($feature)
    {
        return $this->getConfig($feature . '.enabled', false);
    }
}
