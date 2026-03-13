<?php
session_start();
include_once('condb.php');

$onum = $_POST['onum'];
$items = json_decode($_POST['items'], true);
$emp_ID = $_SESSION['employee_ID'];

if (empty($emp_ID)) {
    echo json_encode(['status' => 'error', 'message' => 'หมดเวลา Login']);
    exit;
}

if (empty($onum) || empty($items)) {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ถูกต้อง']);
    exit;
}

// Read existing order_detail to preserve Q_true values
$sql_existing = "SELECT order_detail FROM tbl_order_detail WHERE order_Number = '$onum'";
$result_existing = sqlsrv_query($conn, $sql_existing);
$row_existing = sqlsrv_fetch_array($result_existing, SQLSRV_FETCH_ASSOC);

$existing_qtrue = [];
if ($row_existing && !empty($row_existing['order_detail'])) {
    $existing_items = json_decode($row_existing['order_detail'], true);
    if (is_array($existing_items)) {
        foreach ($existing_items as $ei) {
            $existing_qtrue[$ei['equipment_Code']] = $ei['Q_true'];
        }
    }
}

// Build new order_detail array, preserving Q_true
$new_detail = [];
foreach ($items as $item) {
    $eqCode = $item['equipment_Code'];
    $qty = intval($item['Qty']);
    if ($qty <= 0) $qty = 1;

    // Preserve Q_true if it exists, otherwise default to Qty
    $qtrue = isset($existing_qtrue[$eqCode]) ? $existing_qtrue[$eqCode] : strval($qty);

    $new_detail[] = [
        'equipment_Code' => $eqCode,
        'Qty' => strval($qty),
        'Q_true' => $qtrue
    ];
}

$detailJson = json_encode($new_detail);
$edit_date = date("Y-m-d H:i:s");

$sqlUpdate = "UPDATE tbl_order_detail SET 
    order_detail = '$detailJson',
    edit_by = '$emp_ID',
    edit_date = '$edit_date'
    WHERE order_Number = '$onum'";

if (sqlsrv_query($conn, $sqlUpdate)) {
    echo json_encode(['status' => 'success', 'onum' => $onum]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'บันทึกไม่สำเร็จ']);
}
?>
