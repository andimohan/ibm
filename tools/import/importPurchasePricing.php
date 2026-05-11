<?php

require_once '../../_config.php';
require_once '_include.php';
require_once 'function-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/PurchasePricing.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Supplier.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Item.class.php';

$obj = new PurchasePricing();
$supplier = new Supplier();
$item = new Item();

$MODULE_NAME = 'purchasePricing';
$TITLE = $obj->lang['purchasePricing'];
$AJAX_FILE = 'ajax-api-purchase-pricing';

$PURCHASE_PRICE_DETAIL_STRUCTURE = array();
array_push($PURCHASE_PRICE_DETAIL_STRUCTURE, array('field' => 'pkey'));
array_push($PURCHASE_PRICE_DETAIL_STRUCTURE, array('field' => 'item_id'));
array_push($PURCHASE_PRICE_DETAIL_STRUCTURE, array('field' => 'price'));

$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'date'));
array_push($DATA_STRUCTURE, array('field' => 'supplier_id', 'convert' => array('obj' => $supplier)));
array_push($DATA_STRUCTURE, array('field' => 'notes'));

array_push($DATA_STRUCTURE, array('field' => 'detail', 'detail' => $PURCHASE_PRICE_DETAIL_STRUCTURE));

// ===================== COMPILING DATA
$arrDisplayData = array();

$code = '[auto code]';
$currCode = '';
$indexCtr = 0;
$currentPurchasePrice = null;

for ($row = 2; $row <= $highestRow; ++$row) {

    $isEmpty = true;
    for ($col = 1; $col <= 5; $col++) {
        $value = trim($worksheet->getCellByColumnAndRow($col, $row)->getValue());
        if (!empty($value)) {
            $isEmpty = false;
            break;
        }
    }

   
    if ($isEmpty) {
        $currentPurchasePrice = null; 
        continue;
    }

    $trdate = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
    $trdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($trdate);
    $trdate = $trdate->getTimestamp();

    $supplierId = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    $itemId = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
    $price = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
    $notes = $worksheet->getCellByColumnAndRow(5, $row)->getValue();


    if ($currentPurchasePrice === null) {

        $arrTemp = array();
        $arrTemp['code'] = $code;
        $arrTemp['date'] = $trdate;
        $arrTemp['supplier_id'] = $supplierId;
        $arrTemp['notes'] = $notes;

        $arrTemp['detail'] = array();

        array_push($arrDisplayData, $arrTemp);

        $currentPurchasePrice = true;
        $indexCtr = count($arrDisplayData) - 1;
        $MAX_ROWS_LIMIT--;
    }


    array_push($arrDisplayData[$indexCtr]['detail'], [
        'item_id' => $itemId,
        'price' => $price
    ]);

}

validateSecurity($obj, $MODULE_NAME, $spreadsheet);

// ===================== CONVERT DATA STRUCTURE
$arrData = importData($DATA_STRUCTURE, array('datatype' => 'datastructure', 'dataset' => $arrDisplayData));
$arrData = removeUnusedParameter($arrData);


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
    <div style="width: 2230px; padding: 1em; ">
        <h2><?php echo $TITLE; ?></h2>

        <?php

        $headerRow = '  <tr class="header-row">';
        $headerRow .= '  <td style="width: 120px">' . $obj->lang['code'] . '</td> ';
        $headerRow .= '  <td style="width: 120px;text-align:center;">' . $obj->lang['date'] . '</td> ';
        $headerRow .= '  <td style="width: 150px">' . $obj->lang['supplier'] . '</td> ';
        $headerRow .= '  <td style="width: 250px">' . $obj->lang['note'] . '</td> ';
        $headerRow .= '  <td style="width:5em; text-align:center">' . $obj->lang['status'] . '</td> 
                    <td style="min-width:30em">' . $obj->lang['description'] . '</td>  
                    </td> 
                ';


        $purchaseDetailRow = '<tr class="header-row"  style="background-color:#666 !important">';
        $purchaseDetailRow .= '<td style="width: 200px">' . $obj->lang['itemName'] . '</td>';
        $purchaseDetailRow .= '<td style="width: 120px; text-align:right">' . $obj->lang['price'] . '</td>';
        $purchaseDetailRow .= '</tr>';


        // FAILED RESULT 
        echo '<div class="import-table-title text-red-cardinal">' . $obj->errorMsg[212] . '</div>';
        echo '<table class="import-table import-result-failed" style="margin-bottom:2em; width:1000px !important;"> ';
        echo $headerRow;
        echo '</table>';

        // SUCCESS RESULT   
        echo '<div class="import-table-title">' . $obj->lang['dataHasBeenSuccessfullyUpdated'] . '</div>';
        echo '<table class="import-table import-result-success" style="margin-bottom:2em; width:1000px !important;"> ';
        echo $headerRow;
        echo '</table>';

        // IMPORT LIST
        echo '<div class="import-table-title">' . $obj->lang['jobQueue'] . ' ...</div>';
        echo '<table class="import-table" style="width:1000px !important;"> ';
        echo $headerRow;

        $totalCol = 5;

        foreach ($arrDisplayData as $key => $headerRow) {

            echo '<tr class="item-list border-top" relkey="' . $key . '" relgroup="' . $key . '">';

            echo '<td>' . $headerRow['code'] . '</td>';
            echo '<td style="text-align:center; width: 100px">' . date("d / m / Y", $headerRow['date']) . '</td>';
            echo '<td>' . $headerRow['supplier_id'] . '</td>';
            echo '<td>' . $headerRow['notes'] . '</td>';
            echo '<td style="text-align:center"><div class="response-code"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></td>';
            echo '<td><div class="desc"><div style="text-align:center"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></div></td>';
            echo '</tr>';


            // DETAIL
            $detailPurchasePrice = '<table class="import-table" style="width:100%">';
            $detailPurchasePrice .= $purchaseDetailRow;

            $rsPurchasePriceDetail = $headerRow['detail'];

            foreach ($rsPurchasePriceDetail as $detailRow) {
                $detailPurchasePrice .= '<tr>';
                $detailPurchasePrice .= '<td>' . $detailRow['item_id'] . '</td>';
                $detailPurchasePrice .= '<td style="text-align:right">' . number_format($detailRow['price'], 2) . '</td>';
            }

            $detailPurchasePrice .= '</table>';

            echo '<tr  relgroup="' . $key . '"><td></td><td colspan="' . ($totalCol - 1) . '">' . $detailPurchasePrice . '</td></tr>';

        }
        echo '</table>';
        ?>

    </div>

</body>

</html>