<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';    
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemOut.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';

$OBJ = new ItemOut();   

$MODULE_NAME = 'itemOut';
$TITLE = $OBJ->lang['itemOut'];
$AJAX_FILE = 'ajax-api-item-out';
  
$ITEM_DETAIL = array(); 
array_push($ITEM_DETAIL, array('field' => 'pkey'));
array_push($ITEM_DETAIL, array('field' => 'item_id', 'convert' => array('obj' => new Item() ))); 
array_push($ITEM_DETAIL, array('field' => 'qty')); 
array_push($ITEM_DETAIL, array('field' => 'unit_id', 'convert' => array('obj' => new ItemUnit() ))); 


$DATA_STRUCTURE = array(); 
array_push($DATA_STRUCTURE, array('field' => 'code')); 
array_push($DATA_STRUCTURE, array('field' => 'date')); 
array_push($DATA_STRUCTURE, array('field' => 'warehouse_id', 'convert' => array('obj' => new Warehouse() ))); 
array_push($DATA_STRUCTURE, array('field' => 'description')); 
array_push($DATA_STRUCTURE, array('field' => 'items_detail', 'detail' => $ITEM_DETAIL )); 


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
     
    $itemName = $worksheet->getCellByColumnAndRow(3, $row)->getValue(); 
    $qty = $worksheet->getCellByColumnAndRow(4, $row)->getValue();  
    $unitName = $worksheet->getCellByColumnAndRow(5, $row)->getValue();   

    $ref =  $trdate.'|'.$warehouseId;
    
    if ($currCode <> $ref){  
        
        $arrTemp = array(); 
        $arrTemp['code'] = $code;  
        $arrTemp['date'] = $trdate; 
        $arrTemp['warehouse_id'] = $warehouseId; 
       
        // item details
        $arrTemp['items_detail'] = array();
      
        array_push($arrDisplayData, $arrTemp);
        $indexCtr = count($arrDisplayData)  - 1;
        
        $MAX_ROWS_LIMIT--;
         
        $currCode = $ref; 
        
    }

    array_push($arrDisplayData[$indexCtr]['items_detail'], array(
        'item_id' => $itemName,
        'qty' => $qty,
        'unit_id' => $unitName, 
    )); 


}

checkMaxRowsLimit($MAX_ROWS_LIMIT);

// ===================== CONVERT DATA STRUCTURE
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
<div style="padding: 1em; ">
<h2><?php echo $TITLE; ?></h2> 
    
<?php 
    
    $headerRow = '  <tr class="header-row">'; 
    $headerRow .= '  <td style="width: 120px">'.$OBJ->lang['code'].'</td> ';
    $headerRow .= '  <td style="text-align:center; width: 80px">'.$OBJ->lang['date'].'</td> ';
    $headerRow .= '  <td style="width: 120px">'.$OBJ->lang['warehouse'].'</td> ';   
    $headerRow .= '  <td style="width:60px; text-align:center">'.$OBJ->lang['status'].'</td> 
                     <td>'.$OBJ->lang['description'].'</td>  
                     </td> 
    ';
    
    $itemRow = '<tr class="header-row"  style="background-color:#666 !important">'; 
    $itemRow .= '<td>'.$OBJ->lang['item'].'</td>'; 
    $itemRow .= '<td style="width: 60px;  text-align:right">'.$OBJ->lang['qty'].'</td>';
    $itemRow .= '<td style="width: 80px;">'.$OBJ->lang['unit'].'</td>'; 
    $itemRow .= '</tr>'; 
      
     
    // FAILED RESULT 
    echo '<div class="import-table-title text-red-cardinal">'.$OBJ->errorMsg[212].'</div>'; 
    echo '<table class="import-table import-result-failed" style="margin-bottom:2em; width:1000px !important;"> ';
    echo $headerRow;
    echo '</table>';
      
    // SUCCESS RESULT   
    echo '<div class="import-table-title">'.$OBJ->lang['dataHasBeenSuccessfullyUpdated'].'</div>';
    echo '<table class="import-table import-result-success" style="margin-bottom:2em; width:1000px !important;"> ';
    echo $headerRow;
    echo '</table>';
     
    // IMPORT LIST
    echo '<div class="import-table-title">'.$OBJ->lang['jobQueue'].' ...</div>';
    echo '<table class="import-table" style="width:1000px !important;"> ';
    echo $headerRow;
      
    $totalCol = 5;
    
    foreach($arrDisplayData as $key=>$headerRow){ 
          
        echo '<tr class="item-list border-top" relkey="'.$key.'" relgroup="'.$key.'">'; 
            echo '<td>'.$headerRow['code'].'</td>';
            echo '<td style="text-align:center; width: 100px">'. date("d / m / Y",$headerRow['date']).'</td>';
            echo '<td>'.$headerRow['warehouse_id'].'</td>'; 
            echo '<td style="text-align:center"><div class="response-code"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></td>';
            echo '<td><div class="desc"><div style="text-align:center"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></div></td>'; 
        echo '</tr>';
        
        // detail
        $detailItem = '<table class="import-table" style="width:100%">';
        $detailItem .= $itemRow;
        
        $rsSalesDetail = $headerRow['items_detail'];
        
        foreach($rsSalesDetail as $detailRow){  
              
            $detailItem .= '<tr>'; 
            $detailItem .= '<td>'.$detailRow['item_id'].'</td>';
            $detailItem .= '<td style="text-align:right">'.$OBJ->formatNumber($detailRow['qty'],-2).'</td>';  
            $detailItem .= '<td>'.$detailRow['unit_id'].'</td>';
            $detailItem .= '</tr>';
              
        }
        
        $detailItem .= '</table>';
        echo '<tr relgroup="'.$key.'"><td></td><td colspan="'.($totalCol-1).'">'.$detailItem.'</td></tr>';
         
    }
    
    echo '</table>'; 
?>
</div>    
</body>    
</html> 