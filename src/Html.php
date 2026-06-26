<?php

declare(strict_types=1);

namespace App;

/** Output escaping. All template output of dynamic values must go through e(). */
final class Html
{
    public static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
