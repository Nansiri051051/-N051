<?php

require_once '../config.php';
require_once 'auth_admin.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
header("Location: ../login.php");
exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f43182ff, #ff006aff);
            background-size: 400% 400%;
            animation: gradient-animation 15s ease infinite;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Ensure the body takes full viewport height for vertical centering */
            min-height: 100vh; 
        }
        @keyframes gradient-animation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        /* ... (The rest of the CSS styling remains the same for appearance) ... */
        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52) !important;
            border-top-left-radius: 20px !important;
            border-top-right-radius: 20px !important;
            color: white;
        }
        .btn-warning {
            background-color: #d46ff9ff;
            border-color: #d46ff9ff;
            transition: background-color 0.3s ease;
        }
        .btn-warning:hover {
            background-color: #b516eeff;
            border-color: #b516eeff;
        }
        .btn-dark {
            background-color: #12e884ff;
            border-color: #12e884ff;
            transition: background-color 0.3s ease;
        }
        .btn-dark:hover {
            background-color: #00934eff;
            border-color: #00934eff;
        }
        .btn-primary {
            background-color: #65a0f8ff;
            border-color: #65a0f8ff;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #034bb6ff;
            border-color: #034bb6ff;
        }
        .btn-success {
            background-color: #f1be4eff;
            border-color: #f1be4eff;
            transition: background-color 0.3s ease;
        }
        .btn-success:hover {
            background-color: #fbab00ff;
            border-color: #fbab00ff;
        }
        .btn-secondary {
            background-color: #f54242ff;
            border-color: #f54242ff;
            transition: background-color 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #ff0000ff;
            border-color: #ff0000ff;
        }
    </style>
</head>
<body class="container mt-4 d-flex align-items-center justify-content-center"> 
    <div class="row justify-content-center w-100">
        <div class="col-lg-8">
            <div class="card shadow-lg p-4">
                <h2 class="text-center mb-4">ระบบผู้ดูแลระบบ</h2>
                <p class="text-center mb-4">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?></p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <a href="users.php" class="btn btn-warning w-100">จัดการสมาชิก</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <a href="category.php" class="btn btn-dark w-100">จัดการหมวดหมู่</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <a href="products.php" class="btn btn-primary w-100">จัดการสินค้า</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <a href="orders.php" class="btn btn-success w-100">จัดการคำสั่งซื้อ</a>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="../logout.php" class="btn btn-secondary mt-3 w-100">ออกจากระบบ</a>
            </div>
        </div>
    </div>
</body>
</html>