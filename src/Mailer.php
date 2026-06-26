<?php

declare(strict_types=1);

namespace App;

/** Sends a release confirmation. Abstracted so controllers are testable without SMTP. */
interface Mailer
{
    /** @throws \RuntimeException on send failure */
    public function send(Submission $submission, string $recipientEmail): void;
}
