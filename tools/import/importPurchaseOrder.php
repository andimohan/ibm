<?php

require_once '../../_config.php';
require_once '_include.php';
require_once 'function-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/PurchaseOrder.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Supplier.class.php';
/*require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';*/
//require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TermOfPayment.class.php';

$OBJ = new PurchaseOrder();
//$termOfPayment = new TermOfPayment();


$MODULE_NAME = 'purchaseOrder';
$TITLE = $OBJ->lang['purchaseOrder'];
$AJAX_FILE = 'ajax-api-purchase-order';

$ITEM_DETAIL = array();
array_push($ITEM_DETAIL, array('field' => 'pkey'));
array_push($ITEM_DETAIL, array('field' => 'item_id'));
array_push($ITEM_DETAIL, array('field' => 'qty'));
array_push($ITEM_DETAIL, array('field' => 'price_in_unit'));
array_push($ITEM_DETAIL, array('field' => 'unit_id'));


$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'date'));
array_push($DATA_STRUCTURE, array('field' => 'reference'));
array_push($DATA_STRUCTURE, array('field' => 'warehouse_id', 'convert' => array('obj' => new Warehouse())));
array_push($DATA_STRUCTURE, array('field' => 'supplier_id', 'convert' => array('obj' => new Supplier())));
array_push($DATA_STRUCTURE, array('field' => 'term_of_payment_id'));
array_push($DATA_STRUCTURE, array('field' => 'full_receive'));

array_push($DATA_STRUCTURE, array('field' => 'detail', 'detail' => $ITEM_DETAIL));


// ===================== COMPILING DATA
$arrDisplayData = array();

$code = '[auto code]';
$currCode = '';
$indexCtr = 0;

for ($row = 2; $row <= $highestRow; ++$row) {

    $trdate = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
    $trdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($trdate);
    $trdate = $trdate->getTimestamp();

    $warehouseId = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    $reference = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
    $supplierName = $worksheet->getCellByColumnAndRow(4, $row)->getValue();

    $itemCode = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
    $qty = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
    $unitName = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
    $priceInUnit = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
    //    $discountValue = $worksheet->getCellByColumnAndRow(9, $row)->getValue();   
    //    $serviceFee = $worksheet->getCellByColumnAndRow(10, $row)->getValue();   
    //    $freeShippingFee = $worksheet->getCellByColumnAndRow(11, $row)->getValue();   
    //    $diffShippingFee = $worksheet->getCellByColumnAndRow(12, $row)->getValue();   
    //    $affiliateFee = $worksheet->getCellByColumnAndRow(13, $row)->getValue();   
    $top = $worksheet->getCellByColumnAndRow(9, $row)->getValue();

    //    $ref =  $trdate.'|'.$warehouseId;

    // sementara utk BOS
    $ref = $reference;

    if ($currCode <> $ref) {
 
        $arrTemp = array();
        $arrTemp['code'] = $code;
        $arrTemp['date'] = $trdate;
        $arrTemp['reference'] = $reference;
        $arrTemp['warehouse_id'] = $warehouseId;
        $arrTemp['supplier_id'] = $supplierName;
        $arrTemp['term_of_payment_id'] = $top; //top mandatory. 
		$arrTemp['full_receive'] = 1; // sementara

        // item details
        $arrTemp['detail'] = array();

        array_push($arrDisplayData, $arrTemp);
        $indexCtr = count($arrDisplayData) - 1;

        $MAX_ROWS_LIMIT--;

        $currCode = $ref;

    }
    
    if(!isset($arrDisplayData[$indexCtr]['detail'])) $arrDisplayData[$indexCtr]['detail'] = array();
    array_push($arrDisplayData[$indexCtr]['detail'], array(
        'item_id' => $itemCode,
        'qty' => $qty,
        'unit_id' => $unitName,
        'price_in_unit' => $priceInUnit,
        //'discount' => $discountValue,
    )
    );


}

if(!isset($arrTemp['detail'])) $arrTemp['detail'] = array();
array_push($arrTemp['detail'], $arrDisplayData[$indexCtr]['detail']);

// matikan dul uutk BOS
//checkMaxRowsLimit($MAX_ROWS_LIMIT);


validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);

// ===================== CONVERT DATA STRUCTURE
$arrData = importData($DATA_STRUCTURE, array('datatype' => 'datastructure', 'dataset' => $arrDisplayData));

$arrData = removeUnusedParameter($arrData);
//$OBJ->setLog($arrData,true);

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
            startImportData($(".item-list"), <?php echo json_encode($arrData); ?>,"<?php echo $AJAX_FILE; ?>");
        });
    </script>

</head>

<body>
    <div style="padding: 1em; ">
        <h2><?php echo $TITLE; ?></h2>

        <?php

        $headerRow = '  <tr class="header-row">';
        $headerRow .= '  <td style="width: 120px">' . $OBJ->lang['code'] . '</td> ';
        $headerRow .= '  <td style="width: 120px">' . $OBJ->lang['reference'] . '</td> ';
        $headerRow .= '  <td style="text-align:center; width: 80px">' . $OBJ->lang['date'] . '</td> ';
        $headerRow .= '  <td style="width: 120px">' . $OBJ->lang['warehouse'] . '</td> ';
        $headerRow .= '  <td style="width: 120px">' . $OBJ->lang['supplier'] . '</td> ';
        $headerRow .= '  <td style="width:60px; text-align:center">' . $OBJ->lang['status'] . '</td> 
                     <td>' . $OBJ->lang['description'] . '</td>  
                     </td> 
    ';

        $itemRow = '<tr class="header-row"  style="background-color:#666 !important">';
        $itemRow .= '<td>' . $OBJ->lang['item'] . '</td>';
        $itemRow .= '<td style="width: 60px;  text-align:right">' . $OBJ->lang['qty'] . '</td>';
        $itemRow .= '<td style="width: 80px;">' . $OBJ->lang['unit'] . '</td>';
        $itemRow .= '<td style="width: 80px; text-align:right">' . $OBJ->lang['price'] . '</td>';
        //$itemRow .= '<td style="width: 80px; text-align:right">' . $OBJ->lang['discount'] . '</td>';
        $itemRow .= '</tr>';


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

        $totalCol = 5;

        foreach ($arrDisplayData as $key => $headerRow) {

            echo '<tr class="item-list border-top" relkey="' . $key . '" relgroup="' . $key . '">';
            echo '<td>' . $headerRow['code'] . '</td>';
            echo '<td>' . $headerRow['reference'] . '</td>';
            echo '<td style="text-align:center; width: 100px">' . date("d / m / Y", $headerRow['date']) . '</td>';
            echo '<td>' . $headerRow['warehouse_id'] . '</td>';
            echo '<td>' . $headerRow['supplier_id'] . '</td>';
            echo '<td style="text-align:center"><div class="response-code"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></td>';
            echo '<td><div class="desc"><div style="text-align:center"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></div></td>';
            echo '</tr>';

            // detail
            $detailItem = '<table class="import-table" style="width:100%">';
            $detailItem .= $itemRow;

            $rsPurchaseDetail = $headerRow['detail'];

            foreach ($rsPurchaseDetail as $detailRow) {

                $detailItem .= '<tr>';
                $detailItem .= '<td>' . $detailRow['item_id'] . '</td>';
                $detailItem .= '<td style="text-align:right">' . $OBJ->formatNumber($detailRow['qty'], -2) . '</td>';
                $detailItem .= '<td >' . $detailRow['unit_id'] . '</td>';
                $detailItem .= '<td style="text-align:right">' . $detailRow['price_in_unit'] . '</td>';
                //$detailItem .= '<td style="text-align:right">' . $detailRow['discount'] . '</td>';
                $detailItem .= '</tr>';

            }

            $detailItem .= '</table>';
            echo '<tr relgroup="' . $key . '"><td></td><td colspan="' . ($totalCol - 1) . '">' . $detailItem . '</td></tr>';

        }

        echo '</table>';
        ?>
    </div>
</body>

</html>