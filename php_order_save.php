<?php
session_start();
include_once('condb.php');
include_once('function.php');
$date = date("Y-m-d H:i:s");
$order_Name = $_SESSION['employee_Name'];
$employee_ID = $_POST['employee_ID'];
$manager_ID = $_POST['manager_ID'];

// if($order_Name == '' || $order_Name == null){
//     $response['status'] = 'error';
//     $response['message'] = 'หมดเวลา Login กรุณาเข้าสู่ระบบใหม่';
//     echo json_encode($response);
//     return false;
// }

// 1. Fetch Cart ITEMS First
$sqlcart = "SELECT equipment_Code, Qty 
FROM tbl_carts 
WHERE tbl_carts.employee_ID = '$employee_ID'";
$resultcart = sqlsrv_query($conn, $sqlcart);
$order_detail = array();
while ($rowcart = sqlsrv_fetch_array($resultcart, SQLSRV_FETCH_ASSOC)) {
    $order_detail[] = array(
        'equipment_Code' => $rowcart['equipment_Code'],
        'Qty' => $rowcart['Qty'],
        'Q_true' => $rowcart['Qty']
    );
}
$order_detail_json = json_encode($order_detail);

// 2. Check if Empty
if (empty($order_detail)) {
    $response = array('status' => 'error', 'message' => 'ไม่พบรายการเบิก (No items in requisition)');
    echo json_encode($response);
    exit;
}

// 3. Calculate Order Number
$y = date("y") + 43;
$m = date("m");

$run_num = "SELECT MAX(order_Number) as Max_num FROM tbl_orders";
$q_run = sqlsrv_query($conn, $run_num);
$r_num = sqlsrv_fetch_array($q_run, SQLSRV_FETCH_ASSOC);

$last_order_number = $r_num['Max_num'];
$last_year = substr($last_order_number, 0, 2);

$current_year = date("y") + 43;

if ($last_order_number == "" || $current_year != $last_year) {
    $order_Number = $current_year . "0001";
} else {
    $new_number = substr($last_order_number, 2) + 1;
    $order_Number = $current_year . sprintf("%04d", $new_number);
}

// 4. Insert Order
$sqlorderSave = "INSERT INTO tbl_orders (order_Number, employee_ID, order_Name, order_date, approval, approved_by, approved_date, receiving, receiving_by, receiving_date, add_by, add_date, edit_by, edit_date, status, manager_approve, manager_status, manager_date_approve) 
VALUES ('$order_Number', '$employee_ID', '$order_Name', '$date', 'P', '$manager_ID', '', '', '', '', '$order_Name', '$date', '', '', '1', '', '0', '')";
$resultorder = sqlsrv_query($conn, $sqlorderSave);

// 5. Insert Detail
$sqlcartSave = "INSERT INTO tbl_order_detail (order_Number, order_detail, employee_ID, add_by, add_date, edit_by, edit_date) 
VALUES ('$order_Number', '$order_detail_json', '$employee_ID', '$order_Name', '$date', '', '')";
$resultSave = sqlsrv_query($conn, $sqlcartSave);


if ($resultSave) {

    $sqlDeleteCart = "DELETE FROM tbl_carts WHERE employee_ID = '$employee_ID'";
    $resultDeleteCart = sqlsrv_query($conn, $sqlDeleteCart);

    $sql_order_max = "
        select TOP 1 order_Number, order_ID
        from tbl_orders
        order by order_ID DESC
    ";
    $query = sqlsrv_query($conn, $sql_order_max);
    $rowSelectOrder = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);

    // $codetoken =  Get_Token($manager_ID);

    if ($rowSelectOrder['order_Number'] != '') {
        $titelnoti = "แจ้งเตือนขอเบิกของ (" . $rowSelectOrder['order_Number'] . ")";
        $message = $order_Name . "\nได้สร้างเอกสารเพื่อขออนุมัติเบิกของ" . "\nเมื่อ " . $date;

        $DataE = encryptIt(json_encode([
            "auth_user_name" => $manager_ID,
            "date_U" => time(),
            "FromApp" => "Noti"
        ], JSON_UNESCAPED_UNICODE));

        $post = [
            'notify_type'    =>    'msg',
            'TOWEB'            =>    'TOWEB',
            'url'            =>    base64_encode("https://it.asefa.co.th/withdraw-test/requisition_detail.php?oid=" . $rowSelectOrder['order_ID'] . "&page=approve_page&onum=" . $rowSelectOrder['order_Number'] . "&DataE=" . $DataE),
            'notify_title'    =>    $titelnoti,
            'notify_msg'    =>    $message,
            'user_username'    =>    $manager_ID,
            'Notification_Mode' =>    'other'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://innovation.asefa.co.th/applications/notification/push_notification');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    $response = array('status' => 'success', 'message' => "บันทึกรายการเบิกแล้ว", 'data' => $post);
    echo json_encode($response);
} else {
    $response = array('status' => 'error', 'message' => $sqlcartSave);
    echo json_encode($response);
}
