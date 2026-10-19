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
function postString(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}
function nullableValue(array $values, string $key): ?string
{
    return ($values[$key] ?? '') !== '' ? (string)$values[$key] : null;
}

$allowedStatuses = ['draft', 'published', 'archived'];
$allowedCurrencies = ['INR', 'USD', 'EUR', 'GBP'];
$maxImageSize = 5 * 1024 * 1024;
$uploadDirectory = dirname(__DIR__) . '/uploads/tours';
$projectBaseUrl = '/nexus-educational-tours/';

if (empty($_SESSION['edit_tour_csrf'])) $_SESSION['edit_tour_csrf'] = bin2hex(random_bytes(32));
$tourId = filter_var($_GET['id'] ?? $_POST['tour_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($tourId === false || $tourId === null) {
    http_response_code(400);
    exit('Invalid tour ID.');
}

$errors = [];
$message = (isset($_GET['saved']) && $_GET['saved'] === '1') ? 'Tour updated successfully.' : '';
$uploadedFiles = [];
$galleryRows = [];

try {
    $stmt = $pdo->prepare('SELECT * FROM tours WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $tourId]);
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$tour) {
        http_response_code(404);
        exit('Tour not found.');
    }
} catch (PDOException $exception) {
    error_log('Nexus edit tour lookup failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Unable to load this tour right now.');
}

$galleryStmt = $pdo->prepare('SELECT id, image_path, sort_order FROM tour_gallery WHERE tour_id = :tour_id ORDER BY sort_order ASC, id ASC');
$galleryStmt->execute(['tour_id' => $tourId]);
$galleryRows = $galleryStmt->fetchAll(PDO::FETCH_ASSOC);

$fields = [
    'title' => (string)($tour['title'] ?? ''),
    'category' => (string)($tour['category'] ?? ''),
    'destination' => (string)($tour['destination'] ?? ''),
    'duration' => (string)($tour['duration'] ?? ''),
    'short_description' => (string)($tour['short_description'] ?? ''),
    'description' => (string)($tour['description'] ?? ''),
    'price_from' => (string)($tour['price_from'] ?? ''),
    'currency' => (string)($tour['currency'] ?? 'INR'),
    'min_group_size' => (string)($tour['min_group_size'] ?? ''),
    'max_group_size' => (string)($tour['max_group_size'] ?? ''),
    'start_date' => (string)($tour['start_date'] ?? ''),
    'end_date' => (string)($tour['end_date'] ?? ''),
    'booking_deadline' => (string)($tour['booking_deadline'] ?? ''),
    'best_season' => (string)($tour['best_season'] ?? ''),
    'meeting_point' => (string)($tour['meeting_point'] ?? ''),
    'highlights' => (string)($tour['highlights'] ?? ''),
    'itinerary' => (string)($tour['itinerary'] ?? ''),
    'inclusions' => (string)($tour['inclusions'] ?? ''),
    'exclusions' => (string)($tour['exclusions'] ?? ''),
    'things_to_carry' => (string)($tour['things_to_carry'] ?? ''),
    'terms_conditions' => (string)($tour['terms_conditions'] ?? ''),
    'status' => (string)($tour['status'] ?? 'draft'),
    'sort_order' => (string)($tour['sort_order'] ?? '0'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['edit_tour_csrf'], $token)) {
        http_response_code(403);
        exit('Invalid request. Please refresh the page and try again.');
    }

    foreach ($fields as $key => $default) $fields[$key] = postString($key, $default);
    $status = $fields['status'];
    $sortOrder = filter_var($fields['sort_order'], FILTER_VALIDATE_INT);

    foreach ([['title', 200, 'Tour title'], ['category', 100, 'Category'], ['destination', 200, 'Destination'], ['duration', 100, 'Duration'], ['best_season', 150, 'Best season'], ['meeting_point', 255, 'Meeting point']] as [$key, $max, $label]) {
        if (mb_strlen($fields[$key]) > $max) $errors[] = $label . " must not exceed {$max} characters.";
    }
    foreach (['title' => 'Tour title', 'category' => 'Category', 'destination' => 'Destination'] as $key => $label) if ($fields[$key] === '') $errors[] = $label . ' is required.';
    foreach (['short_description' => 500, 'description' => 20000, 'highlights' => 10000, 'itinerary' => 30000, 'inclusions' => 10000, 'exclusions' => 10000, 'things_to_carry' => 10000, 'terms_conditions' => 20000] as $key => $max) {
        if (mb_strlen($fields[$key]) > $max) $errors[] = ucfirst(str_replace('_', ' ', $key)) . " must not exceed {$max} characters.";
    }
    if (!in_array($fields['currency'], $allowedCurrencies, true)) $errors[] = 'Select a valid currency.';
    if (!in_array($status, $allowedStatuses, true)) $errors[] = 'Select a valid publication status.';
    if ($sortOrder === false || $sortOrder === null || $sortOrder < 0) $errors[] = 'Sort order must be a non-negative whole number.';

    $price = null;
    if ($fields['price_from'] !== '') {
        if (!is_numeric($fields['price_from']) || (float)$fields['price_from'] < 0 || (float)$fields['price_from'] > 99999999.99) $errors[] = 'Starting price must be a valid non-negative amount.';
        else $price = number_format((float)$fields['price_from'], 2, '.', '');
    }
    $minGroup = null;
    $maxGroup = null;
    foreach (['min_group_size' => 'Minimum group size', 'max_group_size' => 'Maximum group size'] as $key => $label) {
        if ($fields[$key] !== '' && (filter_var($fields[$key], FILTER_VALIDATE_INT) === false || (int)$fields[$key] < 1 || (int)$fields[$key] > 100000)) $errors[] = $label . ' must be a whole number between 1 and 100000.';
    }
    if ($fields['min_group_size'] !== '' && filter_var($fields['min_group_size'], FILTER_VALIDATE_INT) !== false) $minGroup = (int)$fields['min_group_size'];
    if ($fields['max_group_size'] !== '' && filter_var($fields['max_group_size'], FILTER_VALIDATE_INT) !== false) $maxGroup = (int)$fields['max_group_size'];
    if ($minGroup !== null && $maxGroup !== null && $minGroup > $maxGroup) $errors[] = 'Minimum group size cannot exceed maximum group size.';
    foreach (['start_date' => 'Start date', 'end_date' => 'End date', 'booking_deadline' => 'Booking deadline'] as $key => $label) {
        if ($fields[$key] !== '') {
            $date = DateTime::createFromFormat('!Y-m-d', $fields[$key]);
            if (!$date || $date->format('Y-m-d') !== $fields[$key]) $errors[] = $label . ' is invalid.';
        }
    }
    if ($fields['start_date'] !== '' && $fields['end_date'] !== '' && $fields['end_date'] < $fields['start_date']) $errors[] = 'End date cannot be earlier than start date.';

    $oldImagePath = (string)($tour['image_path'] ?? '');
    $imagePath = $oldImagePath;
    $newMainImagePath = null;
    $galleryPaths = [];
    $galleryDeletes = $_POST['delete_gallery'] ?? [];
    if (!is_array($galleryDeletes)) $galleryDeletes = [];
    $galleryDeleteIds = array_values(array_unique(array_filter(array_map(static function ($v) {
        return filter_var($v, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
    }, $galleryDeletes))));

    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $saveImage = static function (array $file) use (&$errors, &$uploadedFiles, $uploadDirectory, $allowedTypes, $maxImageSize): ?string {
        if (!isset($file['error'], $file['tmp_name'], $file['size']) || is_array($file['error'])) {
            $errors[] = 'Invalid image upload.';
            return null;
        }
        if ((int)$file['error'] === UPLOAD_ERR_NO_FILE) return null;
        if ((int)$file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Image upload failed. Please select it again.';
            return null;
        }
        if (!is_string($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || (int)$file['size'] <= 0 || (int)$file['size'] > $maxImageSize) {
            $errors[] = 'Each image must be valid and no larger than 5 MB.';
            return null;
        }
        $imageInfo = @getimagesize($file['tmp_name']);
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if ($imageInfo === false || !isset($allowedTypes[$mime])) {
            $errors[] = 'Only valid JPG, PNG and WebP images are allowed.';
            return null;
        }
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            $errors[] = 'Upload folder could not be created.';
            return null;
        }
        $filename = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mime];
        $fullPath = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            $errors[] = 'Image could not be saved. Check folder permissions.';
            return null;
        }
        $uploadedFiles[] = $fullPath;
        return 'uploads/tours/' . $filename;
    };

    if (!$errors && isset($_FILES['tour_image']) && is_array($_FILES['tour_image'])) {
        $newMainImagePath = $saveImage($_FILES['tour_image']);
        if ($newMainImagePath !== null) $imagePath = $newMainImagePath;
    }
    if (!$errors && isset($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['name'] ?? null)) {
        $gallery = $_FILES['gallery_images'];
        $count = count($gallery['name']);
        if ($count > 10) $errors[] = 'You can upload a maximum of 10 new gallery photos per save.';
        else for ($i = 0; $i < $count; $i++) {
            if (($gallery['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
            $one = ['name' => $gallery['name'][$i] ?? '', 'type' => $gallery['type'][$i] ?? '', 'tmp_name' => $gallery['tmp_name'][$i] ?? '', 'error' => $gallery['error'][$i] ?? UPLOAD_ERR_NO_FILE, 'size' => $gallery['size'][$i] ?? 0];
            $path = $saveImage($one);
            if ($path !== null) $galleryPaths[] = $path;
        }
    }

    $slugSource = function_exists('iconv') ? (iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $fields['title']) ?: $fields['title']) : $fields['title'];
    $baseSlug = trim((string)(preg_replace('/[^a-z0-9]+/', '-', strtolower($slugSource)) ?? ''), '-');
    if ($baseSlug === '') $errors[] = 'Please use a title that can create a valid URL slug.';

    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $slug = $baseSlug;
            $suffix = 2;
            $slugCheck = $pdo->prepare('SELECT id FROM tours WHERE slug = :slug AND id <> :id LIMIT 1');
            while (true) {
                $slugCheck->execute(['slug' => $slug, 'id' => $tourId]);
                if (!$slugCheck->fetch()) break;
                $slug = $baseSlug . '-' . $suffix++;
            }

            $update = $pdo->prepare('UPDATE tours SET title=:title, slug=:slug, category=:category, destination=:destination, duration=:duration, short_description=:short_description, description=:description, image_path=:image_path, status=:status, sort_order=:sort_order, price_from=:price_from, currency=:currency, min_group_size=:min_group_size, max_group_size=:max_group_size, start_date=:start_date, end_date=:end_date, booking_deadline=:booking_deadline, best_season=:best_season, meeting_point=:meeting_point, highlights=:highlights, itinerary=:itinerary, inclusions=:inclusions, exclusions=:exclusions, things_to_carry=:things_to_carry, terms_conditions=:terms_conditions WHERE id=:id');
            $update->execute([
                'title' => $fields['title'],
                'slug' => $slug,
                'category' => $fields['category'],
                'destination' => $fields['destination'],
                'duration' => nullableValue($fields, 'duration'),
                'short_description' => nullableValue($fields, 'short_description'),
                'description' => nullableValue($fields, 'description'),
                'image_path' => $imagePath !== '' ? $imagePath : null,
                'status' => $status,
                'sort_order' => $sortOrder,
                'price_from' => $price,
                'currency' => $fields['currency'],
                'min_group_size' => $minGroup,
                'max_group_size' => $maxGroup,
                'start_date' => nullableValue($fields, 'start_date'),
                'end_date' => nullableValue($fields, 'end_date'),
                'booking_deadline' => nullableValue($fields, 'booking_deadline'),
                'best_season' => nullableValue($fields, 'best_season'),
                'meeting_point' => nullableValue($fields, 'meeting_point'),
                'highlights' => nullableValue($fields, 'highlights'),
                'itinerary' => nullableValue($fields, 'itinerary'),
                'inclusions' => nullableValue($fields, 'inclusions'),
                'exclusions' => nullableValue($fields, 'exclusions'),
                'things_to_carry' => nullableValue($fields, 'things_to_carry'),
                'terms_conditions' => nullableValue($fields, 'terms_conditions'),
                'id' => $tourId
            ]);

            $removedGalleryPaths = [];
            if ($galleryDeleteIds) {
                $marks = implode(',', array_fill(0, count($galleryDeleteIds), '?'));
                $findDelete = $pdo->prepare("SELECT id, image_path FROM tour_gallery WHERE tour_id = ? AND id IN ($marks)");
                $findDelete->execute(array_merge([$tourId], $galleryDeleteIds));
                $removedGalleryPaths = $findDelete->fetchAll(PDO::FETCH_ASSOC);
                $delete = $pdo->prepare("DELETE FROM tour_gallery WHERE tour_id = ? AND id IN ($marks)");
                $delete->execute(array_merge([$tourId], $galleryDeleteIds));
            }

            if ($galleryPaths) {
                $maxStmt = $pdo->prepare('SELECT COALESCE(MAX(sort_order), -1) FROM tour_gallery WHERE tour_id = :tour_id');
                $maxStmt->execute(['tour_id' => $tourId]);
                $nextOrder = (int)$maxStmt->fetchColumn() + 1;
                $insertGallery = $pdo->prepare('INSERT INTO tour_gallery (tour_id, image_path, sort_order) VALUES (:tour_id, :image_path, :sort_order)');
                foreach ($galleryPaths as $index => $path) $insertGallery->execute(['tour_id' => $tourId, 'image_path' => $path, 'sort_order' => $nextOrder + $index]);
            }
            $pdo->commit();

            $safeDeleteFile = static function (string $relativePath) use ($uploadDirectory): void {
                if (!str_starts_with($relativePath, 'uploads/tours/') || str_contains($relativePath, '..')) return;
                $full = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
                $realDir = realpath($uploadDirectory);
                $realFile = realpath($full);
                if ($realDir !== false && $realFile !== false && str_starts_with($realFile, $realDir . DIRECTORY_SEPARATOR) && is_file($realFile)) @unlink($realFile);
            };
            if ($newMainImagePath !== null && $oldImagePath !== '' && $oldImagePath !== $imagePath) $safeDeleteFile($oldImagePath);
            foreach ($removedGalleryPaths as $removed) $safeDeleteFile((string)$removed['image_path']);
            header('Location: edit-tour.php?id=' . (int)$tourId . '&saved=1', true, 303);
            exit;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            foreach ($uploadedFiles as $path) if (is_file($path)) @unlink($path);
            error_log('Nexus expanded edit tour failed: ' . $exception->getMessage());
            $errors[] = 'Unable to save changes. Confirm the database migration is complete, then try again.';
        }
    }

    if ($errors) foreach ($uploadedFiles as $path) if (is_file($path)) @unlink($path);
}

// Refresh gallery records after save validation/POST so the page reflects database state.
$galleryStmt = $pdo->prepare('SELECT id, image_path, sort_order FROM tour_gallery WHERE tour_id = :tour_id ORDER BY sort_order ASC, id ASC');
$galleryStmt->execute(['tour_id' => $tourId]);
$galleryRows = $galleryStmt->fetchAll(PDO::FETCH_ASSOC);

$currentImage = (string)($tour['image_path'] ?? '');
$showCurrentImage = $currentImage !== '' && !preg_match('~^(?:https?:)?//~i', $currentImage) && !str_contains($currentImage, '..');
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Edit Tour | Nexus Admin</title>
    <link rel="stylesheet" href="../css/edit-tour.css">
    <style>
        .edit-tour-section {
            margin: 26px 0 12px;
            padding: 18px 20px;
            border: 1px solid #dce6ef;
            border-radius: 16px;
            background: #f7fafc
        }

        .edit-tour-section h3 {
            margin: 0 0 6px;
            color: #173b67
        }

        .edit-tour-section p {
            margin: 0;
            color: #526579;
            font-size: .92rem
        }

        .edit-tour-field textarea {
            width: 100%;
            box-sizing: border-box;
            min-height: 105px
        }

        .edit-tour-field input,
        .edit-tour-field select,
        .edit-tour-field textarea {
            max-width: 100%;
            box-sizing: border-box
        }

        .edit-tour-help {
            display: block;
            margin-top: 7px;
            color: #62748a;
            font-size: .85rem;
            line-height: 1.5
        }

        .edit-tour-current-image {
            display: block;
            width: min(100%, 360px);
            max-height: 220px;
            object-fit: cover;
            border-radius: 14px;
            margin: 12px 0;
            border: 1px solid #dbe4ee
        }

        .edit-tour-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 14px;
            margin: 12px 0
        }

        .edit-tour-gallery-item {
            border: 1px solid #dbe4ee;
            border-radius: 12px;
            padding: 10px;
            background: #fff
        }

        .edit-tour-gallery-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            display: block;
            margin-bottom: 8px
        }

        .edit-tour-gallery-item label {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            font-size: .85rem
        }

        .edit-tour-section-full {
            grid-column: 1/-1
        }
    </style>
</head>

<body class="edit-tour-page">
    <header class="edit-tour-hero">
        <div class="edit-tour-hero-copy"><span class="edit-tour-eyebrow">YOUR CONTROL CENTRE</span>
            <h1>Edit Tour</h1>
            <p>Update complete tour, booking, itinerary, policy and gallery information.</p><a class="edit-tour-back" href="/nexus-educational-tours/admin/tours.php">&larr; Back to Tour Management</a>
        </div>
        <div class="edit-tour-hero-art" aria-hidden="true">
            <div class="edit-tour-orbit edit-tour-orbit-one"></div>
            <div class="edit-tour-orbit edit-tour-orbit-two"></div>
            <div class="edit-tour-orbit-core">N</div>
        </div>
    </header>
    <main class="edit-tour-container">
        <?php if ($message !== ''): ?><p class="edit-tour-success" role="status"><?= e($message) ?></p><?php endif; ?>
        <?php if ($errors): ?><section class="edit-tour-error" role="alert"><strong>Please correct the following:</strong>
                <ul><?php foreach (array_unique($errors) as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
            </section><?php endif; ?>
        <section class="edit-tour-card">
            <div class="edit-tour-card-heading"><span>TOUR MANAGEMENT</span>
                <h2>Update tour details</h2>
                <p>Update the details below. Leave optional fields blank when they are not confirmed.</p>
            </div>
            <form method="post" action="edit-tour.php?id=<?= (int)$tourId ?>" class="edit-tour-form" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= e($_SESSION['edit_tour_csrf']) ?>"><input type="hidden" name="tour_id" value="<?= (int)$tourId ?>">
                <div class="edit-tour-section">
                    <h3>01. Basic Information</h3>
                    <p>Tour name, category and public description.</p>
                </div>
                <div class="edit-tour-grid">
                    <div class="edit-tour-field"><label for="title">Tour title *</label><input id="title" name="title" type="text" maxlength="200" required value="<?= e($fields['title']) ?>"></div>
                    <div class="edit-tour-field"><label for="category">Category *</label><input id="category" name="category" type="text" maxlength="100" required value="<?= e($fields['category']) ?>"></div>
                    <div class="edit-tour-field"><label for="destination">Destination *</label><input id="destination" name="destination" type="text" maxlength="200" required value="<?= e($fields['destination']) ?>"></div>
                    <div class="edit-tour-field"><label for="duration">Duration</label><input id="duration" name="duration" type="text" maxlength="100" value="<?= e($fields['duration']) ?>" placeholder="e.g. 2 Days / 1 Night"></div>
                    <div class="edit-tour-field edit-tour-section-full"><label for="short_description">Short description</label><textarea id="short_description" name="short_description" maxlength="500" rows="3"><?= e($fields['short_description']) ?></textarea></div>
                    <div class="edit-tour-field edit-tour-section-full"><label for="description">Full description</label><textarea id="description" name="description" rows="6"><?= e($fields['description']) ?></textarea></div>
                </div>
                <div class="edit-tour-section">
                    <h3>02. Booking & Group Details</h3>
                    <p>Price, dates and group size. Leave unconfirmed details blank.</p>
                </div>
                <div class="edit-tour-grid">
                    <div class="edit-tour-field"><label for="price_from">Starting price per person</label><input id="price_from" name="price_from" type="number" min="0" max="99999999.99" step="0.01" value="<?= e($fields['price_from']) ?>"></div>
                    <div class="edit-tour-field"><label for="currency">Currency</label><select id="currency" name="currency"><?php foreach (['INR' => 'INR — Indian Rupee', 'USD' => 'USD — US Dollar', 'EUR' => 'EUR — Euro', 'GBP' => 'GBP — Pound Sterling'] as $v => $label): ?><option value="<?= e($v) ?>" <?= $fields['currency'] === $v ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
                    <div class="edit-tour-field"><label for="min_group_size">Minimum group size</label><input id="min_group_size" name="min_group_size" type="number" min="1" max="100000" step="1" value="<?= e($fields['min_group_size']) ?>"></div>
                    <div class="edit-tour-field"><label for="max_group_size">Maximum group size</label><input id="max_group_size" name="max_group_size" type="number" min="1" max="100000" step="1" value="<?= e($fields['max_group_size']) ?>"></div>
                    <div class="edit-tour-field"><label for="start_date">Start date</label><input id="start_date" name="start_date" type="date" value="<?= e($fields['start_date']) ?>"></div>
                    <div class="edit-tour-field"><label for="end_date">End date</label><input id="end_date" name="end_date" type="date" value="<?= e($fields['end_date']) ?>"></div>
                    <div class="edit-tour-field"><label for="booking_deadline">Booking deadline</label><input id="booking_deadline" name="booking_deadline" type="date" value="<?= e($fields['booking_deadline']) ?>"></div>
                    <div class="edit-tour-field"><label for="meeting_point">Meeting / pickup point</label><input id="meeting_point" name="meeting_point" maxlength="255" value="<?= e($fields['meeting_point']) ?>"></div>
                </div>
                <div class="edit-tour-section">
                    <h3>03. Tour Experience & Itinerary</h3>
                    <p>Learning highlights, best season and the planned schedule.</p>
                </div>
                <div class="edit-tour-grid">
                    <div class="edit-tour-field edit-tour-section-full"><label for="best_season">Best season / preferred months</label><input id="best_season" name="best_season" maxlength="150" value="<?= e($fields['best_season']) ?>" placeholder="e.g. October to February"></div>
                    <div class="edit-tour-field edit-tour-section-full"><label for="highlights">Learning highlights</label><textarea id="highlights" name="highlights" rows="5" placeholder="One highlight per line"><?= e($fields['highlights']) ?></textarea><small class="edit-tour-help">The public Tour Details modal can display these as chips.</small></div>
                    <div class="edit-tour-field edit-tour-section-full"><label for="itinerary">Day-wise itinerary</label><textarea id="itinerary" name="itinerary" rows="8" placeholder="Day 1: Departure and orientation&#10;Day 2: Guided visits and activities"><?= e($fields['itinerary']) ?></textarea><small class="edit-tour-help">Enter one day or activity per line.</small></div>
                </div>
                <div class="edit-tour-section">
                    <h3>04. Inclusions, Preparation & Terms</h3>
                    <p>Make it clear what is included and what visitors should know.</p>
                </div>
                <div class="edit-tour-grid">
                    <div class="edit-tour-field"><label for="inclusions">What's included</label><textarea id="inclusions" name="inclusions" rows="5" placeholder="One inclusion per line"><?= e($fields['inclusions']) ?></textarea></div>
                    <div class="edit-tour-field"><label for="exclusions">What's not included</label><textarea id="exclusions" name="exclusions" rows="5" placeholder="One exclusion per line"><?= e($fields['exclusions']) ?></textarea></div>
                    <div class="edit-tour-field"><label for="things_to_carry">Things to carry / student instructions</label><textarea id="things_to_carry" name="things_to_carry" rows="5"><?= e($fields['things_to_carry']) ?></textarea></div>
                    <div class="edit-tour-field"><label for="terms_conditions">Terms & conditions</label><textarea id="terms_conditions" name="terms_conditions" rows="5"><?= e($fields['terms_conditions']) ?></textarea></div>
                </div>
                <div class="edit-tour-section">
                    <h3>05. Main Image & Gallery</h3>
                    <p>JPG, PNG or WebP images, maximum 5 MB per image.</p>
                </div>
                <div class="edit-tour-grid">
                    <div class="edit-tour-field edit-tour-section-full"><label for="tour_image">Main tour image</label><?php if ($showCurrentImage): ?><p>Current image:</p><img class="edit-tour-current-image" src="<?= e($projectBaseUrl . ltrim($currentImage, '/')) ?>" alt="Current image for <?= e($fields['title']) ?>"><?php else: ?><p>No current image is set for this tour.</p><?php endif; ?><input id="tour_image" name="tour_image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><small class="edit-tour-help">Leave empty to keep the current main image.</small></div>
                    <div class="edit-tour-field edit-tour-section-full"><label for="gallery_images">Add gallery photos (up to 10 per save)</label><input id="gallery_images" name="gallery_images[]" type="file" multiple accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><small class="edit-tour-help">New images will be added to the existing gallery.</small></div>
                    <div class="edit-tour-field edit-tour-section-full"><label>Current gallery photos</label><?php if (!$galleryRows): ?><p>No gallery photos have been added yet.</p><?php else: ?><div class="edit-tour-gallery"><?php foreach ($galleryRows as $image): $galleryPath = (string)$image['image_path'];
                                                                                                                                                                                                                                        $gallerySafe = $galleryPath !== '' && !preg_match('~^(?:https?:)?//~i', $galleryPath) && !str_contains($galleryPath, '..'); ?><div class="edit-tour-gallery-item"><?php if ($gallerySafe): ?><img src="<?= e($projectBaseUrl . ltrim($galleryPath, '/')) ?>" alt="Gallery image for <?= e($fields['title']) ?>"><?php endif; ?><label><input type="checkbox" name="delete_gallery[]" value="<?= (int)$image['id'] ?>"> Remove this photo</label></div><?php endforeach; ?></div><small class="edit-tour-help">Selected gallery photos and their files will be removed after the changes are saved.</small><?php endif; ?></div>
                </div>
                <div class="edit-tour-section">
                    <h3>06. Publication Settings</h3>
                    <p>Only publish when the information is ready.</p>
                </div>
                <div class="edit-tour-grid">
                    <div class="edit-tour-field"><label for="status">Publication status *</label><select id="status" name="status" required><?php foreach ($allowedStatuses as $option): ?><option value="<?= e($option) ?>" <?= $fields['status'] === $option ? 'selected' : '' ?>><?= e(ucfirst($option)) ?></option><?php endforeach; ?></select></div>
                    <div class="edit-tour-field"><label for="sort_order">Sort order *</label><input id="sort_order" name="sort_order" type="number" min="0" step="1" required value="<?= e($fields['sort_order']) ?>"></div>
                </div>
                <div class="edit-tour-actions"><a class="edit-tour-button edit-tour-secondary" href="/nexus-educational-tours/admin/tours.php">Cancel</a><button class="edit-tour-button edit-tour-primary" type="submit">Save Changes</button></div>
            </form>
        </section>
    </main>
</body>

</html>