<?php
$conn = new mysqli("localhost", "root", "", "devices");
if ($conn->connect_error) die("連線失敗：" . $conn->connect_error);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // 先查詢該檔案路徑，刪除實體檔案
    $res = $conn->query("SELECT report_path FROM inspections WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        $file = $row['report_path'];
        if (file_exists($file)) {
            unlink($file);
        }
    }
    // 刪除資料庫資料
    $conn->query("DELETE FROM inspections WHERE id = $id");
}

// 導回原頁，這裡可依需求調整
header("Location: index.php");
exit;
?>
