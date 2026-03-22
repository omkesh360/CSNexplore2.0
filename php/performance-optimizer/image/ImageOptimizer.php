<?php

namespace PerformanceOptimizer;

/**
 * ImageOptimizer Class
 * 
 * Handles image optimization including compression, WebP conversion,
 * and generation of multiple size variants with aspect ratio preservation.
 * Supports both GD and ImageMagick backends with automatic fallback.
 */
class ImageOptimizer extends OptimizationComponent
{
    /**
     * Available image backends
     * 
     * @var array
     */
    private $availableBackends = [];

    /**
     * Current backend being used
     * 
     * @var string
     */
    private $currentBackend = '';

    /**
     * Image optimization statistics
     * 
     * @var array
     */
    private $optimizationStats = [];

    /**
     * Constructor
     * 
     * @param array $config Component configuration
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->componentName = 'ImageOptimizer';
        $this->initializeOptimizationStats();
    }

    /**
     * Initialize optimization statistics
     * 
     * @return void
     */
    private function initializeOptimizationStats()
    {
        $this->optimizationStats = [
            'total_images' => 0,
            'successful_optimizations' => 0,
            'failed_optimizations' => 0,
            'total_original_size' => 0,
            'total_optimized_size' => 0,
            'total_webp_size' => 0,
        ];
    }

    /**
     * Initialize the component and detect available backends
     * 
     * @return void
     */
    public function initialize()
    {
        $this->detectBackends();
        
        if (empty($this->availableBackends)) {
            $this->log('No image processing backends available (GD or ImageMagick required)', 'warning');
        } else {
            $this->log('Available backends: ' . implode(', ', $this->availableBackends), 'info');
            $this->currentBackend = $this->availableBackends[0];
            $this->log('Using backend: ' . $this->currentBackend, 'info');
        }
    }

    /**
     * Detect available image processing backends
     * 
     * @return void
     */
    private function detectBackends()
    {
        if (extension_loaded('gd')) {
            $this->availableBackends[] = 'gd';
        }

        if (extension_loaded('imagick')) {
            $this->availableBackends[] = 'imagick';
        }
    }

    /**
     * Optimize an image file
     * 
     * Compresses the image to 70-80% quality, converts to WebP,
     * and generates size variants. Returns array with paths to all generated files.
     * 
     * @param string $sourcePath Path to source image file
     * @param string $destinationDir Base destination directory
     * @return array Array with optimization results and file paths
     */
    public function optimize($sourcePath, $destinationDir)
    {
        $startTime = microtime(true);
        ++$this->optimizationStats['total_images'];

        if (!file_exists($sourcePath)) {
            $this->log("Source file not found: {$sourcePath}", 'error');
            $this->recordOperation(false, 0);
            return [
                'success' => false,
                'error' => 'Source file not found',
                'files' => [],
            ];
        }

        if (empty($this->availableBackends)) {
            $this->log("No image processing backends available for: {$sourcePath}", 'error');
            $this->recordOperation(false, 0);
            return [
                'success' => false,
                'error' => 'No image processing backends available',
                'files' => [],
            ];
        }

        try {
            $filename = basename($sourcePath);
            $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
            $originalExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($originalExt, ['jpg', 'jpeg', 'png'])) {
                $this->log("Unsupported image format: {$originalExt}", 'warning');
                $this->recordOperation(false, 0);
                return [
                    'success' => false,
                    'error' => 'Unsupported image format',
                    'files' => [],
                ];
            }

            $files = [];
            $originalSize = filesize($sourcePath);
            $this->optimizationStats['total_original_size'] += $originalSize;

            // Store original image
            $originalDir = $destinationDir . '/original';
            $this->ensureDirectory($originalDir);
            $originalPath = $originalDir . '/' . $filename;
            copy($sourcePath, $originalPath);
            $files['original'] = $originalPath;

            // Generate compressed variants
            $quality = $this->getConfig('quality', 75);
            $variants = $this->getConfig('variants', []);

            foreach ($variants as $variantName => $dimensions) {
                $variantFiles = $this->generateVariant(
                    $sourcePath,
                    $destinationDir,
                    $filenameWithoutExt,
                    $variantName,
                    $dimensions['width'],
                    $dimensions['height'],
                    $quality
                );

                if ($variantFiles) {
                    $files[$variantName] = $variantFiles;
                }
            }

            // Calculate optimization stats
            $optimizedSize = 0;
            $webpSize = 0;
            foreach ($files as $key => $filePaths) {
                if (is_array($filePaths)) {
                    if (isset($filePaths['jpg'])) {
                        $optimizedSize += filesize($filePaths['jpg']);
                    }
                    if (isset($filePaths['webp'])) {
                        $webpSize += filesize($filePaths['webp']);
                    }
                } elseif ($key === 'original') {
                    // Original is already counted
                }
            }

            $this->optimizationStats['total_optimized_size'] += $optimizedSize;
            $this->optimizationStats['total_webp_size'] += $webpSize;
            ++$this->optimizationStats['successful_optimizations'];

            $elapsedTime = (microtime(true) - $startTime) * 1000;
            $this->recordOperation(true, (int)$elapsedTime);

            $this->log(
                "Successfully optimized: {$filename} " .
                "(Original: " . $this->formatBytes($originalSize) . ", " .
                "Optimized: " . $this->formatBytes($optimizedSize) . ", " .
                "WebP: " . $this->formatBytes($webpSize) . ")",
                'info'
            );

            return [
                'success' => true,
                'files' => $files,
                'original_size' => $originalSize,
                'optimized_size' => $optimizedSize,
                'webp_size' => $webpSize,
                'processing_time_ms' => (int)$elapsedTime,
            ];
        } catch (\Exception $e) {
            ++$this->optimizationStats['failed_optimizations'];
            $this->log("Error optimizing image: " . $e->getMessage(), 'error');
            $this->recordOperation(false, 0);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'files' => [],
            ];
        }
    }

    /**
     * Generate a single image variant
     * 
     * @param string $sourcePath Path to source image
     * @param string $destinationDir Base destination directory
     * @param string $filenameWithoutExt Filename without extension
     * @param string $variantName Name of variant (thumbnail, medium, large)
     * @param int $width Target width
     * @param int $height Target height
     * @param int $quality Compression quality (1-100)
     * @return array|null Array with jpg and webp paths, or null on failure
     */
    private function generateVariant(
        $sourcePath,
        $destinationDir,
        $filenameWithoutExt,
        $variantName,
        $width,
        $height,
        $quality
    ) {
        try {
            $variantDir = $destinationDir . '/' . $variantName;
            $this->ensureDirectory($variantDir);

            // Generate JPEG variant
            $jpgPath = $variantDir . '/' . $filenameWithoutExt . '.jpg';
            $this->resizeAndCompress($sourcePath, $jpgPath, $width, $height, $quality, 'jpeg');

            // Generate WebP variant
            $webpPath = $variantDir . '/' . $filenameWithoutExt . '.webp';
            $this->resizeAndCompress($sourcePath, $webpPath, $width, $height, $quality, 'webp');

            return [
                'jpg' => $jpgPath,
                'webp' => $webpPath,
            ];
        } catch (\Exception $e) {
            $this->log("Error generating {$variantName} variant: " . $e->getMessage(), 'error');
            return null;
        }
    }

    /**
     * Resize and compress an image
     * 
     * Maintains aspect ratio with center-crop for square formats.
     * 
     * @param string $sourcePath Path to source image
     * @param string $destinationPath Path to save resized image
     * @param int $width Target width
     * @param int $height Target height
     * @param int $quality Compression quality (1-100)
     * @param string $format Output format (jpeg or webp)
     * @return void
     * @throws \Exception
     */
    private function resizeAndCompress(
        $sourcePath,
        $destinationPath,
        $width,
        $height,
        $quality,
        $format
    ) {
        if ($this->currentBackend === 'gd') {
            $this->resizeWithGD($sourcePath, $destinationPath, $width, $height, $quality, $format);
        } elseif ($this->currentBackend === 'imagick') {
            $this->resizeWithImageMagick($sourcePath, $destinationPath, $width, $height, $quality, $format);
        } else {
            throw new \Exception('No available image processing backend');
        }
    }

    /**
     * Resize and compress image using GD library
     * 
     * @param string $sourcePath Path to source image
     * @param string $destinationPath Path to save resized image
     * @param int $width Target width
     * @param int $height Target height
     * @param int $quality Compression quality (1-100)
     * @param string $format Output format (jpeg or webp)
     * @return void
     * @throws \Exception
     */
    private function resizeWithGD(
        $sourcePath,
        $destinationPath,
        $width,
        $height,
        $quality,
        $format
    ) {
        $sourceImage = $this->loadImageWithGD($sourcePath);
        if (!$sourceImage) {
            throw new \Exception('Failed to load image with GD');
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        // Calculate center-crop dimensions
        $cropDimensions = $this->calculateCenterCrop($sourceWidth, $sourceHeight, $width, $height);

        // Create cropped image
        $croppedImage = imagecreatetruecolor($cropDimensions['crop_width'], $cropDimensions['crop_height']);
        imagecopy(
            $croppedImage,
            $sourceImage,
            0,
            0,
            $cropDimensions['src_x'],
            $cropDimensions['src_y'],
            $cropDimensions['crop_width'],
            $cropDimensions['crop_height']
        );

        // Resize to target dimensions
        $resizedImage = imagecreatetruecolor($width, $height);
        imagecopyresampled(
            $resizedImage,
            $croppedImage,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $cropDimensions['crop_width'],
            $cropDimensions['crop_height']
        );

        // Save image in requested format
        if ($format === 'webp') {
            if (!imagewebp($resizedImage, $destinationPath, $quality)) {
                throw new \Exception('Failed to save WebP image');
            }
        } else {
            if (!imagejpeg($resizedImage, $destinationPath, $quality)) {
                throw new \Exception('Failed to save JPEG image');
            }
        }

        // Note: imagedestroy() is deprecated in PHP 8.0+
        // GD image resources are automatically destroyed when out of scope
    }

    /**
     * Resize and compress image using ImageMagick
     * 
     * @param string $sourcePath Path to source image
     * @param string $destinationPath Path to save resized image
     * @param int $width Target width
     * @param int $height Target height
     * @param int $quality Compression quality (1-100)
     * @param string $format Output format (jpeg or webp)
     * @return void
     * @throws \Exception
     */
    private function resizeWithImageMagick(
        $sourcePath,
        $destinationPath,
        $width,
        $height,
        $quality,
        $format
    ) {
        try {
            $image = new \Imagick($sourcePath);

            // Get original dimensions
            $sourceWidth = $image->getImageWidth();
            $sourceHeight = $image->getImageHeight();

            // Calculate center-crop dimensions
            $cropDimensions = $this->calculateCenterCrop($sourceWidth, $sourceHeight, $width, $height);

            // Crop to center
            $image->cropImage(
                $cropDimensions['crop_width'],
                $cropDimensions['crop_height'],
                $cropDimensions['src_x'],
                $cropDimensions['src_y']
            );

            // Resize to target dimensions
            $image->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1);

            // Set compression quality
            $image->setImageCompressionQuality($quality);

            // Set format and save
            if ($format === 'webp') {
                $image->setImageFormat('webp');
            } else {
                $image->setImageFormat('jpeg');
            }

            $image->writeImage($destinationPath);
            $image->destroy();
        } catch (\Exception $e) {
            throw new \Exception('ImageMagick error: ' . $e->getMessage());
        }
    }

    /**
     * Load image using GD library
     * 
     * @param string $imagePath Path to image file
     * @return resource|false Image resource or false on failure
     */
    private function loadImageWithGD($imagePath)
    {
        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        if ($ext === 'png') {
            return imagecreatefrompng($imagePath);
        } elseif (in_array($ext, ['jpg', 'jpeg'])) {
            return imagecreatefromjpeg($imagePath);
        }

        return false;
    }

    /**
     * Calculate center-crop dimensions
     * 
     * Maintains aspect ratio by cropping from center.
     * 
     * @param int $sourceWidth Original image width
     * @param int $sourceHeight Original image height
     * @param int $targetWidth Target width
     * @param int $targetHeight Target height
     * @return array Array with crop dimensions and source coordinates
     */
    private function calculateCenterCrop($sourceWidth, $sourceHeight, $targetWidth, $targetHeight)
    {
        $sourceAspect = $sourceWidth / $sourceHeight;
        $targetAspect = $targetWidth / $targetHeight;

        if ($sourceAspect > $targetAspect) {
            // Source is wider, crop width
            $cropWidth = (int)($sourceHeight * $targetAspect);
            $cropHeight = $sourceHeight;
            $srcX = (int)(($sourceWidth - $cropWidth) / 2);
            $srcY = 0;
        } else {
            // Source is taller, crop height
            $cropWidth = $sourceWidth;
            $cropHeight = (int)($sourceWidth / $targetAspect);
            $srcX = 0;
            $srcY = (int)(($sourceHeight - $cropHeight) / 2);
        }

        return [
            'crop_width' => $cropWidth,
            'crop_height' => $cropHeight,
            'src_x' => $srcX,
            'src_y' => $srcY,
        ];
    }

    /**
     * Ensure directory exists and is writable
     * 
     * @param string $directory Directory path
     * @return void
     * @throws \Exception
     */
    private function ensureDirectory($directory)
    {
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                throw new \Exception("Failed to create directory: {$directory}");
            }
        }

        if (!is_writable($directory)) {
            throw new \Exception("Directory is not writable: {$directory}");
        }
    }

    /**
     * Format bytes to human-readable format
     * 
     * @param int $bytes Number of bytes
     * @return string Formatted byte string
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get optimization statistics
     * 
     * @return array Optimization statistics
     */
    public function getOptimizationStats()
    {
        return array_merge($this->stats, $this->optimizationStats);
    }

    /**
     * Get current backend
     * 
     * @return string Current backend name
     */
    public function getCurrentBackend()
    {
        return $this->currentBackend;
    }

    /**
     * Get available backends
     * 
     * @return array List of available backends
     */
    public function getAvailableBackends()
    {
        return $this->availableBackends;
    }
}
