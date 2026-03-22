<?php

namespace PerformanceOptimizer;

/**
 * OptimizationComponent Abstract Class
 * 
 * Base class for all optimization components (PageCache, ImageOptimizer, etc.).
 * Provides common functionality for configuration management, logging, and metrics.
 */
abstract class OptimizationComponent
{
    /**
     * Component configuration
     * 
     * @var array
     */
    protected $config = [];

    /**
     * Component name for logging
     * 
     * @var string
     */
    protected $componentName = '';

    /**
     * Component statistics
     * 
     * @var array
     */
    protected $stats = [];

    /**
     * Constructor
     * 
     * @param array $config Component configuration
     */
    public function __construct($config = [])
    {
        $this->config = $config;
        $this->initializeStats();
    }

    /**
     * Initialize component statistics
     * 
     * @return void
     */
    protected function initializeStats()
    {
        $this->stats = [
            'operations' => 0,
            'successes' => 0,
            'failures' => 0,
            'total_time_ms' => 0,
        ];
    }

    /**
     * Get component configuration
     * 
     * @param string $key Configuration key (dot notation supported)
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value
     */
    protected function getConfig($key, $default = null)
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
     * Log a message
     * 
     * @param string $message Log message
     * @param string $level Log level (info, warning, error)
     * @return void
     */
    protected function log($message, $level = 'info')
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$this->componentName}] [{$level}] {$message}";
        
        $logDir = __DIR__ . '/../../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/performance-optimizer.log';
        error_log($logMessage . PHP_EOL, 3, $logFile);
    }

    /**
     * Record operation statistics
     * 
     * @param bool $success Whether the operation succeeded
     * @param int $timeMs Operation time in milliseconds
     * @return void
     */
    protected function recordOperation($success = true, $timeMs = 0)
    {
        ++$this->stats['operations'];
        if ($success) {
            ++$this->stats['successes'];
        } else {
            ++$this->stats['failures'];
        }
        $this->stats['total_time_ms'] += $timeMs;
    }

    /**
     * Get component statistics
     * 
     * @return array Component statistics
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * Reset component statistics
     * 
     * @return void
     */
    public function resetStats()
    {
        $this->initializeStats();
    }

    /**
     * Check if component is enabled
     * 
     * @return bool True if component is enabled
     */
    public function isEnabled()
    {
        return $this->getConfig('enabled', true);
    }

    /**
     * Abstract method for component initialization
     * 
     * @return void
     */
    abstract public function initialize();
}
