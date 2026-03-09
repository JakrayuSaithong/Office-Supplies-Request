<?php
session_start();
// if (empty($_SESSION['employee_ID'])) {
//     header('location:index.php');
//     // exit();
// }

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

        // if (
        //     $_SESSION['employee_ID'] == '660500122' 
        //     || $_SESSION['employee_ID'] == '570311103'
        //     || $_SESSION['employee_ID'] == '650600189'
        //     || $_SESSION['employee_ID'] == '540411127'
        //     || $_SESSION['employee_ID'] == '490711044'
        //     || $_SESSION['employee_ID'] == '640300021'
        //     ) { //$_SESSION['employee_ID'] == '640300021'
        //     $_SESSION['level'] = '0';
        // } else {
        //     $_SESSION['level'] = '1';
        // }
    }
}
include('header.php');

$datepermission = permiss_get_data();
$arrayPermis = json_decode($datepermission[0]['per_user'], true);

if (in_array($_SESSION['employee_ID'], $arrayPermis)) {
    $_SESSION['level'] = '0';
} else {
    $_SESSION['level'] = '1';
}

if ($_GET['page'] == "requisition") {
    $page = "requisition";
} else if ($_GET['page'] == "approve_page") {
    $page = "approve_page";
}
else if ($_GET['page'] == "approve_page_admin") {
    $page = "approve_page_admin";
}
$order_ID = $_GET['oid'];
$i = 1;

$onum = $_GET['onum'];
$sql = "SELECT 
tbl_order_detail.order_detail,
tbl_order_detail.order_Number,
tbl_orders.employee_ID,
tbl_orders.add_by,
tbl_orders.add_date,
tbl_orders.approval,
tbl_orders.approved_by,
tbl_orders.approved_date,
tbl_orders.receiving,
tbl_orders.receiving_date,
tbl_orders.receiving_by,
tbl_orders.manager_approve,
tbl_orders.manager_status
FROM tbl_orders
INNER JOIN tbl_order_detail 
ON tbl_orders.order_Number = tbl_order_detail.order_Number
WHERE tbl_order_detail.order_Number = '$onum'";
$result = sqlsrv_query($conn, $sql);
$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

$detail = json_decode($row['order_detail'], true);
// $detail_ar = 
// foreach ($detail as $key) {
//     $arr[$key['equipment_Code']] = $key['Qty'];
// }
// echo "<pre>";
// print_r($detail);
// print_r($row);
// echo "</pre>";
// // exit;


function Show_Name_unit($E_code, $a)
{
    global $conn;
    $sql = "SELECT * FROM tbl_equipments WHERE equipment_ID = '$E_code'";
    $result =   sqlsrv_query($conn, $sql);

    $data = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

    $item1 = $data['equipment_Name'];
    $item2 = $data['unit'];

    if ($a == "N") {
        return $item1;
    } else if ($a == "U") {
        return $item2;
    }
}

?>

<?php include('sidebar.php'); ?>
<div class="main">
    <?php include('navbar.php'); ?>
    <div class="p-3">
        <div class="card" style="height:fit-content">
            <div class="text-center">
                <h1 class="text-dark mt-2 ms-4 fw-bold">รายการเบิกวัสดุอุปกรณ์</h1>
                <div class="row p-2">
                    <div class="col-md-6 offset-md-1 text-start mt-2 ">
                        <h4>เลขที่ <span class="ms-2">:</span> <?php echo $row['order_Number'] ?></h4>
                        <h4>ผู้เบิก <span class="ms-2">:</span> <?php echo $row['add_by'] ?></h4>
                        <h4>แผนก <span class="ms-1">:</span> <?php echo ShowNameDivision($row['employee_ID'])['site_f_1144']. " - " .ShowNameDivision($row['employee_ID'])['site_f_1145']; ?></h4>
                        <h4>วันที่เบิก <span class="ms-2">:</span> <?php echo $row['add_date']->format("d/m/y เวลา H:i น.") ?></h4>
                        <h4>สถานะ <span class="ms-2">:</span>
                            <?php
                            echo status($row['approval']);
                            if ($row['approval'] == "A") {
                                echo receiving($row['receiving'], $row['manager_status']);
                            }
                            ?>
                        </h4>
                        <?php if ($row['approval'] == "A" && !empty($row['receiving'])) { ?>
                            <h5>รับของวันที่ <span class="ms-2">:</span> <?php echo $row['receiving_date']->format("d/m/y เวลา H:i น.") ?></h5>
                            <h5>ผู้รับ <span> : </span> <?php echo showname($row['receiving_by'])['site_f_366'] ?> </h5>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="table-responsive p-2">
                <table id="TableOder" class="table table-hover col-md-12">
                    <thead class="table ">
                        <tr class="text-nowrap text-center">
                            <th width="5%">ลำดับ</th>
                            <th width="10%">รหัส</th>
                            <th class="text-center" width="40%">รายการวัสดุอุปกรณ์</th>
                            <th width="10%">จำนวนที่ขอ</th>
                            <?php if ($_SESSION['level'] == "0") { ?>
                                <th>จำนวนที่ได้จริง</th>
                            <?php } else if ($row['approval'] == "A") { ?>
                                <th>จำนวนที่ได้จริง</th>
                            <?php } ?>
                            <th width="25%">หน่วย</th>
                            <?php if ($_SESSION['level'] == "0" && $row['approval'] !== "A") { ?>
                                <th>ของยังไม่เข้า</th>
                            <?php } ?>
                            <!-- <th class="text-center">แก้ไข</th> -->
                        </tr>
                    </thead>
                    <tbody id="requestedItems">
                        <?php foreach ($detail as $item) { ?>
                            <tr class="text-center text-nowrap <?php echo ($item['Q_true'] == "0") ? "bg-danger bg-opacity-10" : "" ?>">
                                <td><?php echo $i++ ?></td>
                                <td class="text-center" id="Code_<?php echo $item['equipment_Code'] ?>"><?php echo ShowDataEquipmentID($item['equipment_Code'])['equipment_Code']; ?></td>
                                <td class="text-start"><?php echo Show_Name_unit($item['equipment_Code'], 'N') ?></td>
                                <td id="Qty_<?php echo $item['equipment_Code'] ?>">
                                    <input readonly min="1" type="text" class="form-control border border-0 text-center <?php echo ($item['Q_true'] == 0) ? "bg-danger" : "bg-info" ?> bg-opacity-25" value="<?php echo $item['Qty']; ?>">
                                </td>
                                <?php if ($_SESSION['level'] == "0" || $row['approval'] == "A") { ?>
                                <td>
                                    <input <?php echo ($_SESSION['level'] == "0" && $row['approval'] != "P" && $item['Q_true'] != "0" && ($row['manager_status'] == "0" || $row['manager_status'] == "2")) ? "" : "readonly" ?> id="q_true_<?php echo $item['equipment_Code'] ?>" onchange="Q_true('<?php echo $item['equipment_Code'] ?>','<?php echo $onum ?>')" type="number" value="<?php echo $item['Q_true'];  ?>" min="0" max="<?php echo $item['Qty']; ?>" class="form-control border border-0 <?php echo ($item['Q_true'] == 0) ? "bg-danger" : "bg-primary" ?>  bg-opacity-25 <?php echo ($_SESSION['level'] == "A" && $row['approval'] !== "P") ? "" : "text-center" ?>">
                                </td>
                                <?php }  ?>
                                <td><?php echo Show_Name_unit($item['equipment_Code'], 'U') ?></td>
                                <!-- <td>
                                    <?php if ($item['Q_true'] !== "0" && $_SESSION['level'] == "0" && $row['approval'] == "P") { ?>
                                        <button onclick="did_item('<?php echo $item['equipment_Code'] ?>','<?php echo $onum ?>')" class="btn btn-danger rounded-pill"><i class="bi bi-ban"></i></button>
                                    <?php } else if ($item['Q_true'] == "0") { ?>
                                        <button disabled class="btn border-0 fw-bold text-danger bg-danger bg-opacity-25 rounded-3 w-100 text-start"> <i class="bi bi-x"></i> ไม่พร้อมเบิก &nbsp</button>
                                    <?php } else if ($row['approval'] == "C") { ?>
                                        <button disabled class="btn border-0 fw-bold text-danger bg-danger bg-opacity-25 rounded-3 w-100 text-start"> <i class="bi bi-x"></i> ไม่อนุมติ.. &nbsp</button>
                                    <?php } else { ?>
                                        <button disabled class="btn border-0 fw-bold text-warning bg-warning bg-opacity-25 rounded-3 w-100 text-start"> <i class="bi bi-hash"></i> รอการอนุมติ.. &nbsp</button>
                                    <?php } ?>
                                </td> -->
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mb-2">
                    <?php 
                        if (($row['approval'] == "P")) {
                            if (($row['approved_by'] == $_SESSION['employee_ID'] || $_SESSION['level'] == "0") && $row['approval'] == 'P') { 
                    ?>
                            <button class="btn btn-success ms-2 rounded-pill" onclick="Approv('<?php echo $order_ID ?>','A')"> <i class="bi bi-check-circle-fill"></i> อนุมัติ</button>
                            <button class="btn btn-danger ms-2 me-2 rounded-pill" onclick="Approv('<?php echo $order_ID ?>','C')"> <i class="bi bi-x-circle-fill"></i> ไม่อนุมัติ</button>
                    <?php 
                            }

                            if($row['employee_ID'] == $_SESSION['employee_ID']){
                    ?>
                            <button id="add-Button ms-2" onclick="req_more('<?php echo $onum ?>')" class="btn btn-lg btn-info rounded-pill"><i class="bi bi-clipboard-plus"></i> แก้ไขรายการเบิก</button>
                    <?php
                            }
                        }

                        if ($row['approval'] == "A") { 
                            if(($row['manager_status'] == "0" || $row['manager_status'] == "2" || $row['manager_status'] == "3") && $_SESSION['level'] == "0"){
                    ?>
                            <button id="add-Button ms-2" onclick="req_more('<?php echo $onum ?>')" class="btn btn-lg btn-info rounded-pill"><i class="bi bi-clipboard-plus"></i> แก้ไขรายการเบิก</button>
                    <?php 
                            }

                            if ($row['manager_status'] == "0" && $_SESSION['level'] == "0") { 
                    ?>
                            <button class="btn btn-success rounded-pill ms-2" onclick="Approv(<?php echo $order_ID ?>,'S')"> <i class="bi bi-cart-check-fill"></i> สั่งซื้อ</button>
                    <?php 
                            }
                            if($row['manager_status'] == "1" && $_SESSION['level'] == "0"){
                    ?>
                            <button class="btn btn-success rounded-pill" onclick="Approv(<?php echo $order_ID ?>,'SS')"> <i class="bi bi-cart-check-fill"></i> ของมาส่งแล้ว</button>
                    <?php
                            }

                            if ($row['manager_status'] == "2") { 
                    ?>
                            <button class="btn btn-primary rounded-pill ms-2" onclick="Approv('<?php echo $order_ID ?>','Y')"><i class="bi bi-cart-check-fill"></i> รับของแล้ว</button>
                    <?php
                            }
                        }
                    ?>
                    <button class="btn btn-warning ms-2 rounded-pill " onclick="window.history.back()"><i class="bi bi-box-arrow-in-left"></i> กลับ</button>
                </div>
            </div>
        </div>

        <?php 
        if($row['approval'] == 'A' || $row['approval'] == 'C'){
        ?>
        <div class="card" style="height:fit-content">
            <h4 class="card-header text-light <?php echo ($row['approval'] == 'A') ? "bg-success" : "bg-danger" ?>">
                <?php
                if ($row['approval'] == 'A') {
                    echo '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-calendar2-check" viewBox="0 0 16 16">
                    <path d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z" />
                    <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z" />
                </svg>';
                } else {
                    echo '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-calendar2-x" viewBox="0 0 16 16">
                    <path d="M6.146 8.146a.5.5 0 0 1 .708 0L8 9.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 10l1.147 1.146a.5.5 0 0 1-.708.708L8 10.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 10 6.146 8.854a.5.5 0 0 1 0-.708" />
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z" />
                    <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 1m1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z" />
            </svg>';
                }
                ?>
                <?php echo ($row['approval'] == 'A' ? 'อนุมัติแล้ว' : 'ไม่อนุมัติ') ?>
            </h4>
            <div class="card-body">
                <div class="row row-cols-1">
                    <div class="col-md-6">
                        <?php echo ($row['approval'] == 'A' ? 'อนุมัติโดย' : 'ไม่อนุมัติโดย') ?> : <?php echo $row['approved_by'] == '' ? 'รอการตรวจสอบ' : showname($row['approved_by'])['site_f_366']; ?>
                    </div>
                    <div class="col-md-6">
                        วันที่ : 
                        <?php 
                        echo ($row['approved_by'] == '') 
                            ? 'รอการตรวจสอบ' 
                            : ($row['approved_date'] 
                                ? $row['approved_date']->format('d/m/Y H:i น.') 
                                : 'ไม่พบวันที่อนุมัติ');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
</div>

<?php include('footer.php'); ?>
<script>
    function did_item(eq_Code, onum) {
        var q_trueDid = "0";
        // var q_true = $("#q_true_" + eq_Code).val()
        // console.log(q_true)
        // console.log(eq_Code)
        Swal.fire({
            title: "รายการนี้ไม่พร้อมเบิก ใช่ หรือ ไม่?",
            showDenyButton: true,
            confirmButtonText: "ไม่พร้อมเบิก",
            denyButtonText: `ไม่แน่ใจ,ตรวจสอบก่อน`,
            icon: "question"
        }).then((result) => {
            if (result.isConfirmed) {
                // $("#q_true_" + eq_Code).val(q_trueDid)
                $.ajax({
                    url: "php_did_item.php",
                    type: "POST",
                    datatype: "json",
                    data: {
                        eq_Code: eq_Code,
                        q_true: q_trueDid,
                        onum: onum
                    },
                    success: function(data) {
                        // console.log(data)
                        Swal.fire({
                            title: "บันทึกแล้ว",
                            icon: "success",
                            timer: 1000,
                            showConfirmButton: false
                        }).then((data) => {
                            window.location.reload();
                        })

                    }
                })
            } else if (result.isDenied) {
                Swal.fire({
                    title: "ตรวจสอบให้แน่ใจก่อนกดบันทึก",
                    icon: "info",
                    timer: 500,
                    showConfirmButton: false
                });
            }
        });

    }

    function Q_true(eq_Code, onum) {
        // console.log(eq_Code);
        var q_true = $("#q_true_" + eq_Code).val();
        // console.log(q_true)
        $.ajax({
            url: "php_q_true.php",
            type: "POST",
            datatype: "json",
            data: {
                eq_Code: eq_Code,
                q_true: q_true,
                onum: onum
            },
            success: function(data) {
                console.log(data)
            }
        })
    }

    function Approv(order_ID, status, emp_ID) {
        console.log('Order ID:', order_ID);
        console.log('Status:', status);
        let status_msg = "";
        if (status == 'A') {
            status_msg = "ต้องการอนุมัติรายการนี้?"
        } else if (status == 'C') {
            status_msg = "ไม่อนุมัติรายการนี้ ใช่หรือไม่?"
        } else if (status == 'S') {
            status_msg = "ต้องการสั่งซื้อรายการนี้ ใช่หรือไม่?"
        } else if (status == 'Y') {
            status_msg = "รับของแล้ว?"
        } else if (status == 'SS') {
            status_msg = "ต้องการยืนยันของมาส่งแล้ว ใช่หรือไม่?"
        }
        Swal.fire({
            icon: "question",
            title: status_msg,
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: "บันทึก",
            denyButtonText: `ยกเลิก`
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "php_approved.php",
                    data: {
                        order_ID: order_ID,
                        status: status,
                        emp_ID: emp_ID
                    },
                    beforeSend: function() {
                        Swal.fire({
                            position: "center",
                            imageUrl: "image/icons8-load.gif",
                            title: 'กำลังดำเนินการ กรุณารอสักครู่...',
                            imageWidth: 100,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            onBeforeOpen: () => {
                                Swal.showLoading()
                            },
                        });
                    },
                    success: function(data) {
                        Swal.close();
                        if (data.status === 'success') {
                            // console.log(data.chk)
                            // console.log("Item added to cart: " + "อนุมัติแล้ว")
                            if (data.stt_app == 'A') {
                                Swal.fire({
                                    position: "center",
                                    title: "อนุมัติแล้ว",
                                    imageUrl: "image/icons8-approved.gif",
                                    imageWidth: 70,
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then((result) => {
                                    window.location.reload();
                                });
                            } 
                            else if (data.stt_app == 'C') {
                                Swal.fire({
                                    position: "center",
                                    title: "ไม่อนุมัติรายการนี้",
                                    imageUrl: "image/icons8-unapproved.gif",
                                    imageWidth: 70,
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then((result) => {
                                    window.location.reload();
                                });
                            } 
                            else if (data.stt_app == 'S') {
                                Swal.fire({
                                    position: "center",
                                    title: "ยืนยันการสั่งซื้อแล้ว",
                                    imageUrl: "image/icons8-approved.gif",
                                    imageWidth: 70,
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then((result) => {
                                    window.location.reload();
                                });
                            } 
                            else if (data.stt_app == 'SS') {
                                Swal.fire({
                                    position: "center",
                                    title: "ยืนยันของเบิกมาส่งเรียบร้อยแล้ว",
                                    imageUrl: "image/icons8-approved.gif",
                                    imageWidth: 70,
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then((result) => {
                                    window.location.reload();
                                });
                            }
                            else if (data.stt_app == 'Y') {
                                Swal.fire({
                                    position: "center",
                                    title: "ยืนยันการรับของแล้ว",
                                    imageUrl: "image/icons8-approved.gif",
                                    imageWidth: 70,
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then((result) => {
                                    window.location.reload();
                                });
                            }


                        } else {
                            console.error("Error adding item to cart: " + data.message),
                                Swal.fire({
                                    position: "center",
                                    icon: "error",
                                    title: data.message,
                                    showConfirmButton: false,
                                    timer: 800
                                });
                        }
                    }

                })
            }
        });
    }

    function req_more(onum) {
        // console.log(onum)
        $.ajax({
            type: "POST",
            datatype: "json",
            url: "php_rq_more.php",
            data: {
                order_Number: onum
            },
            success: function(response) {
                response = JSON.parse(response);
                console.log(response);
                window.location.assign("cart.php?" + "req_more=add&" + "onum=<?php echo $onum ?>");
            }

        })
    }

    function array(onum) {
        var onum = onum;
        var dataArray = [];
        var rows = document.querySelectorAll('#requestedItems tr');

        rows.forEach(function(row) {
            var rowData = {
                name: row.querySelector('td.text-start').innerHTML,
                qty: row.querySelector('td + td input').value
            };

            dataArray.push(rowData);
        });
        var jsonString = JSON.stringify(dataArray);
        console.log("JSON string:", jsonString);

        $.ajax({
            url: "php_detail_qty.php",
            type: "POST",
            data: {
                jsonData: jsonString,
                onum: onum
            },
            success: function(response) {
                // console.log(JSON.parse(response))
                var response = JSON.parse(response);
                if (response.status === 'success') {
                    Swal.fire({
                        title: "อัปเดตจำนวนแล้ว!",
                        icon: "success"
                    }).then(() => {
                        window.location.reload();
                    });
                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    }
</script>