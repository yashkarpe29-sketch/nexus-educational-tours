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

if (empty($_SESSION['add_tour_csrf'])) $_SESSION['add_tour_csrf'] = bin2hex(random_bytes(32));
$errors = [];
$values = [
    'title' => '',
    'category' => '',
    'destination' => '',
    'short_description' => '',
    'description' => '',
    'duration' => '',
    'price_from' => '',
    'currency' => 'INR',
    'min_group_size' => '',
    'max_group_size' => '',
    'start_date' => '',
    'end_date' => '',
    'booking_deadline' => '',
    'best_season' => '',
    'meeting_point' => '',
    'highlights' => '',
    'itinerary' => '',
    'inclusions' => '',
    'exclusions' => '',
    'things_to_carry' => '',
    'terms_conditions' => '',
    'image_path' => '',
    'status' => 'draft',
    'sort_order' => '0'
];
$uploadedFiles = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['add_tour_csrf'], $token)) {
        http_response_code(403);
        exit('Invalid request. Please refresh the page and try again.');
    }
    foreach ($values as $key => $default) $values[$key] = postString($key, $default);

    foreach ([['title', 200, 'Tour title'], ['category', 100, 'Category'], ['destination', 200, 'Destination'], ['duration', 100, 'Duration'], ['best_season', 150, 'Best season'], ['meeting_point', 255, 'Meeting point']] as [$key, $max, $label]) {
        if (mb_strlen($values[$key]) > $max) $errors[] = $label . " must not exceed {$max} characters.";
    }
    if ($values['title'] === '') $errors[] = 'Tour title is required.';
    if ($values['category'] === '') $errors[] = 'Category is required.';
    if ($values['destination'] === '') $errors[] = 'Destination is required.';
    foreach (['short_description' => 500, 'description' => 20000, 'highlights' => 10000, 'itinerary' => 30000, 'inclusions' => 10000, 'exclusions' => 10000, 'things_to_carry' => 10000, 'terms_conditions' => 20000] as $key => $max) {
        if (mb_strlen($values[$key]) > $max) $errors[] = str_replace('_', ' ', ucfirst($key)) . " must not exceed {$max} characters.";
    }
    if (!in_array($values['currency'], ['INR', 'USD', 'EUR', 'GBP'], true)) $errors[] = 'Select a valid currency.';
    if (!in_array($values['status'], ['draft', 'published', 'archived'], true)) $errors[] = 'Select a valid publication status.';
    $sortOrder = filter_var($values['sort_order'], FILTER_VALIDATE_INT);
    if ($sortOrder === false) $errors[] = 'Sort order must be a whole number.';

    $price = null;
    if ($values['price_from'] !== '') {
        if (!is_numeric($values['price_from']) || (float)$values['price_from'] < 0 || (float)$values['price_from'] > 99999999.99) $errors[] = 'Starting price must be a valid non-negative amount.';
        else $price = number_format((float)$values['price_from'], 2, '.', '');
    }
    $minGroup = null;
    $maxGroup = null;
    foreach (['min_group_size' => 'Minimum group size', 'max_group_size' => 'Maximum group size'] as $key => $label) {
        if ($values[$key] !== '' && (filter_var($values[$key], FILTER_VALIDATE_INT) === false || (int)$values[$key] < 1 || (int)$values[$key] > 100000)) $errors[] = $label . ' must be a whole number between 1 and 100000.';
    }
    if ($values['min_group_size'] !== '' && filter_var($values['min_group_size'], FILTER_VALIDATE_INT) !== false) $minGroup = (int)$values['min_group_size'];
    if ($values['max_group_size'] !== '' && filter_var($values['max_group_size'], FILTER_VALIDATE_INT) !== false) $maxGroup = (int)$values['max_group_size'];
    if ($minGroup !== null && $maxGroup !== null && $minGroup > $maxGroup) $errors[] = 'Minimum group size cannot exceed maximum group size.';
    foreach (['start_date' => 'Start date', 'end_date' => 'End date', 'booking_deadline' => 'Booking deadline'] as $key => $label) {
        if ($values[$key] !== '') {
            $d = DateTime::createFromFormat('!Y-m-d', $values[$key]);
            if (!$d || $d->format('Y-m-d') !== $values[$key]) $errors[] = $label . ' is invalid.';
        }
    }
    if ($values['start_date'] !== '' && $values['end_date'] !== '' && $values['end_date'] < $values['start_date']) $errors[] = 'End date cannot be earlier than start date.';

    $slugBase = mb_strtolower($values['title'], 'UTF-8');
    $slugBase = trim((string)preg_replace('/[^\p{L}\p{N}]+/u', '-', $slugBase), '-');
    if ($slugBase === '') $errors[] = 'Unable to create a slug from this title. Use a different title.';

    $uploadDir = dirname(__DIR__) . '/uploads/tours';
    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $saveImage = static function (array $file) use (&$errors, &$uploadedFiles, $uploadDir, $allowedTypes): ?string {
        if (!isset($file['error'], $file['tmp_name'], $file['size']) || is_array($file['error'])) {
            $errors[] = 'Invalid image upload.';
            return null;
        }
        if ($file['error'] === UPLOAD_ERR_NO_FILE) return null;
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'An image upload failed. Please select it again.';
            return null;
        }
        if (!is_uploaded_file($file['tmp_name']) || $file['size'] <= 0 || $file['size'] > 5 * 1024 * 1024) {
            $errors[] = 'Each image must be valid and no larger than 5 MB.';
            return null;
        }
        $imageInfo = @getimagesize($file['tmp_name']);
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if ($imageInfo === false || !isset($allowedTypes[$mime])) {
            $errors[] = 'Only valid JPG, PNG and WebP images are allowed.';
            return null;
        }
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            $errors[] = 'Upload folder could not be created.';
            return null;
        }
        $filename = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mime];
        $fullPath = $uploadDir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            $errors[] = 'An image could not be saved. Check folder permissions.';
            return null;
        }
        $uploadedFiles[] = $fullPath;
        return 'uploads/tours/' . $filename;
    };

    if (!$errors && isset($_FILES['tour_image'])) $values['image_path'] = $saveImage($_FILES['tour_image']) ?? '';
    $galleryPaths = [];
    if (!$errors && isset($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['name'] ?? null)) {
        $gallery = $_FILES['gallery_images'];
        $count = count($gallery['name']);
        if ($count > 10) $errors[] = 'You can upload a maximum of 10 gallery photos.';
        else for ($i = 0; $i < $count; $i++) {
            if (($gallery['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
            $one = ['name' => $gallery['name'][$i] ?? '', 'type' => $gallery['type'][$i] ?? '', 'tmp_name' => $gallery['tmp_name'][$i] ?? '', 'error' => $gallery['error'][$i] ?? UPLOAD_ERR_NO_FILE, 'size' => $gallery['size'][$i] ?? 0];
            $path = $saveImage($one);
            if ($path !== null) $galleryPaths[] = $path;
        }
    }

    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $slug = $slugBase;
            $suffix = 2;
            $check = $pdo->prepare('SELECT id FROM tours WHERE slug = :slug LIMIT 1');
            while (true) {
                $check->execute(['slug' => $slug]);
                if (!$check->fetch()) break;
                $slug = $slugBase . '-' . $suffix++;
            }
            $sql = 'INSERT INTO tours (title,slug,category,destination,short_description,description,duration,image_path,status,sort_order,price_from,currency,min_group_size,max_group_size,start_date,end_date,booking_deadline,best_season,meeting_point,highlights,itinerary,inclusions,exclusions,things_to_carry,terms_conditions) VALUES (:title,:slug,:category,:destination,:short_description,:description,:duration,:image_path,:status,:sort_order,:price_from,:currency,:min_group_size,:max_group_size,:start_date,:end_date,:booking_deadline,:best_season,:meeting_point,:highlights,:itinerary,:inclusions,:exclusions,:things_to_carry,:terms_conditions)';
            $stmt = $pdo->prepare($sql);
            $nullable = static fn(string $key) => $values[$key] !== '' ? $values[$key] : null;
            $stmt->execute([
                'title' => $values['title'],
                'slug' => $slug,
                'category' => $values['category'],
                'destination' => $values['destination'],
                'short_description' => $nullable('short_description'),
                'description' => $nullable('description'),
                'duration' => $nullable('duration'),
                'image_path' => $nullable('image_path'),
                'status' => $values['status'],
                'sort_order' => $sortOrder,
                'price_from' => $price,
                'currency' => $values['currency'],
                'min_group_size' => $minGroup,
                'max_group_size' => $maxGroup,
                'start_date' => $nullable('start_date'),
                'end_date' => $nullable('end_date'),
                'booking_deadline' => $nullable('booking_deadline'),
                'best_season' => $nullable('best_season'),
                'meeting_point' => $nullable('meeting_point'),
                'highlights' => $nullable('highlights'),
                'itinerary' => $nullable('itinerary'),
                'inclusions' => $nullable('inclusions'),
                'exclusions' => $nullable('exclusions'),
                'things_to_carry' => $nullable('things_to_carry'),
                'terms_conditions' => $nullable('terms_conditions')
            ]);
            $tourId = (int)$pdo->lastInsertId();
            if ($galleryPaths) {
                $galleryStmt = $pdo->prepare('INSERT INTO tour_gallery (tour_id,image_path,sort_order) VALUES (:tour_id,:image_path,:sort_order)');
                foreach ($galleryPaths as $index => $path) $galleryStmt->execute(['tour_id' => $tourId, 'image_path' => $path, 'sort_order' => $index]);
            }
            $pdo->commit();
            unset($_SESSION['add_tour_csrf']);
            header('Location: /nexus-educational-tours/admin/tours.php?created=1');
            exit;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log('Nexus tour creation failed: ' . $exception->getMessage());
            foreach ($uploadedFiles as $path) if (is_file($path)) @unlink($path);
            $errors[] = 'The tour could not be saved. Run the database migration first, then try again.';
        }
    }
    if ($errors) {
        foreach ($uploadedFiles as $path) if (is_file($path)) @unlink($path);
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Add New Tour | Nexus Admin</title>
    <link rel="stylesheet" href="../css/add-tour.css">
    <style>
        .add-tour-section {
            margin: 26px 0 12px;
            padding: 18px 20px;
            border: 1px solid #dce6ef;
            border-radius: 16px;
            background: #f7fafc
        }

        .add-tour-section h3 {
            margin: 0 0 6px;
            color: #173b67
        }

        .add-tour-section p {
            margin: 0;
            color: #526579;
            font-size: .92rem
        }

        .add-tour-field textarea {
            width: 100%;
            box-sizing: border-box;
            min-height: 110px
        }

        .add-tour-field input,
        .add-tour-field select,
        .add-tour-field textarea {
            max-width: 100%
        }

        .field-help {
            display: block;
            margin-top: 6px;
            color: #526579;
            font-size: .82rem
        }

        .add-tour-field-full {
            grid-column: 1/-1
        }
    </style>
</head>

<body class="add-tour-page">
    <div class="add-tour-container">
        <header class="add-tour-header add-tour-hero">
            <div class="add-tour-hero-copy">
                <p class="add-tour-eyebrow">Nexus Admin / Tour Management</p>
                <h1>Add New Tour</h1>
                <p class="add-tour-hero-description">Add complete tour, booking, itinerary and travel information.</p>
                <nav class="add-tour-navigation" aria-label="Admin navigation"><a href="/nexus-educational-tours/admin/tours.php">&larr; Back to Tour Management</a><a href="/nexus-educational-tours/admin/dashboard.php">Dashboard &rarr;</a></nav>
            </div>
            <div class="add-tour-hero-art" aria-hidden="true">
                <div class="add-tour-orbit add-tour-orbit-one"></div>
                <div class="add-tour-orbit add-tour-orbit-two"></div>
                <div class="add-tour-orbit add-tour-orbit-three"></div>
                <div class="add-tour-orbit-core"><span>N</span></div><span class="add-tour-orbit-dot add-tour-dot-one"></span><span class="add-tour-orbit-dot add-tour-dot-two"></span><span class="add-tour-orbit-dot add-tour-dot-three"></span><span class="add-tour-orbit-label add-tour-label-one">CREATE</span><span class="add-tour-orbit-label add-tour-label-two">EXPLORE</span>
            </div>
        </header>
        <main class="add-tour-card">
            <div class="add-tour-card-heading">
                <h2>Complete Tour Information</h2>
                <p>Required fields are marked with *. Use Draft until all details are verified.</p>
            </div>
            <div class="add-tour-form">
                <?php if ($errors): ?><section class="add-tour-errors" role="alert" aria-labelledby="error-title">
                        <h2 id="error-title">Please correct the following:</h2>
                        <ul><?php foreach (array_unique($errors) as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
                    </section><?php endif; ?>
                <form method="post" enctype="multipart/form-data" class="add-tour-form-inner"><input type="hidden" name="csrf_token" value="<?= e($_SESSION['add_tour_csrf']) ?>">
                    <div class="add-tour-section">
                        <h3>01. Basic Information</h3>
                        <p>Public-facing name and description for this tour.</p>
                    </div>
                    <div class="add-tour-grid">
                        <div class="add-tour-field"><label for="title">Tour Title <span class="required">*</span></label><input id="title" name="title" maxlength="200" value="<?= e($values['title']) ?>" placeholder="e.g. Pune Science Museum Educational Tour" required></div>
                        <div class="add-tour-field"><label for="category">Category <span class="required">*</span></label><select id="category" name="category" required><?php $categories = ['' => 'Select category', 'Educational Tours' => 'Educational Tours', 'College Tours' => 'College Tours', 'School Tours' => 'School Tours', 'Industrial Visits' => 'Industrial Visits', 'Study Tours' => 'Study Tours', 'Historical Tours' => 'Historical Tours', 'Science Tours' => 'Science Tours', 'Adventure Tours' => 'Adventure Tours', 'Nature Tours' => 'Nature Tours', 'One Day Trips' => 'One Day Trips', 'Multi-Day Tours' => 'Multi-Day Tours', 'Customized Tours' => 'Customized Tours'];
                                                                                                                                                                            foreach ($categories as $v => $label): ?><option value="<?= e($v) ?>" <?= $values['category'] === $v ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select><small class="field-help">Choose a category used by the website filters.</small></div>
                        <div class="add-tour-field"><label for="destination">Destination <span class="required">*</span></label><input id="destination" name="destination" maxlength="200" value="<?= e($values['destination']) ?>" placeholder="City, region or destination" required></div>
                        <div class="add-tour-field"><label for="duration">Duration</label><input id="duration" name="duration" maxlength="100" value="<?= e($values['duration']) ?>" placeholder="e.g. 1 Day or 3 Days / 2 Nights"></div>
                        <div class="add-tour-field add-tour-field-full"><label for="short_description">Short Description</label><textarea id="short_description" name="short_description" maxlength="500" rows="3" placeholder="Short summary shown on tour cards"><?= e($values['short_description']) ?></textarea><small class="field-help">Maximum 500 characters.</small></div>
                        <div class="add-tour-field add-tour-field-full"><label for="description">Full Description</label><textarea id="description" name="description" rows="6" placeholder="Explain the purpose, experience and important tour details"><?= e($values['description']) ?></textarea></div>
                    </div>
                    <div class="add-tour-section">
                        <h3>02. Booking & Group Details</h3>
                        <p>Add confirmed pricing, group limits and travel dates when available.</p>
                    </div>
                    <div class="add-tour-grid">
                        <div class="add-tour-field"><label for="price_from">Starting Price (per person)</label><input type="number" id="price_from" name="price_from" min="0" max="99999999.99" step="0.01" value="<?= e($values['price_from']) ?>" placeholder="e.g. 1250"><small class="field-help">Leave blank if price is not confirmed.</small></div>
                        <div class="add-tour-field"><label for="currency">Currency</label><select id="currency" name="currency"><?php foreach (['INR' => 'INR — Indian Rupee', 'USD' => 'USD — US Dollar', 'EUR' => 'EUR — Euro', 'GBP' => 'GBP — Pound Sterling'] as $v => $label): ?><option value="<?= e($v) ?>" <?= $values['currency'] === $v ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
                        <div class="add-tour-field"><label for="min_group_size">Minimum Group Size</label><input type="number" id="min_group_size" name="min_group_size" min="1" max="100000" step="1" value="<?= e($values['min_group_size']) ?>" placeholder="e.g. 20"></div>
                        <div class="add-tour-field"><label for="max_group_size">Maximum Group Size</label><input type="number" id="max_group_size" name="max_group_size" min="1" max="100000" step="1" value="<?= e($values['max_group_size']) ?>" placeholder="e.g. 50"></div>
                        <div class="add-tour-field"><label for="start_date">Start Date</label><input type="date" id="start_date" name="start_date" value="<?= e($values['start_date']) ?>"></div>
                        <div class="add-tour-field"><label for="end_date">End Date</label><input type="date" id="end_date" name="end_date" value="<?= e($values['end_date']) ?>"></div>
                        <div class="add-tour-field"><label for="booking_deadline">Booking Deadline</label><input type="date" id="booking_deadline" name="booking_deadline" value="<?= e($values['booking_deadline']) ?>"></div>
                        <div class="add-tour-field"><label for="meeting_point">Meeting / Pickup Point</label><input type="text" id="meeting_point" name="meeting_point" maxlength="255" value="<?= e($values['meeting_point']) ?>" placeholder="e.g. School main gate"></div>
                    </div>
                    <div class="add-tour-section">
                        <h3>03. Tour Experience & Itinerary</h3>
                        <p>Help visitors understand what they will do and experience.</p>
                    </div>
                    <div class="add-tour-grid">
                        <div class="add-tour-field"><label for="best_season">Best Season / Preferred Months</label><input type="text" id="best_season" name="best_season" maxlength="150" value="<?= e($values['best_season']) ?>" placeholder="e.g. October to February"></div>
                        <div class="add-tour-field add-tour-field-full"><label for="highlights">Tour Highlights</label><textarea id="highlights" name="highlights" rows="5" placeholder="One highlight per line"><?= e($values['highlights']) ?></textarea><small class="field-help">Enter each highlight on a separate line.</small></div>
                        <div class="add-tour-field add-tour-field-full"><label for="itinerary">Day-wise Itinerary</label><textarea id="itinerary" name="itinerary" rows="8" placeholder="Day 1: Arrival and orientation&#10;Day 2: Guided visit and activities&#10;Day 3: Return journey"><?= e($values['itinerary']) ?></textarea><small class="field-help">Use plain text, with one day or activity per line.</small></div>
                    </div>
                    <div class="add-tour-section">
                        <h3>04. Inclusions, Preparation & Terms</h3>
                        <p>Make the scope and responsibilities clear before enquiries.</p>
                    </div>
                    <div class="add-tour-grid">
                        <div class="add-tour-field"><label for="inclusions">What's Included</label><textarea id="inclusions" name="inclusions" rows="5" placeholder="One inclusion per line"><?= e($values['inclusions']) ?></textarea></div>
                        <div class="add-tour-field"><label for="exclusions">What's Not Included</label><textarea id="exclusions" name="exclusions" rows="5" placeholder="One exclusion per line"><?= e($values['exclusions']) ?></textarea></div>
                        <div class="add-tour-field"><label for="things_to_carry">Things to Carry / Student Instructions</label><textarea id="things_to_carry" name="things_to_carry" rows="5" placeholder="One instruction per line"><?= e($values['things_to_carry']) ?></textarea></div>
                        <div class="add-tour-field"><label for="terms_conditions">Terms & Conditions</label><textarea id="terms_conditions" name="terms_conditions" rows="5" placeholder="Cancellation, payment and participation terms"><?= e($values['terms_conditions']) ?></textarea></div>
                    </div>
                    <div class="add-tour-section">
                        <h3>05. Photos & Publication</h3>
                        <p>Use clear images you have permission to publish. Each image can be up to 5 MB.</p>
                    </div>
                    <div class="add-tour-grid">
                        <div class="add-tour-field add-tour-field-full"><label for="tour_image">Main Tour Image</label><input type="file" id="tour_image" name="tour_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><small class="field-help">JPG, PNG or WebP; maximum 5 MB. This is the main card image.</small></div>
                        <div class="add-tour-field add-tour-field-full"><label for="gallery_images">Gallery Photos (up to 10)</label><input type="file" id="gallery_images" name="gallery_images[]" multiple accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><small class="field-help">Select up to 10 additional JPG, PNG or WebP images; maximum 5 MB each.</small></div>
                        <div class="add-tour-field"><label for="status">Publication Status</label><select id="status" name="status" required>
                                <option value="draft" <?= $values['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="published" <?= $values['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="archived" <?= $values['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                            </select><small class="field-help">Only publish after checking every detail.</small></div>
                        <div class="add-tour-field"><label for="sort_order">Sort Order</label><input type="number" id="sort_order" name="sort_order" step="1" value="<?= e($values['sort_order']) ?>" required><small class="field-help">Lower numbers are displayed first.</small></div>
                    </div>
                    <div class="add-tour-form-footer">
                        <p class="add-tour-footer-note">Review all details before saving. Leave unconfirmed details blank and keep the tour in Draft.</p>
                        <div class="add-tour-actions"><a href="/nexus-educational-tours/admin/tours.php" class="add-tour-button add-tour-button-secondary">Cancel</a><button type="submit" class="add-tour-button add-tour-button-primary"><span aria-hidden="true">&#10003;</span> Save Tour</button></div>
                    </div>
                </form>
            </div>
        </main>
        <footer class="add-tour-page-footer">Nexus Educational Tours &amp; Travels</footer>
    </div>
</body>

</html>