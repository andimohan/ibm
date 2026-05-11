<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';    
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLJobOrder.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';        
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Vessel.class.php';      

$OBJ = new EMKLJobOrder(EMKL['jobType']['export']);   

$MODULE_NAME = 'EMKLJobOrderExport';
$TITLE = $OBJ->lang['salesOrderExport'];
$AJAX_FILE = 'ajax-api-ff-job-order-export';
  

$ITEM_DETAIL_STRUCTURE = array(); 
array_push($ITEM_DETAIL_STRUCTURE, array('field' => 'pkey'));
array_push($ITEM_DETAIL_STRUCTURE, array('field' => 'container_name')); 
array_push($ITEM_DETAIL_STRUCTURE, array('field' => 'qty')); 
array_push($ITEM_DETAIL_STRUCTURE, array('field' => 'service_name')); 
array_push($ITEM_DETAIL_STRUCTURE, array('field' => 'currency')); 
array_push($ITEM_DETAIL_STRUCTURE, array('field' => 'price'));  

$SALES_DETAIL_STRUCTURE = array(); 
array_push($SALES_DETAIL_STRUCTURE, array('field' => 'pkey'));
array_push($SALES_DETAIL_STRUCTURE, array('field' => 'customer_id', 'convert' => array('obj' => new Customer() ))); 
array_push($SALES_DETAIL_STRUCTURE, array('field' => 'hbl')); 
array_push($SALES_DETAIL_STRUCTURE, array('field' => 'currency')); 
array_push($SALES_DETAIL_STRUCTURE, array('field' => 'rate')); 
array_push($SALES_DETAIL_STRUCTURE, array('field' => 'items_detail', 'detail' => $ITEM_DETAIL_STRUCTURE )); 


$DATA_STRUCTURE = array(); 
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'date')); 
array_push($DATA_STRUCTURE, array('field' => 'warehouse_id', 'convert' => array('obj' => new Warehouse() )));
array_push($DATA_STRUCTURE, array('field' => 'carrier_id', 'convert' => array('obj' => new Supplier() ))); 
array_push($DATA_STRUCTURE, array('field' => 'sales_id', 'convert' => array('obj' => new Employee() ))); 
array_push($DATA_STRUCTURE, array('field' => 'pol_name')); 
array_push($DATA_STRUCTURE, array('field' => 'pod_name')); 
array_push($DATA_STRUCTURE, array('field' => 'freight_type')); 
array_push($DATA_STRUCTURE, array('field' => 'load_type')); 
array_push($DATA_STRUCTURE, array('field' => 'mbl')); 
array_push($DATA_STRUCTURE, array('field' => 'booking_number')); 
array_push($DATA_STRUCTURE, array('field' => 'etd')); 
array_push($DATA_STRUCTURE, array('field' => 'eta')); 
array_push($DATA_STRUCTURE, array('field' => 'aju')); 
array_push($DATA_STRUCTURE, array('field' => 'peb')); 
array_push($DATA_STRUCTURE, array('field' => 'po_reference')); 
array_push($DATA_STRUCTURE, array('field' => 'vessel_id', 'convert' => array('obj' => new Vessel() )));  
array_push($DATA_STRUCTURE, array('field' => 'vessel_number'));
array_push($DATA_STRUCTURE, array('field' => 'container_number')); 
array_push($DATA_STRUCTURE, array('field' => 'description')); 
array_push($DATA_STRUCTURE, array('field' => 'sales_detail', 'detail' => $SALES_DETAIL_STRUCTURE )); 


// ===================== COMPILING DATA
$arrDisplayData = array();
$arrCost = array();
$currCode = '';
$currCustomer = '';
 

$indexForCustomersDetail = 0;
$indexForItemsDetail = 0;
 
for ($row = 3; $row <= $highestRow; ++$row) { 
       
    $socode = trim($worksheet->getCellByColumnAndRow(2, $row)->getValue());  
    $customerName = trim($worksheet->getCellByColumnAndRow(7, $row)->getValue());  
          
    $qty = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
    $container = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
    $serviceName = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
    $sellingPriceUSD = $worksheet->getCellByColumnAndRow(22, $row)->getCalculatedValue();
    $sellingPriceIDR = $worksheet->getCellByColumnAndRow(23, $row)->getCalculatedValue();
    
    $ref =  $socode;
    
    if ($currCode <> $ref  && !empty($socode)){  
        
        $arrTemp = array(); 
        $arrTemp['code'] = $socode;  
        $arrTemp['date'] = time(); 
     
        $arrTemp['warehouse_id'] = trim($worksheet->getCellByColumnAndRow(3, $row)->getValue()); 
        $arrTemp['freight_type'] = trim($worksheet->getCellByColumnAndRow(4, $row)->getValue());  
        $arrTemp['load_type'] =  trim($worksheet->getCellByColumnAndRow(5, $row)->getValue());  
        $arrTemp['sales_id'] = trim($worksheet->getCellByColumnAndRow(6, $row)->getValue()); 
        $arrTemp['carrier_id'] =  trim($worksheet->getCellByColumnAndRow(8, $row)->getValue());  
        $arrTemp['pol_name'] = trim($worksheet->getCellByColumnAndRow(9, $row)->getValue()); 
        $arrTemp['pod_name'] = trim($worksheet->getCellByColumnAndRow(10, $row)->getValue()); 
        $arrTemp['mbl'] = trim($worksheet->getCellByColumnAndRow(11, $row)->getValue()); 
        $arrTemp['booking_number'] = trim($worksheet->getCellByColumnAndRow(11, $row)->getValue()); 
        

        $etd = $worksheet->getCellByColumnAndRow(13, $row)->getValue();  
        $etd = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($etd);
        $arrTemp['etd'] = $etd->getTimestamp(); 

        $eta = $worksheet->getCellByColumnAndRow(14, $row)->getValue();  
        $eta = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($eta);
        $arrTemp['eta'] = $eta->getTimestamp();  
        
        $arrTemp['aju'] =  $worksheet->getCellByColumnAndRow(15, $row)->getValue();
        $arrTemp['peb'] =  $worksheet->getCellByColumnAndRow(16, $row)->getValue();
        $arrTemp['po_reference'] =  $worksheet->getCellByColumnAndRow(17, $row)->getValue();
        
        $vessel =  $worksheet->getCellByColumnAndRow(18, $row)->getValue();
        $arrVessel = explode(' ',$vessel);
        $vesselNumber = $arrVessel[count($arrVessel) - 1]; 
        array_pop($arrVessel); 
        
        $arrTemp['vessel_id'] =  implode(' ',$arrVessel);
        $arrTemp['vessel_number'] =  $vesselNumber;
        
        $containerNumber = $worksheet->getCellByColumnAndRow(24, $row)->getValue();
        $sealNumber = $worksheet->getCellByColumnAndRow(25, $row)->getValue();
        $arrTemp['container_number'] = $containerNumber;
        if (!empty($sealNumber)) 
            $arrTemp['container_number'] .= chr(13).chr(13).'SEAL'.chr(13).$sealNumber;
          
        $arrTemp['description']  = $worksheet->getCellByColumnAndRow(26, $row)->getValue();
        
        // customers 
        $arrTemp['sales_detail'] = array();
            
        array_push($arrDisplayData, $arrTemp); 
        $MAX_ROWS_LIMIT--;
        
        $currCode = $ref; 
        $indexForCustomersDetail =  count($arrDisplayData)-1; 
        
        // add detail cust
        $hbl = trim($worksheet->getCellByColumnAndRow(12, $row)->getValue());  
        array_push($arrDisplayData[$indexForCustomersDetail]['sales_detail'], array('customer_id' => $customerName, 'hbl' => $hbl, 'items_detail' => array() )); 
       
        $indexForItemsDetail =  count($arrDisplayData[$indexForCustomersDetail]['sales_detail'])-1; 
        $currCustomer = $customerName; 
        
    }else{
 
        //if ($currCustomer <> $refCustomer  && !empty($customerName)){  
        if (!empty($customerName) && $currCustomer <> $customerName ){  

            $hbl = trim($worksheet->getCellByColumnAndRow(12, $row)->getValue());  
            array_push($arrDisplayData[$indexForCustomersDetail]['sales_detail'], array('customer_id' => $customerName, 'hbl' => $hbl, 'items_detail' => array() )); 

            $indexForItemsDetail =  count($arrDisplayData[$indexForCustomersDetail]['sales_detail'])-1; 
            $currCustomer = $customerName; 
        }
    }
    
  // $refCustomer =  $customerName;
    
   
      
    if(!empty($serviceName)){ 
     
        $amount = $sellingPriceIDR;
        $currency = 'IDR'; 
        $rate = 1;
        
        if(!empty($sellingPriceUSD)){
            $amount = $sellingPriceUSD;
            $currency = 'USD'; 
            $rate = $sellingPriceIDR / $sellingPriceUSD;
        }
          
        // default value  
        if(empty($qty)) $qty = 1;
        
        if (!isset($arrDisplayData[$indexForCustomersDetail]['sales_detail'][$indexForItemsDetail]['currency'])){  
            $arrDisplayData[$indexForCustomersDetail]['sales_detail'][$indexForItemsDetail]['currency'] = 'IDR'; 
            $arrDisplayData[$indexForCustomersDetail]['sales_detail'][$indexForItemsDetail]['rate'] = 1;
        } 
        
           
        if(strtolower($currency) <> strtolower('IDR')){  
            $arrDisplayData[$indexForCustomersDetail]['sales_detail'][$indexForItemsDetail]['currency'] = $currency; 
            $arrDisplayData[$indexForCustomersDetail]['sales_detail'][$indexForItemsDetail]['rate'] = $rate;
        }
        
        array_push($arrDisplayData[$indexForCustomersDetail]['sales_detail'][$indexForItemsDetail]['items_detail'], 
                    array('container_name' => $container,'qty' => $qty, 'service_name' => $serviceName, 'price' => $amount / $qty , 'currency' => $currency, 'rate' => $rate 
                   ));
    }

}

checkMaxRowsLimit($MAX_ROWS_LIMIT);
// ===================== CONVERT DATA STRUCTURE  
//$OBJ->setLog($arrDisplayData,true);
$arrData = importData($DATA_STRUCTURE,array('datatype' => 'datastructure', 'dataset' => $arrDisplayData )); 
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
    jQuery(document).ready(function(){ 
       startImportData($(".item-list"),<?php echo json_encode($arrData); ?>,"<?php echo $AJAX_FILE; ?>");
    });
</script>

</head >
<body>
<div style="width: 2230px; padding: 1em; ">
<h2><?php echo $TITLE; ?></h2> 
    
<?php 
    
    $headerRow = '  <tr class="header-row">'; 
    $headerRow .= '  <td style="width: 120px">'.$OBJ->lang['code'].'</td> ';
    $headerRow .= '  <td style="text-align:center; width: 80px">'.$OBJ->lang['date'].'</td> ';
    $headerRow .= '  <td style="width: 120px">'.$OBJ->lang['warehouse'].'</td> '; 
    $headerRow .= '  <td style="text-align:center; width: 80px">'.$OBJ->lang['freight'].'</td> '; 
    $headerRow .= '  <td style="text-align:center; width: 80px">FCL/LCL</td> '; 
    $headerRow .= '  <td style="width: 120px">'.$OBJ->lang['carrier'].'</td> ';
    $headerRow .= '  <td style="width: 120px">'.$OBJ->lang['destination'].'</td> ';
    $headerRow .= '  <td style="width: 120px">MBL</td> ';
    $headerRow .= '  <td style="text-align:center; width: 80px">ETD</td> ';
    $headerRow .= '  <td style="text-align:center; width: 80px">ETA</td> ';
    $headerRow .= '  <td style="width: 120px"  style="width: 120px">AJU</td> ';
    $headerRow .= '  <td style="width: 120px">PEB</td> ';
    $headerRow .= '  <td style="width: 120px">'.$OBJ->lang['reference'].'</td> ';
    $headerRow .= '  <td style="width: 120px">'.$OBJ->lang['vessel'].'</td> ';
    $headerRow .= '  <td style="width: 120px">'.$OBJ->lang['vesselNumber'].'</td> '; 
    $headerRow .= '  <td style="width: 150px">'.$OBJ->lang['container'].'</td> '; 
    $headerRow .= '  <td style="width:60px; text-align:center">'.$OBJ->lang['status'].'</td> 
                     <td>'.$OBJ->lang['description'].'</td>  
                     </td> 
    ';
    
    $salesHeaderRow = '<tr class="header-row"  style="background-color:#666 !important">'; 
    $salesHeaderRow .= '<td style="width: 150px">'.$OBJ->lang['customer'].'</td>';
    $salesHeaderRow .= '<td style="width: 150px">HBL</td>';
    $salesHeaderRow .= '<td style="width: 60px;  text-align:center">'.$OBJ->lang['currency'].'</td>';
    $salesHeaderRow .= '<td style="width: 80px;  text-align:right">'.$OBJ->lang['rate'].'</td>';
    $salesHeaderRow .= '<td style="width: 60px; text-align:center">'.$OBJ->lang['container'].'</td>';
    $salesHeaderRow .= '<td style="width: 60px; text-align:center">'.$OBJ->lang['qty'].'</td>';
    $salesHeaderRow .= '<td style="width: 150px;">'.$OBJ->lang['service'].'</td>';
    $salesHeaderRow .= '<td style="width: 80px; text-align:center">'.$OBJ->lang['currency'].'</td>';
    $salesHeaderRow .= '<td style="width: 120px; text-align:right">'.$OBJ->lang['price'].'</td>';  
    $salesHeaderRow .= '</tr>'; 
     
   
     
    // FAILED RESULT 
    echo '<div class="import-table-title text-red-cardinal">'.$OBJ->errorMsg[212].'</div>'; 
    echo '<table class="import-table import-result-failed" style="margin-bottom:2em; width:2200px !important;"> ';
    echo $headerRow;
    echo '</table>';
      
    // SUCCESS RESULT   
    echo '<div class="import-table-title">'.$OBJ->lang['dataHasBeenSuccessfullyUpdated'].'</div>';
    echo '<table class="import-table import-result-success" style="margin-bottom:2em; width:2200px !important;"> ';
    echo $headerRow;
    echo '</table>';
     
    // IMPORT LIST
    echo '<div class="import-table-title">'.$OBJ->lang['jobQueue'].' ...</div>';
    echo '<table class="import-table" style="width:2200px !important;"> ';
    echo $headerRow;
      
    $totalCol = 18;
    
    foreach($arrDisplayData as $key=>$headerRow){ 
          
        echo '<tr class="item-list border-top" relkey="'.$key.'" relgroup="'.$key.'">'; 
            echo '<td>'.$headerRow['code'].'</td>';
            echo '<td style="text-align:center;">'. date("d / m / Y",$headerRow['date']).'</td>';
            echo '<td>'.$headerRow['warehouse_id'].'</td>';
            echo '<td>'.$headerRow['freight_type'].'</td>';
            echo '<td>'.$headerRow['load_type'].'</td>';
            echo '<td>'.$headerRow['carrier_id'].'</td>';
            echo '<td>'.$headerRow['destination_name'].'</td>';
            echo '<td>'.$headerRow['mbl'].'</td>';
            echo '<td style="text-align:center; ">'. date("d / m / Y",$headerRow['etd']).'</td>';
            echo '<td style="text-align:center; ">'. date("d / m / Y",$headerRow['eta']).'</td>';
            echo '<td>'.$headerRow['aju'].'</td>';
            echo '<td>'.$headerRow['peb'].'</td>';
            echo '<td>'.$headerRow['po_reference'].'</td>'; 
            echo '<td>'.$headerRow['vessel_id'].'</td>'; 
            echo '<td>'.$headerRow['vessel_number'].'</td>'; 
            echo '<td>'.$headerRow['container_number'].'</td>'; 
            echo '<td style="text-align:center"><div class="response-code"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></td>';
            echo '<td><div class="desc"><div style="text-align:center"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></div></td>'; 
        echo '</tr>';
        
        // detail sales
        $detailSales = '<table class="import-table" style="width:auto !important">';
        $detailSales .= $salesHeaderRow;
        
        $rsSalesDetail = $headerRow['sales_detail'];
        
        foreach($rsSalesDetail as $customerRow){
            
            $firstCustomerRow = true; 
            $rsItemDetail = $customerRow['items_detail'];
              
            foreach($rsItemDetail as $itemRow){   
                
                if ($firstCustomerRow){ 
                    $customerName = $customerRow['customer_id'];
                    $hbl = $customerRow['hbl']; 
                }else{
                    $customerName = '';
                    $hbl = '';
                }
                
                $detailSales .= '<tr>';
                $detailSales .= '<td>'.$customerName.'</td>';
                $detailSales .= '<td>'.$hbl.'</td>';
                $detailSales .= '<td style="text-align:center">'.$customerRow['currency'].'</td>';
                $detailSales .= '<td style="text-align:right">'.$OBJ->formatNumber($customerRow['rate'],-2).'</td>';
                $detailSales .= '<td style="text-align:center">'.$itemRow['container_name'].'</td>';
                $detailSales .= '<td style="text-align:center">'.$itemRow['qty'].'</td>';
                $detailSales .= '<td>'.$itemRow['service_name'].'</td>';
                $detailSales .= '<td style="text-align:center">'.$itemRow['currency'].'</td>';
                $detailSales .= '<td style="text-align:right">'.$OBJ->formatNumber($itemRow['price'],-2).'</td>'; 
                $detailSales .= '</tr>';
                
                $firstCustomerRow = false;
            }
              
        }
        
        $detailSales .= '</table>';
        echo '<tr  relgroup="'.$key.'"><td></td><td colspan="'.($totalCol-1).'">'.$detailSales.'</td></tr>';
         
    } 
    echo '</table>';    
?>
</div>    
</body>    
</html> 