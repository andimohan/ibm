<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';    
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrderSubscription.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/JobDetails.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';      

$OBJ = new SalesOrderSubscription();  
//$emklJobOrderExport = new EMKLJobOrder(EMKL['jobType']['export']);  
//$class->setLog("tes aja",true);
$MODULE_NAME = 'salesOrderSubscription';
$TITLE = $OBJ->lang['salesOrder'];
$AJAX_FILE = 'ajax-api-sales-order-subscription';

validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);  

/*$supplierTableKey = $OBJ->getTableKeyAndObj( $supplier->tableName ,array('key'))['key'];
$warehouseTableKey = $OBJ->getTableKeyAndObj( $warehouse->tableName ,array('key'))['key'];*/

$DATA_STRUCTURE_DETAIL = array(); 
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'pkey'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'service_name'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'qty'));
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'unit_name')); 
array_push($DATA_STRUCTURE_DETAIL, array('field' => 'selling_price')); 
//array_push($DATA_STRUCTURE_DETAIL, array('field' => 'description'));

// 'ref' => array('obj' => new Warehouse(), 'field' => 'code' )), 

$DATA_STRUCTURE = array(); 
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'date'));
array_push($DATA_STRUCTURE, array('field' => 'warehouse_id', 'convert' => array('obj' => new Warehouse() )));
array_push($DATA_STRUCTURE, array('field' => 'customer_id', 'convert' => array('obj' => new Customer() ))); 
array_push($DATA_STRUCTURE, array('field' => 'invoice_periode'));
array_push($DATA_STRUCTURE, array('field' => 'employee_id', 'convert' => array('obj' => new Employee() ))); 
array_push($DATA_STRUCTURE, array('field' => 'jobdetails_id', 'convert' => array('obj' => new JobDetails() ))); 

array_push($DATA_STRUCTURE, array('field' => 'product'));
array_push($DATA_STRUCTURE, array('field' => 'description'));

array_push($DATA_STRUCTURE, array('field' => 'service_detail', 'detail' => $DATA_STRUCTURE_DETAIL )); 


// ===================== COMPILING DATA
$arrDisplayData = array();
$arrCost = array();
$currCode = '';

for ($row = 3; $row <= $highestRow; ++$row) { 
    
    $socode = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());  
    $customerName = trim($worksheet->getCellByColumnAndRow(3, $row)->getValue()); 
    $ref =  $socode;
        
    if ($currCode <> $ref  && !empty($socode)){ 
        $arrTemp = array(); 
        $arrTemp['code'] = $socode; 
        $arrTemp['warehouse_id'] = trim($worksheet->getCellByColumnAndRow(2, $row)->getValue()); 
        $arrTemp['customer_id'] = $customerName; 
        $recurring = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
        if(strtoupper($recurring)=='Y')
            $arrTemp['invoice_periode'] = INVOICE_PERIODE['monthly'];
        else 
            $arrTemp['invoice_periode'] = INVOICE_PERIODE['manual'];

        $dob = $worksheet->getCellByColumnAndRow(5, $row)->getValue();  
        $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dob);
        $arrTemp['date'] = $dob->getTimestamp(); 

        $arrTemp['employee_id'] = trim($worksheet->getCellByColumnAndRow(6, $row)->getValue()); 
        $arrTemp['product'] = trim($worksheet->getCellByColumnAndRow(7, $row)->getValue()); 
        $arrTemp['jobdetails_id'] = trim($worksheet->getCellByColumnAndRow(8, $row)->getValue()); 
        $arrTemp['description'] = trim($worksheet->getCellByColumnAndRow(9, $row)->getValue());

        $arrTemp['service_detail'] = array();

        array_push($arrDisplayData, $arrTemp); 
        $MAX_ROWS_LIMIT--;
        $currCode = $ref; 
    }

        $serviceName = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
        $qty = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
        //$unit = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
        $unit = 'PCS';
        $price = $worksheet->getCellByColumnAndRow(12, $row)->getValue();

        if(!empty($serviceName) ){ 
            $arrDataLastIndex = count($arrDisplayData)-1;

            // default value  
            if(empty($qty)) $qty = 1;
            array_push($arrDisplayData[$arrDataLastIndex]['service_detail'], array('qty' => $qty,'service_name' => $serviceName,'unit_name' => $unit, 'selling_price' => $price ));
        }
    
}

checkMaxRowsLimit($MAX_ROWS_LIMIT);

// ===================== CONVERT DATA STRUCTURE 
$arrData = importData($DATA_STRUCTURE,array('datatype' => 'datastructure', 'dataset' => $arrDisplayData ));
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
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['customer'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['invoiceRecurring'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['date'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['PIC'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['products'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['jobDetails'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['note'].'</div> ';
    
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['service'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="text-align:right">'.$OBJ->lang['qty'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="text-align:right">'.$OBJ->lang['price'].'</div> ';
//    $headerRow .='</div> ';
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
        $arrDetail = $itemRow['service_detail'];
        
        $startRow = true;
        $border = (!$firstRow) ? 'border-top' : '' ;
            
        foreach($arrDetail as $detailRow){  
            
            if($startRow){
                echo '<div class="div-table-row item-list '.$border.'" relkey="'.$key.'" relgroup="'.$key.'">'; 
                echo '<div class="div-table-col-3">'.$itemRow['code'].'</div>';
                echo '<div class="div-table-col-3">'.$itemRow['warehouse_id'].'</div>';
                echo '<div class="div-table-col-3">'.$itemRow['customer_id'].'</div>';
                echo '<div class="div-table-col-3">'.$itemRow['invoice_periode'].'</div>';
                echo '<div class="div-table-col-3">'. date("d / m / Y",$itemRow['date']).'</div>';
                echo '<div class="div-table-col-3">'.$itemRow['employee_id'].'</div>'; 
                echo '<div class="div-table-col-3">'.$itemRow['product'].'</div>'; 
                echo '<div class="div-table-col-3">'.$itemRow['jobdetails_id'].'</div>'; 
                echo '<div class="div-table-col-3">'.$itemRow['description'].'</div>'; 
            }else{ 
                echo '<div class="div-table-row"  relgroup="'.$key.'">'; 
                echo '<div class="div-table-col-3"></div>'; 
                echo '<div class="div-table-col-3"></div>';
                echo '<div class="div-table-col-3"></div>';
                echo '<div class="div-table-col-3"></div>';
                echo '<div class="div-table-col-3"></div>'; 
                echo '<div class="div-table-col-3"></div>'; 
                echo '<div class="div-table-col-3"></div>'; 
                echo '<div class="div-table-col-3"></div>'; 
                echo '<div class="div-table-col-3"></div>'; 
            }
            echo '<div class="div-table-col-3">'.$detailRow['service_name'].'</div>'; 
            echo '<div class="div-table-col-3" style="text-align:right">'.$detailRow['qty'].'</div>';
            echo '<div class="div-table-col-3" style="text-align:right">'.$detailRow['selling_price'].'</div>';
            
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
