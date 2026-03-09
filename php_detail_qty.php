<?php
session_start();
include_once('condb.php');

// Validate and sanitize input
$jsonData = isset($_POST['jsonData']) ? $_POST['jsonData'] : '';
$onum = isset($_POST['onum']) ? $_POST['onum'] : '';

if (!empty($jsonData) && !empty($onum)) {
    // Decode JSON string
    $dataArray = json_decode($jsonData, true);

    // Create associative array for easy lookup
    $arr = [];
    foreach ($dataArray as $key) {
        $arr[$key['name']] = $key['qty'];
    }

    // Fetch order details from the database using parameterized query
    $sql_s = "SELECT order_detail FROM tbl_order_detail WHERE order_Number = ?";
    $params_s = array($onum);
    $result_s = sqlsrv_query($conn, $sql_s, $params_s);

    if ($result_s !== false) {
        $row_s = sqlsrv_fetch_array($result_s, SQLSRV_FETCH_ASSOC);

        // Decode the existing order details JSON
        $jsonData = $row_s['order_detail'];
        $jsonArray = json_decode($jsonData, true);

        // Update quantities in the existing JSON array
        foreach ($jsonArray as &$item) {
            $equipmentCode = $item["equipment_Code"];

            if (isset($arr[$equipmentCode])) {
                $item["Qty"] = $arr[$equipmentCode];
            }
        }

        $jsonUpdate = json_encode($jsonArray);

        // Update the order_detail using parameterized query
        $sql_u = "UPDATE tbl_order_detail SET order_detail = ? WHERE order_Number = ?";
        $params_u = array($jsonUpdate, $onum);
        $result_u = sqlsrv_query($conn, $sql_u, $params_u);

        if ($result_u) {
            $response = array('status' => 'success');
            echo json_encode($response);
        } else {
            $errorInfo = sqlsrv_errors();
            $response = array('status' => 'error', 'message' => htmlspecialchars($errorInfo[0]['message']));
            echo json_encode($response);
        }
    } else {
        // Handle query error
        die(print_r(sqlsrv_errors(), true));
    }
} else {
    // Handle missing or invalid input
    echo "Invalid input.";
}
?>
