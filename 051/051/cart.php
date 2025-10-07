<?php
session_start();
// ตรวจสอบให้แน่ใจว่าไฟล์ config.php ถูกเรียกใช้โดยใช้ require
require 'config.php'; 

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit;
}

// กำหนด user_id ที่ล็อกอินอยู่
$user_id = $_SESSION['user_id']; 

// -----------------------------
// เพิ่มสินค้าเข้าตะกร้า (ต้องอยู่ก่อนการดึงรายการตะกร้า)
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) { 
    // ใช้ $conn แทน $pdo ตามที่คุณใช้ในส่วนอื่น
    $product_id = $_POST['product_id']; 
    $quantity = max(1, intval($_POST['quantity'] ?? 1));

    // ตรวจสอบว่าสินค้านั้นอยู่ในตะกร้าแล้วหรือยัง
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        // ถ้ามีแล้ว ให้เพิ่มจ านวน
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE cart_id = ?");
        $stmt->execute([$quantity, $item['cart_id']]);
    } else {
        // ถ้ายังไม่มี ให้เพิ่มใหม่
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
    header("Location: cart.php"); // กลับมาที่ cart
    exit;
}

// -----------------------------
// ลบสินค้าออกจากตะกร้า (ต้องอยู่ก่อนการดึงรายการตะกร้า)
// -----------------------------
if (isset($_GET['remove'])) {
    $cart_id = $_GET['remove'];
    // ต้องตรวจสอบตัวแปร $conn หรือ $pdo ที่ใช้ในการเชื่อมต่อ
    // จากโค้ดของคุณ ผมเห็นคุณใช้ $conn เป็นหลัก ดังนั้นจึงใช้ $conn ในที่นี้
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?"); 
    $stmt->execute([$cart_id, $user_id]);
    header("Location: cart.php"); // กลับมาที่ cart
    exit;
}

// -----------------------------
// ดึงรายการสินค้าในตะกร้า (ต้องอยู่ก่อนการค านวณ)
// -----------------------------
$stmt = $conn->prepare("SELECT cart.cart_id, cart.quantity, products.product_name, products.price
                             FROM cart
                             JOIN products ON cart.product_id = products.product_id
                             WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);


// -----------------------------
// ค านวณราคารวม (ต้องอยู่หลังการดึงรายการสินค้า)
// -----------------------------
$total = 0;
foreach ($items as $item) {
    $total += $item['quantity'] * $item['price'];
}


?>


<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>ตะกร้าสินค้า</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* แก้ไขปัญหากด confirm ใน iframe ไม่ได้ */
.btn-danger-custom {
    cursor: pointer;
}
</style>
</head>
<body class="container mt-4">

<!-- Modal สำหรับยืนยันการลบ (แทน alert/confirm) -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">ยืนยันการลบสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                คุณต้องการลบสินค้านี้ออกจากตะกร้าหรือไม่?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <a id="deleteConfirmLink" class="btn btn-danger">ยืนยันการลบ</a>
            </div>
        </div>
    </div>
</div>

<h2>ตะกร้าสินค้า</h2>
<!-- แก้ไข TODO: หน้ำ index -->
<a href="index.php" class="btn btn-secondary mb-3">← กลับไปเลือกสินค้า</a> 

<?php if (count($items) === 0): ?>
<!-- แก้ไข TODO: ข้อความกรณีตะกร้าว่าง -->
<div class="alert alert-warning">ยังไม่มีสินค้าในตะกร้า</div> 
<?php else: ?>
<table class="table table-bordered">
<thead>
<tr>
<th>ชื่อสินค้า</th>
<th>จำนวน</th>
<th>ราคาต่อหน่วย</th>
<th>ราคารวม</th>
<th>จัดการ</th>
</tr>
</thead>
<tbody>
<?php foreach ($items as $item): ?>
<tr>
<td><?= htmlspecialchars($item['product_name']) ?></td> 
<td><?= $item['quantity'] ?></td> 
<td><?= number_format($item['price'], 2) ?></td> 
<td><?= number_format($item['price'] * $item['quantity'], 2) ?></td> 
<td>
    <!-- แก้ไข: ใช้ Modal แทน Confirm() เพราะ Confirm() จะไม่ทำงานใน iframe -->
    <button type="button" 
            class="btn btn-sm btn-danger btn-danger-custom"
            data-bs-toggle="modal" 
            data-bs-target="#confirmDeleteModal" 
            data-cart-id="<?= $item['cart_id'] ?>">
        ลบ
    </button>
</td>
</tr>
<?php endforeach; ?>
<tr>
<td colspan="3" class="text-end"><strong>รวมทั้งหมด:</strong></td>
<td colspan="2"><strong><?= number_format($total, 2) ?> บาท</strong></td>
</tr>
</tbody>
</table>
<!-- แก้ไข TODO: checkout -->
<a href="checkout.php" class="btn btn-success">สั่งซื้อสินค้า</a> 
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // สคริปต์ JavaScript เพื่อจัดการ Modal ยืนยันการลบ
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = document.getElementById('confirmDeleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            // ดึงปุ่มที่กดลบ
            const button = event.relatedTarget;
            // ดึง cart_id จากปุ่ม
            const cartId = button.getAttribute('data-cart-id');
            // กำหนดลิงก์สำหรับปุ่ม "ยืนยันการลบ" ใน Modal
            const deleteConfirmLink = deleteModal.querySelector('#deleteConfirmLink');
            deleteConfirmLink.href = 'cart.php?remove=' + cartId;
        });
    });
</script>

</body>
</html>
