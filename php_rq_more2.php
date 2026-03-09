<?php
session_start();
include_once("condb.php");

$onum = $_POST['onum'];
$emp_ID = $_POST['emp_ID'];
$edit_date = date("Y-m-d H:i:s");

$sqlcart = "SELECT equipment_Code, Qty 
FROM tbl_carts 
WHERE employee_ID = '$emp_ID'";
$resultcart = sqlsrv_query($conn, $sqlcart);
$order_detail = array();
while ($rowcart = sqlsrv_fetch_array($resultcart, SQLSRV_FETCH_ASSOC)) {
    $order_detail[] = array(
        'equipment_Code' => $rowcart['equipment_Code'],
        'Qty' => $rowcart['Qty'],
        'Q_true' => $rowcart['Qty']
    );
}

$detailJson = json_encode($order_detail);

if (empty($order_detail)) {
    echo json_encode(array('status' => 'error', 'message' => 'ไม่พบรายการเบิก (No items in requisition)'));
    exit;
}

$sqlReq_add = "UPDATE tbl_order_detail SET 
order_detail = '$detailJson',
edit_by = '$emp_ID',
edit_date = '$edit_date'
WHERE order_Number = '$onum' ";

if ($resulReq_add = sqlsrv_query($conn, $sqlReq_add)) {
    $sqlDeleteCart = "DELETE FROM tbl_carts WHERE employee_ID = '$emp_ID'";
    $resultDeleteCart = sqlsrv_query($conn, $sqlDeleteCart);

    echo json_encode(array('status' => 'success', 'onum' => $onum));
}
