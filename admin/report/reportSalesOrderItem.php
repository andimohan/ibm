<?php
	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass(array('SalesOrder.class.php','Warehouse.class.php', 'Employee.class.php', 'Brand.class.php'));
$salesOrder = createObjAndAddToCol( new SalesOrder()); 
$warehouse= createObjAndAddToCol( new Warehouse()); 
$employee= createObjAndAddToCol( new Employee()); 
$brand= createObjAndAddToCol( new Brand()); 

include '_global.php';

$obj= $salesOrder;
$securityObject = 'reportSalesOrder'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
//$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  

$_POST['selStatus[]'] = array(2,3);

$arrGroupBy = array(
    array('key' => 'item', 'groupby' => 'itemkey','label' => $obj->lang['item'],'arrColumn' => array( 'itemCode' =>  array('title'=>ucwords($obj->lang['code']),'dbfield' => 'itemcode', 'width'=>"100px"),
                                                                                  'itemName' => array('title'=>ucwords($obj->lang['item']),'dbfield' => 'itemname', 'width'=>"300px")
                                                                                )),
    array('key' => 'customer', 'groupby' => 'customerkey','label' => $obj->lang['customer'],'arrColumn' => array( 'customerCode' =>  array('title'=>ucwords($obj->lang['code']),'dbfield' => 'customercode', 'width'=>"100px"),
                                                                                          'customerName' => array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"300px"),
                                                                                )),
    array('key' => 'brand', 'groupby' => 'brandkey','label' => $obj->lang['brand'],'arrColumn' => array( 'brandCode' =>  array('title'=>ucwords($obj->lang['code']),'dbfield' => 'brandcode', 'width'=>"100px"),
                                                                                                         'brandName' => array('title'=>ucwords($obj->lang['brand']),'dbfield' => 'brandname', 'width'=>"300px"),
                                                                                                    )),
    array('key' => 'salesman', 'groupby' => 'salesordersaleskey','label' => $obj->lang['salesman'],'arrColumn' => array( 'salesCode' =>  array('title'=>ucwords($obj->lang['code']),'dbfield' => 'salesmancode', 'width'=>"100px"),
                                                                                                         'salesNaame' => array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'salesmanname', 'width'=>"300px"),
                                                                                                    ))
          
);

$arrFilterInformation = array(); 
$detailCriteria = '';

$dataToExport = array();

/* data structure */
$arrTemplate = array();
//$isGroupByItem = (isset($_POST['isGroupByItem']) && !empty($_POST['isGroupByItem'])) ? true : false;

$groupOpt = array(); 
$selGroupBy = (isset($_POST['selGroupBy']) && !empty($_POST['selGroupBy'])) ? $_POST['selGroupBy'] : '';
if(!empty($selGroupBy)){
    $arrTempGroup = array_column($arrGroupBy,null,'key');
    if(isset($arrTempGroup[$selGroupBy])) 
        $groupOpt = $arrTempGroup[$selGroupBy]; 
}

$hasGroup = (!empty($selGroupBy)) ? true : false;

// ====================== must be set before TWIG
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
} 

$orderCriteria = array(); 
$orderCriteria['orderBy'] =  (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ?  $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'itemcode'; //$obj->tableName.'.
$orderCriteria['orderType'] = (isset ($_POST) && !empty($_POST['hidOrderType'])) ?   $_POST['hidOrderType'] : -1;
// ====================== must be set before TWIG


$arrDataStructure = array();
if($hasGroup){
    $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
    
    foreach($groupOpt['arrColumn'] as $key=>$row) 
         $arrDataStructure[$key] = $row;
        
    /*$arrDataStructure['itemCode'] = array('title'=>ucwords($obj->lang['itemCode']),'dbfield' => 'itemcode', 'width'=>"100px");
    $arrDataStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),'dbfield' => 'itemname', 'width'=>"300px");*/
    
    $arrDataStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),'dbfield' => 'totalqty','align'=>'right', 'width'=>"70px",'format'=>'number','calculateTotal' => true); 
    //$arrDataStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),'dbfield' => 'unitname','align'=>'left', 'width'=>"100px"); 
    $arrDataStructure['qtyAvarageMonth'] = array('title'=>ucwords($obj->lang['avg']),'dbfield' => 'qtyavaragemonth','align'=>'right', 'width'=>"70px",'format'=>'number',"sortable" => false); 
    $arrDataStructure['unitAvarage'] = array('title'=>ucwords($obj->lang['unit']),'dbfield' => 'unitname','align'=>'left', 'width'=>"100px","sortable" => false); 
    $arrDataStructure['totalSales'] = array('title'=>ucwords($obj->lang['totalSales']),'dbfield' => 'totalsales','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
}else{
    $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
    $arrDataStructure['itemCode'] = array('title'=>ucwords($obj->lang['itemCode']),'dbfield' => 'itemcode', 'width'=>"100px");
    $arrDataStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),'dbfield' => 'itemname', 'width'=>"300px");
    $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['soCode']),  'width'=>"100px", 'dbfield' => 'code'); 
    $arrDataStructure['refcode'] = array('title'=>ucwords($obj->lang['refCode']),  'width'=>"130px", 'dbfield' => 'refcode'); 
    $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"120px",'format'=>'date');
    $arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"110px");
    $arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"150px");
    $arrDataStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),'dbfield' => 'qty','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
    $arrDataStructure['unit'] = array('title'=>ucwords($obj->lang['unit']),'dbfield' => 'unitname','align'=>'left', 'width'=>"100px"); 
    $arrDataStructure['priceInUnit'] = array('title'=>ucwords($obj->lang['price']),'dbfield' => 'priceinunit','align'=>'right', 'width'=>"100px",'format'=>'number'); 
    $arrDataStructure['discount'] = array('title'=>ucwords($obj->lang['discount']),'dbfield' => 'discount','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
    $arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'totalafterheaderdisc','align'=>'right', 'width'=>"100px",'format'=>'number','calculateTotal' => true); 
    $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");
}
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['salesOrderByGroupReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate); 

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true,' and '.$employee->tableName.'.issales = 1 '),'pkey','name');   
$arrBrand = $class->convertForCombobox($brand->searchData($brand->tableName.'.statuskey',1,true),'pkey','name');      
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true),'pkey','name');   

 
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');  
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSalesName'] =  $class->inputSelect('selSales[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputBrandName'] =  $class->inputSelect('selBrand[]', $arrBrand, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputIsGroupByItem'] =  $class->inputCheckBox('isGroupByItem');

$comboGroupBy = $class->convertForCombobox($arrGroupBy,'key','label');  
$arrTwigVar['inputGroupBy'] =  $class->inputSelect('selGroupBy', $comboGroupBy); 

$arrTwigVar['order'] =  $orderCriteria;
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['salesCode']));
	}
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
    
        $dateDiff = '';
        if($hasGroup){ 
            $dateDiff = 'PERIOD_DIFF(DATE_FORMAT('.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ').',"%Y%m"),DATE_FORMAT('.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').',"%Y%m")) as datediff,';
        }
        		
        array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
	if($hasGroup) {
		array_push($arrFilterInformation,array("label" => 'Group', 'filter' =>  $groupOpt['label']));
	} 
    
  
	if(isset($_POST) && !empty($_POST['customerName'])) {
		$criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'Pelanggan', 'filter' =>  $_POST['customerName']));
	} 
	 
	if(isset($_POST) && !empty($_POST['itemName'])) { 
        $criteria .= ' AND '.$obj->tableItem.'.name  LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemName']));
	}
     
	if(isset($_POST) && !empty($_POST['selBrand'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selBrand']));   
        
        $criteria .=  ' AND '.$obj->tableItem.'.brandkey in('.$key.')';  

        $rsCriteria =  $brand->searchData('','',true, ' and '.$brand->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$brandName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Brand', 'filter' => $brandName));
        
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
    
    if(isset($_POST) && !empty($_POST['selSales'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSales']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.saleskey in('.$key.')';  

        $rsCriteria =  $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$salesName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Sales', 'filter' => $salesName));
        
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

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'itemcode'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
    //$groupBy = ($isGroupByItem) ? 'group by itemcode' : '';
    $groupBy = ($hasGroup) ? 'group by ' . $groupOpt['groupby'] : '';
    
	$order = 'order by '.$orderBy.' ' .$orderType; 
	$rs = $obj->generateSalesOrderItem($criteria,$order,$groupBy,$dateDiff);
		
    // khusus grouping
    /*if($isGroupByItem){
        $rsMonthly = $obj->generateSalesOrderItem($criteria,$order,' group by itemcode, YEAR('.$obj->tableName.'.trdate) , MONTH('.$obj->tableName.'.trdate) ');
    }*/
    
    $tempreport = '';

    if (empty($rs)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

    for( $i=0;$i<count($rs);$i++) {   
                  
        if($hasGroup){ 
            $diffDate = ($rs[$i]['datediff'] > 0) ? $rs[$i]['datediff'] : 1;
            $totalQtyMonth = $rs[$i]['totalqty'] / $diffDate;
            $rs[$i]['qtyavaragemonth'] = $totalQtyMonth; 
        }        
        
        $discount = $rs[$i]['discount'];
        $discountType = $rs[$i]['discounttype'];
        $priceInUnit = $rs[$i]['priceinunit'];

        $discountValue = ($discount != 0 && $discountType == 2) ? $discount/100 * $priceInUnit : $discount;  
		
		$headerDiscValue = ($rs[$i]['headerdiscvalueinunit']*$rs[$i]['qty']);
        $rs[$i]['discount'] = $discountValue + $headerDiscValue ;
			
        $return = $obj->formatReportRows(array('data' => $rs[$i] ),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }
    

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
	$obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);

}

echo $twig->render('reportSalesOrderItem.html', $arrTwigVar);  
 
?>