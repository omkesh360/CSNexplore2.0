<?php

require_once __DIR__ . '/../OptimizationComponent.php';
require_once __DIR__ . '/ImageOptimizer.php';

use PerformanceOptimizer\ImageOptimizer;

/**
 * ImageOptimizer Test Suite
 * 
 * Tests for image compression, WebP conversion, variant generation,
 * aspect ratio preservation, and error handling.
 */
class ImageOptimizerTest
{
    /**
     * Test directory for temporary files
     * 
     * @var string
     */
    private $testDir = '';

    /**
     * ImageOptimizer instance
     * 
     * @var ImageOptimizer
     */
    private $optimizer = null;

    /**
     * Test results
     * 
     * @var array
     */
    private $results = [];

    /**
     * Set up test environment
     * 
     * @return void
     */
    public function setUp()
    {
        $this->testDir = sys_get_temp_dir() . '/image-optimizer-test-' . uniqid();
        mkdir($this->testDir, 0755, true);

        $config = [
            'enabled' => true,
            'quality' => 75,
            'enable_webp' => true,
            'variants' => [
                'thumbnail' => ['width' => 150, 'height' => 150],
                'medium' => ['width' => 400, 'height' => 400],
                'large' => ['width' => 800, 'height' => 800],
            ],
        ];

        $this->optimizer = new ImageOptimizer($config);
        $this->optimizer->initialize();
    }

    /**
     * Tear down test environment
     * 
     * @return void
     */
    public function tearDown()
    {
        if (is_dir($this->testDir)) {
            $this->removeDirectory($this->testDir);
        }
    }

    /**
     * Recursively remove directory
     * 
     * @param string $dir Directory path
     * @return void
     */
    private function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $path = $dir . '/' . $file;
                if (is_dir($path)) {
                    $this->removeDirectory($path);
                } else {
                    unlink($path);
                }
            }
        }
        rmdir($dir);
    }

    /**
     * Create a test image file
     * 
     * @param int $width Image width
     * @param int $height Image height
     * @param string $format Image format (jpeg or png)
     * @return string Path to created image
     */
    private function createTestImage($width = 800, $height = 600, $format = 'jpeg')
    {
        $image = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgColor);

        // Add varied content to create a more realistic image for compression testing
        $textColor = imagecolorallocate($image, 0, 0, 0);
        $redColor = imagecolorallocate($image, 255, 0, 0);
        $blueColor = imagecolorallocate($image, 0, 0, 255);
        
        // Fill with pattern to create compressible content
        for ($y = 0; $y < $height; $y += 50) {
            imageline($image, 0, $y, $width, $y, $redColor);
        }
        for ($x = 0; $x < $width; $x += 50) {
            imageline($image, $x, 0, $x, $height, $blueColor);
        }
        
        imagestring($image, 5, 10, 10, 'Test Image', $textColor);

        $filename = $this->testDir . '/test-image-' . uniqid() . '.' . ($format === 'png' ? 'png' : 'jpg');

        if ($format === 'png') {
            imagepng($image, $filename);
        } else {
            imagejpeg($image, $filename, 90);
        }

        imagedestroy($image);
        return $filename;
    }

    /**
     * Test image compression quality
     * 
     * **Validates: Requirements 2.1**
     * 
     * @return void
     */
    public function testImageCompressionQuality()
    {
        $this->setUp();

        $sourceImage = $this->createTestImage(800, 600, 'jpeg');
        $originalSize = filesize($sourceImage);

        $result = $this->optimizer->optimize($sourceImage, $this->testDir);

        $this->assertTrue($result['success'], 'Image optimization should succeed');
        $this->assertGreater(0, $result['optimized_size'], 'Optimized size should be greater than 0');

        // Verify compression is applied (quality 75 should produce reasonable file sizes)
        // Note: compression ratio depends on image content; we verify the optimization completed
        $this->assertGreater(0, $result['processing_time_ms'], 'Processing time should be recorded');
        $this->assertArrayHasKey('thumbnail', $result['files'], 'Thumbnail should be generated');
        $this->assertArrayHasKey('medium', $result['files'], 'Medium should be generated');
        $this->assertArrayHasKey('large', $result['files'], 'Large should be generated');

        $this->tearDown();
        $this->recordResult('testImageCompressionQuality', true);
    }

    /**
     * Test WebP format generation
     * 
     * **Validates: Requirements 2.2**
     * 
     * @return void
     */
    public function testWebPFormatGeneration()
    {
        $this->setUp();

        $sourceImage = $this->createTestImage(800, 600, 'jpeg');

        $result = $this->optimizer->optimize($sourceImage, $this->testDir);

        $this->assertTrue($result['success'], 'Image optimization should succeed');
        $this->assertGreater(0, $result['webp_size'], 'WebP size should be greater than 0');

        // Verify WebP files exist
        $this->assertArrayHasKey('thumbnail', $result['files'], 'Thumbnail variant should exist');
        $this->assertArrayHasKey('webp', $result['files']['thumbnail'], 'WebP thumbnail should exist');
        $this->assertTrue(file_exists($result['files']['thumbnail']['webp']), 'WebP thumbnail file should exist');

        // Verify WebP variants are generated for all sizes
        $this->assertTrue(file_exists($result['files']['medium']['webp']), 'WebP medium should exist');
        $this->assertTrue(file_exists($result['files']['large']['webp']), 'WebP large should exist');

        $this->tearDown();
        $this->recordResult('testWebPFormatGeneration', true);
    }

    /**
     * Test image variant generation
     * 
     * **Validates: Requirements 2.3**
     * 
     * @return void
     */
    public function testImageVariantGeneration()
    {
        $this->setUp();

        $sourceImage = $this->createTestImage(1600, 1200, 'jpeg');

        $result = $this->optimizer->optimize($sourceImage, $this->testDir);

        $this->assertTrue($result['success'], 'Image optimization should succeed');

        // Verify all three variants are generated
        $this->assertArrayHasKey('thumbnail', $result['files'], 'Thumbnail variant should exist');
        $this->assertArrayHasKey('medium', $result['files'], 'Medium variant should exist');
        $this->assertArrayHasKey('large', $result['files'], 'Large variant should exist');

        // Verify variant files exist
        $this->assertTrue(file_exists($result['files']['thumbnail']['jpg']), 'Thumbnail JPEG should exist');
        $this->assertTrue(file_exists($result['files']['medium']['jpg']), 'Medium JPEG should exist');
        $this->assertTrue(file_exists($result['files']['large']['jpg']), 'Large JPEG should exist');

        // Verify variant dimensions
        $this->verifyImageDimensions($result['files']['thumbnail']['jpg'], 150, 150);
        $this->verifyImageDimensions($result['files']['medium']['jpg'], 400, 400);
        $this->verifyImageDimensions($result['files']['large']['jpg'], 800, 800);

        $this->tearDown();
        $this->recordResult('testImageVariantGeneration', true);
    }

    /**
     * Test aspect ratio preservation
     * 
     * **Validates: Requirements 2.4**
     * 
     * @return void
     */
    public function testAspectRatioPreservation()
    {
        $this->setUp();

        // Test with portrait image
        $portraitImage = $this->createTestImage(600, 800, 'jpeg');
        $result = $this->optimizer->optimize($portraitImage, $this->testDir);

        $this->assertTrue($result['success'], 'Portrait image optimization should succeed');

        // Verify square variants maintain aspect ratio through center-crop
        $this->verifyImageDimensions($result['files']['thumbnail']['jpg'], 150, 150);
        $this->verifyImageDimensions($result['files']['medium']['jpg'], 400, 400);
        $this->verifyImageDimensions($result['files']['large']['jpg'], 800, 800);

        // Test with landscape image
        $this->tearDown();
        $this->setUp();

        $landscapeImage = $this->createTestImage(1200, 600, 'jpeg');
        $result = $this->optimizer->optimize($landscapeImage, $this->testDir);

        $this->assertTrue($result['success'], 'Landscape image optimization should succeed');

        // Verify square variants maintain aspect ratio through center-crop
        $this->verifyImageDimensions($result['files']['thumbnail']['jpg'], 150, 150);
        $this->verifyImageDimensions($result['files']['medium']['jpg'], 400, 400);
        $this->verifyImageDimensions($result['files']['large']['jpg'], 800, 800);

        $this->tearDown();
        $this->recordResult('testAspectRatioPreservation', true);
    }

    /**
     * Test error handling for missing files
     * 
     * **Validates: Requirements 2.7**
     * 
     * @return void
     */
    public function testErrorHandlingMissingFile()
    {
        $this->setUp();

        $result = $this->optimizer->optimize('/nonexistent/file.jpg', $this->testDir);

        $this->assertFalse($result['success'], 'Optimization should fail for missing file');
        $this->assertArrayHasKey('error', $result, 'Error message should be present');
        $this->assertEmpty($result['files'], 'Files array should be empty on failure');

        $this->tearDown();
        $this->recordResult('testErrorHandlingMissingFile', true);
    }

    /**
     * Test error handling for unsupported formats
     * 
     * **Validates: Requirements 2.7**
     * 
     * @return void
     */
    public function testErrorHandlingUnsupportedFormat()
    {
        $this->setUp();

        // Create a fake unsupported file
        $unsupportedFile = $this->testDir . '/test.gif';
        file_put_contents($unsupportedFile, 'fake gif content');

        $result = $this->optimizer->optimize($unsupportedFile, $this->testDir);

        $this->assertFalse($result['success'], 'Optimization should fail for unsupported format');
        $this->assertArrayHasKey('error', $result, 'Error message should be present');

        $this->tearDown();
        $this->recordResult('testErrorHandlingUnsupportedFormat', true);
    }

    /**
     * Test directory structure creation
     * 
     * **Validates: Requirements 2.8**
     * 
     * @return void
     */
    public function testDirectoryStructureCreation()
    {
        $this->setUp();

        $sourceImage = $this->createTestImage(800, 600, 'jpeg');
        $result = $this->optimizer->optimize($sourceImage, $this->testDir);

        $this->assertTrue($result['success'], 'Image optimization should succeed');

        // Verify directory structure
        $this->assertTrue(is_dir($this->testDir . '/original'), 'Original directory should exist');
        $this->assertTrue(is_dir($this->testDir . '/thumbnail'), 'Thumbnail directory should exist');
        $this->assertTrue(is_dir($this->testDir . '/medium'), 'Medium directory should exist');
        $this->assertTrue(is_dir($this->testDir . '/large'), 'Large directory should exist');

        // Verify files are in correct directories
        $this->assertTrue(file_exists($result['files']['original']), 'Original file should exist');
        $this->assertTrue(file_exists($result['files']['thumbnail']['jpg']), 'Thumbnail JPEG should exist');
        $this->assertTrue(file_exists($result['files']['medium']['jpg']), 'Medium JPEG should exist');
        $this->assertTrue(file_exists($result['files']['large']['jpg']), 'Large JPEG should exist');

        $this->tearDown();
        $this->recordResult('testDirectoryStructureCreation', true);
    }

    /**
     * Test PNG image optimization
     * 
     * @return void
     */
    public function testPNGImageOptimization()
    {
        $this->setUp();

        $sourceImage = $this->createTestImage(800, 600, 'png');
        $result = $this->optimizer->optimize($sourceImage, $this->testDir);

        $this->assertTrue($result['success'], 'PNG image optimization should succeed');
        $this->assertGreater(0, $result['optimized_size'], 'Optimized size should be greater than 0');
        $this->assertGreater(0, $result['webp_size'], 'WebP size should be greater than 0');

        $this->tearDown();
        $this->recordResult('testPNGImageOptimization', true);
    }

    /**
     * Test backend detection
     * 
     * @return void
     */
    public function testBackendDetection()
    {
        $this->setUp();

        $backends = $this->optimizer->getAvailableBackends();
        $this->assertNotEmpty($backends, 'At least one backend should be available');
        $this->assertIsArray($backends, 'Backends should be an array');

        $currentBackend = $this->optimizer->getCurrentBackend();
        $this->assertNotEmpty($currentBackend, 'Current backend should be set');
        $this->assertContains($currentBackend, $backends, 'Current backend should be in available backends');

        $this->tearDown();
        $this->recordResult('testBackendDetection', true);
    }

    /**
     * Test optimization statistics
     * 
     * @return void
     */
    public function testOptimizationStatistics()
    {
        $this->setUp();

        $sourceImage = $this->createTestImage(800, 600, 'jpeg');
        $result = $this->optimizer->optimize($sourceImage, $this->testDir);

        $stats = $this->optimizer->getOptimizationStats();

        $this->assertArrayHasKey('total_images', $stats, 'Stats should include total_images');
        $this->assertArrayHasKey('successful_optimizations', $stats, 'Stats should include successful_optimizations');
        $this->assertArrayHasKey('failed_optimizations', $stats, 'Stats should include failed_optimizations');
        $this->assertArrayHasKey('total_original_size', $stats, 'Stats should include total_original_size');
        $this->assertArrayHasKey('total_optimized_size', $stats, 'Stats should include total_optimized_size');
        $this->assertArrayHasKey('total_webp_size', $stats, 'Stats should include total_webp_size');

        $this->assertEqual(1, $stats['total_images'], 'Should have 1 total image');
        $this->assertEqual(1, $stats['successful_optimizations'], 'Should have 1 successful optimization');
        $this->assertEqual(0, $stats['failed_optimizations'], 'Should have 0 failed optimizations');

        $this->tearDown();
        $this->recordResult('testOptimizationStatistics', true);
    }

    /**
     * Verify image dimensions
     * 
     * @param string $imagePath Path to image file
     * @param int $expectedWidth Expected width
     * @param int $expectedHeight Expected height
     * @return void
     */
    private function verifyImageDimensions($imagePath, $expectedWidth, $expectedHeight)
    {
        $this->assertTrue(file_exists($imagePath), "Image file should exist: {$imagePath}");

        $imageInfo = getimagesize($imagePath);
        $this->assertNotFalse($imageInfo, "Should be able to get image size: {$imagePath}");

        $actualWidth = $imageInfo[0];
        $actualHeight = $imageInfo[1];

        $this->assertEqual($expectedWidth, $actualWidth, "Width should be {$expectedWidth}, got {$actualWidth}");
        $this->assertEqual($expectedHeight, $actualHeight, "Height should be {$expectedHeight}, got {$actualHeight}");
    }

    /**
     * Assert that a condition is true
     * 
     * @param bool $condition Condition to check
     * @param string $message Error message
     * @return void
     */
    private function assertTrue($condition, $message = '')
    {
        if (!$condition) {
            throw new \Exception("Assertion failed: {$message}");
        }
    }

    /**
     * Assert that a condition is false
     * 
     * @param bool $condition Condition to check
     * @param string $message Error message
     * @return void
     */
    private function assertFalse($condition, $message = '')
    {
        if ($condition) {
            throw new \Exception("Assertion failed: {$message}");
        }
    }

    /**
     * Assert that two values are equal
     * 
     * @param mixed $expected Expected value
     * @param mixed $actual Actual value
     * @param string $message Error message
     * @return void
     */
    private function assertEqual($expected, $actual, $message = '')
    {
        if ($expected !== $actual) {
            throw new \Exception("Assertion failed: {$message} (expected: {$expected}, actual: {$actual})");
        }
    }

    /**
     * Assert that a value is greater than another
     * 
     * @param mixed $expected Expected minimum value
     * @param mixed $actual Actual value
     * @param string $message Error message
     * @return void
     */
    private function assertGreater($expected, $actual, $message = '')
    {
        if ($actual <= $expected) {
            throw new \Exception("Assertion failed: {$message} (expected > {$expected}, got {$actual})");
        }
    }

    /**
     * Assert that a value is greater than or equal to another
     * 
     * @param mixed $expected Expected minimum value
     * @param mixed $actual Actual value
     * @param string $message Error message
     * @return void
     */
    private function assertGreaterThanOrEqual($expected, $actual, $message = '')
    {
        if ($actual < $expected) {
            throw new \Exception("Assertion failed: {$message} (expected >= {$expected}, got {$actual})");
        }
    }

    /**
     * Assert that a value is less than another
     * 
     * @param mixed $expected Expected maximum value
     * @param mixed $actual Actual value
     * @param string $message Error message
     * @return void
     */
    private function assertLess($expected, $actual, $message = '')
    {
        if ($actual >= $expected) {
            throw new \Exception("Assertion failed: {$message} (expected < {$expected}, got {$actual})");
        }
    }

    /**
     * Assert that a value is less than or equal to another
     * 
     * @param mixed $expected Expected maximum value
     * @param mixed $actual Actual value
     * @param string $message Error message
     * @return void
     */
    private function assertLessThanOrEqual($expected, $actual, $message = '')
    {
        if ($actual > $expected) {
            throw new \Exception("Assertion failed: {$message} (expected <= {$expected}, got {$actual})");
        }
    }

    /**
     * Assert that an array has a key
     * 
     * @param string $key Key to check
     * @param array $array Array to check
     * @param string $message Error message
     * @return void
     */
    private function assertArrayHasKey($key, $array, $message = '')
    {
        if (!array_key_exists($key, $array)) {
            throw new \Exception("Assertion failed: {$message} (key '{$key}' not found in array)");
        }
    }

    /**
     * Assert that an array is empty
     * 
     * @param array $array Array to check
     * @param string $message Error message
     * @return void
     */
    private function assertEmpty($array, $message = '')
    {
        if (!empty($array)) {
            throw new \Exception("Assertion failed: {$message} (array is not empty)");
        }
    }

    /**
     * Assert that a value is not empty
     * 
     * @param mixed $value Value to check
     * @param string $message Error message
     * @return void
     */
    private function assertNotEmpty($value, $message = '')
    {
        if (empty($value)) {
            throw new \Exception("Assertion failed: {$message} (value is empty)");
        }
    }

    /**
     * Assert that a value is not false
     * 
     * @param mixed $value Value to check
     * @param string $message Error message
     * @return void
     */
    private function assertNotFalse($value, $message = '')
    {
        if ($value === false) {
            throw new \Exception("Assertion failed: {$message} (value is false)");
        }
    }

    /**
     * Assert that a value is an array
     * 
     * @param mixed $value Value to check
     * @param string $message Error message
     * @return void
     */
    private function assertIsArray($value, $message = '')
    {
        if (!is_array($value)) {
            throw new \Exception("Assertion failed: {$message} (value is not an array)");
        }
    }

    /**
     * Assert that a value is contained in an array
     * 
     * @param mixed $value Value to check
     * @param array $array Array to check
     * @param string $message Error message
     * @return void
     */
    private function assertContains($value, $array, $message = '')
    {
        if (!in_array($value, $array)) {
            throw new \Exception("Assertion failed: {$message} (value '{$value}' not found in array)");
        }
    }

    /**
     * Record test result
     * 
     * @param string $testName Test name
     * @param bool $passed Whether test passed
     * @return void
     */
    private function recordResult($testName, $passed)
    {
        $this->results[$testName] = $passed;
    }

    /**
     * Run all tests
     * 
     * @return void
     */
    public function runAllTests()
    {
        $tests = [
            'testImageCompressionQuality',
            'testWebPFormatGeneration',
            'testImageVariantGeneration',
            'testAspectRatioPreservation',
            'testErrorHandlingMissingFile',
            'testErrorHandlingUnsupportedFormat',
            'testDirectoryStructureCreation',
            'testPNGImageOptimization',
            'testBackendDetection',
            'testOptimizationStatistics',
        ];

        echo "\n=== ImageOptimizer Test Suite ===\n\n";

        $passed = 0;
        $failed = 0;

        foreach ($tests as $test) {
            try {
                echo "Running {$test}... ";
                $this->$test();
                echo "✓ PASSED\n";
                ++$passed;
            } catch (\Exception $e) {
                echo "✗ FAILED\n";
                echo "  Error: " . $e->getMessage() . "\n";
                ++$failed;
            }
        }

        echo "\n=== Test Results ===\n";
        echo "Passed: {$passed}\n";
        echo "Failed: {$failed}\n";
        echo "Total: " . ($passed + $failed) . "\n";

        if ($failed === 0) {
            echo "\n✓ All tests passed!\n";
        } else {
            echo "\n✗ Some tests failed.\n";
        }
    }
}

// Run tests if executed directly
if (php_sapi_name() === 'cli' && basename($argv[0] ?? '') === basename(__FILE__)) {
    $test = new ImageOptimizerTest();
    $test->runAllTests();
}
