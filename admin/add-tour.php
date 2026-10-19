<?php

declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../config/database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!function_exists('e')) {
    function e(string $v): string
    {
        return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
    }
}
if (empty($_SESSION['nexus_tour_csrf'])) $_SESSION['nexus_tour_csrf'] = bin2hex(random_bytes(32));
function line_items(string $s): array
{
    return array_values(array_unique(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $s) ?: []), fn($x) => $x !== '')));
}
function slugify(string $s): string
{
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
    return trim($s, '-') ?: 'tour-' . bin2hex(random_bytes(4));
}
function save_tour_image(array $f, string $label): ?string
{
    if (!isset($f['error']) || is_array($f['error']) || $f['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($f['error'] !== UPLOAD_ERR_OK) throw new RuntimeException("$label upload failed.");
    if (($f['size'] ?? 0) > 8 * 1024 * 1024) throw new RuntimeException("$label must be 8 MB or smaller.");
    $tmp = (string)($f['tmp_name'] ?? '');
    if ($tmp === '' || !is_uploaded_file($tmp)) throw new RuntimeException("Invalid $label upload.");
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
    $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($types[$mime])) throw new RuntimeException("$label must be JPG, PNG or WEBP.");
    $dir = dirname(__DIR__) . '/uploads/tours';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) throw new RuntimeException('Could not create uploads/tours folder.');
    $name = 'tour-' . date('Ymd-His') . '-' . bin2hex(random_bytes(5)) . '.' . $types[$mime];
    if (!move_uploaded_file($tmp, $dir . '/' . $name)) throw new RuntimeException("Could not save $label.");
    return 'uploads/tours/' . $name;
}
function table_exists(PDO $pdo, string $table): bool
{
    $s = $pdo->prepare('SHOW TABLES LIKE ?');
    $s->execute([$table]);
    return (bool)$s->fetchColumn();
}
$fields = ['title' => '', 'category' => '', 'destination' => '', 'duration' => '', 'short_description' => '', 'description' => '', 'highlights' => '', 'activities' => '', 'places_covered' => '', 'best_season' => '', 'suitable_for' => '', 'status' => 'draft', 'sort_order' => '0'];
$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fields as $k => $v) if (isset($_POST[$k]) && is_scalar($_POST[$k])) $fields[$k] = trim((string)$_POST[$k]);
    if (!hash_equals((string)$_SESSION['nexus_tour_csrf'], (string)($_POST['csrf_token'] ?? ''))) $errors[] = 'Security token expired. Refresh and try again.';
    if ($fields['title'] === '') $errors[] = 'Tour title is required.';
    if ($fields['destination'] === '') $errors[] = 'Destination is required.';
    if (!in_array($fields['status'], ['draft', 'published', 'archived'], true)) $errors[] = 'Choose a valid status.';
    if (!preg_match('/^\d+$/', $fields['sort_order'])) $errors[] = 'Display order must be a whole number.';
    if (!$errors) {
        try {
            $main = save_tour_image($_FILES['image'] ?? [], 'Main image');
            $gallery = [];
            $gf = $_FILES['gallery'] ?? null;
            if (is_array($gf) && isset($gf['name']) && is_array($gf['name'])) {
                if (count($gf['name']) > 12) throw new RuntimeException('Upload no more than 12 gallery images at once.');
                foreach ($gf['name'] as $i => $unused) {
                    $err = $gf['error'][$i] ?? UPLOAD_ERR_NO_FILE;
                    if ($err === UPLOAD_ERR_NO_FILE) continue;
                    $gallery[] = save_tour_image(['name' => $gf['name'][$i] ?? '', 'type' => $gf['type'][$i] ?? '', 'tmp_name' => $gf['tmp_name'][$i] ?? '', 'error' => $err, 'size' => $gf['size'][$i] ?? 0], 'Gallery image ' . ($i + 1));
                }
            }
            $base = slugify($fields['title']);
            $slug = $base;
            $n = 2;
            $check = $pdo->prepare('SELECT id FROM tours WHERE slug=? LIMIT 1');
            do {
                $check->execute([$slug]);
                $found = $check->fetchColumn();
                if ($found) $slug = $base . '-' . $n++;
            } while ($found);
            $cols = ['title', 'slug', 'category', 'destination', 'short_description', 'description', 'duration', 'image_path', 'highlights', 'activities', 'places_covered', 'best_season', 'suitable_for', 'status', 'sort_order'];
            $vals = [$fields['title'], $slug, $fields['category'], $fields['destination'], $fields['short_description'], $fields['description'], $fields['duration'], $main, json_encode(line_items($fields['highlights']), JSON_UNESCAPED_UNICODE), json_encode(line_items($fields['activities']), JSON_UNESCAPED_UNICODE), json_encode(line_items($fields['places_covered']), JSON_UNESCAPED_UNICODE), $fields['best_season'], $fields['suitable_for'], $fields['status'], (int)$fields['sort_order']];
            $pdo->beginTransaction();
            $q = $pdo->prepare('INSERT INTO tours(' . implode(',', $cols) . ') VALUES(' . implode(',', array_fill(0, count($cols), '?')) . ')');
            $q->execute($vals);
            $id = (int)$pdo->lastInsertId();
            if ($gallery && table_exists($pdo, 'tour_gallery')) {
                $g = $pdo->prepare('INSERT INTO tour_gallery(tour_id,image_path,sort_order) VALUES(?,?,?)');
                foreach ($gallery as $i => $p) if ($p) $g->execute([$id, $p, $i + 1]);
            }
            $pdo->commit();
            $success = $fields['status'] === 'published' ? 'Tour published successfully. It should now appear on the public Tours page.' : 'Tour saved successfully as ' . $fields['status'] . '.';
            foreach ($fields as $k => $v) $fields[$k] = in_array($k, ['status'], true) ? 'draft' : (in_array($k, ['sort_order'], true) ? '0' : '');
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log('Nexus add tour error: ' . $ex->getMessage());
            $errors[] = $ex instanceof RuntimeException ? $ex->getMessage() : 'Could not save tour. Confirm the database columns exist and check the PHP error log.';
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Add Tour | Nexus Admin</title>
    <style>
        :root {
            --navy: #173b67;
            --blue: #2b83c6;
            --orange: #f4a340;
            --bg: #f4f7fb;
            --text: #102a43;
            --muted: #5b7083;
            --line: #dce6ef
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, Manrope, system-ui, sans-serif;
            line-height: 1.55
        }

        .shell {
            width: min(1080px, calc(100% - 32px));
            margin: 30px auto 55px
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            margin-bottom: 22px
        }

        .kicker {
            color: var(--blue);
            font-size: .76rem;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase
        }

        .top h1 {
            margin: 4px 0;
            font-size: clamp(1.7rem, 4vw, 2.4rem);
            color: var(--navy)
        }

        .top p {
            margin: 5px 0;
            color: var(--muted)
        }

        .link {
            display: inline-flex;
            padding: 11px 15px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            color: var(--navy);
            font-weight: 800;
            text-decoration: none;
            white-space: nowrap
        }

        .card {
            background: white;
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 16px 42px #102a4312;
            overflow: hidden
        }

        .card-head {
            padding: 22px 25px;
            border-bottom: 1px solid var(--line);
            background: linear-gradient(120deg, #fff, #f6faff)
        }

        .card-head h2 {
            margin: 0;
            color: var(--navy);
            font-size: 1.15rem
        }

        .card-head p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: .9rem
        }

        .form {
            padding: 25px
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px
        }

        .field {
            min-width: 0
        }

        .full,
        .divider {
            grid-column: 1/-1
        }

        .divider {
            margin: 8px 0 -5px;
            padding-bottom: 9px;
            border-bottom: 1px solid #e6edf4;
            color: var(--navy);
            font-weight: 900
        }

        .field label {
            display: block;
            margin-bottom: 7px;
            font-size: .88rem;
            font-weight: 800;
            color: #243f58
        }

        .field input,
        .field select,
        .field textarea {
            display: block;
            width: 100%;
            min-height: 45px;
            padding: 10px 12px;
            border: 1px solid #ccd9e5;
            border-radius: 10px;
            background: white;
            color: var(--text);
            font: inherit;
            outline: none
        }

        .field textarea {
            min-height: 120px;
            resize: vertical
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px #2b83c622
        }

        .field small {
            display: block;
            margin-top: 5px;
            color: var(--muted);
            font-size: .79rem
        }

        .field input[type=file] {
            padding: 8px;
            background: #f9fbfd
        }

        .required {
            color: #c43b31
        }

        .note {
            padding: 13px 15px;
            border: 1px solid #d7e8fb;
            border-radius: 10px;
            background: #eef6ff;
            color: #294f73;
            font-size: .87rem
        }

        .alert {
            padding: 13px 16px;
            border-radius: 10px;
            margin-bottom: 16px
        }

        .success {
            background: #eaf8f0;
            border: 1px solid #bfe6ce;
            color: #17613c
        }

        .error {
            background: #fff0ee;
            border: 1px solid #f3c5bf;
            color: #8e2f25
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--line)
        }

        .btn {
            border: 0;
            border-radius: 11px;
            padding: 13px 18px;
            font: inherit;
            font-weight: 900;
            cursor: pointer
        }

        .primary {
            background: var(--navy);
            color: white
        }

        .secondary {
            display: inline-flex;
            align-items: center;
            background: #edf2f7;
            color: var(--navy);
            text-decoration: none
        }

        .foot {
            text-align: center;
            color: #6d8193;
            font-size: .82rem;
            margin-top: 18px
        }

        @media(max-width:700px) {
            .shell {
                width: calc(100% - 20px);
                margin: 16px auto 32px
            }

            .top {
                align-items: flex-start;
                flex-direction: column
            }

            .form {
                padding: 17px
            }

            .card-head {
                padding: 18px
            }

            .grid {
                grid-template-columns: 1fr;
                gap: 15px
            }

            .full,
            .divider {
                grid-column: auto
            }

            .actions>* {
                width: 100%;
                justify-content: center
            }
        }

        /* Activity picker with search */
        .activity-picker {
            margin: 12px 0;
            padding: 15px;
            background: #f8fbff;
            border: 1px solid #dce6ef;
            border-radius: 14px;
        }

        .activity-picker-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .activity-picker-toolbar label {
            color: #173b67;
            font-weight: 800;
        }

        .activity-count {
            color: #52677b;
            font-size: 13px;
        }

        .activity-search {
            width: 100%;
            min-height: 44px;
            padding: 11px 13px;
            margin-bottom: 12px;
            border: 1px solid #cbd9e6;
            border-radius: 10px;
            background: #fff;
            color: #102a43;
            font: inherit;
            outline: none;
        }

        .activity-search:focus {
            border-color: #2b83c6;
            box-shadow: 0 0 0 3px #2b83c622;
        }

        .activity-picker-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 9px;
        }

        .activity-pick {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
            padding: 10px;
            border: 1px solid #f0d2aa;
            border-radius: 10px;
            background: #fff8ef;
            color: #77420b;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            text-align: left;
            overflow-wrap: anywhere;
            cursor: pointer;
            transition: background .2s, border-color .2s;
        }

        .activity-pick:hover {
            background: #ffedda;
            border-color: #f4a340;
        }

        .activity-pick.added {
            background: #eaf8f0;
            border-color: #9ed8b4;
            color: #17613c;
        }

        .activity-pick.added::after {
            content: "✓";
            margin-left: auto;
            font-weight: 900;
        }

        .activity-empty {
            padding: 20px 10px;
            color: #52677b;
            text-align: center;
        }

        .activity-selected-note {
            margin-top: 10px;
            color: #52677b;
            font-size: 13px;
        }

        @media (max-width: 900px) {
            .activity-picker-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            .activity-picker {
                padding: 10px;
            }

            .activity-picker-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 7px;
            }

            .activity-pick {
                padding: 9px 7px;
                font-size: 12px;
            }
        }

        /* Custom Activity */
        .custom-activity-box {
            margin-top: 16px;
            padding: 14px;
            background: #f7fafc;
            border: 1px dashed #2b83c6;
            border-radius: 12px;
        }

        .custom-activity-box>label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #173b67;
        }

        .custom-activity-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 145px auto;
            gap: 10px;
        }

        .custom-activity-row input,
        .custom-activity-row select {
            width: 100%;
            min-width: 0;
            padding: 11px;
            border: 1px solid #d6e0ea;
            border-radius: 8px;
            background: #fff;
            font: inherit;
        }

        .custom-activity-row button {
            padding: 10px 18px;
            border: 0;
            border-radius: 8px;
            background: #173b67;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
        }

        .custom-activity-row button:hover {
            background: #2b83c6;
        }

        @media (max-width: 600px) {
            .custom-activity-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="shell">
        <header class="top">
            <div><span class="kicker">Nexus Educational Tours & Travels</span>
                <h1>Add a new tour</h1>
                <p>Add tour details, learning highlights, activities and images.</p>
            </div><a class="link" href="<?= e(rtrim(BASE_URL, '/') . '/pages/tours.php') ?>">View public tours ↗</a>
        </header>
        <?php if ($success !== ''): ?><div class="alert success" role="status"><?= e($success) ?></div><?php endif; ?>
        <?php if ($errors): ?><div class="alert error" role="alert"><strong>Please fix the following:</strong>
                <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
            </div><?php endif; ?>
        <section class="card">
            <div class="card-head">
                <h2>Tour information</h2>
                <p>Fields marked <span class="required">*</span> are required. Enter one highlight/activity/place per line.</p>
            </div>
            <form class="form" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= e((string)$_SESSION['nexus_tour_csrf']) ?>">
                <div class="grid">
                    <div class="divider">01 — Basic details</div>
                    <div class="field"><label for="title">Tour title <span class="required">*</span></label><input id="title" name="title" required maxlength="180" value="<?= e($fields['title']) ?>" placeholder="e.g. Agricultural Learning Tour"></div>
                    <div class="field"><label for="category">Category</label><input id="category" name="category" maxlength="100" value="<?= e($fields['category']) ?>" placeholder="Educational Tours"></div>
                    <div class="field"><label for="destination">Destination <span class="required">*</span></label><input id="destination" name="destination" required maxlength="180" value="<?= e($fields['destination']) ?>" placeholder="City, region or venue"></div>
                    <div class="field"><label for="duration">Duration</label><input id="duration" name="duration" maxlength="100" value="<?= e($fields['duration']) ?>" placeholder="1 Day / 2 Days, 1 Night"></div>
                    <div class="field full"><label for="short_description">Short description</label><textarea id="short_description" name="short_description" maxlength="1000" placeholder="Short introduction for the tour card"><?= e($fields['short_description']) ?></textarea></div>
                    <div class="field full"><label for="description">Full description</label><textarea id="description" name="description" placeholder="Full tour information, itinerary or important notes"><?= e($fields['description']) ?></textarea></div>
                    <div class="divider">02 — Learning highlights & activities</div>
                    <div class="field full"><label for="highlights">Learning highlights</label><textarea id="highlights" name="highlights" placeholder="Hands-on learning&#10;Practical exposure&#10;Team collaboration"><?= e($fields['highlights']) ?></textarea><small>One item per line. Public page displays these as navy-blue tags.</small></div>

                    <div class="field full">
                        <label for="activities">Activities — search and select</label>

                        <div class="activity-picker">
                            <div class="activity-picker-toolbar">
                                <label for="activitySearch">🔎 Find an activity</label>
                                <span class="activity-count" id="activityCount">
                                    Loading activities...
                                </span>
                            </div>

                            <input
                                type="search"
                                id="activitySearch"
                                class="activity-search"
                                placeholder="Search swimming, cricket, trek..."
                                autocomplete="off">

                            <div
                                class="activity-picker-grid"
                                id="activityPicker"
                                aria-label="Available activities"></div>

                            <div class="activity-empty" id="activityEmpty" hidden>
                                No matching activities found.
                            </div>

                            <div class="activity-selected-note">
                                Green activity = already selected. Click another activity to add it.
                            </div>
                            <div class="custom-activity-box">
                                <label for="customActivityName">➕ Add your own activity</label>

                                <div class="custom-activity-row">
                                    <input
                                        type="text"
                                        id="customActivityName"
                                        placeholder="Example: Pottery workshop"
                                        maxlength="100">

                                    <select id="customActivityEmoji">
                                        <option value="⭐">⭐ General</option>
                                        <option value="🏕️">🏕️ Outdoor</option>
                                        <option value="📚">📚 Educational</option>
                                        <option value="🔬">🔬 Science</option>
                                        <option value="🌿">🌿 Nature</option>
                                        <option value="🏃">🏃 Sports</option>
                                        <option value="🎨">🎨 Creative</option>
                                        <option value="🎵">🎵 Music</option>
                                    </select>

                                    <button type="button" id="addCustomActivity">
                                        + Add
                                    </button>
                                </div>
                            </div>
                        </div>

                        <textarea
                            id="activities"
                            name="activities"
                            placeholder="Selected activities appear here, one per line..."><?= e($fields['activities']) ?></textarea>

                        <small>
                            Activity select kelyavar emoji ani nav automatic add hoil.
                            Swatahchi activity suddha type karu shakta.
                        </small>
                    </div>

                    <div class="field full"><label for="places_covered">Places covered</label><textarea id="places_covered" name="places_covered" placeholder="Farm visit&#10;Learning centre&#10;Nature trail"><?= e($fields['places_covered']) ?></textarea></div>
                    <div class="divider">03 — Additional details & publishing</div>
                    <div class="field"><label for="best_season">Best season / time</label><input id="best_season" name="best_season" value="<?= e($fields['best_season']) ?>" placeholder="October to February"></div>
                    <div class="field"><label for="suitable_for">Suitable for</label><input id="suitable_for" name="suitable_for" value="<?= e($fields['suitable_for']) ?>" placeholder="Schools, colleges, families"></div>
                    <div class="field"><label for="image">Main tour image</label><input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><small>Optional, JPG/PNG/WEBP, maximum 8 MB.</small></div>
                    <div class="field"><label for="gallery">Gallery images</label><input id="gallery" name="gallery[]" type="file" multiple accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><small>Up to 12 images, maximum 8 MB each. Requires tour_gallery table.</small></div>
                    <div class="field"><label for="status">Status</label><select id="status" name="status">
                            <option value="draft" <?= $fields['status'] === 'draft' ? 'selected' : '' ?>>Draft — keep private</option>
                            <option value="published" <?= $fields['status'] === 'published' ? 'selected' : '' ?>>Published — show on website</option>
                            <option value="archived" <?= $fields['status'] === 'archived' ? 'selected' : '' ?>>Archived — hide from website</option>
                        </select></div>
                    <div class="field"><label for="sort_order">Display order</label><input id="sort_order" name="sort_order" type="number" min="0" step="1" value="<?= e($fields['sort_order']) ?>"><small>Smaller numbers appear first.</small></div>
                    <div class="full note">Only tours with <strong>Published</strong> status appear on the public Tours page. Keep descriptions and activity information accurate.</div>
                </div>
                <div class="actions"><button class="btn primary" type="submit">Save tour</button><a class="btn secondary" href="<?= e(rtrim(BASE_URL, '/') . '/pages/tours.php') ?>">Cancel / view tours</a></div>
            </form>
        </section>
        <p class="foot">Nexus Admin · Keep tour information accurate and up to date.</p>
    </div>

    <script>
        (() => {
            const picker = document.getElementById('activityPicker');
            const search = document.getElementById('activitySearch');
            const textarea = document.getElementById('activities');
            const count = document.getElementById('activityCount');
            const empty = document.getElementById('activityEmpty');

            if (!picker || !search || !textarea || !count || !empty) return;

            const groups = {
                "Water & Pool": [
                    ["🏊", "Swimming Pool"],
                    ["🌧️", "Rain Dance"],
                    ["🏞️", "Artificial Waterfall"],
                    ["💦", "Water Play"],
                    ["🤽", "Water Games"],
                    ["🛟", "Pool Float"],
                    ["🏖️", "Beach Games"],
                    ["🌊", "Water Sports"],
                    ["🚣", "Boating"],
                    ["🛶", "Kayaking"],
                    ["⛵", "Sailing"],
                    ["🤿", "Snorkelling"],
                    ["🐠", "Aquarium Visit"],
                    ["🎣", "Fishing"],
                    ["🪣", "Water Bucket Game"]
                ],
                "Adventure & Outdoor": [
                    ["🧗", "Adventure Rope Games"],
                    ["🪂", "Zip Line"],
                    ["🥾", "Small Hill Trek"],
                    ["⛰️", "Mountain Trek"],
                    ["🏕️", "Camping"],
                    ["🔥", "Campfire"],
                    ["🧭", "Nature Trail"],
                    ["🪢", "Rope Course"],
                    ["🌉", "Burma Bridge"],
                    ["🧗", "Rock Climbing"],
                    ["🚵", "Mountain Biking"],
                    ["🚴", "Cycling"],
                    ["🏃", "Obstacle Course"],
                    ["🤸", "Adventure Challenge"],
                    ["🪵", "Balance Walk"],
                    ["🛝", "Commando Net"],
                    ["🎯", "Target Challenge"],
                    ["🏹", "Archery"],
                    ["🪁", "Kite Flying"],
                    ["🧗", "Wall Climbing"],
                    ["🪜", "Rope Ladder"],
                    ["🌄", "Sunrise Walk"],
                    ["🌅", "Sunset Point Visit"],
                    ["🚶", "Guided Walk"]
                ],
                "Games & Sports": [
                    ["🏏", "Cricket"],
                    ["🏸", "Badminton"],
                    ["⚽", "Football"],
                    ["🏀", "Basketball"],
                    ["🏐", "Volleyball"],
                    ["🏓", "Table Tennis"],
                    ["🎾", "Tennis"],
                    ["🏑", "Hockey"],
                    ["🤾", "Handball"],
                    ["🏃", "Relay Race"],
                    ["🏃", "Running Race"],
                    ["🥇", "Sports Competition"],
                    ["🪢", "Tug of War"],
                    ["🥏", "Frisbee"],
                    ["🏹", "Archery Game"],
                    ["🎯", "Dart Game"],
                    ["🎳", "Bowling"],
                    ["🎱", "Pool Game"],
                    ["🏸", "Shuttle Challenge"],
                    ["🤼", "Wrestling Game"],
                    ["🥋", "Martial Arts Demo"],
                    ["⛳", "Mini Golf"],
                    ["🏋️", "Fitness Challenge"],
                    ["🧘", "Yoga Session"],
                    ["🤸", "Gymnastics"],
                    ["🛼", "Roller Skating"]
                ],
                "Kids & Fun": [
                    ["🌀", "Trampoline"],
                    ["🎈", "Jumping Bouncer"],
                    ["🛝", "Swings and Slides"],
                    ["🛝", "Kids Play Area"],
                    ["🎠", "Merry-Go-Round"],
                    ["🎡", "Ferris Wheel"],
                    ["🧸", "Toy Corner"],
                    ["🎨", "Face Painting"],
                    ["🎭", "Puppet Show"],
                    ["🎪", "Fun Fair"],
                    ["🫧", "Bubble Play"],
                    ["🧩", "Puzzles"],
                    ["🪅", "Piñata Game"],
                    ["🪀", "Yo-Yo Game"],
                    ["🧱", "Building Blocks"],
                    ["🎯", "Ring Toss"],
                    ["🥚", "Egg and Spoon Race"],
                    ["🥄", "Spoon Race"],
                    ["🪑", "Musical Chairs"],
                    ["🎈", "Balloon Games"],
                    ["🧵", "Craft Corner"],
                    ["🖍️", "Colouring Activity"],
                    ["🪄", "Magic Show"],
                    ["🎁", "Treasure Hunt"]
                ],
                "Indoor Games": [
                    ["🎲", "Indoor Board Games"],
                    ["🥏", "Carrom"],
                    ["♟️", "Chess"],
                    ["🧩", "Jigsaw Puzzle"],
                    ["🃏", "Playing Cards"],
                    ["🎯", "Dart Game"],
                    ["🎮", "Video Games"],
                    ["🕹️", "Arcade Games"],
                    ["🧠", "Quiz Competition"],
                    ["🔤", "Word Games"],
                    ["🔢", "Number Games"],
                    ["🧩", "Brain Teasers"],
                    ["🎲", "Ludo"],
                    ["⭕", "Tic-Tac-Toe"],
                    ["🀄", "Strategy Games"]
                ],
                "Music & Performance": [
                    ["🎤", "Karaoke Music & Dance"],
                    ["💃", "Dance Session"],
                    ["🕺", "Group Dance"],
                    ["🎶", "Music Games"],
                    ["🥁", "Drumming"],
                    ["🎸", "Guitar Performance"],
                    ["🎹", "Keyboard Performance"],
                    ["🎻", "Music Demo"],
                    ["🎭", "Drama Activity"],
                    ["🎬", "Short Film Screening"],
                    ["🎙️", "Talent Show"],
                    ["🎵", "Antakshari"],
                    ["👏", "Clap Rhythm Game"],
                    ["🎼", "Music Workshop"],
                    ["🎤", "Open Mic"],
                    ["🪘", "Traditional Music"]
                ],
                "Farm & Nature": [
                    ["🚜", "Tractor Ride"],
                    ["🌿", "Visit to Farm"],
                    ["🌱", "Plantation Activity"],
                    ["🌳", "Tree Planting"],
                    ["🌾", "Crop Demonstration"],
                    ["🐄", "Dairy Farm Visit"],
                    ["🐓", "Poultry Farm Visit"],
                    ["🐐", "Farm Animal Visit"],
                    ["🥬", "Vegetable Garden Visit"],
                    ["🌻", "Flower Garden Visit"],
                    ["🪴", "Potting Plants"],
                    ["🍅", "Vegetable Harvesting"],
                    ["🥭", "Fruit Picking"],
                    ["🪱", "Vermicomposting Demo"],
                    ["♻️", "Composting Workshop"],
                    ["🌎", "Nature Conservation"],
                    ["🦋", "Butterfly Garden"],
                    ["🐦", "Bird Watching"],
                    ["🌲", "Forest Walk"],
                    ["🍃", "Leaf Collection"],
                    ["🔍", "Nature Observation"],
                    ["💧", "Water Conservation Demo"],
                    ["🌤️", "Weather Observation"],
                    ["🪺", "Biodiversity Walk"]
                ],
                "Educational & Science": [
                    ["🔬", "Science Experiments"],
                    ["🧪", "Chemistry Demo"],
                    ["🔭", "Telescope Observation"],
                    ["🪐", "Planetarium Visit"],
                    ["🚀", "Space Science Demo"],
                    ["🤖", "Robotics Workshop"],
                    ["💻", "Coding Workshop"],
                    ["🧑‍💻", "Computer Lab Visit"],
                    ["⚙️", "Engineering Demo"],
                    ["🏭", "Industrial Visit"],
                    ["🧰", "Hands-on Workshop"],
                    ["🧲", "Magnet Experiments"],
                    ["⚡", "Electricity Demo"],
                    ["🌋", "Volcano Model"],
                    ["🧬", "Biology Activity"],
                    ["🦴", "Fossil Learning"],
                    ["🌍", "Geography Activity"],
                    ["🗺️", "Map Reading"],
                    ["📚", "Reading Session"],
                    ["✍️", "Creative Writing"],
                    ["🧮", "Math Games"],
                    ["🧠", "Memory Games"],
                    ["🧑‍🔬", "Scientist Interaction"],
                    ["🏫", "Campus Tour"],
                    ["🎓", "Career Guidance Session"],
                    ["🧑‍🏫", "Guest Lecture"]
                ],
                "History & Culture": [
                    ["🏛️", "Museum Visit"],
                    ["🏰", "Fort Visit"],
                    ["🛕", "Heritage Site Visit"],
                    ["🗿", "Monument Visit"],
                    ["🏺", "Archaeology Learning"],
                    ["🖼️", "Art Gallery Visit"],
                    ["🎨", "Traditional Art Workshop"],
                    ["🧶", "Handicraft Workshop"],
                    ["🪔", "Cultural Demonstration"],
                    ["👘", "Traditional Dress Activity"],
                    ["📜", "History Storytelling"],
                    ["🗺️", "Heritage Walk"],
                    ["🎭", "Folk Theatre"],
                    ["🥁", "Folk Music"],
                    ["🏘️", "Village Tour"],
                    ["🧵", "Textile Craft Demo"]
                ],
                "Food & Team Building": [
                    ["👨‍🍳", "Cooking Workshop"],
                    ["🍕", "Pizza Making"],
                    ["🍪", "Cookie Decorating"],
                    ["🥗", "Healthy Food Workshop"],
                    ["🍱", "Lunch Experience"],
                    ["🧁", "Cupcake Decorating"],
                    ["🍋", "Lemonade Making"],
                    ["🥤", "Mocktail Making"],
                    ["🤝", "Team Building Games"],
                    ["🧩", "Group Problem Solving"],
                    ["🗣️", "Communication Challenge"],
                    ["🧑‍🤝‍🧑", "Trust Activities"],
                    ["🎯", "Goal Setting Workshop"],
                    ["💡", "Creative Thinking"],
                    ["📝", "Reflection Session"],
                    ["🏆", "Prize Distribution"]
                ],
                "Special Activities": [
                    ["📸", "Photography Walk"],
                    ["🎥", "Video Making"],
                    ["🌌", "Stargazing"],
                    ["🧘", "Meditation"],
                    ["📖", "Storytelling"],
                    ["🎨", "Painting Session"],
                    ["🖌️", "Canvas Painting"],
                    ["🧵", "Craft Making"],
                    ["🧼", "Soap Making Demo"],
                    ["🕯️", "Candle Making"],
                    ["📦", "Recycling Workshop"],
                    ["🩹", "First Aid Demo"],
                    ["🚒", "Fire Safety Demo"],
                    ["🛡️", "Safety Awareness"],
                    ["🧑‍🚒", "Emergency Services Visit"],
                    ["🚉", "Railway Station Visit"],
                    ["✈️", "Airport Visit"],
                    ["🚦", "Road Safety Activity"],
                    ["🧑‍⚕️", "Healthcare Centre Visit"],
                    ["🏥", "Hospital Visit"],
                    ["🛍️", "Local Market Visit"],
                    ["🧳", "Travel Skills Workshop"]
                ]
            };

            const normalize = value => value.trim().toLocaleLowerCase();

            const allActivities = [];
            Object.entries(groups).forEach(([group, items]) => {
                items.forEach(([emoji, name]) => {
                    allActivities.push({
                        group,
                        emoji,
                        name,
                        value: `${emoji} ${name}`
                    });
                });
            });

            function selectedActivities() {
                return new Set(
                    textarea.value
                    .split(/\r?\n/)
                    .map(normalize)
                    .filter(Boolean)
                );
            }

            function render() {
                const query = normalize(search.value);
                const selected = selectedActivities();

                const filtered = allActivities.filter(item =>
                    normalize(item.name).includes(query) ||
                    normalize(item.group).includes(query) ||
                    normalize(item.emoji).includes(query)
                );

                picker.replaceChildren();

                filtered.forEach(item => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'activity-pick';
                    button.dataset.activity = item.value;
                    button.textContent = item.value;
                    button.title = item.group;

                    const isAdded = selected.has(normalize(item.value));
                    button.classList.toggle('added', isAdded);
                    button.setAttribute('aria-pressed', String(isAdded));

                    button.addEventListener('click', () => {
                        const lines = textarea.value
                            .split(/\r?\n/)
                            .map(line => line.trim())
                            .filter(Boolean);

                        const exists = lines.some(
                            line => normalize(line) === normalize(item.value)
                        );

                        if (!exists) {
                            lines.push(item.value);
                            textarea.value = lines.join('\n');
                            textarea.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }

                        render();
                    });

                    picker.appendChild(button);
                });

                count.textContent =
                    `${filtered.length} activities · ${selected.size} selected`;

                empty.hidden = filtered.length > 0;
            }

            search.addEventListener('input', render);
            textarea.addEventListener('input', render);

            render();
        })();

        const customNameInput = document.getElementById("customActivityName");
        const customEmojiSelect = document.getElementById("customActivityEmoji");
        const customAddButton = document.getElementById("addCustomActivity");
        const activitiesTextarea = document.getElementById("activities");

        if (
            customNameInput &&
            customEmojiSelect &&
            customAddButton &&
            activitiesTextarea
        ) {
            function addCustomActivity() {
                const name = customNameInput.value.trim();
                const emoji = customEmojiSelect.value;

                if (!name) {
                    alert("Please enter an activity name.");
                    customNameInput.focus();
                    return;
                }

                const newActivity = `${emoji} ${name}`;

                const existing = activitiesTextarea.value
                    .split(/\r?\n/)
                    .map(item => item.trim())
                    .filter(Boolean);

                const duplicate = existing.some(
                    item => item.toLowerCase() === newActivity.toLowerCase()
                );

                if (duplicate) {
                    alert("This activity is already added.");
                    return;
                }

                existing.push(newActivity);
                activitiesTextarea.value = existing.join("\n");

                activitiesTextarea.dispatchEvent(
                    new Event("input", {
                        bubbles: true
                    })
                );

                customNameInput.value = "";
                customNameInput.focus();
            }

            customAddButton.addEventListener("click", addCustomActivity);

            customNameInput.addEventListener("keydown", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    addCustomActivity();
                }
            });
        }
    </script>

</body>

</html>