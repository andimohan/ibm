<?php
	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass('Voucher.class.php');
$voucher = createObjAndAddToCol( new Voucher());  

include '_global.php';

$obj= $voucher;
$securityObject = 'reportVoucher'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$arrStatus = $obj->getAllStatus();

$arrFilterInformation = array();     
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$_POST['module'] = IMPORT_TEMPLATE['voucher'];

switch($EXPORT_TYPE){
    case 2 :
           
            break;
        
    default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'createdon', 'width'=>"100px", 'format' =>'date', 'align' => 'center');
            $arrDataStructure['customercode'] = array('title'=>ucwords($obj->lang['customerCode']),'dbfield' => 'customercode', 'width'=>"150px");
            $arrDataStructure['customername'] = array('title'=>ucwords($obj->lang['customerName']),'dbfield' => 'customername', 'width'=>"250px");
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
}
  
$arrHeaderTemplate = array();  
$arrHeaderTemplate['reportTitle'] = $obj->lang['vouchereport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure; 
$arrHeaderTemplate['total'] = array();
 
array_push($arrTemplate, $arrHeaderTemplate);

// ===== END FOR EXPORT SECTION

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['customerCode'])) {
		$criteria .= ' AND '.$obj->tableCustomer.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode Pelanggan', 'filter' => $_POST['customerCode']));
	}
    
	if(isset($_POST) && !empty($_POST['customerName'])) {
		$criteria .= ' AND '.$obj->tableCustomer.'.name LIKE  ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Nama Pelanggan', 'filter' =>  $_POST['customerName']));
	} 
    
    if(isset($_POST) && !empty($_POST['trStartDate'])){
        
		$criteria .= ' and '.$obj->tableName.'.startdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59:59'); 
		array_push($arrFilterInformation,array("label" => $obj->lang['date'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
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
    
    // ============================= GENERATE DATA ============================= 
 
    for( $i=0;$i<count($rs);$i++) {      
  
       
        switch($EXPORT_TYPE){
            case 2 :  
                break;
            default :
 
        }
 
        
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate);
        
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']); 
        // ===== END FOR EXPORT SECTION
        
        $tempreport .= $return['html']; 
         
        // count subtotal for each col
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]); 
         
    }
		 
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

$arrStatus = $class->convertForCombobox($arrStatus,'pkey','status');   
//$arrCategory = $class->convertForCombobox($customerCategory->searchData($customerCategory->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['importUrl'] = $obj->importUrl; 
$arrTwigVar['inputCustomerCode'] =  $class->inputText('customerCode');  
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"')); 

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
echo $twig->render('reportVoucher.html', $arrTwigVar);  
 
?>