<?php

declare(strict_types=1);

namespace App;

/**
 * File-based sliding-window rate limiter (no DB). One JSON file per key-hash,
 * holding recent hit timestamps. Suitable for a single-host deployment.
 */
final class RateLimiter
{
    public function __construct(
        private readonly string $dir,
        private readonly int $maxHits = 5,
        private readonly int $windowSeconds = 600,
    ) {
    }

    /**
     * Record a hit for $key at time $now. Returns true if allowed, false if over the limit.
     */
    public function hit(string $key, int $now): bool
    {
        if (!is_dir($this->dir)) {
            @mkdir($this->dir, 0700, true);
        }

        $file = $this->dir . '/' . hash('sha256', $key) . '.json';
        $handle = @fopen($file, 'c+');
        if ($handle === false) {
            return true; // fail open: never block a real user on a storage hiccup
        }

        try {
            flock($handle, LOCK_EX);
            $raw = stream_get_contents($handle);
            $timestamps = is_string($raw) ? json_decode($raw, true) : null;
            if (!is_array($timestamps)) {
                $timestamps = [];
            }

            $cutoff = $now - $this->windowSeconds;
            $recent = array_values(array_filter(
                $timestamps,
                static fn ($ts): bool => is_int($ts) && $ts > $cutoff,
            ));

            if (count($recent) >= $this->maxHits) {
                return false;
            }

            $recent[] = $now;
            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, (string) json_encode($recent));

            return true;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
