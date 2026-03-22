# Implementation Plan: MailerLite Email Integration

## Overview

This plan implements automated email notifications for the CSN Explore booking system using MailerLite's transactional email API. The implementation follows a non-blocking architecture where email failures never prevent booking creation.

## Tasks

- [ ] 1. Set up configuration and environment
  - Create `.env` file with MailerLite API credentials
  - Add `.env` to `.gitignore` to prevent credential exposure
  - Extend `php/config.php` to load environment variables and define email constants
  - Create `.env.example` template with placeholder values for documentation
  - _Requirements: 1.1, 1.2_

- [ ] 2. Implement MailerLite API client
  - [ ] 2.1 Create `php/services/MailerLiteClient.php` with API wrapper class
    - Implement constructor that accepts API key
    - Implement `sendEmail()` method with parameters: to, subject, htmlContent, fromEmail, fromName
    - Implement `makeRequest()` private method for HTTP communication using cURL
    - Set correct API base URL: `https://connect.mailerlite.com/api`
    - Set required headers: Content-Type, Accept, Authorization Bearer token
    - Implement 10-second timeout for API requests
    - Implement `isValidEmail()` private method using `filter_var(FILTER_VALIDATE_EMAIL)`
    - Return structured response array with success status, message, and API response
    - _Requirements: 1.1, 1.3, 1.4, 5.4, 5.6_

  - [ ]* 2.2 Write property test for MailerLite API client
    - **Property 8: Email address validation**
    - **Validates: Requirements 5.6**
    - Test that invalid email formats are rejected before API calls
    - Test that valid email formats pass validation

- [ ] 3. Create email templates
  - [ ] 3.1 Create `php/templates/emails/` directory structure
    - Create directory if it doesn't exist
    - _Requirements: 4.1_

  - [ ] 3.2 Implement user confirmation email template
    - Create `php/templates/emails/user-confirmation.php`
    - Design HTML structure with table-based layout for email client compatibility
    - Include CSN Explore logo reference (absolute URL)
    - Apply brand color #ec5b13 for headers and accents
    - Use inline CSS for all styling
    - Implement mobile-responsive design (max-width 600px, fluid layout)
    - Include dynamic placeholders: customer name, booking ID, service type, booking date, number of people
    - Include static content: "processed within 4 hours" message, "confirmation after admin approval" message
    - Include contact information: phone +91 86009 68888, email supportcsnexplore@gmail.com
    - Sanitize all dynamic content using `htmlspecialchars()` with ENT_QUOTES
    - Handle missing optional fields gracefully (booking_date, listing_name)
    - _Requirements: 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 2.9, 2.10, 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7, 6.1, 6.2, 6.3, 6.4_

  - [ ] 3.3 Implement admin notification email template
    - Create `php/templates/emails/admin-notification.php`
    - Design information-dense HTML layout for quick scanning
    - Include all booking fields: full_name, phone, email, booking_date, checkin_date, checkout_date, number_of_people, service_type, listing_id, listing_name, notes, created_at
    - Use clear section headers (Customer Details, Booking Details, Notes)
    - Include booking reference number prominently
    - Include formatted timestamp
    - Apply brand styling with #ec5b13 color
    - Sanitize all dynamic content
    - Handle null/missing fields gracefully
    - _Requirements: 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9, 3.10, 4.1, 4.2, 4.8, 6.1, 6.2, 6.3_

  - [ ]* 3.4 Write property tests for email templates
    - **Property 3: User email content completeness**
    - **Validates: Requirements 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 2.9, 2.10**
    - Test that all required fields appear in rendered user email
    - **Property 5: Admin email content completeness**
    - **Validates: Requirements 3.3**
    - Test that all booking fields appear in rendered admin email
    - **Property 9: Dynamic content sanitization**
    - **Validates: Requirements 6.1, 6.2**
    - Test that HTML special characters are escaped in user-provided data
    - Test that script tags and malicious content are neutralized
    - **Property 10: Missing optional data handled gracefully**
    - **Validates: Requirements 6.3, 6.4**
    - Test templates with null/missing optional fields don't cause errors
    - **Property 13: Branding elements present**
    - **Validates: Requirements 4.2**
    - Test that logo reference and brand color #ec5b13 appear in both templates

- [ ] 4. Implement email service orchestration
  - [ ] 4.1 Create `php/services/EmailService.php` with email orchestration logic
    - Implement static `sendBookingEmails($bookingId)` method as main entry point
    - Fetch complete booking record from database using booking ID
    - Implement private `sendUserConfirmation($booking)` method
    - Implement private `sendAdminNotification($booking)` method
    - Implement private `logError($message, $context)` method that writes to `logs/email_errors.log`
    - Load email templates using `include` and output buffering
    - Pass booking data to templates as variables
    - Call MailerLiteClient for each email
    - Handle missing user email gracefully (skip user email, still send admin email)
    - Return structured response: ['user_sent' => bool, 'admin_sent' => bool, 'errors' => array]
    - Log all errors with booking ID, email type, and error details
    - Never throw exceptions (catch all errors and log them)
    - _Requirements: 2.1, 2.2, 2.11, 3.1, 3.2, 5.1, 5.2, 5.3, 5.5, 7.2, 7.3_

  - [ ]* 4.2 Write property tests for email service
    - **Property 1: Email service triggered for all new bookings**
    - **Validates: Requirements 2.1, 3.1**
    - Test that both emails are attempted for any valid booking
    - **Property 2: User email recipient correctness**
    - **Validates: Requirements 2.2**
    - Test that user email is sent to the email from booking record
    - **Property 4: Admin email recipient correctness**
    - **Validates: Requirements 3.2**
    - Test that admin email is sent to supportcsnexplore@gmail.com
    - **Property 6: Email failures don't break bookings**
    - **Validates: Requirements 5.1, 5.2**
    - Test that email service errors don't throw exceptions
    - Test that service returns gracefully even with API failures
    - **Property 7: Email failures are logged**
    - **Validates: Requirements 5.3**
    - Test that all email errors are written to log file with booking ID and details
    - **Property 11: Complete booking data passed to email service**
    - **Validates: Requirements 7.2**
    - Test that all booking record fields are available in email templates

- [ ] 5. Integrate with booking API
  - [ ] 5.1 Modify `php/api/bookings.php` to call email service after booking creation
    - Add `require_once` for EmailService at top of file
    - After successful booking insert (after `$newId` is assigned), call `EmailService::sendBookingEmails($newId)`
    - Wrap email service call in try-catch to prevent booking failures
    - Log email service response for monitoring
    - Do NOT modify the API response structure (still return `['success' => true, 'id' => $newId]`)
    - Consider using `fastcgi_finish_request()` before email sending to avoid blocking response
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

  - [ ]* 5.2 Write integration tests for booking API with email service
    - **Property 12: API response structure unchanged**
    - **Validates: Requirements 7.4**
    - Test that booking API response format is identical with or without email success
    - Test that booking creation succeeds even when email service fails
    - Test that booking ID is correctly passed to email service

- [ ] 6. Checkpoint - Test email integration end-to-end
  - Create test booking with valid email address
  - Verify user confirmation email is received
  - Verify admin notification email is received
  - Check email rendering in Gmail, Outlook, and Apple Mail
  - Test mobile responsiveness on actual devices
  - Verify all dynamic content displays correctly
  - Test with missing optional fields (dates, listing name, notes)
  - Test with special characters in names and notes
  - Verify branding (logo, colors) displays correctly
  - Ensure all tests pass, ask the user if questions arise

- [ ] 7. Security and error handling validation
  - [ ] 7.1 Verify API key security
    - Confirm `.env` file is in `.gitignore`
    - Verify API key is not exposed in logs or error messages
    - Test that missing API key is handled gracefully
    - _Requirements: 1.2, 1.3_

  - [ ] 7.2 Test error scenarios
    - Test booking creation with invalid API key (booking should succeed, error logged)
    - Test booking creation with network timeout (booking should succeed, error logged)
    - Test booking creation without user email (only admin email sent)
    - Test booking creation with invalid user email format (email skipped, logged)
    - Verify all errors are logged to `logs/email_errors.log` with proper format
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6_

  - [ ]* 7.3 Write unit tests for error handling
    - Test EmailService handles missing API key gracefully
    - Test EmailService handles API timeout gracefully
    - Test EmailService handles malformed API responses
    - Test EmailService handles missing user email
    - Test EmailService handles invalid email addresses
    - Verify error logging format and content

- [ ] 8. Documentation and deployment preparation
  - [ ] 8.1 Create deployment documentation
    - Document environment variable setup process
    - Document API key rotation procedure
    - Create `.env.example` with all required variables and descriptions
    - Document email template customization process
    - _Requirements: 1.2_

  - [ ] 8.2 Verify production readiness
    - Confirm all files are created and in correct locations
    - Verify `logs/` directory exists and is writable
    - Test with actual MailerLite API key provided by user
    - Verify email deliverability (check spam folders)
    - Monitor initial error logs after deployment
    - Document monitoring and maintenance procedures

- [ ] 9. Final checkpoint - Complete integration validation
  - Run all property-based tests and verify they pass
  - Run all unit tests and verify they pass
  - Test complete booking flow end-to-end with real API
  - Verify both email types are delivered successfully
  - Check error logs are empty or contain only expected warnings
  - Confirm booking creation never fails due to email issues
  - Verify email templates display correctly across email clients
  - Ensure all tests pass, ask the user if questions arise

## Notes

- Tasks marked with `*` are optional testing tasks and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Email sending is non-blocking - booking creation always succeeds
- API key must be kept secure and never committed to version control
- All user-provided data must be sanitized before inserting into email templates
- Property tests validate universal correctness properties across randomized inputs
- Integration tests ensure email service works seamlessly with existing booking flow
