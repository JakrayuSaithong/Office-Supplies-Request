<?php
session_start();
include_once('condb.php');
include_once('function.php');

$date = date("Y-m-d H:i:s");
$admin_ID = $_SESSION['employee_ID'];
$admin_Name = $_SESSION['employee_Name'];
$employee_ID = $_POST['employee_ID'];
$items_json = $_POST['items'];

if (empty($admin_ID)) {
    echo json_encode(['status' => 'error', 'message' => 'หมดเวลา Login กรุณาเข้าสู่ระบบใหม่']);
    exit;
}

$items = json_decode($items_json, true);
if (empty($items)) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบรายการเบิก']);
    exit;
}

// Get employee name from MySQL
$empData = showname($employee_ID);
$order_Name = $empData['site_f_366'] ?? '';

if (empty($order_Name)) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลพนักงาน']);
    exit;
}

// Calculate Order Number
$y = date("y") + 43;
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

$order_detail_json = json_encode($items);

// Insert Order with note=1, approval=A (auto-approved), manager_approve = employee_ID (selected person)
$sqlOrder = "INSERT INTO tbl_orders (order_Number, employee_ID, order_Name, order_date, approval, approved_by, approved_date, receiving, receiving_by, receiving_date, add_by, add_date, edit_by, edit_date, status, manager_approve, manager_status, manager_date_approve, note)
VALUES (?, ?, ?, ?, 'P', ?, ?, '', '', '', ?, ?, '', '', '1', '', '0', '', 1)";
$params = [$order_Number, $employee_ID, $order_Name, $date, $employee_ID, $date, $admin_Name, $date];
$stmtOrder = sqlsrv_query($conn, $sqlOrder, $params);

if (!$stmtOrder) {
    echo json_encode(['status' => 'error', 'message' => 'บันทึก Order ไม่สำเร็จ']);
    exit;
}

// Insert Order Detail
$sqlDetail = "INSERT INTO tbl_order_detail (order_Number, order_detail, employee_ID, add_by, add_date, edit_by, edit_date)
VALUES (?, ?, ?, ?, ?, '', '')";
$paramsDetail = [$order_Number, $order_detail_json, $employee_ID, $admin_Name, $date];
$stmtDetail = sqlsrv_query($conn, $sqlDetail, $paramsDetail);

if ($stmtDetail) {
    // Send notification — wrapped in try-catch so DB success is always returned
    try {
        $sql_order_max = "SELECT TOP 1 order_Number, order_ID FROM tbl_orders ORDER BY order_ID DESC";
        $query = sqlsrv_query($conn, $sql_order_max);
        $rowOrder = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);

        if ($rowOrder['order_Number'] != '') {
            $titelnoti = "แจ้งเตือนเบิกเพิ่มเติม (" . $rowOrder['order_Number'] . ")";
            $message = $admin_Name . "\nได้สร้างเอกสารเบิกเพิ่มเติมให้คุณ" . "\nกรุณาตรวจสอบรายการ" . "\nเมื่อ " . $date;

            $DataE = encryptIt(json_encode([
                "auth_user_name" => $employee_ID,
                "date_U" => time(),
                "FromApp" => "Noti"
            ], JSON_UNESCAPED_UNICODE));

            $post = [
                'notify_type'       => 'msg',
                'TOWEB'             => 'TOWEB',
                'url'               => base64_encode("https://it.asefa.co.th/withdraw/requisition_detail.php?oid=" . $rowOrder['order_ID'] . "&page=approve_page&onum=" . $rowOrder['order_Number'] . "&DataE=" . $DataE),
                'notify_title'      => $titelnoti,
                'notify_msg'        => $message,
                'user_username'     => $employee_ID,
                'Notification_Mode' => 'other'
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://innovation.asefa.co.th/applications/notification/push_notification');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($ch);
            curl_close($ch);
        }
    } catch (\Throwable $notiErr) {
        error_log("Notification error (admin save): " . $notiErr->getMessage(), 0);
    }

    echo json_encode(['status' => 'success', 'message' => 'บันทึกรายการเบิกเพิ่มเติมแล้ว', 'order_Number' => $order_Number]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'บันทึกรายละเอียดไม่สำเร็จ']);
}
