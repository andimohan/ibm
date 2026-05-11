<?php
	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj = $truckingSellingRate;
$securityObject = 'ReportSellingRate'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  

$arrFilterInformation = array();
$detailCriteria = '';
$_POST['selStatus[]'] = array(1,2);

// ===== FOR EXPORT SECTION
$dataToExport = array();
/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"80px" );
$arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"250px" );
$arrDataStructure['cargoType'] = array('title'=>ucwords($obj->lang['cargoType']),'dbfield' => 'cargotypename', 'width'=>"100px" );
$arrDataStructure['category'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"200px" );
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px" );
$arrDataStructure['consignee'] = array('title'=>ucwords($obj->lang['consignee']),'dbfield' => 'consigneename', 'width'=>"250px" );
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"150px" );


$arrService = array();
$rsService = $truckingService->searchData($truckingService->tableName.'.statuskey',1,true);
foreach($rsService as $row => $value){
    
    $arrService[$value['pkey']] = array('label'=>$row);
    $arrDataStructure['itemprice'.$value['pkey']] = array('title'=> $value['name'] ,'align'=>'right', 'dbfield' => 'itemprice'.$value['pkey'],'format'=>'number', 'sortable' => false, 'width'=>"100px");

}//$obj->setLog($rsService,true);

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['sellingRateReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
  

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['sellingCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['sellingCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['sellingCode']));
	}
    
       
    if(isset($_POST) && !empty($_POST['name'])) { 
        $criteria .= ' AND '.$obj->tableName.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['name'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Nama', 'filter' => $_POST['name']));
	}
    
    if(isset($_POST) && !empty($_POST['selCategory'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCategory']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.categorykey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['category'], 'filter' => $statusName ));
        
	} 
    
    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['category'], 'filter' => $statusName ));
        
	} 
    
    if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $truckingServiceOrderCategory->searchData('','',true, ' and '.$truckingServiceOrderCategory->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['warehouse'], 'filter' => $statusName ));
        
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
		 
		 
	$orderBy = 'pkey';
	
	$orderType = 'asc';
		   
	$order = 'order by '.$orderBy.' ' .$orderType; 
	$rs = $obj->generateReportSellingRate($criteria,$order);
    
    
    $tempreport = '';
    $refkey = 0;
    $arrCost = array();
    for( $i=0;$i<count($rs);$i++)
        $arrCost[$rs[$i]['refkey']][$rs[$i]['itemkey']] = $rs[$i]['price'];

           
    for( $i=0;$i<count($rs);$i++) {   
        

        if($rs[$i]['refkey']==$refkey)
            continue;
        
//          if($i>10)
//              continue;
        
        foreach( $rsService as $key => $row) {
            $rs[$i]['itemprice'.$row['pkey']] = (isset($arrCost[$rs[$i]['refkey']][$row['pkey']])) ? $arrCost[$rs[$i]['refkey']][$row['pkey']] : 0;
        }
            
        $refkey = $rs[$i]['refkey'];

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
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');     
$arrCategory = $class->convertForCombobox($truckingServiceOrderCategory->searchData($truckingServiceOrderCategory->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name'); 
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name'); 


$arrTwigVar['inputSellingRateCode'] =  $class->inputText('sellingCode');
$arrTwigVar['inputName'] =  $class->inputText('name');
$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;    

echo $twig->render('reportTruckingSellingRate.html', $arrTwigVar);  
 
?>