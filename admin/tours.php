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

if (empty($_SESSION['tours_csrf'])) {
    $_SESSION['tours_csrf'] = bin2hex(random_bytes(32));
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($submittedToken) ||
        !hash_equals($_SESSION['tours_csrf'], $submittedToken)
    ) {
        http_response_code(403);
        exit('Invalid request. Please refresh the page and try again.');
    }

    $tourId = filter_var(
        $_POST['tour_id'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    $newStatus = $_POST['status'] ?? '';

    if (
        $tourId === false ||
        $tourId === null ||
        !is_string($newStatus) ||
        !in_array($newStatus, $allowedStatuses, true)
    ) {
        $error = 'Invalid tour or status.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'UPDATE tours
                 SET status = :status
                 WHERE id = :id'
            );

            $stmt->execute([
                'status' => $newStatus,
                'id' => $tourId,
            ]);

            if ($stmt->rowCount() > 0) {
                $message = 'Tour status updated successfully.';
            } else {
                $check = $pdo->prepare(
                    'SELECT id, status FROM tours WHERE id = :id'
                );
                $check->execute(['id' => $tourId]);
                $existingTour = $check->fetch();

                if (!$existingTour) {
                    $error = 'Tour not found.';
                } elseif ($existingTour['status'] === $newStatus) {
                    $message = 'The tour already has this status.';
                } else {
                    $error = 'Could not update the tour status.';
                }
            }
        } catch (PDOException $exception) {
            error_log('Nexus tour status update failed: ' . $exception->getMessage());
            $error = 'Unable to update the tour right now. Please try again.';
        }
    }
}

$search = $_GET['search'] ?? '';

if (!is_string($search)) {
    $search = '';
}

$search = trim($search);
$search = mb_substr($search, 0, 200);

$categoryFilter = $_GET['category'] ?? 'all';

if (!is_string($categoryFilter)) {
    $categoryFilter = 'all';
}

$statusFilter = $_GET['status'] ?? 'all';

if (
    !is_string($statusFilter) ||
    ($statusFilter !== 'all' &&
        !in_array($statusFilter, $allowedStatuses, true))
) {
    $statusFilter = 'all';
}

try {
    $where = [];
    $params = [];

    if ($statusFilter !== 'all') {
        $where[] = 'status = :status';
        $params['status'] = $statusFilter;
    }

    if ($search !== '') {
        $where[] = '(
            title LIKE :search_title
            OR destination LIKE :search_destination
            OR slug LIKE :search_slug
        )';

        $term = '%' . $search . '%';
        $params['search_title'] = $term;
        $params['search_destination'] = $term;
        $params['search_slug'] = $term;
    }

    if ($categoryFilter !== 'all') {
        $where[] = 'category = :category';
        $params['category'] = $categoryFilter;
    }

    $sql = 'SELECT id, title, slug, category, destination, duration,
                   status, sort_order, created_at
            FROM tours';

    if ($where !== []) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY sort_order ASC, id DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tours = $stmt->fetchAll();

    $categoryStmt = $pdo->query(
        'SELECT DISTINCT category
         FROM tours
         ORDER BY category ASC'
    );
    $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);

    $countStmt = $pdo->query(
        'SELECT status, COUNT(*) AS total
         FROM tours
         GROUP BY status'
    );

    $counts = [
        'draft' => 0,
        'published' => 0,
        'archived' => 0,
    ];

    foreach ($countStmt->fetchAll() as $row) {
        $counts[$row['status']] = (int) $row['total'];
    }
} catch (PDOException $exception) {
    error_log('Nexus tour list retrieval failed: ' . $exception->getMessage());

    $tours = [];
    $categories = [];
    $counts = ['draft' => 0, 'published' => 0, 'archived' => 0];

    $error = 'Unable to load tours. Please check the database and try again.';
}


?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tour Management | Nexus Admin</title>

    <link rel="stylesheet" href="../css/tours.css">

</head>

<body class="tours-admin-page">


    <header class="ta-header ta-hero">
        <div class="ta-hero-copy">
            <span class="ta-eyebrow">YOUR CONTROL CENTRE</span>

            <h1>Tour Management</h1>

            <p>
                Manage your tours, review their status and keep
                your Nexus travel experiences organised.
            </p>

            <a
                class="ta-back-button"
                href="/nexus-educational-tours/admin/dashboard.php">
                <span aria-hidden="true">←</span>
                Back to Dashboard
            </a>
        </div>

        <div class="ta-hero-art" aria-hidden="true">
            <div class="ta-orbit ta-orbit-one"></div>
            <div class="ta-orbit ta-orbit-two"></div>

            <div class="ta-orbit-core">
                <span>N</span>
            </div>

            <span class="ta-orbit-dot ta-dot-one"></span>
            <span class="ta-orbit-dot ta-dot-two"></span>
            <span class="ta-orbit-dot ta-dot-three"></span>
        </div>
    </header>


    <main class="ta-container">

        <section>
            <h2>Tour Overview</h2>

            <p>Total tours: <?= array_sum($counts) ?></p>
            <p>Draft: <?= $counts['draft'] ?></p>
            <p>Published: <?= $counts['published'] ?></p>
            <p>Archived: <?= $counts['archived'] ?></p>
        </section>

        <?php if ($message !== ''): ?>
            <p role="status"><?= e($message) ?></p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p role="alert"><?= e($error) ?></p>
        <?php endif; ?>

        <section>
            <section>
                <div class="ta-section-heading">
                    <h2>Manage Tours</h2>

                    <a
                        class="ta-add-button"
                        href="/nexus-educational-tours/admin/add-tour.php">
                        + Add New Tour
                    </a>
                </div>

                <form method="get" action="" class="ta-search-form">
                    <label for="tour-search">Search tours</label>
                    <input
                        type="search"
                        id="tour-search"
                        name="search"
                        maxlength="200"
                        value="<?= e($search) ?>"
                        placeholder="Tour name, destination or slug">

                    <label for="tour-category">Category</label>
                    <select id="tour-category" name="category">
                        <option value="all">All categories</option>

                        <?php foreach ($categories as $category): ?>
                            <option
                                value="<?= e((string) $category) ?>"
                                <?= $categoryFilter === $category ? 'selected' : '' ?>>
                                <?= e((string) $category) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input
                        type="hidden"
                        name="status"
                        value="<?= e($statusFilter) ?>">

                    <button type="submit">Search / Filter</button>
                    <a href="?status=all">Reset</a>
                </form>


                <div class="ta-status-filters">
                    <span class="ta-filter-label">Filter tours:</span>

                    <a class="<?= $statusFilter === 'all' ? 'active' : '' ?>"
                        href="?status=all">All</a>

                    <a class="<?= $statusFilter === 'draft' ? 'active' : '' ?>"
                        href="?status=draft">Draft</a>

                    <a class="<?= $statusFilter === 'published' ? 'active' : '' ?>"
                        href="?status=published">Published</a>

                    <a class="<?= $statusFilter === 'archived' ? 'active' : '' ?>"
                        href="?status=archived">Archived</a>
                </div>


                <?php if (count($tours) === 0): ?>
                    <p>No tours found for this filter.</p>
                    <p>You can add tour creation functionality in the next step.</p>
                <?php else: ?>

                    <?php foreach ($tours as $tour): ?>
                        <article class="ta-tour-card">
                            <hr>

                            <h3><?= e((string) $tour['title']) ?></h3>

                            <p>
                                <strong>Category:</strong>
                                <?= e((string) $tour['category']) ?>
                            </p>

                            <p>
                                <strong>Destination:</strong>
                                <?= e((string) $tour['destination']) ?>
                            </p>

                            <p>
                                <strong>Duration:</strong>
                                <?= e((string) ($tour['duration'] ?? 'Not specified')) ?>
                            </p>

                            <p>
                                <strong>Status:</strong>
                                <?= e(ucfirst((string) $tour['status'])) ?>
                            </p>

                            <p>
                                <strong>Sort order:</strong>
                                <?= (int) $tour['sort_order'] ?>
                            </p>


                            <form method="post" action="">
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= e($_SESSION['tours_csrf']) ?>">

                                <input
                                    type="hidden"
                                    name="tour_id"
                                    value="<?= (int) $tour['id'] ?>">

                                <label for="status-<?= (int) $tour['id'] ?>">
                                    Change status:
                                </label>

                                <select
                                    name="status"
                                    id="status-<?= (int) $tour['id'] ?>"
                                    required>
                                    <?php foreach ($allowedStatuses as $status): ?>
                                        <option
                                            value="<?= e($status) ?>"
                                            <?= $tour['status'] === $status ? 'selected' : '' ?>>
                                            <?= e(ucfirst($status)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <button type="submit">Update Status</button>
                            </form>

                            <div class="ta-tour-actions">
                                <a
                                    href="/nexus-educational-tours/admin/edit-tour.php?id=<?= (int) $tour['id'] ?>"
                                    class="ta-edit-button">
                                    <span class="ta-edit-icon">✎</span>
                                    <span>Edit Tour</span>
                                    <span class="ta-edit-arrow">→</span>
                                </a>
                            </div>
                            </div>
                        </article>


                    <?php endforeach; ?>

                <?php endif; ?>
            </section>

    </main>

</body>

</html>