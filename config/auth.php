<?php
// config/auth.php
session_start();
require_once __DIR__ . '/database.php';

function is_logged_in() {
    return isset($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit;
    }
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_role($role) {
    if (!is_logged_in() || ($_SESSION['user']['role'] ?? '') !== $role) {
        http_response_code(403);
        echo 'Access denied.';
        exit;
    }
}

?>
