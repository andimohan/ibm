<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';     

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CashBankIn.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/RevenueCashIn.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ChartOfAccount.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';   

$OBJ = new CashBankIn();  
$MODULE_NAME = 'cashBankIn';
$TITLE = $OBJ->lang['cashBankIn'];
$AJAX_FILE = 'ajax-api-cash-bank-in';
validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);  

$DATA_STRUCTURE_DETAIL = array(); 
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'pkey'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'customer_name'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'revenue_name'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'amount'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'description'));


$DATA_STRUCTURE = array(); 
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'date'));
array_push($DATA_STRUCTURE, array('field' => 'warehouse_id', 'convert' => array('obj' => new Warehouse() )));
array_push($DATA_STRUCTURE, array('field' => 'coa_id', 'convert' => array('obj' => new ChartOfAccount() ))); 
array_push($DATA_STRUCTURE, array('field' => 'note'));

array_push($DATA_STRUCTURE, array('field' => 'cash_detail', 'detail' => $DATA_STRUCTURE_DETAIL )); 

// ===================== COMPILING DATA
$arrDisplayData = array();
$arrCost = array();
$currCode = '';

for ($row = 2; $row <= $highestRow; ++$row) {  
    $coaName = trim($worksheet->getCellByColumnAndRow(4, $row)->getValue()); 
	$warehouseCode = trim($worksheet->getCellByColumnAndRow(2, $row)->getValue());
	$dateTrans = $worksheet->getCellByColumnAndRow(3, $row)->getValue();  
    $dateTrans = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateTrans);
	$trDate = $dateTrans->getTimestamp(); 
    $ref =  $trDate.'|'.$warehouseCode.'|'.$coaName;
        
    if ($currCode <> $ref  && !empty($coaName)  && !empty($trDate)  && !empty($warehouseCode)){ 
        $arrTemp = array(); 
        $arrTemp['code'] = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());  
        $arrTemp['warehouse_id'] = $warehouseCode; 
        $arrTemp['coa_id'] = $coaName; 
        $arrTemp['date'] = $trDate; 

        $arrTemp['note'] = trim($worksheet->getCellByColumnAndRow(5, $row)->getValue());

        $arrTemp['cash_detail'] = array();

        array_push($arrDisplayData, $arrTemp); 
        $MAX_ROWS_LIMIT--;
        $currCode = $ref; 
    }

        $customerName = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
		$desc = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
		$amount = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
        $revenueName = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
        
        
        // customer name boleh kosong selain utk ayat silang
        // validasi nya di class saja
        // !empty($customerName) && 
        //if(!empty($revenueName) && $amount>0 ){ 
            $arrDataLastIndex = count($arrDisplayData)-1; 
            array_push($arrDisplayData[$arrDataLastIndex]['cash_detail'], array('amount' => $amount,'customer_name' => $customerName,'revenue_name' => $revenueName, 'description' => $desc ));
        //}
    
}

checkMaxRowsLimit($MAX_ROWS_LIMIT);

// ===================== CONVERT DATA STRUCTURE 
$arrData = importData($DATA_STRUCTURE,array('datatype' => 'datastructure', 'dataset' => $arrDisplayData));
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
    jQuery(document).ready(function(){
        startImportData($(".item-list"),<?php echo json_encode($arrData); ?>,"<?php echo $AJAX_FILE; ?>");
    });
</script>

</head>
<body>
<div style="margin: 2em">
<h2><?php echo $TITLE; ?></h2> 
    
<?php 
    
    $headerRow = '  <div class="div-table-row header-row">'; 
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['code'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['warehouse'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['date'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['cash/bank'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['note'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['customer'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['note'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="text-align:right">'.$OBJ->lang['amount'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['transactionType'].'</div> ';
	
    $headerRow .= '  <div class="div-table-col-3" style="min-width:5em; text-align:center">'.$OBJ->lang['status'].'</div> 
                <div class="div-table-col-3" style="min-width:30em">'.$OBJ->lang['description'].'</div>  
            </div> 
        ';
 
         
    // FAILED RESULT 
    echo '<div class="div-table import-table import-result-failed" style="margin-bottom:2em">';
    echo '<div class="div-table-caption text-red-cardinal">'.$OBJ->errorMsg[212].'</div>';
    echo $headerRow;
    echo '</div>';
      
    // SUCCESS RESULT 
    echo '<div class="div-table import-table import-result-success" style="margin-bottom:2em">';
    echo '<div class="div-table-caption text-green-avocado">'.$OBJ->lang['dataHasBeenSuccessfullyUpdated'].'</div>';
    echo $headerRow;
    echo '</div>';
     
    // IMPORT LIST
    echo '<div class="div-table import-table"> ';
    echo '<div class="div-table-caption">'.$OBJ->lang['jobQueue'].' ...</div>';
    echo $headerRow;
    
    $firstRow = true;
    foreach($arrDisplayData as $key=>$itemRow){ 
        $arrDetail = $itemRow['cash_detail'];
        
        $startRow = true;
        $border = (!$firstRow) ? 'border-top' : '' ;
            
        foreach($arrDetail as $detailRow){  
            
            if($startRow){
                echo '<div class="div-table-row item-list '.$border.'" relkey="'.$key.'" relgroup="'.$key.'">'; 
                echo '<div class="div-table-col-3">'.$itemRow['code'].'</div>';
                echo '<div class="div-table-col-3">'.$itemRow['warehouse_id'].'</div>';
                echo '<div class="div-table-col-3">'. date("d / m / Y",$itemRow['date']).'</div>';
                echo '<div class="div-table-col-3">'.$itemRow['coa_id'].'</div>'; 
                echo '<div class="div-table-col-3">'.$itemRow['note'].'</div>'; 
            }else{ 
                echo '<div class="div-table-row"  relgroup="'.$key.'">'; 
                echo '<div class="div-table-col-3"></div>'; 
                echo '<div class="div-table-col-3"></div>';
                echo '<div class="div-table-col-3"></div>';
                echo '<div class="div-table-col-3"></div>';
                echo '<div class="div-table-col-3"></div>'; 
            }
			
            echo '<div class="div-table-col-3">'.$detailRow['customer_name'].'</div>'; 
            echo '<div class="div-table-col-3">'.$detailRow['description'].'</div>'; 
            echo '<div class="div-table-col-3" style="text-align:right">'.$detailRow['amount'].'</div>';
            echo '<div class="div-table-col-3" style="text-align:right">'.$detailRow['revenue_name'].'</div>';
            
            if($startRow){
                echo '<div class="div-table-col-3" style="text-align:center"><div class="response-code"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></div>';
                echo '<div class="div-table-col-3"><div class="desc"><div style="text-align:center"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></div></div>';
            }else{
                echo '<div class="div-table-col-3"></div>';
                echo '<div class="div-table-col-3"></div>';
            }
                 
            echo '</div>';
            
            
            $startRow = false;
            $firstRow = false;
            
        }
    }
    
    
    echo '</div>';    
?>
</div>    
</body>    
</html>
