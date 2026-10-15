<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Page Not Found | Nexus Educational Tours and Travels</title>
  <meta name="robots" content="noindex, follow">
  <meta name="description" content="The page you are looking for could not be found. Explore Nexus Educational Tours and Travels or contact our team.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/responsive.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/animations.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/404.css">
</head>

<body>

  <?php include __DIR__ . '/includes/header.php'; ?>

  <main class="error-page">
    <section class="error-section">
      <div class="container">

        <div class="error-visual" aria-hidden="true">
          <span class="error-orbit error-orbit-one"></span>
          <span class="error-orbit error-orbit-two"></span>
          <span class="error-number">404</span>
          <span class="error-plane">✈</span>
        </div>

        <p class="error-eyebrow">OOPS! WRONG DESTINATION</p>

        <h1>Looks like you've <span>lost your way.</span></h1>

        <p class="error-description">
          The page you're looking for may have moved,
          been removed, or is no longer available.
          Don't worry — let's get your journey back on track.
        </p>

        <div class="error-actions">
          <a href="<?= BASE_URL ?>index.php" class="btn btn-primary">
            <span aria-hidden="true">←</span>
            Back to Home
          </a>

          <a href="<?= BASE_URL ?>pages/contact.php" class="btn btn-secondary">
            Contact Our Team
          </a>
        </div>

        <div class="error-help">
          <span class="error-help-dot"></span>
          <p>
            Need help planning an educational tour?
            <a href="https://wa.me/917448228001"
              target="_blank"
              rel="noopener noreferrer">Chat with us on WhatsApp</a>.
          </p>
        </div>

      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="<?= BASE_URL ?>js/main.js"></script>

</body>

</html>