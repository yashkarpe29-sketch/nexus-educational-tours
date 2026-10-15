<?php
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FAQs | Nexus Educational Tours and Travels</title>
    <meta name="description" content="Find answers to common questions about educational tours, school trips, college tours and tour planning with Nexus Educational Tours and Travels in Chakan, Pune.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/responsive.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/animations.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/faq.css">
</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>
        <section class="page-hero">
            <div class="container">
                <span class="section-eyebrow">HELP &amp; INFORMATION</span>
                <h1>Frequently Asked <span>Questions</span></h1>
                <p>
                    Have questions about planning an educational tour?
                    Find helpful information below, or contact our team for assistance.
                </p>
                <a href="<?= BASE_URL ?>pages/contact.php" class="btn btn-primary">
                    Contact Our Team
                </a>
            </div>
        </section>

        <section class="faq-section section-padding">
            <div class="container">
                <div class="section-header">
                    <span class="section-eyebrow">YOUR QUESTIONS, ANSWERED</span>
                    <h2>Everything You Need to Know</h2>
                    <p>Every tour is different. Contact us to discuss your institution's requirements.</p>
                </div>

                <div class="faq-list">
                    <details class="faq-item">
                        <summary>Who can enquire about an educational tour?</summary>
                        <div class="faq-answer">
                            <p>Schools, colleges, universities, coaching classes, institutions, teachers and authorised group representatives can contact us to discuss their tour requirements.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>Can we request a customised tour?</summary>
                        <div class="faq-answer">
                            <p>Yes, you can share your group's interests, preferred dates, group size and requirements with us. The itinerary and arrangements can then be discussed based on your needs.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>How do we enquire about a tour?</summary>
                        <div class="faq-answer">
                            <p>Visit our Contact page or reach out through WhatsApp. Share your institution or group details, approximate number of participants and preferred travel dates.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>Can we discuss transportation, accommodation and meals?</summary>
                        <div class="faq-answer">
                            <p>Yes. Tell us which arrangements your group requires. The availability, inclusions and costs can be discussed while planning your tour.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>How can we know the tour cost?</summary>
                        <div class="faq-answer">
                            <p>The cost depends on your group size, destination, duration, travel dates and required arrangements. Contact us with these details to discuss a quotation.</p>
                        </div>
                    </details>

                    <details class="faq-item">
                        <summary>How can we contact Nexus Educational Tours and Travels?</summary>
                        <div class="faq-answer">
                            <p>
                                Call us on
                                <a href="tel:7219766277">7219766277</a>
                                or
                                <a href="tel:7219493164">7219493164</a>.
                                You can also email
                                <a href="mailto:nexuseducationaltoursandtravel@gmail.com">nexuseducationaltoursandtravel@gmail.com</a>.
                            </p>
                        </div>
                    </details>
                </div>

                <div class="faq-contact-cta">
                    <h2>Still Have Questions?</h2>
                    <p>Tell us about your group's requirements, and we'll discuss the next steps with you.</p>
                    <a href="<?= BASE_URL ?>pages/contact.php" class="btn btn-primary">
                        Get in Touch
                    </a>
                    <a href="https://wa.me/917448228001"
                        class="btn btn-secondary"
                        target="_blank"
                        rel="noopener noreferrer">
                        WhatsApp Us
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="<?= BASE_URL ?>js/main.js"></script>
    <script src="<?= BASE_URL ?>js/faq.js"></script>
</body>

</html>