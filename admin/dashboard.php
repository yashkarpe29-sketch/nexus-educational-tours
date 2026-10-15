<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

requireAdmin();

require_once __DIR__ . '/../config/database.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$adminUsername = (string) ($_SESSION['admin_username'] ?? 'Admin');

$stats = [
    'contacts' => 0,
    'tours' => 0,
    'published_tours' => 0,
    'tour_enquiries' => 0,
    'gallery' => 0,
    'reviews' => 0,
];

$databaseAvailable = true;

try {
    $stats['contacts'] = (int) $pdo
        ->query('SELECT COUNT(*) FROM contact_enquiries')
        ->fetchColumn();

    $stats['tours'] = (int) $pdo
        ->query('SELECT COUNT(*) FROM tours')
        ->fetchColumn();

    $stats['published_tours'] = (int) $pdo
        ->query("SELECT COUNT(*) FROM tours WHERE status = 'published'")
        ->fetchColumn();

    $stats['tour_enquiries'] = (int) $pdo
        ->query('SELECT COUNT(*) FROM tour_enquiries')
        ->fetchColumn();

    $stats['gallery'] = (int) $pdo
        ->query('SELECT COUNT(*) FROM gallery')
        ->fetchColumn();

    $stats['reviews'] = (int) $pdo
        ->query('SELECT COUNT(*) FROM reviews')
        ->fetchColumn();
} catch (PDOException $exception) {
    error_log('Nexus dashboard statistics failed: ' . $exception->getMessage());
    $databaseAvailable = false;
}

?>
<!doctype html>
<html lang="en" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard | Nexus Admin</title>
    <meta
        name="description"
        content="Nexus Educational Tours and Travels administration dashboard.">

    <!-- Apply saved theme before rendering the page to reduce theme flashing. -->
    <script>
        (() => {
            try {
                const saved = localStorage.getItem('nexus-admin-theme');
                const systemDark = window.matchMedia(
                    '(prefers-color-scheme: dark)'
                ).matches;

                document.documentElement.dataset.theme =
                    saved === 'dark' || saved === 'light' ?
                    saved :
                    (systemDark ? 'dark' : 'light');
            } catch (error) {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>

    <link
        rel="stylesheet"
        href="/nexus-educational-tours/css/admin.css">
</head>

<body class="admin-body">

    <div class="admin-shell">

        <aside class="admin-sidebar" id="admin-sidebar">
            <a
                class="brand"
                href="/nexus-educational-tours/admin/dashboard.php"
                aria-label="Nexus Admin Home">

                <span class="brand-logo">
                    <img
                        src="/nexus-educational-tours/assets/images/logo/logo.png"
                        alt="Nexus logo"
                        onerror="this.style.display='none';">
                    <span class="brand-fallback" aria-hidden="true">N</span>
                </span>

                <span class="brand-copy">
                    <strong>NEXUS</strong>
                    <small>EDUCATIONAL TOURS</small>
                </span>
            </a>

            <div class="sidebar-caption">WORKSPACE</div>

            <nav class="admin-nav" aria-label="Admin navigation">
                <a
                    class="nav-link is-active"
                    href="/nexus-educational-tours/admin/dashboard.php"
                    aria-current="page">
                    <span class="nav-icon" aria-hidden="true">⌂</span>
                    <span>Dashboard</span>
                </a>

                <a
                    class="nav-link"
                    href="/nexus-educational-tours/admin/contact-enquiries.php">
                    <span class="nav-icon" aria-hidden="true">✉</span>
                    <span>Contact Enquiries</span>
                </a>

                <a
                    class="nav-link"
                    href="/nexus-educational-tours/admin/tours.php">
                    <span class="nav-icon" aria-hidden="true">◇</span>
                    <span>Tour Management</span>
                </a>

                <a
                    class="nav-link"
                    href="/nexus-educational-tours/admin/add-tour.php">
                    <span class="nav-icon" aria-hidden="true">＋</span>
                    <span>Add New Tour</span>
                </a>
            </nav>

            <div class="sidebar-caption">CONTENT STUDIO</div>

            <nav class="admin-nav" aria-label="Content management">
                <a class="nav-link nav-link-disabled" href="#gallery-coming-soon" aria-disabled="true">
                    <span class="nav-icon" aria-hidden="true">▧</span>
                    <span>Gallery Management</span>
                    <span class="nav-soon">Soon</span>
                </a>

                <a class="nav-link nav-link-disabled" href="#reviews-coming-soon" aria-disabled="true">
                    <span class="nav-icon" aria-hidden="true">☆</span>
                    <span>Reviews Management</span>
                    <span class="nav-soon">Soon</span>
                </a>

                <a class="nav-link nav-link-disabled" href="#tour-enquiries-coming-soon" aria-disabled="true">
                    <span class="nav-icon" aria-hidden="true">↗</span>
                    <span>Tour Enquiries</span>
                    <span class="nav-soon">Soon</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <div class="sidebar-help">
                    <span class="help-mark" aria-hidden="true">N</span>
                    <div>
                        <strong>Nexus Workspace</strong>
                        <small>Management portal</small>
                    </div>
                </div>

                <form
                    method="post"
                    action="/nexus-educational-tours/admin/logout.php">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e((string) ($_SESSION['admin_login_csrf'] ?? '')) ?>">

                    <button class="logout-button" type="submit">
                        <span aria-hidden="true">↪</span>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="admin-main">

            <header class="topbar">
                <div class="topbar-heading">
                    <span class="eyebrow">NEXUS / WORKSPACE</span>
                    <h1>Dashboard</h1>
                </div>

                <div class="topbar-actions">
                    <button
                        class="theme-toggle"
                        id="theme-toggle"
                        type="button"
                        aria-label="Switch colour theme"
                        aria-pressed="false">
                        <span class="theme-icon" id="theme-icon" aria-hidden="true">☾</span>
                        <span id="theme-label">Dark mode</span>
                    </button>

                    <div class="admin-profile">
                        <span class="profile-avatar" aria-hidden="true">
                            <?= e(strtoupper(substr($adminUsername, 0, 1))) ?>
                        </span>

                        <span class="profile-copy">
                            <strong><?= e($adminUsername) ?></strong>
                            <small>Administrator</small>
                        </span>
                    </div>
                </div>
            </header>

            <main class="dashboard-content">

                <section class="welcome-panel">
                    <div class="welcome-copy">
                        <span class="eyebrow welcome-eyebrow">YOUR CONTROL CENTRE</span>

                        <h2>Welcome back, <?= e($adminUsername) ?>.</h2>

                        <p>
                            Manage your tours, enquiries and content
                            from one central workspace.
                        </p>

                        <a
                            class="primary-button"
                            href="/nexus-educational-tours/admin/add-tour.php">
                            <span aria-hidden="true">＋</span>
                            Create a tour
                        </a>
                    </div>

                    <div class="welcome-art" aria-hidden="true">
                        <div class="orbit orbit-one"></div>
                        <div class="orbit orbit-two"></div>
                        <div class="orbit-core">
                            <span>N</span>
                        </div>
                        <span class="orbit-dot dot-one"></span>
                        <span class="orbit-dot dot-two"></span>
                        <span class="orbit-dot dot-three"></span>
                    </div>
                </section>

                <?php if (!$databaseAvailable): ?>
                    <div class="notice notice-error" role="alert">
                        Dashboard statistics could not be loaded.
                        Please check the database connection.
                    </div>
                <?php endif; ?>

                <div class="section-heading">
                    <div>
                        <span class="eyebrow">AT A GLANCE</span>
                        <h2>Workspace overview</h2>
                    </div>

                    <span class="live-indicator">
                        <span></span>
                        Admin workspace
                    </span>
                </div>

                <section class="stats-grid" aria-label="Workspace statistics">

                    <article class="stat-card stat-card-blue">
                        <div class="stat-topline">
                            <span class="stat-label">Total tours</span>
                            <span class="stat-icon" aria-hidden="true">◇</span>
                        </div>
                        <p class="stat-value"><?= number_format($stats['tours']) ?></p>
                        <p class="stat-note">Tours in your database</p>
                        <a href="/nexus-educational-tours/admin/tours.php">
                            Manage tours <span aria-hidden="true">→</span>
                        </a>
                    </article>

                    <article class="stat-card stat-card-orange">
                        <div class="stat-topline">
                            <span class="stat-label">Published tours</span>
                            <span class="stat-icon" aria-hidden="true">↗</span>
                        </div>
                        <p class="stat-value"><?= number_format($stats['published_tours']) ?></p>
                        <p class="stat-note">Marked as published</p>
                        <a href="/nexus-educational-tours/admin/tours.php?status=published">
                            View published <span aria-hidden="true">→</span>
                        </a>
                    </article>

                    <article class="stat-card stat-card-green">
                        <div class="stat-topline">
                            <span class="stat-label">Contact enquiries</span>
                            <span class="stat-icon" aria-hidden="true">✉</span>
                        </div>
                        <p class="stat-value"><?= number_format($stats['contacts']) ?></p>
                        <p class="stat-note">Submitted contact forms</p>
                        <a href="/nexus-educational-tours/admin/contact-enquiries.php">
                            View enquiries <span aria-hidden="true">→</span>
                        </a>
                    </article>

                    <article class="stat-card stat-card-navy">
                        <div class="stat-topline">
                            <span class="stat-label">Tour enquiries</span>
                            <span class="stat-icon" aria-hidden="true">↗</span>
                        </div>
                        <p class="stat-value"><?= number_format($stats['tour_enquiries']) ?></p>
                        <p class="stat-note">Tour request submissions</p>
                        <span class="stat-footnote">Tour enquiry management is next</span>
                    </article>

                </section>

                <div class="section-heading section-heading-spaced">
                    <div>
                        <span class="eyebrow">QUICK ACCESS</span>
                        <h2>Manage your workspace</h2>
                    </div>
                </div>

                <section class="management-grid">

                    <article class="management-card">
                        <div class="management-icon icon-blue" aria-hidden="true">◇</div>
                        <div class="management-copy">
                            <h3>Tour Management</h3>
                            <p>Add tours and manage their draft, published or archived status.</p>
                        </div>
                        <a href="/nexus-educational-tours/admin/tours.php">
                            Open tour manager <span aria-hidden="true">→</span>
                        </a>
                    </article>

                    <article class="management-card">
                        <div class="management-icon icon-orange" aria-hidden="true">＋</div>
                        <div class="management-copy">
                            <h3>Create a Tour</h3>
                            <p>Add tour details to the database using the secure admin form.</p>
                        </div>
                        <a href="/nexus-educational-tours/admin/add-tour.php">
                            Add a tour <span aria-hidden="true">→</span>
                        </a>
                    </article>

                    <article class="management-card">
                        <div class="management-icon icon-green" aria-hidden="true">✉</div>
                        <div class="management-copy">
                            <h3>Contact Enquiries</h3>
                            <p>Review messages submitted through the website contact form.</p>
                        </div>
                        <a href="/nexus-educational-tours/admin/contact-enquiries.php">
                            View messages <span aria-hidden="true">→</span>
                        </a>
                    </article>

                    <article class="management-card management-card-muted" id="gallery-coming-soon">
                        <div class="management-icon icon-purple" aria-hidden="true">▧</div>
                        <div class="management-copy">
                            <h3>Gallery & Watermarks</h3>
                            <p>Photo and video uploads with Nexus logo watermarking.</p>
                        </div>
                        <span class="coming-soon-label">Planned feature</span>
                    </article>

                    <article class="management-card management-card-muted" id="reviews-coming-soon">
                        <div class="management-icon icon-green" aria-hidden="true">☆</div>
                        <div class="management-copy">
                            <h3>Reviews Management</h3>
                            <p>Manage reviews and publish only approved content.</p>
                        </div>
                        <span class="coming-soon-label">Planned feature</span>
                    </article>

                    <article class="management-card management-card-muted" id="tour-enquiries-coming-soon">
                        <div class="management-icon icon-blue" aria-hidden="true">↗</div>
                        <div class="management-copy">
                            <h3>Tour Enquiries</h3>
                            <p>Review requests for existing tours and customized journeys.</p>
                        </div>
                        <span class="coming-soon-label">Planned feature</span>
                    </article>

                </section>

                <footer class="dashboard-footer">
                    <span>© <?= date('Y') ?> Nexus Educational Tours & Travels</span>
                    <span>We create experiences, not just tours.</span>
                </footer>

            </main>
        </div>
    </div>

    <script>
        (() => {
            const root = document.documentElement;
            const toggle = document.getElementById('theme-toggle');
            const label = document.getElementById('theme-label');
            const icon = document.getElementById('theme-icon');

            function updateThemeButton() {
                const dark = root.dataset.theme === 'dark';

                label.textContent = dark ? 'Light mode' : 'Dark mode';
                icon.textContent = dark ? '☀' : '☾';
                toggle.setAttribute('aria-pressed', String(dark));
                toggle.setAttribute(
                    'aria-label',
                    dark ? 'Switch to light mode' : 'Switch to dark mode'
                );
            }

            updateThemeButton();

            toggle.addEventListener('click', () => {
                const nextTheme =
                    root.dataset.theme === 'dark' ? 'light' : 'dark';

                root.dataset.theme = nextTheme;

                try {
                    localStorage.setItem('nexus-admin-theme', nextTheme);
                } catch (error) {
                    // The theme still works for this page if storage is unavailable.
                }

                updateThemeButton();
            });
        })();
    </script>

</body>

</html>