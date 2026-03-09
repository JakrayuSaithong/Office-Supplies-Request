<?php 
session_start();
include('condb.php');

$edit_by = $_SESSION['employee_ID'];
$equipment_Name = $_POST['equipment_Name'];
$equipment_Code = $_POST['equipment_Code'];
$unit = $_POST['unit'];
$catalog_ID = $_POST['catalog'];
$date = date('Y-m-d H:i:s');
$ID = $_POST['equipment_ID'];


if($edit_by == '' || $edit_by == null){
    $response['status'] = 'error';
    $response['message'] = 'หมดเวลา Login กรุณาเข้าสู่ระบบใหม่';
    echo json_encode($response);
    return false;
}

$sqlUpdate = "UPDATE
    tbl_equipments
SET
    equipment_Name = '$equipment_Name',
    catalog_ID = '$catalog_ID',
    edit_by = '$edit_by',
    edit_date = '$date',
    unit = '$unit',
    equipment_Code = '$equipment_Code'
WHERE
    equipment_ID = '$ID'";

$resultUpdate = sqlsrv_query($conn, $sqlUpdate);

if ($resultUpdate) {
    echo json_encode(array("status" => "success", "msg" => "แก้ไขแคตตาล็อกแล้ว" ,"chk" => $sqlUpdate));
} else {
    echo json_encode(array("status" => "error", "msg" => "Update failed: " . sqlsrv_errors($conn)));
}


?>
