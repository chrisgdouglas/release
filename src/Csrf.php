<?php

declare(strict_types=1);

namespace App;

/** Issues and verifies per-session CSRF tokens. */
final class Csrf
{
    public function __construct(private readonly TokenStorage $storage)
    {
    }

    /** Return the current token, generating and storing one if absent. */
    public function token(): string
    {
        $token = $this->storage->get();
        if ($token === null || $token === '') {
            $token = bin2hex(random_bytes(32));
            $this->storage->set($token);
        }
        return $token;
    }

    public function validate(?string $candidate): bool
    {
        $stored = $this->storage->get();
        if ($stored === null || $stored === '' || $candidate === null || $candidate === '') {
            return false;
        }
        return hash_equals($stored, $candidate);
    }
}
