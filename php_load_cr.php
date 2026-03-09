<?php 
include_once("condb.php");

$catalog_ID = $_POST['catalog_ID'];
if($catalog_ID != "0"){
    $sql = "SELECT * FROM tbl_categorys WHERE catalog_ID = '$catalog_ID'";
    $result = sqlsrv_query($conn,$sql);
    
    while($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)){
        echo '<option value="'.$row['category_ID'].'">'.$row['category_Name'].'</option>';
    }
}else{
    echo '<option value="0">* โปรดระบุประเภท</option>';
}

?>