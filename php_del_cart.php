<?php
session_start();
include('condb.php');

if (isset($_POST['equipmentCode'])) {
    $equipmentCode = $_POST['equipmentCode'];
    $employee_ID = $_POST['employee_ID'];

    $sqlDelete = "DELETE FROM tbl_carts WHERE equipment_Code = '$equipmentCode' AND employee_ID = '$employee_ID'";
    $params = array($equipmentCode, $employee_ID);
    $stmt = sqlsrv_query($conn, $sqlDelete, $params);

    if ($stmt) {
        $response = array('status' => 'success', 'message' => 'นำออกจากรายการเรียบร้อย');
        echo json_encode($response);
    } else {
        $errorInfo = sqlsrv_errors();
        $response = array('status' => 'error', 'message' => $errorInfo[0]['message']);
        echo json_encode($response);
    }
} else {
    $response = array('status' => 'error', 'message' => 'Invalid data received');
    echo json_encode($response);
}
?>
