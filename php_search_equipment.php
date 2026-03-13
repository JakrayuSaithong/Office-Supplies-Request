<?php
session_start();
include_once('condb.php');

$term = isset($_GET['term']) ? $_GET['term'] : '';
$exclude = isset($_GET['exclude']) ? $_GET['exclude'] : '';

if (strlen($term) < 1) {
    echo json_encode([]);
    exit;
}

$excludeClause = '';
if ($exclude !== '') {
    $excludeList = array_filter(array_map('intval', explode(',', $exclude)));
    if (!empty($excludeList)) {
        $excludeClause = " AND equipment_ID NOT IN (" . implode(',', $excludeList) . ")";
    }
}

$sql = "SELECT
            equipment_ID,
            equipment_Code,
            equipment_Name,
            unit
        FROM tbl_equipments
        WHERE status = '1'
          AND (equipment_Code LIKE '%$term%' OR equipment_Name LIKE '%$term%')
          $excludeClause
        ORDER BY equipment_Code ASC";

$result = sqlsrv_query($conn, $sql);
$data = [];

while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $data[] = [
        'id' => $row['equipment_ID'],
        'text' => $row['equipment_Code'] . ' - ' . $row['equipment_Name'],
        'code' => $row['equipment_Code'],
        'name' => $row['equipment_Name'],
        'unit' => $row['unit']
    ];
}

echo json_encode(['results' => $data]);
?>
