<?php
include '../../_config.php';
include '../../_include-v2.php';


includeClass(array('EMKLOrderInvoice.class.php','Warehouse.class.php','Currency.class.php'));
$emklOrderInvoice = new EMKLOrderInvoice();
$warehouse = new Warehouse();
$currency = new Currency();

include '_global.php';

$obj = $emklOrderInvoice; 
$securityObject = 'reportSalesOrderInvoiceFF'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true)); 


$arrFilterInformation = array(); 
$detailCriteria = '';
$_POST['selStatus[]'] = array(2,3);
if(!isset($_POST['isGrouping']))
    $_POST['isGrouping'] = 1;

// ====================== must be set before TWIG
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}   

$orderCriteria = array(); 
$orderCriteria['orderBy'] =  (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ?  $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate'; //$obj->tableName.'.
$orderCriteria['orderType'] = (isset ($_POST) && !empty($_POST['hidOrderType'])) ?   $_POST['hidOrderType'] : -1;
  
// ====================== must be set before TWIG 


$rsKey = $class->getTableKeyAndObj($obj->tableName);  
 
$customCodeInactiveCriteria = '';
     
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

// laporan gk boelh di report per detail karena ad komponen diskon di invoice, menyebabkan nilainya berbeda
//$isGrouping = (isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;

$rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1,true,'','order by name asc');
$isGrouping = true;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['jocode'] = array('title'=>ucwords($obj->lang['JOCode']),  'width'=>"150px", 'dbfield' => 'salesordercodecache'); 
$arrDataStructure['ajucode'] = array('title'=>ucwords($obj->lang['aju']),  'width'=>"150px", 'dbfield' => 'salesorderajucache'); 
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),  'width'=>"150px", 'dbfield' => 'warehousename'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px");
if (!$isGrouping){ 
// sofar $isGrouping selalu true
//    $arrDataStructure['socode'] = array('title'=>ucwords($obj->lang['soCode']),  'width'=>"150px", 'dbfield' => 'socode');
//    $arrDataStructure['qty'] = array('title'=>ucwords($obj->lang['party']),'dbfield' => 'qtyinbaseunit','align'=>'right', 'width'=>"60px",'format'=>'number'); 
//    $arrDataStructure['service'] = array('title'=>ucwords($obj->lang['service']),  'width'=>"200px", 'dbfield' => 'itemname');
//    $arrDataStructure['amount'] = array('title'=>ucwords($obj->lang['amount']),'dbfield' => 'total', 'width'=>"100px" ,'format'=>'number'); // ,'calculateTotal' => true harus dipisah IDR / USD

}else{
   $arrDataStructure['beforetax'] = array('title'=>ucwords($obj->lang['beforeTax']),'dbfield' => 'beforetaxtotal', 'width'=>"100px" ,'format'=>'number'); //, 'calculateTotal' => true karena kalo ad USD, sebelum pajaknya kita gk pisah dan MEMANG tdk dipisah, jadiny angkany akan salah
   $arrDataStructure['ppn'] = array('title'=>ucwords($obj->lang['PPN']),'dbfield' => 'taxvalue', 'width'=>"100px" ,'format'=>'number', 'calculateTotal' => true);
   
	  if(count($rsCurrency) == 1){
		  $arrDataStructure['othercost'] = array('title'=>ucwords($obj->lang['otherCost']),'dbfield' => 'othercost', 'width'=>"100px" ,'format'=>'number', 'calculateTotal' => true);
   		  $arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'grandtotal','align'=>'right', 'width'=>"110px",'format'=>'number', 'calculateTotal' => true);
	  }else{ 
        foreach($rsCurrency as $currRow){
      	  $arrDataStructure['othercost'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['otherCost']). ' ' .$currRow['name'], "sortable" => false,'dbfield' => 'othercost'.$currRow['pkey'], 'width'=>"120px" ,'format'=>'number', 'calculateTotal' => true);
   		  $arrDataStructure['total'.$currRow['pkey']] = array('title'=>ucwords($obj->lang['total']). ' ' .$currRow['name'], "sortable" => false,'dbfield' => 'grandtotal'.$currRow['pkey'],'align'=>'right', 'width'=>"110px",'format'=>'number', 'calculateTotal' => true);
	    }
    }
	
}
     
if($obj->isActiveModule('invoicetax'))    
     $arrDataStructure['invoiceTax'] = array('title'=>ucwords($obj->lang['invoiceTaxNumber']),  'dbfield' => 'invoicetaxnumber', 'width'=>"150px" );

$arrDataStructure['paymentto'] = array('title'=>ucwords($obj->lang['paymentTo']), 'width'=>"200px",'dbfield' => 'companybank');
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']), 'width'=>"300px",'dbfield' => 'trdesc');
$arrDataStructure['created'] = array('title'=>ucwords($obj->lang['createdBy']),'dbfield' => 'createdbyname', 'width'=>"200px");
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['salesInvoiceReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if ($isGrouping){ 
	if ($isShowDetail){ 
		// detail ...
		$arrDataDetailStructure = array(); 
		$arrDataDetailStructure['soCode'] = array('title'=>ucwords($obj->lang['soCode']),  'dbfield' => 'socode', 'width'=>"150px" );  
		$arrDataDetailStructure['soDate'] = array('title'=>ucwords($obj->lang['date']),  'dbfield' => 'sodate', 'width'=>"90px",'format'=>'date');
		$arrDataDetailStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'amount', 'width'=>"120px",'format'=>'number','calculateTotal' => true);
		$arrDataDetailStructure['description'] = array('title'=>ucwords($obj->lang['description']),'dbfield' => 'description', 'width'=>"300px");

		$arrDetailTemplate = array(); 
		$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
		$arrDetailTemplate['total'] = array();

		array_push($arrTemplate, $arrDetailTemplate); 
	}
}


$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status'); 
$arrCustomCode =  $class->convertForCombobox($customCode->searchData($customCode->tableName.'.reftabletype',$rsKey['key'],true,' and ('.$customCode->tableName.'.statuskey = 1 ' . $customCodeInactiveCriteria.')'),'pkey','name');  
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true,''),'pkey','name');   

$arrCurrency = $class->convertForCombobox($rsCurrency,'pkey','name');

$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputSelEmployee'] =  $class->inputSelect('selEmployee[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputInvoiceType'] =  $class->inputSelect('selInvoiceType[]', $arrCustomCode, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputsalesInvoiceCode'] =  $class->inputText('salesInvoiceCode'); 
$arrTwigVar['inputHidCutomerKey'] =  $class->inputHidden('hidCustomerKey');  
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName'); 
$arrTwigVar['inputIsGrouping'] =  $class->inputCheckBox('isGrouping');
$arrTwigVar['inputSelCurrency'] =  $class->inputSelect('selCurrency[]', $arrCurrency, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['order'] =  $orderCriteria;
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;     


if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';

	if(isset($_POST) && !empty($_POST['salesInvoiceCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesInvoiceCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['salesInvoiceCode']));
	}
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
	
	if(isset($_POST) && !empty($_POST['customerName'])) { 
        $criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').' )';
	    array_push($arrFilterInformation,array("label" => 'Customer', 'filter' => $_POST['customerName']));
	}
    
    if(isset($_POST) && !empty($_POST['selInvoiceType'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selInvoiceType']));   
        
       	$criteria .= ' AND customcodekey in('.$key.')';  
        
        $rsCriteria = $customCode->searchData($customCode->tableName.'.reftabletype',$rsKey['key'],true,' and ('.$customCode->tableName.'.statuskey = 1) and '.$customCode->tableName.'.pkey in ('.$key.')');  

        //$rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$invoiceType = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Type Invoice', 'filter' => $invoiceType ));
        
	}
  	 
  	 
    if(isset($_POST) && !empty($_POST['selEmployee'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selEmployee']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.createdby in('.$key.')';  

        $rsCriteria =  $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$createdName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['created'], 'filter' => $createdName));
        
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
    
    
    if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
		$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	   
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
        
	}
	
	    
    if(isset($_POST) && !empty($_POST['selCurrency'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCurrency']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.currencykey in('.$key.')';  

        $rsCriteria = $currency->searchData('','',true, ' and '.$currency->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Mata Uang', 'filter' => $statusName ));
        
	}	
	  
	$order = 'order by '.$orderCriteria['orderBy'].' ' . (($orderCriteria['orderType'] == 1) ? 'desc' : 'asc'); 
    $rs = (!$isGrouping) ? $obj->generateInvoiceReport($criteria,$order) :  $obj->searchData('','',true,$criteria,$order);
		 
    $tempreport = ''; 
		
    if (empty($rs)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
    
    if($isGrouping){ 
        $rsDetailCol = ($isShowDetail) ? $obj->getDetailCollections($rs,'refkey') : array(); 

        $rsAllItemDetail = $obj->getItemDetail(array_column($rs,'pkey'),'refheaderkey');
        $rsAllItemDetailCol = $obj->reindexDetailCollections($rsAllItemDetail,'refkey');
    }
    
     for( $i=0;$i<count($rs);$i++) {   
		$rs[$i]['salesordercodecache'] = implode('<br>',explode(' ',$rs[$i]['salesordercodecache']));
        if($isGrouping){ 
            
			if(count($rsCurrency) >= 1){
				foreach($rsCurrency as $currRow){
					$rs[$i]['othercost'.$currRow['pkey']] = 0;
					$rs[$i]['grandtotal'.$currRow['pkey']] = 0;

					$currencykey = $rs[$i]['currencykey'];
					$rs[$i]['othercost'.$currencykey] = $rs[$i]['othercost'];
					$rs[$i]['grandtotal'.$currencykey] = $rs[$i]['grandtotal'];
				}
			}
			
			if($isShowDetail){
				   $rsDetail = $rsDetailCol[$rs[$i]['pkey']]; 
 
		/*            if (empty($rsDetail))
						continue; */

					for($j=0;$j<count($rsDetail);$j++){
						$description = array(); 
						$rsInvoiceDetail = $rsAllItemDetailCol[$rsDetail[$j]['pkey']];

						if(!empty($rsDetail[$j]['description']))
							array_push($description, $rsDetail[$j]['description']); 

						for($k=0;$k<count($rsInvoiceDetail);$k++){
							if(empty($rsInvoiceDetail[$k]['itemname']))
								continue;

							$party = $obj->formatNumber($rsInvoiceDetail[$k]['qtyinbaseunit']).' x '.$rsInvoiceDetail[$k]['itemname'].' @'.$obj->formatNumber($rsInvoiceDetail[$k]['priceinunit']).' = '.$obj->formatNumber($rsInvoiceDetail[$k]['total']);
							array_push($description, $party);    
						}


						$rsDetail[$j]['description'] =  implode('<br>',$description);
						if(!empty($rsDetail[$j]['itemkey']) && empty($rsDetail[$j]['salesorderkey']))
							$rsDetail[$j]['socode'] = $rsDetail[$j]['itemname'];
					}


					// has detail
					$rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 
			}
           
        }
        
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

echo $twig->render('reportEMKLSalesOrderInvoice.html', $arrTwigVar);   

?>
