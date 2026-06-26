<?php

declare(strict_types=1);

namespace App\Tests;

use App\Csrf;
use App\TokenStorage;
use PHPUnit\Framework\TestCase;

final class CsrfTest extends TestCase
{
    private function storage(): TokenStorage
    {
        return new class implements TokenStorage {
            private ?string $token = null;

            public function get(): ?string
            {
                return $this->token;
            }

            public function set(string $token): void
            {
                $this->token = $token;
            }
        };
    }

    public function testTokenIsStableWithinSession(): void
    {
        $csrf = new Csrf($this->storage());
        $a = $csrf->token();
        $b = $csrf->token();
        self::assertSame($a, $b);
        self::assertSame(64, strlen($a));
    }

    public function testValidateAcceptsIssuedToken(): void
    {
        $csrf = new Csrf($this->storage());
        $token = $csrf->token();
        self::assertTrue($csrf->validate($token));
    }

    public function testValidateRejectsWrongOrEmptyToken(): void
    {
        $csrf = new Csrf($this->storage());
        $csrf->token();
        self::assertFalse($csrf->validate('wrong'));
        self::assertFalse($csrf->validate(null));
        self::assertFalse($csrf->validate(''));
    }

    public function testValidateRejectsWhenNoTokenIssued(): void
    {
        $csrf = new Csrf($this->storage());
        self::assertFalse($csrf->validate('anything'));
    }
}
