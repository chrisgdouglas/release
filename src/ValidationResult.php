<?php

declare(strict_types=1);

namespace App;

final class ValidationResult
{
    /** @param array<string> $errors */
    private function __construct(
        public readonly ?Submission $submission,
        public readonly array $errors,
    ) {
    }

    public static function ok(Submission $submission): self
    {
        return new self($submission, []);
    }

    /** @param array<string> $errors */
    public static function fail(array $errors): self
    {
        return new self(null, $errors);
    }

    public function isValid(): bool
    {
        return $this->submission !== null;
    }
}
