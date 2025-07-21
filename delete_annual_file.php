<?php
if (isset($_GET['file'])) {
    $filename = basename($_GET['file']);  // 確保不會被目錄穿越攻擊
    $filepath = __DIR__ . "/uploads/annual_reports/" . $filename;

    if (file_exists($filepath)) {
        unlink($filepath);  // 刪除檔案
    }
}

header("Location: index.php");
exit;