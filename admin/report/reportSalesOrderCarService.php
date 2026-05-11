<?php
	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj= $salesOrderCarService;
$securityObject = 'reportPurchaseOrder'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  

$_POST['selStatus[]'] = array(2,3);
  
$arrFilterInformation = array(); 
$detailCriteria = '';
    
$dataToExport = array();


/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"110px");
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"150px");
$arrDataStructure['policenumber'] = array('title'=>ucwords($obj->lang['car']),'dbfield' => 'policenumber', 'width'=>"80px");
$arrDataStructure['subtotal'] = array('title'=>ucwords($obj->lang['subtotal']),'dbfield' => 'subtotal','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['finalDiscount'] = array('title'=>ucwords($obj->lang['finalDiscount']),'dbfield' => 'finaldiscount','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['point'] = array('title'=>ucwords($obj->lang['point']),'dbfield' => 'pointvalue','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['tax'] = array('title'=>ucwords($obj->lang['tax']),'dbfield' => 'taxvalue','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['shipmentFee'] = array('title'=>ucwords($obj->lang['shippingFee']),'dbfield' => 'shipmentfee','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['etccost'] = array('title'=>ucwords($obj->lang['etccost']),'dbfield' => 'etccost','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'grandtotal','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['profit'] = array('title'=>ucwords($obj->lang['profit']),'dbfield' => 'profit','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['salesOrderReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
$arrDataDetailStructure = array(); 
$arrDataDetailStructure['itemCode'] = array('title'=>ucwords($obj->lang['itemCode']),  'dbfield' => 'itemcode', 'width'=>"100px" );  
$arrDataDetailStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname', 'width'=>"240px" );  
$arrDataDetailStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qty', 'width'=>"60px" , 'format' => 'number'); 
$arrDataDetailStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),  'dbfield' => 'unitname', 'width'=>"100px" );  
$arrDataDetailStructure['priceInUnit'] = array('title'=>ucwords($obj->lang['price']),'dbfield' => 'priceinunit', 'width'=>"100px",'format'=>'number');
$arrDataDetailStructure['hpp'] = array('title'=>"HPP",'dbfield' => 'costinbaseunit', 'width'=>"100px",'format'=>'number');
$arrDataDetailStructure['discount'] = array('title'=>ucwords($obj->lang['discount']),'dbfield' => 'discount', 'width'=>"100px",'format'=>'number');
$arrDataDetailStructure['profit'] = array('title'=>ucwords($obj->lang['profit'] .' @'),'dbfield' => 'profit', 'width'=>"100px",'format'=>'number');
$arrDataDetailStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'total', 'width'=>"100px",'format'=>'number');
$arrDataDetailStructure['profitTotal'] = array('title'=>ucwords($obj->lang['profit']),'dbfield' => 'profittotal', 'width'=>"100px",'format'=>'number');
$arrDataDetailStructure['salesName'] = array('title'=>ucwords($obj->lang['salesman']),  'dbfield' => 'salesname', 'width'=>"240px" );  
  
$arrDetailTemplate = array(); 
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['salesCode']));
	}
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
	if(isset($_POST) && !empty($_POST['customerName'])) {
		$criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'Pelanggan', 'filter' =>  $_POST['customerName']));
	} 
    
	if(isset($_POST) && !empty($_POST['carNumber'])) {
		$criteria .= ' AND '.$obj->tableName.'.policenumber LIKE  ('.$class->oDbCon->paramString('%'.$_POST['carNumber'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'No. Polisi', 'filter' =>  $_POST['carNumber']));
	} 
	
	 
	if(isset($_POST) && !empty($_POST['itemName'])) { 
        $detailCriteria .= ' AND '.$obj->tableItem.'.name  LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemName']));
	}
     
	/*if(isset($_POST) && !empty($_POST['selBrand'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selBrand']));   
        
        $detailCriteria .=  ' AND '.$obj->tableItem.'.brandkey in('.$key.')';  

        $rsCriteria =  $brand->searchData('','',true, ' and '.$brand->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$brandName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Brand', 'filter' => $brandName));
        
	}*/
    
    if(isset($_POST) && !empty($_POST['salesName'])) { 
        $detailCriteria .= ' AND '.$obj->tableEmployee.'.name  LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesName'].'%').')';
        array_push($arrFilterInformation,array("label" => 'Sales', 'filter' => $_POST['salesName'])); 
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
  
    $tempreport = '';
		
    if (empty($rs)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

	  for( $i=0;$i<count($rs);$i++) {  
        $arrHeaderStyle = array();  

        if ($rs[$i]['profit'] < 0)  
            $arrHeaderStyle['profit']['textColor'] = 'C41E3A'; 
        else if ($rs[$i]['profit'] > 0) 
            $arrHeaderStyle['profit']['textColor'] = '568203'; 
              
        $rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'],$detailCriteria); 
        if (empty($rsDetail))
            continue;
        
        $arrDetailStyle = array();
        for($j=0;$j<count($rsDetail);$j++){

            $rsDetail[$j]['profittotal'] = $rsDetail[$j]['qtyinbaseunit'] * $rsDetail[$j]['profit'];
            if ($rsDetail[$j]['profit'] < 0) { 
                $arrDetailStyle[$j]['profit']['textColor'] = 'C41E3A';
                $arrDetailStyle[$j]['profittotal']['textColor'] = 'C41E3A';
            }else if ($rsDetail[$j]['profit'] > 0){ 
                $arrDetailStyle[$j]['profit']['textColor'] = '568203'; 
                $arrDetailStyle[$j]['profittotal']['textColor'] = '568203';
            }
        }

        $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail, 'style' => $arrDetailStyle); 


        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle ),$arrTemplate); 

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
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true,' and isdriver = 1'),'pkey','name'); //gak tau klo teknisi apa  
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
  
   
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode'); 
$arrTwigVar['inputCarNumber'] =  $class->inputText('carNumber');   
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputItemName'] =  $class->inputText('itemName');  
$arrTwigVar['inputSalesName'] =  $class->inputText('salesName'); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

echo $twig->render('reportSalesOrderCarService.html', $arrTwigVar);  
 
?>
