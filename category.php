<?php
$page = "category";
include('header.php');

if (empty($_SESSION['employee_ID'])) {
    header('location:index.php');
    // exit();
}

?>
<?php include('sidebar.php'); ?>

<div class="main">

    <?php include('navbar.php'); ?>
    <div class="container mt-3">
        <div class="mt-3 mb-2">
            <div class="row row-cols-12">
                <div class="col">
                    <h1><span class="fw-bold text-info fs-1">| </span> หมวดหมู่</h1>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <i class="bi bi-plus-circle-fill"></i> เพิ่มหมวดหมู่
                    </button>
                </div>
            </div>
        </div>



        <div class="bg-white p-3 rounded-2">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-hover col-md-12">
                    <thead>
                        <tr class="text-nowrap text-center">
                            <th width="10">ลำดับ</th>
                            <th>ชื่อหมวดหมู่</th>
                            <th>ประเภท</th>
                            <th>สร้างโดย</th>
                            <th>วันที่เพิ่ม</th>
                            <th width="60" class="text-center">แก้ไข</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sqlTb = "SELECT tbl_categorys.category_Name, tbl_categorys.add_by, 
                        tbl_categorys.add_date, tbl_categorys.category_ID, tbl_categorys.catalog_ID, 
                        tbl_catalogs.catalog_Name, tbl_categorys.edit_by, tbl_categorys.edit_date
                        FROM tbl_categorys
                        LEFT JOIN tbl_catalogs 
                        ON tbl_catalogs.catalog_ID = tbl_categorys.catalog_ID
                        WHERE tbl_categorys.status = '1' 
                        ORDER BY tbl_categorys.add_date DESC";
                        $resultTb = sqlsrv_query($conn, $sqlTb);
                        $i = 1;
                        while ($rowTb = sqlsrv_fetch_array($resultTb, SQLSRV_FETCH_ASSOC)) :
                        ?>
                            <tr class="text-nowrap">
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td><?php echo $rowTb['category_Name']; ?></td>
                                <td><?php echo $rowTb['catalog_Name'] ?></td>
                                <td><?php echo showname($rowTb['add_by'])['site_f_366']; ?></td>
                                <td><?php echo !empty($rowTb['add_date']) ? $rowTb['add_date']->format('d/m/Y H:i') . " น." : "" ?></td>
                                <td class="text-center">
                                    <button type="button" onclick="editFrom('<?php echo $rowTb['catalog_ID'] ?>','<?php echo $rowTb['category_ID'] ?>')" class="btn btn-warning rounded-pill" data-bs-toggle="modal" data-bs-target="#editcategorys-<?php echo $rowTb['category_ID'] ?>">
                                        <i class="bi bi-pencil-square"></i> แก้ไข
                                    </button>
                                    <button onclick="delcategory(<?php echo $rowTb['category_ID'] ?>)" class="btn btn-danger rounded-pill">
                                        <i class="bi bi-trash3"></i> ลบ
                                    </button>
                                </td>
                            </tr>

                            <!-- Button trigger modal Edit -->
                            <div class="modal modal fade" id="editcategorys-<?php echo $rowTb['category_ID'] ?>" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h3 class="modal-title" id="editModalLabel"><i class="bi bi-pencil-square"></i> แก้ไขข้อมูล</h3>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- <form action="php_edit_category.php" method="post" id="editcategoryF-<?php echo $rowTb['category_ID'] ?>"> -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="" class="form-label">หมวดหมู่</label>
                                                    <input type="text" class="form-control" name="category_Name" id="category_Name<?php echo $rowTb['category_ID'] ?>" value="<?php echo $rowTb['category_Name'] ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="" class="form-label">ประเภท</label>
                                                    <select name="catalog_ID" id="catalogE-<?php echo $rowTb['category_ID'] ?>">
                                                        <?php
                                                        $catalogE = sqlsrv_query($conn, "SELECT catalog_Name, catalog_ID FROM tbl_catalogs WHERE status = '1'");
                                                        while ($r_catalogE = sqlsrv_fetch_array($catalogE, SQLSRV_FETCH_ASSOC)) { ?>
                                                            <option <?php echo ($rowTb['catalog_ID'] == $r_catalogE['catalog_ID']) ? "selected" : "" ?> value="<?php echo $r_catalogE['catalog_ID'] ?>"><?php echo $r_catalogE['catalog_Name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php if (!empty($rowTb['edit_by'])) { ?>
                                                <p class="fs-6 mt-2 text-body-tertiary">
                                                    แก้ไขล่าสุดโดย : <span class="fs-6 mt-2" id="edit_by"><?php echo showName($rowTb['edit_by'])['site_f_366'] ?></span>
                                                    <span class="fs-6 mt-2"> เวลา : <span class="fs-6 mt-2" id="edit_date"><?php echo (!empty($rowTb['edit_date']) ? $rowTb['edit_date']->format('d/m/Y H:i') : "") ?></span></span>
                                                </p>
                                            <?php } ?>
                                            <!-- <button onclick="save_edit('<?php echo $rowTb['catalog_ID'] ?>','<?php echo $rowTb['category_ID'] ?>')" class="btn btn-warning btn-sm mt-3 w-100 ">บันทึกการแก้ไข</button> -->
                                            <!-- </form> -->
                                            <div class="col text-end mb-3">
                                                <button  type="button" class="btn btn-secondary rounded-pill btn-lg" data-bs-dismiss="modal">ปิด</button>
                                                <button onclick="save_edit('<?php echo $rowTb['catalog_ID'] ?>','<?php echo $rowTb['category_ID'] ?>')" type="submit" class="btn btn-warning rounded-pill btn-lg">บันทึก</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal add -->
<div class="modal fade " id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel"><i class="bi bi-plus-circle-fill"> </i>เพิ่มหมวดหมู่</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="php_add_category.php" method="post" id="addcategory">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="" class="form-label">หมวดหมู่</label>
                            <input type="text" class="form-control" name="category_Name" id="category_Name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="" class="form-label">ประเภท</label>
                            <select name="catalog_ID" id="catalogA">
                                <option value="" selected>กรุณาเลือกประเภท</option>
                                <?php
                                $catalogA = sqlsrv_query($conn, "SELECT catalog_Name, catalog_ID FROM tbl_catalogs WHERE status = '1'");
                                while ($r_catalogA = sqlsrv_fetch_array($catalogA, SQLSRV_FETCH_ASSOC)) {
                                ?>
                                    <option value="<?php echo $r_catalogA['catalog_ID'] ?>"><?php echo $r_catalogA['catalog_Name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
            </div>
            <div class="col text-end mb-3 me-3">
                <button type="button" class="btn btn-secondary btn-lg rounded-pill" data-bs-dismiss="modal">ปิด</button>
                <button type="submit" class="btn btn-primary btn-lg rounded-pill">บันทึก</button>
            </div>
            </form>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<script type="text/javascript">
    function editFrom(catalogID, categoryID) {
        $('#catalogE-' + categoryID).select2({
            dropdownParent: $('#editcategorys-' + categoryID),
            width: '100%'
        });
    }

    function save_edit(catalog_ID, category_ID) {
        var category_Name = $("#category_Name" + category_ID).val()
        var catalog_ID = $("#catalogE-" + category_ID).val()

        $.ajax({
            url: "php_edit_category.php",
            type: "POST",
            datatype: "json",
            data: {
                category_Name: category_Name,
                catalog_ID: catalog_ID,
                category_ID: category_ID
            },
            success: function(data) {
                let result = JSON.parse(data);
                if (result.status == "success") {
                    // console.log("Success", result.chk)
                    Swal.fire({
                        title: "สำเร็จ!",
                        text: result.msg,
                        icon: result.status,
                        timer: 800,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.reload();
                    });
                } else {
                    console.log("Error", result)
                    Swal.fire("อ้ะ!", result.msg, result.status).then(function() {
                        window.location.reload();
                    })
                }
            }
        })
    }


    $('#myTable').dataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json',
        },
        stateSave: true
    })

    $('#catalogA').select2({
        dropdownParent: $('#exampleModal'),
        width: '100%'
    });


    $(document).ready(function() {
        $("#addcategory").submit(function(e) {
            e.preventDefault();

            // let categoryURL = $(this).attr("action");
            let reqMethod = $(this).attr("method");
            let formData = $(this).serialize();
            let catalog = $("#catalogA").val();

            if (catalog === "") {
                Swal.fire({
                    icon: "warning",
                    title: "กรุณาเลือกประเภท!",
                })
            } else {
                // ทำ AJAX เมื่อค่าไม่ว่างเปล่า
                $.ajax({
                    url: "php_add_category.php",
                    type: reqMethod,
                    data: formData,

                    success: function(data) {
                        let result = JSON.parse(data);
                        if (result.status == "success") {
                            console.log("Success", result);
                            Swal.fire({
                                icon: "success",
                                title: "สำเร็จ!",
                                text: result.msg,
                            }).then(function() {
                                window.location.reload();
                            });
                        } else {
                            console.log("Error", result);
                            Swal.fire({
                                icon: "error",
                                title: "อ้ะ!",
                                text: result.msg,
                            }).then(function() {
                                window.location.reload();
                            });
                        }
                    },
                });
            }
        });
    });




    function delcategory(categoryId) {
        Swal.fire({
            title: "คุณแน่ใจหรือไม่?",
            text: "คุณต้องการลบหมวดนี้หรือไม่?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "ใช่",
            cancelButtonText: "ไม่ใช่"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "php_del_category.php?category_ID=" + categoryId;
            }
        });
    }
</script>