<?php 
session_start();
include_once("condb.php");

$itemId = $_POST['id'];

$sql = "SELECT * FROM tbl_equipments WHERE equipment_ID = ?";
$params = array($itemId);
$stmt = sqlsrv_query($conn, $sql, $params);
$result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);


// ส่งข้อมูลในรูปแบบ JSON
echo json_encode($result);

?>