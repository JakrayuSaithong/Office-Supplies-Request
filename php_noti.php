<?php
    ob_clean();
    header_remove();
    header("Content-type: application/json; charset=utf-8");
    session_start();
    include_once('condb.php');
    include_once('function.php');

    $order_ID = $_POST['order_ID'];
    $order_Number = $_POST['order_Number'];
    $order_Name = $_POST['order_Name'];
    $order_date = $_POST['order_date'];
    $approved_by = $_POST['approved_by'];

    // $codetoken =  Get_Token($approved_by);

    $DataE = encryptIt(json_encode([
        "auth_user_name" => $approved_by,
        "date_U" => time(),
        "FromApp" => "Noti"
    ], JSON_UNESCAPED_UNICODE));

    $titelnoti = "แจ้งเตือนขอเบิกของ (". $order_Number .")";
    $message = $order_Name . "\nได้สร้างเอกสารเพื่อขออนุมัติเบิกของ" . "\nเมื่อ " . $order_date;

    $post = [
        'notify_type'    =>    'msg',
        'TOWEB'            =>    'TOWEB',
        'url'            =>    base64_encode("https://it.asefa.co.th/withdraw/requisition_detail.php?oid=". $order_ID ."&page=approve_page&onum=". $order_Number ."&DataE=". $DataE),
        'notify_title'    =>    $titelnoti,
        'notify_msg'    =>    $message,
        'user_username'    =>    $approved_by,
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
    echo $result;

    exit();
?>