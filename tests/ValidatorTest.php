<?php

declare(strict_types=1);

namespace App\Tests;

use App\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    /** @return array<string,mixed> */
    private function validInput(): array
    {
        return [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'date' => '2026-06-26',
            'photo_number' => 'A-1234',
            'disclaimer' => 'true',
        ];
    }

    public function testAcceptsValidInput(): void
    {
        $result = $this->validator->validate($this->validInput());
        self::assertTrue($result->isValid());
        self::assertNotNull($result->submission);
        self::assertSame('jane@example.com', $result->submission->email);
        self::assertTrue($result->submission->disclaimer);
    }

    public function testRejectsHeaderInjectionInName(): void
    {
        $input = $this->validInput();
        $input['name'] = "Jane\r\nBcc: victim@example.com";
        $result = $this->validator->validate($input);
        self::assertFalse($result->isValid());
    }

    public function testRejectsBadEmail(): void
    {
        $input = $this->validInput();
        $input['email'] = 'not-an-email';
        self::assertFalse($this->validator->validate($input)->isValid());
    }

    public function testRejectsBadDate(): void
    {
        $input = $this->validInput();
        $input['date'] = '2026-13-99';
        self::assertFalse($this->validator->validate($input)->isValid());
    }

    public function testRejectsMissingDisclaimer(): void
    {
        $input = $this->validInput();
        $input['disclaimer'] = 'false';
        self::assertFalse($this->validator->validate($input)->isValid());
    }

    public function testRejectsMissingFields(): void
    {
        self::assertFalse($this->validator->validate([])->isValid());
    }
}
