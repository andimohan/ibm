<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';    
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemAdjustment.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemMovement.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';

$OBJ = new ItemAdjustment();   
$itemMovement = new ItemMovement();
$item = new Item();
$warehouse = new Warehouse();

$MODULE_NAME = 'ItemAdjustment';
$TITLE = $OBJ->lang['itemAdjustment'];
$AJAX_FILE = 'ajax-api-item-adjustment';
  
$ITEM_DETAIL = array(); 
array_push($ITEM_DETAIL, array('field' => 'pkey'));
array_push($ITEM_DETAIL, array('field' => 'item_id', 'convert' => array('obj' => new Item() )));  
array_push($ITEM_DETAIL, array('field' => 'qty_before'));  
array_push($ITEM_DETAIL, array('field' => 'qty_after'));  
//array_push($ITEM_DETAIL, array('field' => 'unit_id', 'convert' => array('obj' => new ItemUnit() ))); 
array_push($ITEM_DETAIL, array('field' => 'cost_in_base_unit'));  

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


//sementara
//$item = new Item();

$rsWarehouseCol = $warehouse->searchDataRow(array($warehouse->tableName.'.pkey',$warehouse->tableName.'.code',$warehouse->tableName.'.name'));
$rsWarehouseCol = array_column($rsWarehouseCol,null,'name');

// compile dulu jadi satu array, agar kalo ad SKU di beberapa baris, kita sum dulu
$arrQueue = array();


for ($row = 2; $row <= $highestRow; ++$row) { 
	     
    $trdate = $worksheet->getCellByColumnAndRow(1, $row)->getValue();  
    $trdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($trdate);
    $trdate = $trdate->getTimestamp(); 
	
    $warehouseName = $worksheet->getCellByColumnAndRow(2, $row)->getValue();  
     
    $itemCode = $worksheet->getCellByColumnAndRow(3, $row)->getValue(); 
    $qtyAfter = $worksheet->getCellByColumnAndRow(4, $row)->getValue();  
	if(empty($qtyAfter)) $qtyAfter = 0;
	
	// gk perlu unit, selalu keupdate nanti pake baseunit
//    $unitName = $worksheet->getCellByColumnAndRow(5, $row)->getValue();  
    $costInBaseUnit = $worksheet->getCellByColumnAndRow(5, $row)->getValue();  
	
	if(!isset($arrQueue[$itemCode])){
		$arrQueue[$itemCode] = array(
				'trdate' => $trdate,
				'itemcode' => $itemCode,
				'warehousename' => $warehouseName,
				'qtyafter' => $qtyAfter,
				'cogs' => $costInBaseUnit,
		);
	}else{
		$arrQueue[$itemCode]['qtyafter'] += $qtyAfter;
	}
	 
}


$rsItemCol = $item->searchDataRow(array('pkey','code'), ' and code in ('.$OBJ->oDbCon->paramString(array_keys($arrQueue),',').')');
$rsItemCol = array_column($rsItemCol,null,'code');

$qtyBeforeCol = $itemMovement->sumItemsMovement(array_column($rsItemCol,'pkey'),$rsWarehouse['$warehouseId']['pkey'],date("d / m / Y",$trdate));
$qtyBeforeCol = array_column($qtyBeforeCol, null, 'itemkey');	

foreach($arrQueue as $row){
	
	// jika SKU tdk ketemu dan stok kosong
	if (!isset($rsItemCol[$row['itemcode']]) && $row['qtyafter'] == 0) continue;
		
	if(!isset($rsItemCol[$row['itemcode']])){
		$class->setLog($row['itemcode'],true);
		continue;
	}
	
	$itemkey = $rsItemCol[$row['itemcode']]['pkey']; 
	$qtyBefore = $qtyBeforeCol[$itemkey]['qtyinbaseunit'];
	
	
    $ref =  $row['trdate'].'|'.$row['warehousename'];
	 
    if ($currCode <> $ref){  
        
        $arrTemp = array(); 
        $arrTemp['code'] = $code;  
        $arrTemp['date'] = $row['trdate']; 
        $arrTemp['warehouse_id'] = $rsWarehouseCol[$row['warehousename']]['code']; 
       
        // item details
        $arrTemp['items_detail'] = array();
      
        array_push($arrDisplayData, $arrTemp);
        $indexCtr = count($arrDisplayData) - 1;
        
        $MAX_ROWS_LIMIT--;
         
        $currCode = $ref; 
        
    }
	
	if(empty($itemCode)) continue;

	array_push($arrDisplayData[$indexCtr]['items_detail'], array(
		'item_id' => $row['itemcode'],
		'qty_before' => $qtyBefore,
		'qty_after' => $row['qtyafter'],
		'cost_in_base_unit' => $row['cogs'],
	)); 


	

}

//
//for ($row = 2; $row <= $highestRow; ++$row) { 
//        
//    $trdate = $worksheet->getCellByColumnAndRow(1, $row)->getValue();  
//    $trdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($trdate);
//    $trdate = $trdate->getTimestamp(); 
//	
//    $warehouseId = $worksheet->getCellByColumnAndRow(2, $row)->getValue();  
//     
//    $itemCode = $worksheet->getCellByColumnAndRow(3, $row)->getValue(); 
//    $qtyAfter = $worksheet->getCellByColumnAndRow(4, $row)->getValue();  
//	// gk perlu unit, selalu keupdate nanti pake baseunit
////    $unitName = $worksheet->getCellByColumnAndRow(5, $row)->getValue();  
//    $costInBaseUnit = $worksheet->getCellByColumnAndRow(5, $row)->getValue();  
// 
//	$rsItem = $item->searchDataRow(array('pkey'), ' and code = '. $OBJ->oDbCon->paramString($itemCode) );
//
//	$qtyBefore = $itemMovement->sumItemMovement($rsItem[0]['pkey'],$rsWarehouse['$warehouseId']['pkey'],date("d / m / Y",$trdate));
//	
//    $ref =  $trdate.'|'.$warehouseId;
//    
//    if ($currCode <> $ref){  
//        
//        $arrTemp = array(); 
//        $arrTemp['code'] = $code;  
//        $arrTemp['date'] = $trdate; 
//        $arrTemp['warehouse_id'] = $rsWarehouseCol[]['code']; 
//       
//        // item details
//        $arrTemp['items_detail'] = array();
//      
//        array_push($arrDisplayData, $arrTemp);
//        $indexCtr = count($arrDisplayData)  - 1;
//        
//        $MAX_ROWS_LIMIT--;
//         
//        $currCode = $ref; 
//        
//    }
//
//
//    if(empty($itemCode)) continue;
//	
////	$rsItem = $item->searchDataRow(array('pkey'), ' and name like \'%'.$itemName.'%\'' );
////	if (empty($rsItem)){
////		$OBJ->setLog($itemName,true);
////	}
//	
//    array_push($arrDisplayData[$indexCtr]['items_detail'], array(
//        'item_id' => $itemCode,
//        'qty_before' => $qtyBefore,
//        'qty_after' => $qtyAfter,
////        'unit_id' => $unitName,
//        'cost_in_base_unit' => $costInBaseUnit
//    )); 
//
//
//}

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
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>font-awesome-5.15.min.css">    
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
    $itemRow .= '<td style="width: 60px; text-align:right">'.$OBJ->lang['cost'].'</td>'; 
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
            $detailItem .= '<td style="text-align:right">'.$OBJ->formatNumber($detailRow['qty_after'],-2).'</td>';   
            $detailItem .= '<td style="text-align:right">'.$OBJ->formatNumber($detailRow['cost_in_base_unit']).'</td>'; 
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
