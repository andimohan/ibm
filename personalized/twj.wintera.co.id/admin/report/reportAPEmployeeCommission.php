<?php

includeClass(array('AP.class.php','APPayment.class.php','APEmployeeCommission.class.php','APEmployeeCommissionPayment.class.php','Warehouse.class.php','Employee.class.php','Location.class.php','TruckingServiceWorkOrder.class.php'));
$apEmployeeCommission = createObjAndAddToCol( new APEmployeeCommission()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$employee = createObjAndAddToCol( new Employee());
$location = new Location();
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();

$obj= $apEmployeeCommission;
$apEmployeeCommissionPayment = $obj->getPaymentObj();
$securityObject = 'reportAPEmployeeCommission'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
if(!$security->isAdminLogin($securityObject,10,true));

$_POST['selStatus[]'] = array(1,2);

$arrFilterInformation = array();   

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['refcode2'] = array('title'=>ucwords($obj->lang['JOCode']), 'width'=>"90px", 'dbfield'=>'refcode2');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']), 'align'=>'center', 'width'=>"100px", 'dbfield'=>'trdate','format'=>'date');
$arrDataStructure['customername'] = array('title'=>ucwords($obj->lang['customerName']), 'width'=>"200px", 'dbfield'=>'customername');
$arrDataStructure['route'] = array('title'=>ucwords($obj->lang['route']), 'width'=>"200px", 'dbfield'=>'route');
$arrDataStructure['serviceName'] = array('title'=>ucwords($obj->lang['services']), 'width'=>"120px", 'dbfield'=>'servicename');
$arrDataStructure['container'] = array('title'=>ucwords($obj->lang['containerNumber']), 'width'=>"120px", 'dbfield'=>'container');
$arrDataStructure['employee'] = array('title'=>ucwords($obj->lang['employee']), 'width'=>"160px", 'dbfield'=>'employeename');
$arrDataStructure['uangjalan'] = array('title'=>ucwords('Uang Jalan'), 'width'=>"80px", 'dbfield'=>'uangjalan','format'=>'number', 'calculateTotal' => true, 'sortable' => false);
$arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['commission']), 'width'=>"80px",'format'=>'number', 'dbfield'=>'amount', 'calculateTotal' => true);
//$arrDataStructure['outstanding'] = array('title'=>ucwords($obj->lang['outstanding']), 'width'=>"100px",'format'=>'number', 'dbfield'=>'outstanding', 'calculateTotal' => true);
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']), 'width'=>"80px", 'dbfield'=>'statusname');

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['apEmployeeCommissionReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

$arrDataDetailStructure = array();
$arrDataDetailStructure['apcode'] = array('title'=>ucwords($obj->lang['apCode']),  'dbfield' => 'code', 'width'=>'100px', 'format' => 'string' ); 
$arrDataDetailStructure['appaymentdate'] = array('title'=>ucwords($obj->lang['date']),  'dbfield' => 'trdate', 'format' => 'date', 'width'=>'100px'); 
$arrDataDetailStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),  'dbfield' => 'amount', 'width'=>"100px", 'format' => 'number' , 'width'=>'50px','calculateTotal' => true);

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "400px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


/* search*/
if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
        
        $dateField =  ($_POST['selDateType'][0] == 1) ? 'trdate' : 'refdate';
		$criteria .= ' AND '.$obj->tableName.'.'.$dateField.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
		
        array_push($arrFilterInformation,array("label" => ($_POST['selDateType'][0] == 1) ? $obj->lang['transactionDate'] : $obj->lang['jobsDate'] , 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    //
/*	if(isset($_POST) && !empty($_POST['trRefStartDate'])){
		$criteria .= ' AND '.$obj->tableName.'.refdate between '.$class->oDbCon->paramDate( $_POST['trRefStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trRefEndDate'],' / ','Y-m-d 23:59'); 
		array_push($arrFilterInformation,array("label" => $obj->lang['jobsDate'], 'filter' => $_POST['trRefStartDate'] . ' - ' .$_POST['trRefEndDate'] ));
	}
    */
    
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND  '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['selEmployee'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selEmployee']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.employeekey in('.$key.')';  

        $rsCriteria =  $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$employeeName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Karyawan', 'filter' => $employeeName));
        
	}
    
    
    if(isset($_POST) && !empty($_POST['consigneeName'])) {
		$criteria .= ' AND '.$obj->tableConsignee.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['consigneeName'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'Consignee', 'filter' =>  $_POST['consigneeName']));
	}
    if(isset($_POST) && !empty($_POST['soCode'])) {
		$criteria .= ' AND '.$obj->tableSO.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['soCode'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'Kode Job Order', 'filter' =>  $_POST['soCode']));
	} 
    
    if(isset($_POST) && !empty($_POST['woCode'])) {
		$criteria .= ' AND '.$obj->tableWO.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['woCode'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'Kode SPK', 'filter' =>  $_POST['woCode']));
	}
    
    if(isset($_POST) && !empty($_POST['route'])) {
		$criteria .= ' AND '.$obj->tableName.'.route LIKE ('.$class->oDbCon->paramString('%'.$_POST['route'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'Rute', 'filter' =>  $_POST['route']));
	}
    
    if(isset($_POST) && !empty($_POST['container'])) {
		$criteria .= ' AND '.$obj->tableWO.'.containernumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['container'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'No. Container', 'filter' =>  $_POST['container']));
	}
    
    if(isset($_POST) && !empty($_POST['selLocation'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selLocation']));   
        
       	$criteria .= ' AND '.$obj->tableWO.'.locationkey in('.$key.')';  

        $rsCriteria =  $location->searchData('','',true, ' and '.$location->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$locationName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Lokasi', 'filter' => $locationName));
        
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
    
	if(isset($_POST) && !empty($_POST['chkDueDate'])){ 
        $criteria .= ' having datediff > 0';
        array_push($arrFilterInformation,array("label" => 'Aging', 'filter' => 'Tampilkan hanya yang jatuh tempo'));
    }
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
  
	$order = 'order by '.$orderBy.' ' .$orderType; 
	   
    $rs = $obj->searchData('','',true,$criteria,$order); 
	
	// ambil informasi uang jalan
	$arrWOKey = array_column($rs,'refkey'); // refkey => pkey spk
	$rsCostCol = $truckingServiceWorkOrder->getCostDetail($arrWOKey,8003); // 8003 => Uang Jalan
	$rsCostCol = $obj->reindexDetailCollections($rsCostCol,'refkey');  // bisa aj lebih dari 1, buat jaga2 // refkey => pkey spk
	
    $tempreport = '';  
	 
		for( $i=0;$i<count($rs);$i++) { 
            $arrHeaderStyle = array();
			
			$WOKey = $rs[$i]['refkey'];
			$rsCost = $rsCostCol[$WOKey];
			
			$totalUJ = 0;
			foreach($rsCost as $costRow)
				$totalUJ+=$costRow['amount'];

			$rs[$i]['uangjalan'] = $totalUJ;
				
            $rsPayment = $apEmployeeCommissionPayment->getDetailPaymentByAPKey($rs[$i]['pkey']);  
            if ($rs[$i]['datediff']  > 0 ){
                foreach($arrTemplate[0]['dataStructure'] as $key=>$el) 
                    if (isset($el['dbfield']))
                        $arrHeaderStyle[$el['dbfield']]['textColor'] = 'C41E3A';   
            }else{
                $arrHeaderStyle['outstanding']['textColor'] = '0093AF';  
            }

            $rsDetail = array();    /* detail per primary key */
            for ($j=0;$j<count($rsPayment);$j++){ 

                $rsApPayment= $apEmployeeCommissionPayment->getDataRowById($rsPayment[$j]['refkey']);

                $arrTemp = array();
                $arrTemp['code'] = $rsApPayment[0]['code'];
                $arrTemp['trdate'] = $rsApPayment[0]['trdate'];
                $arrTemp['amount'] = $rsPayment[$j]['amount']; 

                array_push($rsDetail, $arrTemp);
            }
            
            // has detail
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);        
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
    $_POST['selDateType'] = 2;
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrDateType = array('1' =>  $obj->lang['transactionDate'], '2' =>  $obj->lang['jobsDate']);
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrLocation = $class->convertForCombobox($location->searchData($location->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputHidEmployeeKey'] = $class->inputHidden('hidEmployeeKey');
$arrTwigVar['inputConsigneeName'] =  $class->inputText('consigneeName');
$arrTwigVar['inputSOCode'] =  $class->inputText('soCode');
$arrTwigVar['inputWOCode'] =  $class->inputText('woCode');
$arrTwigVar['inputContainer'] =  $class->inputText('container');
$arrTwigVar['inputRoute'] =  $class->inputText('route');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType ); 
$arrTwigVar['inputSelLocation'] =  $class->inputSelect('selLocation[]', $arrLocation, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelEmployee'] =  $class->inputSelect('selEmployee[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputChkDueDate'] =  $class->inputCheckBox('chkDueDate',array('overwritePost' => false, 'value' => 0, 'class' => 'no-class'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
      
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 

echo $twig->render('reportAPEmployeeCommission.html', $arrTwigVar);   
?>