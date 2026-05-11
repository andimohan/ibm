<?php

// sementara buat utk BOS dulu
// patokan ar payment adalah referensi transaksi wintera atau invoice marketplace
// nanti ditarik berdasarkan refcode / transcode


require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';    
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrder.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AR.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ARPayment.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Currency.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PaymentMethod.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CostCashOut.class.php';

	
$OBJ = new ARPayment();   
$customer = new Customer();
$costCashOut = new CostCashOut();
$paymentMethod = new PaymentMethod();
$warehouse = new Warehouse();

$MODULE_NAME = 'arPayment';
$TITLE = $OBJ->lang['arPayment'];
$AJAX_FILE = 'ajax-api-ar-payment';
  
$ITEM_DETAIL = array(); 
array_push($ITEM_DETAIL, array('field' => 'pkey'));
array_push($ITEM_DETAIL, array('field' => 'ar_id'));
array_push($ITEM_DETAIL, array('field' => 'amount')); 
array_push($ITEM_DETAIL, array('field' => 'discount')); 
array_push($ITEM_DETAIL, array('field' => 'outstanding'));
array_push($ITEM_DETAIL, array('field' => 'tax_pph_23')); 
// taxpph blm dimasukkan

$COST_DETAIL = array();
array_push($COST_DETAIL, array('field' => 'pkey'));
array_push($COST_DETAIL, array('field' => 'cost_id'));
array_push($COST_DETAIL, array('field' => 'amount'));

// baru API dari bank langsung
$PAYMENT_DETAIL = array(); 
array_push($PAYMENT_DETAIL, array('field' => 'pkey'));
array_push($PAYMENT_DETAIL, array('field' => 'payment_method_id')); 
array_push($PAYMENT_DETAIL, array('field' => 'amount'));

$DATA_STRUCTURE = array(); 
array_push($DATA_STRUCTURE, array('field' => 'code')); 
array_push($DATA_STRUCTURE, array('field' => 'date'));   
array_push($DATA_STRUCTURE, array('field' => 'currency_id')); 
array_push($DATA_STRUCTURE, array('field' => 'rate'));   
array_push($DATA_STRUCTURE, array('field' => 'warehouse_id', 'convert' => array('obj' => new Warehouse() ))); 
array_push($DATA_STRUCTURE, array('field' => 'customer_id')); // customer harus ID, karena nama bisa sama
array_push($DATA_STRUCTURE, array('field' => 'description')); 
//array_push($DATA_STRUCTURE, array('field' => 'total_payment'));

array_push($DATA_STRUCTURE, array('field' => 'detail', 'detail' => $ITEM_DETAIL )); 
array_push($DATA_STRUCTURE, array('field' => 'payment_method_detail', 'detail' => $PAYMENT_DETAIL )); 
array_push($DATA_STRUCTURE, array('field' => 'cost_detail', 'detail' => $COST_DETAIL )); 


// ===================== COMPILING DATA
$arrDisplayData = array(); 
 
$code = '[auto code]'; 
$currCode = ''; 
$indexCtr = 0;

$salesOrder = new SalesOrder();
$ar = new AR();


// baca jenis biaya
$startCostColIndex = 10;
$arrCost = array();

$rsCost = $costCashOut->searchDataRow(array($costCashOut->tableName.'.pkey','lower('.$costCashOut->tableName.'.name) as name',$costCashOut->tableName.'.code'),
									  ' and '. $costCashOut->tableName.'.statuskey = 1');
$rsCost = array_column($rsCost,null,'name');
  
for ($col = $startCostColIndex; $col <= $highestColumnIndex; $col++) { 
	$colName = strtolower($worksheet->getCellByColumnAndRow($col, 1)->getValue()); 
	
	if (!isset($rsCost[$colName])) continue;
	  
	$arrCost[$col] = array('code'=>$rsCost[$colName]['code'] , 'amount' => 0); 
}

$rsPaymentMethod = $paymentMethod->searchDataRow(array($paymentMethod->tableName.'.code'),' and '.$paymentMethod->tableName.'.statuskey =1  order by '.$paymentMethod->tableName.'.pkey asc limit 1');
$defaultPaymentMethod = $rsPaymentMethod[0]['code'];

//tarik aj dulu semau customer, harusnya sih gk byk2 amat
$rsCustomerCol = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.code'));
$rsCustomerCol = array_column($rsCustomerCol,null,'pkey');


$rsWarehouseCol = $warehouse->searchDataRow(array($warehouse->tableName.'.pkey',$warehouse->tableName.'.code'));
$rsWarehouseCol = array_column($rsWarehouseCol,null,'pkey');


for ($row = 2; $row <= $highestRow; ++$row) { 
        
    $trdate = $worksheet->getCellByColumnAndRow(1, $row)->getValue();  
    $trdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($trdate);
    $trdate = $trdate->getTimestamp(); 

	// gk perlu lagi, narik otomatis dari invoice saja
//    $warehouseId = $worksheet->getCellByColumnAndRow(2, $row)->getValue();  
    $invoiceType = $worksheet->getCellByColumnAndRow(2, $row)->getValue();  
    $invoiceNo = $worksheet->getCellByColumnAndRow(3, $row)->getValue();  
	// gk perlu lagi, narik otomatis dari invoice saja
	//    $customerName = $worksheet->getCellByColumnAndRow(5, $row)->getValue();  
	
    
	$paymentId = $worksheet->getCellByColumnAndRow(4, $row)->getValue(); 
	// kalo kosong otomatis ambil dari payment default atau payment pertama

	$currencyId = $worksheet->getCellByColumnAndRow(5, $row)->getValue(); 
	$rate = $worksheet->getCellByColumnAndRow(6, $row)->getValue(); 
	$paymentAmount = $worksheet->getCellByColumnAndRow(7, $row)->getValue();  
	$discount = abs($worksheet->getCellByColumnAndRow(8, $row)->getValue()); // abs utk BOS
	$tax23 = $worksheet->getCellByColumnAndRow(9, $row)->getValue(); 
	
	
	// update nilai cost  
	$totalCost = 0;
	$costCounter = -1; // nant idi set, tergantung dari jensi laporannya, costnya positif atau negatif
	
	// harus cocokin ulang index nya 
	foreach($arrCost as $costRowKey=>$costRow){  
		// abs utk BOS 
//		$arrCost[$costRowKey]['amount'] = abs($worksheet->getCellByColumnAndRow( ($costRowKey ) , $row)->getValue()); 
		$arrCost[$costRowKey]['amount'] = $worksheet->getCellByColumnAndRow( ($costRowKey ) , $row)->getValue() * $costCounter; 
		$totalCost += $arrCost[$costRowKey]['amount'];
	}

 
	// cari ar key
	$fieldName = (strtolower($invoiceType) == 'refcode') ? 'refcode' : 'code' ;
 
	
	$rsSalesOrder = $salesOrder->searchDataRow(array($salesOrder->tableName.'.pkey',$salesOrder->tableName.'.customerkey',$salesOrder->tableName.'.warehousekey'),
												   ' and '. $salesOrder->tableName.'.statuskey in (2,3)
												   	 and '. $salesOrder->tableName.'.'. $fieldName.' = ' . $OBJ->oDbCon->paramString($invoiceNo) );
 	
	$invoicekey = $rsSalesOrder[0]['pkey'];
	$warehousekey = $rsSalesOrder[0]['warehousekey'];
	
	// cari warehouse berdasarkan invoice
	$warehouseId = $rsWarehouseCol[$warehousekey]['code'];
	
	// cari customer berdasarkan invoice
//	$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.code'),
//											   ' and '. $customer->tableName.'.pkey = ' . $OBJ->oDbCon->paramString($rsSalesOrder[0]['customerkey']) );

	$customerkey = $rsSalesOrder[0]['customerkey'];
	$rsCustomer = $rsCustomerCol[$customerkey];
	
	// kalo kosong, ambil dr default
	if(empty($paymentId)){
		$rsPaymentDefault = $customer->getDefaultPaymentBank($rsCustomer['pkey']);
		$rsPaymentDefault = array_column($rsPaymentDefault,null,'pkey');
		
		$paymentId = (!empty($rsPaymentDefault)) ? $rsPaymentDefault[$rsCustomer['pkey']]['code'] : $defaultPaymentMethod;
	}
		
	
		
	$customerId = $rsCustomer['code'];
	$outstanding = 0;
	$arcode = '';
	
	// cari ar berdasarkan no inv   
	if(!empty($invoicekey)){
		$rsAR = $ar->searchDataRow(array($ar->tableName.'.pkey',$ar->tableName.'.code',$ar->tableName.'.outstanding'),
								   ' and '. $ar->tableName.'.statuskey in (1,2)
									 and '. $ar->tableName.'.outstanding  > 0
									 and '. $ar->tableName.'.refheaderkey = ' .  $OBJ->oDbCon->paramString($invoicekey) );
 
		$arcode = (!empty($rsAR)) ? $rsAR[0]['code'] : '' ;
		$outstanding = (!empty($rsAR)) ? $rsAR[0]['outstanding'] : 0 ;
	}
	

	$arrTemp = array(); 
	$arrTemp['code'] = $code;  
	$arrTemp['date'] = $trdate;
	$arrTemp['warehouse_id'] = $warehouseId; 
	$arrTemp['customer_id'] = $customerId;
	$arrTemp['currency_id'] = $currencyId;
	$arrTemp['rate'] = $rate;
	$arrTemp['description'] = ''; 

	// item details
	$arrTemp['detail'] = array();
	$arrTemp['payment_method_detail'] = array();
	$arrTemp['cost_detail'] = array();

	array_push($arrDisplayData, $arrTemp);
	$indexCtr = count($arrDisplayData) - 1;

	$MAX_ROWS_LIMIT--;
 
	// pelunasan per baris AR
    array_push($arrDisplayData[$indexCtr]['detail'], array(
        'ar_id' => $arcode, 
        'outstanding' => $outstanding, 
        'amount' => $paymentAmount, 
		'discount' => $discount,
        'tax_pph_23' => $tax23, 
		'invoice_id' => $invoiceNo
    ));
	
	// nilai payment utk BOS yg diisi adalah total keseluruhan (sebelum potong biaya lain2)
	// nanti buatkan settingan saja agar bisa berbeda
	 
    array_push($arrDisplayData[$indexCtr]['payment_method_detail'], array(
        'payment_method_id' => $paymentId,  
        'amount' => ($paymentAmount-$discount-$tax23-$totalCost),  // asumsi cuma satu payment method
    )); 

	
	foreach($arrCost as  $costRow) { 
		if($costRow['amount']<=0) continue;
		
		array_push($arrDisplayData[$indexCtr]['cost_detail'], array(
			'cost_id' => $costRow['code'],  
			'amount' => $costRow['amount'],  // asumsi cuma satu payment method
		)); 
	}


}

// matikan dul uutk BOS
//checkMaxRowsLimit($MAX_ROWS_LIMIT);

validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);  

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
    $headerRow .= '  <td style="width: 120px">'.$OBJ->lang['customer'].'</td> ';   
    $headerRow .= '  <td style="width:60px; text-align:center">'.$OBJ->lang['status'].'</td> 
                     <td>'.$OBJ->lang['description'].'</td>  
                     </td> 
    ';
    
    $itemRow = '<tr class="header-row"  style="background-color:#666 !important">'; 
    $itemRow .= '<td>'.$OBJ->lang['invoice'].'</td>';   
    $itemRow .= '<td style="width: 80px; text-align:right">'.$OBJ->lang['amount'].'</td>'; 
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
            echo '<td>'.$headerRow['customer_id'].'</td>'; 
            echo '<td style="text-align:center"><div class="response-code"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></td>';
            echo '<td><div class="desc"><div style="text-align:center"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></div></td>'; 
        echo '</tr>';
        
        // detail
        $detailItem = '<table class="import-table" style="width:100%">';
        $detailItem .= $itemRow;
        
        $rsSalesDetail = $headerRow['detail'];
        
        foreach($rsSalesDetail as $detailRow){  
              
            $detailItem .= '<tr>'; 
            $detailItem .= '<td>'.$detailRow['invoice_id'].'</td>';
            $detailItem .= '<td style="text-align:right">'.$OBJ->formatNumber($detailRow['amount'],-2).'</td>';   
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