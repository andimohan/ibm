<?php

require_once '../../_config.php';
require_once '_include.php';
require_once 'function-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/GeneralJournal.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/ChartOfAccount.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Currency.class.php';

$OBJ = new GeneralJournal();
$warehouse = new Warehouse();
$chartOfAccount = new ChartOfAccount();
$currency = new Currency();

$MODULE_NAME = 'generalJournal';
$TITLE = $OBJ->lang['generalJournal'];
$AJAX_FILE = 'ajax-api-general-journal';

$JOURNAL_DETAIL_STRUCTURE = array();
array_push($JOURNAL_DETAIL_STRUCTURE , array('field' => 'pkey'));
array_push($JOURNAL_DETAIL_STRUCTURE , array('field' => 'coa_id'));
array_push($JOURNAL_DETAIL_STRUCTURE , array('field' => 'currency_id'));
array_push($JOURNAL_DETAIL_STRUCTURE , array('field' => 'debit_source'));
array_push($JOURNAL_DETAIL_STRUCTURE , array('field' => 'credit_source'));
array_push($JOURNAL_DETAIL_STRUCTURE , array('field' => 'rate'));
array_push($JOURNAL_DETAIL_STRUCTURE , array('field' => 'debit'));
array_push($JOURNAL_DETAIL_STRUCTURE , array('field' => 'credit'));
array_push($JOURNAL_DETAIL_STRUCTURE , array('field' => 'notes'));

$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'date'));
array_push($DATA_STRUCTURE, array('field' => 'warehouse_id', 'convert' => array('obj' => $warehouse)));

array_push($DATA_STRUCTURE, array('field' => 'detail', 'detail' => $JOURNAL_DETAIL_STRUCTURE));

// ===================== COMPILING DATA
// ===================== COMPILING DATA
$arrDisplayData = array();

$code = '[auto code]';
$currCode = '';
$indexCtr = 0;
$currentJournal = null;

$rsCurrency = $currency->getDataRowById(CURRENCY['idr']);
$defaultCurrency = $rsCurrency[0]['name'];
$defaultRate = 1;

for ($row = 2; $row <= $highestRow; ++$row) {


    $isEmpty = true;
    for ($col = 1; $col <= 9; $col++) {
        $value = trim($worksheet->getCellByColumnAndRow($col, $row)->getValue());
        if (!empty($value)) {
            $isEmpty = false;
            break;
        }
    }

    // Jika baris kosong = pemisah jurnal
    if ($isEmpty) {
        $currentJournal = null; // Reset untuk mulai jurnal baru
        continue;
    }

    $trdate = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
    $trdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($trdate);
    $trdate = $trdate->getTimestamp();

    $warehouseId = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    $coaDebitId = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
    $coaCreditId = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
    $currencyId = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
    $rate = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
    $debit = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
    $credit = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
    $notes = $worksheet->getCellByColumnAndRow(9, $row)->getValue();

    if ($currentJournal === null) {

        $arrTemp = array();
        $arrTemp['code'] = $code;
        $arrTemp['date'] = $trdate;
        $arrTemp['warehouse_id'] = $warehouseId;

        $arrTemp['detail'] = array();

        array_push($arrDisplayData, $arrTemp);

        $currentJournal = true;
        $indexCtr = count($arrDisplayData) - 1;
        $MAX_ROWS_LIMIT--;
    }

    $currencyId = (empty($currencyId) ? $defaultCurrency : $currencyId);
    $rate = (float) (empty($rate) ? $defaultRate : $rate);

    $debit = (float) (empty($debit) ? 0 : $debit);
    $credit = (float) (empty($credit) ? 0 : $credit);


    $debitSource = $debit / $rate;
    $creditSource = $credit / $rate;

    if (!empty($coaDebitId) && $debit > 0) {
        array_push($arrDisplayData[$indexCtr]['detail'], [
            'coa_id' => $coaDebitId,
            'currency_id' => $currencyId,
            'rate' => $rate,
            'debit_source' => $debitSource,
            'credit_source' => 0,
            'debit' => $debit,
            'credit' => 0,
            'notes' => $notes,
        ]);
    }

    if (!empty($coaCreditId) && $credit > 0) {
        array_push($arrDisplayData[$indexCtr]['detail'], [
            'coa_id' => $coaCreditId,
            'currency_id' => $currencyId,
            'rate' => $rate,
            'debit_source' => 0,
            'credit_source' => $creditSource,
            'debit' => 0,
            'credit' => $credit,
            'notes' => $notes,
        ]);
    }

    
    
}
array_push($arrTemp['detail'], $arrDisplayData[$indexCtr]['detail']);
// $OBJ->setLog($arrDisplayData, true);
validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);

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
    $headerRow .= '  <td style="width: 120px">' . $OBJ->lang['code'] . '</td> ';
    $headerRow .= '  <td style="text-align:center; width:120px">' . $OBJ->lang['date'] . '</td> ';
    $headerRow .= '  <td style="width: 150px">' . $OBJ->lang['warehouse'] . '</td> ';
    $headerRow .= '  <td style="width:5em; text-align:center">' . $OBJ->lang['status'] . '</td> 
                    <td style="min-width:30em">' . $OBJ->lang['description'] . '</td>  
                    </td> 
                ';

        
    $journalDetailRow = '<tr class="header-row"  style="background-color:#666 !important">';
    $journalDetailRow .= '<td style="width: 150px">' . $OBJ->lang['account'] . '</td>';
    $journalDetailRow .= '<td style="width: 300px">' . $OBJ->lang['note'] . '</td>';
    $journalDetailRow .= '<td style="width: 120px; text-align:right">' . $OBJ->lang['debitSource'] . '</td>';
    $journalDetailRow .= '<td style="width: 120px; text-align:right">' . $OBJ->lang['creditSource'] . '</td>';
    $journalDetailRow .= '<td style="width: 120px; text-align:right">' . $OBJ->lang['currency'] . '</td>';
    $journalDetailRow .= '<td style="width: 120px; text-align:right">' . $OBJ->lang['rate'] . '</td>';
    $journalDetailRow .= '<td style="width: 120px; text-align:right">' . $OBJ->lang['debit'] . '</td>';
    $journalDetailRow .= '<td style="width: 120px; text-align:right">' . $OBJ->lang['credit'] . '</td>';
    $journalDetailRow .= '</tr>';


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

    $totalCol =5;

    foreach ($arrDisplayData as $key => $headerRow) {

        echo '<tr class="item-list border-top" relkey="' . $key . '" relgroup="' . $key . '">';

        echo '<td>' . $headerRow['code'] . '</td>';
        echo '<td style="text-align:center">' . date('d/m/Y', $headerRow['date']) . '</td>';
        echo '<td>' . $headerRow['warehouse_id'] . '</td>';
        echo '<td style="text-align:center"><div class="response-code"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></td>';
        echo '<td><div class="desc"><div style="text-align:center"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></div></td>';
        echo '</tr>';
        
        
        // DETAIL
        //$detailJournal =  '<table class="import-table" style="width:auto !important">';
        $detailJournal = '<table class="import-table" style="width:100%">';
        $detailJournal .= $journalDetailRow;

        $rsJournalDetail = $headerRow['detail']; 

        foreach ($rsJournalDetail as $detailRow) {
            $detailJournal .= '<tr>';
            $detailJournal .= '<td>' . $detailRow['coa_id'] . '</td>';
            $detailJournal .= '<td>' . $detailRow['notes'] . '</td>';
            $detailJournal .= '<td style="text-align:right">' . number_format($detailRow['debit_source'], 2) . '</td>';
            $detailJournal .= '<td style="text-align:right">' . number_format($detailRow['credit_source'], 2) . '</td>';
            $detailJournal .= '<td style="text-align:right">' . $detailRow['currency_id'] . '</td>';
            $detailJournal .= '<td style="text-align:right">' . number_format($detailRow['rate'], 2) . '</td>';
            $detailJournal .= '<td style="text-align:right">' . number_format($detailRow['debit'], 2) . '</td>';
            $detailJournal .= '<td style="text-align:right">' . number_format($detailRow['credit'], 2) . '</td>';
            $detailJournal .= '</tr>';
    }

        $detailJournal .= '</table>';

        echo '<tr  relgroup="'.$key.'"><td></td><td colspan="'.($totalCol-1).'">'.$detailJournal.'</td></tr>';

    }
    echo '</table>';
    ?>

    </div>

</body>

</html>