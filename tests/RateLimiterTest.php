<?php

declare(strict_types=1);

namespace App\Tests;

use App\RateLimiter;
use PHPUnit\Framework\TestCase;

final class RateLimiterTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $dir = sys_get_temp_dir() . '/rl_test_' . bin2hex(random_bytes(6));
        mkdir($dir, 0700, true);
        $this->dir = $dir;
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->dir . '/*') ?: []);
        @rmdir($this->dir);
    }

    public function testAllowsUpToLimitThenBlocks(): void
    {
        $rl = new RateLimiter($this->dir, maxHits: 3, windowSeconds: 600);
        $now = 1_000_000;

        self::assertTrue($rl->hit('1.2.3.4', $now));
        self::assertTrue($rl->hit('1.2.3.4', $now + 1));
        self::assertTrue($rl->hit('1.2.3.4', $now + 2));
        self::assertFalse($rl->hit('1.2.3.4', $now + 3));
    }

    public function testWindowPrunesOldHits(): void
    {
        $rl = new RateLimiter($this->dir, maxHits: 2, windowSeconds: 600);
        $now = 1_000_000;

        self::assertTrue($rl->hit('ip', $now));
        self::assertTrue($rl->hit('ip', $now + 1));
        self::assertFalse($rl->hit('ip', $now + 2));
        // Past the window — old hits expire, allowed again.
        self::assertTrue($rl->hit('ip', $now + 601));
    }

    public function testKeysAreIndependent(): void
    {
        $rl = new RateLimiter($this->dir, maxHits: 1, windowSeconds: 600);
        $now = 1_000_000;
        self::assertTrue($rl->hit('a', $now));
        self::assertTrue($rl->hit('b', $now));
        self::assertFalse($rl->hit('a', $now));
    }
}
