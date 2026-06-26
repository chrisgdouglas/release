<?php

declare(strict_types=1);

namespace App\Tests;

use App\Csrf;
use App\Mailer;
use App\RateLimiter;
use App\SubmitController;
use App\Submission;
use App\TokenStorage;
use App\UserDirectory;
use App\Validator;
use PHPUnit\Framework\TestCase;

final class SubmitControllerTest extends TestCase
{
    private string $rlDir;

    protected function setUp(): void
    {
        $this->rlDir = sys_get_temp_dir() . '/sc_test_' . bin2hex(random_bytes(6));
        mkdir($this->rlDir, 0700, true);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->rlDir . '/*') ?: []);
        @rmdir($this->rlDir);
    }

    private function fixedTokenStorage(string $token): TokenStorage
    {
        return new class ($token) implements TokenStorage {
            public function __construct(private string $token)
            {
            }

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

    /** Recording fake mailer. */
    private function spyMailer(): Mailer
    {
        return new class implements Mailer {
            public ?string $sentTo = null;

            public function send(Submission $submission, string $recipientEmail): void
            {
                $this->sentTo = $recipientEmail;
            }
        };
    }

    /** @return array<string,string> */
    private function validPost(): array
    {
        return [
            'u' => 'chrisd',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'date' => '2026-06-26',
            'photo_number' => 'A-1',
            'disclaimer' => 'true',
        ];
    }

    private function controller(Mailer $mailer): SubmitController
    {
        return new SubmitController(
            new UserDirectory(['chrisd' => 'chris@example.com']),
            new Validator(),
            new Csrf($this->fixedTokenStorage('good-token')),
            new RateLimiter($this->rlDir, maxHits: 3, windowSeconds: 600),
            $mailer,
        );
    }

    public function testHappyPathResolvesRecipientServerSide(): void
    {
        $mailer = $this->spyMailer();
        $resp = $this->controller($mailer)->handle($this->validPost(), 'good-token', '1.2.3.4', 1000);
        self::assertSame(200, $resp->status);
        // Recipient came from the directory, NOT from any client field.
        self::assertSame('chris@example.com', $mailer->sentTo);
    }

    public function testRejectsBadCsrf(): void
    {
        $resp = $this->controller($this->spyMailer())->handle($this->validPost(), 'wrong', '1.2.3.4', 1000);
        self::assertSame(403, $resp->status);
    }

    public function testIgnoresClientSuppliedRecipient(): void
    {
        $mailer = $this->spyMailer();
        $post = $this->validPost();
        $post['crew_email'] = 'attacker@evil.com';
        $post['u'] = 'chrisd';
        $this->controller($mailer)->handle($post, 'good-token', '1.2.3.4', 1000);
        self::assertSame('chris@example.com', $mailer->sentTo);
    }

    public function testUnknownUserRejected(): void
    {
        $post = $this->validPost();
        $post['u'] = 'ghost';
        $resp = $this->controller($this->spyMailer())->handle($post, 'good-token', '1.2.3.4', 1000);
        self::assertSame(422, $resp->status);
    }

    public function testRateLimitBlocksAfterLimit(): void
    {
        $c = $this->controller($this->spyMailer());
        $c->handle($this->validPost(), 'good-token', '9.9.9.9', 1000);
        $c->handle($this->validPost(), 'good-token', '9.9.9.9', 1001);
        $c->handle($this->validPost(), 'good-token', '9.9.9.9', 1002);
        $resp = $c->handle($this->validPost(), 'good-token', '9.9.9.9', 1003);
        self::assertSame(429, $resp->status);
    }
}
