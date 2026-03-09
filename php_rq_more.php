<?php
session_start();
include_once('condb.php');

$onum = $_POST['order_Number'];
$emp = $_SESSION['employee_ID'];

if ($emp == '' || $emp == null) {
    $response['status'] = 'error';
    $response['message'] = 'หมดเวลา Login กรุณาเข้าสู่ระบบใหม่';
    echo json_encode($response);
    return false;
}

$sql_detail = "SELECT order_detail FROM tbl_order_detail WHERE order_Number = '$onum'";
$result_detail = sqlsrv_query($conn, $sql_detail);
$row_detail = sqlsrv_fetch_array($result_detail, SQLSRV_FETCH_ASSOC);
// echo $row_detail['order_detail'];

$detail_arr = json_decode($row_detail['order_detail'], true);
// print_r($detail_arr) ;

$sqlDelete = "DELETE FROM tbl_carts WHERE employee_ID = '$emp'";
sqlsrv_query($conn, $sqlDelete);

foreach ($detail_arr as $keyd) {
    $equipment_Code = $keyd['equipment_Code'];
    $Qty = $keyd['Qty'];
    // echo $Qty;
    $sqlInsert = "INSERT INTO tbl_carts (employee_ID, equipment_Code, Qty) VALUES ('$emp', '$equipment_Code', '$Qty')";
    $resultInsert = sqlsrv_query($conn, $sqlInsert);
    // echo $sqlInsert;
}
if ($resultInsert) {
    echo json_encode(array('status' => 'success'));
}
