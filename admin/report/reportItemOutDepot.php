<?php
include '../../_config.php';
include '../../_include.php';
include '_global.php';
  
$obj = $itemOutDepot;
$securityObject = 'reportItemOutDepot'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  

$arrFilterInformation = array();
$detailCriteria = '';
$_POST['selStatus[]'] = array(2,3);


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"150px",'align' => 'center','format'=>'datetime');
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"250px" );
$arrDataStructure['doCode'] = array('title'=>ucwords($obj->lang['deliveryNotes']),'dbfield' => 'docode', 'width'=>"120px" );
$arrDataStructure['policeNumber'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),'dbfield' => 'policenumber', 'width'=>"120px" );
$arrDataStructure['depot'] = array('title'=>ucwords($obj->lang['depot']),'dbfield' => 'depotname', 'width'=>"150px" );
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"350px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px" );
		  
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['itemOutReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
$arrDataDetailStructure = array();
$arrDataDetailStructure['itemcode'] = array('title'=>ucwords($obj->lang['itemCode']),  'dbfield' => 'itemcode', 'width'=>'100px' ); 
$arrDataDetailStructure['itemname'] = array('title'=>ucwords($obj->lang['itemName']),  'dbfield' => 'itemname', 'width'=>'280px','mergeExcelCell' => 2); 
$arrDataDetailStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),  'dbfield' => 'qty', 'width'=>"60px", 'format' => 'number','calculateTotal' => true ); 
$arrDataDetailStructure['unit'] = array('title'=> '',  'dbfield' => 'unitname', 'width'=>"60px" ,  'textColor' => '999999', 'style' => 'padding-left:0px;'); 
$arrDataDetailStructure['totalWeight'] = array('title'=>ucwords($obj->lang['totalWeight']),  'dbfield' => 'totalweight', 'width'=>"70px", 'format' => 'decimal' ,'calculateTotal' => true); 
$arrDataDetailStructure['weightUnit'] = array('title'=>'',  'dbfield' => 'weightunitname', 'width'=>"50px"  ,  'textColor' => '999999', 'style' => 'padding-left:0px;'); 
$arrDataDetailStructure['totalVolume'] = array('title'=>ucwords($obj->lang['totalVolume']),  'dbfield' => 'totalvolume', 'width'=>"80px", 'format' => 'decimal','calculateTotal' => true ); 
$arrDataDetailStructure['volumeUnit'] = array('title'=>'',  'dbfield' => 'volumeunitname', 'width'=>"50px" , 'textColor' => '999999', 'style' => 'padding-left:0px;');

$arrDetailTemplate = array();
$arrDetailTemplate['reportWidth'] = "500px";
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';
	if(isset($_POST) && !empty($_POST['code'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['code']));
	}
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'] . ' 23:59:59',' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
	if(isset($_POST) && !empty($_POST['itemName'])) { 
        $detailCriteria .= ' AND '.$obj->tableItem.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemName']));
	}
    if(isset($_POST) && !empty($_POST['customerName'])) { 
        $criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Customer', 'filter' => $_POST['customerName']));
	}
    if(isset($_POST) && !empty($_POST['doCode'])) { 
        $criteria .= ' AND '.$obj->tableName.'.docode LIKE ('.$class->oDbCon->paramString('%'.$_POST['doCode'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Surat Jalan', 'filter' => $_POST['doCode']));
	}
    if(isset($_POST) && !empty($_POST['policeNumber'])) { 
        $criteria .= ' AND '.$obj->tableName.'.policenumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['policeNumber'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'No.Polisi', 'filter' => $_POST['policeNumber']));
	}
	 
	if(isset($_POST) && !empty($_POST['selDepot'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selDepot']));   
        
       	$criteria .= ' AND depotkey in('.$key.')';  

        $rsCriteria = $depot->searchData('','',true, ' and '.$depot->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$depotName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Depot', 'filter' => $depotName ));
        
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
	 
		for( $i=0;$i<count($rs);$i++) {   
			
            $rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'],$detailCriteria); 
            if (empty($rsDetail))
                continue;
                    
            for ($j=0;$j<count($rsDetail);$j++){    
                $rsDetail[$j]['volumeunitname'] = 'CM<sup>3</sup>';
            }
            
            // has detail
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);
                  
            $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 
            
            // ===== FOR EXPORT SECTION 
            array_push($dataToExport, $return['data']);  
            // ===== END FOR EXPORT SECTION
            
            $tempreport .= $return['html'];  
            
		}

        $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);


}else{ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
} 

$arrDepot = $class->convertForCombobox($depot->searchData($depot->tableName.'.statuskey',1,true,' and isprivate = 1','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');

$arrTwigVar['inputCode'] =  $class->inputText('code'); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputSelDepot'] =  $class->inputSelect('selDepot[]', $arrDepot, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputHidItemKey'] = $class->inputHidden('hidItemKey');
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName'); 
$arrTwigVar['inputDeliveryNotes'] =  $class->inputText('doCode'); 
$arrTwigVar['inputPoliceNumber'] =  $class->inputText('policeNumber');
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  

echo $twig->render('reportItemOutDepot.html', $arrTwigVar);   

?>
