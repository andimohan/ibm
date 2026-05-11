<?php
	  
include '../../_config.php';   
include '../../_include-v2.php';

includeClass('PurchaseOrder.class.php');
$purchaseOrder = createObjAndAddToCol(new PurchaseOrder());
$item = createObjAndAddToCol(new Item());
$supplier = createObjAndAddToCol(new Supplier());
$warehouse = createObjAndAddToCol(new Warehouse());
include '_global.php';

$obj= $purchaseOrder;
$securityObject = 'reportPurchaseOrder'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 

$arrFilterInformation = array();    
$detailCriteria = '';
$dataToExport = array();
$_POST['selStatus[]'] = array(2,3);

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"130px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"150px");
$arrDataStructure['supplier'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'suppliername', 'width'=>"150px", 'mergeExcelCell' => 2);
$arrDataStructure['subtotal'] = array('title'=>ucwords($obj->lang['subtotal']),'dbfield' => 'subtotal','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['finalDiscount'] = array('title'=>ucwords($obj->lang['finalDiscount']),'dbfield' => 'finaldiscount','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['tax'] = array('title'=>ucwords($obj->lang['tax']),'dbfield' => 'taxvalue','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['shipmentFee'] = array('title'=>ucwords($obj->lang['shippingFee']),'dbfield' => 'shipmentfee','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['etccost'] = array('title'=>ucwords($obj->lang['etccost']),'dbfield' => 'etccost','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'grandtotal','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['purchaseOrderReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
if ($isShowDetail){ 
// detail ... 
$arrDataDetailStructure = array(); 
$arrDataDetailStructure['itemCode'] = array('title'=>ucwords($obj->lang['itemCode']),  'dbfield' => 'itemcode', 'width'=>"100px" );  
$arrDataDetailStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname', 'width'=>"200px", 'mergeExcelCell' => 2 );  
$arrDataDetailStructure['qty'] = array('title'=>ucwords($obj->lang['orderedQty']),  'dbfield' => 'qty', 'width'=>"100px" , 'format' => 'number'); 
$arrDataDetailStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),  'dbfield' => 'unitname', 'width'=>"60px" );  
$arrDataDetailStructure['receivedQty'] = array('title'=>ucwords($obj->lang['receivedQty']),  'dbfield' => 'receivedqty', 'width'=>"180px"); 
$arrDataDetailStructure['priceInUnit'] = array('title'=>ucwords($obj->lang['price']),'dbfield' => 'priceinunit', 'width'=>"60px",'format'=>'number');
$arrDataDetailStructure['discount'] = array('title'=>ucwords($obj->lang['discount']),'dbfield' => 'discount', 'width'=>"100px",'format'=>'number');
$arrDataDetailStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'total', 'width'=>"100px",'format'=>'number');
  
$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "960px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 
}

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['purchaseCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['purchaseCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['purchaseCode']));
	}
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
 
	if(isset($_POST) && !empty($_POST['supplierName'])) {
		$criteria .= ' AND '.$obj->tableSupplier.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['supplierName'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'Pemasok', 'filter' =>  $_POST['supplierName']));
	} 
	 
	if(isset($_POST) && !empty($_POST['itemName'])) { 
        $detailCriteria .= ' AND '.$obj->tableItem.'.name  LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemName']));
	}
    
 
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
        
	} 
	

	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status', 'filter' => $statusName));
        
	}
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
     
	$order = 'order by '.$orderBy.' ' .$orderType;
	$rs = $obj->searchData('','',true,$criteria,$order);
	
	 	$totalSubTotal = 0;
		$totalDiscount = 0;
		$totalTax = 0;
		$totalShipmentFee = 0;
		$totalEtcCost = 0;
		$totalGrandTotal = 0;
		
		$temp = 1;
		$tempreport = '';
		
		if (empty($rs))
            $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';


        $rsDetailCol = ($isShowDetail) ? $obj->getDetailCollections($rs,'refkey',$detailCriteria) : array();

        $totalRs = count($rs);
        for( $i=0;$i<$totalRs;$i++) {   
		      
			//$rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'],$detailCriteria); 
           
            $discount = $rs[$i]['finaldiscount'];
            $discountType = $rs[$i]['finaldiscounttype'];
            $subtotal =  $rs[$i]['subtotal'];

            $discountValue = ($discount != 0 && $discountType == 2) ? $discount/100 * $subtotal : $discount;  
            $rs[$i]['finaldiscount']= $discountValue;


            if($isShowDetail){ 
                
                if (!isset($rsDetailCol[$rs[$i]['pkey']])) continue;
                
                $rsDetail = $rsDetailCol[$rs[$i]['pkey']]; 
             
                for($j=0;$j<count($rsDetail);$j++){
                    if($rs[$i]['isfullreceive'] ==1)
                        $rsDetail[$j]['receivedqtyinbaseunit'] = $rsDetail[$j]['qty'];

                    $discount = $rsDetail[$j]['discount'];
                    $discountType = $rsDetail[$j]['discounttype'];
                    $priceInUnit = $rsDetail[$j]['priceinunit'];

                    $discountValue = ($discount != 0 && $discountType == 2) ? $discount/100 * $priceInUnit : $discount;  
                    $rsDetail[$j]['discount'] = $discountValue;

                    $receivedQty = $item->splitQtyBaseOnUnit($rsDetail[$j]['itemkey'], $rsDetail[$j]['receivedqtyinbaseunit']);
                    $rsDetail[$j]['receivedqty'] = $receivedQty;
                }

                $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 
                
            }
            
        
            $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

            // ===== FOR EXPORT SECTION 
            array_push($dataToExport, $return['data']);  
            // ===== END FOR EXPORT SECTION

            $tempreport .= $return['html'];
            $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
				
		}
		
		$obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}
else{ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
} 

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName.'.statuskey',1,true),'pkey','name');   
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
  

$arrTwigVar['inputHidItemKey'] =  $class->inputHidden('hidItemKey');
$arrTwigVar['inputPurchaseCode'] =  $class->inputText('purchaseCode');
$arrTwigVar['inputHidSupplierKey'] =  $class->inputHidden('hidSupplierKey');
$arrTwigVar['inputSupplierName'] =  $class->inputText('supplierName');
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;      
echo $twig->render('reportPurchaseOrder.html', $arrTwigVar);  
 
?>
