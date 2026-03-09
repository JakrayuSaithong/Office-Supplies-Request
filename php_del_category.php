<?php 
include('condb.php');
$ID = $_GET['category_ID'];
$date = date('Y-m-d H:i:s');

$sql = "UPDATE tbl_categorys
SET status = '0',
edit_by = 'Test User',
edit_date = '$date' 
WHERE category_ID = '$ID'";

$result = sqlsrv_query($conn, $sql);

if ($result) {
    header("Refresh:0; category.php");
}


?>
