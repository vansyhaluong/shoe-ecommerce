<?php
include_once 'config.php';

// Cấu hình Header trả về JSON nếu là request AJAX
$is_ajax = isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

if ($is_ajax) {
    header('Content-Type: application/json; charset=utf-8');
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    if ($is_ajax) {
        echo json_encode([
            'status' => 'unauthorized',
            'message' => 'Vui lòng đăng nhập để thực hiện chức năng này.',
            'redirect' => 'login.php'
        ]);
        exit();
    } else {
        // Lưu trang trước đó để redirect sau khi login
        $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        redirect('login.php');
    }
}

$user_id = (int)$_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Hàm tính số lượng sản phẩm trong wishlist
function get_wishlist_count($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return (int)$stmt->fetchColumn();
}

if ($action === 'toggle') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    if ($product_id <= 0 || !db_record_exists('products', 'id', $product_id)) {
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không hợp lệ hoặc không tồn tại.']);
            exit();
        }
        redirect('index.php');
    }
    
    // Kiểm tra xem sản phẩm đã có trong wishlist chưa
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Nếu đã có -> xóa đi
        $del = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $del->execute([$user_id, $product_id]);
        $response_action = 'removed';
    } else {
        // Nếu chưa có -> thêm mới
        try {
            $ins = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
            $ins->execute([$user_id, $product_id]);
            $response_action = 'added';
        } catch (PDOException $e) {
            // Đề phòng trường hợp trùng lặp do race condition
            $response_action = 'added';
        }
    }
    
    $count = get_wishlist_count($pdo, $user_id);
    
    if ($is_ajax) {
        echo json_encode([
            'status' => 'success',
            'action' => $response_action,
            'count' => $count,
            'message' => $response_action === 'added' ? 'Đã thêm vào danh sách yêu thích!' : 'Đã xóa khỏi danh sách yêu thích!'
        ]);
        exit();
    }
    
    redirect($_SERVER['HTTP_REFERER'] ?? 'wishlist.php');
}

if ($action === 'remove') {
    $product_id = (int)($_GET['product_id'] ?? $_POST['product_id'] ?? 0);
    
    if ($product_id <= 0 || !db_record_exists('products', 'id', $product_id)) {
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không hợp lệ hoặc không tồn tại.']);
            exit();
        }
        redirect('wishlist.php');
    }
    
    // Kiểm tra xem sản phẩm có thực sự nằm trong danh sách yêu thích của người dùng hiện tại không
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $existing = $stmt->fetch();
    
    if (!$existing) {
        if ($is_ajax) {
            echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không nằm trong danh sách yêu thích của bạn hoặc bạn không có quyền xóa!']);
            exit();
        }
        redirect('wishlist.php');
    }
    
    $del = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $del->execute([$user_id, $product_id]);
    
    $count = get_wishlist_count($pdo, $user_id);
    
    if ($is_ajax) {
        echo json_encode([
            'status' => 'success',
            'action' => 'removed',
            'count' => $count,
            'message' => 'Đã xóa sản phẩm khỏi danh sách yêu thích!'
        ]);
        exit();
    }
    
    redirect('wishlist.php');
}

if ($action === 'count') {
    $count = get_wishlist_count($pdo, $user_id);
    if ($is_ajax) {
        echo json_encode(['status' => 'success', 'count' => $count]);
        exit();
    }
    echo $count;
    exit();
}

// Mặc định redirect
redirect('index.php');
?>
