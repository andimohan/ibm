<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/CarServiceMaintenance.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Employee.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Car.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Item.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Supplier.class.php';

$OBJ = new CarServiceMaintenance();
$car = new Car();
$employee = new Employee();

$MODULE_NAME = 'carServiceMaintenance';
$TITLE = $OBJ->lang['carMaintenance'];
$AJAX_FILE = 'ajax-api-car-service-maintenance';

$DATA_STRUCTURE_DETAIL = array();
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'pkey'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'item_id'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'description'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'qty'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'item_unit_id'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'price'));


$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => 'date'));
array_push($DATA_STRUCTURE, array('field' => 'warehouse_id', 'convert' => array('obj' => new Warehouse())));
array_push($DATA_STRUCTURE, array('field' => 'category_id'));
array_push($DATA_STRUCTURE, array('field' => 'technician_id', 'convert' => array('obj' => new Employee())));
array_push($DATA_STRUCTURE, array('field' => 'is_outsource'));
array_push($DATA_STRUCTURE, array('field' => 'supplier_id', 'convert' => array('obj' => new Supplier())));
array_push($DATA_STRUCTURE, array('field' => 'type_id'));
array_push($DATA_STRUCTURE, array('field' => 'invoice_reference'));
array_push($DATA_STRUCTURE, array('field' => 'car_id', 'convert' => array('obj' => new Car(),'columnfrom' =>'policenumber',  'columnto'=> 'policenumber' )));
array_push($DATA_STRUCTURE, array('field' => 'mile_age'));
array_push($DATA_STRUCTURE, array('field' => 'driver_id', 'convert' => array('obj' => new Employee())));
array_push($DATA_STRUCTURE, array('field' => 'note'));


array_push($DATA_STRUCTURE, array('field' => 'detail', 'detail' => $DATA_STRUCTURE_DETAIL));


// ===================== COMPILING DATA
$arrDisplayData = array();

$code = '[auto code]';
$currCode = '';
$indexCtr = 0;
$currentMaintenance = null;
$isOutsource = 0;

for ($row = 2; $row <= $highestRow; ++$row) {


    $isEmpty = true;
    for ($col = 1; $col <= 15; $col++) {
        $value = trim($worksheet->getCellByColumnAndRow($col, $row)->getValue() ?? '');
        if (!empty($value)) {
            $isEmpty = false;
            break;
        }
    }

    // Jika baris kosong = pemisah 
    if ($isEmpty) {
        $currentMaintenance = null; // Reset untuk mulai  baru
        $isOutsource = 0; // Reset outsource
        continue;
    }

    $trdate = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
    $trdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($trdate);
    $trdate = $trdate->getTimestamp();

    $warehouseId = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    $categoryId = strtolower($worksheet->getCellByColumnAndRow(3, $row)->getValue() ?? '');
    $technicianId = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
    $supplierId = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
    $invoiceReference = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
    $typeId = strtolower($worksheet->getCellByColumnAndRow(7, $row)->getValue() ?? '');
    $carId = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
    $carId = strtoupper($car->normalizePoliceNumber($carId) ?? '');
    $mileAge = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
    $driverId = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
    $note = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
    $itemId = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
    $description = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
    $qty = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
    $itemUnit = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
    $price = $worksheet->getCellByColumnAndRow(16, $row)->getValue();

    
    $arrTemp = array();
    if($currentMaintenance === null) {
        
        $isOutsource = !empty($supplierId) ? 1 : 0; //kalau ada supplier, otomatis outsource

        $arrTemp['code'] = $code;
        $arrTemp['date'] = $trdate;
        $arrTemp['warehouse_id'] = $warehouseId;
        $arrTemp['category_id'] = $categoryId;
        $arrTemp['technician_id'] = $technicianId;
        $arrTemp['is_outsource'] = $isOutsource;
        $arrTemp['supplier_id'] = $supplierId;
        $arrTemp['invoice_reference'] = $invoiceReference;
        $arrTemp['note'] = $note;

        $arrTemp['type_id'] = $typeId;
        $arrTemp['car_id'] = $carId;
        $arrTemp['driver_id'] = $driverId;
        $arrTemp['mile_age'] = $mileAge;

        $arrTemp['detail'] = array();

        array_push($arrDisplayData, $arrTemp);

        $currentMaintenance = true;
        $indexCtr = count($arrDisplayData) - 1;
        $MAX_ROWS_LIMIT--;
    }

    if (isset($arrDisplayData[$indexCtr])) {
        if ($isOutsource == 0) {
            $price = 0; //harga dari item
        }

        array_push($arrDisplayData[$indexCtr]['detail'], [
            'item_id' => $itemId,
            'description' => $description,
            'qty' => $qty,
            'item_unit_id' => $itemUnit,
            'price' => $price
        ]);
    }

}

// nanti cek lg harusnya pake apa
if(isset($arrTemp['detail']))
    array_push($arrTemp['detail'], $arrDisplayData[$indexCtr]['detail']);

validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);

// ===================== CONVERT DATA STRUCTURE 
$arrData = importData($DATA_STRUCTURE, array('datatype' => 'datastructure', 'dataset' => $arrDisplayData));
$arrData = removeUnusedParameter($arrData);

// $OBJ->setLog(json_encode($arrData), true);

?>

<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>fontawesome6.min.css">
    <script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>api.min.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            startImportData($(".item-list"), <?php echo json_encode($arrData); ?>, "<?php echo $AJAX_FILE; ?>");
        });
    </script>

</head>

<body>
    <div style="margin: 2em">
        <h2><?php echo $TITLE; ?></h2>


        <?php 

            $headerRow = '  <tr class="div-table-row header-row">';
            $headerRow .= '  <td style="width: 150px">' . $OBJ->lang['code'] . '</td> ';
            $headerRow .= '  <td style="text-align:center; width:130px">' . $OBJ->lang['date'] . '</td> ';
            $headerRow .= '  <td style="width: 160px">' . $OBJ->lang['warehouse'] . '</td> ';
            $headerRow .= '  <td style="width: 180px">' . $OBJ->lang['category'] . '</td> ';
            $headerRow .= '  <td style="width: 150px">' . $OBJ->lang['technician'] . '</td> ';
            $headerRow .= '  <td style="width: 200px">' . $OBJ->lang['supplier'] . '</td> ';
            $headerRow .= '  <td style="width: 200px">' . $OBJ->lang['invoiceReference'] . '</td> ';
            $headerRow .= '  <td style="width: 160px">' . $OBJ->lang['type'] . '</td> ';
            $headerRow .= '  <td style="width: 180px">' . $OBJ->lang['car'] . '</td> ';
            $headerRow .= '  <td style="width: 180px">' . $OBJ->lang['mileage'] . '</td> ';
            $headerRow .= '  <td style="width: 200px">' . $OBJ->lang['driver'] . '</td> ';
            $headerRow .= '  <td style="width: 250px">' . $OBJ->lang['note'] . '</td> ';
            $headerRow .= '  <td style="width:5em; text-align:center">' . $OBJ->lang['status'] . '</td> 
                            <td style="min-width:30em">' . $OBJ->lang['description'] . '</td>  
                            </td> 
                        ';

                $maintenanceDetailRow = '<tr class="header-row"  style="background-color:#666 !important">';
                $maintenanceDetailRow .= '<td style="width: 200px">' . $OBJ->lang['item'] . '</td> ';
                $maintenanceDetailRow .= '<td style="width: 250px">' . $OBJ->lang['description'] . '</td> ';           
                $maintenanceDetailRow .= '<td style="width: 40px">' . $OBJ->lang['qty'] . '</td> ';
                $maintenanceDetailRow .= '<td style="width: 120px">' . $OBJ->lang['itemUnit'] . '</td> ';
                $maintenanceDetailRow .= '<td style="width: 120px">' . $OBJ->lang['price'] . '</td> ';
                $maintenanceDetailRow .= '</tr>';

        // FAILED RESULT 
        echo '<div class="import-table-title text-red-cardinal">' . $OBJ->errorMsg[212] . '</div>';
        echo '<table class="import-table import-result-failed" style="margin-bottom:2em; width:1000px !important;"> ';
        echo $headerRow;
        echo '</table>';

        // SUCCESS RESULT   
        echo '<div class="import-table-title">' . $OBJ->lang['dataHasBeenSuccessfullyUpdated'] . '</div>';
        echo '<table class="import-table import-result-success" style="margin-bottom:2em; width:1000px !important;"> ';
        echo $headerRow;
        echo '</table>';

        // IMPORT LIST
        echo '<div class="import-table-title">' . $OBJ->lang['jobQueue'] . ' ...</div>';
        echo '<table class="import-table" style="width:1000px !important;"> ';
        echo $headerRow;


        $totalCol = 13;


        foreach ($arrDisplayData as $key => $headerRow) {

            echo '<tr class="item-list border-top" relkey="' . $key . '" relgroup="' . $key . '">';

            echo '<td>' . $headerRow['code'] . '</td>';
            echo '<td style="text-align:center">' . date('d/m/Y', $headerRow['date']) . '</td>';
            echo '<td>' . $headerRow['warehouse_id'] . '</td>';
            echo '<td>' . $headerRow['category_id'] . '</td>';
            echo '<td>' . $headerRow['technician_id'] . '</td>';
            echo '<td>' . $headerRow['supplier_id'] . '</td>';
            echo '<td>' . $headerRow['invoice_reference'] . '</td>';
            echo '<td>' . $headerRow['type_id'] . '</td>';
            echo '<td>' . $headerRow['car_id'] . '</td>';
            echo '<td>' .$OBJ->formatNumber($headerRow['mile_age']) . '</td>';
            echo '<td>' . $headerRow['driver_id'] . '</td>';
            echo '<td>' . $headerRow['note'] . '</td>';
            echo '<td style="text-align:center"><div class="response-code"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></td>';
            echo '<td><div class="desc"><div style="text-align:center"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></div></td>';
            echo '</tr>';


            $detailMaintenance = '<table class="import-table" style="width:100%">';
            $detailMaintenance .= $maintenanceDetailRow;
    
            $rsMaintenanceDetail = $headerRow['detail'];

            foreach ($rsMaintenanceDetail as $detailRow) {
                $detailMaintenance .= '<tr>';

                    $detailMaintenance .= '<td>' . $detailRow['item_id'] . '</td>';
                    $detailMaintenance .= '<td>' . $detailRow['description'] . '</td>';
                    $detailMaintenance .= '<td style="text-align:right">' . $OBJ->formatNumber($detailRow['qty']) . '</td>';
                    $detailMaintenance .= '<td>' . $detailRow['item_unit_id'] . '</td>';
                    $detailMaintenance .= '<td style="text-align:right">' . $OBJ->formatNumber($detailRow['price']) . '</td>';

                $detailMaintenance .= '</tr>';

                
            }

            $detailMaintenance .= '</table>';

            echo '<tr  relgroup="' . $key . '"><td></td><td colspan="' . ($totalCol - 1) . '">' . $detailMaintenance . '</td></tr>';

        }

        echo '</table>';

        ?>


        </div>    
</body>    
</html>