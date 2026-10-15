<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/database.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if (!empty($_SESSION['admin_id'])) {
    header('Location: /nexus-educational-tours/admin/dashboard.php');
    exit;
}

if (
    !isset($_SESSION['admin_login_csrf']) ||
    !is_string($_SESSION['admin_login_csrf'])
) {
    $_SESSION['admin_login_csrf'] = bin2hex(random_bytes(32));
}

$error = '';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $sessionToken = $_SESSION['admin_login_csrf'] ?? '';

    if (
        !is_string($token) ||
        !is_string($sessionToken) ||
        $sessionToken === '' ||
        !hash_equals($sessionToken, $token)
    ) {
        $error = 'Invalid request. Please refresh and try again.';
    } elseif (
        $username === '' ||
        strlen($username) > 100 ||
        $password === '' ||
        strlen($password) > 4096
    ) {
        $error = 'Invalid username or password.';
    } else {
        $ipAddress = (string) ($_SERVER['REMOTE_ADDR'] ?? '');

        if (filter_var($ipAddress, FILTER_VALIDATE_IP) === false) {
            $error = 'Login could not be completed. Please try again.';
        } else {
            try {
                $pdo->beginTransaction();

                // Ensure there is a row to lock for this IP.
                $insert = $pdo->prepare(
                    'INSERT IGNORE INTO admin_login_attempts
                        (ip_address, failed_attempts, first_attempt_at)
                     VALUES (:ip, 0, NOW())'
                );
                $insert->execute(['ip' => $ipAddress]);

                $attemptStmt = $pdo->prepare(
                    'SELECT failed_attempts, first_attempt_at, locked_until
                     FROM admin_login_attempts
                     WHERE ip_address = :ip
                     FOR UPDATE'
                );
                $attemptStmt->execute(['ip' => $ipAddress]);
                $attempt = $attemptStmt->fetch();

                $isLocked = $attempt
                    && !empty($attempt['locked_until'])
                    && strtotime($attempt['locked_until']) > time();

                if ($isLocked) {
                    $pdo->commit();
                    $error = 'Too many failed attempts. Please try again in 15 minutes.';
                } else {
                    // Reset an expired lock or an old attempt window.
                    $resetWindow = !$attempt
                        || strtotime($attempt['first_attempt_at'])
                        < strtotime('-15 minutes');

                    if (
                        $attempt &&
                        (
                            $resetWindow ||
                            !empty($attempt['locked_until'])
                        )
                    ) {
                        $reset = $pdo->prepare(
                            'UPDATE admin_login_attempts
                             SET failed_attempts = 0,
                                 first_attempt_at = NOW(),
                                 locked_until = NULL
                             WHERE ip_address = :ip'
                        );
                        $reset->execute(['ip' => $ipAddress]);
                    }

                    $stmt = $pdo->prepare(
                        'SELECT id, username, password_hash
                         FROM admins
                         WHERE username = :username
                         LIMIT 1'
                    );
                    $stmt->execute(['username' => $username]);
                    $admin = $stmt->fetch();

                    if (
                        $admin &&
                        password_verify($password, $admin['password_hash'])
                    ) {
                        $delete = $pdo->prepare(
                            'DELETE FROM admin_login_attempts
                             WHERE ip_address = :ip'
                        );
                        $delete->execute(['ip' => $ipAddress]);

                        $pdo->commit();

                        regenerateAdminSession();

                        $_SESSION['admin_id'] = (int) $admin['id'];
                        $_SESSION['admin_username'] = $admin['username'];
                        $_SESSION['admin_login_csrf'] =
                            bin2hex(random_bytes(32));

                        header(
                            'Location: /nexus-educational-tours/admin/dashboard.php'
                        );
                        exit;
                    }

                    $update = $pdo->prepare(
                        'UPDATE admin_login_attempts
                         SET failed_attempts = failed_attempts + 1,
                             locked_until = CASE
                                 WHEN failed_attempts + 1 >= 5
                                 THEN DATE_ADD(NOW(), INTERVAL 15 MINUTE)
                                 ELSE NULL
                             END
                         WHERE ip_address = :ip'
                    );
                    $update->execute(['ip' => $ipAddress]);

                    $pdo->commit();
                    $error = 'Invalid username or password.';
                }
            } catch (Throwable $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                error_log(
                    'Nexus admin login failed: ' . $exception->getMessage()
                );
                $error = 'Login could not be completed. Please try again.';
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#173B67">

    <title>Admin Login | Nexus</title>

    <link rel="stylesheet" href="../css/admin-login.css">
</head>

<body class="admin-login-page">

    <main class="login-layout">

        <section class="login-brand-panel">
            <a class="login-brand" href="../index.php" aria-label="Nexus Home">
                <span class="login-brand-mark">
                    <img
                        src="../assets/images/logo/logo.png"
                        alt="Nexus Educational Tours and Travels logo">
                </span>
                <span class="login-brand-name">
                    NEXUS
                    <small>EDUCATIONAL TOURS &amp; TRAVELS</small>
                </span>
            </a>

            <div class="login-brand-content">
                <span class="login-eyebrow">ADMINISTRATION PORTAL</span>

                <h1>Every journey<br>starts with<br><span>great planning.</span></h1>

                <p>
                    Manage educational tours, organize enquiries
                    and keep your travel experiences on track.
                </p>

                <div class="login-feature">
                    <span class="login-feature-icon">✓</span>
                    <span>Tour management</span>
                </div>

                <div class="login-feature">
                    <span class="login-feature-icon">✓</span>
                    <span>Enquiry management</span>
                </div>

                <div class="login-feature">
                    <span class="login-feature-icon">✓</span>
                    <span>Secure administrator access</span>
                </div>
            </div>

            <div class="login-brand-footer">
                NEXUS EDUCATIONAL TOURS AND TRAVELS
            </div>

            <div class="login-decoration login-decoration-one"></div>
            <div class="login-decoration login-decoration-two"></div>
        </section>

        <section class="login-form-panel">
            <div class="login-card">

                <div class="login-mobile-brand">
                    <span class="login-brand-mark">
                        <img
                            src="../assets/images/logo/logo.png"
                            alt="Nexus logo">
                    </span>
                    <span>NEXUS</span>
                </div>

                <div class="login-card-heading">
                    <span class="login-card-eyebrow">WELCOME BACK</span>
                    <h2>Admin Login</h2>
                    <p>Sign in to access your administration dashboard.</p>
                </div>

                <?php if ($error !== ''): ?>
                    <div class="login-alert" role="alert">
                        <span class="login-alert-icon">!</span>
                        <span><?= e($error) ?></span>
                    </div>
                <?php endif; ?>

                <form method="post" action="" class="login-form" autocomplete="on">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e($_SESSION['admin_login_csrf']) ?>">

                    <div class="login-field">
                        <label for="username">Username</label>

                        <div class="login-input-wrap">
                            <span class="login-input-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="8" r="4"
                                        stroke="currentColor" stroke-width="1.7" />
                                    <path d="M4 21c0-4.1 3.3-7 8-7s8 2.9 8 7"
                                        stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                </svg>
                            </span>

                            <input
                                id="username"
                                name="username"
                                type="text"
                                maxlength="100"
                                autocomplete="username"
                                placeholder="Enter your username"
                                required>
                        </div>
                    </div>

                    <div class="login-field">
                        <label for="password">Password</label>

                        <div class="login-input-wrap">
                            <span class="login-input-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <rect x="4" y="10" width="16" height="11"
                                        rx="2" stroke="currentColor"
                                        stroke-width="1.7" />
                                    <path d="M8 10V7a4 4 0 0 1 8 0v3"
                                        stroke="currentColor" stroke-width="1.7"
                                        stroke-linecap="round" />
                                    <circle cx="12" cy="15" r="1"
                                        fill="currentColor" />
                                </svg>
                            </span>

                            <input
                                id="password"
                                name="password"
                                type="password"
                                maxlength="4096"
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                required>
                        </div>
                    </div>

                    <button class="login-submit" type="submit">
                        <span>Sign In to Dashboard</span>
                        <span class="login-submit-arrow" aria-hidden="true">→</span>
                    </button>

                </form>

                <div class="login-security-note">
                    <span class="login-security-icon" aria-hidden="true">✓</span>
                    <span>Your administrator session is protected.</span>
                </div>

                <div class="login-card-footer">
                    <a href="../index.php">← Back to website</a>
                    <span>© Nexus</span>
                </div>

            </div>
        </section>

    </main>

</body>

</html>