<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM tb_664230051 WHERE id = ?");
    $stmt->execute([$id]);

    header("Location:index.php");
    exit;
}
?>
