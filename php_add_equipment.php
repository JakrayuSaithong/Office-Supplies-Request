<?php 
session_start();
include('condb.php');

$add_by = $_SESSION['employee_ID'];
$equipment_Name = $_POST['equipment_Name'];
$unit = $_POST['unit'];
$date = date('Y-m-d H:i:s');
$catalog_ID = $_POST['catalog'];
$equipment_Code = $_POST['equipment_Code'];

if($add_by == '' || $add_by == null){
    $response['status'] = 'error';
    $response['message'] = 'หมดเวลา Login กรุณาเข้าสู่ระบบใหม่';
    echo json_encode($response);
    return false;
}

$sqlInsert = "INSERT INTO tbl_equipments (equipment_Name, add_by, add_date, catalog_ID, status, unit, equipment_Code) 
VALUES ('$equipment_Name', '$add_by', '$date', '$catalog_ID', '1', '$unit', '$equipment_Code')";

$resultInsert  = sqlsrv_query($conn, $sqlInsert);

if ($resultInsert) {
    echo json_encode(array("status" => "success", "msg" => "เพิ่มวัสดุแล้ว"));
} else {
    echo json_encode(array("status" => "error", "msg" => "Insert failed: " . sqlsrv_errors($conn)));
}


?>
