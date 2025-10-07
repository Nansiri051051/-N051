<?php
// config.php
$host = "localhost";
$dbname = "db6646_051";     // ตรวจสอบชื่อฐานข้อมูล
$username = "root"; 
$password = "";     
$charset = 'utf8mb4';
$tablename = "tb_664230051"; // ตรวจสอบชื่อตาราง

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // สร้าง Object การเชื่อมต่อ PDO
    $conn = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    // หากเชื่อมต่อไม่ได้ จะแสดงข้อความ Error
    die("Connection failed: " . $e->getMessage());
}
?>