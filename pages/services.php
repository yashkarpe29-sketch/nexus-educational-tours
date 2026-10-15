<?php
require_once __DIR__ . '/../config.php';
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>Our Services | Nexus Educational Tours and Travels</title>

  <meta
    name="description"
    content="Discover educational tour planning, school and college group travel, industrial visits, one-day trips, multi-day tours and customized travel planning with Nexus Educational Tours and Travels." />

  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/responsive.css" />
  <link rel="stylesheet" href="../css/animations.css" />
</head>

<body>
  <!-- HEADER -->

  <?php include __DIR__ . '/../includes/header.php'; ?>

  <main>
    <!-- SERVICES HERO -->

    <section class="section services-hero" id="services-hero">
      <div class="container">
        <div class="services-hero-layout">
          <div class="services-hero-content">
            <span class="section-eyebrow animated-eyebrow">
              <span class="eyebrow-line"></span>
              What We Offer
            </span>

            <h1>
              Thoughtful planning.
              <span>Meaningful journeys.</span>
            </h1>

            <p>
              From educational visits to customized group journeys, explore
              travel planning services designed around your institution, group
              and purpose.
            </p>

            <div class="services-hero-actions">
              <a href="#our-services" class="btn btn-primary">
                Explore Services <span aria-hidden="true">↓</span>
              </a>

              <a href="<?= BASE_URL ?>index.php#enquiry" class="btn btn-secondary">
                Make an Enquiry <span aria-hidden="true">→</span>
              </a>
            </div>
          </div>

          <div class="services-hero-visual" aria-hidden="true">
            <div class="services-visual-orbit orbit-one"></div>
            <div class="services-visual-orbit orbit-two"></div>

            <div class="services-visual-card">
              <span class="services-visual-kicker">THE NEXUS APPROACH</span>

              <div class="services-visual-symbol">✦</div>

              <h2>Learn. Explore. Experience.</h2>

              <div class="services-visual-line"></div>

              <div class="services-visual-steps">
                <span>01 &nbsp; Understand</span>
                <span>02 &nbsp; Plan</span>
                <span>03 &nbsp; Coordinate</span>
              </div>
            </div>

            <span class="services-floating-dot dot-one"></span>
            <span class="services-floating-dot dot-two"></span>
            <span class="services-floating-dot dot-three"></span>
          </div>
        </div>
      </div>
    </section>

    <!-- INTRODUCTION -->

    <section class="section services-intro">
      <div class="container">
        <div class="services-intro-panel">
          <div class="services-intro-mark" aria-hidden="true">N</div>

          <div class="services-intro-copy">
            <span class="section-eyebrow animated-eyebrow">
              <span class="eyebrow-line"></span>
              Planning With Purpose
            </span>

            <h2>
              Every group is different.
              <span>Your journey should be too.</span>
            </h2>

            <p>
              The right journey begins with understanding your goals. We
              discuss your requirements and help plan a travel experience that
              suits your group, schedule and purpose.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- OUR SERVICES -->

    <section class="section services-list-section" id="our-services">
      <div class="container">
        <div class="section-header services-section-header">
          <span class="section-eyebrow animated-eyebrow">
            <span class="eyebrow-line"></span>
            Our Services
          </span>

          <h2>
            Support for every
            <span>stage of your journey.</span>
          </h2>

          <p>
            Explore the types of travel planning we can discuss for your
            institution, students or group.
          </p>
        </div>

        <div class="services-grid">
          <!-- SERVICE 01 -->

          <article class="service-card">
            <div class="service-card-top">
              <span class="service-number">01</span>
              <span class="service-icon" aria-hidden="true">🎓</span>
            </div>

            <h3>Educational Tour Planning</h3>

            <p>
              Plan educational journeys that connect learning objectives with
              exploration and practical exposure.
            </p>

            <a href="<?= BASE_URL ?>index.php#enquiry" class="service-card-link">
              Discuss Your Tour <span aria-hidden="true">→</span>
            </a>
          </article>

          <!-- SERVICE 02 -->

          <article class="service-card">
            <div class="service-card-top">
              <span class="service-number">02</span>
              <span class="service-icon" aria-hidden="true">🏫</span>
            </div>

            <h3>School &amp; College Group Tours</h3>

            <p>
              Discuss group travel requirements, schedules and educational
              interests for schools, colleges and student groups.
            </p>

            <a href="<?= BASE_URL ?>index.php#enquiry" class="service-card-link">
              Discuss Your Tour <span aria-hidden="true">→</span>
            </a>
          </article>

          <!-- SERVICE 03 -->

          <article class="service-card">
            <div class="service-card-top">
              <span class="service-number">03</span>
              <span class="service-icon" aria-hidden="true">🏭</span>
            </div>

            <h3>Industrial Visits</h3>

            <p>
              Discuss industry-focused visits that support practical learning
              and help students explore real-world environments.
            </p>

            <a href="<?= BASE_URL ?>index.php#enquiry" class="service-card-link">
              Discuss Your Tour <span aria-hidden="true">→</span>
            </a>
          </article>

          <!-- SERVICE 04 -->

          <article class="service-card">
            <div class="service-card-top">
              <span class="service-number">04</span>
              <span class="service-icon" aria-hidden="true">☀️</span>
            </div>

            <h3>One-Day Trips</h3>

            <p>
              Explore one-day group trip options based on your preferred
              destination, available time and group requirements.
            </p>

            <a href="<?= BASE_URL ?>index.php#enquiry" class="service-card-link">
              Discuss Your Tour <span aria-hidden="true">→</span>
            </a>
          </article>

          <!-- SERVICE 05 -->

          <article class="service-card">
            <div class="service-card-top">
              <span class="service-number">05</span>
              <span class="service-icon" aria-hidden="true">🗓️</span>
            </div>

            <h3>Multi-Day Tour Planning</h3>

            <p>
              Discuss multi-day itineraries, travel dates and group
              requirements to shape a suitable journey plan.
            </p>

            <a href="<?= BASE_URL ?>index.php#enquiry" class="service-card-link">
              Discuss Your Tour <span aria-hidden="true">→</span>
            </a>
          </article>

          <!-- SERVICE 06 -->

          <article class="service-card">
            <div class="service-card-top">
              <span class="service-number">06</span>
              <span class="service-icon" aria-hidden="true">🧭</span>
            </div>

            <h3>Customized Tour Planning</h3>

            <p>
              Share your destination ideas, group size, schedule and
              preferences to discuss a journey suited to your needs.
            </p>

            <a href="<?= BASE_URL ?>index.php#enquiry" class="service-card-link">
              Discuss Your Tour <span aria-hidden="true">→</span>
            </a>
          </article>
        </div>
      </div>
    </section>

    <!-- HOW WE PLAN -->

    <section class="section services-process-section">
      <div class="container">
        <div class="section-header services-section-header">
          <span class="section-eyebrow animated-eyebrow">
            <span class="eyebrow-line"></span>
            Our Planning Approach
          </span>

          <h2>
            From your first enquiry
            <span>to a clearer travel plan.</span>
          </h2>

          <p>
            A simple starting point for discussing your group's travel
            requirements.
          </p>
        </div>

        <div class="services-process-grid">
          <article class="process-card">
            <span class="process-number">01</span>
            <div class="process-icon" aria-hidden="true">💬</div>
            <h3>Share Your Requirements</h3>
            <p>
              Tell us about your institution, group size, destination ideas
              and preferred travel dates.
            </p>
          </article>

          <article class="process-card">
            <span class="process-number">02</span>
            <div class="process-icon" aria-hidden="true">📝</div>
            <h3>Discuss the Plan</h3>
            <p>
              Discuss the purpose of your journey, schedule and the options
              that may suit your group.
            </p>
          </article>

          <article class="process-card">
            <span class="process-number">03</span>
            <div class="process-icon" aria-hidden="true">🧭</div>
            <h3>Review the Details</h3>
            <p>
              Review the proposed travel details and clarify the requirements
              before confirming arrangements.
            </p>
          </article>
        </div>
      </div>
    </section>

    <!-- WHY NEXUS -->

    <section class="section services-values-section">
      <div class="container">
        <div class="services-values-panel">
          <div class="services-values-content">
            <span class="section-eyebrow animated-eyebrow">
              <span class="eyebrow-line"></span>
              The Nexus Approach
            </span>

            <h2>
              A journey begins with
              <span>the right conversation.</span>
            </h2>

            <p>
              Good planning starts with listening. Share what your group wants
              to learn, explore or experience, and begin discussing the
              details of your journey.
            </p>

            <a href="<?= BASE_URL ?>index.php#enquiry" class="btn btn-primary">
              Tell Us About Your Journey
              <span aria-hidden="true">→</span>
            </a>
          </div>

          <div class="services-values-list">
            <div class="service-value-item">
              <span class="value-check" aria-hidden="true">✓</span>
              <div>
                <h3>Purpose-led discussions</h3>
                <p>Start with the goals and needs of your group.</p>
              </div>
            </div>

            <div class="service-value-item">
              <span class="value-check" aria-hidden="true">✓</span>
              <div>
                <h3>Group-focused planning</h3>
                <p>Discuss requirements specific to your institution.</p>
              </div>
            </div>

            <div class="service-value-item">
              <span class="value-check" aria-hidden="true">✓</span>
              <div>
                <h3>Clear travel discussions</h3>
                <p>Review important details before making decisions.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->

    <section class="section services-cta-section">
      <div class="container">
        <div class="services-cta-panel">
          <div class="services-cta-copy">
            <span class="section-eyebrow">
              <span class="eyebrow-line"></span>
              Let's Start Planning
            </span>

            <h2>
              Have a journey in mind?
              <span>Let's talk about it.</span>
            </h2>

            <p>
              Tell us about your group, travel purpose and requirements. Start
              a conversation about your next journey.
            </p>

            <div class="services-cta-actions">
              <a href="<?= BASE_URL ?>index.php#enquiry" class="btn btn-primary">
                Send an Enquiry <span aria-hidden="true">→</span>
              </a>

              <a href="tel:7219766277" class="btn btn-secondary">
                Call 7219766277
              </a>
            </div>
          </div>

          <div class="services-cta-symbol" aria-hidden="true">
            <span class="cta-ring ring-one"></span>
            <span class="cta-ring ring-two"></span>
            <span class="cta-star">✦</span>
            <span class="cta-caption">YOUR NEXT JOURNEY</span>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- FOOTER -->

  <?php include __DIR__ . '/../includes/footer.php'; ?>

  <!-- FLOATING WHATSAPP -->

  <a
    href="https://wa.me/917448228001?text=Hello%20Nexus%20Educational%20Tours%20and%20Travels%2C%20I%20would%20like%20to%20enquire%20about%20your%20services."
    class="floating-whatsapp"
    target="_blank"
    rel="noopener noreferrer"
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
            C5.4 0 0 5.4 0 12.05c0 2.12.56 4.19 1.62 6.02L0 24l6.1-1.6
            a12 12 0 0 0 5.95 1.57h.01c6.64 0 12.04-5.4 12.04-12.04
            0-3.22-1.25-6.24-3.58-8.45ZM12.06 21.9c-1.86 0-3.68-.5-5.27-1.44
            l-.38-.23-3.62.95.97-3.53-.25-.4a9.84 9.84 0 0 1-1.51-5.2
            c0-5.44 4.43-9.87 9.88-9.87 2.63 0 5.1 1.02 6.96 2.88
            a9.8 9.8 0 0 1 2.9 6.97c0 5.44-4.43 9.87-9.88 9.87Z" />
        <path
          fill="currentColor"
          d="M17.53 14.47c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15
            -.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48
            -.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52
            .15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.61-.92-2.2
            -.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.05 1.03-1.05 2.51
            s1.08 2.91 1.23 3.11c.15.2 2.13 3.25 5.16 4.55.72.31 1.28.49 1.72.63
            .72.23 1.37.2 1.89.12.58-.09 1.76-.72 2.01-1.41.25-.69.25-1.28.17-1.41
            -.07-.12-.27-.2-.57-.35Z" />
      </svg>
    </span>

    <span class="whatsapp-text">Chat on WhatsApp</span>
  </a>

  <script src="<?= BASE_URL ?>js/main.js"></script>
</body>

</html>