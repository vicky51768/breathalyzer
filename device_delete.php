<?php
$conn = new mysqli("localhost", "root", "", "devices");

$id = intval($_GET['id']);
$conn->query("DELETE FROM inspections WHERE device_id = $id");
$conn->query("DELETE FROM devices WHERE id = $id");

$conn->close();
header("Location: index.php");
exit;