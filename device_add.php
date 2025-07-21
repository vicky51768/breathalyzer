<?php

$conn = new mysqli("localhost", "root", "", "devices");
if ($conn->connect_error) die("連線失敗：" . $conn->connect_error);

$device_code = $_POST['device_code'];
$purchase_date = $_POST['purchase_date'];
$notes = $_POST['notes'] ?? '';

// 新增資料
$stmt = $conn->prepare("INSERT INTO devices (device_code, purchase_date, notes) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $device_code, $purchase_date, $notes);
$stmt->execute();
$stmt->close();

header("Location: index.php");
exit;
?>

