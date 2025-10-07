<?php
session_start();
include 'config.php';

$action = $_GET['action'] ?? 'list';
$message = '';

if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}

// ------------------------------------
// 1. ส่วนจัดการ POST (เพิ่ม/แก้ไข)
// ------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_student']) || isset($_POST['edit_student']))) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $age = (int)$_POST['age'];

    if (isset($_POST['add_student'])) {
        // SQL INSERT ที่ใช้ Backticks (`) แก้ปัญหา Unknown column แล้ว
        $sql = "INSERT INTO $tablename (`ID`, `Name`, `Lastname`, `Age`, `Email`, `Tel`) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$id, $name, $lastname, $age, $email, $tel])) {
            $message = 'เพิ่มนักศึกษาใหม่สำเร็จแล้ว';
        } else {
            $message = 'Error: เพิ่มข้อมูลไม่สำเร็จ';
        }
    } elseif (isset($_POST['edit_student'])) {
        $no = (int)$_POST['edit_no'];
        // SQL UPDATE ที่ใช้ Backticks (`) แก้ปัญหา Unknown column แล้ว
        $sql = "UPDATE $tablename SET 
                `ID` = ?, 
                `Name` = ?, 
                `Lastname` = ?, 
                `Age` = ?, 
                `Email` = ?, 
                `Tel` = ? 
                WHERE `No` = ?";

        $stmt = $conn->prepare($sql);
        if ($stmt->execute([$id, $name, $lastname, $age, $email, $tel, $no])) {
            $message = 'แก้ไขข้อมูลนักศึกษาสำเร็จแล้ว';
        } else {
            $message = 'Error: แก้ไขข้อมูลไม่สำเร็จ';
        }
    }
    
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . '?message=' . urlencode($message));
    exit();
}

// ------------------------------------
// 2. ส่วนจัดการลบข้อมูล
// ------------------------------------
if ($action === 'delete' && isset($_GET['no'])) {
    $delete_no = (int)$_GET['no'];
    $sql = "DELETE FROM $tablename WHERE `No` = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$delete_no])) {
        $message = 'ลบนักศึกษาสำเร็จแล้ว!';
    } else {
        $message = 'Error: ลบข้อมูลไม่สำเร็จ';
    }
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . '?message=' . urlencode($message));
    exit();
}

// ------------------------------------
// 3. ส่วนแสดงรายการ (บรรทัดที่เคยเกิด Error 92/97/98)
// ------------------------------------
$filterStudents = [];
$current_filter_age = '';
$where_clause = '';
$params = [];

if (isset($_POST['filter_age']) && !empty($_POST['filter_age'])) {
    $filterAge = (int)$_POST['filter_age'];
    $current_filter_age = $filterAge;
    $where_clause = "WHERE `Age` = ?";
    $params[] = $filterAge;
}

// SQL SELECT ที่ใช้ Backticks (`) แก้ปัญหา Unknown column แล้ว
$sql_select = "SELECT 'No', 'ID', 'Name', 'Lastname', 'Age', 'Email', 'Tel', 'Created At' FROM $tablename $where_clause ORDER BY 'No' ASC";
$stmt = $conn->prepare($sql_select);
$stmt->execute($params);
$filterStudents = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลนักศึกษา</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <style>
        .container { max-width: 1000px; }
        .dataTables_wrapper { overflow-x: auto; }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1>ข้อมูลนักศึกษา</h1>
        
        <?php if ($message) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <a href="adduser.php" class="btn btn-primary mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
            </svg>
            เพิ่มนักศึกษา
        </a>
        
        <hr>

        <form action="" method="POST" class="mb-4">
            <div class="input-group">
                <input type="number" name="filter_age" placeholder="กรอกอายุเพื่อกรองข้อมูล" class="form-control" value="<?= $current_filter_age ?>">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="?action=list" class="btn btn-danger">Clear Filter</a>
            </div>
        </form>
        
        <hr>

        <table id="studentTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Lastname</th>
                    <th>Age</th>
                    <th>Email</th>
                    <th>Tel</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filterStudents as $student) : ?>
                    <tr>
                        <td><?= $student["No"] ?></td>
                        <td><?= $student["ID"] ?></td>
                        <td><?= $student["Name"] ?></td>
                        <td><?= $student["Lastname"] ?></td>
                        <td><?= $student["Age"] ?></td>
                        <td><?= $student["Email"] ?></td>
                        <td><?= $student["Tel"] ?></td>
                        <td><?= $student["Created At"] ?></td>
                        <td>
                            <a href="adduser.php?action=edit&no=<?= $student["No"] ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                            <a href="?action=delete&no=<?= $student["No"] ?>" 
                                class="btn btn-sm btn-danger" 
                                onclick="return confirm('คุณต้องการลบข้อมูลนักศึกษา <?= $student['Name'] ?> ใช่หรือไม่?');">ลบ</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($filterStudents) && isset($_POST['filter_age'])) : ?>
            <div class="alert alert-info">ไม่พบนักศึกษาที่มีอายุ <?= $current_filter_age ?> ปี</div>
        <?php elseif (empty($filterStudents)) : ?>
            <div class="alert alert-info">ยังไม่มีข้อมูลนักศึกษาในระบบ</div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let table = new DataTable('#studentTable');
            setTimeout(function() {
                $(".alert").alert('close');
            }, 3000);
        });
    </script>
</body>
</html>