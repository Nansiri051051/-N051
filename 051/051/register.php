<?php
    require_once 'config.php';

    $error = []; // Array to hold error messages

    // ตัวแปรสำหรับเก็บค่าฟอร์มเดิม (เพื่อไม่ให้ค่าหายเมื่อเกิด error)
    $username = '';
    $fullname = '';
    $email = '';

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $username = trim($_POST['username'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if(empty($username)||empty($fullname)||empty($email)||empty($password)||empty($confirm_password)){
            $error[]= "กรุณากรอกข้อมูลให้ครบทุกช่อง";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error[] = "กรุณากรอกอีเมลให้ถูกต้อง";
        } elseif ($password !==$confirm_password) {
            $error[] = "รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน";
        } else {
            // ตรวจสอบว่าชื่อผู้ใช้หรืออีเมลซ้ำหรือไม่
            $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username,  $email]);

            if($stmt->rowCount() > 0){
                $error[] = "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้ไปแล้ว";
            }
        }

        if (empty($error)) {
            try {
                // เข้ารหัสรหัสผ่านก่อนบันทึก
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO users(username, full_name, email, password, role) VALUES (?, ?, ?, ?, 'member')";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$username, $fullname, $email, $hashedPassword]);
                
                header("Location: login.php?registered=success");
                exit();
            } catch (PDOException $e) {
                // กรณีเกิดข้อผิดพลาดในการเชื่อมต่อ/ฐานข้อมูล
                $error[] = "เกิดข้อผิดพลาดในการสมัครสมาชิก: " . $e->getMessage();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <!-- ใช้ Bootstrap เพื่อความสะดวก -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            /* สีพื้นหลังเต็มจอ: ชมพูเข้ม */
            background-color: #ff006aff;
            font-family: "Tahoma", sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh;
        }

        /* กล่องหลัก: ชมพูอ่อน */
        .card {
            background-color: #ff9bc1ff; 
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(255, 1, 162, 0.6); /* เงาสีชมพู */
            border: none;
        }

        /* ส่วนหัวของกล่อง: ชมพูเข้ม */
        .card-header {
            background-color: #f25e9cff !important; /* ชมพูเข้มแทน primary */
            color: white;
            border-bottom: 1px solid #cc0055;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
        }
        
        /* ปุ่มหลัก: ชมพูเข้ม */
        .btn-primary {
            background-color: #ff006aff !important;
            border-color: #ff006aff !important;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #ff0000ff !important;
            border-color: #cc0055 !important;
        }

        /* ปุ่มลิงก์ (เข้าสู่ระบบ): ชมพูเข้ม */
        .btn-link.text-primary {
            color: #ff006aff !important;
        }
        
        .btn-link.text-primary:hover {
            color: #cc0055 !important;
        }

        /* ข้อความ Error */
        .alert-primary {
             /* เปลี่ยน Alert primary เป็นสีชมพูอ่อนกว่า */
            color: #8b003a; 
            background-color: #ffd9e7; 
            border-color: #ffb3d1; 
        }

    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5"> <!-- ทำให้กล่องเล็กกว่าเดิมนิดหน่อย -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-primary">
                        <ul>
                            <?php foreach ($error as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header text-center text-white">
                        <h2>𝑹𝒆𝒈𝒊𝒔𝒕𝒆𝒓</h2>
                    </div>
                    <div class="card-body">
                        <form action="register.php" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">ชื่อผู้ใช้</label>
                                <input type="text" name="username" id="username" class="form-control" placeholder="ชื่อผู้ใช้" required value="<?= htmlspecialchars($username) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="fullname" class="form-label">ชื่อ-สกุล</label>
                                <input type="text" name="fullname" id="fullname" class="form-control" placeholder="ชื่อ-สกุล" required value="<?= htmlspecialchars($fullname) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">อีเมล</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="อีเมล" required value="<?= htmlspecialchars($email) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">รหัสผ่าน</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="รหัสผ่าน" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="ยืนยันรหัสผ่าน" required>
                            </div>
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
                                <a href="login.php" class="btn btn-link text-primary">เข้าสู่ระบบ</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
