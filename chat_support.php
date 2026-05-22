<?php
include_once 'config.php';

$session_id = session_id();
$user_id = $_SESSION['user_id'] ?? null;

$message = $_POST['message'] ?? '';
$response = '';

if (!empty($message)) {
    // 1. Làm sạch và chuẩn hóa tin nhắn
    $message = htmlspecialchars(trim($message));
    $msg_lower = mb_strtolower($message, 'UTF-8');

    // 2. Định nghĩa các nhóm từ khóa và danh sách câu trả lời ngẫu nhiên (variations)
    $shipping_keywords = ['ship', 'giao hàng', 'vận chuyển', 'cod', 'nhận hàng'];
    $size_keywords = ['size', 'chọn size', 'vừa chân', 'fit', 'cỡ giày'];
    $order_keywords = ['đơn hàng', 'trạng thái đơn', 'order', 'kiểm tra đơn'];
    $payment_keywords = ['thanh toán', 'payment', 'trả tiền', 'chuyển khoản'];
    $product_keywords = ['sản phẩm', 'giày', 'sneaker', 'nike', 'adidas', 'jordan'];
    $cleaning_keywords = ['vệ sinh giày', 'làm sạch giày', 'cleaning', 'sneaker cleaning'];
    $shoelaces_keywords = ['dây giày', 'shoelaces', 'dây sneaker'];
    $return_keywords = ['đổi trả', 'hoàn hàng', 'trả hàng', 'refund'];
    $sale_keywords = ['khuyến mãi', 'sale', 'giảm giá', 'voucher'];
    $contact_keywords = ['liên hệ', 'support', 'tư vấn', 'hotline'];

    // Hàm phụ trợ kiểm tra từ khóa
    function has_keyword($msg, $keywords) {
        foreach ($keywords as $kw) {
            if (mb_strpos($msg, $kw) !== false) {
                return true;
            }
        }
        return false;
    }

    if (has_keyword($msg_lower, $shipping_keywords)) {
        $variations = [
            "Shop hỗ trợ giao hàng toàn quốc từ 2-5 ngày. Bạn có thể kiểm tra trạng thái đơn hàng trong mục Lịch sử đơn hàng.",
            "Đơn hàng của bạn sẽ được vận chuyển nhanh chóng từ 2-5 ngày làm việc. Đặc biệt shop hỗ trợ thanh toán COD khi nhận hàng!",
            "ShoeStore giao hàng toàn quốc siêu tốc từ 2-5 ngày. Bạn có thể theo dõi hành trình đơn hàng ở tài khoản cá nhân nha."
        ];
        $response = $variations[array_rand($variations)];
    } elseif (has_keyword($msg_lower, $size_keywords)) {
        $variations = [
            "Bạn có thể chọn size trực tiếp trên trang chi tiết sản phẩm. Nếu phân vân, hãy chọn theo size giày bạn thường mang.",
            "Chọn size cực kỳ đơn giản ngay tại trang chi tiết sản phẩm. Shop khuyên bạn nên chọn đúng size (true-to-size) thường ngày của mình nhé!",
            "Nếu bạn đắn đo về cỡ giày, hãy xem bảng quy đổi kích cỡ hoặc chọn đúng size giày thường đi của bạn là vừa vặn nhất."
        ];
        $response = $variations[array_rand($variations)];
    } elseif (has_keyword($msg_lower, $order_keywords)) {
        $variations = [
            "Bạn có thể xem trạng thái đơn hàng trong trang Lịch sử đơn hàng sau khi đăng nhập.",
            "Vui lòng đăng nhập tài khoản và truy cập trang Lịch sử đơn hàng để theo dõi tiến độ đơn hàng nhé.",
            "Tất cả đơn đặt hàng của bạn đều được lưu lại chi tiết trong mục 'Đơn hàng của tôi'. Đăng nhập để kiểm tra nhé bạn!"
        ];
        $response = $variations[array_rand($variations)];
    } elseif (has_keyword($msg_lower, $payment_keywords)) {
        $variations = [
            "Shop hỗ trợ thanh toán khi đặt hàng. Thông tin thanh toán sẽ hiển thị ở bước checkout.",
            "Bạn có thể chọn thanh toán COD khi nhận hàng hoặc chuyển khoản ngân hàng ngay tại bước thanh toán (Checkout) cực tiện lợi.",
            "Mọi thông tin thanh toán chi tiết sẽ được hiển thị đầy đủ và bảo mật ở bước thanh toán cuối cùng của đơn hàng."
        ];
        $response = $variations[array_rand($variations)];
    } elseif (has_keyword($msg_lower, $cleaning_keywords)) {
        $variations = [
            "Shop có dịch vụ vệ sinh giày chuyên nghiệp, giúp đôi sneaker của bạn sạch đẹp và bền hơn.",
            "Dịch vụ vệ sinh giày premium của ShoeStore sẽ chăm sóc đôi giày của bạn từ trong ra ngoài, giúp giày luôn như mới!",
            "Hãy trải nghiệm dịch vụ giặt và bảo dưỡng sneaker chuyên sâu tại ShoeStore để đôi giày yêu thích của bạn luôn thơm tho và sạch đẹp."
        ];
        $response = $variations[array_rand($variations)];
    } elseif (has_keyword($msg_lower, $shoelaces_keywords)) {
        $variations = [
            "Shop có nhiều mẫu dây giày chất lượng cao để bạn thay đổi phong cách cho đôi sneaker.",
            "Bạn có thể chọn mua dây giày basic trắng, đen, phản quang 3M hay dây oval thể thao tại danh mục phụ kiện Dây giày nhé!",
            "Đổi mới phong cách đôi sneaker của bạn với các sản phẩm dây giày cao cấp hiện đang được bán tại cửa hàng."
        ];
        $response = $variations[array_rand($variations)];
    } elseif (has_keyword($msg_lower, $product_keywords)) {
        $variations = [
            "Bạn có thể xem các mẫu sneaker mới nhất tại trang Sản phẩm. Shop có nhiều mẫu giày theo phong cách Nike + GOAT.",
            "ShoeStore cập nhật liên tục các mẫu Air Max, Ultraboost, AF1 cực hot. Ghé ngay trang Sản phẩm để chọn cho mình một đôi ưng ý!",
            "Các dòng sneaker đỉnh cao, thời thượng nhất đều đang tụ hội tại mục Sản phẩm của shop. Hãy khám phá ngay nhé!"
        ];
        $response = $variations[array_rand($variations)];
    } elseif (has_keyword($msg_lower, $return_keywords)) {
        $variations = [
            "Shop hỗ trợ đổi trả theo chính sách cửa hàng. Vui lòng liên hệ support nếu sản phẩm có vấn đề.",
            "Chính sách đổi trả linh hoạt của shop giúp bạn hoàn toàn an tâm. Inbox hoặc gọi hotline nếu có bất kỳ lỗi nào từ sản phẩm nhé.",
            "Đổi trả dễ dàng trong vòng 7 ngày nếu lỗi do nhà sản xuất. Hãy liên hệ bộ phận hỗ trợ khách hàng để được xử lý nhanh nhất."
        ];
        $response = $variations[array_rand($variations)];
    } elseif (has_keyword($msg_lower, $sale_keywords)) {
        $variations = [
            "Bạn có thể theo dõi các chương trình khuyến mãi tại trang chủ hoặc mục sản phẩm nổi bật.",
            "Nhiều voucher hấp dẫn và chương trình giảm giá cực sốc được cập nhật liên tục tại trang chủ ShoeStore!",
            "Săn ngay ưu đãi lớn ngay hôm nay! Hãy xem danh sách sản phẩm đang được sale ưu đãi trực tiếp trên website nhé."
        ];
        $response = $variations[array_rand($variations)];
    } elseif (has_keyword($msg_lower, $contact_keywords)) {
        $variations = [
            "Bạn có thể liên hệ shop qua chatbox này hoặc thông tin liên hệ ở cuối trang.",
            "Gặp khó khăn? Vui lòng gọi trực tiếp hotline ở chân trang hoặc tiếp tục để lại lời nhắn tại đây để nhân viên trợ giúp nhé.",
            "Đội ngũ support luôn sẵn sàng hỗ trợ bạn 24/7 qua chatbox trực tuyến hoặc hotline của ShoeStore."
        ];
        $response = $variations[array_rand($variations)];
    } else {
        $variations = [
            "Mình chưa hiểu rõ câu hỏi của bạn. Bạn có thể hỏi về sản phẩm, size, giao hàng, đơn hàng, thanh toán, vệ sinh giày hoặc dây giày.",
            "Dạ, Sneaker Assistant chưa nhận diện được yêu cầu này. Bạn hãy thử nhập các từ khóa liên quan đến size giày, ship hàng, đổi trả hay vệ sinh giày nhé!",
            "Bạn cần trợ giúp về vấn đề gì ạ? Thử hỏi về các dịch vụ vệ sinh giày, mua dây giày, khuyến mãi hoặc kiểm tra đơn hàng xem sao."
        ];
        $response = $variations[array_rand($variations)];
    }

    // 3. Lưu tin nhắn của khách vào cơ sở dữ liệu để đồng bộ lịch sử
    $stmt = $pdo->prepare("INSERT INTO messages (user_id, session_id, message, is_from_admin) VALUES (?, ?, ?, 0)");
    $stmt->execute([$user_id, $session_id, $message]);

    // 4. Lưu phản hồi của Sneaker Assistant vào cơ sở dữ liệu để đồng bộ lịch sử
    $stmt = $pdo->prepare("INSERT INTO messages (user_id, session_id, message, is_from_admin) VALUES (?, ?, ?, 1)");
    $stmt->execute([$user_id, $session_id, $response]);

    echo json_encode([
        'status' => 'success',
        'reply' => $response
    ]);
    exit();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Tin nhắn không được để trống'
    ]);
    exit();
}
?>
