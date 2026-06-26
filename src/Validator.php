<?php

declare(strict_types=1);

namespace App;

use DateTimeImmutable;

/** Validates and sanitizes raw form input into a Submission DTO. */
final class Validator
{
    private const MAX_LEN = 200;

    /** @param array<string,mixed> $input */
    public function validate(array $input): ValidationResult
    {
        $errors = [];

        $name = $this->cleanLine($input['name'] ?? null);
        if ($name === null || $name === '') {
            $errors[] = 'Name is required.';
        }

        $photoNumber = $this->cleanLine($input['photo_number'] ?? null);
        if ($photoNumber === null || $photoNumber === '') {
            $errors[] = 'Photo number is required.';
        }

        $emailRaw = is_string($input['email'] ?? null) ? trim($input['email']) : '';
        $email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
        if ($email === false) {
            $errors[] = 'A valid email is required.';
        }

        $dateRaw = is_string($input['date'] ?? null) ? trim($input['date']) : '';
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $dateRaw);
        $dateValid = $date !== false && $date->format('Y-m-d') === $dateRaw;
        if (!$dateValid) {
            $errors[] = 'A valid date (YYYY-MM-DD) is required.';
        }

        $disclaimer = $this->isTruthy($input['disclaimer'] ?? null);
        if (!$disclaimer) {
            $errors[] = 'The disclaimer must be acknowledged.';
        }

        if ($errors !== []) {
            return ValidationResult::fail($errors);
        }

        /** @var string $name */
        /** @var string $photoNumber */
        /** @var string $email */
        return ValidationResult::ok(new Submission(
            name: $name,
            email: $email,
            date: $dateRaw,
            photoNumber: $photoNumber,
            disclaimer: true,
        ));
    }

    /** Trim, length-cap, and reject CR/LF (email header injection defense). */
    private function cleanLine(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }
        if (preg_match('/[\r\n]/', $value) === 1) {
            return null;
        }
        $value = trim($value);
        if (mb_strlen($value) > self::MAX_LEN) {
            return null;
        }
        return $value;
    }

    private function isTruthy(mixed $value): bool
    {
        return $value === true
            || $value === '1'
            || $value === 1
            || (is_string($value) && strtolower($value) === 'true')
            || $value === 'on';
    }
}
