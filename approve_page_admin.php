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
                    <div class="table-responsive-xxl p-3">
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

    function loadTabData(tabIndex) {
        if (loadedTabs[tabIndex]) return; // Already loaded

        $.ajax({
            type: "POST",
            url: "php_load_tab_admin.php",
            dataType: "json",
            data: { tab: tabIndex },
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
                    }
                })
            }
        });
    }

    // Tab switching — load data on demand
    $(document).ready(function() {
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