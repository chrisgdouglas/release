<?php

declare(strict_types=1);

namespace App;

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use RuntimeException;

/** PHPMailer-backed implementation. Body values are escaped (email is sent as HTML). */
final class PhpMailerMailer implements Mailer
{
    public function __construct(private readonly Config $config)
    {
    }

    public function send(Submission $submission, string $recipientEmail): void
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->config->smtpServer;
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->smtpUsername;
            $mail->Password = $this->config->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($this->config->smtpUsername, $this->config->senderName);
            // Submitter and photographer (recipient resolved server-side, never from input).
            $mail->addAddress($submission->email, $submission->name);
            $mail->addAddress($recipientEmail);
            $mail->addReplyTo($recipientEmail, $this->config->senderName);

            $mail->isHTML(true);
            $mail->Subject = $this->config->subject;
            $mail->Body = $this->body($submission);
            $mail->AltBody = strip_tags(str_replace('<br>', "\n", $this->body($submission)));

            $mail->send();
        } catch (PHPMailerException $e) {
            // Never leak SMTP details to the client; log server-side.
            error_log('Mail send failed: ' . $e->getMessage());
            throw new RuntimeException('Email could not be sent.', 0, $e);
        }
    }

    private function body(Submission $submission): string
    {
        $e = static fn (string $v): string => Html::e($v);

        return 'Name: ' . $e($submission->name)
            . '<br>Email: ' . $e($submission->email)
            . '<br>Date: ' . $e($submission->date)
            . '<br>Photo Number: ' . $e($submission->photoNumber)
            . '<br>Photo Use Permission: ' . ($submission->disclaimer ? 'True' : 'False');
    }
}
