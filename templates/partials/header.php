<?php
declare(strict_types=1);
?>
<header class="site-header">
    <div class="container site-header__bar">
        <a class="brand" href="/" aria-label="<?= e($companyName) ?> - beranda">
            <img src="<?= asset('img/logo.svg') ?>" width="40" height="40" alt="">
            <span class="brand__text">
                <strong><?= e($companyName) ?></strong>
                <span>Portal Web App</span>
            </span>
        </a>
        <span class="status">
            <span class="status__dot" aria-hidden="true"></span>
            Akses internal
        </span>
    </div>
</header>