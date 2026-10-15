
document.addEventListener('DOMContentLoaded', function () {
    const faqItems = document.querySelectorAll('.faq-list .faq-item');

    faqItems.forEach(function (item) {
        item.addEventListener('toggle', function () {
            if (item.open) {
                faqItems.forEach(function (otherItem) {
                    if (otherItem !== item) {
                        otherItem.open = false;
                    }
                });
            }
        });
    });
});
