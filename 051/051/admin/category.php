<?php
// เชื่อมต่อฐานข้อมูลและเรียก Admin Guard
require '../config.php';
require 'auth_admin.php';

// Auth Guard: ตรวจสอบสิทธิ์ Admin 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// เพิ่มหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    if ($category_name) {
        $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)"); 
        $stmt->execute([$category_name]);
        $_SESSION['success'] = "เพิ่มหมวดหมู่เรียบร้อยแล้ว";
        header("Location: category.php");
        exit;
    }
}

// ลบหมวดหมู่
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];
    // ตรวจสอบว่าหมวดหมู่นี้ยังถูกใช้โดยสินค้าหรือไม่
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $productCount = $stmt->fetchColumn();

    if ($productCount > 0) {
        // ถ้ามีสินค้าอยู่ในหมวดหมู่นี้
        $_SESSION['error'] = "ไม่สามารถลบหมวดหมู่นี้ได้ เนื่องจากยังมีสินค้าที่ใช้งานหมวดหมู่นี้อยู่";
    } else {
        // ถ้าไม่มีสินค้า ให้ลบได้
        $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $_SESSION['success'] = "ลบหมวดหมู่เรียบร้อยแล้ว";
    }
    header("Location: category.php");
    exit;
}

// แก้ไขหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = trim($_POST['new_name']);
    if ($category_name) {
        $stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
        $stmt->execute([$category_name, $category_id]);
        $_SESSION['success'] = "แก้ไขหมวดหมู่เรียบร้อยแล้ว";
        header("Location: category.php");
        exit;
    }
}

// ดึงหมวดหมู่ทั้งหมด
$categories = $conn->query("SELECT * FROM categories ORDER BY category_id ASC")->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการหมวดหมู่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* CSS สำหรับพื้นหลังสีชมพูตามสไตล์ของคุณ */
        body {
            background: linear-gradient(135deg, #ff006aff, #ed2779ff); /* สีชมพู-แดง gradient */
            background-size: 400% 400%;
            animation: gradient-animation 15s ease infinite;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        @keyframes gradient-animation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .container {
            max-width: 800px;
        }
        .card, .table {
            background: rgba(255, 255, 255, 0.95); /* ให้พื้นหลังส่วนเนื้อหาโปร่งแสงเล็กน้อย */
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .table {
             margin-top: 15px;
        }
    </style>
</head>
<body class="container mt-4">
    <div class="card p-4 shadow-lg">
        <h2 class="card-title text-center mb-4"><i class="fas fa-tags me-2"></i>จัดการหมวดหมู่สินค้า</h2>
        
        <a href="index.php" class="btn btn-secondary mb-3">← กลับหน้าผู้ดูแล</a>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="card p-3 mb-4 shadow-sm">
            <h5 class="card-title">เพิ่มหมวดหมู่ใหม่</h5>
            <form method="post" class="row g-3">
                <div class="col-md-9">
                    <input type="text" name="category_name" class="form-control" placeholder="ชื่อหมวดหมู่ใหม่" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" name="add_category" class="btn btn-primary w-100"><i class="fas fa-plus me-1"></i>เพิ่มหมวดหมู่</button>
                </div>
            </form>
        </div>

        <h5>รายการหมวดหมู่</h5>
        <table class="table table-bordered bg-white shadow-sm">
            <thead>
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 45%;">ชื่อหมวดหมู่</th>
                    <th style="width: 25%;">แก้ไขชื่อ</th>
                    <th style="width: 20%;">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= htmlspecialchars($cat['category_id']) ?></td>
                    <td><?= htmlspecialchars($cat['category_name']) ?></td>
                    <td>
                        <form method="post" class="d-flex">
                            <input type="hidden" name="category_id" value="<?= $cat['category_id'] ?>">
                            <input type="text" name="new_name" class="form-control me-2 form-control-sm" placeholder="ชื่อใหม่" required>
                            <button type="submit" name="update_category" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                        </form>
                    </td>
                    <td>
                        <a href="category.php?delete=<?= $cat['category_id'] ?>" class="btn btn-sm btn-danger w-100"
                        onclick="return confirm('คุณต้องการลบหมวดหมู่นี้หรือไม่?')"><i class="fas fa-trash-alt me-1"></i>ลบ</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted">ไม่พบรายการหมวดหมู่</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>