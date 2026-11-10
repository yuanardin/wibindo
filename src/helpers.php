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
    return screenshotMeta($file)['url'] ?? '';
}

/**
 * Resolve a screenshot and its intrinsic dimensions.
 *
 * @return array{url: string, width: int, height: int}|null
 */
function screenshotMeta(string $file): ?array
{
    $resolved = resolveScreenshotPath($file);
    $size = $resolved === null ? false : @getimagesize($resolved);

    if (
        $size === false
        || ($size['mime'] ?? '') !== 'image/webp'
        || $size[0] < 1
        || $size[1] < 1
    ) {
        $resolved = resolveScreenshotPath('placeholder.webp');
        $size = $resolved === null ? false : @getimagesize($resolved);
    }

    if (
        $resolved === null
        || $size === false
        || ($size['mime'] ?? '') !== 'image/webp'
        || $size[0] < 1
        || $size[1] < 1
    ) {
        return null;
    }

    return [
        'url' => asset('img/screenshots/' . basename($resolved)),
        'width' => $size[0],
        'height' => $size[1],
    ];
}

function resolveScreenshotPath(string $file): ?string
{
    $screenshotsDir = realpath(__DIR__ . '/../public/assets/img/screenshots');

    if (
        $screenshotsDir === false
        || $file === ''
        || basename($file) !== $file
        || preg_match('/\A[a-z0-9][a-z0-9-]*\.webp\z/', $file) !== 1
    ) {
        return null;
    }

    $fullPath = realpath($screenshotsDir . DIRECTORY_SEPARATOR . $file);

    if (
        $fullPath === false
        || !is_file($fullPath)
        || !str_starts_with($fullPath, $screenshotsDir . DIRECTORY_SEPARATOR)
    ) {
        return null;
    }

    return $fullPath;
}
