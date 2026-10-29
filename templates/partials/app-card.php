<?php
declare(strict_types=1);
?>
<article
    class="app-card"
    aria-labelledby="app-title-<?= e($app->slug) ?>"
    data-category="<?= e($app->category->value) ?>"
    data-search="<?= e(mb_strtolower($app->name . ' ' . $app->tagline . ' ' . $app->description, 'UTF-8')) ?>"
>
    <div class="app-card__media">
        <img
            src="<?= screenshot($app->screenshot) ?>"
            alt="Tampilan aplikasi <?= e($app->name) ?>"
            width="1280"
            height="800"
            loading="lazy"
            decoding="async"
        >
        <?php if ($app->badge !== null && $app->badge !== ''): ?>
            <span class="badge"><?= e($app->badge) ?></span>
        <?php endif; ?>
    </div>

    <div class="app-card__body">
        <p class="app-card__category"><?= e($app->category->value) ?></p>
        <h3 class="app-card__title" id="app-title-<?= e($app->slug) ?>"><?= e($app->name) ?></h3>
        <p class="app-card__tagline"><?= e($app->tagline) ?></p>
        <p class="app-card__description"><?= e($app->description) ?></p>
        <a class="btn" href="<?= e($app->url) ?>" target="_blank" rel="noopener noreferrer">
            Buka Aplikasi <span aria-hidden="true">&#8599;</span>
        </a>
    </div>
</article>
