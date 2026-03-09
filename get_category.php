<?php
session_start();
include_once("condb.php");

$itemId = $_POST['id'];

$sql = "SELECT * FROM tbl_categorys WHERE category_ID = '$itemId'";
$stmt = sqlsrv_query($conn, $sql);

$result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);


// ส่งข้อมูลในรูปแบบ JSON
echo json_encode($result);
// echo $sql;
