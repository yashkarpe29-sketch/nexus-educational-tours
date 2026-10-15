<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../config/database.php';

if (!function_exists('e')) {
  function e(string $value): string
  {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  }
}

$tours = [];
$toursLoadError = false;
try {
  $stmt = $pdo->query(
    "SELECT id, title, slug, category, destination, short_description,
                description, duration, image_path
         FROM tours
         WHERE status = 'published'
         ORDER BY sort_order ASC, created_at DESC, id DESC"
  );
  $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
  error_log('Nexus public tours load failed: ' . $exception->getMessage());
  $toursLoadError = true;
}

$categories = [
  ['🎓', 'Educational Tours', 'Meaningful travel experiences designed to combine learning, exploration and practical exposure.'],
  ['🏫', 'College Tours', 'Educational and group travel experiences planned for colleges and student groups.'],
  ['📚', 'School Tours', 'Well-planned educational travel experiences for schools and young learners.'],
  ['🏭', 'Industrial Visits', 'Industry-focused visits designed to support practical learning and exposure.'],
  ['📖', 'Study Tours', 'Travel experiences that connect classroom learning with real-world exploration.'],
  ['🏛️', 'Historical Tours', 'Travel experiences focused on history, heritage and cultural exploration.'],
  ['🔬', 'Science Tours', 'Exploration-focused tours designed around science and educational experiences.'],
  ['⛰️', 'Adventure Tours', 'Engaging group travel experiences combining exploration, activity and adventure.'],
  ['🌿', 'Nature Tours', 'Travel experiences focused on nature, exploration and outdoor learning.'],
  ['☀️', 'One Day Trips', 'Convenient one-day group travel experiences for educational and recreational purposes.'],
  ['🗓️', 'Multi-Day Tours', 'Planned multi-day travel experiences for institutions and student groups.'],
  ['🧭', 'Customized Tours', 'Flexible tour planning based on institution, group and travel requirements.'],
];
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tours | Nexus Educational Tours and Travels</title>
  <meta name="description" content="Explore educational, school, college, industrial, study, adventure, nature and customized tours offered by Nexus Educational Tours and Travels.">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/responsive.css">
  <link rel="stylesheet" href="../css/animations.css">
  <style>
    .published-tours-section {
      background: #f7fafc;
    }

    .published-tours-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 24px;
      margin-top: 36px;
    }

    .published-tour-card {
      overflow: hidden;
      background: #fff;
      border: 1px solid #e5edf4;
      border-radius: 20px;
      box-shadow: 0 12px 32px rgba(16, 42, 67, .07);
      transition: transform .25s ease, box-shadow .25s ease;
    }

    .published-tour-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 18px 40px rgba(16, 42, 67, .12);
    }

    .published-tour-image-wrap {
      height: 220px;
      background: #eaf1f7;
      overflow: hidden;
    }

    .published-tour-image {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .published-tour-placeholder {
      display: grid;
      place-items: center;
      width: 100%;
      height: 100%;
      color: #173b67;
      font-size: 48px;
    }

    .published-tour-body {
      padding: 24px;
    }

    .published-tour-category {
      display: inline-block;
      margin-bottom: 10px;
      color: #2b83c6;
      font-size: .78rem;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
    }

    .published-tour-body h3 {
      margin: 0 0 10px;
      color: #173b67;
    }

    .published-tour-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 8px 16px;
      margin: 0 0 14px;
      color: #52677b;
      font-size: .9rem;
    }

    .published-tour-body p {
      color: #52677b;
      line-height: 1.7;
    }

    .published-tour-link {
      display: inline-flex;
      gap: 8px;
      align-items: center;
      margin-top: 8px;
      color: #173b67;
      font-weight: 700;
      text-decoration: none;
    }

    .published-tour-link:hover {
      color: #2b83c6;
    }

    .published-tour-empty {
      margin-top: 28px;
      padding: 28px;
      background: #fff;
      border: 1px solid #e5edf4;
      border-radius: 16px;
      color: #52677b;
    }

    @media (max-width:900px) {
      .published-tours-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width:600px) {
      .published-tours-grid {
        grid-template-columns: 1fr;
      }

      .published-tour-image-wrap {
        height: 210px;
      }
    }
  </style>
</head>

<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <main>
    <section class="section tours-page-hero" id="tours-hero">
      <div class="container">
        <div class="tours-hero-layout">
          <div class="tours-hero-content">
            <span class="section-eyebrow animated-eyebrow"><span class="eyebrow-line"></span>Explore Our Tours</span>
            <h1>Travel experiences for <span> every learning journey. </span></h1>
            <p>From educational exploration to customized group journeys, discover travel experiences planned around your purpose.</p>
            <div class="tours-hero-actions">
              <a href="#published-tours" class="btn btn-primary">Explore Tours <span aria-hidden="true">↓</span></a>
              <a href="<?= e(BASE_URL) ?>index.php#enquiry" class="btn btn-secondary">Plan Your Tour <span aria-hidden="true">→</span></a>
            </div>
          </div>
          <div class="tours-hero-visual" aria-hidden="true">
            <div class="tour-orbit tour-orbit-one"></div>
            <div class="tour-orbit tour-orbit-two"></div>
            <div class="tour-orbit tour-orbit-three"></div>
            <div class="tour-compass"><span class="tour-direction tour-n">N</span><span class="tour-direction tour-e">E</span><span class="tour-direction tour-s">S</span><span class="tour-direction tour-w">W</span>
              <div class="tour-compass-center">✦</div>
            </div>
            <div class="tour-floating-label tour-label-one"><span>01</span>Learn</div>
            <div class="tour-floating-label tour-label-two"><span>02</span>Explore</div>
            <div class="tour-floating-label tour-label-three"><span>03</span>Experience</div>
          </div>
        </div>
      </div>
    </section>

    <section class="section tours-intro-section">
      <div class="container">
        <div class="tours-intro-panel">
          <div class="tours-intro-number"><?= count($tours) ?></div>
          <div class="tours-intro-content"><span class="section-eyebrow animated-eyebrow"><span class="eyebrow-line"></span>Your Journey Navigator</span>
            <h2>One purpose. <span> Many ways to experience it. </span></h2>
            <p>Every group has a different reason to travel. Explore our tour options and find the journey that matches your institution, students and travel requirements.</p>
          </div>
          <div class="tours-intro-route"><span></span><span></span><span></span><span></span><span></span></div>
        </div>
      </div>
    </section>

    <section class="section published-tours-section" id="published-tours">
      <div class="container">
        <div class="section-header tours-categories-header"><span class="section-eyebrow animated-eyebrow"><span class="eyebrow-line"></span>Available Experiences</span>
          <h2>Explore our <span> published tours. </span></h2>
          <p>These tours are managed by the Nexus team. New tours appear here when they are published in the admin panel.</p>
        </div>
        <?php if ($toursLoadError): ?>
          <div class="published-tour-empty" role="alert">Tours could not be loaded right now. Please try again later.</div>
        <?php elseif (!$tours): ?>
          <div class="published-tour-empty">No tours have been published yet. Please check back soon or contact us to plan a customized journey.</div>
        <?php else: ?>
          <div class="published-tours-grid">
            <?php foreach ($tours as $tour): ?>
              <?php
              $imagePath = trim((string) ($tour['image_path'] ?? ''));
              $imageUrl = '';
              if ($imagePath !== '') {
                // Database stores paths relative to the website root, e.g. uploads/tours/file.webp.
                $imageUrl = rtrim(BASE_URL, '/') . '/' . ltrim($imagePath, '/');
              }
              $summary = trim((string) ($tour['short_description'] ?? ''));
              if ($summary === '') {
                $summary = trim((string) ($tour['description'] ?? ''));
              }
              if (mb_strlen($summary) > 180) {
                $summary = mb_substr($summary, 0, 177) . '...';
              }
              ?>
              <article class="published-tour-card">
                <div class="published-tour-image-wrap">
                  <?php if ($imageUrl !== ''): ?>
                    <img class="published-tour-image" src="<?= e($imageUrl) ?>" alt="<?= e((string) $tour['title']) ?>" loading="lazy">
                  <?php else: ?>
                    <div class="published-tour-placeholder" aria-label="No tour image available">✦</div>
                  <?php endif; ?>
                </div>
                <div class="published-tour-body">
                  <?php if (!empty($tour['category'])): ?><span class="published-tour-category"><?= e((string) $tour['category']) ?></span><?php endif; ?>
                  <h3><?= e((string) $tour['title']) ?></h3>
                  <div class="published-tour-meta"><span>📍 <?= e((string) $tour['destination']) ?></span><?php if (!empty($tour['duration'])): ?><span>🕒 <?= e((string) $tour['duration']) ?></span><?php endif; ?></div>
                  <?php if ($summary !== ''): ?><p><?= e($summary) ?></p><?php endif; ?>
                  <a class="published-tour-link" href="<?= e(BASE_URL) ?>index.php#enquiry">Enquire about this tour <span aria-hidden="true">→</span></a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="section tour-categories-section tours-category-section" id="tour-categories">
      <div class="container">
        <div class="section-header tours-categories-header"><span class="section-eyebrow animated-eyebrow"><span class="eyebrow-line"></span>Explore Your Journey</span>
          <h2>Find the experience that <span> fits your purpose. </span></h2>
          <p>From learning-focused visits to adventure and customized group journeys, explore the different ways your next experience can take shape.</p>
        </div>
        <div class="tour-category-grid tours-page-grid">
          <?php foreach ($categories as $index => $category): ?>
            <article class="tour-category-card tour-page-card"><span class="tour-page-number"><?= sprintf('%02d', $index + 1) ?></span>
              <div class="tour-category-icon" aria-hidden="true"><?= e($category[0]) ?></div>
              <div class="tour-category-content">
                <h3><?= e($category[1]) ?></h3>
                <p><?= e($category[2]) ?></p><a class="tour-card-arrow" href="<?= e(BASE_URL) ?>index.php#enquiry">Explore <span aria-hidden="true">→</span></a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section tours-journey-cta">
      <div class="container">
        <div class="tours-journey-panel">
          <div class="tours-journey-copy"><span class="section-eyebrow animated-eyebrow"><span class="eyebrow-line"></span>Your Journey, Your Way</span>
            <h2>Don't see exactly what you are looking for? <span> Let's create it. </span></h2>
            <p>Share your institution, group size, destination or travel requirements. Your journey can be discussed around what your group actually needs.</p>
            <div class="tours-journey-actions"><a href="<?= e(BASE_URL) ?>index.php#enquiry" class="btn btn-primary">Start Your Enquiry <span aria-hidden="true">→</span></a><a href="tel:7219766277" class="tours-call-link"><span>Prefer to talk?</span><strong>7219766277</strong></a></div>
          </div>
          <div class="tours-journey-visual" aria-hidden="true">
            <div class="journey-route-line"></div>
            <div class="journey-route-point point-one"><span>01</span></div>
            <div class="journey-route-point point-two"><span>02</span></div>
            <div class="journey-route-point point-three"><span>03</span></div>
            <div class="journey-route-point point-four"><span>04</span></div>
            <div class="journey-route-plane">✦</div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <?php include __DIR__ . '/../includes/footer.php'; ?>
  <a href="https://wa.me/917448228001?text=Hello%20Nexus%20Educational%20Tours%20and%20Travels%2C%20I%20would%20like%20to%20enquire%20about%20a%20tour." class="floating-whatsapp" target="_blank" rel="noopener" aria-label="Chat with Nexus on WhatsApp"><span class="whatsapp-pulse"></span><span class="whatsapp-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="26" height="26" xmlns="http://www.w3.org/2000/svg">
        <path fill="currentColor" d="M20.52 3.48A11.87 11.87 0 0 0 12.05 0C5.4 0 0 5.4 0 12.05c0 2.12.56 4.19 1.62 6.02L0 24l6.1-1.6a12 12 0 0 0 5.95 1.57h.01c6.64 0 12.04-5.4 12.04-12.04 0-3.22-1.25-6.24-3.58-8.45ZM12.06 21.9c-1.86 0-3.68-.5-5.27-1.44l-.38-.23-3.62.95.97-3.53-.25-.4a9.84 9.84 0 0 1-1.51-5.2c0-5.44 4.43-9.87 9.88-9.87 2.63 0 5.1 1.02 6.96 2.88a9.8 9.8 0 0 1 2.9 6.97c0 5.44-4.43 9.87-9.88 9.87Z" />
        <path fill="currentColor" d="M17.53 14.47c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.11c.15.2 2.13 3.25 5.16 4.55.72.31 1.28.49 1.72.63.72.23 1.37.2 1.89.12.58-.09 1.76-.72 2.01-1.41.25-.69.25-1.28.17-1.41-.07-.12-.27-.2-.57-.35Z" />
      </svg></span><span class="whatsapp-text">Chat on WhatsApp</span></a>
  <script src="<?= e(BASE_URL) ?>js/main.js"></script>
</body>

</html>