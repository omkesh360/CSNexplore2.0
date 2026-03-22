# Requirements Document

## Introduction

This document specifies the requirements for integrating MailerLite email service into the CSN Explore website to send automated booking confirmation emails to users and booking notification emails to administrators when a new booking is created.

## Glossary

- **Booking_System**: The existing PHP-based booking functionality that creates booking records in the SQLite database
- **MailerLite_Client**: The PHP client library that communicates with the MailerLite API
- **Email_Service**: The service layer that handles email template rendering and sending via MailerLite
- **User_Email**: The confirmation email sent to the customer who made the booking
- **Admin_Email**: The notification email sent to the administrator about a new booking
- **Email_Template**: HTML email layout with dynamic content placeholders
- **API_Key**: The authentication token for MailerLite API access
- **Booking_Record**: A database entry in the bookings table containing customer and booking details

## Requirements

### Requirement 1: MailerLite API Integration

**User Story:** As a developer, I want to integrate the MailerLite API, so that the system can send transactional emails programmatically

#### Acceptance Criteria

1. THE Email_Service SHALL use the MailerLite API key for authentication
2. THE Email_Service SHALL store the API key securely using environment variables or configuration file
3. WHEN the API key is invalid or expired, THE Email_Service SHALL log an error and return a failure status
4. THE Email_Service SHALL use the MailerLite transactional email endpoint for sending emails
5. THE Email_Service SHALL handle API rate limits gracefully without causing booking failures

### Requirement 2: User Confirmation Email

**User Story:** As a customer, I want to receive a confirmation email after making a booking, so that I know my request was received

#### Acceptance Criteria

1. WHEN a booking is successfully created (regardless of status - pending, completed, or cancelled), THE Booking_System SHALL trigger the Email_Service to send a User_Email
2. THE User_Email SHALL be sent to the email address provided in the Booking_Record
3. THE User_Email SHALL include the customer's full name from the Booking_Record
4. THE User_Email SHALL include the booking reference number (booking ID)
5. THE User_Email SHALL include the service type (stays, cars, bikes, restaurants, attractions, buses)
6. THE User_Email SHALL include the booking date if provided
7. THE User_Email SHALL include the number of people
8. THE User_Email SHALL state that the booking will be processed within 4 hours
9. THE User_Email SHALL state that a confirmation email will be sent after admin approval
10. THE User_Email SHALL include contact information (phone: +91 86009 68888, email: supportcsnexplore@gmail.com)
11. IF the email address is missing from the Booking_Record, THEN THE Booking_System SHALL skip sending the User_Email without failing the booking

### Requirement 3: Admin Notification Email

**User Story:** As an administrator, I want to receive an email notification when a new booking is created, so that I can process it promptly

#### Acceptance Criteria

1. WHEN a booking is successfully created (regardless of status - pending, completed, or cancelled), THE Booking_System SHALL trigger the Email_Service to send an Admin_Email
2. THE Admin_Email SHALL be sent to supportcsnexplore@gmail.com
3. THE Admin_Email SHALL include all booking details from the Booking_Record
4. THE Admin_Email SHALL include the customer's full name, phone number, and email
5. THE Admin_Email SHALL include the booking reference number (booking ID)
6. THE Admin_Email SHALL include the service type and listing name if available
7. THE Admin_Email SHALL include the booking date, check-in date, and check-out date if provided
8. THE Admin_Email SHALL include the number of people
9. THE Admin_Email SHALL include any notes provided by the customer
10. THE Admin_Email SHALL include a timestamp of when the booking was created

### Requirement 4: Email Template Design

**User Story:** As a customer, I want to receive professional and visually appealing emails, so that I trust the service

#### Acceptance Criteria

1. THE Email_Template SHALL use HTML formatting with inline CSS for email client compatibility
2. THE Email_Template SHALL include the CSN Explore logo and branding colors (primary: #ec5b13)
3. THE Email_Template SHALL be mobile-responsive using fluid layouts and appropriate font sizes
4. THE Email_Template SHALL use web-safe fonts or font fallbacks
5. THE Email_Template SHALL include a clear visual hierarchy with headings and sections
6. THE Email_Template SHALL match the website's design theme and color scheme
7. THE User_Email template SHALL have a friendly, welcoming tone
8. THE Admin_Email template SHALL have a clear, information-dense layout for quick scanning

### Requirement 5: Error Handling and Reliability

**User Story:** As a developer, I want the email system to handle errors gracefully, so that booking creation never fails due to email issues

#### Acceptance Criteria

1. IF the Email_Service fails to send an email, THEN THE Booking_System SHALL log the error and continue processing
2. THE Booking_System SHALL NOT fail or rollback a booking if email sending fails
3. WHEN an email fails to send, THE Email_Service SHALL log the error with booking ID and error details
4. THE Email_Service SHALL implement a timeout of 10 seconds for API requests
5. IF the MailerLite API is unreachable, THEN THE Email_Service SHALL log the error and return gracefully
6. THE Email_Service SHALL validate email addresses before attempting to send

### Requirement 6: Email Content Validation

**User Story:** As a developer, I want to ensure email content is properly formatted, so that emails display correctly across all email clients

#### Acceptance Criteria

1. THE Email_Service SHALL sanitize all dynamic content before inserting into Email_Template
2. THE Email_Service SHALL escape HTML special characters in user-provided data
3. THE Email_Service SHALL handle missing or null values in Booking_Record gracefully
4. THE Email_Service SHALL use default values or omit sections when optional data is missing
5. THE Email_Template SHALL be tested for compatibility with major email clients (Gmail, Outlook, Apple Mail)

### Requirement 7: Integration with Existing Booking Flow

**User Story:** As a developer, I want the email integration to work seamlessly with the existing booking system, so that no changes are required to the frontend

#### Acceptance Criteria

1. THE Booking_System SHALL call the Email_Service after successfully inserting a Booking_Record
2. THE Booking_System SHALL pass the complete Booking_Record data to the Email_Service
3. THE Email_Service SHALL be implemented as a separate module that can be called from the booking API
4. THE Email_Service SHALL not modify the existing booking API response structure
5. THE Email_Service SHALL execute asynchronously to avoid delaying the booking API response
