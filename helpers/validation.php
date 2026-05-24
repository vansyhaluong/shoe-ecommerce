<?php
// Centralized validation helpers for ShoeStore

// Ensure session is started if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Clean up text input to prevent XSS
 */
function sanitize_text($str) {
    return htmlspecialchars(trim((string)$str), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate string length boundaries
 */
function validate_string_len($str, $min, $max) {
    $len = mb_strlen(trim((string)$str), 'UTF-8');
    return $len >= $min && $len <= $max;
}

/**
 * Validate numeric ID
 */
function validate_numeric_id($id) {
    if (!is_numeric($id)) return false;
    $val = (int)$id;
    return $val > 0;
}

/**
 * Validate price (must be numeric, positive, and under a maximum limit)
 */
function validate_price($price) {
    if (!is_numeric($price)) return false;
    $val = (float)$price;
    return $val > 0 && $val <= 100000000.0;
}

/**
 * Validate phone number format (basic digits only, 9-15 chars)
 */
function validate_phone($phone) {
    $clean_phone = preg_replace('/[+\s\-()]/', '', trim((string)$phone));
    return preg_match('/^[0-9]{9,15}$/', $clean_phone);
}

/**
 * Validate email format
 */
function validate_email($email) {
    return filter_var(trim((string)$email), FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate image upload
 */
function validate_image_file($file, $max_size = 5242880) { // Default 5MB
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['valid' => false, 'error' => 'Lỗi tải tệp tin lên.'];
    }

    // Check size
    if ($file['size'] > $max_size) {
        return ['valid' => false, 'error' => 'Kích thước ảnh vượt quá giới hạn cho phép (tối đa 5MB).'];
    }

    // Check type/extension
    $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Fallback: check mime type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($ext, $allowed_exts) || !in_array($mime, $allowed_mimes)) {
        return ['valid' => false, 'error' => 'Chỉ cho phép tải lên tệp ảnh định dạng JPG, JPEG, PNG, WEBP.'];
    }

    return ['valid' => true, 'extension' => $ext];
}

/**
 * Check if a record exists in a DB table using prepared statements
 */
function db_record_exists($table, $column, $value) {
    global $pdo;
    $allowed_tables = ['brands', 'categories', 'products', 'users', 'orders'];
    if (!in_array($table, $allowed_tables)) return false;
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) return false;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `$table` WHERE `$column` = ?");
        $stmt->execute([$value]);
        return (int)$stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        // Silent fail, log or do not leak raw details
        return false;
    }
}

/**
 * Session validation helpers
 */
function set_validation_error($key, $message) {
    $_SESSION['validation_errors'][$key] = $message;
}

function has_validation_errors() {
    return !empty($_SESSION['validation_errors']);
}

function get_validation_errors() {
    $errors = $_SESSION['validation_errors'] ?? [];
    unset($_SESSION['validation_errors']);
    return $errors;
}

function set_old_input($data) {
    $_SESSION['old_input'] = $data;
}

function get_old_input() {
    $old = $_SESSION['old_input'] ?? [];
    unset($_SESSION['old_input']);
    return $old;
}
