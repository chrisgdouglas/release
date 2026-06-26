<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

/**
 * Immutable app configuration. Secrets are loaded from a creds file that
 * returns an array (kept outside the webroot, gitignored).
 */
final class Config
{
    public function __construct(
        public readonly string $smtpServer,
        public readonly string $smtpUsername,
        public readonly string $smtpPassword,
        public readonly string $senderName,
        public readonly string $subject,
        public readonly string $orgName,
        public readonly bool $active,
        public readonly string $usersCsvPath,
        public readonly string $rateLimitDir,
    ) {
    }

    /**
     * Load secrets from a creds file (must `return [...]`) and combine with app settings.
     */
    public static function load(
        string $credsFile,
        string $usersCsvPath,
        string $rateLimitDir,
        string $orgName,
        bool $active,
    ): self {
        if (!is_file($credsFile)) {
            throw new RuntimeException('Credentials file not found.');
        }

        /** @var mixed $creds */
        $creds = require $credsFile;
        if (!is_array($creds)) {
            throw new RuntimeException('Credentials file must return an array.');
        }

        $get = static function (string $key) use ($creds): string {
            if (!isset($creds[$key]) || !is_string($creds[$key]) || $creds[$key] === '') {
                throw new RuntimeException("Missing credential: {$key}");
            }
            return $creds[$key];
        };

        return new self(
            smtpServer: $get('smtp_server'),
            smtpUsername: $get('smtp_username'),
            smtpPassword: $get('smtp_password'),
            senderName: $get('sender_name'),
            subject: $get('subject'),
            orgName: $orgName,
            active: $active,
            usersCsvPath: $usersCsvPath,
            rateLimitDir: $rateLimitDir,
        );
    }
}
