<?php
include 'config.php';
check_admin();

$action = $_GET['action'] ?? '';
$upload_dir = 'public/images/';

// Hàm xử lý upload ảnh
function handle_image_upload($file) {
    global $upload_dir;
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('prod_', true) . '.' . $ext;
        $target = $upload_dir . $filename;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return '/images/' . $filename;
        }
    }
    return null;
}

if ($action === 'add') {
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, brand_id, category_id, price, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['brand_id'],
            $_POST['category_id'],
            $_POST['price'],
            $_POST['description']
        ]);
        $product_id = $pdo->lastInsertId();

        // Xử lý ảnh chính
        if (!empty($_FILES['image']['name'])) {
            $image_url = handle_image_upload($_FILES['image']);
            if ($image_url) {
                $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, 1)");
                $stmt->execute([$product_id, $image_url]);
            }
        }

        // Xử lý ảnh liên quan
        if (isset($_FILES['gallery'])) {
            for ($i = 0; $i < count($_FILES['gallery']['name']); $i++) {
                if ($_FILES['gallery']['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $_FILES['gallery']['name'][$i],
                        'type' => $_FILES['gallery']['type'][$i],
                        'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                        'error' => $_FILES['gallery']['error'][$i],
                        'size' => $_FILES['gallery']['size'][$i]
                    ];
                    $image_url = handle_image_upload($file);
                    if ($image_url) {
                        $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, 0)");
                        $stmt->execute([$product_id, $image_url]);
                    }
                }
            }
        }
        
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Lỗi: " . $e->getMessage());
    }
    redirect('admin_products.php');
}

if ($action === 'edit') {
    $id = (int)$_POST['id'];
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, brand_id = ?, category_id = ?, price = ?, description = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['brand_id'],
            $_POST['category_id'],
            $_POST['price'],
            $_POST['description'],
            $id
        ]);

        // Xử lý ảnh chính
        if (!empty($_FILES['image']['name'])) {
            $stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND is_primary = 1");
            $stmt->execute([$id]);
            $old_img = $stmt->fetch();

            $image_url = handle_image_upload($_FILES['image']);
            if ($image_url) {
                if ($old_img) {
                    $old_file = 'public' . $old_img['image_url'];
                    if (file_exists($old_file)) unlink($old_file);
                    $stmt = $pdo->prepare("UPDATE product_images SET image_url = ? WHERE product_id = ? AND is_primary = 1");
                    $stmt->execute([$image_url, $id]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, 1)");
                    $stmt->execute([$id, $image_url]);
                }
            }
        }

        // Xử lý ảnh liên quan
        if (isset($_FILES['gallery'])) {
            // Lấy danh sách ảnh phụ hiện có
            $stmt = $pdo->prepare("SELECT id, image_url FROM product_images WHERE product_id = ? AND is_primary = 0 ORDER BY id ASC");
            $stmt->execute([$id]);
            $current_gallery = $stmt->fetchAll();

            for ($i = 0; $i < count($_FILES['gallery']['name']); $i++) {
                if ($_FILES['gallery']['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $_FILES['gallery']['name'][$i],
                        'type' => $_FILES['gallery']['type'][$i],
                        'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                        'error' => $_FILES['gallery']['error'][$i],
                        'size' => $_FILES['gallery']['size'][$i]
                    ];
                    $image_url = handle_image_upload($file);
                    if ($image_url) {
                        // Nếu đã có ảnh ở vị trí này thì thay thế
                        if (isset($current_gallery[$i])) {
                            $old_file = 'public' . $current_gallery[$i]['image_url'];
                            if (file_exists($old_file)) unlink($old_file);
                            $stmt = $pdo->prepare("UPDATE product_images SET image_url = ? WHERE id = ?");
                            $stmt->execute([$image_url, $current_gallery[$i]['id']]);
                        } else {
                            // Nếu chưa có thì thêm mới
                            $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, 0)");
                            $stmt->execute([$id, $image_url]);
                        }
                    }
                }
            }
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Lỗi: " . $e->getMessage());
    }
    redirect('admin_products.php');
}

if ($action === 'delete') {
    $id = (int)$_GET['id'];
    
    // Lấy danh sách ảnh để xóa file
    $stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
    $stmt->execute([$id]);
    $images = $stmt->fetchAll();
    
    foreach ($images as $img) {
        $file_path = 'public' . $img['image_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Xóa sản phẩm (DB sẽ tự xóa product_images do ON DELETE CASCADE)
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    redirect('admin_products.php');
}

redirect('admin_products.php');
?>
