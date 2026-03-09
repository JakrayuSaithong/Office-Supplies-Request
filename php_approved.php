<?php
session_start();
include_once("condb.php");

$receiving_by = $_POST['emp_ID'];
$order_ID = $_POST['order_ID'];
$status = $_POST['status'];
$approved_date = date("Y-m-d H:i:s");
$approved_by = $_SESSION['employee_ID'];

if($approved_by == '' || $approved_by == null){
    $response['status'] = 'error';
    $response['message'] = 'หมดเวลา Login กรุณาเข้าสู่ระบบใหม่';
    echo json_encode($response);
    return false;
}

try {
    if ($status == "A") {
        $sql = "UPDATE tbl_orders SET 
                approved_date = '$approved_date',
                approval = '$status'
                WHERE order_ID = '$order_ID'";
    } 
    elseif ($status == "C") {
        $sql = "UPDATE tbl_orders SET 
                approved_date = '$approved_date',
                approval = '$status'
                WHERE order_ID = '$order_ID'";
    }
    elseif ($status == "S") {
        $sql = "UPDATE tbl_orders SET
                manager_approve = '$approved_by',
                manager_status = '1',
                manager_date_approve = '$approved_date'
                WHERE order_ID = '$order_ID'";
    }
    elseif ($status == "SS") {
        $sql = "UPDATE tbl_orders SET
                manager_approve = '$approved_by',
                manager_status = '2',
                manager_date_approve = '$approved_date'
                WHERE order_ID = '$order_ID'";
    }
    elseif ($status == "Y") {
        $sql = "UPDATE tbl_orders SET 
                receiving_by = '$approved_by',
                receiving_date = '$approved_date',
                receiving = '$status',
                manager_status = '3'
                WHERE order_ID = '$order_ID'";
    }
    $result = sqlsrv_query($conn, $sql);

    if($result) {
        $order_Data = Orders_Number($order_ID);
        $codetoken =  Get_Token($order_Data['employee_ID']);

        // $datepermission = permiss_get_data();
        // $arrayPermis = json_decode($datepermission[0]['per_user'], true);

        $arrayPermis[] = '570311103';
        $arrayPermis[] = '640300021';

        if ($status == "A") {
            $titelnoti = "แจ้งเตือนขอเบิกของ (". $order_Data['order_Number'] .")";
            $message = "เลขที่เอกสาร ". $order_Data['order_Number'] ."\nได้รับอนุมัติเรียบร้อยแล้ว" . "\nเมื่อ " . $approved_date;

            $post = [
                'notify_type'    =>    'msg',
                'TOWEB'            =>    'TOWEB',
                'url'            =>    base64_encode("https://it.asefa.co.th/withdraw/requisition_detail.php?oid=". $order_Data['order_ID'] ."&page=approve_page&onum=". $order_Data['order_Number'] ."&token=". $codetoken['Users_Token']),
                'notify_title'    =>    $titelnoti,
                'notify_msg'    =>    $message,
                'user_username'    =>    $order_Data['employee_ID'],
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

            if($result){
                $titelnoti = "แจ้งเตือนขอเบิกของ (". $order_Data['order_Number'] .")";
                $message = "เลขที่เอกสาร ". $order_Data['order_Number'] ."\nได้รับอนุมัติให้สามารถเบิกของได้" . "\nเมื่อ " . $approved_date;

                foreach($arrayPermis as $key => $value){
                    $post = [
                        'notify_type'    =>    'msg',
                        'TOWEB'            =>    'TOWEB',
                        'url'            =>    base64_encode("https://it.asefa.co.th/withdraw/requisition_detail.php?oid=". $order_Data['order_ID'] ."&page=approve_page&onum=". $order_Data['order_Number'] ."&token=". $codetoken['Users_Token']),
                        'notify_title'    =>    $titelnoti,
                        'notify_msg'    =>    $message,
                        'user_username'    =>    $value,
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
            }
        } 
        elseif ($status == "C") {
            $titelnoti = "แจ้งเตือนขอเบิกของ (". $order_Data['order_Number'] .")";
            $message = "เลขที่เอกสาร ". $order_Data['order_Number'] ."\nไม่อนุมัติให้สามารถเบิกของได้" . "\nเมื่อ " . $approved_date;

            $post = [
                'notify_type'    =>    'msg',
                'TOWEB'            =>    'TOWEB',
                'url'            =>    base64_encode("https://it.asefa.co.th/withdraw/requisition_detail.php?oid=". $order_Data['order_ID'] ."&page=approve_page&onum=". $order_Data['order_Number'] ."&token=". $codetoken['Users_Token']),
                'notify_title'    =>    $titelnoti,
                'notify_msg'    =>    $message,
                'user_username'    =>    $order_Data['employee_ID'],
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
        elseif ($status == "S") {
            $titelnoti = "แจ้งเตือนขอเบิกของ (". $order_Data['order_Number'] .")";
            $message = "เลขที่เอกสาร ". $order_Data['order_Number'] ."\nได้รับการสั่งซื้อเรียบร้อยแล้ว" . "\nเมื่อ " . $approved_date;

            $post = [
                'notify_type'    =>    'msg',
                'TOWEB'            =>    'TOWEB',
                'url'            =>    base64_encode("https://it.asefa.co.th/withdraw/requisition_detail.php?oid=". $order_Data['order_ID'] ."&page=approve_page&onum=". $order_Data['order_Number'] ."&token=". $codetoken['Users_Token']),
                'notify_title'    =>    $titelnoti,
                'notify_msg'    =>    $message,
                'user_username'    =>    $order_Data['employee_ID'],
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
        elseif ($status == "SS") {
            $titelnoti = "แจ้งเตือนขอเบิกของ (". $order_Data['order_Number'] .")";
            $message = "เลขที่เอกสาร ". $order_Data['order_Number'] ."\nของเบิกมาส่งเรียบร้อยแล้ว" . "\nสามารถติดต่อรับของเบิกได้ที่ HR";

            $post = [
                'notify_type'    =>    'msg',
                'TOWEB'            =>    'TOWEB',
                'url'            =>    base64_encode("https://it.asefa.co.th/withdraw/requisition_detail.php?oid=". $order_Data['order_ID'] ."&page=approve_page&onum=". $order_Data['order_Number'] ."&token=". $codetoken['Users_Token']),
                'notify_title'    =>    $titelnoti,
                'notify_msg'    =>    $message,
                'user_username'    =>    $order_Data['employee_ID'],
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
        elseif ($status == "Y") {
            $titelnoti = "แจ้งเตือนขอเบิกของ (". $order_Data['order_Number'] .")";
            $message = "เลขที่เอกสาร ". $order_Data['order_Number'] ."\nได้รับของเบิกเรียบร้อยแล้ว" . "\nเมื่อ ". $approved_date;

            $post = [
                'notify_type'    =>    'msg',
                'TOWEB'            =>    'TOWEB',
                'url'            =>    base64_encode("https://it.asefa.co.th/withdraw/requisition_detail.php?oid=". $order_Data['order_ID'] ."&page=approve_page&onum=". $order_Data['order_Number'] ."&token=". $codetoken['Users_Token']),
                'notify_title'    =>    $titelnoti,
                'notify_msg'    =>    $message,
                'user_username'    =>    $order_Data['employee_ID'],
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
        
        echo json_encode(array('status' => 'success', 'stt_app' => $status ,'chk' => $sql));
    } else {
        $errors = sqlsrv_errors();
        error_log(print_r($errors, true), 0);
        echo json_encode(array('status' => 'error', 'message' => 'Error updating order status'));
    }
} catch (Exception $e) {
    error_log($e->getMessage(), 0);
    echo json_encode(array('status' => 'error', 'message' => 'Exception caught while updating order status'));
}
?>
