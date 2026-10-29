<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <meta name="robots" content="noindex, nofollow">
    <meta name="color-scheme" content="light dark">
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script src="<?= asset('js/app.js') ?>" defer></script>
</head>
<body>
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>

    <?php require __DIR__ . '/partials/header.php'; ?>

    <main id="main-content" tabindex="-1">
        <?php require __DIR__ . '/partials/hero.php'; ?>

        <section class="directory container" aria-labelledby="directory-title">
            <div class="directory__heading">
                <p class="eyebrow">Direktori Aplikasi</p>
                <h2 id="directory-title">Akses cepat</h2>
                <p>Pilih kategori atau cari aplikasi yang Anda butuhkan.</p>
            </div>

            <div class="app-grid" id="app-grid">
                <?php foreach ($apps as $app): ?>
                    <?php require __DIR__ . '/partials/app-card.php'; ?>
                <?php endforeach; ?>
            </div>

            <p class="empty-state" id="empty-state" hidden>Aplikasi tidak ditemukan. Coba kata kunci lain.</p>
            <p class="visually-hidden" id="result-count" role="status" aria-live="polite"></p>
        </section>
    </main>

    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
