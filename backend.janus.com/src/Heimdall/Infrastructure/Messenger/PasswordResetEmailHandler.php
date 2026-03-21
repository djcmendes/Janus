<?php

declare(strict_types=1);

namespace App\Heimdall\Infrastructure\Messenger;

use App\Heimdall\Domain\Message\PasswordResetEmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
final class PasswordResetEmailHandler
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {}

    public function __invoke(PasswordResetEmailMessage $message): void
    {
        $resetUrl = sprintf(
            '%s/reset-password?token=%s',
            rtrim($message->appBaseUrl, '/'),
            urlencode($message->resetToken),
        );

        $text = <<<TEXT
            You requested a password reset for your Janus account.

            Click the link below to set a new password (valid for 1 hour):

            {$resetUrl}

            If you did not request this, you can safely ignore this email.
            TEXT;

        $html = sprintf(
            '<p>You requested a password reset for your <strong>Janus</strong> account.</p>'
            . '<p><a href="%1$s">Reset your password</a></p>'
            . '<p>Or copy this link into your browser:<br><code>%1$s</code></p>'
            . '<p>This link is valid for 1 hour. If you did not request a reset, ignore this email.</p>',
            htmlspecialchars($resetUrl, ENT_QUOTES),
        );

        $email = (new Email())
            ->to($message->recipientEmail)
            ->subject('Reset your Janus password')
            ->text($text)
            ->html($html);

        $this->mailer->send($email);
    }
}
