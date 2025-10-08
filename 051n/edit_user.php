<?php
session_start();
require_once 'config.php'; 

if (!isset($_GET['no'])) {
    header('Location: index.php?error=' . urlencode('ไม่พบรหัสนักศึกษาที่ต้องการแก้ไข'));
    exit();
}

$no = (int)$_GET['no'];
$student = [];
$error = '';
$tablename = "tb_664230051"; 

try {
    $sql = "SELECT `No`, `ID`, `fname`, `lname`, `Age`, `Email`, `Tel` FROM `$tablename` WHERE `No` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$no]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        header('Location: index.php?error=' . urlencode('ไม่พบข้อมูลนักศึกษาในฐานข้อมูล'));
        exit();
    }
} catch (PDOException $e) {
    $error = "Database Error (SELECT): " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขนักศึกษา</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .container { min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .card { max-width: 450px; width: 100%; border-radius: 15px; }
    </style>
</head>

<body>
    <div class="container">
        <div class="card shadow-lg p-4">
            <h2 class="mb-4 text-center text-dark"><i class="fas fa-edit me-2"></i>แก้ไขข้อมูลนักศึกษา</h2>
            
            <?php if ($error) : ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form action="index.php" method="post" class="row g-3">
                
                <input type="hidden" name="edit_student" value="1">
                <input type="hidden" name="edit_no" value="<?= $student['No'] ?? '' ?>">

                <div class="col-12">
                    <label for="id" class="form-label">รหัสนักศึกษา</label>
                    <input type="text" name="id" id="id" class="form-control" placeholder="เช่น 664230051" required value="<?= $student['ID'] ?? '' ?>">
                </div> 
                <div class="col-md-6">
                    <label for="name" class="form-label">ชื่อ</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="ชื่อ" required value="<?= $student['fname'] ?? '' ?>">
                </div>
                <div class="col-md-6">
                    <label for="lastname" class="form-label">นามสกุล</label>>
                    <input type="text" name="lastname" id="lastname" class="form-control" placeholder="นามสกุล" required value="<?= $student['lname'] ?? '' ?>">
                </div>
                
                <div class="col-12">
                    <label for="age" class="form-label">อายุ</label>
                    <input type="number" name="age" id="age" class="form-control" placeholder="อายุ" required min="18" value="<?= $student['Age'] ?? '' ?>">
                </div>
                
                <div class="col-12">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="example@gmail.com" required value="<?= $student['Email'] ?? '' ?>">
                </div>
                <div class="col-12">
                    <label for="tel" class="form-label">เบอร์โทร</label>
                    <input type="text" name="tel" id="tel" class="form-control" placeholder="0623456789" required value="<?= $student['Tel'] ?? '' ?>">
                </div>
               
                <div class="col-12 d-grid mt-4">
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="fas fa-save me-1"></i> บันทึกการแก้ไข
                    </button>
                </div>
                <div class="col-12 text-center">
                    <a href="index.php" class="btn btn-link text-secondary">
                         <i class="fas fa-arrow-left me-1"></i> ยกเลิก
                    </a>
                </div>
                
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>