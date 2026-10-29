<?php
declare(strict_types=1);

require __DIR__ . '/../src/Category.php';
require __DIR__ . '/../src/App.php';
require __DIR__ . '/../src/helpers.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
header("Content-Security-Policy: default-src 'self'; base-uri 'none'; object-src 'none'; form-action 'none'; img-src 'self' data:; style-src 'self'; script-src 'self'; frame-ancestors 'none'");

/** @var App[] $apps */
$apps = require __DIR__ . '/../config/apps.php';

$pageTitle = 'Portal Web App Wibindo';
$pageDescription = 'Satu pintu masuk untuk menemukan dan membuka seluruh aplikasi kerja internal Wibindo.';
$companyName = 'Wibindo';
$itContactEmail = 'it@wibindo.local';

require __DIR__ . '/../templates/layout.php';
