<?php

declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../config/database.php';
if (!function_exists('e')) {
  function e(string $v): string
  {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
  }
}


function tour_items($value): array
{
  $raw = trim((string)($value ?? ''));

  if ($raw === '') {
    return [];
  }

  $decoded = json_decode($raw, true);

  if (is_array($decoded)) {
    $items = $decoded;
  } else {
    $items = preg_split('/\r\n|\r|\n|,/', $raw) ?: [];
  }

  return array_values(array_unique(array_filter(
    array_map(
      static fn($x) => is_scalar($x) ? trim((string)$x) : '',
      $items
    ),
    static fn($x) => $x !== ''
  )));
}

function tour_url($path): string
{
  $path = trim((string)($path ?? ''));
  return $path === '' ? '' : rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}
$tours = [];
$loadError = false;
$gallery = [];
try {
  $tours = $pdo->query("SELECT * FROM tours WHERE status='published' ORDER BY sort_order ASC, created_at DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
  $exists = $pdo->query("SHOW TABLES LIKE 'tour_gallery'")->fetchColumn();
  if ($exists && $tours) {
    $ids = array_map(static fn($t) => (int)$t['id'], $tours);
    $marks = implode(',', array_fill(0, count($ids), '?'));
    $q = $pdo->prepare("SELECT tour_id,image_path FROM tour_gallery WHERE tour_id IN ($marks) ORDER BY sort_order ASC,id ASC");
    $q->execute($ids);
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $g) {
      $gallery[(int)$g['tour_id']][] = tour_url($g['image_path'] ?? '');
    }
  }
} catch (Throwable $ex) {
  error_log('Nexus tours page: ' . $ex->getMessage());
  $loadError = true;
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
$modalData = [];
foreach ($tours as $t) {
  $id = (int)$t['id'];
  $modalData[(string)$id] = [
    'title' => (string)($t['title'] ?? ''),
    'category' => (string)($t['category'] ?? ''),
    'destination' => (string)($t['destination'] ?? ''),
    'duration' => (string)($t['duration'] ?? ''),
    'short_description' => (string)($t['short_description'] ?? ''),
    'description' => (string)($t['description'] ?? ''),
    'highlights' => tour_items($t['highlights'] ?? ''),
    'activities' => tour_items($t['activities'] ?? ''),
    'places_covered' => tour_items($t['places_covered'] ?? ''),
    'suitable_for' => tour_items($t['suitable_for'] ?? ''),
    'best_season' => (string)($t['best_season'] ?? ''),
    'image' => tour_url($t['image_path'] ?? ''),
    'gallery' => $gallery[$id] ?? []
  ];
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tours | Nexus Educational Tours and Travels</title>
  <meta name="description" content="Explore educational, school, college, industrial, study, adventure, nature and customized tours offered by Nexus Educational Tours and Travels.">
  <link rel="stylesheet" href="<?= e(BASE_URL) ?>css/style.css">
  <link rel="stylesheet" href="<?= e(BASE_URL) ?>css/responsive.css">
  <link rel="stylesheet" href="<?= e(BASE_URL) ?>css/animations.css">
  <style>
    .published-tours-section {
      background: #f7fafc
    }

    .published-tours-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 24px;
      margin-top: 36px
    }

    .published-tour-card {
      min-width: 0;
      overflow: hidden;
      background: #fff;
      border: 1px solid #e2eaf2;
      border-radius: 22px;
      box-shadow: 0 12px 32px #102a4312;
      transition: transform .25s, box-shadow .25s
    }

    .published-tour-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 18px 40px #102a4320
    }

    .published-tour-image-wrap {
      height: 220px;
      background: #eaf1f7;
      overflow: hidden
    }

    .published-tour-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform .4s
    }

    .published-tour-card:hover .published-tour-image {
      transform: scale(1.04)
    }

    .published-tour-placeholder {
      height: 100%;
      display: grid;
      place-items: center;
      color: #173b67;
      font-size: 48px
    }

    .published-tour-body {
      padding: 23px
    }

    .published-tour-category {
      display: inline-block;
      margin-bottom: 9px;
      color: #2b83c6;
      font-size: .76rem;
      font-weight: 800;
      letter-spacing: .08em;
      text-transform: uppercase
    }

    .published-tour-body h3 {
      margin: 0 0 10px;
      color: #173b67
    }

    .published-tour-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 8px 14px;
      margin-bottom: 12px;
      color: #52677b;
      font-size: .88rem
    }

    .published-tour-body p {
      color: #52677b;
      line-height: 1.7
    }

    .tour-tag-section {
      margin-top: 16px
    }

    .tour-tag-heading {
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 0 0 9px;
      color: #173b67;
      font-size: .83rem;
      font-weight: 800
    }

    .tour-tag-heading span {
      display: inline-grid;
      place-items: center;
      width: 27px;
      height: 27px;
      border-radius: 8px;
      background: #edf4fb
    }

    .tour-tag-list {
      display: flex;
      flex-wrap: wrap;
      gap: 7px
    }

    .tour-tag {
      display: inline-flex;
      align-items: center;
      max-width: 100%;
      padding: 7px 10px;
      border-radius: 999px;
      font-size: .76rem;
      line-height: 1.35;
      font-weight: 700;
      overflow-wrap: anywhere
    }

    .tour-tag-learning {
      color: #173b67;
      background: #edf4ff;
      border: 1px solid #d4e3fb
    }

    .tour-tag-activity {
      color: #874807;
      background: #fff3e3;
      border: 1px solid #f8d5a5
    }

    .tour-tag-place {
      color: #276749;
      background: #eaf8f0;
      border: 1px solid #ccebd9
    }

    .tour-tag-more {
      cursor: pointer;
      background: white;
      color: #173b67;
      border: 1px dashed #9bb3cb
    }

    .published-tour-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 18px;
      align-items: center
    }

    .published-tour-link {
      display: inline-flex;
      gap: 8px;
      align-items: center;
      border: 0;
      background: transparent;
      color: #173b67;
      font: inherit;
      font-weight: 800;
      text-decoration: none;
      cursor: pointer
    }

    .published-tour-link:hover {
      color: #2b83c6
    }

    .published-tour-link.primary {
      padding: 11px 14px;
      border-radius: 10px;
      background: #173b67;
      color: #fff
    }

    .published-tour-link.primary:hover {
      background: #2b83c6;
      color: white
    }

    .published-tour-empty {
      margin-top: 28px;
      padding: 28px;
      background: #fff;
      border: 1px solid #e5edf4;
      border-radius: 16px;
      color: #52677b
    }

    .tour-details-modal {
      position: fixed;
      inset: 0;
      z-index: 9999;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 18px;
      background: #071424b8;
      backdrop-filter: blur(5px)
    }

    .tour-details-modal.open {
      display: flex
    }

    .tour-details-dialog {
      width: min(900px, 100%);
      max-height: 92vh;
      overflow: auto;
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 28px 90px #0005
    }

    .tour-modal-close {
      position: sticky;
      top: 10px;
      float: right;
      z-index: 2;
      margin: 10px 10px -50px 0;
      width: 40px;
      height: 40px;
      border: 1px solid #dce6ef;
      border-radius: 50%;
      background: #fff;
      color: #173b67;
      font-size: 25px;
      cursor: pointer
    }

    .tour-modal-hero {
      height: 290px;
      background: #eaf1f7
    }

    .tour-modal-hero img {
      width: 100%;
      height: 100%;
      object-fit: cover
    }

    .tour-modal-content {
      padding: clamp(20px, 4vw, 36px)
    }

    .tour-modal-content h2 {
      margin: 0 0 12px;
      color: #173b67
    }

    .tour-modal-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin: 12px 0 18px
    }

    .tour-modal-meta span {
      padding: 8px 10px;
      border: 1px solid #e1eaf2;
      border-radius: 9px;
      background: #f2f6fa;
      color: #334e68;
      font-size: .88rem
    }

    .tour-modal-description {
      white-space: pre-line;
      color: #52677b;
      line-height: 1.8
    }

    .tour-modal-section {
      margin-top: 22px;
      padding-top: 18px;
      border-top: 1px solid #e6edf4
    }

    .tour-modal-section h3 {
      margin: 0 0 12px;
      color: #173b67;
      font-size: 1rem
    }

    .tour-modal-gallery {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 10px;
      margin-top: 20px
    }

    .tour-modal-gallery img {
      width: 100%;
      height: 105px;
      object-fit: cover;
      border-radius: 10px
    }

    .tour-modal-enquire {
      display: inline-flex;
      margin-top: 24px;
      padding: 13px 17px;
      border-radius: 11px;
      background: #f4a340;
      color: #102a43;
      font-weight: 800;
      text-decoration: none
    }

    @media(max-width:1000px) {
      .published-tours-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr))
      }
    }

    @media(max-width:650px) {
      .published-tours-grid {
        grid-template-columns: 1fr;
        gap: 18px
      }

      .published-tour-body {
        padding: 19px
      }

      .published-tour-image-wrap {
        height: 210px
      }

      .tour-details-modal {
        padding: 9px
      }

      .tour-modal-hero {
        height: 210px
      }

      .tour-modal-gallery {
        grid-template-columns: repeat(2, minmax(0, 1fr))
      }

      .tour-modal-gallery img {
        height: 110px
      }

      .tour-tag {
        font-size: .73rem;
        padding: 7px 9px
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
          <div class="tours-hero-content"><span class="section-eyebrow animated-eyebrow"><span class="eyebrow-line"></span>Explore Our Tours</span>
            <h1>Travel experiences for <span> every learning journey. </span></h1>
            <p>From educational exploration to customized group journeys, discover travel experiences planned around your purpose.</p>
            <div class="tours-hero-actions"><a href="#published-tours" class="btn btn-primary">Explore Tours <span aria-hidden="true">↓</span></a><a href="<?= e(BASE_URL) ?>index.php#enquiry" class="btn btn-secondary">Plan Your Tour <span aria-hidden="true">→</span></a></div>
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
        <?php if ($loadError): ?><div class="published-tour-empty" role="alert">Tours could not be loaded right now. Please check the database connection and try again.</div>
        <?php elseif (!$tours): ?><div class="published-tour-empty">No tours have been published yet. Please check back soon or contact us to plan a customized journey.</div>
        <?php else: ?><div class="published-tours-grid">
            <?php foreach ($tours as $t):
              $id = (int)$t['id'];
              $img = tour_url($t['image_path'] ?? '');
              $summary = trim((string)($t['short_description'] ?? ''));
              if ($summary === '') $summary = trim((string)($t['description'] ?? ''));
              if (function_exists('mb_strlen') && mb_strlen($summary) > 180) $summary = mb_substr($summary, 0, 177) . '...';
              elseif (strlen($summary) > 180) $summary = substr($summary, 0, 177) . '...';
              $hi = tour_items($t['highlights'] ?? '');
              $acts = tour_items($t['activities'] ?? '');
              $places = tour_items($t['places_covered'] ?? '');
              $json = json_encode($modalData[(string)$id] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
            ?>
              <article class="published-tour-card">
                <div class="published-tour-image-wrap"><?php if ($img !== ''): ?><img class="published-tour-image" src="<?= e($img) ?>" alt="<?= e((string)$t['title']) ?>" loading="lazy"><?php else: ?><div class="published-tour-placeholder" aria-label="No tour image available">✦</div><?php endif; ?></div>
                <div class="published-tour-body">
                  <?php if (!empty($t['category'])): ?><span class="published-tour-category"><?= e((string)$t['category']) ?></span><?php endif; ?><h3><?= e((string)$t['title']) ?></h3>
                  <div class="published-tour-meta"><span>📍 <?= e((string)($t['destination'] ?? 'Destination to be confirmed')) ?></span><?php if (!empty($t['duration'])): ?><span>🕒 <?= e((string)$t['duration']) ?></span><?php endif; ?></div><?php if ($summary !== ''): ?><p><?= e($summary) ?></p><?php endif; ?>
                  <?php foreach ([['🎓', 'Learning Highlights', $hi, 'tour-tag-learning', 4], ['✨', 'Activities', $acts, 'tour-tag-activity', 4], ['📍', 'Places Covered', $places, 'tour-tag-place', 3]] as $group): if (!$group[2]) continue; ?>
                    <div class="tour-tag-section">
                      <h4 class="tour-tag-heading"><span aria-hidden="true"><?= $group[0] ?></span><?= $group[1] ?></h4>
                      <div class="tour-tag-list"><?php foreach (array_slice($group[2], 0, $group[4]) as $item): ?><span class="tour-tag <?= $group[3] ?>"><?= e($item) ?></span><?php endforeach; ?><?php if (count($group[2]) > $group[4]): ?><button type="button" class="tour-tag tour-tag-more" data-tour-details="<?= $id ?>">+<?= count($group[2]) - $group[4] ?> more</button><?php endif; ?></div>
                    </div>
                  <?php endforeach; ?>

                  <div class="published-tour-actions">
                    <button
                      type="button"
                      class="published-tour-link primary"
                      data-tour-details="<?= (int)$id ?>">
                      View full details ↗
                    </button>

                    <a
                      class="published-tour-link"
                      href="<?= e(rtrim(BASE_URL, '/') . '/pages/tour-enquiry.php?tour_id=' . (int)$id) ?>">
                      Enquire →
                    </a>
                  </div>

                  <script type="application/json" id="tour-data-<?= $id ?>">
                    <?= $json ?: '{}' ?>
                  </script>
                </div>
              </article><?php endforeach; ?>
          </div><?php endif; ?>
      </div>
    </section>
    <section class="section tour-categories-section tours-category-section" id="tour-categories">
      <div class="container">
        <div class="section-header tours-categories-header"><span class="section-eyebrow animated-eyebrow"><span class="eyebrow-line"></span>Explore Your Journey</span>
          <h2>Find the experience that <span> fits your purpose. </span></h2>
          <p>From learning-focused visits to adventure and customized group journeys, explore the different ways your next experience can take shape.</p>
        </div>
        <div class="tour-category-grid tours-page-grid"><?php foreach ($categories as $i => $c): ?><article class="tour-category-card tour-page-card"><span class="tour-page-number"><?= sprintf('%02d', $i + 1) ?></span>
              <div class="tour-category-icon" aria-hidden="true"><?= e($c[0]) ?></div>
              <div class="tour-category-content">
                <h3><?= e($c[1]) ?></h3>
                <p><?= e($c[2]) ?></p><a class="tour-card-arrow" href="<?= e(BASE_URL) ?>index.php#enquiry">Explore <span aria-hidden="true">→</span></a>
              </div>
            </article><?php endforeach; ?></div>
      </div>
    </section>
    <section class="section tours-journey-cta">
      <div class="container">
        <div class="tours-journey-panel">
          <div class="tours-journey-copy"><span class="section-eyebrow animated-eyebrow"><span class="eyebrow-line"></span>Your Journey, Your Way</span>
            <h2>Don't see exactly what you are looking for? <span> Let's create it. </span></h2>
            <p>Share your institution, group size, destination or travel requirements. Your journey can be discussed around what your group actually needs.</p>
            <div class="tours-journey-actions"><a href="<?= e(BASE_URL) ?>index.php#enquiry" class="btn btn-primary">Start Your Enquiry →</a><a href="tel:7219766277" class="tours-call-link"><span>Prefer to talk?</span><strong>7219766277</strong></a></div>
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
  <div class="tour-details-modal" id="tourDetailsModal" aria-hidden="true">
    <div class="tour-details-dialog" role="dialog" aria-modal="true" aria-labelledby="tourModalTitle"><button type="button" class="tour-modal-close" id="tourModalClose" aria-label="Close tour details">×</button>
      <div class="tour-modal-hero" id="tourModalHero"></div>
      <div class="tour-modal-content" id="tourModalContent"></div>
    </div>
  </div>
  <?php include __DIR__ . '/../includes/footer.php'; ?>
  <a href="https://wa.me/917448228001?text=Hello%20Nexus%20Educational%20Tours%20and%20Travels%2C%20I%20would%20like%20to%20enquire%20about%20a%20tour." class="floating-whatsapp" target="_blank" rel="noopener" aria-label="Chat with Nexus on WhatsApp"><span class="whatsapp-pulse"></span><span class="whatsapp-icon" aria-hidden="true">☏</span><span class="whatsapp-text">Chat on WhatsApp</span></a>
  <script src="<?= e(BASE_URL) ?>js/main.js"></script>
  <script>
    (() => {
      const modal = document.getElementById('tourDetailsModal'),
        close = document.getElementById('tourModalClose'),
        hero = document.getElementById('tourModalHero'),
        content = document.getElementById('tourModalContent');
      let prior = null;
      const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      } [c]));
      const tags = (arr, type) => Array.isArray(arr) && arr.length ? '<div class="tour-tag-list">' + arr.map(x => '<span class="tour-tag ' + type + '">' + esc(x) + '</span>').join('') + '</div>' : '';
      const section = (title, icon, arr, type) => Array.isArray(arr) && arr.length ? '<section class="tour-modal-section"><h3>' + icon + ' ' + title + '</h3>' + tags(arr, type) + '</section>' : '';

      function open(id, trigger) {
        const node = document.getElementById('tour-data-' + id);
        if (!node) return;
        let d;
        try {
          d = JSON.parse(node.textContent)
        } catch (e) {
          return
        }
        prior = trigger;
        hero.innerHTML = d.image ? '<img src="' + esc(d.image) + '" alt="' + esc(d.title) + '">' : '<div class="published-tour-placeholder">✦</div>';
        const meta = [d.category ? '🧭 ' + esc(d.category) : '', d.destination ? '📍 ' + esc(d.destination) : '', d.duration ? '🕒 ' + esc(d.duration) : '', d.best_season ? '🌤️ ' + esc(d.best_season) : ''].filter(Boolean).map(x => '<span>' + x + '</span>').join('');
        const gallery = Array.isArray(d.gallery) && d.gallery.length ? '<div class="tour-modal-gallery">' + d.gallery.map(x => '<img src="' + esc(x) + '" alt="' + esc(d.title) + ' gallery image" loading="lazy">').join('') + '</div>' : '';
        content.innerHTML = (d.category ? '<span class="published-tour-category">' + esc(d.category) + '</span>' : '') + '<h2 id="tourModalTitle">' + esc(d.title || 'Tour details') + '</h2><div class="tour-modal-meta">' + meta + '</div>' + (d.short_description ? '<p class="tour-modal-description">' + esc(d.short_description) + '</p>' : '') + (d.description ? '<p class="tour-modal-description">' + esc(d.description) + '</p>' : '') + section('Learning Highlights', '🎓', d.highlights, 'tour-tag-learning') + section('Activities', '✨', d.activities, 'tour-tag-activity') + section('Places Covered', '📍', d.places_covered, 'tour-tag-place') + section('Suitable For', '👥', Array.isArray(d.suitable_for) ? d.suitable_for : (d.suitable_for ? [d.suitable_for] : []), 'tour-tag-learning') + gallery + '<a class="tour-modal-enquire" href="<?= e(BASE_URL) ?>index.php#enquiry">Enquire about this tour →</a>';
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        close.focus()
      }

      function shut() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (prior) prior.focus()
      }
      document.addEventListener('click', e => {
        const b = e.target.closest('[data-tour-details]');
        if (b) {
          e.preventDefault();
          open(b.dataset.tourDetails, b)
        }
      });
      close.addEventListener('click', shut);
      modal.addEventListener('click', e => {
        if (e.target === modal) shut()
      });
      document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal.classList.contains('open')) shut()
      });
    })();
  </script>
</body>

</html>