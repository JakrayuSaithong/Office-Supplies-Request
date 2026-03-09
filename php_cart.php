<?php
session_start();
include('condb.php');

if (isset($_POST['equipment_Code']) && isset($_POST['action']) && $_POST['action'] == 'add') {
    $equipment_Code = $_POST['equipment_Code'];
    $date = date('Y-m-d H:i:s');
    $emp = $_SESSION['employee_ID'];

    if($emp == '' || $emp == null){
        $response['status'] = 'error';
        $response['message'] = 'หมดเวลา Login กรุณาเข้าสู่ระบบใหม่';
        echo json_encode($response);
        return false;
    }

    $sqlCheck = "SELECT * FROM tbl_carts WHERE employee_ID = '$emp' AND equipment_Code = '$equipment_Code'";
    $resultCheck = sqlsrv_query($conn, $sqlCheck);
    $rowCheck = sqlsrv_fetch_array($resultCheck, SQLSRV_FETCH_ASSOC);

    if (!$rowCheck) {
        // If the item is not in the cart, add it
        $sqlInsert = "INSERT INTO tbl_carts (employee_ID, equipment_Code, Qty, add_date) VALUES ('$emp', '$equipment_Code', 1 , '$date')";
        $resultInsert = sqlsrv_query($conn, $sqlInsert);

        if ($resultInsert) {
            $response['status'] = 'success';
            $response['message'] = 'เพิ่มเข้ารายการเรียบร้อย!';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error adding item to the cart.';
        }
    } else {
        // If the item is already in the cart, update the quantity
        $sqlUpdate = "UPDATE tbl_carts SET Qty = Qty + 1 , edit_date = '$date' WHERE employee_ID = '$emp' AND equipment_Code = '$equipment_Code'";
        $resultUpdate = sqlsrv_query($conn, $sqlUpdate);

        if ($resultUpdate) {
            $response['status'] = 'success';
            $response['message'] = 'อัปเดตรายการเรียบร้อย!';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error updating item quantity in the cart.';
        }
    }

    echo json_encode($response);
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request.';
    echo json_encode($response);
}
