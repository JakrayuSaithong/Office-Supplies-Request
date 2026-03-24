<?php include('header.php');
include_once('condb.php');
// if (empty($_SESSION['employee_ID'])) {
//     header('location:index.php');
//     // exit();
// }
$employee_ID = $_SESSION['employee_ID'];
$page = "approve_page_noti";

$countP = countOrders_all();


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
                        รายการที่รอหัวหน้าอนุมัติ
                    </h3>
                    <h1 class="card-text"><?php echo $countP ?> <span class="text-secondary">รายการ</span></h1>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column me-4 ms-4">
        <div class="nav nav-tabs border-0" id="nav-tab" role="tablist">
            <button class="nav-link bg-white text-warning active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true"> 
                <i class="fa-solid fa-basket-shopping" style="font-size: 20px;"></i>
            </button>
        </div>
        <div class="card border-0 p-2 rounded-bottom rounded-0">
            <div class="tab-content" id="nav-tabContent">


                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                    <div class="table-responsive-xxl p-3">
                        <!-- <form action="#" method="POST"> -->
                        <table id="TableOder_P" class="table table-hover">
                            <thead class="table">
                                <tr class="text-nowrap">
                                    <th class="text-center" >เลขที่</th>
                                    <th class="text-center" style="width: 10rem;">สถานะ</th>
                                    <th class="text-center" style="width: 25rem;">แผนก</th>
                                    <th class="text-center" style="width: 25rem;">ชื่อผู้เบิก</th>
                                    <th class="text-center" style="width: 15rem;">วันที่เบิก</th>
                                    <th class="text-center" style="width: 15rem;">ผู้อนุมัติ</th>
                                    <th class="text-center" style="width: 25rem;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="requestedItems">
                                <?php
                                $sql_P = "SELECT order_ID, order_Number, order_Name, order_date, approval, approved_by, receiving, employee_ID, manager_status
                                FROM tbl_orders 
                                WHERE status = '1' AND approval = 'P' AND manager_status = '0' ORDER BY order_date DESC";
                                $result_P = sqlsrv_query($conn, $sql_P);
                                while ($row_P = sqlsrv_fetch_array($result_P, SQLSRV_FETCH_ASSOC)) {
                                    // $DataE = encryptIt(json_encode([
                                    //     "auth_user_name" => $row_P['approved_by'],
                                    //     "date_U" => time(),
                                    //     "FromApp" => "Noti"
                                    // ], JSON_UNESCAPED_UNICODE));
                                    // $titelnoti = "แจ้งเตือนขอเบิกของ (". $row_P['order_Number'] .")";
                                    // $message = $row_P['order_Name'] . "\nได้สร้างเอกสารเพื่อขออนุมัติเบิกของ" . "\nเมื่อ " . date_format($row_P['order_date'], "Y-m-d H:m:s");
                                    // $urlnoti = "https://it.asefa.co.th/withdraw/requisition_detail.php?oid=". $row_P['order_ID'] ."&page=approve_page&onum=". $row_P['order_Number'] ."&DataE=". $DataE;
                                ?>
                                    <tr class="text-center text-nowrap">
                                        <td><?php echo $row_P['order_Number']; ?></td>
                                        <td><?php echo "<p class='ms-2 mt-3 badge rounded-pill text-bg-secondary'> <i class='fa-solid fa-basket-shopping'></i> รอหัวหน้าอนุมัติ</p>"; ?></td>
                                        <td class="text-start"><?php echo ShowNameDivision($row_P['employee_ID'])['site_f_1144']. " - " .ShowNameDivision($row_P['employee_ID'])['site_f_1145']; ?></td>
                                        <td class="text-start"><?php echo $row_P['order_Name']; ?></td>
                                        <td><?php echo $row_P['order_date']->format('d/m/Y H:m'); ?></td>
                                        <td class="text-start"><?php echo showname($row_P['approved_by'])['site_f_366']; ?></td>
                                        <td class="text-center">
                                            <a href="requisition_detail.php?oid=<?php echo $row_P['order_ID'] ?>&page=approve_page_admin&onum=<?php echo $row_P['order_Number']; ?>" class="btn btn-info rounded-pill">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <button class="btn btn-success rounded-pill" onclick="NotifyApp('<?php echo $row_P['order_ID'] ?>','<?php echo $row_P['order_Number'] ?>','<?php echo $row_P['order_Name'] ?>', '<?php echo date_format($row_P['order_date'], 'Y-m-d H:m:s') ?>','<?php echo $row_P['approved_by'] ?>')"> <i class="fas fa-bell"></i> ส่งแจ้งเตือน</button>
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

            </div>
        </div>

    </div>
</div>
</div>
<?php include('footer.php'); ?>
<script>
    function NotifyApp(order_ID, order_Number, order_Name, order_date, approved_by){
        Swal.fire({
            title: "ต้องการส่งแจ้งเตือนหรือไม่?",
            text: "ส่งแจ้งเตือนรายการของ "+ order_Name,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "ส่งแจ้งเตือน",
            cancelButtonText: "ยกเลิก"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "php_noti.php",
                    data: {
                        order_ID: order_ID,
                        order_Number: order_Number,
                        order_Name: order_Name,
                        order_date: order_date,
                        approved_by: approved_by
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
                        console.log(data);
                        if(data == true){
                            Swal.fire({
                                position: "center",
                                title: "ส่งแจ้งเตือนเรียบร้อย",
                                imageUrl: "image/icons8-approved.gif",
                                imageWidth: 70,
                                showConfirmButton: false,
                                timer: 1500
                            }).then((result) => {
                                window.location.reload();
                            });
                        }
                        else{
                            Swal.fire({
                                position: "center",
                                icon: "error",
                                title: "ไม่สามารถส่งแจ้งเตือนได้",
                                showConfirmButton: false,
                                timer: 800
                            });
                        }
                        
                    }

                })
            }
        });
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
                    data: {
                        OrderId: OrderId
                    },
                    success: function(data) {
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
        // scrollX: true,
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