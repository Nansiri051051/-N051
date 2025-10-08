<?php
session_start();
require_once 'config.php'; 
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มนักศึกษาใหม่</title>
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
            <h2 class="mb-4 text-center text-dark"><i class="fas fa-user-plus me-2"></i>เพิ่มนักศึกษาใหม่</h2>
            <form action="index.php" method="post" class="row g-3">
                <input type="hidden" name="add_student" value="1">

                <div class="col-12">
                    <label for="id" class="form-label">รหัสนักศึกษา</label>
                    <input type="text" name="id" id="id" class="form-control" placeholder="เช่น 664230051" required>
                </div> 
                <div class="col-md-6">
                    <label for="name" class="form-label">ชื่อ</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="ชื่อ" required>
                </div>
                <div class="col-md-6">
                    <label for="lastname" class="form-label">นามสกุล</label>
                    <input type="text" name="lastname" id="lastname" class="form-control" placeholder="นามสกุล" required>
                </div>
                
                <div class="col-12">
                    <label for="age" class="form-label">อายุ</label>
                    <input type="number" name="age" id="age" class="form-control" placeholder="อายุ" required min="18">
                </div>
                
                <div class="col-12">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="example@gmail.com" required>
                </div>
                <div class="col-12">
                    <label for="tel" class="form-label">เบอร์โทร</label>
                    <input type="text" name="tel" id="tel" class="form-control" placeholder="0623456789" required>
                </div>
               
                <div class="col-12 d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-1"></i> เพิ่มข้อมูลนักศึกษา
                    </button>
                </div>
                <div class="col-12 text-center">
                    <a href="index.php" class="btn btn-link text-secondary">
                         <i class="fas fa-arrow-left me-1"></i> กลับไปรายการนักศึกษา
                    </a>
                </div>
                
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
