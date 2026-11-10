<?php
// functions used in every page
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

// Automatic schema & sample images update
try {
    $hasPhotoCol = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_photo'")->fetch();
    if (!$hasPhotoCol) {
        $pdo->exec("ALTER TABLE users ADD COLUMN profile_photo VARCHAR(255) NULL DEFAULT NULL AFTER role");
    }
    // Update existing sample articles with real NSBM campus photos
    $sampleImgs = [
        1 => 'nsbm_exam.jpg',
        2 => 'nsbm_greenfest.jpg',
        3 => 'nsbm_cricket.jpg',
        4 => 'nsbm_library.jpg',
        5 => 'nsbm_computing.jpg',
        6 => 'nsbm_hostel.jpg'
    ];
    foreach ($sampleImgs as $artId => $imgName) {
        $pdo->prepare("UPDATE articles SET image = ? WHERE id = ? AND (image IS NULL OR image = '')")->execute([$imgName, $artId]);
    }
} catch (Exception $e) {
    // Fail silently if DB is in another state
}

// escape text before showing it
function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// get a post value without extra spaces
function post($key) {
    return trim($_POST[$key] ?? '');
}

// short version of the article text
function excerpt($text, $length = 140) {
    $text = trim(strip_tags($text));
    if (mb_strlen($text) > $length) {
        return mb_substr($text, 0, $length) . '...';
    }
    return $text;
}

function fmt_date($date) {
    return date('d M Y', strtotime($date));
}

// if no image uploaded use the placeholder
function img_url($name, $base = '') {
    if ($name) {
        return $base . 'assets/uploads/' . e($name);
    }
    return $base . 'assests/img/placeholder.svg';
}

// ---------- login checks ----------
function is_logged_in() {
    return !empty($_SESSION['user']);
}

function user() {
    if (!empty($_SESSION['user']) && !array_key_exists('profile_photo', $_SESSION['user'])) {
        refresh_user_session();
    }
    return $_SESSION['user'] ?? null;
}

function is_admin() {
    return is_logged_in() && $_SESSION['user']['role'] == 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        flash('warning', 'Please log in first.');
        redirect('/News-Desk/login.php');
    }
}

// used in all admin pages (except admin/login.php)
function require_admin() {
    if (!is_admin()) {
        flash('danger', 'Only admins can open this page.');
        redirect('/News-Desk/admin/login.php');
    }
}

function login_user($row) {
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int)$row['id'],
        'name' => $row['name'],
        'student_id' => $row['student_id'] ?? '',
        'email' => $row['email'],
        'role' => $row['role'],
        'profile_photo' => $row['profile_photo'] ?? null
    ];
}

function refresh_user_session() {
    global $pdo;
    if (!is_logged_in()) {
        return null;
    }
    $st = $pdo->prepare('SELECT id, name, student_id, email, role, profile_photo FROM users WHERE id = ?');
    $st->execute([$_SESSION['user']['id']]);
    $u = $st->fetch();
    if ($u) {
        $_SESSION['user']['name'] = $u['name'];
        $_SESSION['user']['student_id'] = $u['student_id'] ?? '';
        $_SESSION['user']['email'] = $u['email'];
        $_SESSION['user']['role'] = $u['role'];
        $_SESSION['user']['profile_photo'] = $u['profile_photo'] ?? null;
    }
    return $_SESSION['user'];
}

function user_avatar_url($userObj = null, $base = '') {
    $u = $userObj ?? user();
    if ($u && !empty($u['profile_photo'])) {
        $path = __DIR__ . '/../assets/uploads/' . $u['profile_photo'];
        if (is_file($path)) {
            return $base . 'assets/uploads/' . e($u['profile_photo']);
        }
    }
    return $base . 'assests/img/default-avatar.svg';
}

// ---------- flash messages (alerts shown once) ----------
function flash($type, $message) {
    $_SESSION['flash'][] = [$type, $message];
}

function get_flash() {
    $list = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $list;
}

// ---------- csrf token for forms ----------
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

// ---------- image upload ----------
// returns the new file name, or null if no file / error
function save_image($file, &$error) {
    $error = '';
    if (!$file || $file['error'] == UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] != UPLOAD_ERR_OK) {
        $error = 'Image upload failed.';
        return null;
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        $error = 'Image must be less than 2MB.';
        return null;
    }
    // check the real file type, not only the extension
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    if (!isset($types[$mime])) {
        $error = 'Only jpg, png, webp or gif images are allowed.';
        return null;
    }
    $name = bin2hex(random_bytes(8)) . '.' . $types[$mime];
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../assets/uploads/' . $name)) {
        $error = 'Could not save the image.';
        return null;
    }
    return $name;
}

function delete_image($name) {
    if ($name) {
        $path = __DIR__ . '/../assets/uploads/' . basename($name);
        if (is_file($path)) {
            unlink($path);
        }
    }
}

// ---------- other small helpers ----------
function all_categories() {
    global $pdo;
    return $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
}

// colored badge for status
function status_badge($status) {
    $colors = ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger', 'visible' => 'success', 'hidden' => 'secondary'];
    $color = $colors[$status] ?? 'secondary';
    return '<span class="badge bg-' . $color . '">' . e(ucfirst($status)) . '</span>';
}
