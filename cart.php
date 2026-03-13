<?php
session_start();
include_once 'funtion.php';
// session_destroy();
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
$currentDay = date('d');
// if($_SESSION['employee_ID'] == '660500122'){
//     $currentDay = 24;
// }
$showButtons = true; //($currentDay >= 20)
// if (empty($_SESSION['employee_ID'])) {
//     header('location:index.php');
//     // exit();
// }
$page = "index";
$page_shop = "index";
$req_more = ($_GET['req_more'] == "add") ? "d-none" : "";
$hide_more = ($_GET['req_more'] !== "add") ? "d-none" : "";
$onum = $_GET['onum'];
$emp = $_SESSION['employee_ID'];


$datepermission = permiss_get_data();
$arrayPermis = json_decode($datepermission[0]['per_user'], true);

if (in_array($_SESSION['employee_ID'], $arrayPermis)) {
    $_SESSION['level'] = '0';
} else {
    $_SESSION['level'] = '1';
}

?>
<?php include('sidebar.php'); ?>
<div class="main">
    <?php include('navbar.php'); ?>
    <div class="row row-cols-1 row-cols-md-2 g-2 p-3">
        <div class="col-md-7 d-fixed">
            <div class="card h-100 p-3">
                <?php
                // if ($_SESSION['employee_ID'] == '660500122') {
                ?>
                <!-- <div class="col-3">
                    <select name="list_product" id="list_product" class="form-select mb-3">
                        <option value="24" <?php //echo ($_GET['list_product'] == '24') ? 'selected' : '' 
                                            ?> >เบิกของ HR</option>
                        <option value="29" <?php //echo ($_GET['list_product'] == '29') ? 'selected' : '' 
                                            ?>>เบิกของการตลาด</option>
                    </select>
                </div> -->
                <?php
                // }
                ?>
                <div class="table-responsive">
                    <table id="TableStore" class="table table-striped table-hover col-md-12">
                        <thead class="table">
                            <tr>
                                <th width="10">รหัส</th>
                                <th>รายการ</th>
                                <th>หน่วยนับ</th>
                                <th width="80" class="text-center">เบิก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sqlTb = "
                                SELECT 
                                    e.equipment_Code,
                                    e.equipment_ID,
                                    e.equipment_Name,
                                    e.unit
                                FROM tbl_equipments AS e
                                JOIN tbl_catalogs AS c ON e.catalog_ID = c.catalog_ID
                                WHERE 
                                    e.status = '1'
                                    AND c.active = 0
                                    AND e.equipment_ID NOT IN (
                                        SELECT equipment_Code 
                                        FROM tbl_carts 
                                        WHERE employee_ID = '$emp'
                                    )
                            ";
                            if ($_GET['list_product'] == '24' || $_GET['list_product'] == '') {
                                $sqlTb .= " AND e.equipment_Code LIKE '%24%'";
                            } elseif ($_GET['list_product'] == '29') {
                                $sqlTb .= " AND (e.equipment_Code LIKE '%29F%' OR e.equipment_Code LIKE '%29GI%' OR e.equipment_Code LIKE '%29GO%')";
                            }

                            $resultTb = sqlsrv_query($conn, $sqlTb);
                            while ($rowTb = sqlsrv_fetch_array($resultTb, SQLSRV_FETCH_ASSOC)) {
                            ?>
                                <tr>
                                    <td class="text-center fs-6">
                                        <p class="mt-3" id="equipment_Code<?php echo $rowTb['equipment_ID'] ?>">
                                            <?php echo $rowTb['equipment_Code']; ?>
                                        </p>
                                    </td>
                                    <td class="text-nowrap"><?php echo $rowTb['equipment_Name']; ?></td>
                                    <td><?php echo $rowTb['unit']; ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-lg btn-success rounded-pill add-request" onclick="addrequest('<?php echo $rowTb['equipment_ID']; ?>')">
                                            <i class="bi bi-basket3-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-5 d-none d-sm-inline-block">
            <div class="card" style="height:fit-content">
                <h2 class="text-dark mt-2 ms-4 text-center">รายการเบิก</h2>
                <!-- <h4 class="text-success ms-4">นาย ทศพร เทียนทอง</h4> -->
                <div class="table-responsive p-3">
                    <!-- <form action="#" method="POST"> -->
                    <table class="table table-hover col-md-12">
                        <thead class="table">
                            <tr>
                                <!-- <th width="10%">รหัส</th> -->
                                <th width="35%">รายการ</th>
                                <th class="text-center" width="25%">จำนวน</th>
                                <th class="text-center" width="25%">หน่วย</th>
                                <th width="10%">ลบ</th>
                            </tr>
                        </thead>
                        <tbody id="requestedItems" class="text-nowrap">
                            <?php
                            $sqlCart = "SELECT 
                                tbl_carts.employee_ID, 
                                tbl_carts.equipment_Code,
                                tbl_carts.Qty, 
                                tbl_equipments.equipment_Name, 
                                tbl_equipments.unit, 
                                tbl_carts.add_date
                            FROM tbl_carts
                            INNER JOIN tbl_equipments 
                            ON tbl_carts.equipment_Code = tbl_equipments.equipment_ID
                            WHERE tbl_carts.employee_ID = '$emp' 
                            ORDER BY tbl_carts.add_date DESC";
                            // echo $sqlCart;
                            $resultCart = sqlsrv_query($conn, $sqlCart);
                            while ($rowCart = sqlsrv_fetch_array($resultCart, SQLSRV_FETCH_ASSOC)) {
                            ?>
                                <tr>
                                    <!-- <td>
                                        <p class="mt-3"><?php echo $rowCart['equipment_Code'] ?></p>
                                    </td> -->
                                    <td class="text-nowrap">
                                        <p class="mt-3"><?php echo $rowCart['equipment_Name'] ?></p>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <div class="btn btn-sm btn-secondary" onclick="editQty('<?php echo $rowCart['equipment_Code']; ?>','subtract')">
                                                <i class="bi bi-dash-circle-fill"></i>
                                            </div>
                                            <input type="text" id="qty_<?php echo $rowCart['equipment_Code']; ?>" value="<?php echo $rowCart['Qty']; ?>" class="form-control border border-0 text-center" oninput="this.value = this.value.replace(/[^0-9]/g, '');" onchange="editQtyManual('<?php echo $rowCart['equipment_Code']; ?>')">
                                            <div class="btn btn-sm btn-secondary" onclick="editQty('<?php echo $rowCart['equipment_Code']; ?>','add')">
                                                <i class="bi bi-plus-circle-fill"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <p class="mt-3"><?php echo $rowCart['unit'] ?></p>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm rounded-pill" onclick="delCart('<?php echo $rowCart['equipment_Code'] ?>')"> <i class="bi bi-trash-fill"></i> </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php if (sqlsrv_has_rows($resultCart)) { ?>
                        <?php if ($showButtons) { ?>
                            <div class="text-center d-flex <?php echo $req_more ?>">
                                <button class="btn btn-info w-50 me-2" onclick="select_emo()" <?php echo ($showButtons) ? '' : 'disabled' ?>>บันทึกรายการเบิก</button>
                                <button class="btn btn-danger w-50 ms-2" onclick="cancelCart('<?php echo $_SESSION['employee_ID'] ?>')">ยกเลิก</button>
                            </div>
                            <div class="text-center d-flex <?php echo $hide_more ?>">
                                <button class="btn btn-lg btn-info w-50 me-2 w-100" id="edit-save" onclick="editOrder('<?php echo $onum ?>','<?php echo $_SESSION['employee_ID'] ?>')" <?php echo ($showButtons) ? '' : 'disabled' ?>> <i class="bi bi-floppy2"></i> บันทึกการแก้ไข / <i class="bi bi-caret-left-fill"></i> ย้อนกลับ</button>
                            </div>
                        <?php } else { ?>
                            <div class="text-center text-warning">
                                เกินกำหนดเบิกแล้ว เบิกได้อีกในวันที่ 1 ของเดือนถัดไป
                            </div>
                            <hr>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="text-center">
                            ยังไม่ได้เลือกรายการ
                            <hr>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">เลือกผู้อนุมัติ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <select id="employeeSelect" class="form-control select2">
                    <option value="" selected>เลือกผู้อนุมัติ</option>
                    <?php
                    $sqlmy = "SELECT site_f_366 as f_name, site_f_1188 as PrefixName, site_f_365 as id_employee
                    FROM work_progress_010 WHERE site_f_3005 = '600' AND (site_f_398 = '0000-00-00' OR site_f_398 > CURRENT_DATE())";
                    $resultmy = mysqli_query($connmy, $sqlmy);
                    foreach ($resultmy as $p => $codemy) {
                    ?>
                        <option value="<?php echo $codemy['id_employee']; ?>"><?php echo $codemy['f_name']; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" onclick="saveOrder('<?php echo $_SESSION['employee_ID'] ?>')">บันทึกรายการเบิกของ</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ShopModal" tabindex="-1" aria-labelledby="ShopModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ShopModalLabel">รายการเบิกของ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <!-- <form action="#" method="POST"> -->
                    <table class="table table-hover col-md-12">
                        <thead class="table">
                            <tr>
                                <!-- <th width="10%">รหัส</th> -->
                                <th width="35%">รายการ</th>
                                <th class="text-center" width="25%">จำนวน</th>
                                <th class="text-center" width="25%">หน่วย</th>
                                <th width="10%">ลบ</th>
                            </tr>
                        </thead>
                        <tbody id="requestedItems" class="text-nowrap">
                            <?php
                            $sqlCart = "SELECT 
                            tbl_carts.employee_ID,
                            tbl_carts.equipment_Code, 
                            tbl_carts.Qty, 
                            tbl_equipments.equipment_Name, 
                            tbl_equipments.unit,    
                            tbl_carts.add_date
                        FROM tbl_carts
                        INNER JOIN tbl_equipments 
                        ON tbl_carts.equipment_Code = tbl_equipments.equipment_ID
                        WHERE tbl_carts.employee_ID = '$emp' 
                        ORDER BY tbl_carts.add_date DESC";
                            // echo $sqlCart;
                            $resultCart = sqlsrv_query($conn, $sqlCart);
                            while ($rowCart = sqlsrv_fetch_array($resultCart, SQLSRV_FETCH_ASSOC)) {
                            ?>
                                <tr>
                                    <!-- <td>
                                    <p class="mt-3"><?php echo $rowCart['equipment_Code'] ?></p>
                                </td> -->
                                    <td>
                                        <p class="mt-3"><?php echo $rowCart['equipment_Name'] ?></p>
                                    </td>
                                    <td>
                                        <div class="input-group" style="flex-direction: column-reverse;">
                                            <div class="btn btn-sm btn-secondary" onclick="editQty_M('<?php echo $rowCart['equipment_Code']; ?>','subtract')">
                                                <i class="bi bi-dash-circle-fill"></i>
                                            </div>
                                            <input type="text" readonly id="qtym_<?php echo $rowCart['equipment_Code']; ?>" value="<?php echo $rowCart['Qty'] ?>" class="form-control border border-0  text-center w-100">
                                            <div class="btn btn-sm btn-secondary" onclick="editQty_M('<?php echo $rowCart['equipment_Code']; ?>','add')">
                                                <i class="bi bi-plus-circle-fill"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <p class="mt-3"><?php echo $rowCart['unit'] ?></p>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm rounded-pill" onclick="delCart('<?php echo $rowCart['equipment_Code'] ?>')"> <i class="bi bi-trash-fill"></i> </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <?php if (sqlsrv_has_rows($resultCart)) { ?>
                    <?php if ($showButtons) { ?>
                        <button type="button" class="btn btn-info <?php echo $req_more ?>" data-bs-dismiss="modal" onclick="select_emo()" <?php echo ($showButtons) ? '' : 'disabled' ?>>บันทึกรายการเบิก</button>
                        <button type="button" class="btn btn-danger <?php echo $req_more ?>" onclick="cancelCart('<?php echo $_SESSION['employee_ID'] ?>')">ยกเลิก</button>
                    <?php } ?>
                    <button type="button" class="btn btn-lg btn-info <?php echo $hide_more ?>" id="edit-save" onclick="editOrder('<?php echo $onum ?>','<?php echo $_SESSION['employee_ID'] ?>')" <?php echo ($showButtons) ? '' : 'disabled' ?>> <i class="bi bi-floppy2"></i> บันทึกการแก้ไข / <i class="bi bi-caret-left-fill"></i> ย้อนกลับ</button>
                <?php } else { ?>

                <?php } ?>
            </div>
        </div>
    </div>
</div>

</div>
<?php include('footer.php'); ?>
<script>
    $(document).ready(function() {
        $("#list_product").change(function() {
            var list_product = $("#list_product").val();
            window.location.href = "cart.php?list_product=" + list_product;
        });
    })

    function editQty(equipment_Code, action) {

        var currentQty = parseInt($("#qty_" + equipment_Code).val());
        // var currentQty = parseInt($("#test_" + equipment_Code).val());
        if (action === "add") {
            currentQty += 1;
        } else if (action === "subtract" && currentQty > 1) {
            currentQty -= 1;
        } else if (action === "subtract" && currentQty == 1) {
            delCart(equipment_Code)
        }

        $("#qty_" + equipment_Code).val(currentQty);
        // $("#test_" + equipment_Code).val(currentQty);

        updateQty(equipment_Code, currentQty, "<?php echo $_SESSION['employee_ID']; ?>");
    }

    function editQtyManual(equipment_Code) {
        var val = parseInt($("#qty_" + equipment_Code).val());
        if (isNaN(val) || val <= 0) {
            val = 1;
            $("#qty_" + equipment_Code).val(1);
        }
        updateQty(equipment_Code, val, "<?php echo $_SESSION['employee_ID']; ?>");
    }

    function editQty_M(equipment_Code, action) {

        var currentQty = parseInt($("#qtym_" + equipment_Code).val());
        // var currentQty = parseInt($("#test_" + equipment_Code).val());
        if (action === "add") {
            currentQty += 1;
        } else if (action === "subtract" && currentQty > 1) {
            currentQty -= 1;
        } else if (action === "subtract" && currentQty == 1) {
            delCart(equipment_Code)
        }

        $("#qtym_" + equipment_Code).val(currentQty);
        // $("#test_" + equipment_Code).val(currentQty);

        updateQty(equipment_Code, currentQty, "<?php echo $_SESSION['employee_ID']; ?>");
    }

    function updateQty(equipment_Code, newQty, empID) {
        if (empID == "") {
            Swal.fire(
                'หมดเวลาล็อคอิน',
                'กรุณาเข้าใช้งานใหม่อีกครั้ง',
                'warning'
            )

            return false;
        }
        // console.log(equipment_Code)
        // console.log(newQty)
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "php_update_quantity.php",
            data: {
                equipment_Code: equipment_Code,
                newQty: newQty,
                employee_ID: empID
            },
            success: function(data) {

            },
            error: function(xhr, status, error) {
                console.error("AJAX error: " + error);
            }
        });


    }

    $('#TableStore').dataTable({
        "order": [
            [0, 'DESC']
        ],
        // scrollX: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json',
        },
        stateSave: true
    });

    function editOrder(onum, emp_ID) {
        // var employee_ID = emp_ID;
        // console.log(onum)
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "php_rq_more2.php",
            data: {
                onum: onum,
                emp_ID: emp_ID
            },
            success: function(data) {
                // console.log(data)
                if (data.status === 'success') {
                    // console.log("Item added to cart: " + data.message)
                    Swal.fire({
                        position: "center",
                        title: "บันทึกการแก้ไขแล้ว",
                        imageUrl: "image/icons8-cart.gif",
                        imageWidth: 100,
                        showConfirmButton: false,
                        timer: 1000
                    }).then((result) => {
                        window.location.assign("requisition_detail.php?page=requisition&onum=" + data.onum);
                    });

                } else {
                    console.log(data.message)
                }
            }
        })
    }

    function select_emo() {
        $('#ShopModal').modal('hide');
        $('#myModal').modal('show');
        $('.select2').select2({
            dropdownParent: $('#myModal'),
            width: '100%'
        });
    }

    // $('.btn-secondary[data-bs-dismiss="modal"]').on('click', function() {
    //     $('.modal-backdrop').remove();
    // });


    function saveOrder(emp_ID) {
        var employee_ID = emp_ID;
        var approve = $("#employeeSelect").val();

        if (approve == '') {
            Swal.fire(
                'กรุณาเลือกผู้ขออนุมัติ',
                '',
                'warning'
            )

            return false;
        }

        $.ajax({
            type: "POST",
            url: "php_order_save.php",
            data: {
                employee_ID: employee_ID,
                manager_ID: approve
            },
            success: function(data) {
                console.log(data);
                // console.log("Item added to cart: " + data.message)
                Swal.fire({
                    position: "center",
                    title: "บันทึกรายการเบิกแล้ว",
                    imageUrl: "image/icons8-cart.gif",
                    imageWidth: 100,
                    showConfirmButton: false,
                    timer: 1000
                }).then((result) => {
                    window.location.assign("approve_page.php");
                });
            },
            error: function(error) {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'เพิ่มข้อมูลไม่สำเร็จ',
                    text: 'บันทึกข้อมูลไม่สำเร็จ'
                }).then((result) => {
                    if (result) {
                        window.location.href = 'approve_page.php';
                    }
                })
            }
        })
    }

    function delCart(equipmentCode) {
        Swal.fire({
            title: "คุณแน่ใจหรือไม่?",
            text: "ยืนยันที่จะลบรายการออกจากตระกร้า?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ยืนยัน",
            cancelButtonText: "ยกเลิก"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "php_del_cart.php",
                    data: {
                        equipmentCode: equipmentCode,
                        employee_ID: "<?php echo $_SESSION['employee_ID']; ?>"
                    },
                    success: function(data) {
                        // console.log("ได้")
                        console.log(data);
                        if (data.status === 'success') {
                            console.log("Item added to cart: " + data.message),
                                Swal.fire({
                                    position: "center",
                                    title: data.message,
                                    imageUrl: "image/icons8-load.gif",
                                    imageWidth: 100,
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then((result) => {
                                    window.location.reload();
                                });

                        }
                    }
                })
            }
        });
    }

    function addrequest(equipment_Code) {
        if (equipment_Code !== '') {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "php_cart.php",
                data: {
                    equipment_Code: equipment_Code,
                    action: 'add'
                },
                success: function(data) {
                    // console.log(data)
                    // return false
                    if (data.status === 'success') {
                        console.log("Item added to cart: " + data.message),
                            Swal.fire({
                                position: "center",
                                title: data.message,
                                imageUrl: "image/icons8-buy.gif",
                                imageWidth: 70,
                                showConfirmButton: false,
                                timer: 1000
                            }).then((result) => {
                                window.location.reload();
                            });

                    } else {
                        console.error("Error adding item to cart: " + data.message),
                            Swal.fire({
                                position: "center",
                                icon: "error",
                                title: data.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error: " + error);
                }
            });
        } else {
            console.error("Error: Unable to retrieve equipment_Code.");
        }
    }


    function cancelCart(employee_ID) {
        Swal.fire({
            title: "คุณแน่ใจหรือไม่?",
            text: "คุณต้องการยกเลิกรายการเบิกทั้งหมดใช่หรือไม่?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ใช่",
            cancelButtonText: "ไม่"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "php_cancel_cart.php",
                    data: {
                        employee_ID: employee_ID
                    },
                    success: function(data) {
                        if (data.status === 'success') {
                            Swal.fire({
                                text: "ยกเลิกรายการทั้งหมดในตระกร้าแล้ว",
                                icon: "success"
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error: " + error);

                        // Show SweetAlert error notification
                        Swal.fire({
                            position: "center",
                            icon: "error",
                            title: "Error cancelling cart",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }
        });
    }
</script>