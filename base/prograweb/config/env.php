<?php

declare(strict_types=1);

if (!function_exists('loadEnv')) {
    function loadEnv(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            if ($trimmedLine === '' || str_starts_with($trimmedLine, '#')) {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $trimmedLine, 2), 2, '');

            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            $value = trim($value, "\"'");

            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;

            putenv(sprintf('%s=%s', $key, $value));
        }
    }
}
