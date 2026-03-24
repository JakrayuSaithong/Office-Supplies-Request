<?php
session_start();
include_once('condb.php');

$tab = isset($_POST['tab']) ? $_POST['tab'] : '';
$filterMonth = isset($_POST['filterMonth']) ? $_POST['filterMonth'] : '';
$filterYear = isset($_POST['filterYear']) ? $_POST['filterYear'] : '';

// Map tab to manager_status and specific conditions
$whereExtra = "";
switch ($tab) {
    case '0':
        $whereExtra = "AND manager_status = '0'";
        break;
    case '1':
        $whereExtra = "AND manager_status = '1'";
        break;
    case '2':
        $whereExtra = "AND receiving = '' AND manager_status = '2'";
        break;
    case '3':
        $whereExtra = "AND receiving = 'Y' AND manager_status = '3'";
        // If filter provided, use it; otherwise default to last 2 months
        if ($filterMonth !== '' && $filterYear !== '') {
            $fMonth = intval($filterMonth);
            $fYear = intval($filterYear);
            $startDate = sprintf("%04d-%02d-01", $fYear, $fMonth);
            // Last day of the month
            $endDate = date("Y-m-t", strtotime($startDate));
            $whereExtra .= " AND receiving_date >= '$startDate' AND receiving_date <= '$endDate 23:59:59'";
        } else {
            $twoMonthsAgo = date("Y-m-d", strtotime("-2 months"));
            $whereExtra .= " AND receiving_date >= '$twoMonthsAgo'";
        }
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid tab']);
        exit;
}

$sql = "SELECT order_ID, order_Number, order_Name, approved_date, order_date, approval, receiving, employee_ID, manager_status, ISNULL(note, 0) as note
        FROM tbl_orders
        WHERE status = '1' AND approval = 'A' $whereExtra
        ORDER BY order_date DESC";

$result = sqlsrv_query($conn, $sql);
$html = '';

while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $divisionData = ShowNameDivision($row['employee_ID']);
    $divisionText = ($divisionData['site_f_1144'] ?? '') . " - " . ($divisionData['site_f_1145'] ?? '');
    if ($row['note'] == 1) {
        $divisionText .= ' <span class="badge bg-info text-white ms-1">เบิกเพิ่มเติม</span>';
    }
    
    // Use output buffering to capture the echo-based receiving() from condb.php
    ob_start();
    receiving($row['receiving'], $row['manager_status']);
    $statusBadge = ob_get_clean();
    
    // Format dates safely
    $orderDate = ($row['order_date'] instanceof DateTime) ? $row['order_date']->format('d/m/Y H:i') : '';
    $approvedDate = ($row['approved_date'] instanceof DateTime) ? $row['approved_date']->format('d/m/Y H:i') : '';

    $html .= '<tr class="text-center text-nowrap">';
    $html .= '<td>' . htmlspecialchars($row['order_Number']) . '</td>';
    $html .= '<td>' . $statusBadge . '</td>';
    $html .= '<td class="text-start">' . $divisionText . '</td>';
    $html .= '<td class="text-start">' . htmlspecialchars($row['order_Name']) . '</td>';
    $html .= '<td>' . $orderDate . '</td>';

    // Tab 0 has extra approved_date column
    if ($tab === '0') {
        $html .= '<td>' . $approvedDate . '</td>';
    }

    // Action buttons differ per tab
    $html .= '<td class="text-center">';
    $viewBtn = '<a href="requisition_detail.php?oid=' . $row['order_ID'] . '&page=approve_page_admin&onum=' . $row['order_Number'] . '" class="btn btn-info rounded-pill btn-sm me-1"><i class="fa-solid fa-eye"></i></a>';
    $html .= $viewBtn;

    if ($tab === '0') {
        $html .= ' <button class="btn btn-success rounded-pill btn-sm me-1" onclick="Approv(' . $row['order_ID'] . ',\'S\')"><i class="fa-solid fa-cart-shopping"></i> สั่งซื้อ</button>';
        $html .= ' <button onclick="delOrder(' . $row['order_Number'] . ')" class="btn rounded-pill btn-danger btn-sm"><i class="fa-solid fa-trash-can"></i> ลบ</button>';
    } elseif ($tab === '1') {
        $html .= ' <button class="btn btn-success rounded-pill btn-sm" onclick="Approv(' . $row['order_ID'] . ',\'SS\')"><i class="fa-solid fa-truck-ramp-box"></i> ของมาส่งแล้ว</button>';
    } elseif ($tab === '2') {
        $html .= ' <button class="btn btn-primary rounded-pill btn-sm" onclick="Approv(' . $row['order_ID'] . ',\'Y\')"><i class="fa-solid fa-box-open"></i> รับของแล้ว</button>';
    }

    $html .= '</td>';
    $html .= '</tr>';
}

echo json_encode(['status' => 'success', 'html' => $html, 'tab' => $tab]);
?>
