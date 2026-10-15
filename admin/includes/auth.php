
<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    // Strengthen PHP session handling before starting the session.
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');

    session_name('NEXUS_ADMIN_SESSION');

    $isHttps = isset($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== ''
        && strtolower((string) $_SERVER['HTTPS']) !== 'off';

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/nexus-educational-tours/admin/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function requireAdmin(): void
{
    if (
        !isset($_SESSION['admin_id']) ||
        !is_int($_SESSION['admin_id']) ||
        $_SESSION['admin_id'] < 1
    ) {
        header('Location: /nexus-educational-tours/admin/login.php');
        exit;
    }
}

function regenerateAdminSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        if (!session_regenerate_id(true)) {
            error_log('Nexus admin session regeneration failed.');
            http_response_code(500);
            exit('A server error occurred. Please try again later.');
        }
    }
}

function destroyAdminSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
    }

    session_destroy();
}
