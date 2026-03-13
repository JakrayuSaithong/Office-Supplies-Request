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
    }
}

include('header.php');
$currentDay = date('d');
$showButtons = true;
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

// Pre-fetch cart rows
$sqlCart = "SELECT tbl_carts.employee_ID, tbl_carts.equipment_Code, tbl_carts.Qty,
            tbl_equipments.equipment_Name, tbl_equipments.unit, tbl_carts.add_date
            FROM tbl_carts
            INNER JOIN tbl_equipments ON tbl_carts.equipment_Code = tbl_equipments.equipment_ID
            WHERE tbl_carts.employee_ID = '$emp'
            ORDER BY tbl_carts.add_date DESC";
$resultCart = sqlsrv_query($conn, $sqlCart);
$cartRows = [];
while ($rc = sqlsrv_fetch_array($resultCart, SQLSRV_FETCH_ASSOC)) { $cartRows[] = $rc; }
$cartCount = count($cartRows);
?>
<?php include('sidebar.php'); ?>
<div class="main">
    <?php include('navbar.php'); ?>
    <div class="row g-3 p-3">

        <!-- ===== ตารางสินค้า ===== -->
        <div class="col-md-7">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header bg-primary bg-gradient text-white py-3 px-4 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked fs-5"></i>
                    <span class="fw-bold">รายการวัสดุอุปกรณ์</span>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table id="TableStore" class="table table-hover align-middle">
                            <thead>
                                <tr class="table-light" style="border-bottom: 2px solid #dee2e6;">
                                    <th width="12%">รหัส</th>
                                    <th>รายการ</th>
                                    <th>หน่วย</th>
                                    <th width="90" class="text-center">เบิก</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sqlTb = "SELECT e.equipment_Code, e.equipment_ID, e.equipment_Name, e.unit
                                    FROM tbl_equipments AS e
                                    JOIN tbl_catalogs AS c ON e.catalog_ID = c.catalog_ID
                                    WHERE e.status = '1' AND c.active = 0
                                    AND e.equipment_ID NOT IN (SELECT equipment_Code FROM tbl_carts WHERE employee_ID = '$emp')";
                                if ($_GET['list_product'] == '24' || $_GET['list_product'] == '') {
                                    $sqlTb .= " AND e.equipment_Code LIKE '%24%'";
                                } elseif ($_GET['list_product'] == '29') {
                                    $sqlTb .= " AND (e.equipment_Code LIKE '%29F%' OR e.equipment_Code LIKE '%29GI%' OR e.equipment_Code LIKE '%29GO%')";
                                }
                                $resultTb = sqlsrv_query($conn, $sqlTb);
                                while ($rowTb = sqlsrv_fetch_array($resultTb, SQLSRV_FETCH_ASSOC)) {
                                ?>
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-light text-secondary border" id="equipment_Code<?php echo $rowTb['equipment_ID'] ?>">
                                                <?php echo $rowTb['equipment_Code']; ?>
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-nowrap"><?php echo $rowTb['equipment_Name']; ?></td>
                                        <td><span class="text-muted small"><?php echo $rowTb['unit']; ?></span></td>
                                        <td class="text-center">
                                            <button class="btn btn-success btn-sm rounded-pill px-3 add-request" onclick="addrequest('<?php echo $rowTb['equipment_ID']; ?>')">
                                                <i class="bi bi-basket3-fill"></i> เบิก
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== ตะกร้าเบิก (desktop) ===== -->
        <div class="col-md-5 d-none d-sm-block">
            <div class="card shadow-sm border-0" style="border-radius: 14px; overflow: hidden; position: sticky; top: 80px;">
                <div class="card-header bg-success bg-gradient text-white py-3 px-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-cart-shopping fs-5"></i>
                        <span class="fw-bold">รายการเบิก</span>
                    </div>
                    <span class="badge bg-white text-success rounded-pill fw-bold"><?php echo $cartCount ?> รายการ</span>
                </div>
                <div class="card-body p-0">
                    <?php if ($cartCount > 0): ?>
                        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                                    <tr style="border-bottom: 2px solid #dee2e6;">
                                        <th class="ps-3">รายการ</th>
                                        <th class="text-center" width="130">จำนวน</th>
                                        <th class="text-center" width="55">หน่วย</th>
                                        <th width="40"></th>
                                    </tr>
                                </thead>
                                <tbody id="requestedItems" class="text-nowrap">
                                    <?php foreach ($cartRows as $rowCart): ?>
                                        <tr>
                                            <td class="ps-3 fw-semibold small"><?php echo $rowCart['equipment_Name'] ?></td>
                                            <td>
                                                <div class="input-group input-group-sm justify-content-center">
                                                    <button class="btn btn-outline-secondary rounded-start-pill" onclick="editQty('<?php echo $rowCart['equipment_Code']; ?>','subtract')"><i class="bi bi-dash"></i></button>
                                                    <input type="text" id="qty_<?php echo $rowCart['equipment_Code']; ?>" value="<?php echo $rowCart['Qty']; ?>" class="form-control text-center border-secondary" style="max-width:50px;" oninput="this.value = this.value.replace(/[^0-9]/g, '');" onchange="editQtyManual('<?php echo $rowCart['equipment_Code']; ?>')">
                                                    <button class="btn btn-outline-secondary rounded-end-pill" onclick="editQty('<?php echo $rowCart['equipment_Code']; ?>','add')"><i class="bi bi-plus"></i></button>
                                                </div>
                                            </td>
                                            <td class="text-center"><span class="text-muted small"><?php echo $rowCart['unit'] ?></span></td>
                                            <td>
                                                <button class="btn btn-outline-danger btn-sm rounded-pill" onclick="delCart('<?php echo $rowCart['equipment_Code'] ?>')"><i class="bi bi-trash3"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top">
                            <?php if ($showButtons): ?>
                                <div class="d-flex gap-2 <?php echo $req_more ?>">
                                    <button class="btn btn-primary rounded-pill flex-fill fw-semibold" onclick="select_emo()">
                                        <i class="fa-solid fa-paper-plane me-1"></i> บันทึกรายการเบิก
                                    </button>
                                    <button class="btn btn-outline-danger rounded-pill" onclick="cancelCart('<?php echo $_SESSION['employee_ID'] ?>')" title="ยกเลิกทั้งหมด">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                                <div class="d-flex gap-2 <?php echo $hide_more ?>">
                                    <button class="btn btn-primary rounded-pill flex-fill fw-semibold" id="edit-save" onclick="editOrder('<?php echo $onum ?>','<?php echo $_SESSION['employee_ID'] ?>')">
                                        <i class="bi bi-floppy2 me-1"></i> บันทึกการแก้ไข
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning rounded-3 text-center small mb-0">
                                    <i class="fa-solid fa-clock me-1"></i> เกินกำหนดเบิกแล้ว เบิกได้อีกในวันที่ 1 ของเดือนถัดไป
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-cart-shopping fa-3x mb-3 opacity-25"></i>
                            <p class="fw-semibold mb-1">ยังไม่ได้เลือกรายการ</p>
                            <p class="small">กดปุ่ม <span class="badge bg-success">เบิก</span> ด้านซ้ายเพื่อเพิ่มสินค้า</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ===== Modal เลือกผู้อนุมัติ ===== -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-primary bg-gradient text-white">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fa-solid fa-user-check me-2"></i>เลือกผู้อนุมัติ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <label class="form-label fw-semibold"><i class="fa-solid fa-user me-1 text-muted"></i> ผู้อนุมัติ</label>
                <select id="employeeSelect" class="form-control select2">
                    <option value="" selected>เลือกผู้อนุมัติ</option>
                    <?php
                    $sqlmy = "SELECT site_f_366 as f_name, site_f_1188 as PrefixName, site_f_365 as id_employee
                    FROM work_progress_010 WHERE site_f_3005 = '600' AND (site_f_398 = '0000-00-00' OR site_f_398 > CURRENT_DATE())";
                    $resultmy = mysqli_query($connmy, $sqlmy);
                    foreach ($resultmy as $p => $codemy) {
                    ?>
                        <option value="<?php echo $codemy['id_employee']; ?>"><?php echo $codemy['f_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-1"></i> ปิด</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="saveOrder('<?php echo $_SESSION['employee_ID'] ?>')"><i class="fa-solid fa-paper-plane me-1"></i> บันทึกรายการเบิก</button>
            </div>
        </div>
    </div>
</div>

<!-- ===== Modal ตะกร้า (mobile) ===== -->
<div class="modal fade" id="ShopModal" tabindex="-1" aria-labelledby="ShopModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-success bg-gradient text-white">
                <h5 class="modal-title" id="ShopModalLabel">
                    <i class="fa-solid fa-cart-shopping me-2"></i>รายการเบิก
                    <span class="badge bg-white text-success ms-2"><?php echo $cartCount ?></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <?php if ($cartCount > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr style="border-bottom: 2px solid #dee2e6;">
                                    <th class="ps-3">รายการ</th>
                                    <th class="text-center" width="130">จำนวน</th>
                                    <th class="text-center" width="55">หน่วย</th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            <tbody id="requestedItemsMobile" class="text-nowrap">
                                <?php foreach ($cartRows as $rowCart): ?>
                                    <tr>
                                        <td class="ps-3 fw-semibold small"><?php echo $rowCart['equipment_Name'] ?></td>
                                        <td>
                                            <div class="input-group input-group-sm justify-content-center">
                                                <button class="btn btn-outline-secondary rounded-start-pill" onclick="editQty_M('<?php echo $rowCart['equipment_Code']; ?>','subtract')"><i class="bi bi-dash"></i></button>
                                                <input type="text" id="qtym_<?php echo $rowCart['equipment_Code']; ?>" value="<?php echo $rowCart['Qty'] ?>" class="form-control text-center border-secondary" style="max-width:50px;" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                                <button class="btn btn-outline-secondary rounded-end-pill" onclick="editQty_M('<?php echo $rowCart['equipment_Code']; ?>','add')"><i class="bi bi-plus"></i></button>
                                            </div>
                                        </td>
                                        <td class="text-center"><span class="text-muted small"><?php echo $rowCart['unit'] ?></span></td>
                                        <td>
                                            <button class="btn btn-outline-danger btn-sm rounded-pill" onclick="delCart('<?php echo $rowCart['equipment_Code'] ?>')"><i class="bi bi-trash3"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-cart-shopping fa-3x mb-3 opacity-25"></i>
                        <p class="fw-semibold">ยังไม่มีรายการ</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-1"></i> ปิด</button>
                <?php if ($cartCount > 0 && $showButtons): ?>
                    <button type="button" class="btn btn-primary rounded-pill px-3 <?php echo $req_more ?>" data-bs-dismiss="modal" onclick="select_emo()">
                        <i class="fa-solid fa-paper-plane me-1"></i> บันทึกรายการเบิก
                    </button>
                    <button type="button" class="btn btn-outline-danger rounded-pill <?php echo $req_more ?>" onclick="cancelCart('<?php echo $_SESSION['employee_ID'] ?>')">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-3 <?php echo $hide_more ?>" id="edit-save-m" onclick="editOrder('<?php echo $onum ?>','<?php echo $_SESSION['employee_ID'] ?>')">
                        <i class="bi bi-floppy2 me-1"></i> บันทึกการแก้ไข
                    </button>
                <?php endif; ?>
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
        if (action === "add") {
            currentQty += 1;
        } else if (action === "subtract" && currentQty > 1) {
            currentQty -= 1;
        } else if (action === "subtract" && currentQty == 1) {
            delCart(equipment_Code);
        }
        $("#qty_" + equipment_Code).val(currentQty);
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
        if (action === "add") {
            currentQty += 1;
        } else if (action === "subtract" && currentQty > 1) {
            currentQty -= 1;
        } else if (action === "subtract" && currentQty == 1) {
            delCart(equipment_Code);
        }
        $("#qtym_" + equipment_Code).val(currentQty);
        updateQty(equipment_Code, currentQty, "<?php echo $_SESSION['employee_ID']; ?>");
    }

    function updateQty(equipment_Code, newQty, empID) {
        if (empID == "") {
            Swal.fire('หมดเวลาล็อคอิน', 'กรุณาเข้าใช้งานใหม่อีกครั้ง', 'warning');
            return false;
        }
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "php_update_quantity.php",
            data: { equipment_Code: equipment_Code, newQty: newQty, employee_ID: empID },
            error: function(xhr, status, error) { console.error("AJAX error: " + error); }
        });
    }

    $('#TableStore').dataTable({
        "order": [[0, 'DESC']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json' },
        stateSave: true
    });

    function editOrder(onum, emp_ID) {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "php_rq_more2.php",
            data: { onum: onum, emp_ID: emp_ID },
            success: function(data) {
                if (data.status === 'success') {
                    Swal.fire({
                        position: "center",
                        title: "บันทึกการแก้ไขแล้ว",
                        imageUrl: "image/icons8-cart.gif",
                        imageWidth: 100,
                        showConfirmButton: false,
                        timer: 1000
                    }).then(() => {
                        window.location.assign("requisition_detail.php?page=requisition&onum=" + data.onum);
                    });
                } else {
                    console.log(data.message);
                }
            }
        });
    }

    function select_emo() {
        $('#ShopModal').modal('hide');
        $('#myModal').modal('show');
        $('.select2').select2({
            dropdownParent: $('#myModal'),
            width: '100%'
        });
    }

    function saveOrder(emp_ID) {
        var employee_ID = emp_ID;
        var approve = $("#employeeSelect").val();

        if (approve == '') {
            Swal.fire('กรุณาเลือกผู้ขออนุมัติ', '', 'warning');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "php_order_save.php",
            data: { employee_ID: employee_ID, manager_ID: approve },
            success: function(data) {
                Swal.fire({
                    position: "center",
                    title: "บันทึกรายการเบิกแล้ว",
                    imageUrl: "image/icons8-cart.gif",
                    imageWidth: 100,
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => { window.location.assign("approve_page.php"); });
            },
            error: function(error) {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'เพิ่มข้อมูลไม่สำเร็จ',
                    text: 'บันทึกข้อมูลไม่สำเร็จ'
                }).then((result) => {
                    if (result) { window.location.href = 'approve_page.php'; }
                });
            }
        });
    }

    function delCart(equipmentCode) {
        Swal.fire({
            title: "คุณแน่ใจหรือไม่?",
            text: "ยืนยันที่จะลบรายการออกจากตะกร้า?",
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
                    data: { equipmentCode: equipmentCode, employee_ID: "<?php echo $_SESSION['employee_ID']; ?>" },
                    success: function(data) {
                        if (data.status === 'success') {
                            Swal.fire({
                                position: "center",
                                title: data.message,
                                imageUrl: "image/icons8-load.gif",
                                imageWidth: 100,
                                showConfirmButton: false,
                                timer: 1000
                            }).then(() => { window.location.reload(); });
                        }
                    }
                });
            }
        });
    }

    function addrequest(equipment_Code) {
        if (equipment_Code !== '') {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "php_cart.php",
                data: { equipment_Code: equipment_Code, action: 'add' },
                success: function(data) {
                    if (data.status === 'success') {
                        Swal.fire({
                            position: "center",
                            title: data.message,
                            imageUrl: "image/icons8-buy.gif",
                            imageWidth: 70,
                            showConfirmButton: false,
                            timer: 1000
                        }).then(() => { window.location.reload(); });
                    } else {
                        Swal.fire({
                            position: "center",
                            icon: "error",
                            title: data.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                },
                error: function(xhr, status, error) { console.error("AJAX error: " + error); }
            });
        }
    }

    function cancelCart(employee_ID) {
        Swal.fire({
            title: "คุณแน่ใจหรือไม่?",
            text: "ต้องการยกเลิกรายการเบิกทั้งหมดใช่หรือไม่?",
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
                    data: { employee_ID: employee_ID },
                    success: function(data) {
                        if (data.status === 'success') {
                            Swal.fire({
                                text: "ยกเลิกรายการทั้งหมดในตะกร้าแล้ว",
                                icon: "success"
                            }).then(() => { window.location.reload(); });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({ position: "center", icon: "error", title: "Error cancelling cart", showConfirmButton: false, timer: 1500 });
                    }
                });
            }
        });
    }
</script>
