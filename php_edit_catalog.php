<?php 
session_start();
include('condb.php');

$catalog_Name = $_POST['catalog_Name'];
$date = date('Y-m-d H:i:s');
$ID = isset($_POST['catalog_ID']) ? $_POST['catalog_ID'] : " ";
$edit_by = $_SESSION['employee_ID'];

if($edit_by == '' || $edit_by == null){
    $response['status'] = 'error';
    $response['message'] = 'หมดเวลา Login กรุณาเข้าสู่ระบบใหม่';
    echo json_encode($response);
    return false;
}

$sqlUpdate = "UPDATE tbl_catalogs SET
catalog_Name = '$catalog_Name',
edit_by = '$edit_by',
edit_date = '$date'
WHERE catalog_ID = '$ID'";

// echo $sqlUpdate;
if ($resultUpdate = sqlsrv_query($conn, $sqlUpdate)) {
    echo json_encode(array("status" => "success", "msg" => "แก้ไขประเภทแล้ว"));
} else {
    $errors = sqlsrv_errors();
    foreach ($errors as $error) {
        echo "SQLSTATE: " . $error['SQLSTATE'] . "<br />";
        echo "Code: " . $error['code'] . "<br />";
        echo "Message: " . $error['message'] . "<br />";
    }
    echo json_encode(array("status" => "error", "msg" => "Update failed"));
}



?>
