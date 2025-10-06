<?php
    session_start();
    // ตรวจสอบการเชื่อมต่อฐานข้อมูล
    require_once 'config.php';
    $isLoggedIn = isset($_SESSION['user_id']);
    
    // ตรวจสอบว่ามี product ID ถูกส่งมาหรือไม่
    if(!isset($_GET['id'])){
        header('Location: index.php');
        exit();
    }
    
    $product_id = $_GET['id'];
    
    // ดึงข้อมูลสินค้ารวมถึงคอลัมน์ 'image'
    $stmt = $conn->prepare("SELECT p.*, c.category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // ถ้าไม่พบสินค้าให้กลับไปหน้าหลัก
    if (!$product) {
        header('Location: index.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดสินค้า: <?= htmlspecialchars($product['product_name'])?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* พื้นหลังสีชมพู */
            background-color: #ff006aff; 
            min-height: 100vh;
        }
        .card-product-detail {
            /* ปรับสไตล์ Card ให้เด่นขึ้น */
            border-radius: 15px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            padding: 20px;
        }
        .product-image-container {
            /* จัดการรูปภาพ */
            padding: 10px;
            text-align: center;
        }
        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .price-text {
            color: #ff006aff; /* สีชมพูสำหรับราคา */
            font-size: 2rem;
            font-weight: bold;
        }
        .btn-success {
            /* ปุ่มเพิ่มในตะกร้าสีเขียว */
            background-color: #ff006aff; 
            border-color: #ff006aff;
            transition: background-color 0.3s;
        }
        .btn-success:hover {
            background-color: #cc0055;
            border-color: #cc0055;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <a href="index.php" class="btn btn-light mb-4">← กลับหน้ารายการสินค้า</a>
        
        <div class="card card-product-detail">
            <div class="card-body">
                <div class="row">
                    <!-- ส่วนแสดงรูปภาพสินค้า (ซ้ายมือ) -->
                    <div class="col-md-5 product-image-container">
                        <?php 
                        // ตรวจสอบ path รูปภาพ (สมมติว่ารูปภาพอยู่ในโฟลเดอร์ product_images/)
                        $image_path = 'product_images/' . htmlspecialchars($product['image']); 
                        $placeholder_url = "https://placehold.co/400x400/CCCCCC/333333?text=Product+Image";
                        ?>
                        <img src="<?= $image_path ?>" 
                             onerror="this.onerror=null;this.src='<?= $placeholder_url ?>';"
                             class="product-image" 
                             alt="<?= htmlspecialchars($product['product_name']) ?>">
                    </div>

                    <!-- ส่วนแสดงรายละเอียดสินค้า (ขวามือ) -->
                    <div class="col-md-7">
                        <h1 class="card-title mb-1"><?= htmlspecialchars($product['product_name'])?></h1>
                        <h5 class="text-muted mb-4">หมวดหมู่: <?= htmlspecialchars($product['category_name'])?></h5>

                        <div class="mb-4">
                            <h4 class="price-text"><?= number_format($product['price'], 2)?> บาท</h4>
                            <p class="text-secondary">คงเหลือในคลัง: <?= htmlspecialchars($product['stock'])?> ชิ้น</p>
                        </div>

                        <!-- คำอธิบายสินค้า -->
                        <div class="mb-4">
                            <h6>รายละเอียดสินค้า:</h6>
                            <p class="card-text text-break"><?= nl2br(htmlspecialchars($product['description'] ?? 'ไม่มีคำอธิบายสินค้า'))?></p>
                        </div>

                        <!-- ส่วนสั่งซื้อ -->
                        <?php if ($isLoggedIn): ?>
                        <form action="cart.php" method="post" class="mt-4 d-flex align-items-center">
                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                            
                            <label for="quantity" class="me-3 fw-bold">จำนวน:</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?=
                            htmlspecialchars($product['stock']) ?>" class="form-control w-auto me-3" style="max-width: 80px;" required>
                            
                            <button type="submit" class="btn btn-success flex-grow-1">เพิ่มในตะกร้า</button>
                        </form>
                        <?php else: ?>
                        <div class="alert alert-danger mt-4" role="alert">
                            <a href="login.php" class="alert-link fw-bold">กรุณาเข้าสู่ระบบ</a> เพื่อทำการสั่งซื้อสินค้า
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>