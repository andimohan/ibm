<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';    
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLPurchaseOrder.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLJobOrder.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';      

$OBJ = new EMKLPurchaseOrder(EMKL['jobType']['export']);  
$emklJobOrderExport = new EMKLJobOrder(EMKL['jobType']['export']);  

$MODULE_NAME = 'EMKLPurchaseOrderExport';
$TITLE = $OBJ->lang['purchaseOrderExport'];
$AJAX_FILE = 'ajax-api-ff-purchase-order-export';

validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);  

/*$supplierTableKey = $OBJ->getTableKeyAndObj( $supplier->tableName ,array('key'))['key'];
$warehouseTableKey = $OBJ->getTableKeyAndObj( $warehouse->tableName ,array('key'))['key'];*/

$DATA_STRUCTURE_DETAIL = array(); 
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'pkey'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'container_name'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'service_name')); 
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'currency'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'qty'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'price')); 
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'description'));

// 'ref' => array('obj' => new Warehouse(), 'field' => 'code' )), 

$DATA_STRUCTURE = array(); 
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'sales_order_id'));
array_push($DATA_STRUCTURE, array('field' => 'warehouse_id', 'convert' => array('obj' => new Warehouse() )));
array_push($DATA_STRUCTURE, array('field' => 'invoice_reference'));
array_push($DATA_STRUCTURE, array('field' => 'supplier_id', 'convert' => array('obj' => new Supplier() ))); // ini harus diconvert dari nama di excel ke kode supplier, gk bisa maksain dr nama karena ad kemungkinan kedepan, supplier namanya sama
array_push($DATA_STRUCTURE, array('field' => 'date'));
array_push($DATA_STRUCTURE, array('field' => 'currency'));
array_push($DATA_STRUCTURE, array('field' => 'rate'));
array_push($DATA_STRUCTURE, array('field' => 'items_detail', 'detail' => $DATA_STRUCTURE_DETAIL )); 


// ===================== COMPILING DATA
$arrDisplayData = array();
$arrCost = array();
$currCode = '';

for ($row = 3; $row <= $highestRow; ++$row) { 
    
    $aju = trim($worksheet->getCellByColumnAndRow(4, $row)->getValue()); 
    
    $socode = trim($worksheet->getCellByColumnAndRow(6, $row)->getValue()); 
    if(empty($socode) && !empty($aju)){
        //$rs = $emklJobOrderExport->searchData($emklJobOrderExport->tableName.'.aju', $aju ,true, ' and '.$emklJobOrderExport->tableName.'.statuskey in (2,3)');
         $rs = $emklJobOrderExport->searchDataRow( array( $emklJobOrderExport->tableName.'.code') , 
                                                '   and '.$emklJobOrderExport->tableName.'.aju = '.$emklJobOrderExport->oDbCon->paramString($aju).'
                                                    and '.$emklJobOrderExport->tableName.'.statuskey in (2,3)' 
                                                ); 
        
        //$OBJ->setLog(' and '.$emklJobOrderExport->tableName.'.aju = '. $emklJobOrderExport->oDbCon->paramString($aju) ,true);
        //$OBJ->setLog($rs,true);
        if (!empty($rs))
            $socode = $rs[0]['code'];
    }
    
    $refInvoice = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
    
    // kalo gk ad $socode, cek dr AJU
    
    $qty = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
    $container = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
    $costName = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
    $itemDescription = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
    $costPriceUSD = $worksheet->getCellByColumnAndRow(20, $row)->getCalculatedValue();
    $costPriceIDR = $worksheet->getCellByColumnAndRow(21, $row)->getCalculatedValue();
    
    $ref =  $aju.'|'.$socode.'|'.$refInvoice;
    
    if ($currCode <> $ref && !empty($aju.$socode)){  
        
        $arrTemp = array(); 
        $arrTemp['code'] = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue()); 
        $arrTemp['aju'] = $aju;
        $arrTemp['sales_order_id'] = $socode;  
        $arrTemp['warehouse_id'] = trim($worksheet->getCellByColumnAndRow(2, $row)->getValue()); 
        $arrTemp['supplier_id'] = trim($worksheet->getCellByColumnAndRow(3, $row)->getValue()); 
        $arrTemp['invoice_reference'] = $refInvoice;
        $dob = $worksheet->getCellByColumnAndRow(5, $row)->getValue();  
        $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dob);
        $arrTemp['date'] = $dob->getTimestamp(); 
     
        // cost 
        $arrTemp['items_detail'] = array();
            
        array_push($arrDisplayData, $arrTemp); 
        $MAX_ROWS_LIMIT--;
        
        $currCode = $ref; 
    }
    
    if(strtolower($costName) != 'total' && !empty($costName) ){ 
        $arrDataLastIndex = count($arrDisplayData)-1;
        
        $amount = $costPriceIDR;
        $currency = 'IDR'; 
        $rate = 1;
        
        if(!empty($costPriceUSD)){
            $amount = $costPriceUSD;
            $currency = 'USD'; 
            $rate = $costPriceIDR / $costPriceUSD;
        }
          
        // default value  
        if(empty($qty)) $qty = 1;
        
        if (!isset($arrDisplayData[$arrDataLastIndex]['currency'])){  
            $arrDisplayData[$arrDataLastIndex]['currency'] = 'IDR'; 
            $arrDisplayData[$arrDataLastIndex]['rate'] = 1;
        } 
        
        if(strtolower($currency) <> strtolower('IDR')){  
            $arrDisplayData[$arrDataLastIndex]['currency'] = $currency; 
            $arrDisplayData[$arrDataLastIndex]['rate'] = $rate;
        }
        
        array_push($arrDisplayData[$arrDataLastIndex]['items_detail'], array('qty' => $qty, 'container_name' => $container, 'service_name' => $costName,'description' => $itemDescription, 'price' => $amount / $qty , 'currency' => $currency, 'rate' => $rate ));
    }
      
}

checkMaxRowsLimit($MAX_ROWS_LIMIT);

// ===================== CONVERT DATA STRUCTURE 
$arrData = importData($DATA_STRUCTURE,array('datatype' => 'datastructure', 'dataset' => $arrDisplayData )); 

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
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['supplier'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">AJU</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['date'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['JOCode'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['invoiceReference'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="text-align:center">'.$OBJ->lang['qty'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="text-align:center">'.$OBJ->lang['container'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['cost'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['description'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="text-align:right">'.$OBJ->lang['amount'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="text-align:center">'.$OBJ->lang['currency'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="text-align:right">'.$OBJ->lang['rate'].'</div> '; 
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
        $arrDetail = $itemRow['items_detail'];
        
        $startRow = true;
        $border = (!$firstRow) ? 'border-top' : '' ;
            
        foreach($arrDetail as $detailRow){  
            
            if($startRow){
                echo '<div class="div-table-row item-list '.$border.'" relkey="'.$key.'" relgroup="'.$key.'">'; 
                echo '<div class="div-table-col-3">'.$itemRow['code'].'</div>';
                echo '<div class="div-table-col-3">'.$itemRow['warehouse_id'].'</div>';
                echo '<div class="div-table-col-3">'.$itemRow['supplier_id'].'</div>';
                echo '<div class="div-table-col-3">'.$itemRow['aju'].'</div>';
                echo '<div class="div-table-col-3">'. date("d / m / Y",$itemRow['date']).'</div>';
                echo '<div class="div-table-col-3">'.$itemRow['sales_order_id'].'</div>'; 
                echo '<div class="div-table-col-3">'.$itemRow['invoice_reference'].'</div>'; 
            }else{ 
                echo '<div class="div-table-row"  relgroup="'.$key.'">'; 
                echo '<div class="div-table-col-3"></div>'; 
                echo '<div class="div-table-col-3"></div>';
                echo '<div class="div-table-col-3"></div>';
                echo '<div class="div-table-col-3"></div>';
                echo '<div class="div-table-col-3"></div>'; 
                echo '<div class="div-table-col-3"></div>'; 
                echo '<div class="div-table-col-3"></div>'; 
            }
                
            echo '<div class="div-table-col-3" style="text-align:center">'.$detailRow['qty'].'</div>';
            echo '<div class="div-table-col-3" style="text-align:center">'.$detailRow['container_name'].'</div>';
            echo '<div class="div-table-col-3">'.$detailRow['service_name'].'</div>';
            echo '<div class="div-table-col-3">'.$detailRow['description'].'</div>';
            echo '<div class="div-table-col-3" style="text-align:right">'.$OBJ->formatNumber($detailRow['price'],-2).'</div>';
            echo '<div class="div-table-col-3" style="text-align:center">'.$detailRow['currency'].'</div>';
            echo '<div class="div-table-col-3" style="text-align:right">'.$OBJ->formatNumber($detailRow['rate'],-2).'</div>';
            
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
