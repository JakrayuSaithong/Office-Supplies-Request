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
    <div class="container mt-3">
        <div class="mt-3 mb-2">
            <div class="row row-cols-12">
                <div class="col">
                    <h1><span class="fw-bold text-info fs-1">|</span> กำหนดสิทธิ์ Admin</h1>
                </div>
            </div>
        </div>

        <div class="p-3 rounded-2 bg-white">
            <div class="col-md-12">
                <label for="" class="form-label">รายชื่อผู้มีสิทธิ์ Admin</label>
                <select name="User_ID[]" id="User_ID" multiple="multiple">
                    <?php
                    $sqlmy = "SELECT 
                        site_f_366 as f_name,
                        site_f_1188 as PrefixName,
                        site_f_365 as id_employee
                        FROM work_progress_010 WHERE site_f_3005 = '600' AND (site_f_398 = '0000-00-00' OR site_f_398 > CURRENT_DATE())";
                    $resultmy = mysqli_query($connmy, $sqlmy);

                    foreach ($resultmy as $p => $codemy) {
                        if (in_array($codemy['id_employee'], $arrayPermis)) {
                            $selected = "selected";
                        } else {
                            $selected = "";
                        }
                    ?>
                    <option value="<?php echo $codemy['id_employee']; ?>" <?php echo $selected; ?> ><?php echo $codemy['f_name']; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-12 mt-3 text-end">
                <button class="btn btn-success rounded-pill ms-2" id="SaveAdmin"> <i class="fa-solid fa-floppy-disk"></i></i> บันทึก</button>
            </div>
        </div>
    </div>

    <?php if($_SESSION['employee_ID'] == '640300021' || $_SESSION['employee_ID'] == '660500122' || $_SESSION['employee_ID'] == '570311103'){ ?>
    <div class="container mt-3">
        <div class="p-3 rounded-2 bg-white">
            <div class="col-md-12">
                <label for="" class="form-label">รายชื่อผู้มีสิทธิ์เข้าใช้งาน</label>
                <select name="Users_ID[]" id="Users_ID" multiple="multiple">
                    <?php
                    $sqlmy = "SELECT 
                        site_f_366 as f_name, 
                        site_f_1188 as PrefixName, 
                        site_id_10 as id_employee
                        FROM work_progress_010 WHERE site_f_3005 = '600' AND (site_f_398 = '0000-00-00' OR site_f_398 > CURRENT_DATE())";
                    $resultmy = mysqli_query($connmy, $sqlmy);

                    foreach ($resultmy as $k => $val) {
                        if (array_key_exists($val['id_employee'], $arrayPermisEmp)) {
                            $selected = "selected";
                        } else {
                            $selected = "";
                        }
                    ?>
                    <option value="<?php echo $val['id_employee']; ?>" <?php echo $selected; ?> ><?php echo $val['f_name']; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-12 mt-3 text-end">
                <button class="btn btn-success rounded-pill ms-2" id="SetUser"> <i class="fa-solid fa-floppy-disk"></i></i> บันทึก</button>
            </div>
        </div>
    </div>
    <?php } ?>

</div>
<?php include('footer.php'); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#User_ID').select2({
            width: '100%',
            placeholder: "กรุณาเลือกผู้มีสิทธิ์ Admin",
            allowClear: true
        });

        $('#Users_ID').select2({
            width: '100%',
            placeholder: "กรุณาเลือกผู้มีสิทธิ์เข้าใช้งาน",
            allowClear: true
        });

        $('#SaveAdmin').click(function() {
            var selectedValues = $('#User_ID').val();
            selectedValues = JSON.stringify(selectedValues);
            
            $.ajax({
                type: "POST",
                url: "admin_list.php",
                data: {
                    selectedAdmin: selectedValues
                },
                success: function(data) {
                    // console.log(data);
                    // console.log("Item added to cart: " + data.message)
                    Swal.fire({
                        position: "center",
                        title: "กำหนดสิทธิ์เรียบร้อยแล้ว",
                        imageUrl: "image/icons8-cart.gif",
                        imageWidth: 100,
                        showConfirmButton: false,
                        timer: 1000
                    }).then((result) => {
                        window.location.assign("admin_list.php");
                    });
                },
                error: function (error) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'เพิ่มข้อมูลไม่สำเร็จ',
                        text: 'บันทึกข้อมูลไม่สำเร็จ'
                    }).then((result) => {
                        if(result){
                            window.location.href = 'admin_list.php';
                        }
                    })
                }
            })
        });


        $('#SetUser').click(function() {
            var selectedValues = $('#Users_ID').val();
            // selectedValues = JSON.stringify(selectedValues);
            var JsonUser = {};
            $.each(selectedValues, function(key, value) {
                JsonUser[value] = 1;
            });

            JsonUser = JSON.stringify(JsonUser);
            
            $.ajax({
                type: "POST",
                url: "admin_list.php",
                data: {
                    selectedUser: JsonUser
                },
                success: function(data) {
                    // console.log(data);
                    // console.log("Item added to cart: " + data.message)
                    Swal.fire({
                        position: "center",
                        title: "กำหนดสิทธิ์เข้าใช้งานเรียบร้อยแล้ว",
                        imageUrl: "image/icons8-cart.gif",
                        imageWidth: 100,
                        showConfirmButton: false,
                        timer: 1000
                    }).then((result) => {
                        window.location.assign("admin_list.php");
                    });
                },
                error: function (error) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'เพิ่มข้อมูลไม่สำเร็จ',
                        text: 'บันทึกข้อมูลไม่สำเร็จ'
                    }).then((result) => {
                        if(result){
                            window.location.href = 'admin_list.php';
                        }
                    })
                }
            })
        });
    })
</script>