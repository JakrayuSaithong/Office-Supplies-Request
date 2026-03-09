<?php
session_start();
include('condb.php'); 

if (isset($_POST['employee_ID'])) {
    $employee_ID = $_POST['employee_ID'];

    // Perform your database operations here, for example, deleting records
    $sqlDelete = "DELETE FROM tbl_carts WHERE employee_ID = '$employee_ID'";
    $resultDelete = sqlsrv_query($conn, $sqlDelete);

    if ($resultDelete) {
        $response['status'] = 'success';
        $response['message'] = 'Cart canceled successfully.';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error canceling the cart.';
    }

    echo json_encode($response);
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request.';
    echo json_encode($response);
}
?>
