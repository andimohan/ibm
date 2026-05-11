<?php
	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj= $truckingServiceWorkOrder;
$securityObject = 'reportTruckingServiceWorkOrder'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));   
   
$arrFilterInformation = array(); 
//$detailCriteria = 'and refcashoutkey is not null and requestamount is not null';
$_POST['selStatus[]'] = array(2,3);  

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array(); 
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date'); 
$arrDataStructure['jobOrderCode'] = array('title'=>ucwords($obj->lang['jobOrderCode']),'dbfield' => 'serviceordercode', 'width'=>"120px" ); 
$arrDataStructure['si'] = array('title'=>ucwords($obj->lang['si']),'dbfield' => 'donumber', 'width'=>"120px"); 
$arrDataStructure['consignee'] = array('title'=>ucwords($obj->lang['consignee']),'dbfield' => 'consigneename', 'width'=>"180px"); 
$arrDataStructure['tl'] = array('title'=>'TL','dbfield' => 'TL', 'width'=>"50px",'align'=>'center'); 
$arrDataStructure['outsourceCost'] = array('title'=>ucwords($obj->lang['outsourceFee']),'dbfield' => 'outsourcecost', 'width'=>"100px",'format'=>'number','sortable' => false,'calculateTotal' => true, 'textColor' => '0093AF');  
$arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['costAmount']),'dbfield' => 'total','align'=>'right', 'width'=>"90px",'sortable' => false,'format'=>'number','calculateTotal' => true);
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"80px");

  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['workOrderCostReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


if (!isset($_POST['trStartDateTime']) || empty($_POST['trStartDateTime'])){ 
   	$_POST['trStartDateTime'] = date('d / m / Y');
	$_POST['trEndDateTime'] = date('d / m / Y'); 
}


$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputWorkOrderCode'] =  $class->inputText('workOrderCode');
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');  
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDateTime'] = $class->inputDate('trStartDateTime', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDateTime'] = $class->inputDate('trEndDateTime', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputChkIsOutsource'] =  $class->inputCheckBox('chkIsOutsource',array('value' => 1, 'overwritePost' => false, 'class' => 'no-class'));  
$arrTwigVar['inputDONumber'] =  $class->inputText('doNumber'); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   

 
if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['workOrderCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['workOrderCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['workOrderCode']));
	}
    
	if(isset($_POST) && !empty($_POST['trStartDateTime'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDateTime'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDateTime'] . ' 23:59:00',' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal SPK', 'filter' => $_POST['trStartDateTime'] . ' - ' .$_POST['trEndDateTime'] ));
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
    
    
	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableServiceOrderHeader.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['salesCode']));
	}
    
    	   
	if(isset($_POST) && !empty($_POST['doNumber'])) {
		$criteria .= ' AND '.$obj->tableServiceOrderHeader.'.donumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['doNumber'].'%').')';
	 	array_push($arrFilterInformation,array("label" => 'DO Pelanggan', 'filter' =>  $_POST['doNumber']));
	} 
   
    if(isset($_POST)){
        
        if( empty($_POST['chkIsOutsource'])) {
            $criteria .= ' AND  isoutsource = 0';   
            array_push($arrFilterInformation,array("label" => 'Trucking Outsource', 'filter' => 'Tidak')); 
        }else{
            array_push($arrFilterInformation,array("label" => 'Trucking Outsource', 'filter' => 'Ya'));  
        }
      
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
   
    // harus looping dulu untuk tau total cost yg kepake apa saja 
      
  
    $arrTemp = array();
    for( $i=0;$i<count($rs);$i++) {   
        
        $total = 0;
        
        //outsourcefee 
        if ($rs[$i]['outsourcecost'] != 0){    
            $total += $rs[$i]['outsourcecost'];
        } 
        
        $rsDetail = $obj->getCostDetail($rs[$i]['pkey']);

        for($j=0;$j<count($rsDetail);$j++) { 
            $total += $rsDetail[$j]['amount'];

            $arrIndex = 'cost'.$rsDetail[$j]['costkey']; 
            $rs[$i][$arrIndex] = (!isset($rs[$i][$arrIndex])) ? $rsDetail[$j]['amount'] : $rs[$i][$arrIndex] + $rsDetail[$j]['amount'];

            if (!isset($arrTemp[$arrIndex]))  
                $arrTemp[$arrIndex] = array('title'=>$rsDetail[$j]['name'],'dbfield' => $arrIndex, 'width'=>"150px",'format'=>'number','sortable' => false,'calculateTotal' => true, 'textColor' => '0093AF');  
        
        }
         
        //$arrDataStructure['outsourceFee'] = array('title'=>ucwords($obj->lang['outsoruceFee']),'dbfield' => 'outsourcefee', 'width'=>"100px",'format'=>'number',); 
         
        $rs[$i]['total'] = $total;
    }
  
    $arrDataStructure = array_slice($arrDataStructure, 0, 8, true) +  $arrTemp + array_slice($arrDataStructure, 8, count($arrDataStructure)-8, true);
     
    foreach($arrDataStructure as $key => &$row){
        if (isset($row['dbfield']) && $row['dbfield'] == $orderBy){ 
            $row['orderType'] = $_POST['hidOrderType'] * -1;
            $row['sortableActive'] = true;
        }
    } 
    unset($row);
     
    /* data structure */ 
    $arrHeaderTemplate['dataStructure'] = $arrDataStructure; 

    $arrTemplate = array();
    array_push($arrTemplate, $arrHeaderTemplate);

    // overwrite
    $arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
    if (empty($rs)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

    
    // ======================================== add cost field 
 
    
    for( $i=0;$i<count($rs);$i++) {    
        
            $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 
            
            // ===== FOR EXPORT SECTION 
            array_push($dataToExport, $return['data']);  
            // ===== END FOR EXPORT SECTION
            
            $tempreport .= $return['html'];
            $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
            
    }  
    
    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
}
 
 
echo $twig->render('reportTruckingServiceWorkOrderCost.html', $arrTwigVar);  
 
?>
