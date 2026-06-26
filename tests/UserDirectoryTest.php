<?php

declare(strict_types=1);

namespace App\Tests;

use App\UserDirectory;
use PHPUnit\Framework\TestCase;

final class UserDirectoryTest extends TestCase
{
    public function testLookupHitAndMiss(): void
    {
        $dir = new UserDirectory(['chrisd' => 'chris@example.com']);
        self::assertTrue($dir->exists('chrisd'));
        self::assertSame('chris@example.com', $dir->emailFor('chrisd'));
        self::assertFalse($dir->exists('nobody'));
        self::assertNull($dir->emailFor('nobody'));
    }

    public function testFromCsvSkipsMalformedRows(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'csv');
        self::assertIsString($path);
        file_put_contents($path, "good,good@example.com\nbad,not-an-email\nempty,\n");

        $dir = UserDirectory::fromCsv($path);
        unlink($path);

        self::assertTrue($dir->exists('good'));
        self::assertFalse($dir->exists('bad'));
        self::assertFalse($dir->exists('empty'));
    }

    public function testFromCsvMissingFileIsEmpty(): void
    {
        $dir = UserDirectory::fromCsv('/no/such/file.csv');
        self::assertFalse($dir->exists('anyone'));
    }
}
