<?php
declare(strict_types=1);
?>
<section class="hero" aria-labelledby="hero-title">
    <div class="container hero__inner">
        <p class="eyebrow">Satu portal, akses semua</p>
        <h1 id="hero-title">Semua aplikasi kerja Anda, satu tempat.</h1>
        <p class="hero__subtitle">Pilih aplikasi di bawah untuk mulai bekerja.</p>

        <div class="search">
            <svg class="search__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="m20 20-3.5-3.5"></path>
            </svg>
            <label class="visually-hidden" for="app-search">Cari aplikasi</label>
            <input
                id="app-search"
                type="search"
                placeholder="Cari aplikasi"
                autocomplete="off"
                spellcheck="false"
                aria-describedby="search-help"
            >
            <button type="button" class="search__clear" id="search-clear" hidden aria-label="Hapus pencarian">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6 6 18M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <p class="search__help" id="search-help">Cari berdasarkan nama, tagline, atau deskripsi aplikasi.</p>

        <div class="filters" id="category-filters" role="group" aria-label="Filter kategori">
            <button type="button" class="chip is-active" data-category="all" aria-pressed="true">Semua</button>
            <?php foreach (Category::cases() as $category): ?>
                <button
                    type="button"
                    class="chip"
                    data-category="<?= e($category->value) ?>"
                    aria-pressed="false"
                ><?= e($category->value) ?></button>
            <?php endforeach; ?>
        </div>
    </div>
</section>
