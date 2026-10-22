
/* =========================================================
   NEXUS FAQ — SEARCH, FILTERS, ACCORDION & MOTION
   File: js/faq.js
========================================================= */

document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.faq-page');

    if (!page) return;

    const faqList = page.querySelector('#faqList');
    const searchInput = page.querySelector('#faqSearch');
    const filterButtons = [
        ...page.querySelectorAll('[data-faq-filter]')
    ];
    const results = page.querySelector('#faqResults');
    const resetButton = page.querySelector('#faqReset');
    const emptyResetButton = page.querySelector('#faqEmptyReset');
    const emptyState = page.querySelector('#faqEmpty');

    if (!faqList) return;

    const items = [...faqList.querySelectorAll('.faq-item')];

    let activeCategory = 'all';

    /* -----------------------------------------
       Search and category filtering
    ----------------------------------------- */

    function filterFAQs() {
        const query = (searchInput?.value || '')
            .trim()
            .toLocaleLowerCase();

        let visibleCount = 0;

        items.forEach((item) => {
            const category = item.dataset.category || 'all';
            const questionText = item.textContent.toLocaleLowerCase();

            const matchesCategory =
                activeCategory === 'all' ||
                category === activeCategory;

            const matchesSearch =
                query.length === 0 ||
                questionText.includes(query);

            const isVisible = matchesCategory && matchesSearch;

            item.hidden = !isVisible;

            if (isVisible) {
                visibleCount += 1;
            } else {
                item.open = false;
            }
        });

        if (results) {
            if (visibleCount === 0) {
                results.textContent =
                    'No matching questions found. Try another search.';
            } else if (query || activeCategory !== 'all') {
                results.textContent =
                    `${visibleCount} question${visibleCount === 1 ? '' : 's'} found.`;
            } else {
                results.textContent =
                    'Explore answers to common questions.';
            }
        }

        if (emptyState) {
            emptyState.hidden = visibleCount !== 0;
        }

        faqList.hidden = visibleCount === 0;
    }

    searchInput?.addEventListener('input', filterFAQs);

    /* -----------------------------------------
       Category filter buttons
    ----------------------------------------- */

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeCategory = button.dataset.faqFilter || 'all';

            filterButtons.forEach((otherButton) => {
                const active = otherButton === button;

                otherButton.classList.toggle('is-active', active);
                otherButton.setAttribute('aria-pressed', String(active));
            });

            filterFAQs();
        });
    });

    /* -----------------------------------------
       Reset search and filters
    ----------------------------------------- */

    function resetFilters() {
        if (searchInput) {
            searchInput.value = '';
        }

        activeCategory = 'all';

        filterButtons.forEach((button) => {
            const active = button.dataset.faqFilter === 'all';

            button.classList.toggle('is-active', active);
            button.setAttribute('aria-pressed', String(active));
        });

        items.forEach((item) => {
            item.open = false;
        });

        filterFAQs();
    }

    resetButton?.addEventListener('click', resetFilters);
    emptyResetButton?.addEventListener('click', resetFilters);

    /* -----------------------------------------
       Single-open accordion
    ----------------------------------------- */

    items.forEach((item) => {
        item.addEventListener('toggle', () => {
            if (!item.open) return;

            items.forEach((otherItem) => {
                if (otherItem !== item && otherItem.open) {
                    otherItem.open = false;
                }
            });
        });
    });

    /* -----------------------------------------
       Scroll reveal animation
    ----------------------------------------- */

    const revealElements = [
        ...page.querySelectorAll('[data-faq-reveal]')
    ];

    const reduceMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    ).matches;

    if ('IntersectionObserver' in window && !reduceMotion) {
        document.documentElement.classList.add('js-faq-motion');

        const observer = new IntersectionObserver(
            (entries, currentObserver) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;

                    entry.target.classList.add('is-visible');
                    currentObserver.unobserve(entry.target);
                });
            },
            {
                threshold: 0.08,
                rootMargin: '0px 0px -25px 0px'
            }
        );

        revealElements.forEach((element) => {
            observer.observe(element);
        });
    } else {
        revealElements.forEach((element) => {
            element.classList.add('is-visible');
        });
    }

    /* -----------------------------------------
       Initial page state
    ----------------------------------------- */

    filterFAQs();
});
