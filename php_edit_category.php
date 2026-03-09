<?php 
session_start();
include('condb.php');

$edit_by = $_SESSION['employee_ID'];
$category_Name = $_POST['category_Name'];
$date = date('Y-m-d H:i:s');
$category_ID = $_POST['category_ID'];
$catalog_ID = $_POST['catalog_ID'];

if($edit_by == '' || $edit_by == null){
    $response['status'] = 'error';
    $response['message'] = 'หมดเวลา Login กรุณาเข้าสู่ระบบใหม่';
    echo json_encode($response);
    return false;
}

$sqlUpdate = "UPDATE tbl_categorys SET
category_Name = '$category_Name',
catalog_ID = '$catalog_ID',
edit_by = '$edit_by',
edit_date = '$date'
WHERE category_ID = '$category_ID'";

$resultUpdate = sqlsrv_query($conn, $sqlUpdate);

if ($resultUpdate) {
    echo json_encode(array("status" => "success", "msg" => "แก้ไขแคตตาล็อกแล้ว" , "chk" => $sqlUpdate));
} else {
    echo json_encode(array("status" => "error", "msg" => "Update failed: " . print_r(sqlsrv_errors($conn), true)));
}
exit;
?>
