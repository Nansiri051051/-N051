<?php
session_start();
require 'config.php';

// ตัวแปรสำหรับเก็บข้อผิดพลาด
$errors = [];
$total = 0; // กำหนดค่าเริ่มต้นให้กับ $total เพื่อป้องกัน Warning

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // หน้า login
    exit;
}
$user_id = $_SESSION['user_id']; 

// ดึงรายการสินค้าในตะกร้า
$stmt = $conn->prepare("SELECT cart.cart_id, cart.quantity, cart.product_id, 
                             products.product_name, products.price
                         FROM cart
                         JOIN products ON cart.product_id = products.product_id
                         WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ตรวจสอบตะกร้าว่าง
if (count($items) === 0) {
    // ถ้าตะกร้าว่าง ให้ส่งกลับไปหน้าตะกร้า
    header("Location: cart.php");
    exit;
}

// คำนวณราคารวมทั้งหมด (แก้ไข Undefined variable $total)
foreach ($items as $item) {
    $total += $item['quantity'] * $item['price'];
}


// -----------------------------
// ประมวลผลฟอร์มการสั่งซื้อ (เมื่อกดยืนยัน)
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($address) || empty($city) || empty($postal_code) || empty($phone)) {
        $errors[] = "กรุณากรอกข้อมูลการจัดส่งให้ครบถ้วน";
    }

    if (empty($errors)) {
        try {
            // 1. เริ่ม Transaction เพื่อให้การบันทึกข้อมูลเป็นชุดเดียวกัน
            $conn->beginTransaction();

            // 2. บันทึกข้อมูลการสั่งซื้อลงในตาราง orders
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, city, postal_code, phone, status) 
                                    VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
            $stmt->execute([$user_id, $total, $address, $city, $postal_code, $phone]);
            $order_id = $conn->lastInsertId();

            // 3. บันทึกรายการสินค้าที่สั่งซื้อลงในตาราง order_items
            $order_items_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt_items = $conn->prepare($order_items_sql);

            foreach ($items as $item) {
                $stmt_items->execute([
                    $order_id, 
                    $item['product_id'], 
                    $item['quantity'], 
                    $item['price'] 
                ]);
            }

            // 4. ลบสินค้าออกจากตะกร้าหลังจากสั่งซื้อสำเร็จ
            $stmt_clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt_clear_cart->execute([$user_id]);

            // 5. Commit Transaction
            $conn->commit();

            // ส่งผู้ใช้ไปยังหน้าสำเร็จ
            header("Location: order_success.php?order_id=" . $order_id);
            exit;

        } catch (PDOException $e) {
            // Rollback หากมีข้อผิดพลาด
            $conn->rollBack();
            $errors[] = "เกิดข้อผิดพลาดในการทำรายการสั่งซื้อ: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>สั่งซื้อสินค้า</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h2>ยืนยันการสั่งซื้อ</h2>

<?php if (count($items) === 0): ?>
    <div class="alert alert-danger">ตะกร้าสินค้าว่างเปล่า กรุณาเพิ่มสินค้าก่อนทำการสั่งซื้อ</div>
    <a href="cart.php" class="btn btn-secondary">← กลับตะกร้า</a>
<?php else: ?>
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
    <ul>
    <?php foreach ($errors as $e): ?>
    <li><?= htmlspecialchars($e) ?></li>
    <?php endforeach; ?>
    </ul>
    </div>
    <?php endif; ?>
    
    <!-- แสดงรายการสินค้าในตะกร้า -->
    <h5>รายการสินค้าในตะกร้า</h5>
    <ul class="list-group mb-4">
    <?php foreach ($items as $item): ?>
    <li class="list-group-item">
    <?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?> = <?=
    number_format($item['price'] * $item['quantity'], 2) ?> บาท
    </li>
    <?php endforeach; ?>
    <!-- แก้ไข $total ที่เป็น Undefined Variable -->
    <li class="list-group-item text-end"><strong>รวมทั้งสิ้น : <?= number_format($total, 2) ?> บาท</strong></li>
    </ul>
    
    <!-- ฟอร์มกรอกข้อมูลการจัดส่ง -->
    <form method="post" class="row g-3">
    <div class="col-md-6">
    <label for="address" class="form-label">ที่อยู่</label>
    <input type="text" name="address" id="address" class="form-control" required>
    </div>
    <div class="col-md-4">
    <label for="city" class="form-label">จังหวัด</label>
    <input type="text" name="city" id="city" class="form-control" required>
    </div>
    <div class="col-md-2">
    <label for="postal_code" class="form-label">รหัสไปรษณีย์</label>
    <input type="text" name="postal_code" id="postal_code" class="form-control" required>
    </div>
    <div class="col-md-6">
    <label for="phone" class="form-label">เบอร์โทรศัพท์</label> 
    <input type="text" name="phone" id="phone" class="form-control" required>
    </div>
    <div class="col-12">
    <button type="submit" class="btn btn-success">ยืนยันการสั่งซื้อ</button>
    <a href="cart.php" class="btn btn-secondary">← กลับตะกร้า</a> <!-- หน้า cart -->
    </div>
    </form>
<?php endif; ?>
</body>
</html>
