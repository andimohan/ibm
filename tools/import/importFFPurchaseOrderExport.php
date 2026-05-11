<?php

require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';    
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLPurchaseOrder.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLJobOrder.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';      

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Currency.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Container.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Service.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TermOfPayment.class.php';    
 
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

$MAX_ROWS_LIMIT = 50;
    
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
            // paksa jadi IDR karena support 2 currency
            $arrDisplayData[$arrDataLastIndex]['currency'] = 'IDR';
            //  $arrDisplayData[$arrDataLastIndex]['currency'] = $currency; 
            $arrDisplayData[$arrDataLastIndex]['rate'] = $rate;
        }
        
        array_push($arrDisplayData[$arrDataLastIndex]['items_detail'], array('qty' => $qty, 'container_name' => $container, 'service_name' => $costName,'description' => $itemDescription, 'price' => $amount / $qty , 'currency' => $currency, 'rate' => $rate ));
    }
      
}

checkMaxRowsLimit($MAX_ROWS_LIMIT);

// ===================== CONVERT DATA STRUCTURE 
$arrData = importData($DATA_STRUCTURE,array('datatype' => 'datastructure', 'dataset' => $arrDisplayData )); 
$arrData = removeUnusedParameter($arrData);

//$class->setLog($arrData,true);


?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />     
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>fontawesome6.min.css">    
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>    
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>api.min.js"></script>    

</head>
<body>
<div style="margin: 2em">
<h2><?php echo $TITLE; ?></h2> 
    
<?php
    
 
    $currency = new Currency();
    $rsCurrency = $currency->searchDataRow(array($currency->tableName.'.pkey', 'lower('.$currency->tableName.'.name) as name'),
                                           ' and '.$currency->tableName.'.statuskey = 1'
                                          );
    $rsCurrency = array_column($rsCurrency,'pkey','name');
    
    $warehouse = new Warehouse();
    $rsWarehouse = $warehouse->searchDataRow(array($warehouse->tableName.'.pkey', 'lower('.$warehouse->tableName.'.name) as name'),
                                           ' and '.$warehouse->tableName.'.statuskey = 1');
    $rsWarehouse = array_column($rsWarehouse,'pkey','name');
    
    
    $rsSupplier = array_column($arrDisplayData,'supplier_id');
    $supplier = new Supplier();
    $rsSupplier = $supplier->searchDataRow(array($supplier->tableName.'.pkey', 'lower('.$supplier->tableName.'.name) as name'),
                                           ' and '.$supplier->tableName.'.statuskey = 1 and '.$supplier->tableName.'.name in('.$OBJ->oDbCon->paramString($rsSupplier,',').')');
    $rsSupplier = array_column($rsSupplier,'pkey','name');
  
    $rsSalesOrder = array_column($arrDisplayData,'sales_order_id'); 
    $rsSalesOrder = $emklJobOrderExport->searchDataRow(array($emklJobOrderExport->tableName.'.pkey', 'lower('.$emklJobOrderExport->tableName.'.code) as code'),
                                           ' and '.$emklJobOrderExport->tableName.'.statuskey in (1,2,3) and '.$emklJobOrderExport->tableName.'.code in('.$OBJ->oDbCon->paramString($rsSalesOrder,',').')');
    $rsSalesOrder = array_column($rsSalesOrder,'pkey','code');
   
    // Job Header
/*    $rsSalesOrderHeader = array_column($arrDisplayData,'sales_order_id'); 
    $rsSalesOrderHeader = $emklJobOrderHeaderExport->searchDataRow(array($emklJobOrderHeaderExport->tableName.'.pkey', 'lower('.$emklJobOrderHeaderExport->tableName.'.code) as code'),
                                           ' and '.$emklJobOrderHeaderExport->tableName.'.statuskey in (1,2,3) and '.$emklJobOrderHeaderExport->tableName.'.code in('.$OBJ->oDbCon->paramString($rsSalesOrderHeader,',').')');
    $rsSalesOrderHeader = array_column($rsSalesOrderHeader,'pkey','code');
    */
   
    $rsTableKey = $emklJobOrderExport->getTableKeyAndObj($emklJobOrderExport->tableName,array('key'));
        
    $service  = new Service(SERVICE);
    $container  = new Container;
    
    $rsService = array();
    $rsContainer = array();
    foreach($arrDisplayData as $key=>$itemRow){ 
        $arrDetail = $itemRow['items_detail'];

        foreach($arrDetail as $detailRow){  
            array_push($rsService,strtolower($detailRow['service_name']));
            array_push($rsContainer,strtolower($detailRow['container_name']));
        }
    }
    
    $rsContainer = $container->searchDataRow(array($container->tableName.'.pkey', 'lower('.$container->tableName.'.name) as name'),
                                           ' and '.$container->tableName.'.statuskey = 1 and '.$container->tableName.'.name in('.$OBJ->oDbCon->paramString($rsContainer,',').')');
    $rsContainer = array_column($rsContainer,'pkey','name');
  
          
    $rsService = $service->searchDataRow(array($service->tableName.'.pkey', 'lower('.$service->tableName.'.name) as name'),
                                           ' and '.$service->tableName.'.statuskey = 1 and '.$service->tableName.'.name in('.$OBJ->oDbCon->paramString($rsService,',').')');
    $rsService = array_column($rsService,'pkey','name');
  
    $arrUpdateResult = array(); 
    $arrUpdateResult['success']['label'] = $OBJ->lang['dataHasBeenSuccessfullyUpdated'];
    $arrUpdateResult['success']['class'] = 'text-green-avocado';
    $arrUpdateResult['success']['data'] = array(); 
    
    $arrUpdateResult['failed']['label'] = $OBJ->errorMsg[212];
    $arrUpdateResult['failed']['class'] = 'text-red-cardinal';
    $arrUpdateResult['failed']['data'] = array(); 
    
    foreach($arrDisplayData as $key=>$itemRow){   
        $arrDetail = $itemRow['items_detail'];

        try{  
			if(!$OBJ->oDbCon->startTrans())
				throw new Exception($OBJ->errorMsg[100]);
		 				 
            
            $arrParam = array();
            $arrParam['code'] = 'xxxxx';
            $arrParam['hidJobOrderKey'] =  $rsSalesOrder[strtolower(trim($itemRow['sales_order_id']))] ;  
            $arrParam['selWarehouseKey'] =  $rsWarehouse[strtolower(trim($itemRow['warehouse_id']))] ; 
            $arrParam['refInvoiceCode'] =  $itemRow['invoice_reference'] ; 
            $arrParam['hidSupplierKey'] =  $rsSupplier[strtolower(trim($itemRow['supplier_id']))]; 
            $arrParam['trDate'] =  date("d / m / Y",$itemRow['date']); 
            $arrParam['selCurrency'] = $rsCurrency[strtolower(trim($itemRow['currency']))]; 
            $arrParam['currencyRate'] =  $itemRow['rate'];  
            $arrParam['selJOType'] = $rsTableKey['key']; 
            //$this->arrData['refjoheaderkey'] = array('hidJobHeaderKey');
            
            $arrParam['hidDetailKey'] = array(); 
            $arrParam['hidContainerDetailKey'] = array(); 
            $arrParam['hidServiceKey'] = array(); 
            $arrParam['qty'] = array(); 
            $arrParam['selCurrencyDetail'] = array(); 
            $arrParam['priceInUnit'] = array(); 
            $arrParam['description'] = array(); 
            
            foreach($arrDetail as $detailRow){  
                array_push($arrParam['hidDetailKey'], 0);
                array_push($arrParam['hidContainerDetailKey'], $rsContainer[strtolower(trim($detailRow['container_name']))]);
                array_push($arrParam['hidServiceKey'], $rsService[strtolower(trim($detailRow['service_name']))]);
                array_push($arrParam['qty'], $detailRow['qty']);
                array_push($arrParam['selCurrencyDetail'], $rsCurrency[strtolower(trim($detailRow['currency']))]); 
                array_push($arrParam['priceInUnit'], $detailRow['price']);
                array_push($arrParam['description'], $detailRow['description']);  
            }

            //$OBJ->setLog($arrParam,true);
            $response = $OBJ->addData($arrParam);
            if (!$response[0]['valid']) { 
                // perlu throw agar connectionCtr nya bisa diset 0 lg.  
                throw new Exception($response[0]['message']);
            }else { 
                array_push($arrUpdateResult['success']['data'], $itemRow);
            }
            
            //$OBJ->setLog($response,true);   
            
			$OBJ->oDbCon->endTrans(); 
		
	    } catch(Exception $e){  
            //$OBJ->setLog($e->getMessage(),true);
            $itemRow['_errMsg'] = $e->getMessage();
            array_push($arrUpdateResult['failed']['data'], $itemRow);
			$OBJ->oDbCon->rollback(); 
		}		 
    }  
 
    
?>
    
<?php 
    
    $headerRow = '  <div class="div-table-row header-row">'; 
    $headerRow .= '  <div class="div-table-col-3" style="width:150px">'.$OBJ->lang['code'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="width:100px">'.$OBJ->lang['warehouse'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="width:100px">'.$OBJ->lang['supplier'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="width:100px">AJU</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="width:100px; text-align:center">'.$OBJ->lang['date'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="width:180px">'.$OBJ->lang['JOCode'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="width:200px">'.$OBJ->lang['invoiceReference'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="text-align:center; width:60px">'.$OBJ->lang['qty'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="text-align:center; width:100px">'.$OBJ->lang['container'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="width:120px">'.$OBJ->lang['cost'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="width:150px">'.$OBJ->lang['description'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="text-align:right; width:100px">'.$OBJ->lang['amount'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="text-align:center; width:80px">'.$OBJ->lang['currency'].'</div> ';
    $headerRow .= '  <div class="div-table-col-3" style="text-align:right; width:80px">'.$OBJ->lang['rate'].'</div> '; 
    $headerRow .= '  <div class="div-table-col-3" style="text-align:center;width:100px">'.$OBJ->lang['status'].'</div>';
    $headerRow .= '  <div class="div-table-col-3">'.$OBJ->lang['description'].'</div>';   
    $headerRow .= '  </div>';
 
    foreach($arrUpdateResult as $key=>$resultRow){
        
        echo '<div class="div-table import-table" style="margin-bottom:2em; width: 2000px">';
        echo '<div class="div-table-caption '.$resultRow['class'] .'">'.$resultRow['label'] .'</div>';
        echo $headerRow;

        $firstRow = true;
        foreach($resultRow['data'] as $key=>$itemRow){ 
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
                    echo '<div class="div-table-col-3" style="text-align:center">'. date("d / m / Y",$itemRow['date']).'</div>';
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
                    echo '<div class="div-table-col-3" style="text-align:center"><div class="response-code"></div></div>';
                    echo '<div class="div-table-col-3"><div class="desc"><div style="text-align:left">'.(isset($itemRow['_errMsg']) ? $itemRow['_errMsg']: '').'</div></div></div>';
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
    }    

    
?>
</div>    
</body>    
</html>