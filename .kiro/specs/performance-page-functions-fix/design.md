# Performance Page Functions Bugfix Design

## Overview

The performance page in the admin panel has a critical JavaScript execution order issue. The file `admin/performance.php` contains inline scripts that call `api()` and `showAdminToast()` functions immediately on page load (via `loadPerformanceData()` and button click handlers), but these functions are not defined until `admin/admin-footer.php` is loaded at the end of the page. This causes ReferenceErrors that prevent all performance management features from functioning. The fix ensures these functions are available before any inline scripts attempt to use them by moving their definitions to an earlier point in the page load sequence.

## Glossary

- **Bug_Condition (C)**: The condition that triggers the bug - when inline scripts in `admin/performance.php` execute before `admin/admin-footer.php` loads the `api()` and `showAdminToast()` functions
- **Property (P)**: The desired behavior when the page loads - all functions should be available before any inline scripts execute
- **Preservation**: Existing functionality on other admin pages and the footer's other features (gallery picker, pending count, logout) that must remain unchanged
- **api()**: The async function defined in `admin/admin-footer.php` that handles authenticated API requests with error handling
- **showAdminToast()**: The function defined in `admin/admin-footer.php` that displays temporary notification messages to the admin
- **loadPerformanceData()**: The function in the inline script that calls `api()` on page load to fetch performance statistics
- **Page Load Sequence**: The order in which HTML elements and scripts are parsed and executed by the browser

## Bug Details

### Bug Condition

The bug manifests when the performance page loads and inline scripts attempt to call `api()` or `showAdminToast()` before these functions are defined. The `admin/performance.php` file includes inline JavaScript in the `$extra_js` variable that:
1. Calls `loadPerformanceData()` immediately on page load
2. Attaches click handlers to buttons that call `api()` 
3. Attaches change handlers to toggles that call `showAdminToast()`

However, these functions are defined in `admin/admin-footer.php`, which is included at the very end of the page via `require 'admin-footer.php'`. This creates a race condition where the inline scripts execute before the footer is loaded.

**Formal Specification:**
```
FUNCTION isBugCondition(input)
  INPUT: input of type PageLoadEvent
  OUTPUT: boolean
  
  RETURN input.page == 'admin/performance.php'
         AND input.scriptExecutionTime < input.footerLoadTime
         AND (functionCalled == 'api' OR functionCalled == 'showAdminToast')
         AND functionDefined == false
END FUNCTION
```

### Examples

- **Example 1 - Page Load Failure**: User navigates to `/admin/performance.php`. The page renders, the inline script calls `loadPerformanceData()` which calls `api()`, but `api()` is not yet defined. Result: ReferenceError "api is not defined", stats grid shows "—" placeholders, page is non-functional.

- **Example 2 - Button Click Failure**: User navigates to performance page (assuming it somehow loaded). User clicks "Purge All Cache" button. The onclick handler calls `purgeAllCache()` which calls `api()`, but if the footer hasn't loaded yet, ReferenceError occurs. Result: No cache purge, no toast notification.

- **Example 3 - Toggle Failure**: User navigates to performance page. User clicks a feature toggle checkbox. The change handler calls `toggleFeature()` which calls `showAdminToast()`, but if the footer hasn't loaded, ReferenceError occurs. Result: No toast notification, feature state may not update.

- **Example 4 - Configuration Save Failure**: User navigates to performance page. User modifies cache TTL and clicks "Save Configuration". The onclick handler calls `saveConfiguration()` which calls `api()`, but if the footer hasn't loaded, ReferenceError occurs. Result: Configuration not saved, no feedback to user.

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- The `api()` function must continue to work exactly as before on all other admin pages (dashboard, blogs, bookings, users, etc.)
- The `showAdminToast()` function must continue to work exactly as before on all other admin pages
- The gallery picker modal and all its functionality must continue to work exactly as before
- The pending bookings count loading must continue to work exactly as before
- The logout functionality must continue to work exactly as before
- All other footer functionality (sidebar toggle, user info population, etc.) must continue to work exactly as before
- The performance page HTML structure and styling must remain completely unchanged
- All performance page UI elements must display correctly (stats grid, quick actions, feature toggles, configuration inputs, metrics table, slow queries table)

**Scope:**
All inputs that do NOT involve the performance page loading should be completely unaffected by this fix. This includes:
- Loading other admin pages (dashboard, blogs, bookings, users, gallery, listings, content)
- Using the gallery picker on any admin page
- Logging out from any admin page
- Using the API on any other admin page
- Displaying toasts on any other admin page

## Hypothesized Root Cause

Based on the bug description, the most likely issues are:

1. **Script Execution Order**: The inline scripts in `admin/performance.php` are executed before `admin/admin-footer.php` is loaded. In HTML, scripts are executed in the order they appear in the document. Since the footer is included at the very end via `require 'admin-footer.php'`, any inline scripts that execute before that point will not have access to functions defined in the footer.

2. **Immediate Function Calls**: The `loadPerformanceData()` function is called immediately on page load (not deferred), and it calls `api()` synchronously. This happens before the footer script has a chance to define `api()`.

3. **Event Handler Attachment**: Button click handlers and toggle change handlers are attached to DOM elements in the inline script, and these handlers reference `api()` and `showAdminToast()`. While the handlers themselves don't execute until clicked, the functions they reference must be defined before the handlers are attached (or at least before they're called).

4. **No Dependency Management**: There is no mechanism to ensure that the footer functions are loaded before the performance page scripts attempt to use them. The code relies on implicit ordering assumptions that are violated by the current structure.

## Correctness Properties

Property 1: Bug Condition - Functions Available Before Use

_For any_ page load of `admin/performance.php`, the fixed code SHALL ensure that the `api()` and `showAdminToast()` functions are defined and available before any inline scripts attempt to call them, allowing `loadPerformanceData()` to execute successfully on page load and all button/toggle handlers to function correctly when triggered.

**Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5**

Property 2: Preservation - Other Admin Pages and Footer Functionality

_For any_ page load of other admin pages (dashboard, blogs, bookings, users, gallery, listings, content) or any use of footer functionality (gallery picker, pending count, logout, API calls, toasts), the fixed code SHALL produce exactly the same behavior as the original code, preserving all existing functionality for non-performance pages and ensuring the footer works identically on all pages.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5**

## Fix Implementation

### Changes Required

Assuming our root cause analysis is correct, the fix involves moving the function definitions to be available before the inline scripts execute. There are several possible approaches:

**Approach 1: Move Functions to Header (Recommended)**
Move the `api()` and `showAdminToast()` function definitions from `admin/admin-footer.php` to `admin/admin-header.php` or a separate included file that loads before the inline scripts. This ensures the functions are defined before any inline scripts execute.

**File**: `admin/admin-header.php`

**Specific Changes**:
1. **Extract Function Definitions**: Copy the `api()` and `showAdminToast()` function definitions from `admin/admin-footer.php` to a new location in `admin/admin-header.php` (after the HTML structure is set up but before any inline scripts)

2. **Maintain Footer Functionality**: Keep all other footer functionality (gallery picker, pending count, logout, etc.) in `admin/admin-footer.php` unchanged

3. **Remove Duplication**: Ensure the functions are not duplicated - they should only be defined once

4. **Preserve Script Order**: The gallery picker and other footer-specific scripts should remain in `admin/admin-footer.php` since they don't need to be available before the performance page inline scripts

**File**: `admin/admin-footer.php`

**Specific Changes**:
1. **Remove Function Definitions**: Remove the `api()` and `showAdminToast()` function definitions from the footer script block (they will now be in the header)

2. **Keep Other Functionality**: Keep all other footer functionality intact (gallery picker, pending count, logout, sidebar toggle, user info population)

### Implementation Details

The `api()` function should be moved to the header because:
- It's a utility function used by multiple pages
- It needs to be available before any inline scripts execute
- It doesn't depend on any footer-specific DOM elements

The `showAdminToast()` function should be moved to the header because:
- It's a utility function used by multiple pages
- It needs to be available before any inline scripts execute
- It only depends on `document.body` which is always available

The gallery picker and other footer-specific functionality should remain in the footer because:
- They depend on specific footer DOM elements (gallery-picker-modal, etc.)
- They don't need to be available before inline scripts execute
- They are only used after the page is fully loaded

## Testing Strategy

### Validation Approach

The testing strategy follows a two-phase approach: first, surface counterexamples that demonstrate the bug on unfixed code, then verify the fix works correctly and preserves existing behavior.

### Exploratory Bug Condition Checking

**Goal**: Surface counterexamples that demonstrate the bug BEFORE implementing the fix. Confirm or refute the root cause analysis. If we refute, we will need to re-hypothesize.

**Test Plan**: Write tests that simulate page load and verify that `api()` and `showAdminToast()` are called successfully. Run these tests on the UNFIXED code to observe failures and understand the root cause.

**Test Cases**:
1. **Page Load Test**: Load the performance page and verify that `loadPerformanceData()` executes without ReferenceError (will fail on unfixed code)
2. **Button Click Test**: Load the performance page and simulate clicking "Purge All Cache" button, verify `api()` is called (will fail on unfixed code)
3. **Toggle Test**: Load the performance page and simulate toggling a feature checkbox, verify `showAdminToast()` is called (will fail on unfixed code)
4. **Configuration Save Test**: Load the performance page and simulate clicking "Save Configuration", verify `api()` is called (will fail on unfixed code)

**Expected Counterexamples**:
- ReferenceError: api is not defined
- ReferenceError: showAdminToast is not defined
- Possible causes: functions defined in footer which loads after inline scripts, script execution order issue

### Fix Checking

**Goal**: Verify that for all inputs where the bug condition holds (performance page loads), the fixed function produces the expected behavior.

**Pseudocode:**
```
FOR ALL pageLoad WHERE pageLoad.page == 'admin/performance.php' DO
  result := loadPage(pageLoad)
  ASSERT result.api_defined == true
  ASSERT result.showAdminToast_defined == true
  ASSERT result.loadPerformanceData_executed == true
  ASSERT result.no_reference_errors == true
END FOR
```

### Preservation Checking

**Goal**: Verify that for all inputs where the bug condition does NOT hold (other admin pages load), the fixed function produces the same result as the original function.

**Pseudocode:**
```
FOR ALL pageLoad WHERE pageLoad.page != 'admin/performance.php' DO
  ASSERT originalBehavior(pageLoad) = fixedBehavior(pageLoad)
END FOR
```

**Testing Approach**: Property-based testing is recommended for preservation checking because:
- It generates many test cases automatically across different admin pages
- It catches edge cases that manual unit tests might miss
- It provides strong guarantees that behavior is unchanged for all non-performance pages

**Test Plan**: Observe behavior on UNFIXED code first for other admin pages, then write property-based tests capturing that behavior.

**Test Cases**:
1. **Dashboard Page Preservation**: Load dashboard page, verify `api()` and `showAdminToast()` work correctly
2. **Blogs Page Preservation**: Load blogs page, verify `api()` and `showAdminToast()` work correctly
3. **Bookings Page Preservation**: Load bookings page, verify `api()` and `showAdminToast()` work correctly
4. **Gallery Picker Preservation**: Open gallery picker on any page, verify it works correctly
5. **Pending Count Preservation**: Verify pending bookings count loads correctly on all pages
6. **Logout Preservation**: Verify logout functionality works correctly on all pages

### Unit Tests

- Test that `api()` function is defined before performance page inline scripts execute
- Test that `showAdminToast()` function is defined before performance page inline scripts execute
- Test that `loadPerformanceData()` executes successfully on page load
- Test that button click handlers can call `api()` without errors
- Test that toggle change handlers can call `showAdminToast()` without errors
- Test that configuration save can call `api()` without errors

### Property-Based Tests

- Generate random admin page loads and verify that `api()` and `showAdminToast()` are always available when needed
- Generate random button clicks on performance page and verify they execute without ReferenceErrors
- Generate random toggle interactions on performance page and verify they execute without ReferenceErrors
- Test that all other admin pages continue to have access to `api()` and `showAdminToast()` after the fix

### Integration Tests

- Test full performance page flow: load page, verify stats display, click buttons, toggle features, save configuration
- Test switching between performance page and other admin pages, verify functions remain available
- Test that gallery picker works correctly after performance page is loaded
- Test that pending count updates correctly after performance page is loaded
- Test that logout works correctly from performance page
