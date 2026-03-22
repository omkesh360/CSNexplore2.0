# Implementation Plan

## Phase 1: Exploration & Preservation Testing

- [x] 1. Write bug condition exploration test
  - **Property 1: Bug Condition** - Functions Undefined on Performance Page Load
  - **CRITICAL**: This test MUST FAIL on unfixed code - failure confirms the bug exists
  - **DO NOT attempt to fix the test or the code when it fails**
  - **NOTE**: This test encodes the expected behavior - it will validate the fix when it passes after implementation
  - **GOAL**: Surface counterexamples that demonstrate the bug exists
  - **Scoped PBT Approach**: For deterministic bugs, scope the property to the concrete failing case(s) to ensure reproducibility
  - Test that when performance page loads, the `api()` function is defined and callable before inline scripts execute
  - Test that when performance page loads, the `showAdminToast()` function is defined and callable before inline scripts execute
  - Test that `loadPerformanceData()` can execute successfully without ReferenceError
  - Test that button click handlers can call `api()` without ReferenceError
  - Test that toggle handlers can call `showAdminToast()` without ReferenceError
  - Run test on UNFIXED code
  - **EXPECTED OUTCOME**: Test FAILS (this is correct - it proves the bug exists)
  - Document counterexamples found to understand root cause (e.g., "ReferenceError: api is not defined", "ReferenceError: showAdminToast is not defined")
  - Mark task complete when test is written, run, and failure is documented
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [ ] 2. Write preservation property tests (BEFORE implementing fix)
  - **Property 2: Preservation** - Other Admin Pages and Footer Functionality
  - **IMPORTANT**: Follow observation-first methodology
  - Observe behavior on UNFIXED code for non-performance admin pages (dashboard, blogs, bookings, users, gallery, listings, content)
  - Observe that `api()` function is available and works correctly on other admin pages
  - Observe that `showAdminToast()` function is available and works correctly on other admin pages
  - Observe that gallery picker modal loads and functions correctly
  - Observe that pending bookings count loads correctly
  - Observe that logout functionality works correctly
  - Write property-based tests capturing observed behavior patterns from Preservation Requirements
  - Property-based testing generates many test cases for stronger guarantees
  - Run tests on UNFIXED code
  - **EXPECTED OUTCOME**: Tests PASS (this confirms baseline behavior to preserve)
  - Mark task complete when tests are written, run, and passing on unfixed code
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

## Phase 2: Implementation

- [ ] 3. Fix for performance page functions availability

  - [x] 3.1 Move api() and showAdminToast() function definitions to admin-header.php
    - Extract `api()` function from admin/admin-footer.php
    - Extract `showAdminToast()` function from admin/admin-footer.php
    - Add both functions to admin/admin-header.php after the HTML structure setup but before the main content area closes
    - Ensure functions are defined only once (no duplication)
    - Verify the functions are placed in a location that executes before any inline scripts in admin/performance.php
    - _Bug_Condition: isBugCondition(input) where input.page == 'admin/performance.php' AND scriptExecutionTime < footerLoadTime_
    - _Expected_Behavior: expectedBehavior(result) - api() and showAdminToast() defined before inline scripts execute_
    - _Preservation: Existing footer functionality (gallery picker, pending count, logout) remains unchanged_
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

  - [x] 3.2 Remove api() and showAdminToast() function definitions from admin-footer.php
    - Remove the `api()` function definition from admin/admin-footer.php
    - Remove the `showAdminToast()` function definition from admin/admin-footer.php
    - Keep all other footer functionality intact (gallery picker, pending count, logout, sidebar toggle, user info population)
    - Verify no other code in footer depends on these functions being defined in the footer
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

  - [x] 3.3 Verify bug condition exploration test now passes
    - **Property 1: Expected Behavior** - Functions Available Before Use
    - **IMPORTANT**: Re-run the SAME test from task 1 - do NOT write a new test
    - The test from task 1 encodes the expected behavior
    - When this test passes, it confirms the expected behavior is satisfied
    - Run bug condition exploration test from step 1
    - Verify that `api()` is now defined before inline scripts execute
    - Verify that `showAdminToast()` is now defined before inline scripts execute
    - Verify that `loadPerformanceData()` executes successfully without ReferenceError
    - Verify that button click handlers can call `api()` without ReferenceError
    - Verify that toggle handlers can call `showAdminToast()` without ReferenceError
    - **EXPECTED OUTCOME**: Test PASSES (confirms bug is fixed)
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

  - [x] 3.4 Verify preservation tests still pass
    - **Property 2: Preservation** - Other Admin Pages and Footer Functionality
    - **IMPORTANT**: Re-run the SAME tests from task 2 - do NOT write new tests
    - Run preservation property tests from step 2
    - Verify that other admin pages (dashboard, blogs, bookings, users, gallery, listings, content) still have access to `api()` and `showAdminToast()`
    - Verify that gallery picker modal still loads and functions correctly
    - Verify that pending bookings count still loads correctly
    - Verify that logout functionality still works correctly
    - Verify that all other footer functionality remains unchanged
    - **EXPECTED OUTCOME**: Tests PASS (confirms no regressions)
    - Confirm all tests still pass after fix (no regressions)
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [x] 4. Checkpoint - Ensure all tests pass
  - Verify that the bug condition exploration test passes (functions are now available)
  - Verify that the preservation tests pass (other pages and footer functionality unchanged)
  - Verify that the performance page loads without errors
  - Verify that all performance page features work correctly (stats display, quick actions, feature toggles, configuration save)
  - Verify that other admin pages still work correctly
  - Verify that gallery picker still works correctly
  - Verify that logout still works correctly
  - Ensure all tests pass, ask the user if questions arise.
