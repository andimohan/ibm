<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('EMKLPurchaseOrder.class.php','EMKLJobOrder.class.php', 'Warehouse.class.php', 'TermOfPayment.class.php'));
$emklPurchaseOrder = createObjAndAddToCol( new EMKLPurchaseOrder());
$warehouse = createObjAndAddToCol( new Warehouse());
$termOfPayment = createObjAndAddToCol( new TermOfPayment());
$container = createObjAndAddToCol(new Container());
$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());
    
include '_global.php';

$securityObject = 'reportTransactionCashFlow'; // the value of security object is manually inserted to handle
								  // some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$arrFilterInformation = array();
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $class->lang['transactionCashFlowReport'];

//$arrWarehouseKey = array();
//$arrContainerTypeKey = array();

$rsWarehouse = $warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc');
$rsContainer = $container->getContainerType();

if (isset($_POST) && !empty($_POST['hidAction'])){
  
    if(isset($_POST) && !empty($_POST['trStartDate'])){
		 array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
     
   /* if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        $arrWarehouseKey = $_POST['selWarehouse'];
        
       	//$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $class->lang['warehouse'], 'filter' => $statusName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['selContainer'])) { 
        
        $key = $_POST['selContainer'];   
        $arrContainerTypeKey = $_POST['selContainer'];
       	//$criteria .= ' AND '.$class->tableName.'.containertypekey in ('.$class->oDbCon->paramString($key,',').')';  

        $rsCriteria = $container->getContainerType($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>$class->lang['containerType'], 'filter' => $statusName ));
        
	} */
    
      
    $criteria = '';
    $tempreport = '<div style="min-width:500px"  class="rewrite-row">'; 
 
    $tempreport .= '<table style="table-layout:fixed; width:100%">';
    
    $tempreport .= '<tr class="row-plain">'; 
    $tempreport .= '<td rowspan="5">Dry</td>';
    
    $tempreport .= '<td style="width: 8em;">'.$class->lang['purchase'].' ('.$class->lang['cash'].')</td>'; 
    
    foreach($rsWarehouse as $warehouseRow){
         $tempreport .= '<td>'.$warehouseRow['name'].'</td>';
    }
    
    $tempreport .= '</tr>';
    
    $tempreport .= '</table>';
    
//    $tempreport .= '<table style="table-layout:fixed;  width:100%" >'; 
//    
//    $rsTOP = $termOfPayment->searchDataRow(array($termOfPayment->tableName.'.pkey'),' and '.$termOfPayment->tableName.'.duedays = 0');
//    $arrTOPKey = array_column($rsTOP,'pkey');
//
//    $totalCashOut = 0;
//    // total cash
//    $totalCashPurchase = $emklPurchaseOrder->sumTotalPurchase(array(
//                                                                    'startDate' => $_POST['trStartDate'],
//                                                                    'endDate' => $_POST['trEndDate'],
//                                                                    'termOfPaymentKey' => $arrTOPKey
//                                                                ));
////'warehousekey' => $arrWarehouseKey,
////'containerTypeKey' => $arrContainerTypeKey,
//    
//    $totalCashOut += $totalCashPurchase;
//    $tempreport .= '<tr class="row-plain">
//                    <td style="width: 15em; font-weight:bold">'.$class->lang['purchase'].' ('.$class->lang['cash'].')</td>
//                    <td style="width: 10em; text-align:right">'.$class->formatNumber($totalCashPurchase).'</td>
//                    <td></td>
//                    </tr>';
//    
//    // total AP Payment
//    $totalAPPayment = $emklPurchaseOrder->sumTotalAPPayment(array(
//                                                                    'startDate' => $_POST['trStartDate'],
//                                                                    'endDate' => $_POST['trEndDate']
//                                                                ));
//    
////    'warehousekey' => $arrWarehouseKey, 
////    'containerTypeKey' => $arrContainerTypeKey,
//
//    $totalCashOut += $totalAPPayment;
//    $tempreport .= '<tr class="row-plain">
//                    <td style="font-weight:bold">'.$class->lang['accountsPayablePayment'].'</td>
//                    <td style="text-align:right">'.$class->formatNumber($totalAPPayment).'</td>
//                    <td></td>
//                    </tr>';
//    
//    //total cash out
//    $tempreport .= '<tr class="row-plain">
//                    <td style="width: 15em; font-weight:bold">'.$class->lang['total'].'</td>
//                    <td style="font-weight:bold; text-align:right">'.$class->formatNumber($totalCashOut).'</td>
//                    <td></td>
//                    </tr>';
//    
//   $tempreport .= '<tr class="row-plain">
//                    <td style="border:0; height: 2em" colspan="3"></td> 
//                    </tr>';
//  
//    
//
//    
//    $totalCashIn = 0; 
//    // total AP Payment
//    $totalARPayment = $emklJobOrder->sumTotalARPayment(array(
//                                                                    'startDate' => $_POST['trStartDate'],
//                                                                    'endDate' => $_POST['trEndDate'],
//                                                                    'warehousekey' => $arrWarehouseKey, 
//                                                                    'containerTypeKey' => $arrContainerTypeKey,
//                                                                ));
//    $totalCashIn += $totalARPayment;
//    $tempreport .= '<tr class="row-plain">
//                    <td style="font-weight:bold">'.$class->lang['accountsReceivablePayment'].'</td>
//                    <td style="text-align:right">'.$class->formatNumber($totalARPayment).'</td>
//                    <td></td>
//                    </tr>';
//    
//    //total cash out
//    $tempreport .= '<tr class="row-plain">
//                    <td style="width: 15em; font-weight:bold">'.$class->lang['total'].'</td>
//                    <td style="font-weight:bold; text-align:right">'.$class->formatNumber($totalCashIn).'</td>
//                    <td></td>
//                    </tr>';
//  
//    
//    
//    $rsTotal = array(); //$emklPurchaseOrder->generateTotalAPPayment($criteria);
//    $tempreport .= '</table><br>';
//    
    $tempreport .= '</div>';

	$reportResult = array();
	$reportResult['filterInformation'] = $arrFilterInformation;
 	$reportResult['content'] = $tempreport;

    if ((isset($_POST['hidExportExcel']) && $_POST['hidExportExcel'] == 1)){  
        $arrTemplate = array();
        $arrTemplate[0]['dataToExport'] = array();
        $arrTemplate[0]['filterInformation'] = $arrFilterInformation;
        
        exportToExcel($arrHeaderTemplate['reportTitle'],$arrTemplate, $arrContent);  
    }else{ 
        echo json_encode($reportResult);
        die;
    }
}else{
    $_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
    
//test 
$_POST['trStartDate'] = '01 / 01 / 2021';
$_POST['trEndDate'] = '30 / 09 / 2021';
}


//$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
//$arrContainer = $class->convertForCombobox($container->getContainerType(),'pkey','name');   

//$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputSelContainer'] =  $class->inputSelect('selContainer[]', $arrContainer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  
echo $twig->render('reportEMKLTransactionCashFlow.html', $arrTwigVar); 
?>
