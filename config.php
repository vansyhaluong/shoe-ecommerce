<?php
ob_start();
session_start();

include_once 'languages.php';

// Xử lý chuyển đổi ngôn ngữ
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$current_lang = $_SESSION['lang'] ?? 'vi';

// Hàm dịch thuật
function __($key)
{
    global $languages, $current_lang;
    return $languages[$current_lang][$key] ?? $key;
}

// Cấu hình Database
$db_host = 'localhost';
$db_name = 'shopgiay';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Kết nối Database thất bại: " . $e->getMessage());
}

// Các hàm tiện ích dùng chung
function format_price($price)
{
    return number_format($price, 0, ',', '.') . 'đ';
}

function redirect($url)
{
    header("Location: " . $url);
    exit();
}

function check_admin()
{
    if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
        header("Location: login.php");
        exit();
    }
}
