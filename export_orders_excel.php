<?php
include_once 'config.php';

// Bảo mật: Chỉ admin mới có quyền truy cập xuất file
check_admin();

// Lấy các đơn hàng đã thanh toán / hoàn thành từ database
try {
    $stmt = $pdo->query("SELECT * FROM orders WHERE status IN ('completed', 'paid') ORDER BY created_at DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi truy vấn dữ liệu: " . $e->getMessage());
}

// Thiết lập tên file xuất ra dạng orders_export_YYYYMMDD.csv
$filename = 'orders_export_' . date('Ymd') . '.csv';

// Thiết lập header để trình duyệt tự động tải xuống
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Mở stream ghi dữ liệu
$output = fopen('php://output', 'w');

// Xuất BOM UTF-8 để Microsoft Excel nhận dạng đúng ký tự tiếng Việt có dấu
fwrite($output, "\xEF\xBB\xBF");

// Xuất tiêu đề các cột
fputcsv($output, [
    'Mã Đơn Hàng (Order ID)',
    'Tên Khách Hàng (Customer Name)',
    'Số Điện Thoại (Phone)',
    'Địa Chỉ Giao Hàng (Shipping Address)',
    'Tổng Tiền (Total Amount - VNĐ)',
    'Trạng Thái (Status)',
    'Ngày Đặt (Created At)'
]);

// Xuất dữ liệu từng đơn hàng
foreach ($orders as $order) {
    // Format trạng thái sang tiếng Việt dễ đọc (tùy chọn nhưng chuyên nghiệp hơn)
    $status_vi = $order['status'];
    switch ($order['status']) {
        case 'pending':
            $status_vi = 'Chờ xử lý (Pending)';
            break;
        case 'processing':
            $status_vi = 'Đang xử lý';
            break;
        case 'completed':
            $status_vi = 'Đã hoàn thành';
            break;
        case 'cancelled':
            $status_vi = 'Đã hủy';
            break;
    }

    fputcsv($output, [
        '#' . $order['id'],
        $order['shipping_name'],
        $order['shipping_phone'],
        $order['shipping_address'],
        number_format($order['total_amount'], 0, ',', '.'),
        $status_vi,
        date('d/m/Y H:i:s', strtotime($order['created_at']))
    ]);
}

fclose($output);
exit();
?>
