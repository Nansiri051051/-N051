<?php
session_start();
function initializeStudents() {
    return [
        ['no' => 1, 'id' => '664230051', 'name' => 'นันศิริ', 'lastname' => 'พุกภูษา', 'email' => '051@gmail.com', 'tel' => '0623456789', 'created_at' => '06:00', 'age' => 20],
        ['no' => 2, 'id' => '664230052', 'name' => 'โชคดี', 'lastname' => 'มีชัย', 'email' => '052@gmail.com', 'tel' => '0623456754', 'created_at' => '06:30', 'age' => 21],
        ['no' => 3, 'id' => '664230053', 'name' => 'ทุเรียน', 'lastname' => 'หมอนทอง', 'email' => '053@gmail.com', 'tel' => '0623456421', 'created_at' => '07:00', 'age' => 20],
        ['no' => 4, 'id' => '664230054', 'name' => 'แตงโม', 'lastname' => 'ใจดี', 'email' => '054@gmail.com', 'tel' => '0623456028', 'created_at' => '07:30', 'age' => 23],
        ['no' => 5, 'id' => '664230055', 'name' => 'สมหญิง', 'lastname' => 'แสนสวย', 'email' => '055@gmail.com', 'tel' => '0623456123', 'created_at' => '08:00', 'age' => 26],
        ['no' => 6, 'id' => '664230056', 'name' => 'สมชาย', 'lastname' => 'แสนหล่อ', 'email' => '056@gmail.com', 'tel' => '0623456987', 'created_at' => '08:30', 'age' => 28],
        ['no' => 7, 'id' => '664230057', 'name' => 'เบกกี้', 'lastname' => 'รีเวิร์ต', 'email' => '057@gmail.com', 'tel' => '0623456295', 'created_at' => '08:50', 'age' => 22],
        ['no' => 8, 'id' => '664230058', 'name' => 'อิงฟ้า', 'lastname' => 'แสนดี', 'email' => '058@gmail.com', 'tel' => '0623456946', 'created_at' => '09:00', 'age' => 25],
        ['no' => 9, 'id' => '664230059', 'name' => 'ชาล็อต', 'lastname' => 'ออสตี้', 'email' => '059@gmail.com', 'tel' => '062345952', 'created_at' => '09:20', 'age' => 23],
        ['no' => 10, 'id' => '664230060', 'name' => 'ดีใจ', 'lastname' => 'ใจดี', 'email' => '060@gmail.com', 'tel' => '0623456364', 'created_at' => '09:30', 'age' => 24],
    ];
}

if (!isset($_SESSION['students']) || count($_SESSION['students']) < 10) {
    $_SESSION['students'] = initializeStudents();
}

$students = $_SESSION['students'];
$next_no = (count($students) > 0) ? max(array_column($students, 'no')) + 1 : 1;
$action = $_GET['action'] ?? 'list';
$editStudent = null;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_student']) || isset($_POST['edit_student']))) {
    $newStudent = [
        'id' => htmlspecialchars($_POST['id']),
        'name' => htmlspecialchars($_POST['name']),
        'lastname' => htmlspecialchars($_POST['lastname']),
        'email' => htmlspecialchars($_POST['email']),
        'tel' => htmlspecialchars($_POST['tel']),
        'created_at' => date('H:i'), 
        'age' => (int)$_POST['age'],
    ];

    if (isset($_POST['add_student'])) {
        $newStudent['no'] = $next_no;
        $_SESSION['students'][] = $newStudent;
        $message = 'เพิ่มนักศึกษาใหม่สำเร็จแล้ว';
        header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();

    } elseif (isset($_POST['edit_student'])) {
        $edit_no = (int)$_POST['edit_no'];
        foreach ($_SESSION['students'] as $key => $student) {
            if ($student['no'] === $edit_no) {
                $newStudent['no'] = $edit_no;
                $_SESSION['students'][$key] = $newStudent;
                $message = 'แก้ไขข้อมูลนักศึกษาสำเร็จแล้ว';
                header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
                exit();
            }
        }
    }
}

if ($action === 'delete' && isset($_GET['no'])) {
    $delete_no = (int)$_GET['no'];
    foreach ($_SESSION['students'] as $key => $student) {
        if ($student['no'] === $delete_no) {
            unset($_SESSION['students'][$key]);
            $_SESSION['students'] = array_values($_SESSION['students']);
            $message = 'ลบนักศึกษาสำเร็จแล้ว!';
            header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
            exit();
        }
    }
}

$filterStudents = $students;
$current_filter_age = '';

if (isset($_POST['filter_age']) && !empty($_POST['filter_age'])) {
    $filterAge = (int)$_POST['filter_age'];
    $current_filter_age = $filterAge;
    $filterStudents = array_filter($students, function ($student) use ($filterAge) {
        return $student['age'] == $filterAge;
    });
    $filterStudents = array_values($filterStudents);
}

if ($action === 'edit' && isset($_GET['no'])) {
    $edit_no = (int)$_GET['no'];
    foreach ($students as $student) {
        if ($student['no'] === $edit_no) {
            $editStudent = $student;
            break;
        }
    }
    if (!$editStudent) {
        $action = 'add'; 
        $error = 'ไม่พบข้อมูลนักศึกษาที่ต้องการแก้ไข';
    }
}
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
        
        <?php if ($error) : ?>
            <div class="alert alert-danger" role="alert"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($action === 'add' || $action === 'edit' || $editStudent) : ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <?= $editStudent ? 'แก้ไขข้อมูลนักศึกษา (No: ' . $editStudent['no'] . ')' : 'เพิ่มนักศึกษาใหม่' ?>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="<?= $editStudent ? 'edit_student' : 'add_student' ?>" value="1">
                    <?php if ($editStudent) : ?>
                        <input type="hidden" name="edit_no" value="<?= $editStudent['no'] ?>">
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">ID (รหัส)</label>
                            <input type="text" name="id" class="form-control" value="<?= $editStudent['id'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ชื่อ</label>
                            <input type="text" name="name" class="form-control" value="<?= $editStudent['name'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">นามสกุล</label>
                            <input type="text" name="lastname" class="form-control" value="<?= $editStudent['lastname'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Age (อายุ)</label>
                            <input type="number" name="age" class="form-control" value="<?= $editStudent['age'] ?? '' ?>" required min="18">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= $editStudent['email'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tel (เบอร์โทร)</label>
                            <input type="text" name="tel" class="form-control" value="<?= $editStudent['tel'] ?? '' ?>" required>
                        </div>
                        
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-<?= $editStudent ? 'warning' : 'success' ?> me-2">
                                <?= $editStudent ? 'บันทึกการแก้ไข' : 'เพิ่มข้อมูลนักศึกษา' ?>
                            </button>
                            <a href="?action=list" class="btn btn-secondary">ยกเลิก</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php else : ?>
        <a href="?action=add" class="btn btn-primary mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
            </svg>
            เพิ่มนักศึกษา
        </a>
        <?php endif; ?>
        
        <hr>

        <form action="" method="POST" class="mb-4">
            <div class="input-group">
                <input type="number" name="filter_age" placeholder="กรอกอายุเพื่อกรองข้อมูล" class="form-control" value="<?= $current_filter_age ?>">
                <button type="submit" class="btn btn-info">Filter</button>
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
                        <td><?= $student["no"] ?></td>
                        <td><?= $student["id"] ?></td>
                        <td><?= $student["name"] ?></td>
                        <td><?= $student["lastname"] ?></td>
                        <td><?= $student["age"] ?></td>
                        <td><?= $student["email"] ?></td>
                        <td><?= $student["tel"] ?></td>
                        <td><?= $student["created_at"] ?></td>
                        <td>
                            <a href="?action=edit&no=<?= $student["no"] ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                            <a href="?action=delete&no=<?= $student["no"] ?>" 
                                class="btn btn-sm btn-danger" 
                                onclick="return confirm('คุณต้องการลบข้อมูลนักศึกษา <?= $student['name'] ?> ใช่หรือไม่?');">ลบ</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
        let table = new DataTable('#studentTable');
    </script>
</body>

</html>
