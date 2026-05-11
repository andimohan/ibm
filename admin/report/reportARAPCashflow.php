<?php
include '../../_config.php';
include '../../_include-v2.php';
include '_global.php';

includeClass(array('AR.class.php','AP.class.php','Warehouse.class.php','Currency.class.php','ARPayment.class.php','APPayment.class.php'));
$ar = createObjAndAddToCol(new AR()); 
$ap = createObjAndAddToCol(new AP()); 
$arPayment = createObjAndAddToCol(new ARPayment()); 
$apPayment = createObjAndAddToCol(new APPayment()); 
$currency = createObjAndAddToCol(new Currency()); 
$warehouse = createObjAndAddToCol(new Warehouse()); 

$securityObject = 'reportARAPCashflow'; // the value of security object is manually inserted to handle
								  // some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));


/* data structure */
$arrTemplate = array(); 
$arrARType= array(
    '1' => $class->lang['ar/apPayment'],   
    '2' => $class->lang['ar/ap'], 
);


$arrFilterInformation = array();
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $class->lang['ARAPCashflowReport'];

if (isset($_POST) && !empty($_POST['hidAction'])){
    
    if(isset($_POST) && !empty($_POST['trStartDate'])){
		 array_push($arrFilterInformation,array("label" => $class->lang['period'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['selARAPType'])){
		 array_push($arrFilterInformation,array("label" => $class->lang['transactionType'], 'filter' => $arrARType[$_POST['selARAPType']] ));
	}
	
    /*if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	   
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $class->lang['warehouse'], 'filter' => $statusName ));
        
	}*/
	
	//$arrWarehouse = $_POST['selWarehouse'];
    $rsWarehouse = $warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc');
    $rsWarehouse = array_column($rsWarehouse,'name','pkey');
      
    $tempreport = '<div style="min-width:500px"  class="rewrite-row">';  
    $tempreport .= generateARAP($_POST['selARAPType'], date('01 / m / Y',strtotime($_POST['trStartDate'])), date('t / m / Y',strtotime($_POST['trEndDate'])), $rsWarehouse ); 
    $tempreport .= '</div>';
   
	$reportResult = array();
	$reportResult['filterInformation'] = $arrFilterInformation;
 	$reportResult['content'] = $tempreport;

    if ((isset($_POST['hidExportExcel']) && $_POST['hidExportExcel'] == 1)){  
        $arrTemplate = array();
        $arrTemplate[0]['dataToExport'] = array();
        $arrTemplate[0]['filterInformation'] = $arrFilterInformation;
        
        $arrContent = array();
        $arrContent['income'] = $rsIncome;
        $arrContent['expense'] = $rsExpense;
        $arrContent['profitloss'] = $profitloss;
        exportToExcel($arrHeaderTemplate['reportTitle'],$arrTemplate, $arrContent);  
    }else{ 
        echo json_encode($reportResult);
        die;
    }
}else{ 
    $_POST['trStartDate'] = date('F Y',mktime(0, 0, 0, 1, 1, date('Y')));
    $_POST['trEndDate'] = date('F Y');   
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrTwigVar['inputSelARAPType'] =  $class->inputSelect('selARAPType', $arrARType ); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  

$arrTwigVar['inputStartDate'] = $class->inputMonth('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputMonth('trEndDate', array('etc' => 'style="text-align:center"'));    
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  
echo $twig->render('reportARAPCashflow.html', $arrTwigVar);


function generateARAP($transactionType,$startDt,$endDt,$arrWarehouseARAP){ 
    global $class;	 
    global $ar;	 
    global $ap;	 
    global $arPayment;	 
    global $apPayment;	 
    global $currency;	 
    global $warehouse;  
     
	$tabledetail = '';
	$criteria = ' AND trdate between '.$class->oDbCon->paramDate( $startDt,' / ').' AND '.$class->oDbCon->paramDate( $endDt,' / ','Y-m-d 23:59'); 
    
	$arrData = array();
    
    if($transactionType == 1){ 
        $rsAR = $arPayment->generateCashflowReport($criteria);
        $rsAP = $apPayment->generateCashflowReport($criteria);
    }else{ 
        $rsAR = $ar->generateCashflowReport($criteria);
        $rsAP = $ap->generateCashflowReport($criteria);
    }
     
    $rsARByPeriod = $class->reindexDetailCollections($rsAR,'timeindex');
    $rsAPByPeriod = $class->reindexDetailCollections($rsAP,'timeindex');
    
    //$arrCurrencyIndex =  array_column($rsAR,'currencyname','currencykey'); // buat patokan looping ad berapa currency
    $rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1);
    $arrCurrencyIndex =  array_column($rsCurrency,'name','pkey');
    
    $arrTimeIndex =  array_column($rsAR,'trmonthyear','timeindex'); // buat patokan looping ad berapa periode
     
    foreach($arrTimeIndex as $timeIndex => $periodeName){
        
		$tabledetail .= '<table  style=" margin-bottom:2em">';
		$tabledetail .= '<thead>'; 
		$tabledetail .= '<tr class="table-header">';
		$tabledetail .= '<th style="width:100px;">'.ucwords($periodeName).'</th>';
		
		foreach($arrWarehouseARAP as $warehouseName) 
			$tabledetail .= '<th style="text-align:right;width:120px;">'.ucwords(strtolower($warehouseName)).'</th>';
	 
		$tabledetail .= '<th style="text-align:right;width:120px; font-weight:bold">'.ucwords($class->lang['total']).'</th>';
		
		$tabledetail .= '</tr>';
		$tabledetail .= '<thead>';
		        
        $totalPerWarehouse = array();
        $totalPerWarehouse[0] = 0;
        

            
        // AR
        // break per currency
        $rsARAP = $rsARByPeriod[$timeIndex]; 
        $rsARByCurrency = $class->reindexDetailCollections($rsARAP,'currencykey');
	    foreach($arrCurrencyIndex as $currencyIndex => $currencyName){  
            $tabledetail .= '<tr>'; 
            $tabledetail .= '<td style="font-weight:bold;">'.$class->lang['ar'] . ' - '.$currencyName.'</td>';

            // tampilin nilai per gudang
            $rsARAP = $rsARByCurrency[$currencyIndex];
            $rsARByWarehouse = $class->reindexDetailCollections($rsARAP,'warehousekey');  
 
            $total = 0;
            foreach($arrWarehouseARAP as $warehouseIndex=>$warehouseName){ 
                 
                $amount = (isset($rsARByWarehouse[$warehouseIndex])) ? $rsARByWarehouse[$warehouseIndex][0]['totalidr']: 0;
                $tabledetail .= '<td style="text-align:right">'.$class->formatNumber($amount).'</td>';
                
                // hitung subtotal 
                if(!isset($totalPerWarehouse[$warehouseIndex])) $totalPerWarehouse[$warehouseIndex] = 0; 
                $total += $amount;
                $totalPerWarehouse[$warehouseIndex] += $amount; 
            }

            $totalPerWarehouse[0] += $total;
                    
            $tabledetail .= '<td style="text-align:right;  font-weight:bold"> '.$class->formatNumber($total).'</td>';

            $tabledetail .= '</tr>';
        }
        
        
        // AP
        // break per currency
        $rsARAP = $rsAPByPeriod[$timeIndex]; 
        $rsAPByCurrency = $class->reindexDetailCollections($rsARAP,'currencykey');
	    foreach($arrCurrencyIndex as $currencyIndex => $currencyName){  
            $tabledetail .= '<tr>'; 
            $tabledetail .= '<td style="font-weight:bold;">'.$class->lang['ap'] . ' - '.$currencyName.'</td>';

            // tampilin nilai per gudang
            $rsARAP = $rsAPByCurrency[$currencyIndex];
            $rsAPByWarehouse = $class->reindexDetailCollections($rsARAP,'warehousekey');  
 
            $total = 0;
            foreach($arrWarehouseARAP as $warehouseIndex=>$warehouseName){ 
                 
                $amount = (isset($rsAPByWarehouse[$warehouseIndex])) ? $rsAPByWarehouse[$warehouseIndex][0]['totalidr']: 0;
                $amount *= -1;
                
                $tabledetail .= '<td style="text-align:right">'.$class->formatNumber($amount).'</td>';
                
                // hitung subtotal 
                if(!isset($totalPerWarehouse[$warehouseIndex])) $totalPerWarehouse[$warehouseIndex] = 0; 
                $total += $amount;
                $totalPerWarehouse[$warehouseIndex] += $amount; 
            }

            $totalPerWarehouse[0] += $total;
                    
            $tabledetail .= '<td style="text-align:right; font-weight:bold"> '.$class->formatNumber($total).'</td>';

            $tabledetail .= '</tr>';
        }
        
        // subtotal
        
        $tabledetail .= '<tr class="subtotal">';
        $tabledetail .= '<td ></td>';
        foreach($arrWarehouseARAP as $warehouseIndex=>$warehouseName){ 
          $tabledetail .= '<td style="text-align:right;">'.$class->formatNumber($totalPerWarehouse[$warehouseIndex]).'</td>';
        }

        $tabledetail .= '<td  style="text-align:right;  font-weight:bold">'.$class->formatNumber($totalPerWarehouse[0]).'</td>';
        $tabledetail .= '</tr>';
        
        
		$tabledetail .= '</table>';
    }
    
	  
	return $tabledetail;
	
} 
?>