<?php

declare(strict_types=1);

namespace App;

/** Storage for the CSRF token. Abstracted so it can be tested without a session. */
interface TokenStorage
{
    public function get(): ?string;

    public function set(string $token): void;
}
