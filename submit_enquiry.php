<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . rtrim(BASE_URL, '/') . '/pages/tours.php');
    exit;
}

function enquiry_response(string $title, string $message, bool $success = false): void
{
    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $baseUrl = rtrim(BASE_URL, '/');

    http_response_code($success ? 200 : 400);
?>
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= $safeTitle ?> | Nexus Educational Tours</title>
        <style>
            body {
                margin: 0;
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 24px;
                box-sizing: border-box;
                background: #F7FAFC;
                color: #102A43;
                font-family: Arial, sans-serif;
            }

            .panel {
                width: min(100%, 560px);
                box-sizing: border-box;
                padding: 32px;
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 18px;
                box-shadow: 0 18px 50px rgba(23, 59, 103, .10);
            }

            h1 {
                color: #173B67;
            }

            p {
                line-height: 1.7;
            }

            a {
                display: inline-block;
                margin-top: 12px;
                padding: 12px 18px;
                border-radius: 8px;
                background: #173B67;
                color: #fff;
                text-decoration: none;
            }
        </style>
    </head>

    <body>
        <main class="panel">
            <h1><?= $safeTitle ?></h1>
            <p><?= $safeMessage ?></p>
            <a href="<?= htmlspecialchars($baseUrl . '/pages/tours.php', ENT_QUOTES, 'UTF-8') ?>">
                Back to Tours
            </a>
        </main>
    </body>

    </html>
<?php
    exit;
}

try {
    // Required contact details
    $tourId = filter_input(INPUT_POST, 'tour_id', FILTER_VALIDATE_INT);
    $institution = trim((string)($_POST['institution_name'] ?? ''));
    $contactPerson = trim((string)($_POST['contact_person'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));

    // Optional trip details
    $startingLocation = trim((string)($_POST['starting_location'] ?? ''));
    $preferredDate = trim((string)($_POST['preferred_date'] ?? ''));
    $studentsRaw = trim((string)($_POST['students_count'] ?? $_POST['estimated_students'] ?? ''));
    $teachersRaw = trim((string)($_POST['teachers_count'] ?? ''));
    $selectedPlan = trim((string)($_POST['selected_plan'] ?? ''));
    $message = trim((string)($_POST['message'] ?? $_POST['requirements'] ?? ''));

    if (!$tourId || $tourId < 1) {
        throw new RuntimeException('Please select a valid tour and try again.');
    }

    if ($institution === '' || $contactPerson === '' || $phone === '') {
        throw new RuntimeException('Please fill in the institution name, contact person and phone number.');
    }

    if (mb_strlen($institution) > 200 || mb_strlen($contactPerson) > 150 || mb_strlen($phone) > 25) {
        throw new RuntimeException('One or more fields are too long. Please check your details.');
    }

    if (!preg_match('/^[0-9+\s().-]{7,25}$/', $phone)) {
        throw new RuntimeException('Please enter a valid phone number.');
    }

    if ($email !== '' && (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190)) {
        throw new RuntimeException('Please enter a valid email address.');
    }

    $students = null;
    if ($studentsRaw !== '') {
        if (!ctype_digit($studentsRaw) || (int)$studentsRaw > 2147483647) {
            throw new RuntimeException('Please enter a valid number of students.');
        }
        $students = (int)$studentsRaw;
    }

    $teachers = null;
    if ($teachersRaw !== '') {
        if (!ctype_digit($teachersRaw) || (int)$teachersRaw > 2147483647) {
            throw new RuntimeException('Please enter a valid number of teachers.');
        }
        $teachers = (int)$teachersRaw;
    }

    if ($preferredDate !== '') {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $preferredDate);
        $dateErrors = DateTimeImmutable::getLastErrors();

        if (
            !$date ||
            ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0)) ||
            $date->format('Y-m-d') !== $preferredDate
        ) {
            throw new RuntimeException('Please select a valid preferred date.');
        }

        if ($preferredDate < date('Y-m-d')) {
            throw new RuntimeException('The preferred date cannot be in the past.');
        }
    } else {
        $preferredDate = null;
    }

    // Only accept a tour that is currently published.
    $tourQuery = $pdo->prepare(
        "SELECT id, title, destination, category, duration
         FROM tours
         WHERE id = :id AND status = 'published'
         LIMIT 1"
    );
    $tourQuery->execute(['id' => $tourId]);
    $tour = $tourQuery->fetch(PDO::FETCH_ASSOC);

    if (!$tour) {
        throw new RuntimeException('This tour is unavailable. Please return to the Tours page and choose another tour.');
    }

    // Keep the selected school/college level with the enquiry requirements.
    if ($selectedPlan !== '') {
        $message = "Selected educational level: " . $selectedPlan
            . ($message !== '' ? "\n\nAdditional requirements:\n" . $message : '');
    }

    $insert = $pdo->prepare(
        "INSERT INTO tour_enquiries (
            enquiry_type,
            tour_id,
            institution_name,
            contact_person,
            phone,
            email,
            starting_location,
            destination,
            tour_category,
            preferred_date,
            duration,
            students_count,
            teachers_count,
            requirements
        ) VALUES (
            'tour',
            :tour_id,
            :institution_name,
            :contact_person,
            :phone,
            :email,
            :starting_location,
            :destination,
            :tour_category,
            :preferred_date,
            :duration,
            :students_count,
            :teachers_count,
            :requirements
        )"
    );

    $insert->execute([
        'tour_id' => (int)$tour['id'],
        'institution_name' => $institution,
        'contact_person' => $contactPerson,
        'phone' => $phone,
        'email' => $email !== '' ? $email : null,
        'starting_location' => $startingLocation !== '' ? $startingLocation : null,
        'destination' => $tour['destination'] ?: $tour['title'],
        'tour_category' => $tour['category'] ?: null,
        'preferred_date' => $preferredDate,
        'duration' => $tour['duration'] ?: null,
        'students_count' => $students,
        'teachers_count' => $teachers,
        'requirements' => $message !== '' ? $message : null,
    ]);

    enquiry_response(
        'Enquiry submitted successfully!',
        'Thank you. Your tour enquiry has been received. Our team can review your request and contact you using the details you provided.',
        true
    );
} catch (RuntimeException $ex) {
    enquiry_response('Please check your details', $ex->getMessage());
} catch (Throwable $ex) {
    error_log('Nexus tour enquiry submission failed: ' . $ex->getMessage());

    enquiry_response(
        'Unable to submit enquiry',
        'Something went wrong while saving your enquiry. Please try again later or contact Nexus directly.'
    );
}
