<?php
session_start();
require_once 'config.php'; 

$error = '';
$username = ''; 

// โค้ดสำหรับประมวลผลการ Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? ''; 
    $password = $_POST['password'] ?? ''; 

    if (empty($username) || empty($password)) {
        $error = "กรุณากรอกชื่อผู้ใช้และรหัสผ่านให้ครบถ้วน";
    } else {
        try {
            $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // 1. ตรวจสอบรหัสผ่านแบบเข้ารหัส
            if ($user && password_verify($password, $user['password'])) {
                
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: /051/index.php"); 
                exit();
                
            } 
            // 2. [แก้ปัญหาชั่วคราว] ตรวจสอบรหัสผ่าน Plain Text สำหรับ admin1@example.com
            else if ($user && $user['username'] === 'admin1@example.com' && $user['password'] === $password) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                $newHashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $updateStmt->execute([$newHashedPassword, $user['user_id']]);

                header("Location: /051/index.php"); 
                exit();
            }
            // 3. ล็อกอินล้มเหลว
            else {
                $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
            }
        } catch (PDOException $e) {
            $error = "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล";
        }
    }
}
?>
<!DOCTYPE html> 
<html lang="th"> 
<head> 
<meta charset="UTF-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>เข้าสู่ระบบ</title> 
<style> 
/* *** โค้ด CSS ที่ได้รับการแก้ไข *** */

/* รีเซ็ต Margin/Padding และบังคับความสูงเต็มจอ */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box; 
}

html, body {
    height: 100%; /* บังคับความสูงเต็มจอ */
    width: 100%;
}

body { 
    font-family: "Tahoma", sans-serif; 
    background-color: #ff006aff;  
    
    /* *** ลบ Flexbox ออก *** */
    /* display: flex; 
    justify-content: center; 
    align-items: center; */
    
    position: relative; /* สำคัญ: กำหนดให้ body เป็นจุดอ้างอิงสำหรับ Absolute Positioning */
    overflow: hidden; 
} 

.login-box { 
    background: #ff9bc1ff; 
    border-radius: 10px; 
    box-shadow: 0 4px 15px rgba(255, 1, 162, 0.8); 
    width: 320px; 
    padding: 35px; 
    text-align: center; 
    
    /* *** เทคนิค Absolute Centering *** */
    position: absolute; /* กำหนดตำแหน่งสัมบูรณ์ */
    top: 50%; /* เลื่อนลงมา 50% ของความสูงหน้าจอ */
    left: 50%; /* เลื่อนไปทางขวา 50% ของความกว้างหน้าจอ */
    transform: translate(-50%, -50%); /* ดึงกลับมา 50% ของความกว้าง/ความสูงของกล่องเอง */
} 

.login-box h2 { 
    margin-bottom: 25px; 
    color: #1c0513ff;  
} 
    
    .error-message {
        color: #d9534f; 
        margin-bottom: 15px;
        font-weight: bold;
    }

.login-box input[type="text"], 
.login-box input[type="password"] { 
    display: block; 
    width: 100%; 
    font-size: 16px; 
    padding: 10px; 
    margin: 10px 0; 
    border: 1px solid #ff006aff; 
    border-radius: 5px; 
    outline: none; 
    box-sizing: border-box;  
} 

.login-box button { 
    width: 100%; 
    padding: 10px; 
    background-color: #ff006aff; 
    border: none; 
    border-radius: 5px; 
    color: white; 
    font-size: 16px; 
    cursor: pointer; 
    margin-top: 10px; 
    transition: background 0.3s ease; 
} 
    .login-box button:hover {
        background-color: #cc0055;
    }

.login-box .register-btn { 
    background-color: #fc7eb3ff;  
    margin-top: 15px; 
      color: white; 
} 

</style> 
</head> 
<body> 
<div class="login-box"> 
<h2>𝑾𝒆𝒍𝒄𝒐𝒎𝒆</h2> 
    
    <?php if ($error): ?>
        <div class="error-message"><?= $error ?></div>
    <?php endif; ?>

<form action="/051/login.php" method="POST"> 
 <input type="text" name="username" placeholder="ชื่อผู้ใช้หรืออีเมล" required 
                value="<?= htmlspecialchars($username) ?>"> 
 <input type="password" name="password" placeholder="รหัสผ่าน" required> 
 <button type="submit">เข้าสู่ระบบ</button> 
 </form> 

<form action="/051/register.php" method="GET"> <button type="submit" class="register-btn">สมัครสมาชิก</button> 
</form> 
</div> 
</body> 
</html>
