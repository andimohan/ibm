<?php

// sementara buat utk BOS dulu
// patokan ar payment adalah referensi transaksi wintera atau invoice marketplace
// nanti ditarik berdasarkan refcode / transcode


require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/AR.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Customer.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Currency.class.php';


$OBJ = new AR();
$MODULE_NAME = 'ar';
$TITLE = $OBJ->lang['accountsReceivable'];
$AJAX_FILE = 'ajax-api-ar';


$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'transaction_type'));
array_push($DATA_STRUCTURE, array('field' => 'warehouse_id', 'convert' => array('obj' => new Warehouse())));
array_push($DATA_STRUCTURE, array('field' => 'customer_id', 'convert' => array('obj' => new Customer())));
array_push($DATA_STRUCTURE, array('field' => 'date'));
array_push($DATA_STRUCTURE, array('field' => 'due_date'));
array_push($DATA_STRUCTURE, array('field' => 'currency_id', 'convert' => array('obj' => new Currency())));
array_push($DATA_STRUCTURE, array('field' => 'rate'));
array_push($DATA_STRUCTURE, array('field' => 'amount'));
array_push($DATA_STRUCTURE, array('field' => 'ref_code'));
array_push($DATA_STRUCTURE, array('field' => 'notes'));
array_push($DATA_STRUCTURE, array('field' => 'overwrite_gl'));
array_push($DATA_STRUCTURE, array('field' => 'status'));


// ===================== COMPILING DATA
$arrDisplayData = array();

$code = '[auto code]';
for ($row = 2; $row <= $highestRow; ++$row) {
   $transactionType = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
   $warehouseId = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
   $customerId = $worksheet->getCellByColumnAndRow(3, $row)->getValue();

   $trdate = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
   $trdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($trdate);
   $trdate = $trdate->getTimestamp();

   $dueDate = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
   $dueDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dueDate);
   $dueDate = $dueDate->getTimestamp();

   $currencyId = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
   $rate = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
   $amount = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
   $refCode = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
   $notes = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
   $overwriteGL = $worksheet->getCellByColumnAndRow(11, $row)->getValue();

   $arrTemp = array();
   $arrTemp['code'] = $code; 
   $arrTemp['transaction_type'] = $transactionType;
   $arrTemp['warehouse_id'] = $warehouseId;
   $arrTemp['customer_id'] = $customerId;
   $arrTemp['date'] = $trdate;
   $arrTemp['due_date'] = $dueDate;
   $arrTemp['currency_id'] = $currencyId;
   $arrTemp['rate'] = $rate;
   $arrTemp['amount'] = $amount;
   $arrTemp['ref_code'] = $refCode;
   $arrTemp['notes'] = $notes;
   $arrTemp['overwrite_gl'] = $overwriteGL;
        

   array_push($arrDisplayData, $arrTemp);
}

validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);

// ===================== CONVERT DATA STRUCTURE
$arrData = importData($DATA_STRUCTURE, array('datatype' => 'datastructure', 'dataset' => $arrDisplayData));

$arrData = removeUnusedParameter($arrData);

// require_once '_import.php';
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
      $headerRow .= '  <td style="width: 100px">' . $OBJ->lang['code'] . '</td> ';
      $headerRow .= '  <td style="width: 100px">' . $OBJ->lang['transactionType'] . '</td> ';
      $headerRow .= '  <td style="width: 100px">' . $OBJ->lang['warehouse'] . '</td> ';
      $headerRow .= '  <td style="width: 100px">' . $OBJ->lang['customer'] . '</td> ';
      $headerRow .= '  <td style="text-align:center; width: 100px">' . $OBJ->lang['date'] . '</td> ';
      $headerRow .= '  <td style="text-align:center; width: 100px">' . $OBJ->lang['duedate'] . '</td> ';
      $headerRow .= '  <td style="width: 60px">' . $OBJ->lang['currency'] . '</td> ';
      $headerRow .= '  <td style="text-align:right; width: 100px">' . $OBJ->lang['rate'] . '</td> ';
      $headerRow .= '  <td style="text-align:right; width: 120px">' . $OBJ->lang['amount'] . '</td> ';
      $headerRow .= '  <td style="width: 120px">' . $OBJ->lang['note'] . '</td> ';
      $headerRow .= '  <td style="width:60px; text-align:center">' . $OBJ->lang['status'] . '</td> 
                        <td style="width: 200px">' . $OBJ->lang['description'] . '</td>  
                     </td> 
   ';


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
         echo '<td>' . $headerRow['transaction_type'] . '</td>';
         echo '<td>' . $headerRow['warehouse_id'] . '</td>';
         echo '<td>' . $headerRow['customer_id'] . '</td>';
         echo '<td style="text-align:center; width: 120px">' . date("d / m / Y", $headerRow['date']) . '</td>';
         echo '<td style="text-align:center; width: 120px">' . date("d / m / Y", $headerRow['due_date']) . '</td>';
         echo '<td style="text-align:left;">' . $headerRow['currency_id'] . '</td>';
         echo '<td style="text-align:right;">' . $headerRow['rate'] . '</td>';
         echo '<td style="text-align:right;">' . $headerRow['amount'] . '</td>';
         echo '<td>' . $headerRow['notes'] . '</td>';
         // echo '<td>' . $headerRow['status_id'] . '</td>';
         echo '<td style="text-align:center"><div class="response-code"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></td>';
         echo '<td><div class="desc"><div style="text-align:center"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></div></td>';
         echo '</tr>';


      }

      echo '</table>';
      ?>
   </div>
</body>

</html>