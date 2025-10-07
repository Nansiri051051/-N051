<?php
session_start();
include 'config.php'; // เรียกไฟล์เชื่อมต่อฐานข้อมูล (PDO)

$action = $_GET['action'] ?? 'add';
$editStudent = null;
$error = '';

// หากเป็นการแก้ไข (action=edit) ดึงข้อมูลนักศึกษาเดิมจากฐานข้อมูล
if ($action === 'edit' && isset($_GET['no'])) {
    $edit_no = (int)$_GET['no'];
    
    // SQL SELECT: ใช้ Backticks ครอบชื่อคอลัมน์ที่ถูกต้อง
    $stmt = $conn->prepare("SELECT `No`, `ID`, `Name`, `Lastname`, `Age`, `Email`, `Tel`, `Created At` FROM $tablename WHERE No = ?");
    $stmt->execute([$edit_no]);
    $row = $stmt->fetch();
    
    if ($row) {
        $editStudent = [
            'no' => $row['No'],
            'id' => $row['ID'],
            'name' => $row['Name'],
            'lastname' => $row['Lastname'],
            'age' => $row['Age'],
            'email' => $row['Email'],
            'tel' => $row['Tel'],
            'created_at' => $row['Created At'],
        ];
    } else {
        $action = 'add'; 
        header('Location: index.php?message=' . urlencode('ไม่พบข้อมูลนักศึกษาที่ต้องการแก้ไข'));
        exit();
    }
}

// กำหนดค่าเริ่มต้นสำหรับฟอร์ม (ใช้ค่าเดิมหากเป็นการแก้ไข)
$default_data = [
    'id' => $editStudent['id'] ?? '',
    'name' => $editStudent['name'] ?? '',
    'lastname' => $editStudent['lastname'] ?? '',
    'age' => $editStudent['age'] ?? '',
    'email' => $editStudent['email'] ?? '',
    'tel' => $editStudent['tel'] ?? '',
    'edit_no' => $editStudent['no'] ?? '', 
    'created_at' => $editStudent['created_at'] ?? '',
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editStudent ? 'แก้ไขนักศึกษา' : 'เพิ่มนักศึกษา' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <style>
        .container { min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .card { max-width: 400px; width: 100%; }
    </style>
</head>

<body>
    <div class="container">
        <div class="card shadow-lg p-4">
            <h2 class="mb-4 text-center text-dark">
                <?= $editStudent ? 'แก้ไขนักศึกษา' : 'เพิ่มนักศึกษา' ?>
            </h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger mt-3">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="post">
                
                <input type="hidden" name="<?= $editStudent ? 'edit_student' : 'add_student' ?>" value="1">
                <?php if ($editStudent) : ?>
                    <input type="hidden" name="edit_no" value="<?= $default_data['edit_no'] ?>">
                    <input type="hidden" name="original_created_at" value="<?= htmlspecialchars($default_data['created_at']) ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="id" class="form-label">รหัสนักศึกษา</label>
                    <input type="text" name="id" id="id" class="form-control" placeholder="เช่น 664230051" 
                           value="<?= htmlspecialchars($default_data['id']) ?>" required>
                </div> 
                <div class="mb-3">
                    <label for="name" class="form-label">ชื่อ</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="ชื่อ" 
                           value="<?= htmlspecialchars($default_data['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="lastname" class="form-label">นามสกุล</label>
                    <input type="text" name="lastname" id="lastname" class="form-control" placeholder="นามสกุล" 
                           value="<?= htmlspecialchars($default_data['lastname']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="age" class="form-label">อายุ</label>
                    <input type="number" name="age" id="age" class="form-control" placeholder="อายุ" 
                           value="<?= htmlspecialchars($default_data['age']) ?>" required min="18">
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="example@gmail.com" 
                           value="<?= htmlspecialchars($default_data['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="tel" class="form-label">เบอร์โทร</label>
                    <input type="text" name="tel" id="tel" class="form-control" placeholder="0623456789" 
                           value="<?= htmlspecialchars($default_data['tel']) ?>" required>
                </div>
               
                <div class="d-grid mb-2">
                    <button type="submit" class="btn btn-<?= $editStudent ? 'warning' : 'primary' ?> btn-lg">
                        <?= $editStudent ? 'บันทึกการแก้ไข' : 'เพิ่มข้อมูล' ?>
                    </button>
                </div>
                <div class="text-center">
                    <a href="index.php" class="btn btn-link text-secondary">
                        ดูรายการนักศึกษา
                    </a>
                </div>
                
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>