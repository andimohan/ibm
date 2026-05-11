<?php
include '../../_config.php';
include '../../_include.php';
include '_global.php';
 
$securityObject = 'EMKLOrderInvoice'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class


$obj= $emklJobOrder; 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$arrFilterInformation = array();
 
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $class->lang['GrossProfitReport'];

$criteria = '';

if (isset($_POST) && !empty($_POST['hidAction'])){ 
   
 $criteria = '';
    
    if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['salesCode']));
	}
   
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.etdpol between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Periode', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND '.$obj->tableNameDetail.'.customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Pelangan', 'filter' => $statusName ));
        
	}
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey';  
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
		   
	$order = 'order by '.$orderBy.' ' .$orderType; 
	$rs = $obj->generateGrossProfitReport($criteria,$order);
		
    $tempreport = '';
 
    $tempreport .= '<table class="main-table" style="width:2500px">';
    $tempreport .= '<thead><tr class="table-header">';
    $tempreport .= '<th  style="text-align:right; width: 50px">No.</th>';
    $tempreport .= '<th style="width:100px">'.$class->lang['jobOrder'].'</th>';
    $tempreport .= '<th style="width:120px">'.$class->lang['transactionCode'].'</th>';
    $tempreport .= '<th style="width:250px">'.$class->lang['shipper'].'</th>'; 
    $tempreport .= '<th style="width:100px; text-align:center">ETD</th>'; 
    $tempreport .= '<th style="width:150px">POD</th>'; 
    $tempreport .= '<th style="width:120px">BL</th>'; 
    $tempreport .= '<th style="width:150px">'.$class->lang['party'].'</th>'; 
    $tempreport .= '<th style="width:150px">'.$class->lang['carrier'].'</th>'; 
    $tempreport .= '<th style="width:150px">'.$class->lang['container'].'</th>'; 
    $tempreport .= '<th style="width:100px; text-align:center">'.$class->lang['invoiceDate'].'</th>'; 
    $tempreport .= '<th style="width:130px">'.$class->lang['invoiceCode'].'</th>'; 
    $tempreport .= '<th style="width:80px; text-align:right">'.$class->lang['rate'].'</th>'; 
    $tempreport .= '<th style="width:100px; text-align:right">'.$class->lang['selling'].'</th>'; 
    $tempreport .= '<th style="width:100px; text-align:right">'.$class->lang['buying'].'</th>'; 
    $tempreport .= '<th style="width:100px; text-align:right">'.$class->lang['commission'].'</th>'; 
    $tempreport .= '<th style="width:100px; text-align:right">'.$class->lang['grossProfit'].'</th>'; 
    $tempreport .= '<th></th>'; 
    $tempreport .= '</tr></thead>';
    $tempreport .= '<tbody>';
			
    
    if (empty($rs)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

    $ctr = 1;
    $currCode = '';
    $oddEvenStyle[1] = 'overwrite-odd-style';
    $oddEvenStyle[-1] = 'overwrite-even-style';
    
    $flag = 1;
    $marginProfit = 0.7;
    
    foreach($rs as $row) {  
        
        $totalBuying = $class->formatNumber($row['totalbuying']);
        $totalSelling = $class->formatNumber($row['totalselling']);
        $totalCommission = $class->formatNumber($row['totalcommission']);
        $grossProfit = $class->formatNumber($row['grossprofit']);
        
        $code = $row['code'];
         
        if($row['code'] == $currCode){ 
            $code = '';
            $totalBuying = '';
            $totalSelling = '';
            $totalCommission = '';
            $grossProfit = ''; 
        }else{ 
            $flag *= -1; 
            $marginStyle = (($row['grossprofit']  / $row['totalselling']) > $marginProfit)  ?  'text-blue-munsell' : '';
        }
        
       /* if( $row['detailcode'] == '0057-00-2')
            $class->setLog($row['detailkey'],true);*/
             
        $party = array();
        if($row['loadcontainertypekey'] == 1){
            $arrParty = $obj->getDetailParty($row['detailkey']);
            foreach($arrParty as $partyRow)
                array_push($party,$class->formatNumber($partyRow['qty'],0). 'x '. $partyRow['container'] );
        }else{
            $party = array('1x ' . $row['lclcontainername']);
        }
      
        
        $profitStyle = ($row['grossprofit'] > 0) ? 'text-green-avocado' : 'text-red-cardinal';
        if($row['grossprofit'] == 0 ) $profitStyle = '';
         
        $tempreport .= '<tr class="report-row rewrite-row '.$oddEvenStyle[$flag].' ' .$marginStyle.'">';
        
        $tempreport .= '<td style="text-align:right">'.$ctr++.'.</td>';
        $tempreport .= '<td>'.$code.'</td>';
        $tempreport .= '<td>'.$row['detailcode'].'</td>';
        $tempreport .= '<td>'.$row['customername'].'</td>';
        $tempreport .= '<td style="text-align:center">'.$class->formatDBDate($row['etdpol'], 'd / m / Y ').'</td>';
        $tempreport .= '<td>'.$row['podname'].'</td>';
        $tempreport .= '<td>'.$row['mblnumber'].'</td>';
        $tempreport .= '<td>'.implode('<br>',$party ).'</td>';
        $tempreport .= '<td>'.$row['carriername'].'</td>';
        $tempreport .= '<td>'.str_replace(chr(13),'<br>',$row['containernumber']).'</td>';
        $tempreport .= '<td  style="text-align:center">'.$class->formatDBDate($row['invoicedate'], 'd / m / Y ').'</td>';
        $tempreport .= '<td>'.$row['invoicecode'].'</td>';
        $tempreport .= '<td style="text-align:right">'.$class->formatNumber($row['rate'],2).'</td>';
        $tempreport .= '<td style="text-align:right">'.$totalSelling.'</td>';
        $tempreport .= '<td style="text-align:right">'.$totalBuying.'</td>';
        $tempreport .= '<td style="text-align:right">'.$totalCommission.'</td>';
        $tempreport .= '<td style="text-align:right" class='.$profitStyle.'>'.$grossProfit.'</td>';
        $tempreport .= '<td></td>';
        $tempreport .= '</tr>'; 
        
        $tempreport .= '<tr class="detail-row rewrite-row"><td class="table-detail-panel" style="width:10px"></td><td colspan="10" class="table-detail-panel"></td></tr>';
        
        $currCode = $row['code'];
        
    }
    
    $tempreport .= '</tbody>';
    $tempreport .='</table>';
    
	$reportResult = array(); 
    $reportResult['filterInformation'] = $arrFilterInformation;  
 	$reportResult['content'] = $tempreport;
     	 
    if ((isset($_POST['hidExportExcel']) && $_POST['hidExportExcel'] == 1)){  
        $arrTemplate = array();
        $arrTemplate[0]['dataToExport'] = array();
        $arrTemplate[0]['filterInformation'] = $arrFilterInformation;
        /*
        $arrContent = array();
        $arrContent['left'] = $arrLeft;
        $arrContent['right'] = $arrRight;
        exportToExcel($arrHeaderTemplate['reportTitle'],$arrTemplate, $arrContent);  */
    }else{ 
        echo json_encode($reportResult);
        die;
    }
    
}else{ 
	$_POST['trStartDate'] = date('d / m / Y'); 
	$_POST['trEndDate'] = date('d / m / Y'); 
}
  
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');

 
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); 

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  


echo $twig->render('reportEMKLSalesGrossProfit.html', $arrTwigVar);
 
 
?>
