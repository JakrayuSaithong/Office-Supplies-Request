<?php
session_start();
// session_destroy();
include('header.php');
// if (empty($_SESSION['employee_ID'])) {
//     header('location:index.php');
//     // exit();
// }  
include_once('condb.php');
$page = "requisition";

?>
<?php include('sidebar.php'); ?>
<div class="main">
    <?php include('navbar.php'); ?>
    <h1 class="text-dark mt-4 ms-3"><span class="fw-bold fs-1 "> | </span> รายการเบิกของ</h1>
    <div class="p-3">
        <div class="card" style="height:fit-content">
            <div class="table-responsive p-3">
                <!-- <form action="#" method="POST"> -->
                <table id="TableOder" class="table table-hover col-md-12 text-nowrap">
                    <thead class="table">
                        <tr>
                            <th width="20%">เลขที่</th>
                            <th width="30%">ชื่อผู้เบิก</th>
                            <th width="30%">แผนก</th>
                            <th width="25%">วันที่เบิก</th>
                            <th class="text-center" width="25%">สถานะ</th>
                            <th class="text-center">ดู</th>
                        </tr>
                    </thead>
                    <tbody id="requestedItems">
                        <?php
                        $emp_ID = $_SESSION['employee_ID'];
                        $sql = "SELECT order_ID, order_Number,  order_Name,  order_date,  approval,  receiving, employee_ID, manager_status
                        FROM tbl_orders 
                        WHERE employee_ID = '$emp_ID' OR approved_by = '$emp_ID'";
                        $result = sqlsrv_query($conn, $sql);
                        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        ?>
                            <tr >
                                <td class="text-start"><?php echo $row['order_Number']; ?></td>
                                <td><?php echo $row['order_Name']; ?></td>
                                <td><?php echo ShowNameDivision($row['employee_ID'])['site_f_1144']. " - " .ShowNameDivision($row['employee_ID'])['site_f_1145']; ?></td>
                                <td><?php echo $row['order_date']->format('d/m/Y H:i'); ?></td>
                                <td class="text-center">
                                    <?php
                                        echo status($row['approval']);
                                    if($row['approval'] == "A"){
                                        echo receiving($row['receiving'], $row['manager_status']);
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group rounded-5">
                                        <a href="requisition_detail.php?oid=<?php echo $row['order_ID'] ?>&page=requisition&onum=<?php echo $row['order_Number']; ?>" class="btn rounded-pill btn-info">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <!-- <div class="btn btn-sm btn-danger" onclick="">
                                            <i class="bi bi-trash2"></i>
                                        </div> -->
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>
<script>
    $('#TableOder').dataTable({
        "order": [
            [3, 'DESC']
        ],language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json',
            },
    });
</script>