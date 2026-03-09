<?php include('header.php');
include_once('condb.php');
// if (empty($_SESSION['employee_ID'])) {
//     header('location:index.php');
//     // exit();
// }
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
    <div class="d-flex  justify-content-center">
        <div class="row col-md-12 p-3">
            <div class="col-md-4">
                <div class="p-3 ps-4 card border border-start border-4 border-top-0 border-bottom-0 border-end-0 border-warning">
                    <h3 class="text-warning">
                        <i class="fa-solid fa-basket-shopping" style="font-size: 25px;"></i>
                        รายการที่รอสั่งซื้อ
                    </h3>
                    <h1 class="card-text"><?php echo $countA ?> <span class="text-secondary">รายการ</span></h1>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 ps-4 card border border-start border-4 border-top-0 border-bottom-0 border-end-0 border-info">
                    <h3 class="text-info">
                        <i class="fa-solid fa-truck-fast" style="font-size: 25px;"></i>
                        รายการที่รอของส่ง
                    </h3>
                    <h1 class="card-text"><?php echo $countP ?> <span class="text-secondary">รายการ</span></h1>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 ps-4 card border border-start border-4 border-top-0 border-bottom-0 border-end-0 border-primary">
                    <h3 class="text-primary">
                    <!-- <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-calendar2-week" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z" />
                        <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5zM11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-5 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z" />
                    </svg> -->
                    <i class="fa-solid fa-cart-flatbed" style="font-size: 25px;"></i>
                        รายการที่รอแจกของเบิก
                    </h3>
                    <h1 class="card-text"><?php echo $countC ?> <span class="text-secondary">รายการ</span></h1>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 ps-4 card border border-start border-4 border-top-0 border-bottom-0 border-end-0 border-success">
                    <h3 class="text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-calendar2-check" viewBox="0 0 16 16">
                            <path d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z" />
                            <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z" />
                        </svg>
                        รายการแจกของเบิกแล้ว
                    </h3>
                    <h1 class="card-text"><?php echo $countS ?> <span class="text-secondary">รายการ</span></h1>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column me-4 ms-4">
        <div class="nav nav-tabs border-0" id="nav-tab" role="tablist">
            <button class="nav-link bg-white text-warning active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true"> 
                <i class="fa-solid fa-basket-shopping" style="font-size: 20px;"></i>
            </button>
            <button class="nav-link bg-white text-info" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                <i class="fa-solid fa-truck-fast" style="font-size: 20px;"></i>
            </button>
            <button class="nav-link bg-white text-primary" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">
                <i class="fa-solid fa-cart-flatbed" style="font-size: 20px;"></i>
            </button>
            <button class="nav-link bg-white text-success" id="nav-success-tab" data-bs-toggle="tab" data-bs-target="#nav-success" type="button" role="tab" aria-controls="nav-success" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-calendar2-check" viewBox="0 0 16 16">
                    <path d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z" />
                    <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z" />
                </svg>
            </button>
        </div>
        <div class="card border-0 p-2 rounded-bottom rounded-0">
            <div class="tab-content" id="nav-tabContent">


                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                    <div class="table-responsive-xxl p-3">
                        <!-- <form action="#" method="POST"> -->
                        <table id="TableOder_P" class="table table-hover ">
                            <thead class="table">
                                <tr class="text-nowrap">
                                    <th class="text-center" >เลขที่</th>
                                    <th class="text-center" style="width: 10rem;">สถานะ</th>
                                    <th class="text-center" style="width: 25rem;">แผนก</th>
                                    <th class="text-center" style="width: 25rem;">ชื่อผู้เบิก</th>
                                    <th class="text-center" style="width: 15rem;">วันที่เบิก</th>
                                    <th class="text-center" style="width: 15rem;">วันที่อนุมัติ</th>
                                    <th class="text-center" style="width: 25rem;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="requestedItems">
                                <?php
                                $sql_P = "SELECT order_ID, order_Number, order_Name, approved_date, order_date, approval, receiving, employee_ID, manager_status
                                FROM tbl_orders 
                                WHERE status = '1' AND approval = 'A' AND manager_status = '0' ORDER BY order_date DESC";
                                $result_P = sqlsrv_query($conn, $sql_P);
                                while ($row_P = sqlsrv_fetch_array($result_P, SQLSRV_FETCH_ASSOC)) {
                                ?>
                                    <tr class="text-center text-nowrap">
                                        <td><?php echo $row_P['order_Number']; ?></td>
                                        <td><?php echo receiving($row_P['receiving'], $row_P['manager_status']); ?></td>
                                        <td class="text-start"><?php echo ShowNameDivision($row_P['employee_ID'])['site_f_1144']. " - " .ShowNameDivision($row_P['employee_ID'])['site_f_1145']; ?></td>
                                        <td class="text-start"><?php echo $row_P['order_Name']; ?></td>
                                        <td><?php echo $row_P['order_date']->format('d/m/Y H:m'); ?></td>
                                        <td><?php echo $row_P['approved_date']->format('d/m/Y H:m'); ?></td>
                                        <td class="text-center">
                                            <a href="requisition_detail.php?oid=<?php echo $row_P['order_ID'] ?>&page=approve_page_admin&onum=<?php echo $row_P['order_Number']; ?>" class="btn btn-info rounded-pill">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <button class="btn btn-success rounded-pill" onclick="Approv(<?php echo $row_P['order_ID'] ?>,'S')"> <i class="bi bi-cart-check-fill"></i> สั่งซื้อ</button>
                                            <button onclick="delOrder(<?php echo $row_P['order_Number']; ?>)" class="btn rounded-pill btn-danger ">
                                                <i class="bi bi-trash3"></i> ลบ
                                            </button>

                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                    <div class="table-responsive-xxl p-3">
                        <!-- <form action="#" method="POST"> -->
                        <table id="TableOder_A" class="table table-hover col-md-12">
                            <thead class="table">
                                <tr class="text-nowrap">
                                    <th class="text-center" width="10%">เลขที่</th>
                                    <th class="text-center" style="width: 10rem;">สถานะ</th>
                                    <th class="text-center" style="width: 25rem;">แผนก</th>
                                    <th class="text-center" style="width: 25rem;">ชื่อผู้เบิก</th>
                                    <th class="text-center" style="width: 15rem;">วันที่เบิก</th>
                                    <th class="text-center" style="width: 25rem;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="requestedItems">
                                <?php
                                $sql_A = "SELECT order_ID, order_Number, order_Name, order_date, approval, receiving , employee_ID, manager_status
                                FROM tbl_orders 
                                WHERE status = '1' AND approval = 'A' AND manager_status = '1' ORDER BY order_date DESC";
                                $result_A = sqlsrv_query($conn, $sql_A);
                                while ($row_A = sqlsrv_fetch_array($result_A, SQLSRV_FETCH_ASSOC)) {
                                ?>
                                    <tr class="text-center text-nowrap">
                                        <td><?php echo $row_A['order_Number']; ?></td>
                                        <td class="text-start"><?php echo receiving($row_A['receiving'], $row_A['manager_status']) ?></td>
                                        <td class="text-start"><?php echo ShowNameDivision($row_A['employee_ID'])['site_f_1144']. " - " .ShowNameDivision($row_A['employee_ID'])['site_f_1145']; ?></td>
                                        <td class="text-start"><?php echo $row_A['order_Name']; ?></td>
                                        <td><?php echo $row_A['order_date']->format('d/m/Y H:m'); ?></td>
                                        <td class="text-center">
                                            <a href="requisition_detail.php?oid=<?php echo $row_A['order_ID'] ?>&page=approve_page_admin&onum=<?php echo $row_A['order_Number']; ?>" class="btn rounded-pill btn-info">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>

                                            <button class="btn btn-success rounded-pill" onclick="Approv(<?php echo $row_A['order_ID'] ?>,'SS')"> <i class="bi bi-cart-check-fill"></i> ของมาส่งแล้ว</button>

                                            <!-- <?php //if ($row['approval'] == "A" && empty($row['receiving']) && $_SESSION['level'] == "0") { ?>
                                                <button class="btn btn-primary rounded-pill" onclick="Approv('<?php echo $row_A['order_ID'] ?>','Y',<?php echo $_SESSION['employee_ID']; ?>)"><i class="bi bi-cart-check-fill"></i> รับของแล้ว</button>
                                                <button class="btn btn-danger rounded-pill" onclick="Approv('<?php echo $row_A['order_ID'] ?>','C')"> <i class="bi bi-x-circle-fill"></i> ยกเลิก</button>
                                            <?php //} ?> -->

                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab" tabindex="0">
                    <div class="table-responsive-xxl p-3 ">
                        <!-- <form action="#" method="POST"> -->
                        <table id="TableOder_C" class="table table-hover">
                            <thead class="table">
                                <tr class="text-nowrap">
                                    <th class="text-center" width="10%">เลขที่</th>
                                    <th class="text-center" style="width: 10rem;">สถานะ</th>
                                    <th class="text-center" style="width: 25rem;">แผนก</th>
                                    <th class="text-center" style="width: 25rem;">ชื่อผู้เบิก</th>
                                    <th class="text-center" style="width: 15rem;">วันที่เบิก</th>
                                    <th class="text-center" style="width: 25rem;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="requestedItems">
                                <?php
                                $sql_C = "SELECT order_ID, order_Number, order_Name, order_date, approval , receiving , employee_ID, manager_status
                                FROM tbl_orders 
                                WHERE status = '1' AND approval = 'A' AND receiving = '' AND manager_status = '2' ORDER BY order_date DESC";
                                $result_C = sqlsrv_query($conn, $sql_C);
                                while ($row_C = sqlsrv_fetch_array($result_C, SQLSRV_FETCH_ASSOC)) {
                                ?>
                                    <tr class="text-center text-nowrap">
                                        <td><?php echo $row_C['order_Number']; ?></td>
                                        <td><?php echo receiving($row_C['receiving'], $row_C['manager_status']); ?></td>
                                        <td class="text-start"><?php echo ShowNameDivision($row_C['employee_ID'])['site_f_1144']. " - " .ShowNameDivision($row_C['employee_ID'])['site_f_1145']; ?></td>
                                        <td class="text-start"><?php echo $row_C['order_Name']; ?></td>
                                        <td><?php echo $row_C['order_date']->format('d/m/Y H:m'); ?></td>
                                        <td>
                                            <a href="requisition_detail.php?oid=<?php echo $row_C['order_ID'] ?>&page=approve_page_admin&onum=<?php echo $row_C['order_Number']; ?>" class="btn rounded-pill btn-info">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <button class="btn btn-primary rounded-pill" onclick="Approv(<?php echo $row_C['order_ID'] ?>,'Y')" ><i class="bi bi-cart-check-fill"></i> รับของแล้ว</button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="tab-pane fade" id="nav-success" role="tabpanel" aria-labelledby="nav-success-tab" tabindex="0">
                    <div class="table-responsive-xxl p-3 ">
                        <!-- <form action="#" method="POST"> -->
                        <table id="TableOder_SS" class="table table-hover">
                            <thead class="table">
                                <tr class="text-nowrap">
                                    <th class="text-center" width="10%">เลขที่</th>
                                    <th class="text-center" style="width: 10rem;">สถานะ</th>
                                    <th class="text-center" style="width: 25rem;">แผนก</th>
                                    <th class="text-center" style="width: 25rem;">ชื่อผู้เบิก</th>
                                    <th class="text-center" style="width: 15rem;">วันที่เบิก</th>
                                    <th class="text-center" style="width: 25rem;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="requestedItems">
                                <?php
                                $sql_SS = "SELECT order_ID, order_Number, order_Name, order_date, approval , receiving , employee_ID, manager_status
                                FROM tbl_orders 
                                WHERE status = '1' AND approval = 'A' AND receiving = 'Y' AND manager_status = '3' ORDER BY order_date DESC";
                                $result_S = sqlsrv_query($conn, $sql_SS);
                                while ($row_S = sqlsrv_fetch_array($result_S, SQLSRV_FETCH_ASSOC)) {
                                ?>
                                    <tr class="text-center text-nowrap">
                                        <td><?php echo $row_S['order_Number']; ?></td>
                                        <td><?php echo receiving($row_S['receiving'], $row_S['manager_status']); ?></td>
                                        <td class="text-start"><?php echo ShowNameDivision($row_S['employee_ID'])['site_f_1144']. " - " .ShowNameDivision($row_S['employee_ID'])['site_f_1145']; ?></td>
                                        <td class="text-start"><?php echo $row_S['order_Name']; ?></td>
                                        <td><?php echo $row_S['order_date']->format('d/m/Y H:m'); ?></td>
                                        <td>
                                            <a href="requisition_detail.php?oid=<?php echo $row_S['order_ID'] ?>&page=approve_page_admin&onum=<?php echo $row_S['order_Number']; ?>" class="btn rounded-pill btn-info">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
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
                    data: {
                        OrderId: OrderId
                    },
                    success: function(data) {
                        // console.log("ได้")
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

    function Approv(order_ID, status, emp_ID) {
        console.log('Order ID:', order_ID);
        console.log('Status:', status);
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
                            onBeforeOpen: () => {
                                Swal.showLoading()
                            },
                        });
                    },
                    success: function(data) {
                        // console.log(data);
                        Swal.close();
                        if (data.status === 'success') {
                            // console.log(data.chk)
                            // console.log("Item added to cart: " + "อนุมัติแล้ว")
                            if (data.stt_app == 'A') {
                                Swal.fire({
                                    position: "center",
                                    title: "อนุมัติแล้ว",
                                    imageUrl: "image/icons8-approved.gif",
                                    imageWidth: 70,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then((result) => {
                                    window.location.reload();
                                });
                            } 
                            else if (data.stt_app == 'C') {
                                Swal.fire({
                                    position: "center",
                                    title: "ไม่อนุมัติรายการนี้",
                                    imageUrl: "image/icons8-unapproved.gif",
                                    imageWidth: 70,
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then((result) => {
                                    window.location.reload();
                                });
                            } 
                            else if (data.stt_app == 'S') {
                                Swal.fire({
                                    position: "center",
                                    title: "ยืนยันการสั่งซื้อแล้ว",
                                    imageUrl: "image/icons8-approved.gif",
                                    imageWidth: 70,
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then((result) => {
                                    window.location.reload();
                                });
                            } 
                            else if (data.stt_app == 'SS') {
                                Swal.fire({
                                    position: "center",
                                    title: "ยืนยันของเบิกมาส่งเรียบร้อยแล้ว",
                                    imageUrl: "image/icons8-approved.gif",
                                    imageWidth: 70,
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then((result) => {
                                    window.location.reload();
                                });
                            }
                            else if (data.stt_app == 'Y') {
                                Swal.fire({
                                    position: "center",
                                    title: "ยืนยันการรับของแล้ว",
                                    imageUrl: "image/icons8-approved.gif",
                                    imageWidth: 70,
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then((result) => {
                                    window.location.reload();
                                });
                            }
                        } else {
                            console.error("Error adding item to cart: " + data.message),
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


    $('#TableOder_A').dataTable({
        "columnDefs": [
            {
                "targets": [4],
                "type": "date"
            }
        ],
        // "order": [
        //     [3, 'desc']
        // ],
        stateSave: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json',
        },
    });
    $('#TableOder_C').dataTable({
        "order": [
            [4, 'desc']
        ],
        stateSave: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json',
        },
    });
    $('#TableOder_P').dataTable({
        "order": [
            [4, 'desc']
        ],
        stateSave: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json',
        },
    });

    $('#TableOder_SS').dataTable({
        "order": [
            [4, 'desc']
        ],
        stateSave: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json',
        },
    });

    document.addEventListener('DOMContentLoaded', function() {
        var selectedTab = localStorage.getItem('selectedTab');

        if (selectedTab) {
            $('.nav-link[data-bs-target="#' + selectedTab + '"]').tab('show');
        }

        $('.nav-link').on('shown.bs.tab', function(e) {
            var selectedTab = e.target.getAttribute('data-bs-target').substring(1); // ดึงชื่อ tab ที่ถูกเลือก
            localStorage.setItem('selectedTab', selectedTab); // บันทึก tab ที่ถูกเลือกไว้ใน Local Storage
        });
    });
</script>