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

// Check permission for edit/add buttons
$canEditAsOwner = ($row['approval'] == "P" && $row['employee_ID'] == $_SESSION['employee_ID']);
$canEditAsAdmin = ($row['approval'] == "A" && ($_SESSION['level'] == "0") && ($row['manager_status'] == "0" || $row['manager_status'] == "2" || $row['manager_status'] == "3"));
$canEdit = $canEditAsOwner || $canEditAsAdmin;

?>

<?php include('sidebar.php'); ?>
<div class="main">
    <?php include('navbar.php'); ?>
    <div class="p-3">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="text-center">
                <h3 class="text-dark mt-3 ms-4 fw-bold">
                    <i class="fa-solid fa-clipboard-list text-primary me-2"></i>รายการเบิกวัสดุอุปกรณ์
                </h3>
                <div class="row p-2">
                    <div class="col-md-6 offset-md-1 text-start mt-2">
                        <div class="d-flex flex-column gap-1 mb-2">
                            <h5><i class="fa-solid fa-hashtag text-secondary me-2"></i>เลขที่ <span class="ms-2">:</span> <strong><?php echo $row['order_Number'] ?></strong></h5>
                            <h5><i class="fa-solid fa-user text-secondary me-2"></i>ผู้เบิก <span class="ms-2">:</span> <?php echo $row['add_by'] ?></h5>
                            <h5><i class="fa-solid fa-building text-secondary me-2"></i>แผนก <span class="ms-1">:</span> <?php echo ShowNameDivision($row['employee_ID'])['site_f_1144']. " - " .ShowNameDivision($row['employee_ID'])['site_f_1145']; ?></h5>
                            <h5><i class="fa-regular fa-calendar text-secondary me-2"></i>วันที่เบิก <span class="ms-2">:</span> <?php echo $row['add_date']->format("d/m/y เวลา H:i น.") ?></h5>
                            <h5><i class="fa-solid fa-circle-info text-secondary me-2"></i>สถานะ <span class="ms-2">:</span>
                                <?php
                                echo status($row['approval']);
                                if ($row['approval'] == "A") {
                                    echo receiving($row['receiving'], $row['manager_status']);
                                }
                                ?>
                            </h5>
                            <?php if ($row['approval'] == "A" && !empty($row['receiving'])) { ?>
                                <h6><i class="fa-solid fa-calendar-check text-secondary me-2"></i>รับของวันที่ <span class="ms-2">:</span> <?php echo $row['receiving_date']->format("d/m/y เวลา H:i น.") ?></h6>
                                <h6><i class="fa-solid fa-user-check text-secondary me-2"></i>ผู้รับ <span> : </span> <?php echo showname($row['receiving_by'])['site_f_366'] ?> </h6>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive p-2">
                <table id="TableOder" class="table table-hover table-borderless col-md-12">
                    <thead>
                        <tr class="text-nowrap text-center" style="border-bottom: 2px solid #dee2e6;">
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
                        </tr>
                    </thead>
                    <tbody id="requestedItems">
                        <?php foreach ($detail as $item) { ?>
                            <tr class="text-center text-nowrap <?php echo ($item['Q_true'] == "0") ? "bg-danger bg-opacity-10" : "" ?>">
                                <td><?php echo $i++ ?></td>
                                <td class="text-center" id="Code_<?php echo $item['equipment_Code'] ?>"><?php echo ShowDataEquipmentID($item['equipment_Code'])['equipment_Code']; ?></td>
                                <td class="text-start"><?php echo Show_Name_unit($item['equipment_Code'], 'N') ?></td>
                                <td id="Qty_<?php echo $item['equipment_Code'] ?>">
                                    <input readonly min="1" type="text" class="form-control border border-0 text-center <?php echo ($item['Q_true'] == 0) ? "bg-danger" : "bg-info" ?> bg-opacity-25" value="<?php echo $item['Qty']; ?>" style="border-radius: 8px;">
                                </td>
                                <?php if ($_SESSION['level'] == "0" || $row['approval'] == "A") { ?>
                                <td>
                                    <input <?php echo ($_SESSION['level'] == "0" && $row['approval'] != "P" && $item['Q_true'] != "0" && ($row['manager_status'] == "0" || $row['manager_status'] == "2")) ? "" : "readonly" ?> id="q_true_<?php echo $item['equipment_Code'] ?>" onchange="Q_true('<?php echo $item['equipment_Code'] ?>','<?php echo $onum ?>')" type="number" value="<?php echo $item['Q_true'];  ?>" min="0" max="<?php echo $item['Qty']; ?>" class="form-control border border-0 <?php echo ($item['Q_true'] == 0) ? "bg-danger" : "bg-primary" ?>  bg-opacity-25 text-center" style="border-radius: 8px;">
                                </td>
                                <?php }  ?>
                                <td><?php echo Show_Name_unit($item['equipment_Code'], 'U') ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mb-3 gap-2 flex-wrap">
                    <?php 
                        if (($row['approval'] == "P")) {
                            if (($row['approved_by'] == $_SESSION['employee_ID'] || $_SESSION['level'] == "0") && $row['approval'] == 'P') { 
                    ?>
                            <button class="btn btn-success rounded-pill px-4" onclick="Approv('<?php echo $order_ID ?>','A')"> <i class="fa-solid fa-circle-check"></i> อนุมัติ</button>
                            <button class="btn btn-danger rounded-pill px-4" onclick="Approv('<?php echo $order_ID ?>','C')"> <i class="fa-solid fa-circle-xmark"></i> ไม่อนุมัติ</button>
                    <?php 
                            }

                            if($row['employee_ID'] == $_SESSION['employee_ID']){
                    ?>
                            <button class="btn btn-info rounded-pill px-4" onclick="openManageModal()"><i class="fa-solid fa-pen-to-square"></i> แก้ไข/เพิ่มรายการ</button>
                    <?php
                            }
                        }

                        if ($row['approval'] == "A") {
                            if(($row['manager_status'] == "0" || $row['manager_status'] == "2" || $row['manager_status'] == "3") && $_SESSION['level'] == "0"){
                    ?>
                            <button class="btn btn-info rounded-pill px-4" onclick="openManageModal()"><i class="fa-solid fa-pen-to-square"></i> แก้ไข/เพิ่มรายการ</button>
                    <?php 
                            }

                            if ($row['manager_status'] == "0" && $_SESSION['level'] == "0") { 
                    ?>
                            <button class="btn btn-success rounded-pill px-4" onclick="Approv(<?php echo $order_ID ?>,'S')"> <i class="fa-solid fa-cart-shopping"></i> สั่งซื้อ</button>
                    <?php 
                            }
                            if($row['manager_status'] == "1" && $_SESSION['level'] == "0"){
                    ?>
                            <button class="btn btn-success rounded-pill px-4" onclick="Approv(<?php echo $order_ID ?>,'SS')"> <i class="fa-solid fa-truck-ramp-box"></i> ของมาส่งแล้ว</button>
                    <?php
                            }

                            if ($row['manager_status'] == "2") { 
                    ?>
                            <button class="btn btn-primary rounded-pill px-4" onclick="Approv('<?php echo $order_ID ?>','Y')"><i class="fa-solid fa-box-open"></i> รับของแล้ว</button>
                    <?php
                            }
                        }
                    ?>
                    <button class="btn btn-warning rounded-pill px-4" onclick="window.history.back()"><i class="fa-solid fa-arrow-left"></i> กลับ</button>
                </div>
            </div>
        </div>

        <?php 
        if($row['approval'] == 'A' || $row['approval'] == 'C'){
        ?>
        <div class="card shadow-sm border-0 mt-3" style="border-radius: 12px;">
            <h5 class="card-header text-light <?php echo ($row['approval'] == 'A') ? "bg-success" : "bg-danger" ?>" style="border-radius: 12px 12px 0 0;">
                <?php echo ($row['approval'] == 'A') ? '<i class="fa-solid fa-circle-check me-2"></i>' : '<i class="fa-solid fa-circle-xmark me-2"></i>'; ?>
                <?php echo ($row['approval'] == 'A' ? 'อนุมัติแล้ว' : 'ไม่อนุมัติ') ?>
            </h5>
            <div class="card-body">
                <div class="row row-cols-1">
                    <div class="col-md-6">
                        <i class="fa-solid fa-user-pen text-secondary me-1"></i>
                        <?php echo ($row['approval'] == 'A' ? 'อนุมัติโดย' : 'ไม่อนุมัติโดย') ?> : <?php echo $row['approved_by'] == '' ? 'รอการตรวจสอบ' : showname($row['approved_by'])['site_f_366']; ?>
                    </div>
                    <div class="col-md-6">
                        <i class="fa-regular fa-calendar-check text-secondary me-1"></i>
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

<!-- ========== MANAGE ITEMS MODAL (รวม edit + add) ========== -->
<div class="modal fade" id="manageItemsModal" tabindex="-1" aria-labelledby="manageItemsModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-primary bg-gradient text-white">
                <h5 class="modal-title" id="manageItemsModalLabel"><i class="fa-solid fa-list-check me-2"></i>จัดการรายการเบิก</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <!-- ส่วนเพิ่มรายการใหม่ -->
                <div class="card border-0 bg-light mb-3" style="border-radius: 12px;">
                    <div class="card-body pb-2">
                        <h6 class="fw-bold text-primary mb-2"><i class="fa-solid fa-circle-plus me-1"></i> เพิ่มรายการใหม่</h6>
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-7">
                                <label class="form-label small fw-semibold"><i class="fa-solid fa-barcode me-1"></i> ค้นหารหัส / ชื่อสินค้า</label>
                                <select id="manageItemSelect" class="form-control" style="width: 100%;"></select>
                            </div>
                            <div class="col-5 col-md-2">
                                <label class="form-label small fw-semibold">จำนวน</label>
                                <input type="number" id="manageItemQty" class="form-control text-center" min="1" value="1" style="border-radius: 8px;">
                            </div>
                            <div class="col-7 col-md-3">
                                <button type="button" class="btn btn-success rounded-pill w-100" id="btnManageAddItem" disabled onclick="manageAddItem()">
                                    <i class="fa-solid fa-plus"></i> เพิ่ม
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ส่วนรายการปัจจุบัน -->
                <h6 class="fw-bold text-secondary mb-2"><i class="fa-solid fa-clipboard-list me-1"></i> รายการปัจจุบัน</h6>
                <div class="table-responsive">
                    <table class="table table-hover table-borderless align-middle">
                        <thead>
                            <tr class="text-center" style="border-bottom: 2px solid #dee2e6;">
                                <th width="10%">รหัส</th>
                                <th width="38%">รายการ</th>
                                <th width="26%">จำนวน</th>
                                <th width="14%">หน่วย</th>
                                <th width="12%">ลบ</th>
                            </tr>
                        </thead>
                        <tbody id="manageItemsBody">
                        </tbody>
                    </table>
                </div>
                <div id="manageItemsEmpty" class="text-center text-muted py-3 d-none">
                    <i class="fa-solid fa-box-open fa-2x mb-2"></i>
                    <p>ไม่มีรายการ</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> ปิด</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="saveManageItems()"><i class="fa-solid fa-floppy-disk"></i> บันทึก</button>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
<script>
    // Current order items data (from PHP)
    var currentItems = <?php echo json_encode($detail); ?>;
    var onum = '<?php echo $onum ?>';

    // ==================== Q_true inline edit ====================
    function did_item(eq_Code, onum) {
        var q_trueDid = "0";
        Swal.fire({
            title: "รายการนี้ไม่พร้อมเบิก ใช่ หรือ ไม่?",
            showDenyButton: true,
            confirmButtonText: "ไม่พร้อมเบิก",
            denyButtonText: `ไม่แน่ใจ,ตรวจสอบก่อน`,
            icon: "question"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "php_did_item.php",
                    type: "POST",
                    datatype: "json",
                    data: { eq_Code: eq_Code, q_true: q_trueDid, onum: onum },
                    success: function(data) {
                        Swal.fire({ title: "บันทึกแล้ว", icon: "success", timer: 1000, showConfirmButton: false }).then(() => {
                            window.location.reload();
                        })
                    }
                })
            } else if (result.isDenied) {
                Swal.fire({ title: "ตรวจสอบให้แน่ใจก่อนกดบันทึก", icon: "info", timer: 500, showConfirmButton: false });
            }
        });
    }

    function Q_true(eq_Code, onum) {
        var q_true = $("#q_true_" + eq_Code).val();
        $.ajax({
            url: "php_q_true.php",
            type: "POST",
            datatype: "json",
            data: { eq_Code: eq_Code, q_true: q_true, onum: onum },
            success: function(data) {
                console.log(data)
            }
        })
    }

    // ==================== Approve functions ====================
    function Approv(order_ID, status, emp_ID) {
        let status_msg = "";
        if (status == 'A') status_msg = "ต้องการอนุมัติรายการนี้?";
        else if (status == 'C') status_msg = "ไม่อนุมัติรายการนี้ ใช่หรือไม่?";
        else if (status == 'S') status_msg = "ต้องการสั่งซื้อรายการนี้ ใช่หรือไม่?";
        else if (status == 'Y') status_msg = "รับของแล้ว?";
        else if (status == 'SS') status_msg = "ต้องการยืนยันของมาส่งแล้ว ใช่หรือไม่?";

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
                    data: { order_ID: order_ID, status: status, emp_ID: emp_ID },
                    beforeSend: function() {
                        Swal.fire({
                            position: "center",
                            imageUrl: "image/icons8-load.gif",
                            title: 'กำลังดำเนินการ กรุณารอสักครู่...',
                            imageWidth: 100,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                        });
                    },
                    success: function(data) {
                        Swal.close();
                        if (data.status === 'success') {
                            var titles = {
                                'A': 'อนุมัติแล้ว', 'C': 'ไม่อนุมัติรายการนี้',
                                'S': 'ยืนยันการสั่งซื้อแล้ว', 'SS': 'ยืนยันของเบิกมาส่งเรียบร้อยแล้ว',
                                'Y': 'ยืนยันการรับของแล้ว'
                            };
                            var img = (data.stt_app == 'C') ? 'image/icons8-unapproved.gif' : 'image/icons8-approved.gif';
                            Swal.fire({
                                position: "center",
                                title: titles[data.stt_app] || "สำเร็จ",
                                imageUrl: img,
                                imageWidth: 70,
                                showConfirmButton: false,
                                timer: 1000
                            }).then(() => { window.location.reload(); });
                        } else {
                            Swal.fire({ position: "center", icon: "error", title: data.message, showConfirmButton: false, timer: 800 });
                        }
                    }
                })
            }
        });
    }

    // ==================== MANAGE ITEMS MODAL (รวม edit + add) ====================
    var manageSelectedEquipment = null;

    function openManageModal() {
        var tbody = $('#manageItemsBody');
        tbody.empty();

        currentItems.forEach(function(item) {
            var eqCodeDisplay = $('#Code_' + item.equipment_Code).text().trim() || item.equipment_Code;
            var nameCell = '';
            var unitCell = '';
            var mainRow = $('tr').filter(function() {
                return $(this).find('[id="Code_' + item.equipment_Code + '"]').length > 0;
            });
            if (mainRow.length) {
                nameCell = mainRow.find('td.text-start').first().text().trim();
                unitCell = mainRow.find('td').last().text().trim();
            }
            tbody.append(buildManageRow(item.equipment_Code, eqCodeDisplay, nameCell, String(item.Qty), unitCell));
        });

        toggleManageEmpty();

        // Reset add-item section
        manageSelectedEquipment = null;
        $('#manageItemQty').val(1);
        $('#btnManageAddItem').prop('disabled', true);
        if ($('#manageItemSelect').data('select2')) {
            $('#manageItemSelect').val(null).trigger('change');
        }

        $('#manageItemsModal').modal('show');

        // Init Select2 after modal shown
        setTimeout(function() {
            $('#manageItemSelect').select2({
                dropdownParent: $('#manageItemsModal'),
                width: '100%',
                placeholder: 'พิมพ์รหัสหรือชื่อสินค้า...',
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    url: 'php_search_equipment.php',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        var existingIds = [];
                        $('#manageItemsBody tr').each(function() {
                            existingIds.push($(this).data('eq'));
                        });
                        return { term: params.term, exclude: existingIds.join(',') };
                    },
                    processResults: function(data) { return data; }
                }
            }).off('select2:select select2:clear')
              .on('select2:select', function(e) {
                manageSelectedEquipment = e.params.data;
                $('#btnManageAddItem').prop('disabled', false);
            }).on('select2:clear', function() {
                manageSelectedEquipment = null;
                $('#btnManageAddItem').prop('disabled', true);
            });
        }, 200);
    }

    function buildManageRow(eqCode, codeDisplay, name, qty, unit) {
        return '<tr class="text-center align-middle" data-eq="' + eqCode + '" data-name="' + name + '" data-unit="' + unit + '">' +
            '<td><small>' + codeDisplay + '</small></td>' +
            '<td class="text-start"><small>' + name + '</small></td>' +
            '<td>' +
                '<div class="input-group input-group-sm justify-content-center">' +
                    '<button class="btn btn-outline-secondary rounded-start-pill" onclick="manageModalQty(\'' + eqCode + '\', -1)"><i class="fa-solid fa-minus"></i></button>' +
                    '<input type="number" class="form-control text-center border-secondary" style="max-width:65px;" min="1" value="' + qty + '" id="manageQty_' + eqCode + '" onchange="validateManageQty(\'' + eqCode + '\')">' +
                    '<button class="btn btn-outline-secondary rounded-end-pill" onclick="manageModalQty(\'' + eqCode + '\', 1)"><i class="fa-solid fa-plus"></i></button>' +
                '</div>' +
            '</td>' +
            '<td><small>' + unit + '</small></td>' +
            '<td><button class="btn btn-outline-danger btn-sm rounded-pill" onclick="removeManageItem(\'' + eqCode + '\')"><i class="fa-solid fa-trash-can"></i></button></td>' +
        '</tr>';
    }

    function manageAddItem() {
        if (!manageSelectedEquipment) return;
        var qty = parseInt($('#manageItemQty').val()) || 1;
        if (qty < 1) qty = 1;
        var eqCode = String(manageSelectedEquipment.id);
        // ตรวจว่ามีในตารางแล้วหรือไม่
        var existing = $('#manageItemsBody tr[data-eq="' + eqCode + '"]');
        if (existing.length) {
            var input = $('#manageQty_' + eqCode);
            input.val(parseInt(input.val()) + qty);
        } else {
            $('#manageItemsBody').append(
                buildManageRow(eqCode, manageSelectedEquipment.code || eqCode, manageSelectedEquipment.name, String(qty), manageSelectedEquipment.unit)
            );
        }
        toggleManageEmpty();
        // Clear select2
        $('#manageItemSelect').val(null).trigger('change');
        manageSelectedEquipment = null;
        $('#btnManageAddItem').prop('disabled', true);
        $('#manageItemQty').val(1);
    }

    function manageModalQty(eqCode, delta) {
        var input = $('#manageQty_' + eqCode);
        var val = parseInt(input.val()) + delta;
        if (val < 1) val = 1;
        input.val(val);
    }

    function validateManageQty(eqCode) {
        var input = $('#manageQty_' + eqCode);
        var val = parseInt(input.val());
        if (isNaN(val) || val < 1) input.val(1);
    }

    function removeManageItem(eqCode) {
        $('#manageItemsBody tr[data-eq="' + eqCode + '"]').fadeOut(200, function() {
            $(this).remove();
            toggleManageEmpty();
        });
    }

    function toggleManageEmpty() {
        if ($('#manageItemsBody tr').length === 0) {
            $('#manageItemsEmpty').removeClass('d-none');
        } else {
            $('#manageItemsEmpty').addClass('d-none');
        }
    }

    function saveManageItems() {
        var items = [];
        $('#manageItemsBody tr').each(function() {
            var eqCode = $(this).data('eq');
            var qty = parseInt($(this).find('input[type="number"]').val()) || 1;
            items.push({ equipment_Code: String(eqCode), Qty: String(qty) });
        });

        if (items.length === 0) {
            Swal.fire('เตือน', 'ไม่มีรายการให้บันทึก', 'warning');
            return;
        }

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "php_edit_order_items.php",
            data: { onum: onum, items: JSON.stringify(items) },
            success: function(data) {
                if (data.status === 'success') {
                    $('#manageItemsModal').modal('hide');
                    Swal.fire({
                        position: "center",
                        title: "บันทึกแล้ว",
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1000
                    }).then(() => { window.location.reload(); });
                } else {
                    Swal.fire('ผิดพลาด', data.message || 'ไม่สามารถบันทึกได้', 'error');
                }
            },
            error: function() {
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
            }
        });
    }

    // ==================== Legacy array function ====================
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

        $.ajax({
            url: "php_detail_qty.php",
            type: "POST",
            data: { jsonData: jsonString, onum: onum },
            success: function(response) {
                var response = JSON.parse(response);
                if (response.status === 'success') {
                    Swal.fire({ title: "อัปเดตจำนวนแล้ว!", icon: "success" }).then(() => {
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