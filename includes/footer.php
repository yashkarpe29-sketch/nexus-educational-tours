<?php
require_once __DIR__ . '/../config.php';
?>

<footer class="site-footer">
    <div class="footer-top-line"></div>

    <div class="container">
        <div class="footer-main">

            <!-- Brand -->
            <div class="footer-brand">
                <a href="<?= BASE_URL ?>index.php" class="footer-logo">
                    <div class="footer-logo-mark">N</div>

                    <div>
                        <strong>NEXUS</strong>
                        <span>Educational Tours &amp; Travels</span>
                    </div>
                </a>

                <p>We create experiences, not just tours.</p>

                <span class="footer-established">
                    Established 2025
                </span>
            </div>

            <!-- Navigation -->
            <div class="footer-column">
                <h3>Explore</h3>

                <a href="<?= BASE_URL ?>index.php">Home</a>
                <a href="<?= BASE_URL ?>pages/about.php">About</a>
                <a href="<?= BASE_URL ?>pages/tours.php">Tours</a>
                <a href="<?= BASE_URL ?>pages/services.php">Services</a>
                <a href="<?= BASE_URL ?>pages/faq.php">FAQ</a>
            </div>

            <!-- Useful Links -->
            <div class="footer-column">
                <h3>Journey</h3>

                <a href="<?= BASE_URL ?>index.php#enquiry">
                    Plan Your Tour
                </a>

                <a href="<?= BASE_URL ?>index.php#gallery">
                    Gallery
                </a>

                <a href="<?= BASE_URL ?>index.php#testimonials">
                    Experiences
                </a>

                <a href="<?= BASE_URL ?>pages/contact.php">
                    Contact
                </a>
            </div>

            <!-- Contact -->
            <div class="footer-column footer-contact">
                <h3>Connect</h3>

                <a href="tel:7219766277">
                    7219766277
                </a>

                <a href="tel:7219493164">
                    7219493164
                </a>

                <a href="mailto:nexuseducationaltoursandtravel@gmail.com">
                    nexuseducationaltoursandtravel@gmail.com
                </a>

                <span>
                    Ekatanagar, Chakan,<br>
                    Tal-Khed, Pune,<br>
                    Maharashtra - 410501
                </span>
            </div>

        </div>

        <!-- Footer Journey Line -->
        <div class="footer-journey">
            <span class="footer-journey-line"></span>

            <span class="footer-journey-dot"></span>
            <span class="footer-journey-text">EXPLORE</span>

            <span class="footer-journey-dot"></span>
            <span class="footer-journey-text">LEARN</span>

            <span class="footer-journey-dot"></span>
            <span class="footer-journey-text">EXPERIENCE</span>

            <span class="footer-journey-line"></span>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <p>
                &copy; <?= date('Y') ?> Nexus Educational Tours and Travels.
                All rights reserved.
            </p>

            <!-- Legal Links -->
            <div class="footer-legal">

                <a href="<?= BASE_URL ?>pages/privacy-policy.php">
                    Privacy Policy
                </a>

                <a href="<?= BASE_URL ?>pages/terms-and-conditions.php">
                    Terms &amp; Conditions
                </a>

            </div>
        </div>

    </div>
</footer>