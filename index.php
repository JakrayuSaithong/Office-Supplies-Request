<?php
if (isset($_GET['DataE'])) {

    $JsonText = decryptIt($_GET['DataE']);
	$JSOnArr = json_decode($JsonText, true);
	$now = time();

    $dataTime = (is_array($JSOnArr) && isset($JSOnArr['date_U'])) ? (int)$JSOnArr['date_U'] : 0;
	if (($now - $dataTime) > 3600) {
		session_unset();
		session_destroy();

		echo "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Session Expired</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'หมดเวลาการใช้งาน',
                text: 'Session หมดอายุแล้ว กรุณาเข้าสู่ระบบใหม่',
                confirmButtonText: 'ตกลง',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                window.close();
                window.location.href = 'about:blank';
            });
        </script>
        </body>
        </html>
        ";
		exit();
	}

    if ($JSOnArr['auth_user_name']) {
        $Users_Username = $JSOnArr['auth_user_name'];

        $get_emp_detail = "https://innovation.asefa.co.th/applications/ds/emp_list_code";
		$chs = curl_init();
		curl_setopt($chs, CURLOPT_URL, $get_emp_detail);
		curl_setopt($chs, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($chs, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($chs, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($chs, CURLOPT_POST, 1);
		curl_setopt($chs, CURLOPT_POSTFIELDS, ["emp_code" => $Users_Username]);
		$emp = curl_exec($chs);
		curl_close($chs);

		$empdata   =   json_decode($emp);
        $_SESSION['employee_ID'] = $empdata[0]->emp_code;
        $_SESSION['FName'] = $empdata[0]->emp_FirstName;
        $_SESSION['LName'] = $empdata[0]->emp_LastName;
        $_SESSION['employee_Name'] = $empdata[0]->emp_FirstName . " " . $empdata[0]->emp_LastName;
        $_SESSION['DataE']	=	$_GET['DataE'];
    }
}
//    else {
//     echo "<script>window.location.href = 'https://it.asefa.co.th/authen/signin.php?url=" . base64_encode("https://it.asefa.co.th/dew/AsefaTest/Admin") . "&uri=index.php';</script>";
//     exit();
//    }
include('header.php');
?>
<div class="d-flex align-items-center py-7">
    <div class="card mb-3 m-auto " style="max-width: 720px;">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="image/login.png" class="img-fluid rounded-start" width="500px">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h1 class="text-secondary fw-bold text-center mt-3">เข้าสู่ระบบ</h1>
                    <form action="php_login.php" method="POST" id="loginform">
                        <div class="form-floating mt-4">
                            <input type="text" name="employee_ID" class="form-control rounded-4 border-0 border-bottom border-2 border-info" id="floatingInput" placeholder="" required>
                            <label for="floatingInput">รหัสพนักงาน</label>
                        </div>
                        <div class="form-floating mt-3">
                            <input type="password" name="password" class="form-control rounded-4 border-0 border-bottom border-2 border-info" id="floatingInput" placeholder="" required>
                            <label for="floatingInput">รหัสผ่าน</label>
                        </div>
                        <button type="submit" class="btn btn-info w-100 mt-3 rounded-5">Go!</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>
<script>
    $(document).ready(function() {
        $("#loginform").submit(function(e) {
            e.preventDefault();

            let catalogURL = $(this).attr("action");
            let reqMethod = $(this).attr("method");
            let formData = $(this).serialize();

            $.ajax({
                url: catalogURL,
                type: reqMethod,
                data: formData,

                success: function(data) {
                    let result = JSON.parse(data);
                    console.log(data);
                    if (result.status == "success") {
                        console.log("Success", result)
                        Swal.fire({
                            title: "สำเร็จ!",
                            text: result.msg,
                            icon: result.status,
                            timer: 1000,
                            showConfirmButton: false
                        }).then(function() {
                            if (result.level == "0") {
                                window.location.href = "approve_page.php";
                            } else if (result.level == "1") {
                                window.location.href = "cart.php";
                            }
                        })
                    } else {
                        console.log("Error", result)
                        Swal.fire("ล้มเหลว!", result.msg, result.status).then(function() {
                            window.location.reload();
                        })
                    }
                }
            })
        })
    })
</script>