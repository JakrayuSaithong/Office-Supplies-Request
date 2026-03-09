<?php
include('condb.php');

if (isset($_POST['OrderId'])) {
    $OrderId = $_POST['OrderId'];

    $sqlDelete = "UPDATE tbl_orders SET 
                status = '0'
                WHERE order_Number = '$OrderId'";
    $stmt = sqlsrv_query($conn, $sqlDelete);

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
