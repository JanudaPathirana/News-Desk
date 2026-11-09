<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}
function redirect($url) {
    header('Location: ' . $url);
    exit;
}
function post($key) {
    return trim($_POST[$key] ?? '');
}
function is_logged_in() {
    return !empty($_SESSION['user']);
}
function user() {
    return $_SESSION['user'] ?? null;
}
function login_user($row) {
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int)$row['id'],
        'name' => $row['name'],
        'student_id' => $row['student_id'] ?? '',
        'email' => $row['email'],
        'role' => $row['role']
    ];
}
function flash($type, $message) {
    $_SESSION['flash'][] = [$type, $message];
}
function get_flash() {
    $list = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $list;
}

function is_admin() {
    return is_logged_in() && $_SESSION['user']['role'] == 'admin';
}
function require_login() {
    if (!is_logged_in()) {
        flash('warning', 'Please log in first.');
        redirect('login.php');
    }
}
function require_admin() {
    if (!is_admin()) {
        flash('danger', 'Only admins can open this page.');
        redirect('login.php');
    }
}

function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}
function csrf_field() {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}
function csrf_check() {
    if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? 'none')) {
        die('Form token is not valid. Go back and try again.');
    }
}
function fmt_date($date) {
    return date('d M Y', strtotime($date));
}
