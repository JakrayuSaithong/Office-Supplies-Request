<?php
include_once("header.php");

$page = "report1";

if(isset($_GET['report_Type']) && isset($_GET['report_Date'])){
    $report_Type = $_GET['report_Type'];
    $report_division = $_GET['report_division'];
    $report_Date = $_GET['report_Date']." 00:00:00";
    $report_Date_End = $_GET['report_Date_End']." 23:59:59";

    if($report_Type == 'N'){
        $sql = "WHERE tbl_orders.status = '1' AND approval = 'A' AND receiving_by = '' AND manager_status = '0' AND approved_date >= '$report_Date' AND approved_date <= '$report_Date_End'";
    }
    else{
        $sql = "WHERE tbl_orders.status = '1' AND approval = 'A' AND receiving = 'Y' AND manager_status = '3' AND receiving_date >= '$report_Date' AND receiving_date <= '$report_Date_End'";
    }

    $sql_report = "SELECT 
        tbl_orders.order_Number, 
        tbl_orders.order_Name, 
        tbl_orders.approved_date, 
        tbl_orders.employee_ID,
        tbl_orders.receiving_date,
        tbl_orders.approved_date,
        tbl_order_detail.order_detail
        FROM tbl_orders
        INNER JOIN tbl_order_detail
        ON tbl_orders.order_Number = tbl_order_detail.order_Number
    " . $sql;
}
else{
    $report_Date = date("Y-m");

    $sql_report = "SELECT 
    tbl_orders.order_Number, 
    tbl_orders.order_Name, 
    tbl_orders.approved_date, 
    tbl_orders.employee_ID,
    tbl_orders.receiving_date,
    tbl_orders.approved_date,
    tbl_order_detail.order_detail
    FROM tbl_orders
    INNER JOIN tbl_order_detail
    ON tbl_orders.order_Number = tbl_order_detail.order_Number
    WHERE tbl_orders.status = '1' AND approval = 'A' AND receiving = 'Y' AND manager_status = '3' AND receiving_date LIKE '%$report_Date%'";
}

$result_p = sqlsrv_query($conn, $sql_report);
$array_profile = array();
while($row_p = sqlsrv_fetch_array($result_p, SQLSRV_FETCH_ASSOC)){
    $order_number = $row_p['order_Number'];
    $Division = ShowNameDivision($row_p['employee_ID'])['site_f_1144'];
    $DivisionName = ShowNameDivision($row_p['employee_ID'])['site_f_1145'];
    $order_detail = json_decode($row_p['order_detail'], true);

    $array_profile[$Division ."-". $order_number] = $row_p;
    $array_profile[$Division ."-". $order_number]['DivisionName'] = $DivisionName;
}

// echo "<pre>";
// print_r($array_profile);
// echo "</pre>";

?>
<?php include('sidebar.php'); ?>
<div class="main">
    <?php include('navbar.php'); ?>
    <div class="container mt-3">
        <div class="mt-3 mb-2">
            <div class="row row-cols-12">
                <div class="col">
                    <h1><span class="fw-bold text-info fs-1">|</span> รายงาน </h1>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn btn-success rounded-pill" id="load-Excel">
                        <i class="bi bi-file-earmark-excel-fill"></i> Excel
                    </button>

                    <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <i class="bi bi-plus-circle-fill"></i> กำหนดรายงาน
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal add -->
        <div class="modal fade " id="exampleModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content ">
                    <div class="modal-header ">
                        <h3 class="modal-title" id="exampleModalLabel"><i class="bi bi-plus-circle-fill"></i> รายละเอียดรายงาน</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="catalog_Name" class="form-label">ประเภทรายงาน</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="report_Type" id="radio1" value="Y" <?php echo $_GET['report_Type'] == 'Y' || !isset($_GET['report_Type']) ? 'checked' : '' ?> >
                            <label class="form-check-label" for="radio1">
                                รับของแล้ว
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="report_Type" id="radio2" value="N" <?php echo $_GET['report_Type'] == 'N' ? 'checked' : '' ?> >
                            <label class="form-check-label" for="radio2">
                                รอสั่งซื้อ
                            </label>
                        </div>
                        <br>
                        <label for="catalog_Name" class="form-label">รายงานแผนก</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="report_division" id="radio3" value="HR" <?php echo $_GET['report_division'] == 'HR' || !isset($_GET['report_division']) ? 'checked' : '' ?> >
                            <label class="form-check-label" for="radio3">
                                เบิกของ HR
                            </label>
                        </div>
                        <!-- <div class="form-check">
                            <input class="form-check-input" type="radio" name="report_division" id="radio4" value="Marketing" <?php //echo $_GET['report_division'] == 'Marketing' ? 'checked' : '' ?> >
                            <label class="form-check-label" for="radio4">
                                เบิกของ การตลาด
                            </label>
                        </div> -->
                        <br>
                        <label for="report_Date" class="form-label">เลือกวันที่เริ่มต้น</label>
                        <input type="text" id="report_Date" name="report_Date" class="form-control" required value="<?php echo isset($_GET['report_Date']) ? $_GET['report_Date'] : ''; ?>">

                        <label for="report_Date_End" class="form-label">เลือกวันที่สิ้นสุด</label>
                        <input type="text" id="report_Date_End" name="report_Date_End" class="form-control" required value="<?php echo isset($_GET['report_Date_End']) ? $_GET['report_Date_End'] : ''; ?>">
                    </div>
                    <div class="col text-end mb-3 me-3">
                        <button type="button" class="btn btn-secondary rounded-pill btn-lg" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" id="submit-data" class="btn btn-primary rounded-pill btn-lg">บันทึก</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-3 rounded-2 bg-white">
            <div class="table-responsive">
                <table class="table table-hover col-md-12" id="table-report">
                    <thead>
                        <tr class="text-nowrap">
                            <td>วันที่</td>
                            <td>รหัสสินค้า</td>
                            <td>รายการ</td>
                            <td>จำนวน</td>
                            <td>หน่วย</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if($_GET['report_Type'] != 'N'){
                            $array_count = array();
                            if(!empty($array_profile)){
                            foreach($array_profile as $k => $v){
                                $array_report = json_decode($v['order_detail'], true);
                                // $array_report_new = array();
                                $division = substr($k, 0, strlen($k) - 7);

                                // echo "<pre>";
                                // print_r($array_report);
                                // echo "</pre>";

                        ?>
                        <tr class="text-nowrap">
                            <td class="table-warning" style="background-color: #fef1d5;" colspan="5">แผนก : <?php echo substr($k, 0, strlen($k) - 7). " - " . $v['DivisionName'] . " (". $v['order_Name'] .")"?></td>
                        </tr>
                        <?php
                                foreach($array_report as $k1 => $v1){
                                    $array_report[$k1]['equipment_Codename'] = ShowDataEquipmentID($v1['equipment_Code'])['equipment_Code'];
                                    if($v1['Q_true'] == '0'){
                                        continue;
                                    }

                                    if($report_division == 'HR'){
                                        if(substr($array_report[$k1]['equipment_Codename'], 0, 2) != '24'){
                                            continue;
                                        }
                                    }
                                    elseif($report_division == 'Marketing'){
                                        if(substr($array_report[$k1]['equipment_Codename'], 0, 2) != '29'){
                                            continue;
                                        }
                                    }
                                    
                                    // $array_count[$division][$v1['equipment_Code']]["Qty"] += $v1['Q_true'];
                                    // $array_count[$division][$v1['equipment_Code']]["receiving_date"] = $v['receiving_date'];
                                    // $array_count[$division][$v1['equipment_Code']]["equipment_Code"] = $v1['equipment_Code'];

                                    // echo "<pre>";
                                    // print_r($array_count);
                                    // echo "</pre>";
                        ?>
                        <tr>
                            <td style="text-align: left;"><?php echo date_format($v['receiving_date'], "Y-m-d") == '1900-01-01' ? date_format($v['approved_date'], "Y-m-d") : date_format($v['receiving_date'], "Y-m-d") ; ?></td>
                            <td style="text-align: left;"><?php echo ShowDataEquipmentID($v1['equipment_Code'])['equipment_Code']; ?></td>
                            <td style="text-align: left;"><?php echo ShowDataEquipmentID($v1['equipment_Code'])['equipment_Name'] ?></td>
                            <td style="text-align: left;"><?php echo $v1['Q_true'] ?></td>
                            <td style="text-align: left;"><?php echo ShowDataEquipmentID($v1['equipment_Code'])['unit'] ?></td>
                        </tr>
                        <?php
                                }
                            }
                            }
                            else{
                                echo '<tr class="text-nowrap">
                                        <td class="table-warning" style="background-color: #fef1d5;" colspan="5">ไม่มีข้อมูล</td>
                                    </tr>';
                            }
                        }


                        elseif($_GET['report_Type'] == 'N'){
                            $array_count = array();
                            if(!empty($array_profile)){

                                // echo "<pre>";
                                // print_r($array_profile);
                                // echo "<pre>";

                                foreach($array_profile as $k => $v){
                                    $array_report = json_decode($v['order_detail'], true);
                                    foreach($array_report as $k1 => $v1){
                                        $array_count[$v1['equipment_Code']]['equipment_Codename'] = ShowDataEquipmentID($v1['equipment_Code'])['equipment_Code'];
                                    // echo "<pre>";
                                    // print_r($v1);
                                    // echo "</pre>";
                                        if($v1['Q_true'] == '0'){
                                            continue;
                                        }
                                        $array_count[$v1['equipment_Code']]['Qty'] += $v1['Q_true'];
                                        $array_count[$v1['equipment_Code']]['Division'][$k] = $v['DivisionName'];
                                        $array_count[$v1['equipment_Code']]['Date'][$k] = $v['order_Number'] . " - " . date_format($v['approved_date'], "Y-m-d");
                                        $array_count[$v1['equipment_Code']]['Qty_'. $k] += $v1['Q_true'];
                                    }
                                }
                            }
                            else{
                                echo    '<tr class="text-nowrap">
                                            <td class="table-warning" style="background-color: #fef1d5;" colspan="5">ไม่มีข้อมูล</td>
                                        </tr>';
                            }

                            // echo "<pre>";
                            // print_r($array_count);
                            // echo "</pre>";

                            foreach($array_count as $kk => $vv){
                                if($report_division == 'HR'){
                                    if(substr($array_count[$kk]['equipment_Codename'], 0, 2) != '24'){
                                        continue;
                                    }
                                }
                                elseif($report_division == 'Marketing'){
                                    if(substr($array_count[$kk]['equipment_Codename'], 0, 2) != '29'){
                                        continue;
                                    }
                                }
                                // echo "<pre>";
                                // print_r($vv);
                                // echo "</pre>";
                                $divisions = [];
                                $line_break = 4;
                                $division_count = 0;
                                foreach($vv['Division'] as $kkk => $vvv){ 
                                    $division_count++;
                                    $divisions[] = $kkk . " - " . $vvv . $vv['Qty_'. $kkk]; //. $vv['Qty_'. $kkk]
                                    if($division_count % $line_break == 0){
                                        $divisions[] = "<br>";
                                    }
                                }
                        ?>
                        <tr class="text-nowrap">
                            <td class="table-warning" style="background-color: #fef1d5;" colspan="5">แผนก : <?php echo implode(", ", $divisions);?></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;"><?php foreach($vv['Date'] as $kkk2 => $vvv2){echo $vvv2 . "<br>";}?></td>
                            <td style="text-align: left;"><?php echo ShowDataEquipmentID($kk)['equipment_Code']; ?></td>
                            <td style="text-align: left;"><?php echo ShowDataEquipmentID($kk)['equipment_Name'] ?></td>
                            <td style="text-align: left;"><?php echo $vv['Qty'] ?></td>
                            <td style="text-align: left;"><?php echo ShowDataEquipmentID($kk)['unit'] ?></td>
                        </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>
<script src="assets/plugins/global/plugins.bundle.js"></script>
<?php include('footer.php'); ?>

<script type="text/javascript">
    $(document).ready(function(){
        $("#report_Date").flatpickr({
            enableTime: false,
            disableMobile: true,
            altFormat: "d-m-Y",
            dateFormat: "Y-m-d",
            allowInput: true,
            altInput: true
        });

        $("#report_Date_End").flatpickr({
            enableTime: false,
            disableMobile: true,
            altFormat: "d-m-Y",
            dateFormat: "Y-m-d",
            allowInput: true,
            altInput: true
        });
        // $('#table-report').dataTable({
        //     language: {
        //         url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json',
        //     },
        //     stateSave: true,
        //     buttons: [
        //         'excel'
        //     ]
        // })

        // $('#load-Excel').click(function(){
        //     var wb = XLSX.utils.book_new();
        //     var ws = XLSX.utils.table_to_sheet(document.getElementById('table-report'), {
        //         cellStyles: true,
        //         "!rows": [{ "hidden": true, "hpx": 20 }],
        //         "!cols": [{ "wpx": 120 }]
        //     });

        //     XLSX.utils.book_append_sheet(wb, ws, "Report");

        //     var today = new Date();
        //     var filename = "report_" + today.getFullYear() + (today.getMonth() + 1) + today.getDate() + ".xlsx";
        //     XLSX.writeFile(wb, filename);
        // });

        $('#load-Excel').click(function() {
            var workbook = new ExcelJS.Workbook();
            var worksheet = workbook.addWorksheet('Report');
            worksheet.getColumn('A').width = 20;
            worksheet.getColumn('B').width = 15;
            worksheet.getColumn('C').width = 30;

            var borderStyle = {
                top: { style: 'thin' },
                left: { style: 'thin' },
                bottom: { style: 'thin' },
                right: { style: 'thin' }
            };

            var mergeCells = [];

            $('#table-report thead tr').each(function(rowIdx, row) {
                $(row).find('td').each(function(colIdx, col) {
                    var cell = worksheet.getCell(rowIdx + 1, colIdx + 1);
                    cell.value = $(col).text();
                    cell.border = borderStyle;
                    cell.alignment = { vertical: 'top', horizontal: 'left' , wrapText: true };
                });
            });

            $('#table-report tbody tr').each(function(rowIdx, row) {
                $(row).find('td').each(function(colIdx, col) {
                    var cell = worksheet.getCell(rowIdx + 2, colIdx + 1);
                    var cellText = $(col).html().replace(/<br\s*\/?>/g, '\n');
                    cell.value = cellText;
                    cell.border = borderStyle;
                    cell.alignment = { vertical: 'top', horizontal: 'left' , wrapText: true };

                    if ($(col).hasClass('table-warning')) {
                        cell.fill = {
                            type: 'pattern',
                            pattern: 'solid',
                            fgColor: { argb: 'fef1d5' }
                        };
                        cell.alignment = { horizontal: 'left' };
                        worksheet.mergeCells(rowIdx + 2, 1, rowIdx + 2, 5);
                    }
                });
            });

            workbook.xlsx.writeBuffer().then(function(buffer) {
                var blob = new Blob([buffer], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'});
                var fileName = 'รายงานเบิกของ(<?php echo ($report_Type == 'Y' ? "รับของแล้ว" : "รอสั่งซื้อ") ?>).xlsx';

                if (navigator.msSaveBlob) {
                    navigator.msSaveBlob(blob, fileName);
                } else {
                    var link = document.createElement('a');
                    if (link.download !== undefined) {
                        var url = URL.createObjectURL(blob);
                        link.setAttribute('href', url);
                        link.setAttribute('download', fileName);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                }
            });
        });


        $('#submit-data').on('click', function() {
            var report_Type = $('input[name="report_Type"]:checked').val();
            var report_division = $('input[name="report_division"]:checked').val();
            var report_Date = $('#report_Date').val();
            var report_Date_End = $('#report_Date_End').val();

            if (report_Type == undefined || report_Type == "") {
                Swal.fire(
                    'กรุณาเลือกประเภทรายงาน',
                    '',
                    'warning'
                )

                return false;
            }

            if (report_division == undefined || report_division == "") {
                Swal.fire(
                    'กรุณาเลือกรายงานแผนก',
                    '',
                    'warning'
                )

                return false;
            }

            if (report_Date == undefined || report_Date == "" ) {
                Swal.fire(
                    'กรุณาเลือกวันที่เริ่มต้นของรายงาน',
                    '',
                    'warning'
                )

                return false;
            }

            if (report_Date_End == undefined || report_Date_End == "" ) {
                Swal.fire(
                    'กรุณาเลือกวันที่สิ้นสุดของรายงาน',
                    '',
                    'warning'
                )

                return false;
            }

            var url = 'https://it.asefa.co.th/withdraw/report.php' + '?report_Type=' + report_Type + '&report_Date=' + report_Date + '&report_Date_End=' + report_Date_End + '&report_division=' + report_division;
            window.location.href = url;
        });

        // flatpickr("#report_Date", {
        //     dateFormat: "Y-m",
        // });
        // $('#table-report').DataTable({
        //     language: {
        //     url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json',
        //     },
        //     stateSave: true
        //     // "ajax": {
        //     //     "url": "fetch_data.php",
        //     //     "dataSrc": ""
        //     // },
        //     // "columns": [
        //     //     { "data": "id" },
        //     //     { "data": "name" }
        //     //     // เพิ่มคอลัมน์ตามที่ต้องการแสดง
        //     // ]
        // });
    });

</script>