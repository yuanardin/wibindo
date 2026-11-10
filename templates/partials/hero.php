<?php
declare(strict_types=1);

$categoryIcon = static fn (Category $category): string => match ($category) {
    Category::Warehouse => '<path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
    Category::Production => '<path d="M4 20V10"/><path d="M10 20V4"/><path d="M16 20v-7"/><path d="M22 20H2"/>',
    Category::Assets => '<rect width="20" height="5" x="2" y="3" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/>',
    Category::Media => '<rect width="20" height="8" x="2" y="2" rx="2"/><rect width="20" height="8" x="2" y="14" rx="2"/><path d="M6 6h.01"/><path d="M6 18h.01"/>',
    Category::Outlet => '<path d="M4 10v10h16V10"/><path d="M3 10 5 4h14l2 6"/><path d="M3 10a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"/><path d="M9 20v-5h6v5"/>',
};
?>
<section class="hero" aria-labelledby="hero-title">
    <div class="container hero__inner">
        <p class="eyebrow hero__eyebrow">
            <span class="eyebrow__line" aria-hidden="true"></span>
            <span>Satu portal, akses semua</span>
        </p>
        <h1 id="hero-title">Portal Aplikasi <span class="hero__title-line">Anda, <span class="hero__accent">satu tempat.</span></span></h1>
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
        <p class="search__help visually-hidden" id="search-help">Cari berdasarkan nama, tagline, atau deskripsi aplikasi.</p>

        <div class="filters" id="category-filters" role="group" aria-label="Filter kategori">
            <button type="button" class="chip is-active" data-category="all" aria-pressed="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m3 10.5 9-7.5 9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9 21v-6h6v6"/>
                </svg>
                <span>Semua</span>
            </button>
            <?php foreach (Category::cases() as $category): ?>
                <button
                    type="button"
                    class="chip"
                    data-category="<?= e($category->value) ?>"
                    aria-pressed="false"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <?= $categoryIcon($category) ?>
                    </svg>
                    <span><?= e($category->value) ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>
