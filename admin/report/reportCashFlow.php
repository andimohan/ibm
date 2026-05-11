<?php
	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$securityObject = 'reportCashFlow'; // the value of security object is manually inserted to handle 
									 // some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$arrFilterInformation = array();  

if (USE_GL){

	$obj= $generalJournal;

	if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	$rsHeaderCriteria = ' and  ('.$obj->tableName.'.statuskey = 2 or '.$obj->tableName.'.statuskey = 3)';

	$dateMethod = $class->loadSetting('movementDateMethod');
    	$datefield = 'createdon';
        if ($dateMethod == 2) 
            $datefield = 'trdate';
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$rsHeaderCriteria .=  ' and  '.$obj->tableName.'.'.$datefield.' between '.$class->oDbCon->paramDate($_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate($_POST['trEndDate'], ' / ','Y-m-d 23:59:59').' order by  '.$obj->tableName.'.'.$datefield.'  asc';
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
	 
	if(isset($_POST) && !empty($_POST['hidCOAKey'])) {
		$criteria .= ' AND '.$chartOfAccount->tableName.'.pkey = '.$class->oDbCon->paramString($_POST['hidCOAKey']); 
		$rsCOA = $chartOfAccount->getDataRowById($_POST['hidCOAKey']); 
		array_push($arrFilterInformation,array("label" => 'Akun', 'filter' =>  $rsCOA[0]['code'].' - '.$rsCOA[0]['name']));
	} 
		 
	$orderBy = 'code'; 
	$orderType = 'asc';  
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
	$rsCOA = $chartOfAccount->searchData('','',true,$criteria,$order);
	
	$tableReport = '';
	for($j=0;$j<count($rsCOA);$j++){ 
	
		$tempreport = '';           	

		$date = date('d / m / Y',strtotime(str_replace('\'','',$obj->oDbCon->paramDate($_POST['trStartDate'],' / ','Y-m-d')).' -1 day'));

		

       	$startCash = $obj->sumCashFlow($rsCOA[$j]['pkey'],'',$date);
	 	
	 	$rsDetail = $obj->getDetailWithRelatedInformation($rsCOA[$j]['pkey'], $rsHeaderCriteria);

		if (empty($rsDetail)){
			$tempreport .= '<tr class="report-row rewrite-row"><td colspan="6"></td></tr>';	
		}else{

			$temptablerow = ''; 

			$temptablerow  .= '<tr class="rewrite-row"> '; 
			$temptablerow  .= '<td style="text-align:center;"></td>';
			$temptablerow  .= '<td style="text-align:center;"></td>'; 
			$temptablerow  .= '<td style="text-align:right;"></td>'; 
			$temptablerow  .= '<td style="text-align:right;"></td>'; 			
			$temptablerow  .= '<td style="text-align:right;">'. $obj->formatNumber($startCash).'</td>';  
			$temptablerow  .= '<td>Saldo Awal</td>';   
			$temptablerow  .= '</tr>';
			$temptablerow  .= '<tr class="detail-row rewrite-row">';
			$temptablerow  .= '<td colspan="6">';
			$temptablerow  .= '';
			$temptablerow  .= '</td>';
			$temptablerow  .= '</tr>';

			$tempreport .= $temptablerow;
		}  

		 

		for( $i=0;$i<count($rsDetail);$i++) {

			$rs = $obj->getDataRowById($rsDetail[$i]['refkey']);

			$temptablerow = ''; 

			$temptablerow  .= '<tr class="rewrite-row"> '; 
			$temptablerow  .= '<td style="text-align:center;">'. $obj->formatDBDate($rs[0][$datefield],'d / m / Y').'</td>';
			$temptablerow  .= '<td style="text-align:center;">'. $rs[0]['code'].'</td>'; 
			$temptablerow  .= '<td style="text-align:right;">'. $obj->formatNumber($rsDetail[$i]['debit']).'</td>'; 
			$temptablerow  .= '<td style="text-align:right;">'. $obj->formatNumber($rsDetail[$i]['credit']).'</td>'; 
			
        	$debitType =  $chartOfAccount->getCOAType($rsCOA[$j]['pkey']);
			$startCash += ($rsDetail[$i]['debit'] - $rsDetail[$i]['credit'])*$debitType;
			
			$temptablerow  .= '<td style="text-align:right;">'. $obj->formatNumber($startCash).'</td>';  
			$temptablerow  .= '<td>'.$rsDetail[$i]['trdesc'].'</td>';   
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
			<div class="rewrite-row">'.$rsCOA[$j]['code'].' - '.$rsCOA[$j]['name'].'</div>
			<div class="rewrite-row">Total Kas : '.$obj->formatNumber($startCash).'</div>
			<table class="rewrite-row" style="width:800px; min-width:100%; margin-bottom:1em">
				<tr class="table-header">  
					<td style="text-align:center;width:100px;" >Tgl. Transaksi</td> 
					<td style="width:100px;text-align:right; ">Kode Jurnal</td>
					<td style="width:100px;text-align:right; ">Debit</td> 
					<td style="width:100px;text-align:right;">Credit</td> 
					<td style="width:100px;text-align:right;">Saldo</td>  
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
}else{
	$obj= $cashMovement;

	if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria = '';
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		//$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
	 
	if(isset($_POST) && !empty($_POST['hidCOAKey'])) {
		$criteria .= ' AND '.$chartOfAccount->tableName.'.pkey = '.$class->oDbCon->paramString($_POST['hidCOAKey']); 
		$rsCOA = $chartOfAccount->getDataRowById($_POST['hidCOAKey']); 
		array_push($arrFilterInformation,array("label" => 'Akun', 'filter' =>  $rsCOA[0]['code'].' - '.$rsCOA[0]['name']));
	} 
 

 	$warehouseCriteria = '';
	$warehousekey = ''; 
	/*		
	if(isset($_POST) && !empty($_POST['chkWarehouse'])){ 
		$warehousekey = implode(",",$class->oDbCon->paramString($_POST['chkWarehouse'])); 
		
	 	$rsWarehouse = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$warehousekey.')');
		
		$arrTempWarehouse = array();
		for ($k=0;$k<count($rsWarehouse);$k++)
		 	array_push($arrTempWarehouse,$rsWarehouse[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempWarehouse);
		
		$warehouseCriteria .= ' AND '.$obj->tableName.'.warehousekey in ('.$warehousekey.') ';
			
		array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
		
	} 
	*/
		 
	$orderBy = 'code'; 
	$orderType = 'asc';  
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
	$rsCOA = $chartOfAccount->searchData('','',true,$criteria,$order);
	
	$tableReport = '';
	for($j=0;$j<count($rsCOA);$j++){ 
	
		$tempreport = '';  
        
        $dateMethod = $class->loadSetting('movementDateMethod');
        $datefield = 'createdon';
        if ($dateMethod == 2) 
            $datefield = 'trdate';
         
		$rs = $obj->searchData('','',true,' and  '.$obj->tableName.'.statuskey = 1 and coakey = ' .$class->oDbCon->paramString($rsCOA[$j]['pkey']) . $warehouseCriteria . ' and  '.$obj->tableName.'.'.$datefield.' between '.$class->oDbCon->paramDate($_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate($_POST['trEndDate'], ' / ','Y-m-d 23:59:59')  ,'order by  '.$obj->tableName.'.'.$datefield.'  asc');
	 
		if (empty($rs)){
			$tempreport .= '<tr class="report-row rewrite-row"><td colspan="6"></td></tr>';	
		}
		
         $date = date('d / m / Y',strtotime(str_replace('\'','',$obj->oDbCon->paramDate($_POST['trStartDate'],' / ','Y-m-d')).' -1 day'));
       	$startCash = $obj->sumCashMovement($rsCOA[$j]['pkey'], $warehousekey ,$date); 
		
		for( $i=0;$i<count($rs);$i++) {   
		 	
			$temptablerow = ''; 
	 
			$temptablerow  .= '<tr class="rewrite-row"> '; 
			$temptablerow  .= '<td style="text-align:center;">'. $obj->formatDBDate($rs[$i][$datefield],'d / m / Y').'</td>'; 
			$temptablerow  .= '<td>'.  $rs[$i]['warehousename'] .'</td>';
			$temptablerow  .= '<td style="text-align:right;">'. $obj->formatNumber($startCash).'</td>';
			$temptablerow  .= '<td style="text-align:right;">'. $obj->formatNumber($rs[$i]['amount']).'</td>'; 
			 
			$startCash += $rs[$i]['amount'];
			
			$temptablerow  .= '<td style="text-align:right;">'. $obj->formatNumber($startCash).'</td>';  
			$temptablerow  .= '<td>'.$rs[$i]['note'].'</td>';   
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
			<div class="rewrite-row">'.$rsCOA[$j]['code'].' - '.$rsCOA[$j]['name'].'</div>
			<div class="rewrite-row">Total Kas : '.$obj->formatNumber($startCash).'</div>
			<table class="rewrite-row" style="width:800px; min-width:100%; margin-bottom:1em">
				<tr class="table-header">  
					<td style="text-align:center;width:100px;" >Tgl. Transaksi</td> 
					<td style="width:100px;" >Gudang</td> 
					<td style="width:100px;text-align:right; ">Jml. Awal</td> 
					<td style="width:100px;text-align:right;">Pergerakan</td> 
					<td style="width:100px;text-align:right;">Saldo</td>  
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
}
   
$arrTwigVar['inputHidCOAKey'] = $class->input('hidden','hidCOAKey');
$arrTwigVar['inputCOAName'] =  $class->input('text','COAName'); 
$arrTwigVar['inputStartDate'] = $class->input('text','trStartDate',true,'','readonly="readonly"','form-control input-date');
$arrTwigVar['inputEndDate'] = $class->input('text','trEndDate',true,'','readonly="readonly"','form-control input-date');
      
echo $twig->render('reportCashMovement.html', $arrTwigVar);   
?>

