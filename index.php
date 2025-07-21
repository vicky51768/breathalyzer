<?php
$conn = new mysqli("localhost", "root", "", "devices");
if ($conn->connect_error) die("連線失敗：" . $conn->connect_error);

// 搜尋功能
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$search_sql = $search ? "WHERE device_code LIKE '%$search%' OR notes LIKE '%$search%'" : '';

$result = $conn->query("SELECT * FROM devices $search_sql ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <title>檢測器設備管理</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet" />
</head>
<body class="container py-4">
  <h2 class="mb-4">檢測器設備與檢驗紀錄</h2>

  <!-- 新增設備按鈕 -->
  <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addDeviceModal">➕ 新增設備</button>

  <table id="deviceTable" class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
      <tr>
        <th>設備編號</th>
        <th>啟用日期</th>
        <th>檢驗紀錄</th>
        <th>備註</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()):
        $device_id = $row['id'];
        $records = $conn->query("SELECT * FROM inspections WHERE device_id = $device_id ORDER BY inspection_date DESC LIMIT 3");
      ?>
      <tr>
        <td><?= htmlspecialchars($row['device_code']) ?></td>
        <td><?= htmlspecialchars($row['purchase_date']) ?></td>
        <td>
          <?php while ($r = $records->fetch_assoc()): ?>
            <div class="mb-1">
              <?= htmlspecialchars($r['inspection_date']) ?>
              <a href="<?= htmlspecialchars($r['report_path']) ?>" target="_blank">📄 檢驗報告</a>
              <a href="inspection_delete.php?id=<?= $r['id'] ?>&return_url=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                 class="btn btn-sm btn-danger ms-2" 
                 onclick="return confirm('確定刪除這份報告嗎？')">刪除</a>
            </div>
          <?php endwhile; ?>
        </td>
        <td><?= htmlspecialchars($row['notes']) ?></td>
        <td>
          <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal" onclick="setDeviceId(<?= $device_id ?>)">新增紀錄</button>
          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editDeviceModal<?= $device_id ?>">編輯</button>
          <a href="device_delete.php?id=<?= $device_id ?>" class="btn btn-sm btn-danger" onclick="return confirm('確定要刪除這個設備嗎？')">刪除</a>
        </td>
      </tr>

  <!-- 編輯設備 Modal -->
  <div class="modal fade" id="editDeviceModal<?= $device_id ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <form action="device_edit.php" method="POST">
            <!-- <input type="hidden" name="device_id" value="<?= $device_id ?>"> -->
            <input type="hidden" name="id" value="<?= $device_id ?>">
              <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">編輯設備</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
              </div>
              <div class="modal-body">
                <label>設備編號</label>
                <input type="text" name="device_code" class="form-control mb-2" value="<?= htmlspecialchars($row['device_code']) ?>" required>
                <label>啟用日期</label>
                <input type="date" name="purchase_date" class="form-control mb-2" value="<?= htmlspecialchars($row['purchase_date']) ?>" required>
                <label>備註</label>
                <textarea name="notes" class="form-control"><?= htmlspecialchars($row['notes']) ?></textarea>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">儲存變更</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- 新增設備 Modal -->
  <div class="modal fade" id="addDeviceModal" tabindex="-1">
    <div class="modal-dialog">
      <form method="POST" action="device_add.php">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">新增設備</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label>設備編號</label>
            <input type="text" name="device_code" class="form-control" required>
            <label class="mt-2">啟用日期</label>
            <input type="date" name="purchase_date" class="form-control" required>
            <label class="mt-2">備註</label>
            <textarea name="notes" class="form-control"></textarea>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
            <button class="btn btn-success" type="submit">新增設備</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- 新增檢驗 Modal -->
  <div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
      <form method="POST" action="upload.php" enctype="multipart/form-data">
        <input type="hidden" name="device_id" id="deviceIdInput">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">新增檢驗紀錄</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label>檢驗日期</label>
            <input type="date" name="inspection_date" class="form-control" required>
            <label class="mt-2">上傳報告</label>
            <input type="file" name="report_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
            <button class="btn btn-primary" type="submit">上傳紀錄</button>
          </div>
        </div>
      </form>
    </div>
  </div>

<!-- 年度報告下載區 -->
<div class="mb-4">
    <h5>📂 全站年度報告下載</h5>
    <ul>
      <?php
        $files = glob("uploads/annual_reports/*.pdf");
        foreach ($files as $file) {
          $name = basename($file);
          // echo "<li><a href='$file' target='_blank'>📄 $name</a></li>";
       
          echo "<li>
          <a href='$file' target='_blank'>📄 $name</a>
          <a href='delete_annual_file.php?file=$name' class='text-danger ms-2' onclick='return confirm(\"確定刪除這份報告？\")'>🗑️ 刪除</a>
          </li>";
          }
      ?>
    </ul>
  </div>

  <!-- 上傳年度報告表單 -->
  <form method="POST" action="upload_annual.php" enctype="multipart/form-data" class="mb-4 border p-3 bg-light rounded">
    <h5 class="mb-3">⬆️ 上傳年度報告</h5>
    <div class="row">
      <div class="col">
        <input type="file" name="annual_files[]" multiple class="form-control" accept=".pdf" required>
      </div>
      <div class="col-auto">
        <button class="btn btn-primary">上傳</button>
      </div>
    </div>
  </form>


  <!-- DataTables & Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function setDeviceId(id) {
      document.getElementById('deviceIdInput').value = id;
    }

    $(document).ready(function () {
      $('#deviceTable').DataTable({
        pageLength: 10,
        lengthMenu: [10, 25, 50],
        ordering: true,
        language: {
          search: "搜尋：",
          lengthMenu: "每頁顯示 _MENU_ 筆",
          info: "顯示第 _START_ 到 _END_ 筆，共 _TOTAL_ 筆",
          paginate: {
            first: "第一頁",
            last: "最後一頁",
            next: "下一頁",
            previous: "上一頁"
          }
        }
      });
    });
  </script>
</body>
</html>