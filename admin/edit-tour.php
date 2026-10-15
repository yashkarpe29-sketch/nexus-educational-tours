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

$allowedStatuses = ['draft', 'published', 'archived'];
$maxImageSize = 5 * 1024 * 1024; // 5 MB
$uploadDirectory = dirname(__DIR__) . '/uploads/tours';
$projectBaseUrl = '/nexus-educational-tours/';

if (empty($_SESSION['edit_tour_csrf'])) {
    $_SESSION['edit_tour_csrf'] = bin2hex(random_bytes(32));
}

$tourId = filter_var(
    $_GET['id'] ?? $_POST['tour_id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($tourId === false || $tourId === null) {
    http_response_code(400);
    exit('Invalid tour ID.');
}

$error = '';
$message = '';
$newImageFullPath = null;
$oldImagePath = null;

try {
    $stmt = $pdo->prepare(
        'SELECT id, title, slug, category, destination, duration,
                short_description, description, image_path,
                status, sort_order
         FROM tours
         WHERE id = :id
         LIMIT 1'
    );
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['edit_tour_csrf'], $token)
    ) {
        http_response_code(403);
        exit('Invalid request. Please refresh the page and try again.');
    }

    $title = trim((string) ($_POST['title'] ?? ''));
    $category = trim((string) ($_POST['category'] ?? ''));
    $destination = trim((string) ($_POST['destination'] ?? ''));
    $duration = trim((string) ($_POST['duration'] ?? ''));
    $shortDescription = trim((string) ($_POST['short_description'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $status = $_POST['status'] ?? '';
    $sortOrder = filter_var($_POST['sort_order'] ?? null, FILTER_VALIDATE_INT);
    $imagePath = (string) ($tour['image_path'] ?? '');
    $oldImagePath = $imagePath;

    if ($title === '' || mb_strlen($title) > 200) {
        $error = 'Title is required and must be no longer than 200 characters.';
    } elseif ($category === '' || mb_strlen($category) > 100) {
        $error = 'Category is required and must be no longer than 100 characters.';
    } elseif ($destination === '' || mb_strlen($destination) > 200) {
        $error = 'Destination is required and must be no longer than 200 characters.';
    } elseif (mb_strlen($duration) > 100) {
        $error = 'Duration must not exceed 100 characters.';
    } elseif (mb_strlen($shortDescription) > 500) {
        $error = 'Short description must not exceed 500 characters.';
    } elseif (mb_strlen($imagePath) > 500) {
        $error = 'Image path is too long.';
    } elseif (!is_string($status) || !in_array($status, $allowedStatuses, true)) {
        $error = 'Please select a valid status.';
    } elseif ($sortOrder === false || $sortOrder === null || $sortOrder < 0) {
        $error = 'Sort order must be a non-negative whole number.';
    }

    // Validate an optional new image. If no file is selected, retain the current image.
    if ($error === '' && isset($_FILES['tour_image']) && is_array($_FILES['tour_image'])) {
        $file = $_FILES['tour_image'];
        $uploadError = $file['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($uploadError !== UPLOAD_ERR_NO_FILE) {
            if ($uploadError !== UPLOAD_ERR_OK) {
                $error = 'Image upload failed. Please select the image again.';
            } elseif (
                !isset($file['tmp_name'], $file['size']) ||
                !is_string($file['tmp_name']) ||
                !is_uploaded_file($file['tmp_name']) ||
                (int) $file['size'] <= 0 ||
                (int) $file['size'] > $maxImageSize
            ) {
                $error = 'Image must be a valid file no larger than 5 MB.';
            } else {
                $imageInfo = @getimagesize($file['tmp_name']);
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($file['tmp_name']);
                $allowedTypes = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                ];

                if ($imageInfo === false || !isset($allowedTypes[$mimeType])) {
                    $error = 'Only valid JPG, PNG, and WebP images are allowed.';
                } else {
                    if (
                        !is_dir($uploadDirectory) &&
                        !mkdir($uploadDirectory, 0755, true) &&
                        !is_dir($uploadDirectory)
                    ) {
                        $error = 'Upload folder could not be created.';
                    } else {
                        $filename = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mimeType];
                        $destinationPath = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;

                        if (move_uploaded_file($file['tmp_name'], $destinationPath)) {
                            $newImageFullPath = $destinationPath;
                            $imagePath = 'uploads/tours/' . $filename;
                        } else {
                            $error = 'Image could not be saved. Check folder permissions.';
                        }
                    }
                }
            }
        }
    }

    if ($error === '') {
        $slugSource = function_exists('iconv')
            ? (iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title) ?: $title)
            : $title;
        $baseSlug = strtolower($slugSource);
        $baseSlug = preg_replace('/[^a-z0-9]+/', '-', $baseSlug) ?? '';
        $baseSlug = trim($baseSlug, '-');

        if ($baseSlug === '') {
            $error = 'Please use a title that can create a valid URL slug.';
        } else {
            try {
                $slug = $baseSlug;
                $suffix = 2;

                while (true) {
                    $slugCheck = $pdo->prepare(
                        'SELECT id FROM tours WHERE slug = :slug AND id <> :id LIMIT 1'
                    );
                    $slugCheck->execute(['slug' => $slug, 'id' => $tourId]);

                    if (!$slugCheck->fetch()) {
                        break;
                    }

                    $slug = $baseSlug . '-' . $suffix;
                    $suffix++;
                }

                $update = $pdo->prepare(
                    'UPDATE tours
                     SET title = :title,
                         slug = :slug,
                         category = :category,
                         destination = :destination,
                         duration = :duration,
                         short_description = :short_description,
                         description = :description,
                         image_path = :image_path,
                         status = :status,
                         sort_order = :sort_order
                     WHERE id = :id'
                );

                $update->execute([
                    'title' => $title,
                    'slug' => $slug,
                    'category' => $category,
                    'destination' => $destination,
                    'duration' => $duration !== '' ? $duration : null,
                    'short_description' => $shortDescription !== '' ? $shortDescription : null,
                    'description' => $description !== '' ? $description : null,
                    'image_path' => $imagePath !== '' ? $imagePath : null,
                    'status' => $status,
                    'sort_order' => $sortOrder,
                    'id' => $tourId,
                ]);

                // Delete the previous image only after the database update succeeds.
                // Only remove files inside this project's uploads/tours directory.
                if (
                    $newImageFullPath !== null &&
                    $oldImagePath !== '' &&
                    $oldImagePath !== $imagePath &&
                    str_starts_with($oldImagePath, 'uploads/tours/')
                ) {
                    $oldFullPath = dirname(__DIR__) . DIRECTORY_SEPARATOR
                        . str_replace('/', DIRECTORY_SEPARATOR, $oldImagePath);
                    $realUploadDirectory = realpath($uploadDirectory);
                    $realOldFullPath = realpath($oldFullPath);

                    if (
                        $realUploadDirectory !== false &&
                        $realOldFullPath !== false &&
                        str_starts_with($realOldFullPath, $realUploadDirectory . DIRECTORY_SEPARATOR) &&
                        is_file($realOldFullPath)
                    ) {
                        @unlink($realOldFullPath);
                    }
                }

                header('Location: edit-tour.php?id=' . (int) $tourId . '&saved=1', true, 303);
                exit;
            } catch (PDOException $exception) {
                if ($newImageFullPath !== null && is_file($newImageFullPath)) {
                    @unlink($newImageFullPath);
                }
                error_log('Nexus edit tour update failed: ' . $exception->getMessage());
                $error = 'Unable to save changes. Please check the fields and try again.';
            }
        }
    }

    // If validation fails after the file was moved, remove that unused upload.
    if ($error !== '' && $newImageFullPath !== null && is_file($newImageFullPath)) {
        @unlink($newImageFullPath);
        $newImageFullPath = null;
        $imagePath = $oldImagePath ?? '';
    }

    // Keep submitted values visible if validation fails.
    $tour = array_merge($tour, [
        'title' => $title,
        'category' => $category,
        'destination' => $destination,
        'duration' => $duration,
        'short_description' => $shortDescription,
        'description' => $description,
        'image_path' => $imagePath,
        'status' => is_string($status) ? $status : '',
        'sort_order' => (string) ($_POST['sort_order'] ?? ''),
    ]);
}

if (isset($_GET['saved']) && $_GET['saved'] === '1') {
    $message = 'Tour updated successfully.';
}

$currentImage = (string) ($tour['image_path'] ?? '');
$showCurrentImage = $currentImage !== ''
    && !preg_match('~^(?:https?:)?//~i', $currentImage)
    && !str_contains($currentImage, '..');

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
        .edit-tour-current-image {
            display: block;
            width: min(100%, 360px);
            max-height: 220px;
            object-fit: cover;
            border-radius: 14px;
            margin: 12px 0;
            border: 1px solid #dbe4ee;
        }

        .edit-tour-help {
            display: block;
            margin-top: 8px;
            color: #62748a;
            font-size: .875rem;
            line-height: 1.5;
        }

        .edit-tour-file-input {
            display: block;
            width: 100%;
            max-width: 100%;
        }
    </style>
</head>

<body class="edit-tour-page">
    <header class="edit-tour-hero">
        <div class="edit-tour-hero-copy">
            <span class="edit-tour-eyebrow">YOUR CONTROL CENTRE</span>
            <h1>Edit Tour</h1>
            <p>Update tour details and keep your Nexus experiences accurate.</p>
            <a class="edit-tour-back" href="/nexus-educational-tours/admin/tours.php">&larr; Back to Tour Management</a>
        </div>
        <div class="edit-tour-hero-art" aria-hidden="true">
            <div class="edit-tour-orbit edit-tour-orbit-one"></div>
            <div class="edit-tour-orbit edit-tour-orbit-two"></div>
            <div class="edit-tour-orbit-core">N</div>
        </div>
    </header>

    <main class="edit-tour-container">
        <?php if ($message !== ''): ?>
            <p class="edit-tour-success" role="status"><?= e($message) ?></p>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <p class="edit-tour-error" role="alert"><?= e($error) ?></p>
        <?php endif; ?>

        <section class="edit-tour-card">
            <div class="edit-tour-card-heading">
                <span>TOUR MANAGEMENT</span>
                <h2>Update tour details</h2>
                <p>Make your changes below and save when you're ready.</p>
            </div>

            <form method="post" action="edit-tour.php?id=<?= (int) $tourId ?>" class="edit-tour-form" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['edit_tour_csrf']) ?>">
                <input type="hidden" name="tour_id" value="<?= (int) $tourId ?>">

                <div class="edit-tour-grid">
                    <div class="edit-tour-field">
                        <label for="title">Tour title *</label>
                        <input id="title" name="title" type="text" maxlength="200" required value="<?= e((string) $tour['title']) ?>">
                    </div>

                    <div class="edit-tour-field">
                        <label for="category">Category *</label>
                        <input id="category" name="category" type="text" maxlength="100" required value="<?= e((string) $tour['category']) ?>">
                    </div>

                    <div class="edit-tour-field">
                        <label for="destination">Destination *</label>
                        <input id="destination" name="destination" type="text" maxlength="200" required value="<?= e((string) $tour['destination']) ?>">
                    </div>

                    <div class="edit-tour-field">
                        <label for="duration">Duration</label>
                        <input id="duration" name="duration" type="text" maxlength="100" value="<?= e((string) ($tour['duration'] ?? '')) ?>" placeholder="e.g. 2 Days / 1 Night">
                    </div>

                    <div class="edit-tour-field edit-tour-field-full">
                        <label for="short_description">Short description</label>
                        <textarea id="short_description" name="short_description" maxlength="500" rows="3"><?= e((string) ($tour['short_description'] ?? '')) ?></textarea>
                    </div>

                    <div class="edit-tour-field edit-tour-field-full">
                        <label for="description">Full description</label>
                        <textarea id="description" name="description" rows="6"><?= e((string) ($tour['description'] ?? '')) ?></textarea>
                    </div>

                    <div class="edit-tour-field edit-tour-field-full">
                        <label for="tour_image">Tour image (optional)</label>
                        <?php if ($showCurrentImage): ?>
                            <p>Current image:</p>
                            <img class="edit-tour-current-image" src="<?= e($projectBaseUrl . ltrim($currentImage, '/')) ?>" alt="Current image for <?= e((string) $tour['title']) ?>">
                        <?php else: ?>
                            <p>No current image is set for this tour.</p>
                        <?php endif; ?>
                        <input class="edit-tour-file-input" id="tour_image" name="tour_image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        <small class="edit-tour-help">Choose a new JPG, PNG, or WebP image up to 5 MB. Leave this empty to keep the current image.</small>
                    </div>

                    <div class="edit-tour-field">
                        <label for="status">Status *</label>
                        <select id="status" name="status" required>
                            <?php foreach ($allowedStatuses as $statusOption): ?>
                                <option value="<?= e($statusOption) ?>" <?= $tour['status'] === $statusOption ? 'selected' : '' ?>><?= e(ucfirst($statusOption)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="edit-tour-field">
                        <label for="sort_order">Sort order *</label>
                        <input id="sort_order" name="sort_order" type="number" min="0" step="1" required value="<?= e((string) $tour['sort_order']) ?>">
                    </div>
                </div>

                <div class="edit-tour-actions">
                    <a class="edit-tour-button edit-tour-secondary" href="/nexus-educational-tours/admin/tours.php">Cancel</a>
                    <button class="edit-tour-button edit-tour-primary" type="submit">Save Changes</button>
                </div>
            </form>
        </section>
    </main>
</body>

</html>