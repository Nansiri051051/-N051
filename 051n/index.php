<?php
session_start();
require_once 'config.php';

$error = '';
$message = '';
$tablename = "tb_664230051"; 
$data = []; 
$filterStudents = []; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_student']) || isset($_POST['edit_student']))) {
    $id = trim($_POST['id']);
    $name = trim($_POST['name']); 
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $tel = trim($_POST['tel']);
    $age = (int)$_POST['age'];

    try {
        if (isset($_POST['add_student'])) {
            $sql = "INSERT INTO `$tablename` (`ID`, `fName`, `lName`, `Age`, `Email`, `Tel`) 
                     VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([$id, $name, $lastname, $age, $email, $tel])) {
                $message = 'เพิ่มนักศึกษาใหม่สำเร็จแล้ว';
            } else {
                $error = 'Error: เพิ่มข้อมูลไม่สำเร็จ';
            }
        } elseif (isset($_POST['edit_student'])) {
            $no = (int)$_POST['edit_no'];
            $sql = "UPDATE `$tablename` SET 
                    `ID` = ?, 
                    `fName` = ?, 
                    `lName` = ?, 
                    `Age` = ?, 
                    `Email` = ?, 
                    `Tel` = ? 
                    WHERE `No` = ?";

            $stmt = $conn->prepare($sql);
            if ($stmt->execute([$id, $name, $lastname, $age, $email, $tel, $no])) {
                $message = 'แก้ไขข้อมูลนักศึกษาสำเร็จแล้ว';
            } else {
                $error = 'Error: แก้ไขข้อมูลไม่สำเร็จ';
            }
        }
    } catch (PDOException $e) {
        $error = "Database Error (POST): " . $e->getMessage();
    }
    
    header('Location: index.php?message=' . urlencode($message) . '&error=' . urlencode($error));
    exit();
}

try {
    $sql = "SELECT `No`, `ID`, `fName`, `lName`, `Age`, `Email`, `Tel`, `Created At` FROM `$tablename`";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $filterStudents = $data;
    if (isset($_POST['filter_id']) && !empty($_POST['filter_id'])) {
        $filterID = $_POST['filter_id'];
        
        $filterStudents = array_filter($data, function($student) use ($filterID) {
            return isset($student['ID']) && $student['ID'] == $filterID;
        });
        $filterStudents = array_values($filterStudents);
    }
    
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['no'])) {
        $delete_no = (int)$_GET['no'];
        $sql_delete = "DELETE FROM `$tablename` WHERE `No` = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        
        if ($stmt_delete->execute([$delete_no])) {
            $message = 'ลบนักศึกษาสำเร็จแล้ว!';
        } else {
            $error = 'Error: ลบข้อมูลไม่สำเร็จ';
        }
        header('Location: index.php?message=' . urlencode($message) . '&error=' . urlencode($error));
        exit();
    }


} catch (PDOException $e) {
    $error = "Database Error (SELECT): " . $e->getMessage();
    $filterStudents = []; 
}

if (isset($_GET['message']) && !empty($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
if (isset($_GET['error']) && !empty($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการนักศึกษา</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        .container { max-width: 1000px; }
        .dataTables_wrapper { overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>รายการนักศึกษา</h1>
        <?php if ($message) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($error) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error ?>
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

        <form action="index.php" method="POST" class="mb-4">
            <div class="input-group">
                <input type="text" name="filter_id" placeholder="กรอกรหัส ID เพื่อกรองข้อมูล" class="form-control" value="<?= $_POST['filter_id'] ?? '' ?>">
                <button type="submit" class="btn btn-primary">Filter ID</button>
                <a href="index.php" class="btn btn-danger">Clear Filter</a>
            </div>
        </form>    
        <hr>
        <table id="studentTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
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
                <?php $i = 1; ?>
                <?php foreach ($filterStudents as $student) : ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($student["ID"] ?? '') ?></td>
                        <td><?= htmlspecialchars($student["fName"] ?? '') ?></td> 
                        <td><?= htmlspecialchars($student["lName"] ?? '') ?></td> 
                        <td><?= htmlspecialchars($student["Age"] ?? '') ?></td>
                        <td><?= htmlspecialchars($student["Email"] ?? '') ?></td>
                        <td><?= htmlspecialchars($student["Tel"] ?? '') ?></td>
                        <td><?= htmlspecialchars($student["Created At"] ?? '') ?></td>
                        <td>
                            <a href="edit_user.php?no=<?= $student["No"] ?? '' ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                            <a href="index.php?action=delete&no=<?= $student["No"] ?? '' ?>" 
                                class="btn btn-sm btn-danger" 
                                onclick="return confirm('คุณต้องการลบข้อมูลนักศึกษา <?= $student['fName'] ?? 'นี้' ?> ใช่หรือไม่?');">ลบ</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($data) && empty($_POST['filter_id'])) : ?>
            <div class="alert alert-info">ยังไม่มีข้อมูลนักศึกษาในระบบ</div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
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
