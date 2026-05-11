<?php
	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass('EMKLJobOrderHeader.class.php');
$emklJobOrderHeaderImport = createObjAndAddToCol(new EMKLJobOrderHeader(EMKL['jobType']['import']));

$container = createObjAndAddToCol(new Container());
$customer = createObjAndAddToCol(new Customer());
$warehouse = createObjAndAddToCol(new Warehouse());
$supplier = createObjAndAddToCol(new Supplier());
$vessel = createObjAndAddToCol(new Vessel());

include '_global.php';

$obj= $emklJobOrderHeaderImport;
$securityObject = 'reportEmklJobOrderHeaderImport'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 

$arrFilterInformation = array();    
$_POST['selStatus[]'] = array(2,3);

$arrDateType= array(
    '1' => $obj->lang['transactionDate'],
    '2' => 'ETD',
    '3' => 'ETA'
);


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$_POST['module'] = IMPORT_TEMPLATE['car'];
$arrDataStructure = array();

$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code' , 'width'=>"150px");
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px");
$arrDataStructure['type'] = array('title'=>ucwords($obj->lang['type']),'dbfield' => 'containertype', 'width'=>"80px");
$arrDataStructure['shipper'] = array('title'=>ucwords($obj->lang['shipper']),'dbfield' => 'customername','width'=>"200px");
$arrDataStructure['salesman'] = array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'salesname', 'width'=>"150px");
$arrDataStructure['pol'] = array('title'=>'POL','dbfield' => 'polname', 'width'=>"100px" );
$arrDataStructure['pod'] = array('title'=>'POD','dbfield' => 'podname', 'width'=>"100px");

$arrDataStructure['20'] = array('title'=>ucwords('20"'), 'width'=>"50px",'dbfield' =>'volume20', "sortable" => false,'calculateTotal' => true,'align'=>'right','format'=>'number');
$arrDataStructure['40'] = array('title'=>ucwords('40"'), 'width'=>"50px",'dbfield' =>'volume40', "sortable" => false,'calculateTotal' => true,'align'=>'right','format'=>'number');
$arrDataStructure['45'] = array('title'=>ucwords('45"'), 'width'=>"50px",'dbfield' =>'volume45', "sortable" => false,'calculateTotal' => true,'align'=>'right','format'=>'number');
$arrDataStructure['cbm'] = array('title'=>'CBM','dbfield' => 'volume', 'width'=>"100px", 'align' =>'right', 'format' => 'decimal','calculateTotal' => true );

$arrDataStructure['shippingLine'] = array('title'=>ucwords($obj->lang['shippingLine']),'dbfield' => 'carriername', 'width'=>"200px");
$arrDataStructure['blNumber'] = array('title'=>ucwords($obj->lang['blNumber']), 'width'=>"100px", 'dbfield' => 'bookingnumber', "sortable" => false);
$arrDataStructure['vessel'] = array('title'=>ucwords($obj->lang['vessel']),'dbfield' => 'vesselvoyage', 'width'=>"180px");
$arrDataStructure['etd'] = array('title'=>ucwords($obj->lang['etd']),'dbfield' => 'etdpol', 'width'=>"100px",'format'=>'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
$arrDataStructure['eta'] = array('title'=>ucwords($obj->lang['eta']),'dbfield' => 'etapod', 'width'=>"100px",'format'=>'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
$arrDataStructure['jobType'] = array('title'=>ucwords($obj->lang['jobType']),'dbfield' => 'emkltypename', 'width'=>"100px");

$arrDataStructure['trdate'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"100px",'format'=>'date');
$arrDataStructure['shipperPEB'] = array('title'=>ucwords($obj->lang['shipper']). ' (PEB)','width'=>"200px",'dbfield' => 'customerpebname');
//$arrDataStructure['consignee'] = array('title'=>ucwords($obj->lang['consignee']),'width'=>"100px", "sortable" => false);
//$arrDataStructure['destination'] = array('title'=>ucwords($obj->lang['destination']),'dbfield' => 'podname', 'width'=>"150px");
$arrDataStructure['closingDate'] = array('title'=>ucwords($obj->lang['closingDate']),'dbfield' => 'closingdate', 'width'=>"120px",'format'=>'datetime');
$arrDataStructure['stuffingIn'] = array('title'=>ucwords($obj->lang['stuffingIn']),'dbfield' => 'stuffingin', 'width'=>"100px",'format'=>'date');
$arrDataStructure['stuffingOut'] = array('title'=>ucwords($obj->lang['stuffingOut']),'dbfield' => 'stuffingout', 'width'=>"100px",'format'=>'date');

$arrDataStructure['aju'] = array('title'=>ucwords('AJU'),'dbfield' => 'aju', 'width'=>"100px");
$arrDataStructure['peb'] = array('title'=>ucwords('PEB'),'dbfield' => 'peb', 'width'=>"100px");
$arrDataStructure['pebDate'] = array('title'=>ucwords('Tgl PEB'), 'width'=>"100px", "sortable" => false);
$arrDataStructure['temperature'] = array('title'=>ucwords($obj->lang['temperature']),'dbfield' => 'temperature','align'=>'right', 'width'=>"80px",'format'=>'number'); 

$arrDataStructure['invoiceNumber'] = array('title'=>ucwords($obj->lang['invoiceNumber']),'dbfield' => 'invoicenumber', 'width'=>"100px"); 
$arrDataStructure['containerNumber'] = array('title'=>ucwords($obj->lang['containerNumber']),'dbfield' => 'container', 'width'=>"130px");
$arrDataStructure['sealNumber'] = array('title'=>ucwords($obj->lang['sealNumber']),'dbfield' => 'seal', 'width'=>"130px");
$arrDataStructure['loi'] = array('title'=>ucwords('Loi/Recoll'), 'width'=>"100px", "sortable" => false);
$arrDataStructure['vendor'] = array('title'=>ucwords($obj->lang['trucking']),'dbfield' => 'vendorname', 'width'=>"150px");
$arrDataStructure['verify'] = array('title'=>ucwords($obj->lang['verify']),'width'=>"50px", "sortable" => false);
$arrDataStructure['cashbon'] = array('title'=>ucwords('Kas Bon'),'width'=>"60px", "sortable" => false);
$arrDataStructure['rate'] = array('title'=>ucwords('Rate'),'width'=>"60px", "sortable" => false);
$arrDataStructure['pickUp'] = array('title'=>ucwords('Pick Up Full'),'width'=>"80px", "sortable" => false);
$arrDataStructure['stuffingLocation'] = array('title'=>$obj->lang['location'],'width'=>"250px", 'dbfield' => 'stuffing' );
$arrDataStructure['picutc'] = array('title'=>ucwords('PIC UTC'),'width'=>"80px", "sortable" => false);
$arrDataStructure['stackArea'] = array('title'=>ucwords($obj->lang['stackArea']),'dbfield' => 'terminalname', 'width'=>"150px");
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"250px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['jobOrderHeaderImportReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
	
if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';

	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => $obj->lang['code'], 'filter' => $_POST['code']));
	}
    
    if(isset($_POST) && !empty($_POST['trStartDate'])){
        
        switch($_POST['selDateType']){
            case '1' : $fieldName = $obj->tableName.'.trdate';  break;
            case '2' : $fieldName = $obj->tableName.'.etdpol'; break;
            case '3' : $fieldName = $obj->tableName.'.etapod'; break;
            default : $fieldName = $obj->tableName.'.trdate';  break;
                
        }
		$criteria .= ' and '.$fieldName.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => $arrDateType[$_POST['selDateType']], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
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
    
   if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['shipper'], 'filter' => $statusName ));
        
	}  
    
    
    if(isset($_POST) && !empty($_POST['selCarrier'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCarrier']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.carrierkey in('.$key.')';  

        $rsCriteria = $supplier->searchData('','',true, ' and '.$supplier->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['shippingLine'], 'filter' => $statusName ));
        
	}  
    
    if(isset($_POST) && !empty($_POST['selVessel'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selVessel']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.vesselkey in('.$key.')';  

        $rsCriteria = $vessel->searchData('','',true, ' and '.$vessel->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['vessel'], 'filter' => $statusName ));
        
	}  
    
    if(isset($_POST) && !empty($_POST['vesselNumber'])) {  
        
       	$criteria .= ' AND '.$obj->tableName.'.vesselnumber like ('.$class->oDbCon->paramString('%'.$_POST['vesselNumber'].'%').')';   
	    array_push($arrFilterInformation,array("label" => $obj->lang['voyage'], 'filter' => $_POST['vesselNumber']));
        
	}  
    
    if(isset($_POST) && !empty($_POST['selType'])) { 
        
        $key = $_POST['selType'];   
        
       	$criteria .= ' AND '.$obj->tableName.'.loadcontainertypekey in('.$class->oDbCon->paramString($key,',').')';  

        $rsCriteria = $obj->getEmklType($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['jobType'], 'filter' => $statusName ));
        
	} 
    
    if(isset($_POST) && !empty($_POST['selContainer'])) { 
        
        $key = $_POST['selContainer']; 
       	$criteria .= ' AND '.$obj->tableName.'.containertypekey in ('.$class->oDbCon->paramString($key,',').')';  

        $rsCriteria = $container->getContainerType($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['containerType'], 'filter' => $statusName ));
        
	} 
    if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['status'], 'filter' => $statusName));
        
	}  
	 
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	$order = 'order by '.$orderBy.' ' .$orderType; 
      
    // select data
    $rs = $obj->searchData('','',true,$criteria,$order);
    $tempreport = ''; 
    $container = new Container();
    $rsContainer = $container->searchDataRow(array($container->tableName.'.pkey' ,$container->tableName.'.volume'));
    $rsContainer = array_column($rsContainer,null,'pkey');
    
    $totalRows = count($rs);
    for( $i=0;$i<$totalRows;$i++) {
        
        $rs[$i]['volume20'] = 0;
        $rs[$i]['volume40'] = 0;
        $rs[$i]['volume45'] = 0;
         
        if( in_array( $rs[$i]['loadcontainertypekey'] , array(EMKL['emklType']['lcl'],EMKL['emklType']['lclnc'])) ){ 
            $vol= $rsContainer[$rs[$i]['itemkey']]['volume'];
            
            if ($vol == 20) $rs[$i]['volume20'] = 1;
            elseif ($vol == 40) $rs[$i]['volume40'] = 1;
            elseif ($vol == 45) $rs[$i]['volume45'] = 1;
             
        }else if($rs[$i]['loadcontainertypekey']==EMKL['emklType']['fcl'] || $rs[$i]['loadcontainertypekey']==EMKL['emklType']['trucking']){
            $rsVolumeDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey']);
             
            foreach($rsVolumeDetail as $row){
                 if ($row['volume'] == 20) $rs[$i]['volume20'] += $row['qty'] ;
                 elseif ($row['volume'] == 40) $rs[$i]['volume40'] += $row['qty'] ;
                 elseif ($row['volume'] == 45) $rs[$i]['volume45'] += $row['qty'] ;
            }
            
        }
        //$obj->setLog($volume,true);
        
        if(!empty($rs[$i]['containernumber'])){ 
            $arrContainerNumber = array();
            $arrSealNumber = array(); 
            $arrContainerSealNumber = explode(chr(13) , $rs[$i]['containernumber']);
            for($j=0;$j<count($arrContainerSealNumber);$j++){
                if(empty($arrContainerSealNumber[$j])) continue;

                $arrContainerSealNumberRow = explode('/' , $arrContainerSealNumber[$j]);
                array_push($arrContainerNumber,$arrContainerSealNumberRow[0]);
                array_push($arrSealNumber, isset($arrContainerSealNumberRow[1]) ? $arrContainerSealNumberRow[1] : '');
             
            }
            $rs[$i]['container'] = implode(', ',$arrContainerNumber);
            $rs[$i]['seal'] = implode(', ',$arrSealNumber);
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
else {
    $_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
$arrContainer = $class->convertForCombobox($container->getContainerType(),'pkey','name');
$arrType = $class->convertForCombobox($obj->getEmklType(),'pkey','name'); 
$arrShippingLine = $class->convertForCombobox($supplier->searchData($supplier->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name'); 
$arrVessel = $class->convertForCombobox($vessel->searchData($vessel->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name'); 

$arrTwigVar['inputCode'] =  $class->inputText('code'); 
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelContainer'] =  $class->inputSelect('selContainer[]', $arrContainer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);  
$arrTwigVar['inputSelType'] =  $class->inputSelect('selType[]', $arrType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelCarrier'] =  $class->inputSelect('selCarrier[]', $arrShippingLine, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelVessel'] =  $class->inputSelect('selVessel[]', $arrVessel, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputVesselNumber'] =  $class->inputText('vesselNumber');  
     
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   

echo $twig->render('reportEMKLJobOrderImportHeader.html', $arrTwigVar);  
    
?>