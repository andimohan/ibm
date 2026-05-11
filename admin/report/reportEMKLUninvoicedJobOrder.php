<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';
include '_global.php';

includeClass(array('EMKLJobOrder.class.php','Container.class.php'.'Warehouse.class.php'));

$emklJobOrderExport = createObjAndAddToCol(new EMKLJobOrder());

$obj = $emklJobOrderExport;

$container = new Container();
$warehouse = new Warehouse();
$employee = new Employee();

$securityObject = 'reportSalesOrderExportFF'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  

$arrFilterInformation = array();
$detailCriteria = '';
$_POST['selStatus[]'] = array(2,3); 
   

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code');  
$arrDataStructure['trdate'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');
$arrDataStructure['etd'] = array('title'=>ucwords($obj->lang['etd']),'dbfield' => 'etdpol', 'width'=>"90px",'format'=>'date');
$arrDataStructure['containertype'] = array('title'=>ucwords($obj->lang['type']),'dbfield' => 'containertype', 'width'=>"80px");
$arrDataStructure['loadcontainertype'] = array('title'=>ucwords($obj->lang['jobType']),'dbfield' => 'jobtypeunion', 'width'=>"140px");
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['shipper']),'dbfield' => 'customername', 'width'=>"250px");
$arrDataStructure['salesman'] = array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'salesname', 'width'=>"100px");
$arrDataStructure['itemname'] = array('title'=>ucwords($obj->lang['service']),'dbfield' => 'itemname', 'width'=>"150px");
$arrDataStructure['currency'] = array('title'=>ucwords($obj->lang['curr']),'dbfield' => 'currency', 'width'=>"80px", 'align' => 'center');
$arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'subtotalcurrency','align'=>'right', 'width'=>"100px",'format'=>'number');
$arrDataStructure['amountidr'] = array('title'=>ucwords($obj->lang['amount']). ' IDR','dbfield' => 'subtotalidr','align'=>'right', 'width'=>"100px",'format'=>'number',  "sortable" => false, 'calculateTotal' => true );
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['uninvoicedSalesOrderReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
    
    if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['salesCode']));
	}
        
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tgl. Transaksi', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['warehouse'], 'filter' => $statusName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['customerName'])) { 
        $criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Pelanggan', 'filter' => $_POST['customerName']));
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
		 
    if(isset($_POST) && !empty($_POST['selContainer'])) { 
        
        $key = $_POST['selContainer'];  
       	$criteria .= ' AND '.$obj->tableName.'.containertypekey in ('.$class->oDbCon->paramString($key,',').')';  

        $rsCriteria = $container->getContainerType($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>$obj->lang['containerType'], 'filter' => $statusName ));
        
	} 

	if(isset($_POST) && !empty($_POST['selSales'])) { 
        
		$key = implode(",", $class->oDbCon->paramString($_POST['selSales']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.saleskey in('.$key.')';  

		$rsCriteria = $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>$obj->lang['sales'], 'filter' => $statusName ));
        
	} 

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'etdpol'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
      
	$order = 'order by '.$orderBy.' ' .$orderType; 
	$rs = $obj->generateUninvoicedReport($criteria,$order);
    $tempreport = '';
    
    
    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {
        

		switch ($rs[$i]['jobtypekey']) {
			case EMKL['jobType']['import']:
				$printUrl = '/admin/print/emklJobOrderImport/' . $rs[$i]['pkey'];
				break;
			case EMKL['jobType']['export']:
				$printUrl = '/admin/print/emklJobOrderExport/' . $rs[$i]['pkey'];
				break;
			case EMKL['jobType']['domestic']:
				$printUrl = '/admin/print/emklJobOrderDomestic/' . $rs[$i]['pkey'];
				break;
			default:
				$printUrl = '#';
				break;
		}
		$rs[$i]['code'] = '<a href="' . $printUrl . '" target="_blank">' . $rs[$i]['code'] . '</a>';
        
        $sokey = $rs[$i]['pkey'];
        $containertype = $rs[$i]['loadcontainertypekey'];
         
        $rs[$i]['subtotalidr'] = ($rs[$i]['detailcurrency'] == CURRENCY['idr']) ? $rs[$i]['subtotalcurrency']  :  $rs[$i]['subtotalcurrency'] * $rs[$i]['rate'];
        
        $arrHeaderStyle = array();
        //$arrHeaderStyle['grossprofit']['textColor'] = '568203';  
           
        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle),$arrTemplate);

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
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');  
$arrContainer = $class->convertForCombobox($container->getContainerType(),'pkey','name');   
$arrSales = $class->convertForCombobox($employee->searchData($employee->tableName . '.statuskey', 2, true, ' and ' . $employee->tableName . '.issales = 1 ', 'order by name asc'), 'pkey', 'name');   
    
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputHidCustomerKey'] =  $class->inputHidden('hidCustomerKey');
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputSelSales'] = $class->inputSelect('selSales[]', $arrSales, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelContainer'] =  $class->inputSelect('selContainer[]', $arrContainer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;    

echo $twig->render('reportEMKLUninvoicedJobOrder.html', $arrTwigVar);  
 
?>
