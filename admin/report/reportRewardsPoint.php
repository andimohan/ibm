<?php	 
include '../../_config.php';   
include '../../_include.php';
include '_global.php';

$obj= $rewardsPoint;
$securityObject = 'rewardsPoint'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$arrFilterInformation = array();    

if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
	
	if(isset($_POST) && !empty($_POST['customerCode'])) {
		$criteria .= ' AND '.$customer->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['customerCode']));
	}
	if(isset($_POST) && !empty($_POST['hidCustomerKey'])) {
		$criteria .= ' AND '.$customer->tableName.'.pkey = '.$class->oDbCon->paramString($_POST['hidCustomerKey']); 
		$rsCustomer = $customer->getDataRowById($_POST['hidCustomerKey']); 
		array_push($arrFilterInformation,array("label" => 'Nama', 'filter' =>  $rsCustomer[0]['name']));
	}
	
		 
	$orderBy = 'name'; 
	$orderType = 'asc'; 
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
	$rsCustomer = $customer->searchData('','',true,$criteria,$order);
	
	$tableReport = '';
	for($j=0;$j<count($rsCustomer);$j++){ 
	
		$tempreport = '';  
		$rs = $obj->searchData('','',true,' and '.$obj->tableName.'.statuskey = 2 and '.$obj->tableName.'.customerkey = ' .$class->oDbCon->paramString($rsCustomer[$j]['pkey']).' and '.$obj->tableName.'.createdon between '.$class->oDbCon->paramDate($_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate($_POST['trEndDate'], ' / ') .' + interval 1 day','order by '.$obj->tableName.'.pkey asc');
	 
		if (empty($rs)){
			$tempreport .= '<tr class="report-row rewrite-row"><td colspan="6"></td></tr>';	
		}
		
		$startQty = $obj->sumRewardsMovement($rsCustomer[$j]['pkey'], $_POST['trStartDate']);
		
		for( $i=0;$i<count($rs);$i++) {   
		 	
			$temptablerow = ''; 
	
			$temptablerow  .= '<tr class="rewrite-row"> '; 
			$temptablerow  .= '<td style="text-align:center;">'. $obj->formatDBDate($rs[$i]['createdon'],'d / m / Y').'</td>'; 
			$temptablerow  .= '<td style="text-align:right;">'. $obj->formatNumber($startQty).'</td>'; 
			$temptablerow  .= '<td style="text-align:right;">'. $obj->formatNumber($rs[$i]['point']).'</td>';
			
			$startQty += $rs[$i]['point'];
			
			$temptablerow  .= '<td style="text-align:right;">'. $obj->formatNumber($startQty).'</td>';
			
			$temptablerow  .= '<td>'.$rs[$i]['trdesc'].'</td>';   
			$temptablerow  .= '</tr>';
			$temptablerow  .= '<tr class="detail-row rewrite-row">';
			$temptablerow  .= '<td colspan="6">';
			$temptablerow  .= '';
			$temptablerow  .= '</td>';
			$temptablerow  .= '</tr>'; 
			
			$tempreport .= $temptablerow; 
			   
		}
		
		$tempreport .= '<tr class="subtotal rewrite-row"> ';  
		$tempreport .= '<td colspan="6"></td>';   
			  
		
		$tempreport .= '</tr> '; 
		
		$tableReport .= '
			<div class="rewrite-row">'.$rsCustomer[$j]['code'].' - '.$rsCustomer[$j]['name'].'</div>
			<div class="rewrite-row">Total Poin : '.$obj->formatNumber($startQty).'</div>
			<table class="rewrite-row" style="width:600px; min-width:100%; margin-bottom:1em">
				<tr class="table-header">  
					<td style="text-align:center;width:100px;" >Tgl. Transaksi</td> 
					<td style="width:70px;text-align:right; ">Poin Awal</td> 
					<td style="width:70px;text-align:right;">Pergerakan</td> 
					<td style="width:70px;text-align:right;">Poin Akhir</td>  
					<td>Catatan</td>  
				</tr>  
				'.$tempreport.'
		    </table>
			';
		 
	}
		
	
	$reportResult = array(); 
	 
	$reportResult['filterInformation'] = $arrFilterInformation;  
 	$reportResult['content'] = $tableReport;  
 	echo json_encode($reportResult);
	die;
}
else{ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
} 

$arrTwigVar['inputCustomerCode'] =  $class->input('text','customerCode');  
$arrTwigVar['inputHidCustomerKey'] = $class->input('hidden','hidCustomerKey');
$arrTwigVar['inputCustomerName'] =  $class->input('text','customerName');
$arrTwigVar['inputStartDate'] = $class->input('text','trStartDate',true,'','readonly="readonly"','form-control input-date');
$arrTwigVar['inputEndDate'] = $class->input('text','trEndDate',true,'','readonly="readonly"','form-control input-date');
      
echo $twig->render('reportRewardsPoint.html', $arrTwigVar);   
?>

