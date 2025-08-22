<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;
use Config\Services;

class EmailService
{
    protected Email $email;

    /**
     * Initializes the email service if it's not already initialized.
     */
    protected function initializeEmail(): void
    {
        if (!isset($this->email)) {
            $this->email = Services::email();
            $this->initializeConfig();
        }
    }

    /**
     * Sends an email with the specified parameters.
     */
    public function sendEmail(string $toEmail, string $subject, string $message, array $attachments = [], string $cc = '', string $bcc = ''): array
    {
        // Ensure the email service is initialized before using it
        $this->initializeEmail();

        // Set the sender's email address and name
        $this->email->setFrom(env('FROM_EMAIL'), env('FROM_NAME'));
        // Set the recipient's email address
        $this->email->setTo($toEmail);
        // Set the email subject
        $this->email->setSubject($subject);
        // Set the email body message
        $this->email->setMessage($message);

        // Set CC (Carbon Copy) if provided
        if (!empty($cc)) {
            $this->email->setCC($cc);
        }

        // Set BCC (Blind Carbon Copy) if provided
        if (!empty($bcc)) {
            $this->email->setBCC($bcc);
        }

        // Handle attachments
        foreach ($attachments as $attachment) {
            // Check if the attachment file exists
            if (file_exists($attachment)) {
                $this->email->attach($attachment); // Attach the file to the email
            } else {
                // If the file does not exist, throw an exception
                throw new \RuntimeException("Attachment file does not exist: $attachment");
            }
        }

        // Send the email and check for success
        if ($this->email->send()) {
            return ['success' => true]; // Return success response
        } else {
            // If sending fails, throw an exception with error details
            throw new \RuntimeException($this->email->printDebugger(['headers']));
        }
    }

    /**
     * Initializes the email configuration.
     */
    protected function initializeConfig(): void
    {
        // Define the email configuration settings
        $config = [
            'protocol' => 'smtp', // Use SMTP protocol
            'SMTPHost' => env('SMTP_HOST'), // SMTP host from environment variables
            'SMTPUser' => env('SMTP_USER'), // SMTP username from environment variables
            'SMTPPass' => env('SMTP_PASS'), // SMTP password from environment variables
            'SMTPPort' => env('SMTP_PORT', 587), // SMTP port, defaulting to 587
            'SMTPCrypto' => env('SMTP_CRYPTO', 'tls'), // Encryption method, defaulting to 'tls'
            'charset' => 'utf-8', // Character set for the email
            'wordWrap' => true, // Enable word wrapping
        ];

        // Initialize the email service with the configuration
        $this->email->initialize($config);
    }
}
