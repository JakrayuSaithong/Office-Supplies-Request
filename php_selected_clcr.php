<?php 
session_start();
include_once("condb.php");

$catalog = $_POST['catalog'];
$category = $_POST['category'];


$sqlCr = "SELECT category_ID, category_Name FROM tbl_categorys WHERE status = 1 AND catalog_ID = '$catalog' ";
$qCr = sqlsrv_query($conn, $sqlCr);
$optionCr = array();
while ($rowCr = sqlsrv_fetch_array($qCr, SQLSRV_FETCH_ASSOC)) {
    $selectedCr = ($rowCr['category_ID'] == $category) ? "selected" : "";
    $optionCr[] = '<option '.$selectedCr.' value="'.$rowCr['category_ID'].'">'.$rowCr['category_Name'].'</option>';
}

echo json_encode(array("category" => $optionCr));
?>
