<?php 
session_start();
include('condb.php');
$add_by = $_SESSION['employee_ID'];
$category_Name = $_POST['category_Name'];
$catalog = $_POST['catalog_ID'];
$date = date('Y-m-d H:i:s');

if($add_by == '' || $add_by == null){
    $response['status'] = 'error';
    $response['message'] = 'หมดเวลา Login กรุณาเข้าสู่ระบบใหม่';
    echo json_encode($response);
    return false;
}

$sqlInsert = "INSERT INTO tbl_categorys (category_Name, catalog_ID, add_by, add_date, status) VALUES ('$category_Name', '$catalog', '$add_by', '$date','1')";

$resultInsert  = sqlsrv_query($conn, $sqlInsert);

if ($resultInsert) {
    echo json_encode(array("status" => "success", "msg" => "เพิ่มหมวดสำเร็จ"));
} else {
    echo json_encode(array("status" => "error", "msg" => "Insert failed: " . sqlsrv_errors($conn)));
}



?>
