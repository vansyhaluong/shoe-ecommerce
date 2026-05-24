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
    $name = sanitize_text($_POST['name'] ?? '');
    $brand_id = (int)($_POST['brand_id'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price = $_POST['price'] ?? '';
    $description = sanitize_text($_POST['description'] ?? '');

    $errors = [];
    if (!validate_string_len($name, 3, 255)) {
        $errors[] = 'Tên sản phẩm phải từ 3 đến 255 ký tự!';
    }
    if (!db_record_exists('brands', 'id', $brand_id)) {
        $errors[] = 'Thương hiệu không hợp lệ hoặc không tồn tại!';
    }
    if (!db_record_exists('categories', 'id', $category_id)) {
        $errors[] = 'Danh mục không hợp lệ hoặc không tồn tại!';
    }
    if (!validate_price($price)) {
        $errors[] = 'Giá sản phẩm phải là số dương hợp lệ (từ 1đ đến 100.000.000đ)!';
    }

    // Image validations
    if (!empty($_FILES['image']['name'])) {
        $img_check = validate_image_file($_FILES['image']);
        if (!$img_check['valid']) {
            $errors[] = 'Hình ảnh chính: ' . $img_check['error'];
        }
    }

    // Gallery images validations
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
                $img_check = validate_image_file($file);
                if (!$img_check['valid']) {
                    $errors[] = "Hình ảnh phụ " . ($i + 1) . ": " . $img_check['error'];
                }
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['validation_errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        redirect('admin_product_form.php');
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, brand_id, category_id, price, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $name,
            $brand_id,
            $category_id,
            $price,
            $description
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
        $_SESSION['validation_errors'] = ['Lỗi hệ thống khi lưu sản phẩm. Vui lòng thử lại sau!'];
        $_SESSION['old_input'] = $_POST;
        redirect('admin_product_form.php');
    }
    redirect('admin_products.php');
}

if ($action === 'edit') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0 || !db_record_exists('products', 'id', $id)) {
        die('Sản phẩm không tồn tại!');
    }

    $name = sanitize_text($_POST['name'] ?? '');
    $brand_id = (int)($_POST['brand_id'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price = $_POST['price'] ?? '';
    $description = sanitize_text($_POST['description'] ?? '');

    $errors = [];
    if (!validate_string_len($name, 3, 255)) {
        $errors[] = 'Tên sản phẩm phải từ 3 đến 255 ký tự!';
    }
    if (!db_record_exists('brands', 'id', $brand_id)) {
        $errors[] = 'Thương hiệu không hợp lệ hoặc không tồn tại!';
    }
    if (!db_record_exists('categories', 'id', $category_id)) {
        $errors[] = 'Danh mục không hợp lệ hoặc không tồn tại!';
    }
    if (!validate_price($price)) {
        $errors[] = 'Giá sản phẩm phải là số dương hợp lệ (từ 1đ đến 100.000.000đ)!';
    }

    // Image validations
    if (!empty($_FILES['image']['name'])) {
        $img_check = validate_image_file($_FILES['image']);
        if (!$img_check['valid']) {
            $errors[] = 'Hình ảnh chính: ' . $img_check['error'];
        }
    }

    // Gallery images validations
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
                $img_check = validate_image_file($file);
                if (!$img_check['valid']) {
                    $errors[] = "Hình ảnh phụ " . ($i + 1) . ": " . $img_check['error'];
                }
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['validation_errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        redirect('admin_product_form.php?id=' . $id);
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, brand_id = ?, category_id = ?, price = ?, description = ? WHERE id = ?");
        $stmt->execute([
            $name,
            $brand_id,
            $category_id,
            $price,
            $description,
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
        $_SESSION['validation_errors'] = ['Lỗi hệ thống khi cập nhật sản phẩm. Vui lòng thử lại sau!'];
        $_SESSION['old_input'] = $_POST;
        redirect('admin_product_form.php?id=' . $id);
    }
    redirect('admin_products.php');
}

if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0 || !db_record_exists('products', 'id', $id)) {
        die('Sản phẩm không tồn tại!');
    }
    
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
