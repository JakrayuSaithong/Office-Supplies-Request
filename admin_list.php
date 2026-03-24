<?php
session_start();
include_once('condb.php');

if(isset($_POST['selectedAdmin'])){
    $selectedAdmin = $_POST['selectedAdmin'];
    $userUpdate = $_SESSION['employee_ID'];
    $date = date("Y-m-d H:i:s");

    $sql_per = "UPDATE tbl_permission SET
    per_user = '$selectedAdmin',
    per_userupdate = '$userUpdate',
    per_dateupdate = '$date'
    WHERE per_id = 1";

    if ($resultUpdate = sqlsrv_query($conn, $sql_per)) {
        echo json_encode(array("status" => "success", "msg" => "แก้ไขเรียบร้อยแล้ว"));
    } else {
        $errors = sqlsrv_errors();
        foreach ($errors as $error) {
            echo "SQLSTATE: " . $error['SQLSTATE'] . "<br />";
            echo "Code: " . $error['code'] . "<br />";
            echo "Message: " . $error['message'] . "<br />";
        }
        echo json_encode(array("status" => "error", "msg" => "Update failed"));
    }
    exit;
}

if(isset($_POST['selectedUser'])){
    $selectedUser = $_POST['selectedUser'];

    $sql_per = "
        UPDATE work_progress_288 SET
        site_f_6720 = '$selectedUser'
        WHERE site_id_288 = 171
        ";

    if ($resultUpdate = mysqli_query($connmy, $sql_per)) {
        echo json_encode(array("status" => "success", "msg" => "แก้ไขเรียบร้อยแล้ว"));
    } else {
        $errors = mysqli_error($connmy);
        foreach ($errors as $error) {
            echo "SQLSTATE: " . $error['SQLSTATE'] . "<br />";
            echo "Code: " . $error['code'] . "<br />";
            echo "Message: " . $error['message'] . "<br />";
        }
        echo json_encode(array("status" => "error", "msg" => "Update failed"));
    }
    exit;
}

$datepermission = permiss_get_data();
$arrayPermis = json_decode($datepermission[0]['per_user'], true);

$arrayPermisEmp = json_decode(permiss_get_data_my()[0], true);

include('header.php');
$page = "adminlist";
?>
<?php include('sidebar.php'); ?>
<div class="main">
    <?php include('navbar.php'); ?>
    <div class="container" style="max-width: 800px;">
        <div class="mt-4 mb-4">
            <h4 class="fw-bold mb-1"><i class="fa-solid fa-shield-halved text-primary me-2"></i>กำหนดสิทธิ์</h4>
            <p class="text-muted small mb-0">จัดการสิทธิ์ Admin และผู้ใช้งานระบบ</p>
        </div>

        <!-- Admin Permission Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; overflow: hidden;">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-user-shield text-primary"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">ผู้มีสิทธิ์ Admin</h6>
                        <small class="text-muted">เลือกพนักงานที่สามารถจัดการระบบได้</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" id="searchAdmin" placeholder="ค้นหาชื่อพนักงาน..." autocomplete="off">
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">เลือกแล้ว <span id="adminCount" class="fw-bold text-primary">0</span> คน</small>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 me-1" id="adminShowChecked"><i class="fa-solid fa-filter me-1"></i>ผู้ที่มีรายชื่อ</button>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 me-1" id="adminSelectAll">เลือกทั้งหมด</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="adminDeselectAll">ยกเลิกทั้งหมด</button>
                    </div>
                </div>
                <div id="adminList" style="max-height: 360px; overflow-y: auto;">
                    <?php
                    $sqlmy = "SELECT
                        site_f_366 as f_name,
                        site_f_1188 as PrefixName,
                        site_f_365 as id_employee
                        FROM work_progress_010 WHERE site_f_3005 = '600' AND (site_f_398 = '0000-00-00' OR site_f_398 > CURRENT_DATE())
                        ORDER BY site_f_366 ASC";
                    $resultmy = mysqli_query($connmy, $sqlmy);
                    foreach ($resultmy as $p => $codemy) {
                        $checked = in_array($codemy['id_employee'], $arrayPermis) ? "checked" : "";
                    ?>
                    <label class="admin-check-item d-flex align-items-center gap-3 p-2 rounded-3 mb-1 <?php echo $checked ? 'active' : ''; ?>" data-name="<?php echo strtolower($codemy['f_name']); ?>">
                        <input type="checkbox" class="form-check-input admin-cb m-0 flex-shrink-0" value="<?php echo $codemy['id_employee']; ?>" <?php echo $checked; ?> style="width: 20px; height: 20px;">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;">
                            <i class="fa-solid fa-user text-primary" style="font-size: 14px;"></i>
                        </div>
                        <div class="text-truncate">
                            <div class="fw-medium" style="font-size: 14px;"><?php echo $codemy['f_name']; ?></div>
                            <div class="text-muted" style="font-size: 12px;"><?php echo $codemy['id_employee']; ?></div>
                        </div>
                    </label>
                    <?php } ?>
                </div>
            </div>
            <div class="card-footer bg-white border-top text-end py-3 px-4">
                <button class="btn btn-primary rounded-pill px-4" id="SaveAdmin">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึก
                </button>
            </div>
        </div>

        <!-- User Access Card (restricted) -->
        <?php if($_SESSION['employee_ID'] == '640300021' || $_SESSION['employee_ID'] == '660500122' || $_SESSION['employee_ID'] == '570311103'){ ?>
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; overflow: hidden;">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-users text-success"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">ผู้มีสิทธิ์เข้าใช้งาน</h6>
                        <small class="text-muted">เลือกพนักงานที่สามารถเข้าใช้ระบบได้</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" id="searchUser" placeholder="ค้นหาชื่อพนักงาน..." autocomplete="off">
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">เลือกแล้ว <span id="userCount" class="fw-bold text-success">0</span> คน</small>
                    <div>
                        <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 me-1" id="userShowChecked"><i class="fa-solid fa-filter me-1"></i>ผู้ที่มีรายชื่อ</button>
                        <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 me-1" id="userSelectAll">เลือกทั้งหมด</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="userDeselectAll">ยกเลิกทั้งหมด</button>
                    </div>
                </div>
                <div id="userList" style="max-height: 360px; overflow-y: auto;">
                    <?php
                    $sqlmy = "SELECT
                        site_f_366 as f_name,
                        site_f_1188 as PrefixName,
                        site_id_10 as id_employee,
                        site_f_365 as code_employee
                        FROM work_progress_010 WHERE site_f_3005 = '600' AND (site_f_398 = '0000-00-00' OR site_f_398 > CURRENT_DATE())
                        ORDER BY site_f_366 ASC";
                    $resultmy = mysqli_query($connmy, $sqlmy);
                    foreach ($resultmy as $k => $val) {
                        $checked = array_key_exists($val['id_employee'], $arrayPermisEmp) ? "checked" : "";
                    ?>
                    <label class="user-check-item d-flex align-items-center gap-3 p-2 rounded-3 mb-1 <?php echo $checked ? 'active' : ''; ?>" data-name="<?php echo strtolower($val['f_name']); ?>">
                        <input type="checkbox" class="form-check-input user-cb m-0 flex-shrink-0" value="<?php echo $val['id_employee']; ?>" <?php echo $checked; ?> style="width: 20px; height: 20px;">
                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;">
                            <i class="fa-solid fa-user text-success" style="font-size: 14px;"></i>
                        </div>
                        <div class="text-truncate">
                            <div class="fw-medium" style="font-size: 14px;"><?php echo $val['f_name']; ?></div>
                            <div class="text-muted" style="font-size: 12px;"><?php echo $val['code_employee']; ?></div>
                        </div>
                    </label>
                    <?php } ?>
                </div>
            </div>
            <div class="card-footer bg-white border-top text-end py-3 px-4">
                <button class="btn btn-success rounded-pill px-4" id="SetUser">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึก
                </button>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<?php include('footer.php'); ?>
<style>
    .admin-check-item, .user-check-item {
        cursor: pointer;
        transition: background-color 0.15s;
        border: 1px solid transparent;
    }
    .admin-check-item:hover { background-color: #e8f0fe; }
    .user-check-item:hover { background-color: #e8f5e9; }
    .admin-check-item.active { background-color: #e8f0fe; border-color: #90caf9; }
    .user-check-item.active { background-color: #e8f5e9; border-color: #a5d6a7; }
    #adminList::-webkit-scrollbar, #userList::-webkit-scrollbar { width: 6px; }
    #adminList::-webkit-scrollbar-thumb, #userList::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        // --- Count helpers ---
        function updateAdminCount() {
            $('#adminCount').text($('.admin-cb:checked').length);
        }
        function updateUserCount() {
            $('#userCount').text($('.user-cb:checked').length);
        }
        updateAdminCount();
        updateUserCount();

        // --- Toggle active class on check ---
        $(document).on('change', '.admin-cb', function() {
            $(this).closest('.admin-check-item').toggleClass('active', this.checked);
            updateAdminCount();
        });
        $(document).on('change', '.user-cb', function() {
            $(this).closest('.user-check-item').toggleClass('active', this.checked);
            updateUserCount();
        });

        // --- Filter helpers ---
        var adminShowCheckedOnly = false;
        var userShowCheckedOnly = false;

        function filterAdmin() {
            var q = $.trim($('#searchAdmin').val()).toLowerCase();
            $('#adminList').find('.admin-check-item').each(function() {
                var el = $(this);
                var itemText = el[0].textContent.toLowerCase();
                var nameMatch = (q === '') || itemText.indexOf(q) !== -1;
                var checkedMatch = !adminShowCheckedOnly || el.find('.admin-cb').is(':checked');
                if (nameMatch && checkedMatch) {
                    el.css('display', 'flex');
                } else {
                    el.attr('style', 'display: none !important;');
                }
            });
        }
        function filterUser() {
            var q = $.trim($('#searchUser').val()).toLowerCase();
            $('#userList').find('.user-check-item').each(function() {
                var el = $(this);
                var itemText = el[0].textContent.toLowerCase();
                var nameMatch = (q === '') || itemText.indexOf(q) !== -1;
                var checkedMatch = !userShowCheckedOnly || el.find('.user-cb').is(':checked');
                if (nameMatch && checkedMatch) {
                    el.css('display', 'flex');
                } else {
                    el.attr('style', 'display: none !important;');
                }
            });
        }

        $(document).on('input', '#searchAdmin', function() { filterAdmin(); });
        $(document).on('input', '#searchUser', function() { filterUser(); });

        // --- Show checked only toggle ---
        $(document).on('click', '#adminShowChecked', function() {
            adminShowCheckedOnly = !adminShowCheckedOnly;
            $(this).toggleClass('btn-outline-primary btn-primary');
            filterAdmin();
        });
        $(document).on('click', '#userShowChecked', function() {
            userShowCheckedOnly = !userShowCheckedOnly;
            $(this).toggleClass('btn-outline-success btn-success');
            filterUser();
        });

        // --- Select all / Deselect all ---
        $('#adminSelectAll').click(function() {
            $('#adminList .admin-check-item:visible .admin-cb').prop('checked', true).closest('.admin-check-item').addClass('active');
            updateAdminCount();
        });
        $('#adminDeselectAll').click(function() {
            $('.admin-cb').prop('checked', false).closest('.admin-check-item').removeClass('active');
            updateAdminCount();
        });
        $('#userSelectAll').click(function() {
            $('#userList .user-check-item:visible .user-cb').prop('checked', true).closest('.user-check-item').addClass('active');
            updateUserCount();
        });
        $('#userDeselectAll').click(function() {
            $('.user-cb').prop('checked', false).closest('.user-check-item').removeClass('active');
            updateUserCount();
        });

        // --- Save Admin ---
        $('#SaveAdmin').click(function() {
            var selectedValues = [];
            $('.admin-cb:checked').each(function() {
                selectedValues.push($(this).val());
            });
            $.ajax({
                type: "POST",
                url: "admin_list.php",
                data: { selectedAdmin: JSON.stringify(selectedValues) },
                success: function(data) {
                    Swal.fire({
                        position: "center",
                        title: "กำหนดสิทธิ์เรียบร้อยแล้ว",
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1000
                    }).then(() => { window.location.assign("admin_list.php"); });
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'บันทึกข้อมูลไม่สำเร็จ' });
                }
            });
        });

        // --- Save User ---
        $('#SetUser').click(function() {
            var JsonUser = {};
            $('.user-cb:checked').each(function() {
                JsonUser[$(this).val()] = 1;
            });
            $.ajax({
                type: "POST",
                url: "admin_list.php",
                data: { selectedUser: JSON.stringify(JsonUser) },
                success: function(data) {
                    Swal.fire({
                        position: "center",
                        title: "กำหนดสิทธิ์เข้าใช้งานเรียบร้อยแล้ว",
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1000
                    }).then(() => { window.location.assign("admin_list.php"); });
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'บันทึกข้อมูลไม่สำเร็จ' });
                }
            });
        });
    });
</script>