<?php 
session_start();
include('condb.php');

$catalog_Name = $_POST['catalog_Name'];
$date = date('Y-m-d H:i:s');
$add_by = $_SESSION['employee_ID'];

if($add_by == '' || $add_by == null){
    $response['status'] = 'error';
    $response['message'] = 'หมดเวลา Login กรุณาเข้าสู่ระบบใหม่';
    echo json_encode($response);
    return false;
}

$sqlInsert = "INSERT INTO tbl_catalogs (catalog_Name, add_by, add_date, status) VALUES ('$catalog_Name', '$add_by', '$date', '1')";

$resultInsert  = sqlsrv_query($conn, $sqlInsert);

if ($resultInsert) {
    echo json_encode(array("status" => "success", "msg" => "เพิ่มแคตตาล็อกแล้ว"));
} else {
    echo json_encode(array("status" => "error", "msg" => "Insert failed"));
}
?>
