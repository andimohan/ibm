<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('ChartOfAccount.class.php','GeneralJournal.class.php')); 
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());
$generalJournal = createObjAndAddToCol(new GeneralJournal());

include '_global.php';
 
$securityObject = 'reportBalanceSheet'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$arrFilterInformation = array();
 
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $class->lang['monthlyReport'];

$hideEmptyAmount =  (isset($_POST['isHideEmptyAmount']) && !empty($_POST['isHideEmptyAmount'])) ? true : false;

if (isset($_POST) && !empty($_POST['hidAction'])){ 
   
	$startDate = date("1 / m / Y", strtotime($_POST['trEndDate']));
	$endDate = date("t / m / Y", strtotime($_POST['trEndDate']));
		 
    if(isset($_POST) && !empty($_POST['trEndDate'])){ 
		array_push($arrFilterInformation,array("label" => 'Periode', 'filter' => $class->toLocalDate($_POST['trEndDate'])));
	}
      
        
    $tempreport = generateReport($startDate, $endDate);
	
	$reportResult = array(); 
    $reportResult['filterInformation'] = $arrFilterInformation;  
 	$reportResult['content'] = $tempreport;
     	 
    if ((isset($_POST['hidExportExcel']) && $_POST['hidExportExcel'] == 1)){  
//        $arrTemplate = array();
//        $arrTemplate[0]['dataToExport'] = array();
//        $arrTemplate[0]['filterInformation'] = $arrFilterInformation;
//        
//        $arrContent = array();
//        $arrContent['left'] = $arrLeft;
//        $arrContent['right'] = $arrRight;
//        exportToExcel($arrHeaderTemplate['reportTitle'],$arrTemplate, $arrContent);  
    }else{ 
        echo json_encode($reportResult);
        die;
    }
    
}else{ 
	$_POST['trEndDate'] = date('F Y'); 
}
  
//$_POST['trEndDate'] = '30 / 09 / 2017';
//$arrTwigVar['inputIsHideEmptyAmount'] =  $class->inputCheckBox('isHideEmptyAmount');
$arrTwigVar['inputEndDate'] = $class->inputMonth('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  


echo $twig->render('reportFinancialSummary.html', $arrTwigVar);   
  

function updateToChildCOA($arrCOA){
	$chartOfAccount = new ChartOfAccount();
	
	foreach($arrCOA as $key=>$row){
		
		if($key == 'break') continue;
		
		$coakey = $row['coa'];
		 
		$arrChild = array();
		foreach($coakey as $coarow){ 
			$childReturn = $chartOfAccount->getChildren($coarow); // jgn kirim parameter kedua agar bisa manual add / merge
			 
			// kalo kosong, berarti bkn parent
			if (empty($childReturn))
				array_push($arrChild,$coarow);
			else
				$arrChild = array_merge($arrChild,$childReturn);
		}

		$arrCOA[$key]['coa'] = $arrChild; 
	}
	 
	return $arrCOA;
}

function countTotalAmount($arrCOA,$rsCOACol, $arrStartingBalance){
	
	$chartOfAccount = new ChartOfAccount();
	 
	// cari semua anak dari COA parent
	// nanti perlu dicek kalo pilhi coa langsung, kayanya blm ketampung
	 
	foreach($arrCOA as $key=>$groupRow){ 
		 
		if($key == 'break') continue;
		
		$arrCOA[$key]['total'] = 0; 
		foreach($groupRow['coa'] as $row){ 
			
			$coaRow = $rsCOACol[$row]; 
			
			if(!isset($arrStartingBalance[$row])) continue;
			
			$rsAmount =  $arrStartingBalance[$row];	
			
			if ($coaRow['isleaf'] == 0) continue; 
			
			if($coaRow['debittype'] == -1) $rsAmount['amount'] *= -1; // buat balikin saldo normal
			$arrCOA[$key]['total'] += $rsAmount['amount'];
		}
		 
	}
	
	return $arrCOA;
}


function generateHTML($opt){
	
	$arrCOA = (isset($opt['arrCOA'])) ? $opt['arrCOA'] : array(); 
	$arrStartingBalance = (isset($opt['arrStartingBalance'])) ? $opt['arrStartingBalance'] : array(); 
	$arrEndingCOA = (isset($opt['arrEndingCOA'])) ? $opt['arrEndingCOA'] : array(); 
	$arrEndingBalance = (isset($opt['arrEndingBalance'])) ? $opt['arrEndingBalance'] : array(); 
	$rsCOACol = (isset($opt['rsCOACol'])) ? $opt['rsCOACol'] : array();  
	$startDate = (isset($opt['startDate'])) ? $opt['startDate'] : array(); 
	$endDate = (isset($opt['endDate'])) ? $opt['endDate'] : array(); 
			  
	global $class;
	$generalJournal = new GeneralJournal();

	$html = '<style>
				.group-title {font-size:1.3em; color: #000;color: #702670 }
				.group-title .div-table-col-3 {border-bottom:1px solid #dedede }
				.account-name-col{width: 20em; }
				.amount-col{width: 8em; text-align:right}
				.parent {font-weight:bold}
				.grid {width: 65em}
				.grid-panel {float:left; padding:0; margin-bottom:2em} 
				.odd-event-style .div-table-row:nth-child(2n+3) .div-table-col-3 {background-color:#eff2f2} 
				.odd-event-style-detail .div-table-row:nth-child(2n+2) .div-table-col-3 {background-color:#eff2f2} 
				 h1 {margin-bottom:1em; font-size: 2em; color: #702670}

				.table-name {font-size:1.3em; color: #702670;  border-bottom:1px solid #dedede; width: 26.7em} 
				.text-green-avocado  {color:#568203;}
				.text-red-cardinal  {color:#C41E3A;}
				.col-header {font-weight:bold;}
				.col-header .div-table-col-3{ border-bottom:1px solid #333; }

				.div-table-col-3 {vertical-align:top}
				.table-report-detail .col-header .div-table-col-3 {background-color:#fff !important}
				.table-report-detail.odd-event-style .div-table-row .div-table-col-3  {background-color:#fff} 
				.table-report-detail.odd-event-style .div-table-row:nth-child(2n+2) .div-table-col-3 {background-color:#eff2f2} 

				.flex {display: flex; align-items:center} 
				.flex > div {margin-right: 0.5em}
				.flex > div:last-child{margin-right: 0}
				.flex .consume {flex:1}  

				</style>';

			$html .= '<h1>'.strtoupper($class->lang['monthlyReport'].' - '.$class->toLocalDate($_POST['trEndDate'])).'</h1>';
			$html .= '<div class="grid">';
 
			foreach($arrCOA as $key=>$groupRow){

				if($key == 'break'){
					$html .= '<div style="clear:both"></div>';
				}else{
					$html .='<div class="grid-panel" style="width: '.$groupRow['width'].'">'; 
					$html .='<div class="div-table odd-event-style" style="width:94%;">';

					$amount = $class->formatNumber( (isset($groupRow['ending']) && $groupRow['ending'] == 1) ? $arrEndingCOA[$key]['total'] : $arrCOA[$key]['total']);

					$html .='<div class="div-table-row group-title ">';  
					$html .='<div class="div-table-col-3 account-name-col">'.$groupRow['title'].'</div>';
					$html .='<div class="div-table-col-3 amount-col">'.$amount.'</div>';
					$html .='</div>';

					foreach($groupRow['coa'] as $row){ 
						
						
						$coaRow = $rsCOACol[$row];
						$rsAmount = (isset($groupRow['ending']) && $groupRow['ending'] == 1) ? $arrEndingBalance[$row] : $arrStartingBalance[$row];	

						if ($coaRow['isleaf'] == 0) continue;

						if (!(isset($groupRow['alwaysShow']) && $groupRow['alwaysShow'] == 1) && $rsAmount['amount'] == 0) continue;

						if($coaRow['debittype'] == -1) $rsAmount['amount'] *= -1; // buat balikin saldo normal

//						if ($coaRow['name'] == 'PIUTANG IPKL') continue;
						
						$html .='<div class="div-table-row">';  
						$html .='<div class="div-table-col-3 account-name-col">'.$coaRow['name']. ( ($coaRow['name'] == 'PIUTANG IPKL') ? ' <span style="color: #f00">*</span>' : '' ).'</div>';
						$html .='<div class="div-table-col-3 amount-col">'.$class->formatNumber($rsAmount['amount']).'</div>';
						$html .='</div>';

					}


					$html .='</div>'; 
					$html .= '</div>';
				}
  
			}

			$html .= '</div>';

	
	$html .= '<div style="clear:both; height: 1px; color: #f00">* to be confirmed</div>'; 
	$html .= '<div style="clear:both; height: 1px; margin-top:2em; page-break-before:always;"></div>';
	$html .= '<h1>DETIL BIAYA/PENGELUARAN</h1>';

 
	// khusus bagian expense saja

	$rsGL = $generalJournal->getJournalForGL($arrCOA['expense']['coa'],$startDate,$endDate);
	$rsGL = $generalJournal->reindexDetailCollections($rsGL,'coakey');    
 	
	foreach($arrCOA['expense']['coa'] as $coakey){ 

			$coaRow = $rsCOACol[$coakey];

			if ($coaRow['isleaf'] == 0 || !isset($rsGL[$coakey]) || empty($rsGL[$coakey])) continue; 

			$detailJournal = $rsGL[$coakey];
		
			$total = 0;
			$rowHTML = '';
			foreach($detailJournal as $glRow){ 

				$amount = ($glRow['debit'] > 0) ? $glRow['debit'] : ($glRow['credit'] * -1);

				$rowHTML .='<div class="div-table-row">';  
				$rowHTML .='<div class="div-table-col-3" style="width:8em; text-aling:center">'.$class->formatDBDate($glRow['trdate']).'</div>';
				$rowHTML .='<div class="div-table-col-3 ">'. $glRow['trdesc'] .'</div>';
				$rowHTML .='<div class="div-table-col-3 amount-col">'.$class->formatNumber($amount).'</div>';
				$rowHTML .='</div>';
				
				$total += $amount;
			}
		
			$html .= '<div style="page-break-inside: avoid;">';
			$html .='<div class="flex table-name"><div class="consume">'.$coaRow['name'].'</div><div>'.$class->formatNumber($total).'</div></div>';
 			$html .='<div class="div-table table-cost-detail odd-event-style-detail" style="width:35em; margin-bottom:1.5em;">';  
		
			$html .= $rowHTML;

			$html .='</div>';
			$html .='</div>';


	} 	
	
	 
	return $html;
}


// nanti dipindah ke class
function generateReport($startDate,$endDate){
	
	global $class;
	global $chartOfAccount;
	global $generalJournal;
	
	$reportType = 2;
	
	// predefined COA
	// 1 : saldo posisi neraca, selalu akumulasi dari awal
	// 2 : saldo start selalu dari 0
	// 3 : saldo per tanggal (saldo awal perperiode)
	
	$arrCOA =  array();
	
 
		$arrCOA['bank'] = array('title'=> 'SALDO AWAL KAS/BANK', 'type' => 3, 'width'=>'33.33%', 'alwaysShow' => 1,'coa'=> array(8002));
		$arrCOA['endingbank'] = array('title'=> 'SALDO AKHIR KAS/BANK', 'type' => 3,'ending'=>1, 'width'=>'33.33%', 'alwaysShow' => 1, 'coa'=> array(8002));
		$arrCOA['ar'] = array('title'=> 'PIUTANG','type' => 1, 'width'=>'33.33%',  'coa'=> array(20));  
//		$arrCOA['deposit'] = array('title'=> 'HUTANG/DEPOSIT WARGA','type' => 1,  'coa'=> array(8038));  
		$arrCOA['break'] = '';
		$arrCOA['income'] =  array('title'=> 'PENDAPATAN', 'type' => 2, 'width'=>'50%', 'coa'=> array(4));   
		$arrCOA['expense'] =  array('title'=> 'PENGELUARAN','type' => 2, 'width'=>'50%',  'coa'=> array(6));    
			
	 
	$arrCOA = updateToChildCOA($arrCOA);
	
	
	// select jd colelction dulu
	$arrCOAKey = array();
	foreach($arrCOA as $key=>$row) {
		
		if($key == 'break') continue;
		$arrCOAKey = array_merge($arrCOAKey, $row['coa']);  
	} 
	$rsCOACol = $chartOfAccount->searchDataRow(array($chartOfAccount->tableName.'.pkey', $chartOfAccount->tableName.'.name', $chartOfAccount->tableName.'.isleaf', $chartOfAccount->tableName.'.debittype'),
											  ' and ' . $chartOfAccount->tableName.'.pkey in ('.$chartOfAccount->oDbCon->paramString( $arrCOAKey,',' ).') ');
	
	$rsCOACol = array_column($rsCOACol,null,'pkey');
	
	// khusus buat saldo akhir kas/bank
	$arrEndingCOA =  array();
	$arrEndingCOA['bank'] = array('title'=> 'SALDO AKHIR KAS/BANK', 'alwaysShow' => 1,'type' => 3, 'coa'=> array(22,8002));
	$arrEndingCOA = updateToChildCOA($arrEndingCOA);
	
	 
	// posisi neraca
	
	// ambil berdasarkan jenis nya karena balance sheet dan pendpatan bulanan beda. posisi neraca pasti dari awal 
	$arrStartingBalance = array();
	
	foreach($arrCOA as $key=>$row){ 
			
		if($key == 'break') continue;
		
		$coaCriteria = ' and  '.$chartOfAccount->tableName.'.pkey in ('.$class->oDbCon->paramString( $row['coa'],',' ).') '; 
		
		if(isset($row['ending']) && $row['ending'] == 1){
			// khusus catat ending balance
			$critStartDate =  '';  
			$critEndDate =  $endDate;
			$arrEndingBalance = array_merge( $arrStartingBalance,  $chartOfAccount->sumRunningAmount($critStartDate, $critEndDate, $coaCriteria) );
			
		}else{
			$critStartDate = ($row['type'] == 2) ? $startDate : ''; 
			$beforeDate =  date('d / m / Y',strtotime( str_replace('\'','',$class->oDbCon->paramDate($startDate)) .' -1 day'));  
			$critEndDate = ($row['type'] == 3) ? $beforeDate : $endDate;
			$arrStartingBalance = array_merge( $arrStartingBalance,  $chartOfAccount->sumRunningAmount($critStartDate, $critEndDate, $coaCriteria) );
		}
		 
		
	} 
	$arrStartingBalance = array_column($arrStartingBalance, null,'pkey');
	$arrEndingBalance = array_column($arrEndingBalance, null,'pkey');
	
	
	// hitung total dulu
 	$arrCOA = countTotalAmount($arrCOA,$rsCOACol, $arrStartingBalance);
 	$arrEndingCOA = countTotalAmount($arrCOA,$rsCOACol, $arrEndingBalance);
  	
	$html = generateHTML(array('arrCOA' => $arrCOA,
							  'arrStartingBalance' => $arrStartingBalance,
							  'arrEndingCOA' => $arrEndingCOA,
							  'arrEndingBalance' => $arrEndingBalance,
							  'rsCOACol' => $rsCOACol,
//							  'reportType' => $reportType,
							  'startDate' => $startDate,
							  'endDate'=>$endDate, 
							  )
						);
	 
	return $html;
}
?>