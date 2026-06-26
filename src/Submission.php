<?php

declare(strict_types=1);

namespace App;

/** Validated form submission. Recipient is resolved separately, never from the client. */
final class Submission
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $date,
        public readonly string $photoNumber,
        public readonly bool $disclaimer,
    ) {
    }
}
