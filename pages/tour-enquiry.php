<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

$tourId = filter_input(INPUT_GET, 'tour_id', FILTER_VALIDATE_INT);

if (!$tourId || $tourId < 1) {
    header('Location: ' . rtrim(BASE_URL, '/') . '/pages/tours.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "SELECT id, title, category, destination, duration
         FROM tours
         WHERE id = :id AND status = 'published'
         LIMIT 1"
    );
    $stmt->execute(['id' => $tourId]);
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $ex) {
    error_log('Nexus tour enquiry page: ' . $ex->getMessage());
    http_response_code(500);
    exit('Unable to load the enquiry form right now. Please try again later.');
}

if (!$tour) {
    http_response_code(404);
    exit('This tour is not available. Please return to the Tours page.');
}

function enquiry_h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$today = date('Y-m-d');
$pageTitle = (string)$tour['title'] . ' — Tour Enquiry';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= enquiry_h($pageTitle) ?> | Nexus Educational Tours</title>
    <meta name="description" content="Submit an educational tour enquiry to Nexus Educational Tours and Travels.">
    <link rel="stylesheet" href="<?= enquiry_h(rtrim(BASE_URL, '/') . '/css/style.css') ?>">
    <link rel="stylesheet" href="<?= enquiry_h(rtrim(BASE_URL, '/') . '/css/responsive.css') ?>">
    <link rel="stylesheet" href="<?= enquiry_h(rtrim(BASE_URL, '/') . '/css/animations.css') ?>">

    <style>
        .tour-enquiry-page {
            min-height: 75vh;
            padding: 64px 20px 80px;
            background: #F7FAFC;
            color: #102A43;
        }

        .tour-enquiry-wrap {
            max-width: 900px;
            margin: 0 auto;
        }

        .tour-enquiry-heading {
            margin-bottom: 28px;
        }

        .tour-enquiry-eyebrow {
            color: #2B83C6;
            font-size: .8rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .tour-enquiry-heading h1 {
            margin: 10px 0;
            color: #173B67;
            font-size: clamp(2rem, 5vw, 3rem);
            line-height: 1.15;
        }

        .tour-enquiry-heading p {
            line-height: 1.7;
        }

        .tour-enquiry-card {
            padding: clamp(20px, 4vw, 38px);
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 18px 50px rgba(23, 59, 103, .08);
        }

        .tour-enquiry-selected {
            margin-bottom: 28px;
            padding: 18px 20px;
            background: #F0F6FC;
            border-left: 4px solid #2B83C6;
            border-radius: 10px;
        }

        .tour-enquiry-selected strong {
            display: block;
            margin-bottom: 5px;
            color: #173B67;
        }

        .tour-enquiry-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .tour-enquiry-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
        }

        .tour-enquiry-field.full {
            grid-column: 1 / -1;
        }

        .tour-enquiry-field label {
            font-weight: 700;
            font-size: .94rem;
        }

        .tour-enquiry-field input,
        .tour-enquiry-field select,
        .tour-enquiry-field textarea {
            box-sizing: border-box;
            width: 100%;
            min-height: 48px;
            padding: 12px 14px;
            color: #102A43;
            background: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font: inherit;
        }

        .tour-enquiry-field textarea {
            min-height: 130px;
            resize: vertical;
        }

        .tour-enquiry-field input:focus,
        .tour-enquiry-field select:focus,
        .tour-enquiry-field textarea:focus {
            outline: 3px solid rgba(43, 131, 198, .2);
            border-color: #2B83C6;
        }

        .tour-enquiry-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 50px;
            margin-top: 24px;
            padding: 12px 24px;
            color: #fff;
            background: #173B67;
            border: 0;
            border-radius: 10px;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
        }

        .tour-enquiry-submit:hover {
            background: #2B83C6;
        }

        .tour-enquiry-back {
            display: inline-block;
            margin-top: 22px;
            color: #173B67;
            font-weight: 700;
        }

        .tour-enquiry-note {
            color: #526579;
            font-size: .88rem;
            line-height: 1.6;
        }

        @media (max-width: 640px) {
            .tour-enquiry-page {
                padding: 38px 14px 54px;
            }

            .tour-enquiry-grid {
                grid-template-columns: 1fr;
            }

            .tour-enquiry-field.full {
                grid-column: auto;
            }

            .tour-enquiry-submit {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php
    $headerFile = __DIR__ . '/../includes/header.php';
    if (is_file($headerFile)) {
        include $headerFile;
    }
    ?>

    <main class="tour-enquiry-page">
        <div class="tour-enquiry-wrap">
            <header class="tour-enquiry-heading">
                <span class="tour-enquiry-eyebrow">Plan your learning journey</span>
                <h1>Request a Tour Quotation</h1>
                <p>
                    Share your institution and travel requirements.
                    Our team can review the details and get in touch with you.
                </p>
            </header>

            <section class="tour-enquiry-card" aria-label="Tour enquiry form">
                <div class="tour-enquiry-selected">
                    <strong>Selected Tour</strong>
                    <span><?= enquiry_h($tour['title']) ?></span>
                    <?php if (!empty($tour['destination'])): ?>
                        <div>Destination: <?= enquiry_h($tour['destination']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($tour['duration'])): ?>
                        <div>Duration: <?= enquiry_h($tour['duration']) ?></div>
                    <?php endif; ?>
                </div>

                <form action="<?= enquiry_h(rtrim(BASE_URL, '/') . '/submit_enquiry.php') ?>"
                    method="post">
                    <input type="hidden" name="tour_id" value="<?= (int)$tour['id'] ?>">

                    <div class="tour-enquiry-grid">
                        <div class="tour-enquiry-field full">
                            <label for="institution_name">School / College / Institution *</label>
                            <input id="institution_name" name="institution_name"
                                type="text" maxlength="200" required
                                autocomplete="organization">
                        </div>

                        <div class="tour-enquiry-field">
                            <label for="contact_person">Contact Person *</label>
                            <input id="contact_person" name="contact_person"
                                type="text" maxlength="150" required
                                autocomplete="name">
                        </div>

                        <div class="tour-enquiry-field">
                            <label for="phone">Mobile Number *</label>
                            <input id="phone" name="phone" type="tel"
                                maxlength="25" required
                                autocomplete="tel"
                                pattern="[0-9+(). -]{7,25}"
                                title="Enter a valid phone number.">
                        </div>

                        <div class="tour-enquiry-field">
                            <label for="email">Email Address</label>
                            <input id="email" name="email" type="email"
                                maxlength="190" autocomplete="email">
                        </div>

                        <div class="tour-enquiry-field">
                            <label for="starting_location">Starting Location</label>
                            <input id="starting_location" name="starting_location"
                                type="text" maxlength="200"
                                placeholder="e.g. Chakan, Pune">
                        </div>

                        <div class="tour-enquiry-field">
                            <label for="selected_plan">Student Group / Standard</label>
                            <select id="selected_plan" name="selected_plan">
                                <option value="">Select student group</option>
                                <option value="Pre-Primary Tier">Pre-Primary</option>
                                <option value="1st to 4th Standard">1st to 4th Standard</option>
                                <option value="5th to 7th Standard">5th to 7th Standard</option>
                                <option value="8th to 10th Standard">8th to 10th Standard</option>
                                <option value="11th and 12th Standard / College">11th, 12th Standard / College</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="tour-enquiry-field">
                            <label for="students_count">Number of Students</label>
                            <input id="students_count" name="students_count"
                                type="number" min="1" max="2147483647"
                                inputmode="numeric">
                        </div>

                        <div class="tour-enquiry-field">
                            <label for="teachers_count">Number of Teachers / Staff</label>
                            <input id="teachers_count" name="teachers_count"
                                type="number" min="0" max="2147483647"
                                inputmode="numeric">
                        </div>

                        <div class="tour-enquiry-field">
                            <label for="preferred_date">Preferred Travel Date</label>
                            <input id="preferred_date" name="preferred_date"
                                type="date" min="<?= enquiry_h($today) ?>">
                        </div>

                        <div class="tour-enquiry-field full">
                            <label for="message">Additional Requirements</label>
                            <textarea id="message" name="message"
                                maxlength="10000"
                                placeholder="Tell us about transport, meals, learning objectives or other requirements."></textarea>
                        </div>
                    </div>

                    <p class="tour-enquiry-note">
                        Fields marked * are required. Please check your contact details before submitting.
                    </p>

                    <button class="tour-enquiry-submit" type="submit">
                        Submit Tour Enquiry →
                    </button>
                </form>

                <a class="tour-enquiry-back"
                    href="<?= enquiry_h(rtrim(BASE_URL, '/') . '/pages/tours.php') ?>">
                    ← Back to All Tours
                </a>
            </section>
        </div>
    </main>

    <?php
    $footerFile = __DIR__ . '/../includes/footer.php';
    if (is_file($footerFile)) {
        include $footerFile;
    }
    ?>
</body>

</html>