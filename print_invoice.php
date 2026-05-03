<?php
include 'config.php';
check_admin();

$order_id = (int)($_GET['id'] ?? 0);

// Lấy thông tin đơn hàng
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) die("Đơn hàng không tồn tại!");

// Lấy danh sách sản phẩm
$stmt = $pdo->prepare("
    SELECT oi.*, p.name 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn #<?= $order_id ?> - ShoeStore</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.6; padding: 40px; }
        .invoice-box { max-width: 800px; margin: auto; border: 1px solid #eee; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.05); }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; border-bottom: 2px solid #6366f1; padding-bottom: 20px; }
        .logo { font-size: 28px; font-weight: 900; color: #6366f1; font-style: italic; }
        .info { margin-bottom: 40px; display: grid; grid-template-cols: 1fr 1fr; gap: 40px; }
        .info h3 { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 10px; }
        .info p { margin: 0; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        table th { background: #f8fafc; text-align: left; padding: 12px; border-bottom: 2px solid #e2e8f0; font-size: 12px; text-transform: uppercase; }
        table td { padding: 12px; border-bottom: 1px solid #f1f5f9; }
        .total-section { text-align: right; }
        .total-row { display: flex; justify-content: flex-end; gap: 20px; align-items: center; margin-top: 10px; }
        .total-label { font-weight: 600; color: #64748b; }
        .total-amount { font-size: 24px; font-weight: 900; color: #6366f1; }
        .footer { text-align: center; margin-top: 60px; color: #94a3b8; font-size: 14px; }
        @media print {
            body { padding: 0; }
            .invoice-box { border: none; box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="max-width: 800px; margin: 0 auto 20px; text-align: right;">
        <button onclick="window.print()" style="background: #6366f1; color: white; border: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer;">IN HÓA ĐƠN</button>
        <button onclick="window.close()" style="background: #e2e8f0; border: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer; margin-left: 10px;">ĐÓNG</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="logo">ShoeStore.</div>
            <div style="text-align: right;">
                <p style="margin: 0; font-weight: 900; font-size: 20px;">HÓA ĐƠN BÁN HÀNG</p>
                <p style="margin: 0; color: #64748b;">Mã đơn: #<?= $order_id ?></p>
                <p style="margin: 0; color: #64748b;">Ngày: <?= date('d/m/Y', strtotime($order['created_at'])) ?></p>
            </div>
        </div>

        <div class="info">
            <div>
                <h3>Thông tin cửa hàng</h3>
                <p>ShoeStore. Sneaker Premium</p>
                <p>123 Đường ABC, Quận 1, TP.HCM</p>
                <p>Hotline: 0123.456.789</p>
            </div>
            <div>
                <h3>Thông tin khách hàng</h3>
                <p><?= htmlspecialchars($order['shipping_name']) ?></p>
                <p><?= htmlspecialchars($order['shipping_phone']) ?></p>
                <p><?= htmlspecialchars($order['shipping_address']) ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th style="text-align: center;">Số lượng</th>
                    <th style="text-align: right;">Đơn giá</th>
                    <th style="text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td style="font-weight: 600;"><?= htmlspecialchars($item['name']) ?></td>
                    <td style="text-align: center;"><?= $item['quantity'] ?></td>
                    <td style="text-align: right;"><?= format_price($item['price']) ?></td>
                    <td style="text-align: right; font-weight: 700;"><?= format_price($item['price'] * $item['quantity']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span class="total-label">Tổng cộng:</span>
                <span class="total-amount"><?= format_price($order['total_amount']) ?></span>
            </div>
        </div>

        <div class="footer">
            <p>Cảm ơn quý khách đã mua sắm tại ShoeStore!</p>
            <p>Vui lòng giữ lại hóa đơn này để được hỗ trợ bảo hành (nếu có).</p>
        </div>
    </div>

    <script>
        // Tự động mở hộp thoại in khi trang được tải xong (tùy chọn)
        // window.onload = () => { window.print(); }
    </script>
</body>
</html>
