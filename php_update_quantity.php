<?php
include('condb.php');

if (isset($_POST['equipment_Code']) && isset($_POST['newQty'])) {
    $equipment_Code = $_POST['equipment_Code'];
    $newQty = $_POST['newQty'];
    $employee_ID = $_POST['employee_ID'];

    $sqlUpdate = "UPDATE tbl_carts SET Qty = '$newQty' WHERE equipment_Code = '$equipment_Code' AND employee_ID = '$employee_ID'";
    $params = array($newQty, $equipment_Code);
    $stmt = sqlsrv_query($conn, $sqlUpdate, $params);

    if ($stmt) {
        $response = array('status' => 'success');
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
