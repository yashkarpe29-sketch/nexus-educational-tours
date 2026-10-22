<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

$pageTitle = 'FAQs | Nexus Educational Tours and Travels';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="Find answers to common questions about educational tours, school trips, college visits, customised travel, tour costs and planning with Nexus Educational Tours and Travels in Chakan, Pune.">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#173B67">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/responsive.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/animations.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/faq.css">
</head>

<body class="faq-page">

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main id="main-content">

        <!-- Premium Hero -->
        <section class="faq-hero">
            <div class="faq-hero-orb faq-hero-orb-one" aria-hidden="true"></div>
            <div class="faq-hero-orb faq-hero-orb-two" aria-hidden="true"></div>
            <div class="faq-hero-grid" aria-hidden="true"></div>

            <div class="container faq-hero-layout">
                <div class="faq-hero-copy">
                    <span class="faq-eyebrow">
                        <span class="faq-eyebrow-dot"></span>
                        HELP &amp; INFORMATION
                    </span>

                    <h1>
                        Your questions.
                        <span>Your journey.</span>
                        Our guidance.
                    </h1>

                    <p>
                        Planning an educational tour should feel exciting,
                        not complicated. Find answers to common questions
                        and discover how Nexus can help you plan your group's journey.
                    </p>

                    <div class="faq-hero-actions">
                        <a href="#faq-explorer" class="faq-btn faq-btn-primary">
                            Explore FAQs
                            <span aria-hidden="true">↓</span>
                        </a>

                        <a href="<?= BASE_URL ?>pages/contact.php" class="faq-btn faq-btn-glass">
                            Contact Our Team
                            <span aria-hidden="true">↗</span>
                        </a>
                    </div>

                    <div class="faq-hero-note">
                        <span class="faq-note-icon" aria-hidden="true">✦</span>
                        <span>School trips · College tours · Custom journeys</span>
                    </div>
                </div>

                <div class="faq-hero-visual" aria-label="Educational travel planning">
                    <div class="faq-visual-ring faq-visual-ring-outer"></div>
                    <div class="faq-visual-ring faq-visual-ring-inner"></div>

                    <div class="faq-visual-card">
                        <span class="faq-visual-kicker">YOUR NEXT JOURNEY</span>

                        <div class="faq-visual-icon" aria-hidden="true">
                            <svg viewBox="0 0 120 120" fill="none">
                                <circle cx="60" cy="60" r="48" stroke="currentColor" stroke-opacity=".15" stroke-width="1.5" />
                                <circle cx="60" cy="60" r="34" stroke="currentColor" stroke-opacity=".22" stroke-width="1.5" stroke-dasharray="4 5" />
                                <path d="M60 25L70 61L60 55L50 61L60 25Z" fill="#F4A340" />
                                <path d="M60 95L50 59L60 65L70 59L60 95Z" fill="#2B83C6" />
                                <circle cx="60" cy="60" r="5" fill="#173B67" />
                            </svg>
                        </div>

                        <h2>Every question leads somewhere.</h2>
                        <p>Get clarity before your group sets off.</p>

                        <div class="faq-visual-route">
                            <span>LEARN</span>
                            <span class="faq-route-line"></span>
                            <span>EXPLORE</span>
                            <span class="faq-route-line"></span>
                            <span>DISCOVER</span>
                        </div>
                    </div>

                    <div class="faq-floating-tag faq-floating-tag-top">
                        <span class="faq-tag-icon" aria-hidden="true">✦</span>
                        <span>Plan with confidence</span>
                    </div>

                    <div class="faq-floating-tag faq-floating-tag-bottom">
                        <span class="faq-tag-status"></span>
                        <span>Made for your group</span>
                    </div>
                </div>
            </div>

            <div class="faq-hero-bottom-line" aria-hidden="true"></div>
        </section>

        <!-- FAQ Explorer -->
        <section class="faq-section" id="faq-explorer">
            <div class="container">

                <header class="faq-section-heading" data-faq-reveal>
                    <span class="faq-section-eyebrow">YOUR QUESTIONS, ANSWERED</span>

                    <h2>
                        Everything you need
                        <span>to know.</span>
                    </h2>

                    <p>
                        From choosing a tour to planning the details,
                        find helpful information for your next group journey.
                    </p>
                </header>

                <!-- Search and category controls -->
                <div class="faq-tools" data-faq-reveal>
                    <label class="faq-search" for="faqSearch">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.8" />
                            <path d="M16 16L21 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>

                        <input
                            type="search"
                            id="faqSearch"
                            placeholder="Search your question..."
                            autocomplete="off"
                            aria-label="Search frequently asked questions">

                        <span class="faq-search-shortcut" aria-hidden="true">SEARCH</span>
                    </label>

                    <div class="faq-filters" role="group" aria-label="Filter questions by category">
                        <button type="button" class="faq-filter is-active" data-faq-filter="all" aria-pressed="true">
                            All Questions
                        </button>

                        <button type="button" class="faq-filter" data-faq-filter="planning" aria-pressed="false">
                            Tour Planning
                        </button>

                        <button type="button" class="faq-filter" data-faq-filter="cost" aria-pressed="false">
                            Cost &amp; Booking
                        </button>

                        <button type="button" class="faq-filter" data-faq-filter="services" aria-pressed="false">
                            Services
                        </button>
                    </div>

                    <div class="faq-list-meta">
                        <p id="faqResults" class="faq-results" aria-live="polite">
                            Explore answers to common questions.
                        </p>

                        <button type="button" id="faqReset" class="faq-reset">
                            Reset filters
                        </button>
                    </div>
                </div>

                <!-- Questions -->
                <div class="faq-content-layout">

                    <aside class="faq-side-note" data-faq-reveal>
                        <span class="faq-side-icon" aria-hidden="true">?</span>
                        <span class="faq-side-label">NEED A QUICK ANSWER?</span>

                        <h3>Every group is different.</h3>

                        <p>
                            Tell us about your institution, group size,
                            preferred destination or travel requirements.
                            We'll help you understand the next steps.
                        </p>

                        <a href="<?= BASE_URL ?>pages/contact.php" class="faq-side-link">
                            Talk to our team <span aria-hidden="true">↗</span>
                        </a>

                        <div class="faq-side-decoration" aria-hidden="true">N</div>
                    </aside>

                    <div class="faq-main-column">
                        <div class="faq-list" id="faqList">

                            <details class="faq-item" data-category="planning" data-faq-reveal>
                                <summary>
                                    <span class="faq-question-number">01</span>
                                    <span class="faq-question-text">Who can enquire about an educational tour?</span>
                                    <span class="faq-toggle" aria-hidden="true"></span>
                                </summary>

                                <div class="faq-answer">
                                    <p>
                                        Schools, colleges, universities, coaching classes,
                                        educational institutions, teachers and authorised
                                        group representatives can contact Nexus to discuss
                                        their educational tour requirements.
                                    </p>
                                </div>
                            </details>

                            <details class="faq-item" data-category="planning" data-faq-reveal>
                                <summary>
                                    <span class="faq-question-number">02</span>
                                    <span class="faq-question-text">Can we request a customised tour?</span>
                                    <span class="faq-toggle" aria-hidden="true"></span>
                                </summary>

                                <div class="faq-answer">
                                    <p>
                                        Yes. Share your group's interests, preferred dates,
                                        approximate group size, destination preferences and
                                        other requirements. The itinerary and arrangements
                                        can then be discussed according to your needs.
                                    </p>

                                    <a href="<?= BASE_URL ?>pages/contact.php" class="faq-answer-link">
                                        Discuss a customised tour <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </details>

                            <details class="faq-item" data-category="planning" data-faq-reveal>
                                <summary>
                                    <span class="faq-question-number">03</span>
                                    <span class="faq-question-text">How do we enquire about a tour?</span>
                                    <span class="faq-toggle" aria-hidden="true"></span>
                                </summary>

                                <div class="faq-answer">
                                    <p>
                                        Visit our Contact page and share your institution
                                        or group details, approximate number of participants,
                                        preferred travel dates and destination. Our team can
                                        discuss your requirements and the next steps.
                                    </p>

                                    <a href="<?= BASE_URL ?>pages/contact.php" class="faq-answer-link">
                                        Open Contact Page <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </details>

                            <details class="faq-item" data-category="services" data-faq-reveal>
                                <summary>
                                    <span class="faq-question-number">04</span>
                                    <span class="faq-question-text">Can we discuss transportation, accommodation and meals?</span>
                                    <span class="faq-toggle" aria-hidden="true"></span>
                                </summary>

                                <div class="faq-answer">
                                    <p>
                                        Yes. Let us know which arrangements your group
                                        requires. Transportation, accommodation, meals and
                                        other inclusions can be discussed during planning,
                                        subject to availability and the agreed itinerary.
                                    </p>
                                </div>
                            </details>

                            <details class="faq-item" data-category="cost" data-faq-reveal>
                                <summary>
                                    <span class="faq-question-number">05</span>
                                    <span class="faq-question-text">How can we know the tour cost?</span>
                                    <span class="faq-toggle" aria-hidden="true"></span>
                                </summary>

                                <div class="faq-answer">
                                    <p>
                                        The cost depends on the destination, duration,
                                        travel dates, group size and required arrangements.
                                        Contact our team with these details to discuss
                                        a quotation for your group.
                                    </p>

                                    <a href="<?= BASE_URL ?>pages/contact.php" class="faq-answer-link">
                                        Request a discussion <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </details>

                            <details class="faq-item" data-category="services" data-faq-reveal>
                                <summary>
                                    <span class="faq-question-number">06</span>
                                    <span class="faq-question-text">How can we contact Nexus Educational Tours and Travels?</span>
                                    <span class="faq-toggle" aria-hidden="true"></span>
                                </summary>

                                <div class="faq-answer">
                                    <p>
                                        Call us at
                                        <a href="tel:7219766277">7219766277</a>
                                        or
                                        <a href="tel:7219493164">7219493164</a>.
                                        You can also email us at
                                        <a href="mailto:nexuseducationaltoursandtravel@gmail.com">
                                            nexuseducationaltoursandtravel@gmail.com
                                        </a>,
                                        or send your enquiry through our Contact page.
                                    </p>

                                    <a href="<?= BASE_URL ?>pages/contact.php" class="faq-answer-link">
                                        Contact our team <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </details>

                        </div>

                        <div class="faq-empty" id="faqEmpty" hidden>
                            <span class="faq-empty-icon" aria-hidden="true">⌕</span>
                            <h3>No matching questions</h3>
                            <p>Try a different keyword or reset your filters.</p>

                            <button type="button" id="faqEmptyReset" class="faq-btn faq-btn-dark">
                                Show all questions
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contact CTA -->
                <section class="faq-contact-cta" data-faq-reveal>
                    <div class="faq-cta-decoration faq-cta-decoration-one" aria-hidden="true"></div>
                    <div class="faq-cta-decoration faq-cta-decoration-two" aria-hidden="true"></div>

                    <div class="faq-cta-content">
                        <span class="faq-cta-eyebrow">
                            <span class="faq-eyebrow-dot"></span>
                            YOUR JOURNEY STARTS HERE
                        </span>

                        <h2>
                            Still have questions?
                            <span>Let's talk.</span>
                        </h2>

                        <p>
                            Tell us about your institution, your group and your travel
                            goals. Let's discuss an educational journey that fits your needs.
                        </p>

                        <div class="faq-cta-actions">
                            <a href="<?= BASE_URL ?>pages/contact.php" class="faq-btn faq-btn-primary">
                                Contact Our Team <span aria-hidden="true">↗</span>
                            </a>

                            <a href="https://wa.me/917448228001"
                                class="faq-btn faq-btn-whatsapp"
                                target="_blank"
                                rel="noopener noreferrer">
                                <span class="faq-whatsapp-dot" aria-hidden="true"></span>
                                Chat on WhatsApp
                            </a>
                        </div>

                        <div class="faq-cta-contact">
                            <a href="tel:7219766277">Call: 7219766277</a>
                            <span aria-hidden="true">·</span>
                            <a href="mailto:nexuseducationaltoursandtravel@gmail.com">Email our team</a>
                        </div>
                    </div>
                </section>

            </div>
        </section>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="<?= BASE_URL ?>js/main.js"></script>
    <script src="<?= BASE_URL ?>js/faq.js" defer></script>

</body>

</html>