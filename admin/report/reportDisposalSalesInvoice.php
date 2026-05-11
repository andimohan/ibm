<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('DisposalSalesInvoice.class.php');
$disposalSalesInvoice = createObjAndAddToCol(new DisposalSalesInvoice()); 
$customer = createObjAndAddToCol(new Customer()); 

include '_global.php';

$obj = $disposalSalesInvoice; 
$securityObject = 'reportDisposalSalesInvoice'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true)); 


$arrDateType= array(
    '1' => $obj->lang['invoiceDate'], 
    //'2' => $obj->lang['lastAct'], // khusus ETI saja
);

$arrFilterInformation = array(); 
$_POST['selStatus[]'] = array(2,3);

// ====================== must be set before TWIG
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}   

$orderCriteria = array(); 
$orderCriteria['orderBy'] =  (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ?  $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate'; //$obj->tableName.'.
$orderCriteria['orderType'] = (isset ($_POST) && !empty($_POST['hidOrderType'])) ?   $_POST['hidOrderType'] : -1; 
// ====================== must be set before TWIG 


$rsKey = $obj->getTableKeyAndObj($obj->tableName, array('key'));
 
$customCodeInactiveCriteria = '';
     
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;


// laporan gk boleh di report per detail karena ad komponen diskon di invoice, menyebabkan nilainya berbeda

$isGrouping =  true; //(isset($_POST['isGrouping']) && !empty($_POST['isGrouping'])) ? true : false;

                    
$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');
$arrDataStructure['salesordercodecache'] = array('title'=>ucwords($obj->lang['jobOrderCode']), 'width'=>"150px",'dbfield' => 'salesordercodecache', "sortable" => false); 

if(!$isGrouping){ 
    $arrDataStructure['salesorderdate'] = array('title'=>ucwords($obj->lang['jobOrderDate']), 'width'=>"100px",'dbfield' => 'sodate',  "format" => "date");  
}

$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px"); 

// total harus tetep ad meskipun grouping
$arrDataStructure['dpp'] = array('title'=>ucwords($obj->lang['dpp']),'dbfield' => 'subtotal','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['tax'] = array('title'=>ucwords($obj->lang['tax']),'dbfield' => 'taxvalue','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'grandtotal','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['tax23'] = array('title'=>ucwords($obj->lang['tax23']),'dbfield' => 'tax23value','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 

$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']), 'width'=>"300px",'dbfield' => 'trdesc');
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['serviceOrderInvoiceReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if ($isGrouping){ 
    if ($isShowDetail) {
    // detail ...
    $arrDataDetailStructure = array(); 
    $arrDataDetailStructure['soCode'] = array('title'=>ucwords($obj->lang['soCode']),  'dbfield' => 'socode', 'width'=>"150px" );  
    $arrDataDetailStructure['soDate'] = array('title'=>ucwords($obj->lang['date']),  'dbfield' => 'sodate', 'width'=>"90px",'format'=>'date');
    $arrDataDetailStructure['WOCode'] = array('title'=>ucwords($obj->lang['WOCode']),  'dbfield' => 'wocode', 'width'=>"200px" );
    $arrDataDetailStructure['manifestCode'] = array('title'=>ucwords($obj->lang['manifestCode']),  'dbfield' => 'manifestcode', 'width'=>"200px" );
    $arrDataDetailStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'amount', 'width'=>"80px",'format'=>'number','calculateTotal' => true);
    $arrDataDetailStructure['description'] = array('title'=>ucwords($obj->lang['description']),'dbfield' => 'description', 'width'=>"300px");

    $arrDetailTemplate = array(); 
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate); 
}
}


$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status'); 
$arrCustomer = $class->convertForCombobox($customer->searchData ('','',true,' and ('.$customer->tableName.'.statuskey = 2 )'),'pkey','name');    
$arrCustomCode =  $class->convertForCombobox($customCode->searchData($customCode->tableName . '.reftabletype', $rsKey['key'], true, ' order by ' . $customCode->tableName . '.orderlist asc'), 'pkey', 'name');

$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);  
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCustomeCode'] =  $class->inputSelect('selCustomCode[]', $arrCustomCode, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputInvoiceCode'] =  $class->inputText('invoiceCode'); 
$arrTwigVar['inputHidCutomerKey'] =  $class->inputHidden('hidCustomerKey');   
$arrTwigVar['inputCustomerName'] =  $class->inputSelect('customerName[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSoCode'] =  $class->inputText('soCode'); 
$arrTwigVar['inputIsGrouping'] =  $class->inputCheckBox('isGrouping', array('value'=> 1));
$arrTwigVar['inputIsShowDetail'] = $class->inputCheckBox('isShowDetail');
$arrTwigVar['order'] =  $orderCriteria;
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;     


if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';

	if(isset($_POST) && !empty($_POST['invoiceCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['invoiceCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['invoiceCode']));
	}
    
    if(isset($_POST) && !empty($_POST['trStartDate'])){
        
        switch($_POST['selDateType']){
            case '1' : $fieldName = $obj->tableName.'.trdate';  break;
            case '2' : $fieldName = $obj->tableSalesOrder.'.lastwodate'; break;
            default : $fieldName = $obj->tableName.'.trdate';  break;
                
        }
		$criteria .= ' and '.$fieldName.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => $arrDateType[$_POST['selDateType']], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}


    if(isset($_POST) && !empty($_POST['customerName'])) { 
        
        $customerKey = implode(",", $class->oDbCon->paramString($_POST['customerName']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.customerkey in('.$customerKey.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$customerKey.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$customerName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Customer', 'filter' => $customerName));
        
	}

    if(isset($_POST) && !empty($_POST['selCustomCode'])) { 
        
        $customCodeKey = implode(",", $class->oDbCon->paramString($_POST['selCustomCode']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.customcodekey in('.$customCodeKey.')';  

        $rsCriteria = $customCode->searchData('','',true, ' and '.$customCode->tableName.'.pkey in ('.$customCodeKey.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$customCode = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Jenis Faktur', 'filter' => $customCode));
        
	}
  	 
    if(isset($_POST) && !empty($_POST['soCode'])) { 
        $criteria .= ' AND '.$obj->tableName.'.salesordercodecache  LIKE ('.$class->oDbCon->paramString('%'.$_POST['soCode'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Kode Job Order', 'filter' => $_POST['soCode']));
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
    
     $totalRs = count($rs);
     for( $i=0;$i<$totalRs;$i++) {   
            
        $rsDetail = $rsDetailCol[$rs[$i]['pkey']];  
        
        $totalRsDetail = count($rsDetail);
         
        if($isGrouping){ 
            // model normal
            if ($isShowDetail) {
            
            for($j=0;$j<$totalRsDetail;$j++){
                $description = array(); 
                $manifestCode = array(); 
                $woCode = array(); 
                $rsInvoiceDetail = $rsAllItemDetailCol[$rsDetail[$j]['pkey']];
                if(!empty($rsDetail[$j]['description']))
                array_push($description, $rsDetail[$j]['description']); 
                
                for($k=0;$k<count($rsInvoiceDetail);$k++){
                    
                    if(empty($rsInvoiceDetail[$k]['wocode'])) continue;

                    // $party = $obj->formatNumber($rsInvoiceDetail[$k]['qtyinbaseunit']).' x '.$rsInvoiceDetail[$k]['itemname'].' @'.$obj->formatNumber($rsInvoiceDetail[$k]['priceinunit']).' = '.$obj->formatNumber($rsInvoiceDetail[$k]['total']);
                    array_push($description, $party);    
                    array_push($manifestCode, $rsInvoiceDetail[$k]['manifestcode']);    
                    array_push($woCode, $rsInvoiceDetail[$k]['wocode']);    
                    
                }

                $rsDetail[$j]['description'] =  implode('<br>',$description); 
                $rsDetail[$j]['manifestcode'] =  implode('<br>',$manifestCode); 
                $rsDetail[$j]['wocode'] =  implode('<br>',$woCode); 
                
                // klao tipenya biaya
                // ini nanti perlu diupdate (dimunculkan) utk yg non grouping
                if(!empty($rsDetail[$j]['itemkey']) && empty($rsDetail[$j]['salesorderkey']))
                    $rsDetail[$j]['socode'] = $rsDetail[$j]['itemname'];
            }
        
            // has detail 
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 
   }
        }else{
            
            $arrItemValue = array();
            for($j=0;$j<$totalRsDetail;$j++){  
                $rsInvoiceDetail = $rsAllItemDetailCol[$rsDetail[$j]['pkey']]; 
                
                foreach($rsInvoiceDetail as $invoiceItem){  
                    if(!isset($rs[$i]['item'.$invoiceItem['itemkey']])) $rs[$i]['item'.$invoiceItem['itemkey']] = 0;
                    $rs[$i]['item'.$invoiceItem['itemkey']] += $invoiceItem['total']; 
                    
                    $vol = strval(intval($invoiceItem['volume'])); 
                    $rs[$i]['volume'.$vol] += $invoiceItem['qtyinbaseunit'];
                } 
            }
            
            
        }
        
        
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    } 
    
    // kalo bisa berubah2 headernya, line ini tetep harus ad utk refresh
    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
		
}  

echo $twig->render('reportDisposalSalesInvoice.html', $arrTwigVar);   

?>
