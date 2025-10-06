<?php
    // FIX: แก้ไข Path ให้ชี้ไปที่ Root ของเว็บไซต์โดยใช้ DOCUMENT_ROOT
    // สมมติว่า config.php อยู่ที่รากของโปรเจกต์ (เช่น /051/config.php)
    require_once $_SERVER['DOCUMENT_ROOT'] . '../config.php';
    
    // หากต้องการใช้การตรวจสอบสิทธิ์ Admin อีกครั้ง ให้เพิ่มบรรทัดนี้: 
    // require_once 'auth_admin.php'; 

    // ตรวจสอบว่าได้ส่ง id สินค้ามาหรือไม่
    if (!isset($_GET['id'])) {
        header("Location: products.php");
        exit;
    }

    $product_id = $_GET['id'];
    
    // ดึงข้อมูลสินค้า
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "<h3>ไม่พบข้อมูลสินค้า</h3>";
        exit;
    }

    // ดึงหมวดหมู่ทั้งหมด
    $categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
    
    // เมื่อมีการส่งฟอร์ม
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['product_name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $category_id = (int)$_POST['category_id'];
        
        // ค่ารูปเดิมจากฟอร์ม
        $oldImage = $_POST['old_image'] ?? null;
        $removeImage = isset($_POST['remove_image']); // true/false

        if ($name && $price > 0) {
            // เตรียมตัวแปรรูปที่จะบันทึก
            $newImageName = $oldImage; // default: คงรูปเดิมไว้
            
            // 1) ถ้ามีติ๊ก "ลบรูปเดิม" → ตั้งให้เป็น null
            if ($removeImage) {
                $newImageName = null;
            }

            // 2) ถ้ามีอัปโหลดไฟล์ใหม่ → ตรวจแล้วเซฟไฟล์และตั้งชื่อใหม่ทับค่า
            if (!empty($_FILES['product_image']['name'])) {
                $file = $_FILES['product_image'];
                // ตรวจชนิดไฟล์แบบง่าย (แนะนำ: ตรวจ MIME จริงด้วย finfo)
                $allowed = ['image/jpeg', 'image/png'];
                if (in_array($file['type'], $allowed, true) && $file['error'] === UPLOAD_ERR_OK) {
                    // สร้างชื่อไฟล์ใหม่
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $newImageName = 'product_' . time() . '.' . $ext;
                    
                    // FIX: ใช้ DOCUMENT_ROOT เพื่อให้ Path สำหรับเซฟรูปถูกต้อง
                    $uploadDir = realpath($_SERVER['DOCUMENT_ROOT'] . '/product_images'); 
                    $destPath = $uploadDir . DIRECTORY_SEPARATOR . $newImageName;
                    
                    // ย้ายไฟล์อัปโหลด
                    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                        // ถ้าไฟล์ย้ายไม่ได้ อาจตั้ง flash message แล้วคงใช้รูปเดิมไว้
                        $newImageName = $oldImage;
                    }
                }
            }

            // อัปเดต DB
            $sql = "UPDATE products
            SET product_name = ?, description = ?, price = ?, stock = ?, category_id = ?, image = ?
            WHERE product_id = ?";
            $args = [$name, $description, $price, $stock, $category_id, $newImageName, $product_id];
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($args);

            // ลบไฟล์เก่าในดิสก์ ถ้า:
            // - มีรูปเดิม ($oldImage) และ
            // - เกิดการเปลี่ยนรูป (อัปโหลดใหม่หรือสั่งลบรูปเดิม)
            if (!empty($oldImage) && $oldImage !== $newImageName) {
                // FIX: ใช้ DOCUMENT_ROOT สำหรับการลบรูปด้วย
                $baseDir = realpath($_SERVER['DOCUMENT_ROOT'] . '/product_images');
                $filePath = realpath($baseDir . DIRECTORY_SEPARATOR . $oldImage);
                // ตรวจสอบความปลอดภัยก่อนลบไฟล์จริง
                if ($filePath && strpos($filePath, $baseDir) === 0 && is_file($filePath)) {
                    @unlink($filePath);
                }
            }
            
            // ส่งกลับไปยังหน้า products.php
            header("Location: products.php");
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* พื้นหลังสีชมพูอ่อนมากสำหรับหน้า Admin */
            background-color: #fce4ec; /* light pink */
            min-height: 100vh;
            padding-bottom: 50px;
        }
        .container {
            max-width: 900px;
        }
        .card-form {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(255, 0, 106, 0.1); /* เงาสีชมพูอ่อน */
            padding: 30px;
            margin-top: 20px;
        }
        .btn-primary {
            /* ปุ่มหลักสีชมพูเข้ม */
            background-color: #ff006aff;
            border-color: #ff006aff;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #cc0055;
            border-color: #cc0055;
        }
        .img-preview {
            border: 1px solid #ff006aff;
            object-fit: cover;
            background-color: #fff;
        }
        .btn-secondary {
             /* ปุ่มย้อนกลับสีชมพูอ่อน */
            background-color: #fc7eb3ff;
            border-color: #fc7eb3ff;
            color: white;
        }
    </style>
</head>
<body class="container mt-4">
    
    <a href="products.php" class="btn btn-secondary mb-3">← กลับไปยังรายการสินค้า</a>

    <div class="card-form">
        <h2 class="mb-4 text-center" style="color: #ff006aff;">แก้ไขสินค้า</h2>
        
        <!-- Note: product_id ต้องถูกใช้เพื่ออัปเดตข้อมูล -->
        <form method="post" enctype="multipart/form-data" class="row g-4">
            <!-- Hidden input สำหรับเก็บ ID ของสินค้าที่กำลังแก้ไข -->
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">

            <div class="col-md-6">
                <label class="form-label fw-bold">ชื่อสินค้า</label>
                <input type="text" name="product_name" class="form-control" value="<?= htmlspecialchars($product['product_name']) ?>" required>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-bold">ราคา (บาท)</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($product['price']) ?>" required>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-bold">จำนวนในคลัง</label>
                <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($product['stock']) ?>" required>
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-bold">หมวดหมู่</label>
                <select name="category_id" class="form-select" required>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['category_id']) ?>" 
                        <?= ($product['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-bold">รายละเอียดสินค้า</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>

            <!-- === แสดงรูปเดิม + เก็บค่าเก่า === -->
            <div class="col-md-6">
                <label class="form-label d-block fw-bold">รูปปัจจุบัน</label>
                <?php if (!empty($product['image'])): ?>
                <!-- Path สำหรับแสดงรูปภาพยังใช้ ../product_images/ เหมือนเดิม เพราะเป็น Relative Path สำหรับเบราว์เซอร์ -->
                <img src="../product_images/<?= htmlspecialchars($product['image']) ?>"
                    width="120" height="120" class="rounded img-preview mb-2">
                <?php else: ?>
                <span class="text-muted d-block mb-2">ไม่มีรูปภาพ</span>
                <?php endif; ?>
                
                <!-- Input ซ่อนสำหรับเก็บชื่อรูปเดิม -->
                <input type="hidden" name="old_image" value="<?= htmlspecialchars($product['image']) ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">อัปโหลดรูปใหม่ (jpg, png)</label>
                <input type="file" name="product_image" class="form-control">
                
                <?php if (!empty($product['image'])): ?>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                    <label class="form-check-label text-danger" for="remove_image">ลบรูปเดิม</label>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary w-100 py-2">บันทึกการแก้ไขสินค้า</button>
            </div>
        </form>
    </div>
</body>
</html>