<?php
include('header.php');
$page = "catalog";
?>
<?php include('sidebar.php'); ?>
<div class="main">
    <?php include('navbar.php'); ?>
    <div class="container mt-3">
        <div class="mt-3 mb-2">
            <div class="row row-cols-12">
                <div class="col">
                    <h1><span class="fw-bold text-info fs-1">|</span> ประเภท</h1>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <i class="bi bi-plus-circle-fill"></i> เพิ่มประเภท
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal add -->
        <div class="modal fade " id="exampleModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content ">
                    <div class="modal-header ">
                        <h3 class="modal-title" id="exampleModalLabel"><i class="bi bi-plus-circle-fill"></i> เพิ่มประเภท</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="php_add_catalog.php" method="POST" id="addcatalog">
                            <label for="catalog_Name" class="form-label">ชื่อประเภท</label>
                            <input type="text" id="catalog_Name" name="catalog_Name" class="form-control" required>
                    </div>
                    <div class="col text-end mb-3 me-3">
                        <button type="button" class="btn btn-secondary rounded-pill btn-lg" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary rounded-pill btn-lg">บันทึก</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="p-3 rounded-2 bg-white">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-hover col-md-12">
                    <thead>
                        <tr class="text-nowrap">
                            <th width="10">ลำดับ</th>
                            <th>ชื่อประเภท</th>
                            <th>เพิ่มโดย</th>
                            <th>วันที่เพิ่ม</th>
                            <th width="60" class="text-center">แก้ไข</th>
                            <th width="80" class="text-center">ลบ</th>
                            <th width="80" class="text-center">Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sqlTb = "SELECT catalog_Name, add_by, add_date, catalog_ID, edit_by, edit_date, active FROM tbl_catalogs WHERE status = '1'";
                        $resultTb = sqlsrv_query($conn, $sqlTb);
                        $i = 1;
                        while ($rowTb = sqlsrv_fetch_array($resultTb, SQLSRV_FETCH_ASSOC)) :
                        ?>
                            <tr class="text-nowrap">
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td><?php echo $rowTb['catalog_Name']; ?></td>
                                <td><?php echo showname($rowTb['add_by'])['site_f_366']; ?></td>
                                <td><?php echo $rowTb['add_date']->format('d/m/y เวลา H:i น.'); ?></td>
                                <td class="text-center">
                                    <!-- <a href="catalog.php?catalog_ID=<?php echo $rowTb['catalog_ID']; ?>"
                            class="btn btn-lg rounded-pill btn-warning text-light">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a> -->
                                    <button type="button" class="btn rounded-pill btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $rowTb['catalog_ID'] ?>" data-id="<?php echo $rowTb['catalog_ID'] ?>">
                                        <i class="bi bi-pencil-square"></i> แก้ไข
                                    </button>
                                </td>
                                <td class="text-center">
                                    <button onclick="delcatalog(<?php echo $rowTb['catalog_ID'] ?>)" class="btn btn-lg btn-danger rounded-pill">
                                        <i class="bi bi-trash3"></i> ลบ
                                    </button>
                                </td>
                                <td class="text-center">
                                    <?php if($rowTb['active'] == 1){ ?>
                                    <button onclick="activelog(<?php echo $rowTb['catalog_ID'] ?>, 0)" class="btn btn-lg btn-success rounded-pill">
                                        <i class="bi bi-trash3"></i> เปิดใช้งาน
                                    </button>
                                    <?php } ?>

                                    <?php if($rowTb['active'] == 0){ ?>
                                    <button onclick="activelog(<?php echo $rowTb['catalog_ID'] ?>, 1)" class="btn btn-lg btn-warning rounded-pill">
                                        <i class="bi bi-trash3"></i> ปิดใช้งาน
                                    </button>
                                    <?php } ?>
                                </td>
                            </tr>

                            <!-- Modal edit -->
                            <div class="modal fade " id="editModal<?php echo $rowTb['catalog_ID'] ?>" tabindex="-1" aria-labelledby="editModal<?php echo $rowTb['catalog_ID'] ?>" aria-hidden="true">
                                <div class="modal-dialog ">
                                    <div class="modal-content ">
                                        <div class="modal-header">
                                            <h3 class="modal-title" id="editModal"><i class="bi bi-pencil-square"></i> แก้ไขประเภท</h3>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- <form action="php_edit_catalog.php" method="POST" id="editcatalog"> -->
                                            <label for="e_catalog_Name" class="form-label">ชื่อประเภท</label>
                                            <input type="text" id="e_catalog_Name<?php echo $rowTb['catalog_ID'] ?>" name="catalog_Name" class="form-control" value="<?php echo $rowTb['catalog_Name'] ?>">
                                            <?php if (!empty($rowTb['edit_by'])) { ?>
                                                <p class="fs-6 mt-2 text-body-tertiary">
                                                    แก้ไขล่าสุดโดย : <span class="fs-6 mt-2" id="edit_by"><?php echo showName($rowTb['edit_by'])['site_f_366'] ?></span>
                                                    <span class="fs-6 mt-2"> เวลา : <span class="fs-6 mt-2" id="edit_date"><?php echo (!empty($rowTb['edit_date']) ? $rowTb['edit_date']->format('d/m/Y H:i') : "") ?></span></span>
                                                </p>
                                            <?php } ?>
                                        </div>
                                        <!-- <div class="col text-end mb-3 me-3">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                            <button onclick="editcatalog('<?php echo $rowTb['catalog_ID'] ?>')" class="btn btn-primary">บันทึก</button>
                                        </div> -->
                                        <!-- </form> -->
                                        <div class="col text-end mb-3 me-3">
                                            <button type="button" class="btn btn-secondary rounded-pill btn-lg" data-bs-dismiss="modal">ปิด</button>
                                            <button onclick="editcatalog('<?php echo $rowTb['catalog_ID'] ?>')" type="submit" class="btn btn-warning rounded-pill btn-lg">บันทึก</button>
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
<?php include('footer.php'); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#addcatalog").submit(function(e) {
            e.preventDefault();

            let catalogURL = $(this).attr("action");
            let reqMethod = $(this).attr("method");
            let formData = $(this).serialize();

            $.ajax({
                url: catalogURL,
                type: reqMethod,
                data: formData,

                success: function(data) {
                    let result = JSON.parse(data);
                    if (result.status == "success") {
                        console.log("Success", result)
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
        })
    })

    function editcatalog(catalog_ID) {
        var catalog_Name = $("#e_catalog_Name" + catalog_ID).val();

        $.ajax({
            url: "php_edit_catalog.php",
            type: "POST",
            datatype: "json",
            data: {
                catalog_ID: catalog_ID,
                catalog_Name: catalog_Name
            },
            success: function(data) {
                let result = JSON.parse(data);
                if (result.status == "success") {
                    console.log("Success", result)
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
                    Swal.fire({
                        title: "อ้ะ!",
                        text: result.msg,
                        icon: result.status,
                    });
                }
            }

        })
    }

    function delcatalog(catalogId) {
        Swal.fire({
            title: "คุณแน่ใจหรือไม่?",
            text: "คุณต้องการลบแคตตาล็อกนี้หรือไม่?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "ใช่",
            cancelButtonText: "ไม่ใช่"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "php_del_catalog.php?catalog_ID=" + catalogId;
            }
        });
    }

    function activelog(catalogId, status) {
        if(status == 0) {
            var text = 'เปิดใช้งาน';
        } else {
            var text = 'ปิดใช้งาน';
        }

        Swal.fire({
            title: "คุณแน่ใจหรือไม่?",
            text: "คุณต้องการ" + text + "แคตตาล็อกนี้หรือไม่?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "ใช่",
            cancelButtonText: "ไม่ใช่"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "php_active_catalog.php?catalog_ID=" + catalogId + "&status=" + status;
            }
        });
    }
</script>