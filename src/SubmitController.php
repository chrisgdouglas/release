<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

final class SubmitController
{
    public function __construct(
        private readonly UserDirectory $users,
        private readonly Validator $validator,
        private readonly Csrf $csrf,
        private readonly RateLimiter $rateLimiter,
        private readonly Mailer $mailer,
    ) {
    }

    /** @param array<string,mixed> $post */
    public function handle(array $post, ?string $csrfHeader, string $clientIp, int $now): Response
    {
        if (!$this->csrf->validate($csrfHeader)) {
            return Response::json(['ok' => false, 'error' => 'Invalid session token.'], 403);
        }

        if (!$this->rateLimiter->hit($clientIp, $now)) {
            return Response::json(['ok' => false, 'error' => 'Too many submissions. Try again later.'], 429);
        }

        // Recipient is resolved from the trusted username, never from client-supplied email.
        $username = is_string($post['u'] ?? null) ? $post['u'] : '';
        $recipient = $this->users->emailFor($username);
        if ($recipient === null) {
            return Response::json(['ok' => false, 'error' => 'Unknown recipient.'], 422);
        }

        $result = $this->validator->validate($post);
        if (!$result->isValid()) {
            return Response::json(['ok' => false, 'error' => implode(' ', $result->errors)], 422);
        }

        /** @var Submission $submission */
        $submission = $result->submission;

        try {
            $this->mailer->send($submission, $recipient);
        } catch (RuntimeException) {
            return Response::json(['ok' => false, 'error' => 'Email could not be sent. Please try again later.'], 502);
        }

        return Response::json(['ok' => true]);
    }
}
