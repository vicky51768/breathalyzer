<?php
$conn = new mysqli("localhost", "root", "", "devices");
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}

if (!isset($_POST['device_id'], $_POST['inspection_date'], $_FILES['report_file'])) {
    die("缺少必要欄位！");
}

$device_id = (int)$_POST['device_id'];  // 強制轉整數
$inspection_date = $_POST['inspection_date'];

$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$file_name = basename($_FILES["report_file"]["name"]);
$target_path = $upload_dir . time() . "_" . $file_name;

if (move_uploaded_file($_FILES["report_file"]["tmp_name"], $target_path)) {
    $stmt = $conn->prepare("INSERT INTO inspections (device_id, inspection_date, report_path) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("SQL 準備失敗：" . $conn->error);
    }
    $stmt->bind_param("iss", $device_id, $inspection_date, $target_path);
    if (!$stmt->execute()) {
        die("資料寫入失敗：" . $stmt->error);
    }
    $stmt->close();
    header("Location: index.php");
    exit;
} else {
    die("檔案上傳失敗！");
}
?>


