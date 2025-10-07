<?php
session_start();
    require 'config.php';
    require_once 'function.php';
    // ตรวจสอบวำ่ ผใู้ชล้็อกอนิ แลว้หรอื ยัง
    if (!isset($_SESSION['user_id'])) { // TODO: ใสช่ อื่ ตัวแปร session เก็บ user id
    header("Location: login.php"); // TODO: ใสห่ นำ้ทใี่ ช ้login
    exit;
    }
// เก็บ user_id
$user_id = $_SESSION['user_id']; // ตัวแปรเก็บ user_id
// -----------------------------
// ดงึค ำสั่งซอื้ ของผใู้ช ้
// -----------------------------
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC"); // orders

$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
// -----------------------------
// ฟังกช์ นั ดงึรำยกำรสนิ คำ้ในค ำสั่งซอื้
// -----------------------------

?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ประวัติการสั่งซื้อ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h2>ประวัติการสั่งซื้อ</h2>
<a href="index.php" class="btn btn-secondary mb-3">← กลับหน้าหลัก</a>
<?php if (isset($_GET['order_id'])): ?>
<div class="alert alert-success">ทำรายการสั่งซื้อเรียบร้ยแล้ว</div>
<?php endif; ?>
<?php if (count($orders) === 0): ?>
<div class="alert alert-warning">คุณยังไม่เคยสั่งซื้อสินค้า</div>
<?php else: ?>
<?php foreach ($orders as $order): ?>
<div class="card mb-4">
<div class="card-header bg-light">
<strong>รหัสคำสั่งซื้อ :</strong> #<?= $order['order_id'] ?> |
<strong>วันที่:</strong> <?= $order['order_date'] ?> |
<strong>สถานะ:</strong> <?= ucfirst($order['status']) ?>
</div>
<div class="card-body">
<ul class="list-group mb-3">
<?php foreach (getOrderItems($conn, $order['order_id']) as $item): ?>
<li class="list-group-item">
<?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?> = <?=
number_format($item['quantity'] * $item['price'], 2) ?> บาทท
</li>
<?php endforeach; ?>
</ul>
<p><strong>รวมทั้งสิ้น:</strong> <?= number_format($order['total_amount'], 2) ?> บาท</p>
<?php $shipping = getShippingInfo($conn, $order['order_id']); ?>
<?php if ($shipping): ?>
<p><strong>ที่อยู่จัดส่ง :</strong> <?= htmlspecialchars($shipping['address']) ?>, <?=
htmlspecialchars($shipping['city']) ?> <?= $shipping['postal_code'] ?></p>
<p><strong>สถานะการจัดส่ง :</strong> <?= ucfirst($shipping['shipping_status']) ?></p>
<p><strong>เบอร์โทร:</strong> <?= htmlspecialchars($shipping['phone']) ?></p>
<?php endif; ?>
</div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</body>
</html>