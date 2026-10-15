<?php
require_once __DIR__ . '/../config.php';
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />

  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>About Nexus | Educational Tours and Travels</title>

  <meta
    name="description"
    content="Learn about Nexus Educational Tours and Travels, an educational travel company based in Chakan, Pune." />

  <meta name="robots" content="index, follow" />

  <meta property="og:type" content="website" />
  <meta
    property="og:title"
    content="About Nexus | Educational Tours and Travels" />
  <meta
    property="og:description"
    content="Discover the purpose, approach and journey behind Nexus Educational Tours and Travels." />

  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/responsive.css" />
  <link rel="stylesheet" href="../css/animations.css" />
</head>

<body class="about-page">
  <!-- =====================================================
         HEADER
         ===================================================== -->

  <?php include __DIR__ . '/../includes/header.php'; ?>

  <main>
    <!-- =====================================================
             ABOUT HERO — NEXUS STORY
             ===================================================== -->

    <section class="about-story-hero">
      <div class="about-hero-route" aria-hidden="true">
        <span class="about-route-dot about-route-dot-one"></span>
        <span class="about-route-dot about-route-dot-two"></span>
        <span class="about-route-dot about-route-three"></span>
      </div>

      <div class="about-hero-orbit about-hero-orbit-one"></div>
      <div class="about-hero-orbit about-hero-orbit-two"></div>

      <div class="container">
        <div class="about-hero-grid">
          <div class="about-hero-copy">
            <span class="section-eyebrow about-hero-eyebrow">
              The Nexus Story
            </span>

            <h1>
              We create
              <span>experiences,</span>
              not just tours.
            </h1>

            <p>
              Nexus Educational Tours and Travels is built around a simple
              idea — travel becomes more meaningful when it creates
              opportunities to learn, explore and experience something new.
            </p>

            <div class="about-hero-actions">
              <a href="#our-story" class="btn btn-primary">
                Discover Our Story
                <span aria-hidden="true">↓</span>
              </a>

              <a href="<?= BASE_URL ?>pages/tours.php" class="about-text-link">
                Explore Our Tours
                <span aria-hidden="true">→</span>
              </a>
            </div>
          </div>

          <div class="about-hero-visual">
            <div class="about-compass">
              <div class="about-compass-ring ring-one"></div>
              <div class="about-compass-ring ring-two"></div>

              <div class="about-compass-direction direction-n">N</div>

              <div class="about-compass-direction direction-e">E</div>

              <div class="about-compass-direction direction-s">S</div>

              <div class="about-compass-direction direction-w">W</div>

              <div class="about-compass-core">
                <span class="compass-star">✦</span>
                <strong>NEXUS</strong>
                <small>YOUR JOURNEY</small>
              </div>

              <span class="about-floating-label label-learn"> Learn </span>

              <span class="about-floating-label label-explore">
                Explore
              </span>

              <span class="about-floating-label label-experience">
                Experience
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
             STORY INTRO
             ===================================================== -->

    <section class="section about-story-section" id="our-story">
      <div class="container">
        <div class="about-story-layout">
          <div class="about-story-marker">
            <span class="about-story-number"> 01 </span>

            <span class="about-story-line"></span>

            <span class="about-story-label"> OUR STORY </span>
          </div>

          <div class="about-story-content">
            <span class="section-eyebrow"> Who We Are </span>

            <h2>
              Travel with a
              <span>purpose.</span>
            </h2>

            <p class="about-story-lead">
              Nexus Educational Tours and Travels is an educational travel
              company based in Chakan, Pune, focused on creating meaningful
              and well-planned travel experiences for schools, colleges,
              educational institutions and student groups.
            </p>

            <p>
              We believe a journey can be more than simply reaching a
              destination. It can create opportunities for students and groups
              to discover new places, experience different environments and
              connect travel with learning.
            </p>

            <p>
              Our approach is centred around thoughtful planning, practical
              coordination and experiences that are designed around the needs
              of the group.
            </p>

            <div class="about-story-signature">
              <span class="signature-line"></span>

              <div>
                <strong>Nexus</strong>
                <small> Educational Tours & Travels </small>
              </div>
            </div>
          </div>

          <div class="about-story-visual">
            <div class="about-story-image-frame">
              <div class="about-story-image-accent"></div>

              <img
                src="../assets/images/general/about-main.jpg"
                alt="Educational travel experience"
                loading="lazy" />

              <div class="about-image-caption">
                <span>EST.</span>
                <strong>2025</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
             NEXUS JOURNEY
             ===================================================== -->

    <section class="section about-journey-section">
      <div class="container">
        <div class="about-section-heading">
          <span class="section-eyebrow"> The Nexus Journey </span>

          <h2>
            From an idea to a
            <span>journey worth remembering.</span>
          </h2>

          <p>
            Our story is guided by a simple direction: make educational travel
            more meaningful, organized and memorable.
          </p>
        </div>

        <div class="about-journey">
          <div class="about-journey-line"></div>

          <article class="about-journey-step">
            <div class="about-journey-node">
              <span>01</span>
            </div>

            <div class="about-journey-card">
              <span class="journey-card-label"> THE BEGINNING </span>

              <h3>A new journey begins.</h3>

              <p>
                Nexus Educational Tours and Travels was established in 2025
                with a focus on educational and group travel experiences.
              </p>
            </div>
          </article>

          <article class="about-journey-step journey-step-reverse">
            <div class="about-journey-node">
              <span>02</span>
            </div>

            <div class="about-journey-card">
              <span class="journey-card-label"> THE PURPOSE </span>

              <h3>Travel should create experiences.</h3>

              <p>
                The purpose is to bring travel, learning, exploration and
                group experiences together in a practical way.
              </p>
            </div>
          </article>

          <article class="about-journey-step">
            <div class="about-journey-node">
              <span>03</span>
            </div>

            <div class="about-journey-card">
              <span class="journey-card-label"> THE APPROACH </span>

              <h3>Plan around the people.</h3>

              <p>
                Every educational group has different needs. Our approach
                focuses on understanding those requirements before planning
                the journey.
              </p>
            </div>
          </article>

          <article class="about-journey-step journey-step-reverse">
            <div class="about-journey-node">
              <span>04</span>
            </div>

            <div class="about-journey-card">
              <span class="journey-card-label"> THE EXPERIENCE </span>

              <h3>Explore. Learn. Experience.</h3>

              <p>
                The journey comes together through thoughtful planning,
                travel, exploration and experiences that groups can carry with
                them.
              </p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- =====================================================
             CORE PRINCIPLES — COMPASS
             ===================================================== -->

    <section class="section about-principles-section">
      <div class="container">
        <div class="about-principles-grid">
          <div class="about-principles-intro">
            <span class="section-eyebrow"> What Guides Us </span>

            <h2>
              Four directions.
              <span>One purpose.</span>
            </h2>

            <p>
              The way we approach educational travel is guided by a few simple
              principles that keep the experience practical and meaningful.
            </p>

            <div class="about-principles-compass" aria-hidden="true">
              <span class="principle-compass-line line-horizontal"></span>
              <span class="principle-compass-line line-vertical"></span>
              <span class="principle-compass-core">✦</span>
            </div>
          </div>

          <div class="about-principles-list">
            <article class="about-principle-card">
              <span class="principle-number"> 01 </span>

              <div class="principle-icon" aria-hidden="true">🎓</div>

              <div>
                <h3>Learning</h3>

                <p>
                  Travel experiences should create space for discovery,
                  observation and learning.
                </p>
              </div>
            </article>

            <article class="about-principle-card">
              <span class="principle-number"> 02 </span>

              <div class="principle-icon" aria-hidden="true">🧭</div>

              <div>
                <h3>Planning</h3>

                <p>
                  A meaningful journey starts with thoughtful planning around
                  the group's requirements.
                </p>
              </div>
            </article>

            <article class="about-principle-card">
              <span class="principle-number"> 03 </span>

              <div class="principle-icon" aria-hidden="true">👥</div>

              <div>
                <h3>People</h3>

                <p>
                  Schools, colleges, teachers and students remain at the
                  centre of the journey.
                </p>
              </div>
            </article>

            <article class="about-principle-card">
              <span class="principle-number"> 04 </span>

              <div class="principle-icon" aria-hidden="true">✦</div>

              <div>
                <h3>Experience</h3>

                <p>
                  The goal is to create journeys that feel meaningful beyond
                  the destination itself.
                </p>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
             WHO WE TRAVEL WITH
             ===================================================== -->

    <section class="section about-audience-section">
      <div class="container">
        <div class="about-section-heading audience-heading">
          <span class="section-eyebrow"> Who We Travel With </span>

          <h2>
            Built for groups with
            <span>different journeys.</span>
          </h2>

          <p>
            Educational travel can look different for every institution and
            group. Our planning approach can adapt around the purpose of the
            journey.
          </p>
        </div>

        <div class="about-audience-rail">
          <article class="about-audience-card">
            <span class="audience-index">01</span>

            <div class="audience-icon" aria-hidden="true">🏫</div>

            <h3>Schools</h3>

            <p>
              Educational travel experiences planned around school groups and
              young learners.
            </p>
          </article>

          <article class="about-audience-card audience-card-offset">
            <span class="audience-index">02</span>

            <div class="audience-icon" aria-hidden="true">🎓</div>

            <h3>Colleges</h3>

            <p>
              Group travel experiences for college students and educational
              communities.
            </p>
          </article>

          <article class="about-audience-card">
            <span class="audience-index">03</span>

            <div class="audience-icon" aria-hidden="true">🏛️</div>

            <h3>Institutions</h3>

            <p>
              Travel planning that can be shaped around institutional
              requirements.
            </p>
          </article>

          <article class="about-audience-card audience-card-offset">
            <span class="audience-index">04</span>

            <div class="audience-icon" aria-hidden="true">👨‍🎓</div>

            <h3>Student Groups</h3>

            <p>
              Experiences designed to bring learning and exploration together.
            </p>
          </article>
        </div>
      </div>
    </section>

    <!-- =====================================================
             EDITORIAL STATEMENT
             ===================================================== -->

    <section class="about-statement-section">
      <div class="container">
        <div class="about-statement">
          <div class="about-statement-mark" aria-hidden="true">✦</div>

          <span class="section-eyebrow"> Our Belief </span>

          <h2>
            A destination is only
            <span>one part of the journey.</span>
          </h2>

          <p>
            What happens along the way — the places discovered, the things
            learned, the people experienced and the memories created — is what
            gives a journey its meaning.
          </p>

          <div class="about-statement-route" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
          </div>
        </div>
      </div>
    </section>

    <!-- =====================================================
             CTA
             ===================================================== -->

    <section class="section final-cta-section about-final-cta">
      <div class="container">
        <div class="final-cta-wrapper">
          <div class="final-cta-content">
            <span class="final-cta-eyebrow"> Start Your Journey </span>

            <h2>
              Your next journey
              <span>starts with an idea.</span>
            </h2>

            <p>
              Share your institution, group or travel requirements with Nexus
              and let's start planning.
            </p>

            <div class="final-cta-actions">
              <a href="<?= BASE_URL ?>index.php#enquiry" class="btn btn-primary">
                Plan Your Tour
                <span aria-hidden="true">→</span>
              </a>

              <a href="tel:7219766277" class="final-cta-phone">
                <span class="final-cta-phone-icon" aria-hidden="true">
                  ☎
                </span>

                <span>
                  <small>Prefer to talk?</small>
                  <strong>Call Nexus</strong>
                </span>
              </a>
            </div>
          </div>

          <div class="final-cta-visual" aria-hidden="true">
            <div class="cta-compass">
              <span>✦</span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- =====================================================
         FOOTER
         ===================================================== -->

  <?php include __DIR__ . '/../includes/footer.php'; ?>

  <!-- =====================================================
         FLOATING WHATSAPP
         ===================================================== -->

  <a
    href="https://wa.me/917448228001?text=Hello%20Nexus%20Educational%20Tours%20and%20Travels,%20I%20would%20like%20to%20enquire%20about%20a%20tour."
    class="floating-whatsapp"
    target="_blank"
    rel="noopener"
    aria-label="Chat with Nexus on WhatsApp">
    <span class="whatsapp-pulse"></span>

    <span class="whatsapp-icon" aria-hidden="true">
      <svg
        viewBox="0 0 24 24"
        width="26"
        height="26"
        xmlns="http://www.w3.org/2000/svg">
        <path
          fill="currentColor"
          d="M20.52 3.48A11.87 11.87 0 0 0 12.05 0
                    C5.4 0 0 5.4 0 12.05c0 2.12.56 4.19
                    1.62 6.02L0 24l6.1-1.6a12 12 0 0 0
                    5.95 1.57h.01c6.64 0 12.04-5.4 12.04-12.04
                    0-3.22-1.25-6.24-3.58-8.45ZM12.06 21.9
                    c-1.86 0-3.68-.5-5.27-1.44l-.38-.23-3.62.95
                    .97-3.53-.25-.4a9.84 9.84 0 0 1-1.51-5.2
                    c0-5.44 4.43-9.87 9.88-9.87 2.63 0 5.1 1.02
                    6.96 2.88a9.8 9.8 0 0 1 2.9 6.97
                    c0 5.44-4.43 9.87-9.88 9.87Z" />

        <path
          fill="currentColor"
          d="M17.53 14.47c-.3-.15-1.76-.87-2.03-.97
                    -.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17
                    -.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48
                    -.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46
                    .13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5
                    .1-.2.05-.37-.02-.52-.07-.15-.67-1.61-.92-2.2
                    -.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37
                    -.27.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.11
                    c.15.2 2.13 3.25 5.16 4.55.72.31 1.28.49 1.72.63
                    .72.23 1.37.2 1.89.12.58-.09 1.76-.72 2.01-1.41
                    .25-.69.25-1.28.17-1.41-.07-.12-.27-.2-.57-.35Z" />
      </svg>
    </span>

    <span class="whatsapp-text"> Chat on WhatsApp </span>
  </a>

  <!-- JAVASCRIPT -->

  <script src="<?= BASE_URL ?>js/main.js"></script>
</body>

</html>