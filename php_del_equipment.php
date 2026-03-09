<?php 
session_start();
include('condb.php');
$ID = $_GET['equipment_id'];
$date = date('Y-m-d H:i:s');
$edit_by = $_SESSION['employee_ID'];

if($edit_by == '' || $edit_by == null){
    $response['status'] = 'error';
    $response['message'] = 'หมดเวลา Login กรุณาเข้าสู่ระบบใหม่';
    echo json_encode($response);
    return false;
}

$sql = "UPDATE tbl_equipments
SET status = '0',
edit_by = '$edit_by',
edit_date = '$date' 
WHERE equipment_ID = '$ID'";

$result = sqlsrv_query($conn, $sql);

if ($result) {
    header("Refresh:0; equipment.php");
}


?>
