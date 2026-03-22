<?php
/**
 * Bug Condition Exploration Test for Performance Page Functions
 * 
 * This test demonstrates that api() and showAdminToast() functions are undefined
 * when the performance page loads, causing ReferenceErrors in inline scripts.
 * 
 * **Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5**
 * 
 * Expected outcome on UNFIXED code: TEST FAILS
 * This failure proves the bug exists.
 */

// Simulate the page load sequence
class PerformancePageBugTest {
    private $functions_defined = [];
    private $errors = [];
    private $header_output = '';
    
    /**
     * Test 1: Verify api() function is defined before inline scripts execute
     */
    public function test_api_function_defined_before_inline_scripts() {
        // Simulate loading admin-header.php
        ob_start();
        require 'admin-header.php';
        $this->header_output = ob_get_clean();
        
        // Check if api() is defined in the JavaScript output before the main content area closes
        // The function should be in a <script> tag in the header
        if (strpos($this->header_output, 'function api(') === false && strpos($this->header_output, 'async function api(') === false) {
            $this->errors[] = "FAIL: api() function not defined in admin-header.php JavaScript";
            return false;
        }
        
        // Verify it's defined before the main content area (before inline scripts execute)
        $main_pos = strpos($this->header_output, '<main');
        $api_pos = strpos($this->header_output, 'function api(');
        if ($api_pos === false) {
            $api_pos = strpos($this->header_output, 'async function api(');
        }
        
        if ($api_pos === false || ($main_pos !== false && $api_pos > $main_pos)) {
            $this->errors[] = "FAIL: api() function defined after main content area";
            return false;
        }
        
        $this->functions_defined['api'] = true;
        return true;
    }
    
    /**
     * Test 2: Verify showAdminToast() function is defined before inline scripts execute
     */
    public function test_showAdminToast_function_defined_before_inline_scripts() {
        // Check if showAdminToast() is defined in the JavaScript output
        if (strpos($this->header_output, 'function showAdminToast(') === false) {
            $this->errors[] = "FAIL: showAdminToast() function not defined in admin-header.php JavaScript";
            return false;
        }
        
        // Verify it's defined before the main content area (before inline scripts execute)
        $main_pos = strpos($this->header_output, '<main');
        $toast_pos = strpos($this->header_output, 'function showAdminToast(');
        
        if ($toast_pos === false || ($main_pos !== false && $toast_pos > $main_pos)) {
            $this->errors[] = "FAIL: showAdminToast() function defined after main content area";
            return false;
        }
        
        $this->functions_defined['showAdminToast'] = true;
        return true;
    }
    
    /**
     * Test 3: Verify loadPerformanceData() can execute without ReferenceError
     * This simulates the inline script calling loadPerformanceData() on page load
     */
    public function test_loadPerformanceData_executes_without_error() {
        // The inline script calls loadPerformanceData() immediately
        // This function calls api() which must be defined
        
        // Check if api() is available for loadPerformanceData() to call
        if (!isset($this->functions_defined['api']) || !$this->functions_defined['api']) {
            $this->errors[] = "FAIL: api() not available when loadPerformanceData() tries to call it";
            return false;
        }
        
        return true;
    }
    
    /**
     * Test 4: Verify button click handlers can call api() without ReferenceError
     * This simulates clicking "Purge All Cache" button
     */
    public function test_button_click_handlers_can_call_api() {
        // Button handlers like purgeAllCache() call api()
        // api() must be defined before the handler is attached
        
        if (!isset($this->functions_defined['api']) || !$this->functions_defined['api']) {
            $this->errors[] = "FAIL: api() not available when button click handlers try to call it";
            return false;
        }
        
        return true;
    }
    
    /**
     * Test 5: Verify toggle handlers can call showAdminToast() without ReferenceError
     * This simulates toggling a feature checkbox
     */
    public function test_toggle_handlers_can_call_showAdminToast() {
        // Toggle handlers call showAdminToast()
        // showAdminToast() must be defined before the handler is attached
        
        if (!isset($this->functions_defined['showAdminToast']) || !$this->functions_defined['showAdminToast']) {
            $this->errors[] = "FAIL: showAdminToast() not available when toggle handlers try to call it";
            return false;
        }
        
        return true;
    }
    
    /**
     * Run all tests and report results
     */
    public function run_all_tests() {
        echo "=== Performance Page Functions Bug Condition Exploration Test ===\n\n";
        
        $tests = [
            'test_api_function_defined_before_inline_scripts' => 'Test 1: api() defined before inline scripts',
            'test_showAdminToast_function_defined_before_inline_scripts' => 'Test 2: showAdminToast() defined before inline scripts',
            'test_loadPerformanceData_executes_without_error' => 'Test 3: loadPerformanceData() executes without error',
            'test_button_click_handlers_can_call_api' => 'Test 4: Button handlers can call api()',
            'test_toggle_handlers_can_call_showAdminToast' => 'Test 5: Toggle handlers can call showAdminToast()',
        ];
        
        $passed = 0;
        $failed = 0;
        
        foreach ($tests as $method => $description) {
            echo "Running: $description\n";
            try {
                $result = $this->$method();
                if ($result) {
                    echo "  ✓ PASS\n";
                    $passed++;
                } else {
                    echo "  ✗ FAIL\n";
                    $failed++;
                }
            } catch (Exception $e) {
                echo "  ✗ ERROR: " . $e->getMessage() . "\n";
                $failed++;
                $this->errors[] = "Exception in $method: " . $e->getMessage();
            }
            echo "\n";
        }
        
        echo "=== Test Results ===\n";
        echo "Passed: $passed\n";
        echo "Failed: $failed\n";
        
        if (!empty($this->errors)) {
            echo "\n=== Errors/Counterexamples Found ===\n";
            foreach ($this->errors as $error) {
                echo "- $error\n";
            }
        }
        
        return $failed === 0;
    }
}

// Run the test
$test = new PerformancePageBugTest();
$all_passed = $test->run_all_tests();

// Exit with appropriate code
exit($all_passed ? 0 : 1);
?>
