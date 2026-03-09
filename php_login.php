<?php 
session_start();
include('condb.php');

if(isset($_POST['employee_ID'])){

    $employee_ID = $_POST['employee_ID'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM tbl_employees WHERE employee_ID = '$employee_ID'";
    $result = sqlsrv_query($conn,$sql);
    if($result){
        $row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC);
        if($password == $row['employee_ID']){
            $_SESSION['employee_ID'] = $row['employee_ID'];
            $_SESSION['employee_Name'] = $row['employee_Name'];
            $_SESSION['department'] = $row['department'];
            $_SESSION['division'] = $row['division'];
            $_SESSION['level'] = $row['level'];
            $level = $row['level'];

            echo json_encode(array("status" => "success", "msg" => "ยินดีต้อนรับ,เข้าสู่ระบบสำเร็จ!", "level" => $level));
        }else{
            echo json_encode(array("status" => "error", "msg" => "รหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง"));
        }
    }else{
        echo json_encode(array("status" => "error", "msg" => "ไม่พบรหัสพนักงานนี้ในระบบ"));
    }

}
?>