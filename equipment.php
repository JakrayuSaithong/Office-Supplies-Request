<?php
include('header.php');
// if (empty($_SESSION['employee_ID'])) {
//     header('location:index.php');
//     // exit();
// }
$page = "equipment";

$sqlCl = "SELECT catalog_ID, catalog_Name FROM tbl_catalogs  WHERE status = 1 ";
$resultCl = sqlsrv_query($conn, $sqlCl);
$dataCl = array();
while ($rowCl = sqlsrv_fetch_array($resultCl, SQLSRV_FETCH_ASSOC)) {
    $dataCl[] = $rowCl;
}

?>
<?php include('sidebar.php'); ?>
<div class="main">
    <?php include('navbar.php'); ?>
    <div class="container mt-3">
        <div class="mt-3 mb-2">
            <div class="row row-cols-12">
                <div class="col">
                    <h1><span class="fw-bold text-info fs-1">| </span>วัสดุอุปกรณ์</h1>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn rounded-pill btn-primary text-whie" data-bs-toggle="modal" data-bs-target="#modaladd">
                        <i class="bi bi-plus-circle-fill"></i> เพิ่มวัสดุอุปกรณ์
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white p-3 rounded-2">
            <div class="table-responsive">
                <table id="TableEquipment" class="table table-striped table-hover col-md-12">
                    <thead class="table">
                        <tr class="text-nowrap text-center">
                            <th>รหัสอุปกรณ์</th>
                            <th>อุปกรณ์</th>
                            <th>หน่วย</th>
                            <th>ประเภท</th>
                            <!-- <th>แคตตาล็อก</th> -->
                            <th>เพิ่มโดย</th>
                            <th>วันที่เพิ่ม</th>
                            <th>แก้ไข</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sqlTb = "SELECT tbl_equipments.equipment_ID,tbl_equipments.equipment_Code,tbl_equipments.equipment_Name,
                        tbl_equipments.add_by,tbl_equipments.add_date,
                        tbl_equipments.unit,tbl_catalogs.catalog_Name ,tbl_catalogs.catalog_ID,
                        tbl_equipments.edit_by,tbl_equipments.edit_date
                        FROM tbl_equipments
                        INNER JOIN tbl_catalogs 
                        ON tbl_equipments.catalog_ID = tbl_catalogs.catalog_ID 
                        WHERE tbl_equipments.status = '1' 
                        ORDER BY tbl_equipments.add_date DESC";
                        $resultTb = sqlsrv_query($conn, $sqlTb);
                        ?>
                        <?php while ($rowTb = sqlsrv_fetch_array($resultTb, SQLSRV_FETCH_ASSOC)) { ?>
                            <tr class="text-nowrap text-center">
                                <td><?php echo $rowTb['equipment_Code']; ?></td>
                                <td class="text-start"><?php echo $rowTb['equipment_Name']; ?></td>
                                <td><?php echo $rowTb['unit']; ?></td>
                                <!-- <td><?php echo $rowTb['category_Name']; ?></td> -->
                                <td><?php echo $rowTb['catalog_Name']; ?></td>
                                <td><?php echo showname($rowTb['add_by'])['site_f_366']; ?></td>
                                <td><?php echo !empty($rowTb['add_date']) ? $rowTb['add_date']->format('d/m/Y H:i') . " น." : "" ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn rounded-pill btn-warning" data-bs-toggle="modal" data-id="<?php echo $rowTb['equipment_ID']; ?>" onclick="editModel('<?php echo $rowTb['equipment_ID']; ?>')" data-bs-target="#editModal-<?php echo $rowTb['equipment_ID']; ?>">
                                        <i class="bi bi-pencil-square"></i> แก้ไข
                                    </button>
                                    <button onclick="delequipment(<?php echo $rowTb['equipment_ID'] ?>)" class="btn rounded-pill btn-danger ">
                                        <i class="bi bi-trash3"></i> ลบ
                                    </button>
                                </td>
                            </tr>

                            <!-- modal Edit -->
                            <div class="modal modal fade" id="editModal-<?php echo $rowTb['equipment_ID'] ?>" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h3 class="modal-title" id="editModalLabel"><i class="bi bi-pencil-square"></i> แก้ไขข้อมูล</h3>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- <form id="editequipment" method="post" action="php_edit_equipment.php"> -->
                                            <label class="mt-2" for="equipment_Code">รหัส</label>
                                            <input type="hidden" id="equipment_ID<?php echo $rowTb['equipment_ID'] ?>" name="equipment_ID" value="<?php echo $rowTb['equipment_ID'] ?>">
                                            <input class="form-control" type="text" id="equipment_Code<?php echo $rowTb['equipment_ID'] ?>" name="equipment_ID" value="<?php echo $rowTb['equipment_Code'] ?>">
                                            <label class="mt-2" for="equipment_Name">วัสดุอุปกรณ์</label>
                                            <input class="form-control" type="text" id="equipment_Name<?php echo $rowTb['equipment_ID'] ?>" name="equipment_Name" value="<?php echo $rowTb['equipment_Name'] ?>">
                                            <label class="mt-2" for="unit">หน่วย</label>
                                            <input class="form-control" type="text" id="unit<?php echo $rowTb['equipment_ID'] ?>" name="unit" value="<?php echo $rowTb['unit'] ?>">

                                            <label for="catalogE" class="form-label">ประเภท</label>
                                            <select name="catalog" id="catalogE-<?php echo $rowTb['equipment_ID'] ?>" class="form-select" onchange="editModel('<?php echo $rowTb['equipment_ID'] ?>')">
                                                <?php foreach ($dataCl as $rowCl) : ?>
                                                    <option <?php echo ($rowTb['catalog_ID'] == $rowCl['catalog_ID'] ? "selected" : "") ?> value="<?php echo $rowCl['catalog_ID'] ?>">
                                                        <?php echo $rowCl['catalog_Name'] ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>

                                            <?php if (!empty($rowTb['edit_by'])) { ?>
                                                <p class="fs-6 mt-2 text-body-tertiary" id="edit_bychk">
                                                    แก้ไขล่าสุดโดย : <span class="fs-6 mt-2" id="edit_by"><?php echo showName($rowTb['edit_by'])['site_f_366'] ?></span>
                                                    <span class="fs-6 mt-2"> เวลา : <span class="fs-6 mt-2" id="edit_date"><?php echo (empty($rowTb['edit_date']) ? "" : $rowTb['edit_date']->format('d/m/Y H:i')) ?></span></span>
                                                </p>
                                            <?php } ?>

                                            <div class="col text-end mb-1 mt-3 ">
                                                <button type="button" class="btn btn-secondary btn-lg rounded-pill" data-bs-dismiss="modal">ปิด</button>
                                                <button onclick="edit_equipment('<?php echo $rowTb['equipment_ID'] ?>')" type="submit" class="btn btn-warning btn-lg rounded-pill">บันทึก</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal add -->
    <div class="modal fade " id="modaladd" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content ">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel"><i class="bi bi-plus-circle-fill"></i> เพิ่มวัสดุอุปกรณ์</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="php_add_equipment.php" method="post" id="addequipment">

                        <label for="" class="form-label">รหัส</label>
                        <input type="text" class="form-control" name="equipment_Code" id="equipment_Code" required>

                        <label for="" class="form-label">วัสดุอุปกรณ์</label>
                        <input type="text" class="form-control" name="equipment_Name" id="equipment_Name" required>

                        <label for="" class="form-label">หน่วยนับ</label>
                        <input type="text" class="form-control" name="unit" id="equipment_Name" required>
                        <div class="row g-1">
                            <div class="col">
                                <label for="" class="form-label">ประเภท</label>
                                <select name="catalog" id="catalog" class="form-select" onchange="load_Cr()">
                                    <option value="0" selected id="catalog-null">* เลือกประเภท</option>
                                    <?php foreach ($dataCl as $rowCl) : ?>
                                        <option value="<?php echo $rowCl['catalog_ID'] ?>">
                                            <?php echo $rowCl['catalog_Name'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col text-end mb-1 mt-3 ">
                            <button type="button" class="btn btn-secondary btn-lg rounded-pill" data-bs-dismiss="modal">ปิด</button>
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <?php include('footer.php'); ?>
    <script>
        $("#catalog").select2({
            dropdownParent: $('#modaladd'),
            width: '100%',
            Class: 'form-select'
        })

        function editModel(equipment_Code) {
            // console.log(equipment_Code);
            $("#catalogE-" + equipment_Code).select2({
                dropdownParent: $('#editModal-' + equipment_Code),
                width: '100%',
            })
            $("#categoryE-" + equipment_Code).select2({
                dropdownParent: $('#editModal-' + equipment_Code),
                width: '100%'
            })


            selected_crcl(equipment_Code)
        }

        function selected_crcl(equipment_ID) {
            var catalog = $("#catalogE-" + equipment_ID).val();
            var category = $("#categoryE-" + equipment_ID).val();

            console.log(catalog, category)
            $.ajax({
                url: "php_selected_clcr.php",
                type: "POST",
                dataType: "json",
                data: {
                    catalog: catalog,
                    category: category
                },
                success: function(data) {
                    $("#categoryE-" + equipment_ID).html(data.category)
                }
            })
        }

        $('#TableEquipment').dataTable({
            // "order": [
            //     [0, 'DESC']
            // ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json',
            },
            stateSave: true
        })

        $(document).ready(function() {
            $("#addequipment").submit(function(e) {
                e.preventDefault();

                let equipmentURL = $(this).attr("action");
                let reqMethod = $(this).attr("method");
                let formData = $(this).serialize();

                let catalogNull = $("#catalog").val();
                let categoryNull = $("#category").val();

                if (catalogNull === "0") {
                    Swal.fire({
                        icon: "warning",
                        title: "กรุณาเลือกประเภท!"
                    })
                } else {
                    $.ajax({
                        url: equipmentURL,
                        type: reqMethod,
                        data: formData,

                        success: function(data) {
                            let result = JSON.parse(data);
                            if (result.status == "success") {
                                console.log("Success", result);

                                Swal.fire({
                                    title: "สำเร็จ!",
                                    text: result.msg,
                                    icon: result.status,
                                    timer: 1000,
                                    showConfirmButton: false
                                }).then(function() {
                                    window.location.href = "equipment.php?addequipment=1";
                                })
                            } else {
                                console.log("Error", result)
                                Swal.fire("อ้ะ!", result.msg, result.status).then(function() {
                                    window.location.reload();
                                })
                            }
                        }
                    })
                }
            })
        })


        function edit_equipment(equipment_ID) {
            // console.log(equipment_Code)
            var equipment_Code = $("#equipment_Code" + equipment_ID).val()
            var equipment_Name = $("#equipment_Name" + equipment_ID).val()
            var unit = $("#unit" + equipment_ID).val()
            var catalog = $("#catalogE-" + equipment_ID).val()
            var equipment_ID = $("#equipment_ID" + equipment_ID).val()

            // console.log(equipment_Code)
            // console.log(equipment_Name)
            // console.log(unit)
            // console.log(catalog)
            // console.log(category)
            // console.log(equipment_ID)

            $.ajax({
                url: "php_edit_equipment.php",
                type: "POST",
                datatype: "json",
                data: {
                    equipment_Code: equipment_Code,
                    equipment_Name: equipment_Name,
                    unit: unit,
                    catalog: catalog,
                    equipment_ID: equipment_ID
                },
                success: function(data) {
                    let result = JSON.parse(data);
                    // console.log(result.chk)
                    if (result.status == "success") {
                        // console.log("Success", result.chk)
                        Swal.fire({
                            title: "สำเร็จ!",
                            text: result.msg,
                            icon: result.status,
                            timer: 1000,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.reload();
                        })
                    } else {
                        console.log("Error", result)
                        Swal.fire("อ้ะ!", result.msg, result.status).then(function() {
                            window.location.reload();
                        })
                    }
                }
            })

        }

        function delequipment(equipmentId) {
            Swal.fire({
                title: "คุณแน่ใจหรือไม่?",
                text: "คุณต้องการลบวัสดุนี้หรือไม่?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "ใช่",
                cancelButtonText: "ไม่ใช่"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "php_del_equipment.php?equipment_id=" + equipmentId;
                }
            });
        }
    </script>