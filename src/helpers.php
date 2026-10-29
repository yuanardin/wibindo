<?php
declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function asset(string $path): string
{
    $relative = ltrim($path, '/');
    $file = __DIR__ . '/../public/assets/' . $relative;
    $version = is_file($file) ? (string) filemtime($file) : '0';

    return '/assets/' . $relative . '?v=' . rawurlencode($version);
}

function screenshot(string $file): string
{
    $relative = 'img/screenshots/' . ltrim($file, '/');
    $fullPath = __DIR__ . '/../public/assets/' . $relative;

    if (!is_file($fullPath)) {
        $relative = 'img/screenshots/placeholder.webp';
    }

    return asset($relative);
}
