<?php
$upload_dir = "uploads/annual_reports/";
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

foreach ($_FILES['annual_files']['tmp_name'] as $index => $tmp_name) {
    $original_name = basename($_FILES['annual_files']['name'][$index]);
    $target_path = $upload_dir . $original_name;

    // 若檔案已存在，自動在檔名後加 _1, _2, ...
    $file_parts = pathinfo($original_name);
    $counter = 1;
    while (file_exists($target_path)) {
        $new_name = $file_parts['filename'] . "_" . $counter . "." . $file_parts['extension'];
        $target_path = $upload_dir . $new_name;
        $counter++;
    }

    move_uploaded_file($tmp_name, $target_path);
}

header("Location: index.php");
exit;
?>
