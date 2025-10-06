<?php
    session_start();
    require_once 'config.php';
    $isLoggedIn = isset($_SESSION['user_id']);

    // ดึงข้อมูลสินค้า พร้อมคอลัมน์ image
    $stmt = $conn->query("SELECT p.product_id, p.product_name, p.description, p.price, p.stock, p.category_id, p.created_at, p.image, c.category_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC");
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* ... ส่วนของ CSS เดิม ... */
        body {
            background: linear-gradient(135deg, rgba(255, 77, 163, 1), #ff006aff);
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
        .btn-success {
            background-color: #1ca4ffff;
            border-color: #ffffffff;
            transition: background-color 0.3s ease;
        }
        .btn-success:hover {
            background-color: #1ca4ffff;
            border-color: #ffffffff;
        }
        .btn-primary {
            background-color: #ff0000ff;
            border-color: #ffffffff;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #ff0000ff;
            border-color: #ffffffff;
        }
        .btn-outline-primary {
            border-color: #ff006aff;
            color: #000000ff;
        }
        .btn-outline-primary:hover {
            background-color: #00ffffff;
            color: black;
        }
        .success-message {
            position: fixed;
            top: 2rem;
            left: 50%;
            transform: translateX(-50%);
            padding: 1rem 2rem;
            background-color: #ff006aff;
            color: white;
            border-radius: 9999px;
            box-shadow: 0 4px 6px rgba(255, 0, 0, 1);
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            z-index: 1000;
        }
        .success-message.show {
            opacity: 1;
        }
        @media (max-width: 768px) {
            h1 {
                font-size: 1.8rem;
            }
        }
        product-card { border: 1; background:#fff; }
        .product-thumb { height: 180px; object-fit: cover; border-radius:.5rem; }
        .product-meta { font-size:.75rem; letter-spacing:.05em; color:#8a8f98; text-transform:uppercase; }
        .product-title { font-size:1rem; margin:.25rem 0 .5rem; font-weight:600; color:#222; }
        .price { font-weight:700; }
        .rating i { color:#ffc107; } /* ดำวสที อง */
        .wishlist { color:#b9bfc6; }
        .wishlist:hover { color:#ff5b5b; }
        .badge-top-left {
        position:absolute; top:.5rem; left:.5rem; z-index:2;
        border-radius:.375rem;
        }

    </style>
</head>
<body class="container mt-4">
    <div id="success-message" class="success-message">
        <i class="fa-solid fa-check-circle mr-2"></i>เพิ่มสินค้าลงในตะกร้าแล้ว!
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>รายการสินค้า</h1>
            <div>
                <?php if ($isLoggedIn): ?>
                    <span class="me-3">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?> (<?=
                    $_SESSION['role'] ?>)</span>
                    <a href="profile.php" class="btn btn-info">ข้อมูลส่วนตัว</a>
                    <a href="cart.php" class="btn btn-warning">ดูตะกร้า</a>
                    <a href="orders.php" class="btn btn-primary">ประวัติการสั่งซื้อ</a>
                    <a href="logout.php" class="btn btn-secondary">ออกจากระบบ</a>
                    <?php else: ?>
                    <a href="/051/login.php" class="btn btn-success">เข้าสู่ระบบ</a>
                    <a href="/051/register.php" class="btn btn-primary">สมัครสมาชิก</a>
                    <?php endif; ?>
            </div>
    </div>
    <div class="row g-4"> 
        <?php foreach ($products as $p): ?>
            <?php
            // สร้างพาธรูปภาพด้วย Relative Path
            $img = !empty($p['image'])
            ? 'product_images/' . rawurlencode($p['image'])
            : 'product_images/no-image.jpg';
            
            // โค้ดที่เหลือ
            $isNew = isset($p['created_at']) && (time() - strtotime($p['created_at']) <= 7*24*3600);
            $isHot = (int)$p['stock'] > 0 && (int)$p['stock'] < 5;
            $rating = isset($p['rating']) ? (float)$p['rating'] : 4.5;
            $full = floor($rating);
            $half = ($rating - $full) >= 0.5 ? 1 : 0;
            ?>
        
            <div class="col-12 col-sm-6 col-lg-3"> 
                <div class="card product-card h-100 position-relative"> 
                    <?php if ($isNew): ?>
                    <span class="badge bg-success badge-top-left">NEW</span>
                    <?php elseif ($isHot): ?>
                    <span class="badge bg-danger badge-top-left">HOT</span>
                    <?php endif; ?>
                    
                    <a href="product_detail.php?id=<?= (int)$p['product_id'] ?>" class="p-3 d-block">
                        <img src="<?= htmlspecialchars($img) ?>"
                        alt="<?= htmlspecialchars($p['product_name']) ?>"
                        class="img-fluid w-100 product-thumb">
                    </a>
                    
                    <div class="px-3 pb-3 d-flex flex-column"> 
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="product-meta">
                            <?= htmlspecialchars($p['category_name'] ?? 'Category') ?>
                            </div>
                            <button class="btn btn-link p-0 wishlist" title="Add to wishlist" type="button">
                            <i class="bi bi-heart"></i>
                            </button>
                        </div>
                        <a class="text-decoration-none" href="product_detail.php?id=<?= (int)$p['product_id'] ?>">
                            <div class="product-title">
                            <?= htmlspecialchars($p['product_name']) ?>
                            </div>
                        </a>
                        <div class="rating mb-2">
                            <?php for ($i=0; $i<$full; $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
                            <?php if ($half): ?><i class="bi bi-star-half"></i><?php endif; ?>
                            <?php for ($i=0; $i<5-$full-$half; $i++): ?><i class="bi bi-star"></i><?php endfor; ?>
                        </div>
                        <div class="price mb-3">
                            <?= number_format((float)$p['price'], 2) ?> บาท
                        </div>
                        <div class="mt-auto d-flex gap-2">
                            <?php if ($isLoggedIn): ?>
                            <form action="cart.php" method="post" class="d-inline-flex gap-2">
                                <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-sm btn-success">เพิ่มในตะกร้า</button>
                            </form>
                            <?php else: ?>
                            <small class="text-muted">เข้าสู่ระบบเพื่อสั่งซื้อ</small>
                            <?php endif; ?>
                            <a href="product_detail.php?id=<?= (int)$p['product_id'] ?>"
                            class="btn btn-sm btn-outline-primary ms-auto">ดูรายละเอียด</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showSuccessMessage(event) {
            event.preventDefault();
            const message = document.getElementById('success-message');
            message.classList.add('show');
            setTimeout(() => {
                message.classList.remove('show');
                event.target.submit();
            }, 2000);
        }
        
        // แนบ Event Listener กับฟอร์มเพิ่มในตะกร้า
        document.querySelectorAll('form[action="cart.php"]').forEach(form => {
            form.addEventListener('submit', showSuccessMessage);
        });
    </script>
</body>
</html>