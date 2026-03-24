<?php include('header.php');
include_once('condb.php');
$employee_ID = $_SESSION['employee_ID'];
$page = "approve_page_admin";

function getCountByStatus_Admin($status)
{
    global $conn;
    $sql = "SELECT COUNT(manager_status) AS total_count
            FROM tbl_orders
            WHERE status = '1' AND manager_status = ? AND approval != 'C' AND approval = 'A'";
    $stmt = sqlsrv_prepare($conn, $sql, array(&$status));
    $result = sqlsrv_execute($stmt);
    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $totalCount = $row['total_count'];
    sqlsrv_free_stmt($stmt);

    return $totalCount;
}
$countA = getCountByStatus_Admin('0');
$countP = getCountByStatus_Admin('1');
$countC = getCountByStatus_Admin('2');
$countS = getCountByStatus_Admin('3');

// Fetch employee list for admin requisition modal
$sqlEmpList = "SELECT site_f_366 as f_name, site_f_365 as id_employee
    FROM work_progress_010 WHERE site_f_3005 = '600' AND (site_f_398 = '0000-00-00' OR site_f_398 > CURRENT_DATE())
    ORDER BY site_f_366 ASC";
$resultEmpList = mysqli_query($connmy, $sqlEmpList);
$empList = [];
while ($empRow = mysqli_fetch_assoc($resultEmpList)) {
    $empList[] = $empRow;
}
?>
<?php include('sidebar.php'); ?>
<div class="main">
    <?php include('navbar.php'); ?>
    <div class="d-flex justify-content-center">
        <div class="row col-md-12 p-3 g-3">
            <div class="col-md-3">
                <div class="p-3 ps-4 card shadow-sm border-0 border-start border-4 border-warning" style="border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                    <h5 class="text-warning mb-1">
                        <i class="fa-solid fa-basket-shopping"></i> รอสั่งซื้อ
                    </h5>
                    <h2 class="card-text fw-bold mb-0"><?php echo $countA ?> <small class="text-secondary fs-6 fw-normal">รายการ</small></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 ps-4 card shadow-sm border-0 border-start border-4 border-info" style="border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                    <h5 class="text-info mb-1">
                        <i class="fa-solid fa-truck-fast"></i> รอของส่ง
                    </h5>
                    <h2 class="card-text fw-bold mb-0"><?php echo $countP ?> <small class="text-secondary fs-6 fw-normal">รายการ</small></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 ps-4 card shadow-sm border-0 border-start border-4 border-primary" style="border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                    <h5 class="text-primary mb-1">
                        <i class="fa-solid fa-cart-flatbed"></i> รอแจกของ
                    </h5>
                    <h2 class="card-text fw-bold mb-0"><?php echo $countC ?> <small class="text-secondary fs-6 fw-normal">รายการ</small></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 ps-4 card shadow-sm border-0 border-start border-4 border-success" style="border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                    <h5 class="text-success mb-1">
                        <i class="fa-solid fa-circle-check"></i> แจกของแล้ว
                    </h5>
                    <h2 class="card-text fw-bold mb-0"><?php echo $countS ?> <small class="text-secondary fs-6 fw-normal">รายการ</small></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column me-4 ms-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <div class="nav nav-tabs border-0" id="nav-tab" role="tablist">
                <button class="nav-link bg-white text-warning active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" data-tab="0" type="button" role="tab" aria-controls="nav-home" aria-selected="true">
                    <i class="fa-solid fa-basket-shopping" style="font-size: 18px;"></i> <span class="d-none d-md-inline">รอสั่งซื้อ</span>
                </button>
            <button class="nav-link bg-white text-info" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" data-tab="1" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                <i class="fa-solid fa-truck-fast" style="font-size: 18px;"></i> <span class="d-none d-md-inline">รอของส่ง</span>
            </button>
            <button class="nav-link bg-white text-primary" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" data-tab="2" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">
                <i class="fa-solid fa-cart-flatbed" style="font-size: 18px;"></i> <span class="d-none d-md-inline">รอแจกของ</span>
            </button>
            <button class="nav-link bg-white text-success" id="nav-success-tab" data-bs-toggle="tab" data-bs-target="#nav-success" data-tab="3" type="button" role="tab" aria-controls="nav-success" aria-selected="false">
                <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i> <span class="d-none d-md-inline">แจกของแล้ว</span>
            </button>
            </div>
            <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#adminReqModal">
                <i class="fa-solid fa-plus me-1"></i> เบิกเพิ่มเติม
            </button>
        </div>
        <div class="card border-0 shadow-sm p-2 rounded-bottom rounded-0">
            <div class="tab-content" id="nav-tabContent">

                <!-- Tab 0: รอสั่งซื้อ -->
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                    <div class="table-responsive-xxl p-3">
                        <table id="TableOder_P" class="table table-hover table-borderless">
                            <thead>
                                <tr class="text-nowrap text-center" style="border-bottom: 2px solid #dee2e6;">
                                    <th>เลขที่</th>
                                    <th style="width: 10rem;">สถานะ</th>
                                    <th style="width: 25rem;">แผนก</th>
                                    <th style="width: 25rem;">ชื่อผู้เบิก</th>
                                    <th style="width: 15rem;">วันที่เบิก</th>
                                    <th style="width: 15rem;">วันที่อนุมัติ</th>
                                    <th style="width: 25rem;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_tab0">
                                <tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-warning" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 1: รอของส่ง -->
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                    <div class="table-responsive-xxl p-3">
                        <table id="TableOder_A" class="table table-hover table-borderless">
                            <thead>
                                <tr class="text-nowrap text-center" style="border-bottom: 2px solid #dee2e6;">
                                    <th width="10%">เลขที่</th>
                                    <th style="width: 10rem;">สถานะ</th>
                                    <th style="width: 25rem;">แผนก</th>
                                    <th style="width: 25rem;">ชื่อผู้เบิก</th>
                                    <th style="width: 15rem;">วันที่เบิก</th>
                                    <th style="width: 25rem;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_tab1">
                                <tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 2: รอแจกของ -->
                <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab" tabindex="0">
                    <div class="table-responsive-xxl p-3">
                        <table id="TableOder_C" class="table table-hover table-borderless">
                            <thead>
                                <tr class="text-nowrap text-center" style="border-bottom: 2px solid #dee2e6;">
                                    <th width="10%">เลขที่</th>
                                    <th style="width: 10rem;">สถานะ</th>
                                    <th style="width: 25rem;">แผนก</th>
                                    <th style="width: 25rem;">ชื่อผู้เบิก</th>
                                    <th style="width: 15rem;">วันที่เบิก</th>
                                    <th style="width: 25rem;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_tab2">
                                <tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 3: แจกของแล้ว -->
                <div class="tab-pane fade" id="nav-success" role="tabpanel" aria-labelledby="nav-success-tab" tabindex="0">
                    <div class="p-3 pb-0">
                        <div class="d-flex align-items-end gap-2 flex-wrap mb-3">
                            <div>
                                <label class="form-label small text-muted mb-1">เดือน</label>
                                <select id="filterMonth" class="form-select form-select-sm" style="width: 140px;">
                                    <option value="">ล่าสุด 2 เดือน</option>
                                    <?php
                                    $thaiMonths = ['', 'มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
                                    for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?php echo $m; ?>"><?php echo $thaiMonths[$m]; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="form-label small text-muted mb-1">ปี (พ.ศ.)</label>
                                <select id="filterYear" class="form-select form-select-sm" style="width: 110px;">
                                    <option value="">-</option>
                                    <?php
                                    $currentBE = date("Y") + 543;
                                    for ($y = $currentBE; $y >= $currentBE - 3; $y--): ?>
                                        <option value="<?php echo $y - 543; ?>"><?php echo $y; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <button class="btn btn-sm btn-primary rounded-pill px-3" onclick="filterTab3()">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> ค้นหา
                            </button>
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="resetFilterTab3()">
                                <i class="fa-solid fa-rotate-left me-1"></i> ล่าสุด 2 เดือน
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive-xxl p-3 pt-0">
                        <table id="TableOder_SS" class="table table-hover table-borderless">
                            <thead>
                                <tr class="text-nowrap text-center" style="border-bottom: 2px solid #dee2e6;">
                                    <th width="10%">เลขที่</th>
                                    <th style="width: 10rem;">สถานะ</th>
                                    <th style="width: 25rem;">แผนก</th>
                                    <th style="width: 25rem;">ชื่อผู้เบิก</th>
                                    <th style="width: 15rem;">วันที่เบิก</th>
                                    <th style="width: 25rem;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_tab3">
                                <tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
</div>
<!-- ===== Modal เบิกเพิ่มเติม (Admin) ===== -->
<div class="modal fade" id="adminReqModal" tabindex="-1" aria-labelledby="adminReqModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-primary bg-gradient text-white">
                <h5 class="modal-title" id="adminReqModalLabel"><i class="fa-solid fa-plus me-2"></i>เบิกเพิ่มเติม</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- เลือกผู้เบิก -->
                <div class="mb-3">
                    <label class="form-label fw-semibold"><i class="fa-solid fa-user me-1 text-muted"></i> ผู้เบิก</label>
                    <select id="adminReqEmployee" class="form-control admin-req-select2">
                        <option value="" selected>เลือกผู้เบิก</option>
                        <?php foreach ($empList as $emp): ?>
                            <option value="<?php echo $emp['id_employee']; ?>"><?php echo $emp['f_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- ค้นหาและเพิ่มรายการ -->
                <div class="mb-3">
                    <label class="form-label fw-semibold"><i class="fa-solid fa-magnifying-glass me-1 text-muted"></i> ค้นหารายการของเบิก</label>
                    <select id="adminReqEquipment" class="form-control admin-req-select2-eq">
                        <option value="" selected>พิมพ์ค้นหารหัสหรือชื่ออุปกรณ์</option>
                    </select>
                </div>

                <!-- ตารางรายการที่เลือก -->
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr style="border-bottom: 2px solid #dee2e6;">
                                <th class="ps-3">รหัส</th>
                                <th>รายการ</th>
                                <th class="text-center" width="130">จำนวน</th>
                                <th class="text-center" width="60">หน่วย</th>
                                <th width="40"></th>
                            </tr>
                        </thead>
                        <tbody id="adminReqItems">
                            <tr id="adminReqEmpty">
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-box-open fa-2x mb-2 opacity-25"></i>
                                    <p class="mb-0">ยังไม่มีรายการ — ค้นหาและเลือกอุปกรณ์ด้านบน</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-1"></i> ปิด</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="btnAdminReqSave"><i class="fa-solid fa-paper-plane me-1"></i> บันทึกรายการเบิก</button>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
<script>
    // Track which tabs have been loaded
    var loadedTabs = {};
    var dtInstances = {};

    // DataTable config template
    var dtConfig = {
        stateSave: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json',
        },
        order: [[0, 'desc']]
    };

    function loadTabData(tabIndex, extraData) {
        if (loadedTabs[tabIndex] && !extraData) return; // Already loaded

        var postData = { tab: tabIndex };
        if (extraData) {
            $.extend(postData, extraData);
        }

        $.ajax({
            type: "POST",
            url: "php_load_tab_admin.php",
            dataType: "json",
            data: postData,
            success: function(data) {
                if (data.status === 'success') {
                    $('#tbody_tab' + tabIndex).html(data.html);
                    loadedTabs[tabIndex] = true;

                    // Initialize DataTable after content is loaded
                    var tableIds = { '0': '#TableOder_P', '1': '#TableOder_A', '2': '#TableOder_C', '3': '#TableOder_SS' };
                    var tableId = tableIds[tabIndex];
                    if (tableId && !$.fn.DataTable.isDataTable(tableId)) {
                        var cfg = Object.assign({}, dtConfig);
                        // Tab 0 has 7 columns, date at index 4; others have 6, date at index 4
                        if (tabIndex === '0') {
                            cfg.order = [[4, 'desc']];
                        }
                        dtInstances[tabIndex] = $(tableId).DataTable(cfg);
                    }
                }
            },
            error: function() {
                var colCount = (tabIndex === '0') ? 7 : 6;
                $('#tbody_tab' + tabIndex).html(
                    '<tr><td colspan="' + colCount + '" class="text-center text-danger py-4"><i class="fa-solid fa-triangle-exclamation"></i> เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>'
                );
            }
        });
    }

    // Reload tab data (after an action)
    function reloadTab(tabIndex) {
        loadedTabs[tabIndex] = false;
        var tableIds = { '0': '#TableOder_P', '1': '#TableOder_A', '2': '#TableOder_C', '3': '#TableOder_SS' };
        var tableId = tableIds[tabIndex];
        if (tableId && $.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().destroy();
        }
        var colCount = (tabIndex === '0') ? 7 : 6;
        $('#tbody_tab' + tabIndex).html(
            '<tr><td colspan="' + colCount + '" class="text-center py-4"><div class="spinner-border text-secondary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>'
        );
        loadTabData(tabIndex);
    }

    function delOrder(OrderId) {
        Swal.fire({
            title: "คุณแน่ใจหรือไม่?",
            text: "คุณต้องการลบรายการนี้หรือไม่?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "ใช่",
            cancelButtonText: "ไม่ใช่"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "php_del_order.php",
                    data: { OrderId: OrderId },
                    success: function(data) {
                        if (data.status === 'success') {
                            Swal.fire({
                                position: "center",
                                title: data.message,
                                icon: "success",
                                showConfirmButton: false,
                                timer: 1000
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    }
                })
            }
        });
    }

    function Approv(order_ID, status, emp_ID) {
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
                    url: "php_approved.php",
                    dataType: "json",
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
                        });
                    },
                    success: function(data) {
                        Swal.close();
                        if (data.status === 'success') {
                            var titles = {
                                'A': 'อนุมัติแล้ว',
                                'C': 'ไม่อนุมัติรายการนี้',
                                'S': 'ยืนยันการสั่งซื้อแล้ว',
                                'SS': 'ยืนยันของเบิกมาส่งเรียบร้อยแล้ว',
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
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                position: "center",
                                icon: "error",
                                title: data.message,
                                showConfirmButton: false,
                                timer: 800
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: "error",
                            title: "เกิดข้อผิดพลาดในการเชื่อมต่อ",
                            text: "กรุณาลองใหม่อีกครั้ง",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                })
            }
        });
    }

    // ===== Tab 3 Filter =====
    function filterTab3() {
        var month = $('#filterMonth').val();
        var year = $('#filterYear').val();
        if (month && !year) {
            Swal.fire('กรุณาเลือกปี', '', 'warning');
            return;
        }
        // Force reload tab 3
        loadedTabs['3'] = false;
        var tableId = '#TableOder_SS';
        if ($.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().destroy();
        }
        $('#tbody_tab3').html('<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        loadTabData('3', { filterMonth: month, filterYear: year });
    }

    function resetFilterTab3() {
        $('#filterMonth').val('');
        $('#filterYear').val('');
        loadedTabs['3'] = false;
        var tableId = '#TableOder_SS';
        if ($.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().destroy();
        }
        $('#tbody_tab3').html('<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        loadTabData('3');
    }

    // ===== Admin Requisition Modal =====
    var adminReqItemsList = []; // {id, code, name, unit, qty}

    function adminReqRenderItems() {
        var tbody = $('#adminReqItems');
        tbody.empty();
        if (adminReqItemsList.length === 0) {
            tbody.html('<tr id="adminReqEmpty"><td colspan="5" class="text-center text-muted py-4"><i class="fa-solid fa-box-open fa-2x mb-2 opacity-25"></i><p class="mb-0">ยังไม่มีรายการ — ค้นหาและเลือกอุปกรณ์ด้านบน</p></td></tr>');
            return;
        }
        adminReqItemsList.forEach(function(item, idx) {
            var row = '<tr>' +
                '<td class="ps-3"><span class="badge bg-light text-secondary border">' + item.code + '</span></td>' +
                '<td class="fw-semibold small">' + item.name + '</td>' +
                '<td><div class="d-flex align-items-center justify-content-center gap-1">' +
                    '<button class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width:30px;height:30px;padding:0;" onclick="adminReqQty(' + idx + ',-1)"><i class="bi bi-dash"></i></button>' +
                    '<input type="text" value="' + item.qty + '" class="form-control form-control-sm text-center border-secondary fw-semibold" style="width:50px;" oninput="this.value=this.value.replace(/[^0-9]/g,\'\');" onchange="adminReqQtyManual(' + idx + ',this.value)" id="arq_' + idx + '">' +
                    '<button class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width:30px;height:30px;padding:0;" onclick="adminReqQty(' + idx + ',1)"><i class="bi bi-plus"></i></button>' +
                '</div></td>' +
                '<td class="text-center"><span class="text-muted small">' + item.unit + '</span></td>' +
                '<td><button class="btn btn-outline-danger btn-sm rounded-pill" onclick="adminReqRemove(' + idx + ')"><i class="bi bi-trash3"></i></button></td>' +
                '</tr>';
            tbody.append(row);
        });
    }

    function adminReqQty(idx, delta) {
        var newQty = adminReqItemsList[idx].qty + delta;
        if (newQty < 1) {
            adminReqRemove(idx);
            return;
        }
        adminReqItemsList[idx].qty = newQty;
        $('#arq_' + idx).val(newQty);
    }

    function adminReqQtyManual(idx, val) {
        var v = parseInt(val);
        if (isNaN(v) || v < 1) v = 1;
        adminReqItemsList[idx].qty = v;
        $('#arq_' + idx).val(v);
    }

    function adminReqRemove(idx) {
        adminReqItemsList.splice(idx, 1);
        adminReqRenderItems();
        // Refresh select2 to allow re-adding
        $('#adminReqEquipment').val(null).trigger('change');
    }

    $('#btnAdminReqSave').click(function() {
        var empID = $('#adminReqEmployee').val();
        if (!empID) {
            Swal.fire('กรุณาเลือกผู้เบิก', '', 'warning');
            return;
        }
        if (adminReqItemsList.length === 0) {
            Swal.fire('กรุณาเลือกรายการของเบิก', '', 'warning');
            return;
        }

        var items = adminReqItemsList.map(function(i) {
            return { equipment_Code: i.id, Qty: i.qty, Q_true: i.qty };
        });

        Swal.fire({
            icon: "question",
            title: "ยืนยันการเบิกเพิ่มเติม?",
            showDenyButton: true,
            confirmButtonText: "บันทึก",
            denyButtonText: "ยกเลิก"
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "php_order_save_admin.php",
                    dataType: "json",
                    data: {
                        employee_ID: empID,
                        items: JSON.stringify(items)
                    },
                    beforeSend: function() {
                        Swal.fire({
                            position: "center",
                            imageUrl: "image/icons8-load.gif",
                            title: 'กำลังดำเนินการ กรุณารอสักครู่...',
                            imageWidth: 100,
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });
                    },
                    success: function(data) {
                        Swal.close();
                        if (data.status === 'success') {
                            Swal.fire({
                                position: "center",
                                title: "บันทึกรายการเบิกเพิ่มเติมแล้ว",
                                imageUrl: "image/icons8-approved.gif",
                                imageWidth: 70,
                                showConfirmButton: false,
                                timer: 1200
                            }).then(function() {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({ icon: "error", title: data.message || "เกิดข้อผิดพลาด", timer: 2000, showConfirmButton: false });
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: "error", title: "เกิดข้อผิดพลาดในการเชื่อมต่อ", timer: 2000, showConfirmButton: false });
                    }
                });
            }
        });
    });

    // Tab switching — load data on demand
    $(document).ready(function() {
        // Init Select2 for admin requisition modal
        $('#adminReqEmployee').select2({
            dropdownParent: $('#adminReqModal'),
            width: '100%',
            placeholder: 'เลือกผู้เบิก'
        });

        $('#adminReqEquipment').select2({
            dropdownParent: $('#adminReqModal'),
            width: '100%',
            placeholder: 'พิมพ์ค้นหารหัสหรือชื่ออุปกรณ์',
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                url: 'php_search_equipment.php',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    var excludeIds = adminReqItemsList.map(function(i) { return i.id; }).join(',');
                    return { term: params.term, exclude: excludeIds };
                },
                processResults: function(data) {
                    return { results: data.results };
                }
            }
        });

        // When equipment selected, add to list
        $('#adminReqEquipment').on('select2:select', function(e) {
            var d = e.params.data;
            // Check duplicate
            for (var i = 0; i < adminReqItemsList.length; i++) {
                if (adminReqItemsList[i].id == d.id) return;
            }
            adminReqItemsList.push({ id: d.id, code: d.code, name: d.name, unit: d.unit, qty: 1 });
            adminReqRenderItems();
            $(this).val(null).trigger('change');
        });

        // Reset modal on close
        $('#adminReqModal').on('hidden.bs.modal', function() {
            adminReqItemsList = [];
            adminReqRenderItems();
            $('#adminReqEmployee').val(null).trigger('change');
            $('#adminReqEquipment').val(null).trigger('change');
        });

        // Restore saved tab from localStorage
        var savedTab = localStorage.getItem('selectedTab');
        if (savedTab) {
            var tabBtn = $('.nav-link[data-bs-target="#' + savedTab + '"]');
            if (tabBtn.length) {
                tabBtn.tab('show');
                var tabIndex = tabBtn.data('tab').toString();
                loadTabData(tabIndex);
            } else {
                loadTabData('0'); // Default
            }
        } else {
            loadTabData('0'); // Load first tab on page ready
        }

        // Load on tab switch
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            var tabIndex = $(e.target).data('tab').toString();
            var selectedTab = e.target.getAttribute('data-bs-target').substring(1);
            localStorage.setItem('selectedTab', selectedTab);
            loadTabData(tabIndex);
        });
    });
</script>