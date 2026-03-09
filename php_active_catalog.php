<?php 
include('condb.php');
$ID = $_GET['catalog_ID'];
$status = $_GET['status'];
$date = date('Y-m-d H:i:s');

$sql = "
    UPDATE tbl_catalogs 
    SET 
        active = '$status',
        edit_by = 'Test User',
        edit_date = '$date' 
    WHERE catalog_ID = '$ID'";

$result = sqlsrv_query($conn, $sql);

if ($result) {
    header("Refresh:0; catalog.php");
}


?>
