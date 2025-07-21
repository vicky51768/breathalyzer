<?php
$conn = new mysqli("localhost", "root", "", "devices");
if ($conn->connect_error) die("連線失敗：" . $conn->connect_error);

$id = $_POST['id'];
$device_code = $_POST['device_code'];
$purchase_date = $_POST['purchase_date'];
$notes = $_POST['notes'];

$stmt = $conn->prepare("UPDATE devices SET device_code=?, purchase_date=?, notes=? WHERE id=?");
$stmt->bind_param("sssi", $device_code, $purchase_date, $notes, $id);
$stmt->execute();

header("Location: index.php");
exit;
?>
