<?php
require_once __DIR__ . '/../config.php';
?>

<header class="site-header" id="site-header">
    <div class="container">
        <nav class="navbar" aria-label="Main navigation">

            <!-- Logo / Brand -->
            <a
                href="<?= BASE_URL ?>index.php"
                class="navbar-brand"
                aria-label="Nexus Educational Tours and Travels Home">

                <div class="brand-logo">
                    <img
                        src="<?= BASE_URL ?>assets/images/logo/logo.png"
                        alt="Nexus Educational Tours and Travels Logo" />
                </div>

                <div class="brand-text">
                    <span class="brand-name">Nexus</span>
                    <span class="brand-subtitle">
                        Educational Tours &amp; Travels
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <div class="navbar-menu" id="navbar-menu">

                <a href="<?= BASE_URL ?>index.php" class="nav-link">
                    Home
                </a>

                <a href="<?= BASE_URL ?>pages/about.php" class="nav-link">
                    About
                </a>

                <a href="<?= BASE_URL ?>pages/tours.php" class="nav-link">
                    Tours
                </a>

                <a href="<?= BASE_URL ?>pages/services.php" class="nav-link">
                    Services
                </a>

                <a href="<?= BASE_URL ?>pages/faq.php" class="nav-link">
                    FAQ
                </a>

                <a href="<?= BASE_URL ?>pages/contact.php" class="nav-link">
                    Contact
                </a>

                <a
                    href="<?= BASE_URL ?>index.php#enquiry"
                    class="btn btn-primary nav-cta">

                    Plan Your Tour
                    <span aria-hidden="true">→</span>
                </a>

            </div>

            <!-- Mobile Menu Button -->
            <button
                class="navbar-toggle"
                id="navbar-toggle"
                type="button"
                aria-label="Open navigation menu"
                aria-controls="navbar-menu"
                aria-expanded="false">

                <span></span>
                <span></span>
                <span></span>
            </button>

        </nav>
    </div>
</header>