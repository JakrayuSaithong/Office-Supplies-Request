<?php 
session_start();
include_once("condb.php");

$eq_Code = $_POST['eq_Code'];
$Q_true = $_POST['q_true'];
$onum = $_POST['onum'];
// echo json_encode(array("A" => $eq_Code,"B" => $q_true, "C" => $onum));

$select_detail = "SELECT order_detail FROM tbl_order_detail WHERE order_Number = '$onum'";
$q_detail = sqlsrv_query($conn,$select_detail);
$r_detail = sqlsrv_fetch_array($q_detail,SQLSRV_FETCH_ASSOC);

$detail_array =  json_decode($r_detail['order_detail'],true);

foreach($detail_array as &$item){
    if($item['equipment_Code'] == $eq_Code){
        $item['Q_true'] = $Q_true;
        break;
    }
}

$detail_json = json_encode($detail_array);
// echo $detail_json;
$update = "UPDATE tbl_order_detail SET order_detail = '$detail_json' WHERE order_Number = '$onum'";

if($q_update = sqlsrv_query($conn,$update)){
    echo "success";
}



// print_r($detail_array);

?>