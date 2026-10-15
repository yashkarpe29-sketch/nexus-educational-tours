<?php

require_once __DIR__ . '/includes/auth.php';

if (empty($_SESSION['admin_id'])) {
    header('Location: /nexus-educational-tours/admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Method not allowed.');
}

$token = $_POST['csrf_token'] ?? '';
$sessionToken = $_SESSION['admin_login_csrf'] ?? '';

if (
    !is_string($token) ||
    !is_string($sessionToken) ||
    $sessionToken === '' ||
    !hash_equals($sessionToken, $token)
) {
    http_response_code(403);
    exit('Invalid request. Please return to the dashboard and try again.');
}

destroyAdminSession();

header('Location: /nexus-educational-tours/admin/login.php');
exit;
