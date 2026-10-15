<?php

require_once __DIR__ . '/../config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('NEXUS_CONTACT_SESSION');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => BASE_URL,
        'secure' => !empty($_SERVER['HTTPS'])
            && strtolower((string) $_SERVER['HTTPS']) !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

if (
    !isset($_SESSION['contact_csrf']) ||
    !is_string($_SESSION['contact_csrf'])
) {
    $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
}

$contactError = '';
$contactSuccess = !empty($_SESSION['contact_success']);

unset($_SESSION['contact_success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['contact_csrf'] ?? '';

    $name = trim((string) ($_POST['name'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $organization = trim((string) ($_POST['organization'] ?? ''));
    $groupSize = trim((string) ($_POST['group_size'] ?? ''));
    $tourDate = trim((string) ($_POST['tour_date'] ?? ''));
    $tourType = trim((string) ($_POST['tour_type'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));

    $allowedTourTypes = [
        '',
        'Educational Tour',
        'School Tour',
        'College Tour',
        'Industrial Visit',
        'Study Tour',
        'Historical Tour',
        'Science Tour',
        'Adventure Tour',
        'Nature Tour',
        'One Day Trip',
        'Multi-Day Tour',
        'Customized Tour',
        'Other',
    ];

    if (
        !is_string($token) ||
        !is_string($sessionToken) ||
        $sessionToken === '' ||
        !hash_equals($sessionToken, $token)
    ) {
        $contactError = 'Invalid request. Please refresh the page and try again.';
    } elseif (
        $name === '' ||
        strlen($name) > 150 ||
        $phone === '' ||
        strlen($phone) > 30 ||
        !preg_match('/^[0-9+() .-]{7,30}$/', $phone) ||
        ($email !== '' && (
            strlen($email) > 190 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        )) ||
        $message === '' ||
        strlen($message) > 10000 ||
        strlen($organization) > 200 ||
        !in_array($tourType, $allowedTourTypes, true)
    ) {
        $contactError = 'Please check your details and try again.';
    } elseif (
        $groupSize !== '' &&
        (
            !ctype_digit($groupSize) ||
            (int) $groupSize < 1 ||
            (int) $groupSize > 100000
        )
    ) {
        $contactError = 'Please enter a valid group size.';
    } elseif (
        $tourDate !== '' &&
        (
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tourDate) ||
            (
                ($parsedDate = DateTimeImmutable::createFromFormat(
                    '!Y-m-d',
                    $tourDate
                )) === false
            ) ||
            $parsedDate->format('Y-m-d') !== $tourDate
        )
    ) {
        $contactError = 'Please enter a valid tour date.';
    } else {
        $subject = $tourType !== ''
            ? $tourType
            : 'Website Contact Enquiry';

        $fullMessage = $message;

        if ($organization !== '') {
            $fullMessage .= "\n\nOrganization: " . $organization;
        }

        if ($groupSize !== '') {
            $fullMessage .= "\nApproximate group size: " . $groupSize;
        }

        if ($tourDate !== '') {
            $fullMessage .= "\nPreferred tour date: " . $tourDate;
        }

        if ($tourType !== '') {
            $fullMessage .= "\nTour type: " . $tourType;
        }

        try {
            require_once __DIR__ . '/../config/database.php';

            $stmt = $pdo->prepare(
                'INSERT INTO contact_enquiries
                    (name, phone, email, subject, message)
                 VALUES
                    (:name, :phone, :email, :subject, :message)'
            );

            $stmt->execute([
                'name' => $name,
                'phone' => $phone,
                'email' => $email !== '' ? $email : null,
                'subject' => $subject,
                'message' => $fullMessage,
            ]);

            $_SESSION['contact_success'] = true;
            $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));

            header('Location: ' . BASE_URL . 'pages/contact.php');
            exit;
        } catch (Throwable $exception) {
            error_log(
                'Nexus contact enquiry save failed: ' .
                    $exception->getMessage()
            );

            $contactError = 'Your enquiry could not be submitted. Please try again.';
        }
    }
}

function contactEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contact Us | Nexus Educational Tours and Travels</title>

    <meta
        name="description"
        content="Contact Nexus Educational Tours and Travels in Chakan, Pune, for educational tours, school trips, college tours and group travel planning.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/responsive.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/animations.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/contact.css">
</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>

        <section class="page-hero">
            <div class="container">
                <span class="section-eyebrow">LET'S PLAN SOMETHING MEMORABLE</span>
                <h1>Let's Start Your <span>Journey</span></h1>
                <p>
                    Planning an educational tour for your school, college or group?
                    Connect with Nexus Educational Tours and Travels.
                </p>
                <a href="#contact-form" class="btn btn-primary">
                    Send an Enquiry <span aria-hidden="true">→</span>
                </a>
            </div>
        </section>

        <section class="contact-section section-padding">
            <div class="container">

                <div class="section-heading">
                    <span class="section-eyebrow">GET IN TOUCH</span>
                    <h2>We're Here to <span>Help</span></h2>
                    <p>
                        Tell us about your educational tour requirements.
                        We will be happy to hear from you.
                    </p>
                </div>

                <div class="contact-grid">

                    <div class="contact-info">

                        <div class="contact-info-card">
                            <div class="contact-icon" aria-hidden="true">☎</div>
                            <div>
                                <h3>Call Us</h3>
                                <p>Speak with our team about your tour.</p>
                                <a href="tel:7219766277">7219766277</a><br>
                                <a href="tel:7219493164">7219493164</a>
                            </div>
                        </div>

                        <div class="contact-info-card">
                            <div class="contact-icon" aria-hidden="true">✉</div>
                            <div>
                                <h3>Email Us</h3>
                                <p>Send us your questions and requirements.</p>
                                <a href="mailto:nexuseducationaltoursandtravel@gmail.com">
                                    nexuseducationaltoursandtravel@gmail.com
                                </a>
                            </div>
                        </div>

                        <div class="contact-info-card">
                            <div class="contact-icon" aria-hidden="true">⌖</div>
                            <div>
                                <h3>Visit Us</h3>
                                <p>
                                    Ekatanagar, Chakan, Tal-Khed,
                                    Pune, Maharashtra - 410501
                                </p>
                            </div>
                        </div>

                        <div class="contact-whatsapp-card">
                            <h3>Prefer WhatsApp?</h3>
                            <p>
                                You can contact us directly on WhatsApp
                                for your tour enquiry.
                            </p>
                            <a
                                href="https://wa.me/917448228001"
                                class="btn btn-primary"
                                target="_blank"
                                rel="noopener noreferrer">
                                Chat on WhatsApp <span aria-hidden="true">↗</span>
                            </a>
                        </div>

                    </div>

                    <div class="contact-form-card" id="contact-form">

                        <span class="section-eyebrow">YOUR NEXT EXPERIENCE</span>
                        <h2>Send an Enquiry</h2>
                        <p>Share a few details so we can understand your requirements.</p>

                        <?php if ($contactSuccess): ?>
                            <p role="status">
                                Thank you! Your enquiry has been submitted successfully.
                            </p>
                        <?php endif; ?>

                        <?php if ($contactError !== ''): ?>
                            <p role="alert">
                                <?= contactEscape($contactError) ?>
                            </p>
                        <?php endif; ?>

                        <form action="" method="post">

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= contactEscape(
                                            (string) $_SESSION['contact_csrf']
                                        ) ?>">

                            <div class="form-group">
                                <label for="contact-name">Full Name *</label>
                                <input
                                    type="text"
                                    id="contact-name"
                                    name="name"
                                    maxlength="150"
                                    placeholder="Enter your full name"
                                    autocomplete="name"
                                    required>
                            </div>

                            <div class="form-row">

                                <div class="form-group">
                                    <label for="contact-phone">Phone Number *</label>
                                    <input
                                        type="tel"
                                        id="contact-phone"
                                        name="phone"
                                        maxlength="30"
                                        placeholder="Your phone number"
                                        autocomplete="tel"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="contact-email">Email Address</label>
                                    <input
                                        type="email"
                                        id="contact-email"
                                        name="email"
                                        maxlength="190"
                                        placeholder="you@example.com"
                                        autocomplete="email">
                                </div>

                            </div>

                            <div class="form-group">
                                <label for="contact-organization">
                                    School / College / Organization
                                </label>
                                <input
                                    type="text"
                                    id="contact-organization"
                                    name="organization"
                                    maxlength="200"
                                    placeholder="Enter organization name">
                            </div>

                            <div class="form-row">

                                <div class="form-group">
                                    <label for="contact-group-size">
                                        Approximate Group Size
                                    </label>
                                    <input
                                        type="number"
                                        id="contact-group-size"
                                        name="group_size"
                                        min="1"
                                        max="100000"
                                        step="1"
                                        placeholder="Number of people">
                                </div>

                                <div class="form-group">
                                    <label for="contact-date">Preferred Tour Date</label>
                                    <input
                                        type="date"
                                        id="contact-date"
                                        name="tour_date">
                                </div>

                            </div>

                            <div class="form-group">
                                <label for="contact-tour-type">Tour Type</label>
                                <select id="contact-tour-type" name="tour_type">
                                    <option value="">Select tour type</option>
                                    <option value="Educational Tour">Educational Tour</option>
                                    <option value="School Tour">School Tour</option>
                                    <option value="College Tour">College Tour</option>
                                    <option value="Industrial Visit">Industrial Visit</option>
                                    <option value="Study Tour">Study Tour</option>
                                    <option value="Historical Tour">Historical Tour</option>
                                    <option value="Science Tour">Science Tour</option>
                                    <option value="Adventure Tour">Adventure Tour</option>
                                    <option value="Nature Tour">Nature Tour</option>
                                    <option value="One Day Trip">One Day Trip</option>
                                    <option value="Multi-Day Tour">Multi-Day Tour</option>
                                    <option value="Customized Tour">Customized Tour</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="contact-message">Your Requirements *</label>
                                <textarea
                                    id="contact-message"
                                    name="message"
                                    rows="5"
                                    maxlength="10000"
                                    placeholder="Tell us about your destination, dates and requirements..."
                                    required></textarea>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary contact-submit">
                                Submit Enquiry <span aria-hidden="true">→</span>
                            </button>

                            <p class="form-note">
                                * Required fields. Your enquiry will be reviewed by our team.
                            </p>

                        </form>
                    </div>

                </div>
            </div>
        </section>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <a
        href="https://wa.me/917448228001?text=Hello%20Nexus%20Educational%20Tours%20and%20Travels,%20I%20would%20like%20to%20enquire%20about%20a%20tour."
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

        <span class="whatsapp-text">Chat on WhatsApp</span>
    </a>

    <script src="<?= BASE_URL ?>js/main.js"></script>

</body>

</html>