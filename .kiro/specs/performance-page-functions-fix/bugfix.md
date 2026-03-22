# Performance Page Functions Bugfix

## Introduction

The performance page in the admin panel has a critical JavaScript execution order issue. Functions defined in `admin/admin-footer.php` (`api()` and `showAdminToast()`) are being called by inline scripts in `admin/performance.php` before the footer is loaded. This causes all performance management features to fail immediately on page load, preventing administrators from viewing performance stats, managing cache, toggling features, or saving configuration settings.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN the performance page loads and `loadPerformanceData()` is called on page load THEN the system throws a ReferenceError because `api()` function is not defined

1.2 WHEN a user clicks a quick action button (Purge All Cache, Clear Page Cache, etc.) THEN the system throws a ReferenceError because the button's onclick handler calls `api()` which is not defined

1.3 WHEN a user clicks the "Save Feature Settings" button THEN the system throws a ReferenceError because `saveFeatureSettings()` calls `api()` which is not defined

1.4 WHEN a user clicks the "Save Configuration" button THEN the system throws a ReferenceError because `saveConfiguration()` calls `api()` which is not defined

1.5 WHEN a user interacts with feature toggle checkboxes THEN the system throws a ReferenceError because toggle handlers call `showAdminToast()` which is not defined

### Expected Behavior (Correct)

2.1 WHEN the performance page loads and `loadPerformanceData()` is called on page load THEN the system SHALL successfully fetch performance data via the `api()` function and populate the stats grid with cache hit rate, page load time, cache size, and images optimized

2.2 WHEN a user clicks a quick action button (Purge All Cache, Clear Page Cache, etc.) THEN the system SHALL successfully execute the action via the `api()` function and display a success toast notification

2.3 WHEN a user clicks the "Save Feature Settings" button THEN the system SHALL successfully save all feature toggle states via the `api()` function and display a success toast notification

2.4 WHEN a user clicks the "Save Configuration" button THEN the system SHALL successfully save all configuration values via the `api()` function and display a success toast notification

2.5 WHEN a user interacts with feature toggle checkboxes THEN the system SHALL successfully toggle features and display appropriate toast notifications via the `showAdminToast()` function

### Unchanged Behavior (Regression Prevention)

3.1 WHEN the admin footer is loaded on other admin pages (dashboard, blogs, bookings, etc.) THEN the system SHALL CONTINUE TO define and make available the `api()` function for use by those pages

3.2 WHEN the admin footer is loaded on other admin pages THEN the system SHALL CONTINUE TO define and make available the `showAdminToast()` function for use by those pages

3.3 WHEN the performance page HTML is rendered THEN the system SHALL CONTINUE TO display all UI elements (stats grid, quick actions, feature toggles, configuration inputs, metrics table, slow queries table) correctly

3.4 WHEN the admin footer loads on any admin page THEN the system SHALL CONTINUE TO execute all footer functionality (gallery picker, pending count loading, logout, etc.) without interference

3.5 WHEN other admin pages load their inline scripts THEN the system SHALL CONTINUE TO have access to `api()` and `showAdminToast()` functions at the time those scripts execute
