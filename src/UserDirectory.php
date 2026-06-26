<?php

declare(strict_types=1);

namespace App;

/**
 * Registry of photographers (username => email), loaded from CSV.
 *
 * Security: the recipient email is ALWAYS resolved here from a trusted
 * username, never accepted from client input (prevents open-relay abuse).
 */
final class UserDirectory
{
    /** @param array<string,string> $map username => email */
    public function __construct(private readonly array $map)
    {
    }

    public static function fromCsv(string $path): self
    {
        $map = [];
        $handle = @fopen($path, 'r');
        if ($handle === false) {
            return new self($map);
        }

        while (($row = fgetcsv($handle, 1000, ',', '"', '\\')) !== false) {
            if (!isset($row[0], $row[1])) {
                continue;
            }
            $username = trim((string) $row[0]);
            $email = trim((string) $row[1]);
            if ($username === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                continue; // skip malformed rows
            }
            $map[$username] = $email;
        }
        fclose($handle);

        return new self($map);
    }

    public function exists(string $username): bool
    {
        return isset($this->map[$username]);
    }

    public function emailFor(string $username): ?string
    {
        return $this->map[$username] ?? null;
    }
}
