<?php

declare(strict_types=1);

namespace App;

/** Minimal PHP template renderer. Templates live outside the analysed source. */
final class View
{
    public function __construct(private readonly string $dir)
    {
    }

    /** @param array<string,mixed> $data */
    public function render(string $name, array $data = []): string
    {
        $file = $this->dir . '/' . $name . '.php';
        $render = static function (string $__file, array $__data): string {
            extract($__data, EXTR_SKIP);
            ob_start();
            include $__file;
            return (string) ob_get_clean();
        };
        return $render($file, $data);
    }
}
