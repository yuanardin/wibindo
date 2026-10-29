<?php
declare(strict_types=1);
?>
<footer class="site-footer">
    <div class="container site-footer__inner">
        <div class="site-footer__info">
            <p class="site-footer__brand"><?= e($companyName) ?> &mdash; Portal Web App</p>
            <p>Butuh bantuan akses? Hubungi tim IT di <a href="mailto:<?= e($itContactEmail) ?>"><?= e($itContactEmail) ?></a>.</p>
        </div>
        <p class="site-footer__legal">&copy; <?= e((string) date('Y')) ?> <?= e($companyName) ?>. Internal use only.</p>
    </div>
</footer>
