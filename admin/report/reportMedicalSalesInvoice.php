<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('MedicalSalesInvoice.class.php');
$medicalSalesInvoice = createObjAndAddToCol(new MedicalSalesInvoice()); 
$item = createObjAndAddToCol(new Item()); 

include '_global.php';

$obj = $medicalSalesInvoice; 
$securityObject = 'ReportMedicalSalesInvoice'; // the value of security object is manually inserted to handle 
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


$rsKey = $class->getTableKeyAndObj($obj->tableName);  
 
$customCodeInactiveCriteria = '';
     
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

// laporan gk boleh di report per detail karena ad komponen diskon di invoice, menyebabkan nilainya berbeda

$isGrouping = (isset($_POST['isGrouping']) && !empty($_POST['isGrouping'])) ? true : false;

                    
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
$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'grandtotal','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['tax'] = array('title'=>ucwords($obj->lang['tax']),'dbfield' => 'taxvalue','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['tax23'] = array('title'=>ucwords($obj->lang['tax23']),'dbfield' => 'tax23value','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 

$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']), 'width'=>"300px",'dbfield' => 'trdesc');
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['serviceOrderInvoiceReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if ($isGrouping){ 
    // detail ...
    $arrDataDetailStructure = array(); 
    $arrDataDetailStructure['soCode'] = array('title'=>ucwords($obj->lang['soCode']),  'dbfield' => 'socode', 'width'=>"150px" );  
    $arrDataDetailStructure['soDate'] = array('title'=>ucwords($obj->lang['date']),  'dbfield' => 'sodate', 'width'=>"90px",'format'=>'date');
    $arrDataDetailStructure['insuredName'] = array('title'=>ucwords($obj->lang['insuredName']),  'dbfield' => 'insuredname', 'width'=>"200px" );
    $arrDataDetailStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'amount', 'width'=>"80px",'format'=>'number','calculateTotal' => true);
    $arrDataDetailStructure['description'] = array('title'=>ucwords($obj->lang['description']),'dbfield' => 'description', 'width'=>"300px");

    $arrDetailTemplate = array(); 
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate); 
}


$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status'); 

$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);  
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputInvoiceCode'] =  $class->inputText('invoiceCode'); 
$arrTwigVar['inputHidCutomerKey'] =  $class->inputHidden('hidCustomerKey');  
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName'); 
$arrTwigVar['inputSoCode'] =  $class->inputText('soCode'); 
$arrTwigVar['inputIsGrouping'] =  $class->inputCheckBox('isGrouping', array('value'=> 1));
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
        $criteria .= ' AND '.$obj->tableCustomer.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').' )';
	    array_push($arrFilterInformation,array("label" => 'Customer', 'filter' => $_POST['customerName']));
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
     

    $rsDetailCol = $obj->getDetailCollections($rs,'refkey');    
    $rsAllItemDetail = $obj->getItemDetail(array_column($rs,'pkey'),'refheaderkey');
    $rsAllItemDetailCol   = $obj->reindexDetailCollections($rsAllItemDetail,'refkey');
    
    if($isGrouping){ 
        /* $rsDetailCol = $obj->getDetailCollections($rs,'refkey');
    
        $rsAllItemDetail = $obj->getItemDetail(array_column($rs,'pkey'),'refheaderkey');
        $rsAllItemDetailCol = $obj->reindexDetailCollections($rsAllItemDetail,'refkey');*/
    }else{ 
        
        $arrTempStructure = array();
        //$arrTempStructure['socode'] = array('title'=>ucwords($obj->lang['soCode']),  'width'=>"150px", 'dbfield' => 'socode'); 
        //$arrTempStructure['sodate'] = array('title'=>ucwords($obj->lang['jobOrderDate']),  'width'=>"100px", 'dbfield' => 'sodate', 'format' => 'date'); 
        //$arrTempStructure['lastactivitydate'] = array('title'=>ucwords($obj->lang['activityDate']),  'width'=>"100px", 'dbfield' => 'lastwodate', 'format' => 'date'); 

        // cari semua jenis selling yg ada
        $arrItemKey = array_unique(array_column($rsAllItemDetail,'itemkey'));
        $rsItem = $item->searchDataRow( array($item->tableName.'.pkey, '.$item->tableName.'.name') ,
                                         'and '.$item->tableName.'.itemtype = 2 and ' .$item->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrItemKey,',').')',
                                         ' order by '.$item->tableName.'.servicecost asc'
                                        );
        
        foreach($rsItem as $itemRow)
            $arrTempStructure['item'.$itemRow['pkey']] = array('title'=>$itemRow['name'],'dbfield' => 'item'.$itemRow['pkey'], 'width'=>"150px",'format'=>'number','sortable' => false,'calculateTotal' => true, 'textColor' => '568203');  
        
        $arrReturn = $obj->insertReportColumns(9, $arrDataStructure, $arrTempStructure,$twig,$arrTwigVar,  $arrHeaderTemplate);
        $arrTemplate = $arrReturn['tableTemplate']; 
        
    }
    
     $totalRs = count($rs);
     for( $i=0;$i<$totalRs;$i++) {   
            
        $rsDetail = $rsDetailCol[$rs[$i]['pkey']];  
        $totalRsDetail = count($rsDetail);
         
        if($isGrouping){ 
            // model normal
            
            for($j=0;$j<$totalRsDetail;$j++){
                $description = array(); 
                $rsInvoiceDetail = $rsAllItemDetailCol[$rsDetail[$j]['pkey']];
                
                if(!empty($rsDetail[$j]['description']))
                    array_push($description, $rsDetail[$j]['description']); 
                
                for($k=0;$k<count($rsInvoiceDetail);$k++){
                    if(empty($rsInvoiceDetail[$k]['itemname'])) continue;

                    $party = $obj->formatNumber($rsInvoiceDetail[$k]['qtyinbaseunit']).' x '.$rsInvoiceDetail[$k]['itemname'].' @'.$obj->formatNumber($rsInvoiceDetail[$k]['priceinunit']).' = '.$obj->formatNumber($rsInvoiceDetail[$k]['total']);
                    array_push($description, $party);    
                }
 

                $rsDetail[$j]['description'] =  implode('<br>',$description); 
                
                // klao tipenya biaya
                // ini nanti perlu diupdate (dimunculkan) utk yg non grouping
                if(!empty($rsDetail[$j]['itemkey']) && empty($rsDetail[$j]['salesorderkey']))
                    $rsDetail[$j]['socode'] = $rsDetail[$j]['itemname'];
            }
        
            // has detail 
            $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 
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
        // $obj->setLog($rs, true);
        
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

echo $twig->render('reportMedicalSalesInvoice.html', $arrTwigVar);   

?>