<?php

/**
 * Email Service
 * 
 * Orchestrates email sending for booking confirmations and admin notifications
 * Uses PHPMailer with SMTP for reliable email delivery
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/phpmailer/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/SMTP.php';
require_once __DIR__ . '/../vendor/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    
    /**
     * Send both user confirmation and admin notification emails
     * 
     * @param int $bookingId The booking record ID
     * @return array ['user_sent' => bool, 'admin_sent' => bool, 'errors' => array]
     */
    public static function sendBookingEmails(int $bookingId): array {
        $result = [
            'user_sent' => false,
            'admin_sent' => false,
            'errors' => []
        ];
        
        try {
            // Fetch complete booking record from database
            $db = getDB();
            $booking = $db->fetchOne("SELECT * FROM bookings WHERE id = ?", [$bookingId]);
            
            if (!$booking) {
                $error = "Booking not found: ID $bookingId";
                self::logError($error, ['booking_id' => $bookingId]);
                $result['errors'][] = $error;
                return $result;
            }
            
            // Send user confirmation email
            $result['user_sent'] = self::sendUserConfirmation($booking);
            
            // Send admin notification email
            $result['admin_sent'] = self::sendAdminNotification($booking);
            
        } catch (Exception $e) {
            $error = "Failed to send booking emails: " . $e->getMessage();
            self::logError($error, [
                'booking_id' => $bookingId,
                'exception' => get_class($e)
            ]);
            $result['errors'][] = $error;
        }
        
        return $result;
    }
    
    /**
     * Send status update email to user
     * 
     * @param int $bookingId The booking record ID
     * @param string $status New status (completed or cancelled)
     * @return bool Success status
     */
    public static function sendStatusUpdateEmail(int $bookingId, string $status): bool {
        try {
            // Fetch complete booking record from database
            $db = getDB();
            $booking = $db->fetchOne("SELECT * FROM bookings WHERE id = ?", [$bookingId]);
            
            if (!$booking) {
                self::logError("Booking not found for status update", ['booking_id' => $bookingId]);
                return false;
            }
            
            // Handle missing user email gracefully
            if (empty($booking['email'])) {
                self::logError("No email provided for status update", [
                    'booking_id' => $bookingId,
                    'status' => $status
                ]);
                return false;
            }
            
            // Validate email address
            if (!filter_var($booking['email'], FILTER_VALIDATE_EMAIL)) {
                self::logError("Invalid email address for status update", [
                    'booking_id' => $bookingId,
                    'email' => $booking['email'],
                    'status' => $status
                ]);
                return false;
            }
            
            // Determine template and subject based on status
            if ($status === 'completed') {
                $template = 'booking-confirmed';
                $subject = 'Booking Confirmed - CSN Explore';
            } elseif ($status === 'cancelled') {
                $template = 'booking-cancelled';
                $subject = 'Booking Cancelled - CSN Explore';
            } else {
                self::logError("Invalid status for email", [
                    'booking_id' => $bookingId,
                    'status' => $status
                ]);
                return false;
            }
            
            // Render email template
            $htmlContent = self::renderTemplate($template, $booking);
            
            if ($htmlContent === false) {
                self::logError("Failed to render status update template", [
                    'booking_id' => $bookingId,
                    'template' => $template
                ]);
                return false;
            }
            
            // Send email using PHPMailer
            $mail = self::createMailer();
            $mail->addAddress($booking['email'], $booking['full_name']);
            $mail->Subject = $subject;
            $mail->Body = $htmlContent;
            $mail->AltBody = strip_tags($htmlContent);
            
            if (!$mail->send()) {
                self::logError("Failed to send status update email", [
                    'booking_id' => $bookingId,
                    'email' => $booking['email'],
                    'status' => $status,
                    'error' => $mail->ErrorInfo
                ]);
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            self::logError("Exception in sendStatusUpdateEmail", [
                'booking_id' => $bookingId,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Send user confirmation email
     * 
     * @param array $booking Booking record data
     * @return bool Success status
     */
    private static function sendUserConfirmation(array $booking): bool {
        try {
            // Handle missing user email gracefully
            if (empty($booking['email'])) {
                self::logError("No email provided for booking", [
                    'booking_id' => $booking['id'],
                    'email_type' => 'user_confirmation'
                ]);
                return false;
            }
            
            // Validate email address
            if (!filter_var($booking['email'], FILTER_VALIDATE_EMAIL)) {
                self::logError("Invalid email address", [
                    'booking_id' => $booking['id'],
                    'email' => $booking['email'],
                    'email_type' => 'user_confirmation'
                ]);
                return false;
            }
            
            // Render email template
            $htmlContent = self::renderTemplate('user-confirmation', $booking);
            
            if ($htmlContent === false) {
                self::logError("Failed to render user confirmation template", [
                    'booking_id' => $booking['id'],
                    'email_type' => 'user_confirmation'
                ]);
                return false;
            }
            
            // Send email using PHPMailer
            $mail = self::createMailer();
            $mail->addAddress($booking['email'], $booking['full_name']);
            $mail->Subject = 'Booking Confirmation - CSN Explore';
            $mail->Body = $htmlContent;
            $mail->AltBody = strip_tags($htmlContent);
            
            if (!$mail->send()) {
                self::logError("Failed to send user confirmation email", [
                    'booking_id' => $booking['id'],
                    'email' => $booking['email'],
                    'email_type' => 'user_confirmation',
                    'error' => $mail->ErrorInfo
                ]);
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            self::logError("Exception in sendUserConfirmation", [
                'booking_id' => $booking['id'],
                'email_type' => 'user_confirmation',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Send admin notification email
     * 
     * @param array $booking Booking record data
     * @return bool Success status
     */
    private static function sendAdminNotification(array $booking): bool {
        try {
            // Render email template
            $htmlContent = self::renderTemplate('admin-notification', $booking);
            
            if ($htmlContent === false) {
                self::logError("Failed to render admin notification template", [
                    'booking_id' => $booking['id'],
                    'email_type' => 'admin_notification'
                ]);
                return false;
            }
            
            // Send email using PHPMailer
            $mail = self::createMailer();
            $mail->addAddress(ADMIN_NOTIFICATION_EMAIL, 'CSN Explore Admin');
            $serviceType = ucfirst($booking['service_type'] ?? 'Service');
            $mail->Subject = "New Booking #{$booking['id']} - {$serviceType}";
            $mail->Body = $htmlContent;
            $mail->AltBody = strip_tags($htmlContent);
            
            if (!$mail->send()) {
                self::logError("Failed to send admin notification email", [
                    'booking_id' => $booking['id'],
                    'email' => ADMIN_NOTIFICATION_EMAIL,
                    'email_type' => 'admin_notification',
                    'error' => $mail->ErrorInfo
                ]);
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            self::logError("Exception in sendAdminNotification", [
                'booking_id' => $booking['id'],
                'email_type' => 'admin_notification',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Create and configure PHPMailer instance
     * 
     * @return PHPMailer Configured mailer instance
     */
    private static function createMailer(): PHPMailer {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port = SMTP_PORT;
        
        // Performance optimizations
        $mail->Timeout = 10; // Reduce timeout from default 300s to 10s
        $mail->SMTPKeepAlive = true; // Keep connection alive for multiple emails
        
        // Sender info
        $mail->setFrom(MAILERLITE_FROM_EMAIL, MAILERLITE_FROM_NAME);
        $mail->addReplyTo(MAILERLITE_FROM_EMAIL, MAILERLITE_FROM_NAME);
        
        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        // Disable SSL verification for development (remove in production)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        return $mail;
    }
    
    /**
     * Render email template using output buffering
     * 
     * @param string $templateName Template name (without .php extension)
     * @param array $booking Booking data to pass to template
     * @return string|false Rendered HTML or false on failure
     */
    private static function renderTemplate(string $templateName, array $booking) {
        $templatePath = __DIR__ . "/../templates/emails/{$templateName}.php";
        
        if (!file_exists($templatePath)) {
            self::logError("Template file not found", [
                'template' => $templateName,
                'path' => $templatePath
            ]);
            return false;
        }
        
        try {
            // Start output buffering
            ob_start();
            
            // Include template (template has access to $booking variable)
            include $templatePath;
            
            // Get rendered content
            $htmlContent = ob_get_clean();
            
            return $htmlContent;
            
        } catch (Exception $e) {
            // Clean buffer on error
            ob_end_clean();
            
            self::logError("Template rendering exception", [
                'template' => $templateName,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Log email errors to logs/email_errors.log
     * 
     * @param string $message Error message
     * @param array $context Additional context
     */
    private static function logError(string $message, array $context = []): void {
        $logDir = __DIR__ . '/../../logs';
        $logFile = $logDir . '/email_errors.log';
        
        // Ensure logs directory exists
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        
        // Format log entry
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] ERROR: {$message}\n";
        
        // Add context details
        foreach ($context as $key => $value) {
            $logEntry .= ucfirst(str_replace('_', ' ', $key)) . ": " . (is_array($value) ? json_encode($value) : $value) . "\n";
        }
        
        $logEntry .= "\n";
        
        // Write to log file
        @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
