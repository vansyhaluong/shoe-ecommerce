<?php
include_once 'config.php';

$action = $_GET['action'] ?? '';
$session_id = session_id();
$user_id = $_SESSION['user_id'] ?? null;

if ($action === 'send') {
    $message = $_POST['message'] ?? '';
    if (!empty($message)) {
        // 1. Lưu tin nhắn của khách
        $stmt = $pdo->prepare("INSERT INTO messages (user_id, session_id, message, is_from_admin) VALUES (?, ?, ?, 0)");
        $stmt->execute([$user_id, $session_id, $message]);

        // 2. Logic Chatbot tự động
        $reply = "";
        $msg_lower = mb_strtolower($message, 'UTF-8');

        if (strpos($msg_lower, 'giá') !== false || strpos($msg_lower, 'bao nhiêu') !== false) {
            $reply = "Chào bạn, giá của tất cả sản phẩm đều được niêm yết công khai ngay dưới mỗi mẫu giày ạ! Bạn có thể xem trực tiếp nhé. ✨";
        } elseif (strpos($msg_lower, 'ship') !== false || strpos($msg_lower, 'giao hàng') !== false) {
            $reply = "Dạ, ShoeStore miễn phí vận chuyển cho mọi đơn hàng trên 2.000.000đ. Các đơn khác phí ship đồng giá 30k toàn quốc ạ! 🚚";
        } elseif (strpos($msg_lower, 'size') !== false || strpos($msg_lower, 'kích cỡ') !== false) {
            $reply = "Bạn có thể xem 'Bảng chọn Size' trong phần Thông số kỹ thuật của từng sản phẩm để chọn được đôi giày vừa vặn nhất nhé! 👟";
        } elseif (strpos($msg_lower, 'thật') !== false || strpos($msg_lower, 'auth') !== false || strpos($msg_lower, 'fake') !== false) {
            $reply = "Bạn hoàn toàn yên tâm ạ! ShoeStore cam kết chỉ bán hàng chính hãng 100%, phát hiện hàng giả đền tiền gấp đôi ạ! ✅";
        } else {
            $reply = "Cảm ơn bạn đã quan tâm! Tin nhắn của bạn đã được chuyển đến nhân viên hỗ trợ. Chúng mình sẽ phản hồi bạn trong ít phút nhé! 🙏";
        }

        // Lưu câu trả lời của Bot
        if ($reply) {
            $stmt = $pdo->prepare("INSERT INTO messages (user_id, session_id, message, is_from_admin) VALUES (?, ?, ?, 1)");
            $stmt->execute([$user_id, $session_id, $reply]);
        }

        echo json_encode(['status' => 'success']);
    }
}

if ($action === 'fetch') {
    // Lấy tin nhắn của user hiện tại hoặc session hiện tại
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE (user_id = ? AND user_id IS NOT NULL) OR (session_id = ?) ORDER BY created_at ASC");
    $stmt->execute([$user_id, $session_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($messages);
}
?>
