<?php

declare(strict_types=1);

namespace App;

/** CSRF token storage backed by $_SESSION. */
final class SessionTokenStorage implements TokenStorage
{
    private const KEY = 'csrf_token';

    public function get(): ?string
    {
        $value = $_SESSION[self::KEY] ?? null;
        return is_string($value) ? $value : null;
    }

    public function set(string $token): void
    {
        $_SESSION[self::KEY] = $token;
    }
}
