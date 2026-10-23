<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

$pageTitle = 'Privacy Policy | Nexus Educational Tours and Travels';
$pageDescription = 'Learn how Nexus Educational Tours and Travels handles enquiry information and protects visitor privacy.';

$lastReviewed = '23 October 2026';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#173B67">

    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/responsive.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/animations.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/legal.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="legal-page privacy-page">

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main id="main-content">

        <!-- Premium animated hero -->
        <section class="legal-hero" id="legal-hero">
            <div class="legal-hero-grid" aria-hidden="true"></div>
            <div class="legal-glow legal-glow-one" aria-hidden="true"></div>
            <div class="legal-glow legal-glow-two" aria-hidden="true"></div>

            <div class="legal-orbit legal-orbit-one" aria-hidden="true">
                <span class="legal-orbit-dot"></span>
            </div>
            <div class="legal-orbit legal-orbit-two" aria-hidden="true">
                <span class="legal-orbit-dot"></span>
            </div>

            <div class="container legal-hero-inner">
                <div class="legal-hero-copy" data-reveal>
                    <div class="legal-eyebrow">
                        <span class="legal-eyebrow-dot"></span>
                        YOUR PRIVACY MATTERS
                    </div>

                    <h1>
                        Privacy <span class="legal-gradient-text">Policy</span>
                    </h1>

                    <p class="legal-hero-description">
                        Your trust is an important part of every journey.
                        Learn how Nexus Educational Tours and Travels handles
                        the information you share with us.
                    </p>

                    <div class="legal-hero-meta">
                        <span class="legal-meta-icon" aria-hidden="true">✦</span>
                        <span>Transparency. Responsibility. Trust.</span>
                    </div>
                </div>

                <div class="legal-hero-visual" data-reveal aria-hidden="true">
                    <div class="legal-visual-ring legal-visual-ring-outer"></div>
                    <div class="legal-visual-ring legal-visual-ring-inner"></div>

                    <div class="legal-shield">
                        <div class="legal-shield-inner">
                            <svg viewBox="0 0 100 100" role="presentation">
                                <path d="M50 10 L80 22 V45 C80 65 67 80 50 90 C33 80 20 65 20 45 V22 Z"
                                    fill="none" stroke="currentColor" stroke-width="4"
                                    stroke-linejoin="round" />
                                <path d="M35 49 L45 59 L66 37"
                                    fill="none" stroke="currentColor" stroke-width="5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>

                    <div class="legal-float-card legal-float-card-top">
                        <span class="legal-float-icon">✓</span>
                        <span>Privacy first</span>
                    </div>

                    <div class="legal-float-card legal-float-card-bottom">
                        <span class="legal-float-icon legal-float-icon-orange">✦</span>
                        <span>Travel with trust</span>
                    </div>
                </div>
            </div>

            <div class="legal-hero-bottom-line" aria-hidden="true"></div>
        </section>

        <!-- Policy content -->
        <section class="legal-section">
            <div class="container legal-container">

                <div class="legal-page-intro" data-reveal>
                    <div>
                        <span class="legal-section-label">THE DETAILS</span>
                        <h2>How we respect your <span>privacy</span></h2>
                        <p>
                            This page explains the information we may collect,
                            why we may use it, and how you can contact us about it.
                        </p>
                    </div>

                    <div class="legal-reviewed">
                        <span class="legal-reviewed-icon" aria-hidden="true">◷</span>
                        <div>
                            <span>Last reviewed</span>
                            <strong><?= htmlspecialchars($lastReviewed, ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                    </div>
                </div>

                <div class="legal-notice" data-reveal>
                    <span class="legal-notice-icon" aria-hidden="true">i</span>
                    <div>
                        <strong>A note about this policy</strong>
                        <p>
                            This policy is a general description and must match the
                            information your website actually collects and how it is
                            handled. Please review it before publishing the website.
                        </p>
                    </div>
                </div>

                <div class="legal-layout">

                    <aside class="legal-sidebar" data-reveal>
                        <div class="legal-sidebar-heading">ON THIS PAGE</div>
                        <nav aria-label="Privacy policy sections">
                            <a href="#introduction" class="legal-nav-link is-active">01. Introduction</a>
                            <a href="#information" class="legal-nav-link">02. Information we collect</a>
                            <a href="#usage" class="legal-nav-link">03. How we use information</a>
                            <a href="#sharing" class="legal-nav-link">04. Information sharing</a>
                            <a href="#security" class="legal-nav-link">05. Data security</a>
                            <a href="#retention" class="legal-nav-link">06. Data retention</a>
                            <a href="#cookies" class="legal-nav-link">07. Cookies and links</a>
                            <a href="#rights" class="legal-nav-link">08. Your choices</a>
                            <a href="#children" class="legal-nav-link">09. Children’s privacy</a>
                            <a href="#contact" class="legal-nav-link">10. Contact us</a>
                        </nav>
                        <div class="legal-sidebar-accent"></div>
                    </aside>

                    <article class="legal-content">

                        <section class="legal-card" id="introduction" data-reveal>
                            <div class="legal-card-number">01</div>
                            <div class="legal-card-body">
                                <span class="legal-card-kicker">WELCOME</span>
                                <h2>Introduction</h2>
                                <p>
                                    Nexus Educational Tours and Travels respects your
                                    privacy. This Privacy Policy describes how we may
                                    collect, use, store and handle information when you
                                    visit our website or contact us about educational
                                    tours, school trips, college visits, industrial
                                    visits and other travel services.
                                </p>
                                <p>
                                    By using our website, you acknowledge that you
                                    have read this policy. Where consent is legally
                                    required, we will seek it separately.
                                </p>
                            </div>
                        </section>

                        <section class="legal-card" id="information" data-reveal>
                            <div class="legal-card-number">02</div>
                            <div class="legal-card-body">
                                <span class="legal-card-kicker">WHAT WE MAY RECEIVE</span>
                                <h2>Information we collect</h2>
                                <p>Depending on how you interact with our website, we may receive:</p>
                                <ul class="legal-list">
                                    <li>
                                        <strong>Contact details:</strong>
                                        your name, phone number and email address.
                                    </li>
                                    <li>
                                        <strong>Enquiry details:</strong>
                                        institution name, destination, starting location,
                                        preferred dates, group size and tour requirements
                                        that you choose to provide.
                                    </li>
                                    <li>
                                        <strong>Messages:</strong>
                                        information you include when contacting our team.
                                    </li>
                                    <li>
                                        <strong>Technical information:</strong>
                                        limited browser, device or server-log information
                                        that may be generated when the website is accessed.
                                    </li>
                                </ul>
                                <p>
                                    Please do not submit unnecessary sensitive personal
                                    information through general enquiry forms.
                                </p>
                            </div>
                        </section>

                        <section class="legal-card" id="usage" data-reveal>
                            <div class="legal-card-number">03</div>
                            <div class="legal-card-body">
                                <span class="legal-card-kicker">WHY WE USE IT</span>
                                <h2>How we use information</h2>
                                <p>Information provided to us may be used to:</p>
                                <ul class="legal-list">
                                    <li>Respond to questions and tour enquiries.</li>
                                    <li>Prepare itineraries, quotations and travel arrangements.</li>
                                    <li>Communicate about a request or service.</li>
                                    <li>Maintain website operations and help prevent misuse.</li>
                                    <li>Meet applicable legal or record-keeping obligations.</li>
                                </ul>
                                <p>
                                    We should only use personal information for purposes
                                    that are appropriate, lawful and consistent with the
                                    information provided to visitors.
                                </p>
                            </div>
                        </section>

                        <section class="legal-card" id="sharing" data-reveal>
                            <div class="legal-card-number">04</div>
                            <div class="legal-card-body">
                                <span class="legal-card-kicker">LIMITED DISCLOSURE</span>
                                <h2>Information sharing</h2>
                                <p>
                                    We do not intend to sell personal information.
                                    Information may be shared when reasonably necessary
                                    to respond to your request, operate the service,
                                    comply with law or protect legitimate rights.
                                </p>
                                <p>
                                    For example, if a requested trip requires coordination
                                    with a transport, accommodation or activity provider,
                                    relevant details may need to be shared with that
                                    provider. Any such sharing should be limited to what
                                    is necessary for the arrangement.
                                </p>
                                <p>
                                    Only describe third-party sharing here if it reflects
                                    your actual business practices.
                                </p>
                            </div>
                        </section>

                        <section class="legal-card" id="security" data-reveal>
                            <div class="legal-card-number">05</div>
                            <div class="legal-card-body">
                                <span class="legal-card-kicker">SAFE HANDLING</span>
                                <h2>Data security</h2>
                                <p>
                                    We aim to use reasonable safeguards to protect
                                    information against unauthorised access, alteration,
                                    disclosure or loss. No website or electronic
                                    transmission can be guaranteed to be completely secure.
                                </p>
                                <p>
                                    Access to enquiry records should be limited to
                                    authorised people who need the information to
                                    perform their responsibilities.
                                </p>
                            </div>
                        </section>

                        <section class="legal-card" id="retention" data-reveal>
                            <div class="legal-card-number">06</div>
                            <div class="legal-card-body">
                                <span class="legal-card-kicker">RECORD MANAGEMENT</span>
                                <h2>Data retention</h2>
                                <p>
                                    Enquiry information may be retained for as long as
                                    necessary to respond to requests, manage services,
                                    maintain appropriate business records or comply with
                                    applicable legal requirements. Retention periods
                                    should reflect the actual needs and obligations of
                                    the business.
                                </p>
                            </div>
                        </section>

                        <section class="legal-card" id="cookies" data-reveal>
                            <div class="legal-card-number">07</div>
                            <div class="legal-card-body">
                                <span class="legal-card-kicker">WEBSITE EXPERIENCE</span>
                                <h2>Cookies and external links</h2>
                                <p>
                                    The website may use essential technical features
                                    required for its operation. If analytics, advertising
                                    or other non-essential tracking tools are added,
                                    this policy should be updated to describe them and
                                    any consent choices that apply.
                                </p>
                                <p>
                                    Our website may contain links to external websites.
                                    Those websites operate under their own privacy
                                    practices, and we encourage you to review their
                                    policies before sharing information with them.
                                </p>
                            </div>
                        </section>

                        <section class="legal-card" id="rights" data-reveal>
                            <div class="legal-card-number">08</div>
                            <div class="legal-card-body">
                                <span class="legal-card-kicker">YOUR OPTIONS</span>
                                <h2>Your choices and requests</h2>
                                <p>
                                    You may contact us to ask about personal information
                                    you have submitted, request a correction, or raise
                                    a privacy concern. We may need to verify your request
                                    before taking action and may retain certain records
                                    where required by law.
                                </p>
                                <p>
                                    We will handle such requests in accordance with
                                    applicable law and our actual record-management
                                    procedures.
                                </p>
                            </div>
                        </section>

                        <section class="legal-card" id="children" data-reveal>
                            <div class="legal-card-number">09</div>
                            <div class="legal-card-body">
                                <span class="legal-card-kicker">YOUNG TRAVELLERS</span>
                                <h2>Children’s privacy</h2>
                                <p>
                                    Our services may involve school and college groups.
                                    Please ensure that any information about students
                                    or minors is shared only by an authorised person
                                    and only when necessary for the requested service.
                                </p>
                                <p>
                                    Do not submit student lists, identity documents,
                                    medical details or other sensitive information
                                    through a general website enquiry form unless a
                                    secure and appropriate process has been arranged.
                                </p>
                            </div>
                        </section>

                        <section class="legal-card legal-contact-card" id="contact" data-reveal>
                            <div class="legal-card-number">10</div>
                            <div class="legal-card-body">
                                <span class="legal-card-kicker">WE ARE HERE TO HELP</span>
                                <h2>Contact us about privacy</h2>
                                <p>
                                    If you have a question about this policy or a
                                    privacy-related request, contact Nexus Educational
                                    Tours and Travels using the contact details below.
                                </p>

                                <div class="legal-contact-actions">
                                    <a class="legal-contact-link"
                                        href="<?= BASE_URL ?>pages/contact.php">
                                        <span aria-hidden="true">✉</span>
                                        Contact our team
                                        <span aria-hidden="true">↗</span>
                                    </a>

                                    <a class="legal-contact-link legal-contact-link-secondary"
                                        href="<?= BASE_URL ?>index.php#enquiry">
                                        <span aria-hidden="true">↗</span>
                                        Send an enquiry
                                    </a>
                                </div>
                            </div>
                        </section>

                    </article>
                </div>

                <div class="legal-bottom-cta" data-reveal>
                    <div class="legal-cta-orb" aria-hidden="true"></div>
                    <div class="legal-cta-content">
                        <span class="legal-section-label">LET'S PLAN WITH CONFIDENCE</span>
                        <h2>Questions? We're happy to help.</h2>
                        <p>
                            Get in touch with our team if you need clarification
                            before submitting a tour enquiry.
                        </p>
                    </div>
                    <a href="<?= BASE_URL ?>pages/contact.php" class="legal-cta-button">
                        Contact Nexus <span aria-hidden="true">→</span>
                    </a>
                </div>

            </div>
        </section>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="<?= BASE_URL ?>js/main.js"></script>
    <script src="<?= BASE_URL ?>js/legal.js"></script>
</body>

</html>