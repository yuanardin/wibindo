(() => {
    "use strict";

    const grid = document.getElementById("app-grid");
    const searchInput = document.getElementById("app-search");
    const clearButton = document.getElementById("search-clear");
    const emptyState = document.getElementById("empty-state");
    const resultCount = document.getElementById("result-count");
    const filterGroup = document.getElementById("category-filters");

    if (!grid || !searchInput || !emptyState || !filterGroup) {
        return;
    }

    const cards = Array.from(grid.querySelectorAll(".app-card"));
    const chips = Array.from(filterGroup.querySelectorAll("[data-category]"));

    let activeCategory = "all";
    let query = "";
    let debounceId = null;

    const normalize = (value) => value
        .toLocaleLowerCase("id-ID")
        .normalize("NFKD")
        .replace(/[\u0300-\u036f]/g, "")
        .trim();

    const applyFilters = () => {
        const normalizedQuery = normalize(query);
        let visible = 0;

        cards.forEach((card) => {
            const matchesCategory = activeCategory === "all"
                || card.dataset.category === activeCategory;
            const searchableText = normalize(card.dataset.search || "");
            const matchesSearch = normalizedQuery === ""
                || searchableText.includes(normalizedQuery);
            const show = matchesCategory && matchesSearch;

            card.hidden = !show;

            if (show) {
                visible += 1;
            }
        });

        emptyState.hidden = visible !== 0;

        if (clearButton) {
            clearButton.hidden = query.length === 0;
        }

        if (resultCount) {
            resultCount.textContent = visible === 0
                ? "Tidak ada aplikasi yang cocok."
                : `${visible} aplikasi ditampilkan.`;
        }
    };

    searchInput.addEventListener("input", () => {
        query = searchInput.value;
        window.clearTimeout(debounceId);
        debounceId = window.setTimeout(applyFilters, 150);
    });

    searchInput.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            searchInput.value = "";
            query = "";
            applyFilters();
        }
    });

    if (clearButton) {
        clearButton.addEventListener("click", () => {
            searchInput.value = "";
            query = "";
            searchInput.focus();
            applyFilters();
        });
    }

    chips.forEach((chip) => {
        chip.addEventListener("click", () => {
            activeCategory = chip.dataset.category || "all";

            chips.forEach((otherChip) => {
                const isActive = otherChip === chip;
                otherChip.classList.toggle("is-active", isActive);
                otherChip.setAttribute("aria-pressed", String(isActive));
            });

            applyFilters();
        });
    });

    applyFilters();
})();
