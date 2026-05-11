<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('ChartOfAccount.class.php'); 
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());
$warehouse = createObjAndAddToCol(new Warehouse());

$_POST['hidTotalFreezeCol'] = 1;
 
include '_global.php';


// ===== khusus save excel report HTML
if ($EXPORT_TYPE == 3 && isset($_POST['hidFileData']) && !empty($_POST['hidFileData'])) {
    //$start_time = microtime(TRUE);

    $excel = new Excel();

    $fileIndexName = $_POST['hidFileData'];

    $path = $class->uploadTempDoc . 'export-data/';
    $fileName = $path . $fileIndexName;

    $handle = fopen($fileName, "r");
    $contents = fread($handle, filesize($fileName));
    //$contents = gzuncompress($contents);

    $dataFromFile = json_decode($contents, true);

    $excel->exportToSaveFromArray($dataFromFile);

    die;
}
// ===== khusus save excel report HTML



$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
 
$securityObject = 'reportIncomeStatement'; // the value of security object is manually inserted to handle
								  // some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$arrFilterInformation = array();
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $class->lang['incomeStatementReport'];

$firstPeriod = $chartOfAccount->getFirstPeriod()[0]['runningmonth'];
$firstYearPeriod = $class->formatDBDate($firstPeriod,'Y');


$arrAvailableYear = $class->generateYearSelectBox('',(date('Y')-$firstYearPeriod));
$arrAvailableYearReverse = $class->generateYearSelectBox('',(date('Y')-$firstYearPeriod),true);
$arrAvailableMonth = $class->generateMonthSelectBox();

$balanceAsPositive = $chartOfAccount->loadSetting('GLAsPositiveBalance');
$balanceAsPositive = ($balanceAsPositive) ? -1 : 1;

define('BALANCE_AS_POSITIVE',$balanceAsPositive);

$hideEmptyAmount =  (isset($_POST['isHideEmptyAmount']) && !empty($_POST['isHideEmptyAmount'])) ? true : false;

// biar gampang dikirim/global, jadiin 1 array aj
$EXPORT_EXCEL_DATA = array(); 
$EXPORT_EXCEL_DATA['dataToExport'] = array();
$EXPORT_EXCEL_DATA['dataRowIndex'] = 0;
$EXPORT_EXCEL_DATA['dataColIndex'] = 0;

// style default
$EXPORT_EXCEL_DATA['class']['col-header'] =  [ 
												'font' => [
													'bold' => true,
													 'color' => ['argb' => '2b55a2']
												],
												 'alignment' => [
													'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
												],
										];
		
$EXPORT_EXCEL_DATA['class']['row-parent'] =  [ 
												'font' => [
													'bold' => true,
												],
	
												 'alignment' => [
													'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
												],
										];
$EXPORT_EXCEL_DATA['class']['amount-parent'] =  [ 
												'font' => [
													'bold' => true,
												], 
										];

$EXPORT_EXCEL_DATA['class']['subtotal'] =  [ 
												'font' => [ 
													'color' => ['argb' => '2b55a2']
												],
										];

$EXPORT_EXCEL_DATA['class']['row-header'] =  [ 
												'font' => [ 
													'bold' => true,
												],
										];

$EXPORT_EXCEL_DATA['class']['balance-profit'] =  [ 
												'font' => [ 
													'bold' => true,
													'color' => ['argb' => '568203']
												],
										];


$EXPORT_EXCEL_DATA['class']['balance-loss'] =  [ 
												'font' => [ 
													'bold' => true,
													'color' => ['argb' => 'c41e3a']
												],
										];



if (isset($_POST) && !empty($_POST['hidAction'])){
  
	$reportType = $_POST['selReportType'];
	
	// validasi tahun
	if($_POST['trYearEndDate'] < $_POST['trYearStartDate']) $_POST['trYearEndDate'] = $_POST['trYearStartDate'];
 
      
    $tempreport = '<div style="min-width:500px"  class="rewrite-row">'; 

	
	$maxWidth = 'width:100%';
	if($reportType == 0){
		$maxWidth = 'width:50em; max-width: 100%';
	}
	
    // dummy for column width
	$tempreport .= '<div class="report-income-statement">';
    $tempreport .= '<table class="no-odd-even-style row-border-bottom" style="table-layout:fixed;'.$maxWidth.'" >';
    $tempreport .= '<thead><tr>'; 
    $tempreport .= '<th class="top-left-freeze" style="width:20em; font-weight:bold; "></th>';
	 
	$arrIncome  = array();
	$arrIncomeTotal  = array(); 
	
	$arrExpense  = array();
	$arrExpenseTotal  = array(); 
	
	// gk boleh empty array karena harus dicek berdasarkan akses user
//	$selectedWarehouse = ((!empty($_POST['selWarehouse']))) ? $_POST['selWarehouse'] : array(); 
	$selectedWarehouse = ((!empty($_POST['selWarehouse']))) ? $_POST['selWarehouse'] : array_keys($arrWarehouse);
	
	$totalGroupYOY = 0;
	
	$tempRow = array();
	 
    array_push($tempRow, array('value' => ''));
    array_push($tempRow, array('value' => ''));
	
	switch($reportType){
		
		case 0:
		  
				$rsIncome = generateIncome(array('income'), $_POST['trStartDate'], $_POST['trEndDate'], $selectedWarehouse ); 
				$arrIncome[$_POST['trStartDate']] = $rsIncome;

				$rsExpense = generateIncome(array('expense'), $_POST['trStartDate'], $_POST['trEndDate'], $selectedWarehouse ); 
				$arrExpense[$_POST['trStartDate']] = $rsExpense;
			
				$tempreport .= '<th></th>';
			 
				array_push($tempRow, array('value' => ''));
			
				array_push($arrFilterInformation,array("label" => $class->lang['period'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
			
				break;
			
		case 1: $startPeriod = date_format(new DateTime($_POST['trMonthStartDate']), '01 / m / Y');
				$endPeriod = date_format(new DateTime($_POST['trMonthEndDate']), 't / m / Y');
			
				$arrIncome =  $chartOfAccount->getCOAAmount(array('income'),$startPeriod,$endPeriod,$reportType,$selectedWarehouse);
				$arrExpense =  $chartOfAccount->getCOAAmount(array('expense'),$startPeriod,$endPeriod,$reportType,$selectedWarehouse);
 
				foreach($arrIncome as $key=>$row) { 
					
					$dateLabel = strtoupper(str_replace('\'','',$class->oDbCon->paramDate($key,' / ','F Y')));
					$tempreport .= '<th style="text-align:right; width: 9em; font-weight:bold">'.$dateLabel.'</th>'; 
					
					array_push($tempRow, array('value' => $dateLabel, 'style' => $EXPORT_EXCEL_DATA['class']['col-header']));
					 
				}
			
				$labelTotal = strtoupper($class->lang['total']);
				$tempreport .= '<th style="text-align:right; width: 10em; font-weight:bold">'.$labelTotal.'</th>';
				
				array_push($tempRow, array('value' => $labelTotal, 'style' => $EXPORT_EXCEL_DATA['class']['col-header']));
			
				array_push($arrFilterInformation,array("label" => $class->lang['period'], 'filter' => $startPeriod . ' - ' .$endPeriod));
			
				break;
		case 2: 
				$startPeriod =  '01 / 01 / ' .intval($_POST['trYearStartDate']);
				$endPeriod = '31 / 12 / ' .intval($_POST['trYearEndDate']);
			
				$arrIncome =  $chartOfAccount->getCOAAmount(array('income'),$startPeriod,$endPeriod,$reportType,$selectedWarehouse);
				$arrExpense =  $chartOfAccount->getCOAAmount(array('expense'),$startPeriod,$endPeriod,$reportType,$selectedWarehouse);

				foreach($arrIncome as $key=>$row) { 
					$dateLabel = strtoupper(str_replace('\'','',$class->oDbCon->paramDate($key,' / ','Y')));
					$tempreport .= '<th style="text-align:right; width: 9em; font-weight:bold">'.$dateLabel.'</th>'; 
					
					array_push($tempRow, array('value' => $dateLabel, 'style' => $EXPORT_EXCEL_DATA['class']['col-header']));
				}
			
				$labelTotal = strtoupper($class->lang['total']);
				$tempreport .= '<th style="text-align:right; width: 10em; font-weight:bold">'.$labelTotal.'</th>';
			
				array_push($tempRow, array('value' => $labelTotal, 'style' => $EXPORT_EXCEL_DATA['class']['col-header']));
			
				array_push($arrFilterInformation,array("label" => $class->lang['period'], 'filter' => $startPeriod . ' - ' .$endPeriod));
				break;
			 
		case 3: 
				$startPeriod =  '01 / 01 / ' .intval($_POST['trQuarterlyStartDate']);
				$endPeriod = '31 / 12 / ' .intval($_POST['trQuarterlyStartDate']);
			
				$arrIncome =  $chartOfAccount->getCOAAmount(array('income'),$startPeriod,$endPeriod,$reportType,$selectedWarehouse);
				$arrExpense =  $chartOfAccount->getCOAAmount(array('expense'),$startPeriod,$endPeriod,$reportType,$selectedWarehouse);

				// pasti terbagi 4
				for($i=1;$i<=4;$i++){  
					$tempreport .= '<th style="text-align:right; width: 8em; font-weight:bold">Q'.$i.'</th>'; 
					
					array_push($tempRow, array('value' => 'Q'.$i, 'style' => $EXPORT_EXCEL_DATA['class']['col-header'])); 
				}
			
				$labelTotal = strtoupper($class->lang['total']);
				$tempreport .= '<th style="text-align:right; width: 8em; font-weight:bold">'.$labelTotal.'</th>';
				array_push($tempRow, array('value' => $labelTotal, 'style' => $EXPORT_EXCEL_DATA['class']['col-header']));
			
				array_push($arrFilterInformation,array("label" => $class->lang['period'], 'filter' => $startPeriod . ' - ' .$endPeriod));
				break;
			 			 
		case 4: 
				$arrIncome = array();
				$arrExpense = array();
				$arrYear = $_POST['trQuarterlyYOYYear'];
				sort($arrYear);
			
				foreach($arrYear as $yearRow){
					$startPeriod =  '01 / 01 / ' .$yearRow;
					$endPeriod = '31 / 12 / ' .$yearRow;
					$arrIncome= array_merge($arrIncome, $chartOfAccount->getCOAAmount(array('income'),$startPeriod,$endPeriod,$reportType,$selectedWarehouse));
					$arrExpense= array_merge($arrExpense,  $chartOfAccount->getCOAAmount(array('expense'),$startPeriod,$endPeriod,$reportType,$selectedWarehouse)); 
				}
				
				$totalLoop = 4;
				$totalYear = count($arrYear);
				$totalGroupYOY = $totalYear;
			
				$arrIncome = sortYOY($arrIncome,$arrYear,$totalLoop);
				$arrExpense = sortYOY($arrExpense,$arrYear,$totalLoop);
			 
					 
				for($i=1;$i<=$totalLoop;$i++) { 
					for($j=0;$j<$totalYear;$j++){ 
						
						$tempLabel = 'Q'.$i.' '.$arrYear[$j];
						$tempreport .= '<th style="text-align:right; width: 8em; font-weight:bold">'.$tempLabel.'</th>';  
						
						array_push($tempRow, array('value' => $tempLabel, 'style' => $EXPORT_EXCEL_DATA['class']['col-header']));  
					}
						
				}
			
				array_push($arrFilterInformation,array("label" => $class->lang['period'], 'filter' => implode(', ',$arrYear)));
				break;
			
						 			 
		case 5: 
				$arrIncome = array();
				$arrExpense = array();
				$arrYear = $_POST['trMonthlyYOYYear'];
				$arrMonth = $_POST['trMonthlyYOYMonth'];
			 
				sort($arrYear);
 
			
				foreach($arrMonth as $monthRow){
					foreach($arrYear as $yearRow){
						$startPeriod =  '01 / '.$monthRow.' / ' .$yearRow; 
						$endPeriod = date_format(new DateTime($yearRow.'/'.$monthRow.'/01'), 't / m / Y');
						 
						$arrIncome= array_merge($arrIncome, $chartOfAccount->getCOAAmount(array('income'),$startPeriod,$endPeriod,$reportType,$selectedWarehouse));
						$arrExpense= array_merge($arrExpense,  $chartOfAccount->getCOAAmount(array('expense'),$startPeriod,$endPeriod,$reportType,$selectedWarehouse)); 
					}
				}
				
				$totalLoop = count($arrMonth);
				$totalYear = count($arrYear);
				$totalGroupYOY = $totalYear;
	 
					 
				for($i=0;$i<$totalLoop;$i++) { 
					for($j=0;$j<$totalYear;$j++){
						
						$tempLabel = strtoupper($arrAvailableMonth[$arrMonth[$i]]).' '.$arrYear[$j];
						$tempreport .= '<th style="text-align:right; width: 8em; font-weight:bold">'.$tempLabel.'</th>';  
						
						array_push($tempRow, array('value' => $tempLabel, 'style' => $EXPORT_EXCEL_DATA['class']['col-header']));  
						
						//addToDataToExport($EXPORT_EXCEL_DATA['headerToExport'],$EXPORT_EXCEL_DATA['dataRowIndex'],$EXPORT_EXCEL_DATA['dataColIndex']++,strtoupper($arrAvailableMonth[$arrMonth[$i]]).' '.$arrYear[$j]);
					}
						
				}
			
				$arrMonthFilterLabel = array();
				foreach($arrMonth as $row)
				 array_push($arrMonthFilterLabel,$arrAvailableMonth[$row]);
				
				array_push($arrFilterInformation,array("label" => $class->lang['month'], 'filter' => implode(', ', $arrMonthFilterLabel)));
				array_push($arrFilterInformation,array("label" => $class->lang['year'], 'filter' => implode(', ',$arrYear)));
				break;
						
	}
  
	// ============== loop data utk JSON AI
	
	$arrDataJSON = array('companyName' => $class->loadSetting('companyName'),
						'companyType' => PLAN_TYPE['categorykey'],
						'action' => 'incomeStatement',
						);
	
	$dataCOA = array();
	
	$arrCOACategory = array( '1' => 'Aktiva', '2' => 'Pasiva', '3' => 'Modal', '4' => 'Pendapatan', '5' => 'Biaya Operasional', '6' => ' Beban Operasional', '7' => "Biaya dan Pendapatan Non Operasional");
	$arrAllCOA = array($arrIncome,$arrExpense);
	foreach($arrAllCOA as $setRow){
		
		foreach($setRow as $datePeriode=> $row){
			$indexPeriod = str_replace('\'','',$class->oDbCon->paramDate($datePeriode,' / ','Y-m'));
			$datePeriodeLabel = str_replace('\'','',$class->oDbCon->paramDate($datePeriode,' / ','F Y'));
			if(!isset($dataCOA[$indexPeriod])) $dataCOA[$indexPeriod] = array();

			$rsTemp = $row['rs'];
			foreach($rsTemp as $coaRow){ 
				array_push($dataCOA[$indexPeriod],
							array('period' => $datePeriodeLabel,
								  'code' => $coaRow['code'],
								  'name' => $coaRow['name'],
								  'amount' => $coaRow['amount'],
								  'category' => $arrCOACategory[$coaRow['rootkey']]
								 )
						  ); 
			}
		}  
		
	}
	
	$arrDataJSON['data'] = $dataCOA;
	$arrDataJSON = json_encode($arrDataJSON);
	// ============== loop data utk JSON AI
	 

	
	$tempreport .= '</tr></thead>'; 
	$tempreport .= '<tbody>';
	
	pushExcelDataRow($tempRow);

	$colOddClass = (in_array($reportType,array(4,5)) && $totalGroupYOY > 0) ? 'yoy-style yoy-style-'.$totalGroupYOY : '';
	
		 
	// ======================================= INCOME =======================================
	
	$tempRow = array();
	
	$arrCOAValue = getCOAList($arrIncome); 
	$tempreport .= generateRow($arrCOAValue,$arrIncomeTotal,$reportType,$totalGroupYOY);
	 
    $tempreport .= '<tr class="subtotal"><th></th>';
	 
    array_push($tempRow, array('value' => ''));
    array_push($tempRow, array('value' => ''));
	
//    $arrTempColumn = array();
	foreach($arrIncomeTotal as $row) {  
		$tempreport .= '<td class="'.$colOddClass.'" style="text-align:right;">'.$class->formatNumber($row).'</td>';  
		array_push($tempRow, array('value' => $row, 'style' => $EXPORT_EXCEL_DATA['class']['subtotal'], 'format' =>  array('format'=>'number') ) );
	}
     
          
 	$tempreport .= '</tr>';
	pushExcelDataRow($tempRow);
	
	$totalColspan = count($arrIncomeTotal); 
	
    $tempreport .= '<tr><td colspan="'.($totalColspan+1).'" style="height:2em"></td></tr>';
	
    $tempRow = array();
    array_push($tempRow, array('value' => ''));
	pushExcelDataRow($tempRow);
	
	// ======================================= EXPENSE =======================================
	
	$tempRow = array();
	
	$arrCOAValue = getCOAList($arrExpense); 
	$tempreport .= generateRow($arrCOAValue,$arrExpenseTotal,$reportType,$totalGroupYOY);
	
    $tempreport .= '<tr class="subtotal"><th></th>';
	
	array_push($tempRow, array('value' => '') );
	array_push($tempRow, array('value' => '') );
	
	// khusus expense total dikali dengan balance as positif
	foreach($arrExpenseTotal as $key=>$row)  {  
		$tempreport .= '<td  class="'.$colOddClass.'"  style="text-align:right;">'.$class->formatNumber($row*BALANCE_AS_POSITIVE).'</td>';  
		array_push($tempRow, array('value' => $row, 'style' => $EXPORT_EXCEL_DATA['class']['subtotal'], 'format' => array('format'=>'number') ) );
	}

	
 	$tempreport .= '</tr>';
	pushExcelDataRow($tempRow);
	
    $tempreport .= '<tr><td colspan="'.($totalColspan+1).'" style="height:2em"></td></tr>';
 
    $tempRow = array();
    array_push($tempRow, array('value' => ''));
	pushExcelDataRow($tempRow);
	
	// ======================================= BALANCE =======================================
	 
	$tempRow = array();
	 
	$tempreport .= '<tr><th style="font-weight:bold;">'.strtoupper($class->lang['profitLoss']).'</th>'; 
	
	array_push($tempRow, array('value' => '') );
	array_push($tempRow, array('value' => strtoupper($class->lang['profitLoss']),'style' => $EXPORT_EXCEL_DATA['class']['row-header']) );
	
	foreach($arrIncomeTotal as $key=>$row) {
		$profitloss = $arrIncomeTotal[$key] + $arrExpenseTotal[$key];
		
		if($profitloss == 0){ 
			$textStyle = 'text-gray-dim';
			$excelStyle = $EXPORT_EXCEL_DATA['class']['row-header'];
		}else{ 
			$textStyle = ($profitloss < 0) ? 'text-red-cardinal' : 'text-green-avocado'; 
			$excelStyle = ($profitloss < 0) ?  $EXPORT_EXCEL_DATA['class']['balance-loss'] :  $EXPORT_EXCEL_DATA['class']['balance-profit'];
		}

		$tempreport .= '<td class="'.$colOddClass.' '.$textStyle.'"  style="text-align:right;">'.$class->formatNumber($profitloss).'</td>';  
		
		
		array_push($tempRow, array('value' => $profitloss,  'format'=> array('format' =>'decimal') , 'style'  =>$excelStyle ));
	}
	 
	pushExcelDataRow($tempRow);
	
	$tempreport .= '</tr>';
	
	$tempreport .= '</tbody>';
    $tempreport .= '</table><br>';
    
    $tempreport .= '</div>';
    $tempreport .= '</div>';
    $tempreport .= '<script>$(".expand-link").bind( "click", function( event ) { expandLevel($(this));});</script>'; 
	// ======== nulis ke json  
	 
	// init file json yang mau di write ke temp utk export excel
	$arrWriteExcel = array();
	$arrWriteExcel['arrTemplate'] = array(
                                            array('reportTitle' =>  $arrHeaderTemplate['reportTitle'],
                                                  'dataToExport' => $EXPORT_EXCEL_DATA['dataToExport'],
                                                  'filterInformation' => $arrFilterInformation
                                          )
                                    );
     
	
	$path = $class->uploadTempDoc.'export-data/';  
	if (!is_dir($path)) mkdir ($path,  0755, true);

	$fileDataName = time().$class->userkey.rand();
	$fileDataPath = fopen($path.$fileDataName, "w");
 
	$contentToWrite = json_encode($arrWriteExcel);

	fwrite($fileDataPath, $contentToWrite); 
	fclose($fileDataPath); 
	
	// utk AI 
	$path = $class->uploadTempDoc.'data-ai/';  
	if (!is_dir($path)) mkdir ($path,  0755, true);
	$fileDataPath = fopen($path.$fileDataName, "w");  
	fwrite($fileDataPath, $arrDataJSON); 
	fclose($fileDataPath);
	
	// ======== nulis ke json  

	
	$reportResult = array();
	$reportResult['filterInformation'] = $arrFilterInformation;
 	$reportResult['content'] = $tempreport;
 	$reportResult['fileData'] = $fileDataName;

	echo json_encode($reportResult);
	die;
	
}else{
    $_POST['trStartDate'] = date('01 / 01 / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
  
    $_POST['trMonthStartDate'] = date_format(date_create(date('Y-01-01')),"F Y");
	$_POST['trMonthEndDate'] = date('F Y');
	
    $_POST['trYearStartDate'] = $firstYearPeriod;
	$_POST['trYearEndDate'] = date('Y');
}

$arrReportType = array();
$arrReportType[0] = array('label' => $class->lang['periodically']);
$arrReportType[1] = array('label' => $class->lang['monthly']);
$arrReportType[2] = array('label' => $class->lang['annualy']);
$arrReportType[3] = array('label' => $class->lang['quarterly']);
$arrReportType[4] = array('label' => $class->lang['quarterly']. ' YOY');
$arrReportType[5] = array('label' => $class->lang['yearOverYear']);

$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelReportType'] = $class->inputSelect('selReportType', $arrReportType);  
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));

// gk bisa disamakan setiap select box karena klao cuma di hide di htmlnya, tetep kekirim datannya (apalagi kalo ama selcetboxnya sama)
$arrTwigVar['inputMonthStartDate'] = $class->inputMonth('trMonthStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputMonthEndDate'] = $class->inputMonth('trMonthEndDate', array('etc' => 'style="text-align:center"'));

$arrTwigVar['inputYearStartDate'] = $class->inputSelect('trYearStartDate',$arrAvailableYearReverse);
$arrTwigVar['inputYearEndDate'] = $class->inputSelect('trYearEndDate',$arrAvailableYearReverse);

$arrTwigVar['inputQuarterlyStartDate'] = $class->inputSelect('trQuarterlyStartDate',$arrAvailableYearReverse);

$arrTwigVar['inputQuarterlyYOYYear'] = $class->inputSelect('trQuarterlyYOYYear[]',$arrAvailableYearReverse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['inputMonthlyYOYYear'] = $class->inputSelect('trMonthlyYOYYear[]',$arrAvailableYearReverse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputMonthlyYOYMonth'] = $class->inputSelect('trMonthlyYOYMonth[]',$arrAvailableMonth, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['inputIsHideEmptyAmount'] = $class->inputCheckBox('isHideEmptyAmount');

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  

$arrTwigVar['AIAnalysis'] = true;


echo $twig->render('reportIncomeStatement.html', $arrTwigVar);

function pushExcelDataRow($arrCol){
	global $EXPORT_EXCEL_DATA;
	$colIndex = 0;
	
    $arrRow = array();
    foreach($arrCol as $col)
		array_push($arrRow, array(   'excelValue' => $col['value'],
                                    'format' => isset($col['format']) ? $col['format'] : array(),
                                    'style' => isset($col['style']) ? $col['style'] : array(),
                                    'class' => isset($col['class']) ? $col['class'] : array(),)
                  );
    
    array_push($EXPORT_EXCEL_DATA['dataToExport'], $arrRow);
        
	$EXPORT_EXCEL_DATA['dataRowIndex']++;
}
 
function sortYOY($arrIncome,$arrYear, $totalLoop = 4){
	global $class;
	
	$arrNewKey = array();
	$arrNewIncome = array(); 
 
	$totalYear = count($arrYear);
	$arrIncomeKeys = array_keys($arrIncome);

//	$class->setLog('sort ---',true);
//	$class->setLog($arrIncomeKeys,true); 

	//push ke array baru agar urut per kuartal
	for($i=0;$i<$totalLoop;$i++){  
		$startKeyIndex = $i; 
		for($j=0;$j<$totalYear;$j++){ 
			 array_push($arrNewKey, $arrIncomeKeys[$startKeyIndex]); 
			 $startKeyIndex += $totalLoop;
		} 
	}

	foreach($arrNewKey as $keyIndex) 
		$arrNewIncome[$keyIndex] = $arrIncome[$keyIndex]; 

//	$class->setLog(array_keys($arrNewIncome),true); 
	return $arrNewIncome;
}

function getEndDate($currDate,$reportType){
	global $class;
	
	// hati2, obj date selalu dikirim dalam bentuk reference
	// jd harus diconvert dulu agar tidak merubah nilai asli jika mau di add interval
	
	switch($reportType){
	
		case 1 : $periodInterval = 1;  
			     $formattedEndDate = date_format($currDate,'t / m / Y');
				 break;
		case 2 : $periodInterval = 12;  
			     $formattedEndDate = date_format($currDate,'31 / 12 / Y');
				 break;
		case 3 : 
		case 4 : $periodInterval = 3;  
				 $endDate = new DateTime(date_format($currDate,'Y-m-d')); 
				 $endDate->add(new DateInterval('P2M'));  
			     $formattedEndDate = date_format($endDate,'t / m / Y');
				 break; 
		case 5 : $periodInterval = 1;  
			     $formattedEndDate = date_format($currDate,'t / m / Y');
				 break; 
	}
	 
	return $formattedEndDate;
}

/*
function getCOAAmount($arrCOAType, $startPeriod,$endPeriod,$reportType,$arrWarehouseKey){
	global $class;
	
	
	// $startPeriod : d / M / Y
	// $periodInterval dalam bulan
	
	$periodInterval = 1;
	$periodStartDateFormat = '01 / m / Y';
		
	switch($reportType){
	
		case 1 : $periodInterval = 1;
				 $periodStartDateFormat = '01 / m / Y';
				 break;
		case 2 : $periodInterval = 12;
				 $periodStartDateFormat = '01 / 01 / Y';
				 break;
		case 3 : 
		case 4 : $periodInterval = 3;
				 $periodStartDateFormat = '01 / m / Y';
				 break; 
		case 5 : $periodInterval = 1;
				 $periodStartDateFormat = '01 / m / Y';
				 break; 
	}

 	

	// patokan dalam detik, tgl berakhir
	
	$uTimeEnd = str_replace('\'','',$class->oDbCon->paramDate($endPeriod,' / ','Y-m-d')); 
	$uTimeEnd = date_format( new DateTime($uTimeEnd),'U'); 

	$arrIncome = array();
	$ctr = 0;  

	// convert balik ke standart format waktu
	$currDate = str_replace('\'','',$class->oDbCon->paramDate($startPeriod,' / ','Y-m-d')); 
	$currDate = new DateTime($currDate);

	do{

		if($ctr > 0){ 
			$currDate->add(new DateInterval('P'.$periodInterval.'M'));  
			if(date_format($currDate,'U') > $uTimeEnd) break;
		}
 
		
		$formattedCurrDate = date_format($currDate,$periodStartDateFormat);
		$formattedEndDate = getEndDate($currDate,$reportType); //date_format($currDate,$periodEndDateFormat);
		
//		$class->setLog($formattedCurrDate. ' ---- '. $formattedEndDate,true);
		$rsIncome = generateIncome($arrCOAType, $formattedCurrDate, $formattedEndDate, $arrWarehouseKey ); 
 
		$arrIncome[$formattedCurrDate] = $rsIncome;

		$ctr++;

		// buat jaga2
		if($ctr > 23) break;

	}while (true);
	
	return $arrIncome;
}
*/

function getCOAList($arrIncome){ 	
	$arrCOAValue = array();
	
	foreach($arrIncome[array_keys($arrIncome)[0]]['rs'] as $row){ 
		
		$row['periodamount'] =  array();
		foreach($arrIncome as $key=>$periodRow){
			
		 	$coakey = $row['pkey'];
			//$row['periodamount'][$key] = $periodRow['rs'][$coakey]['amount']; 
			$row['periodamount'][$key]['amount'] = $periodRow['rs'][$coakey]['amount']; 
			$row['periodamount'][$key]['balanceAsPositive'] = $periodRow['rs'][$coakey]['balanceAsPositive']; 
		}
		
		array_push($arrCOAValue, $row);
	}
	
	return $arrCOAValue;
}

function generateRow($arrCOAValue, &$arrIncomeTotal, $reportType, $totalGroupYOY=0){
	global $class;
	global $chartOfAccount;
	global $EXPORT_EXCEL_DATA;
	global $hideEmptyAmount;
	  
	$defaultShowedLevel = 1;
	$returnVal = '';
	
	$colOddClass = '';// (in_array($reportType,array(4))) ? 'yoy-event-style' : ''; 
	if(in_array($reportType,array(4,5)) && $totalGroupYOY > 0){
		$colOddClass = 'yoy-style yoy-style-'.$totalGroupYOY;
	}
	
	foreach($arrCOAValue as $coakey=>$row){
		$rowHtml = '';
		$hasNonZeroAmount = false;
		
		//init ulang 
        $tempRow = array();
		
		$parentStyle = ($row['isleaf'] == 0 ) ? 'font-weight:bold;' : ''; 
		$displayStyle = ($row['level'] > $defaultShowedLevel) ? "display:none;": "";  
		
		$headerStyle = '';
		if ($row['isleaf'] == 0) $headerStyle = 'expand-link clickable ';  
		if ($row['level'] < $defaultShowedLevel && $row['isleaf'] == 0)   $headerStyle .= ' expand '; 
		
		 $rowHtml .= '<tr style="'.$displayStyle.'"  class="'.$headerStyle. ' '.$row['rootpath'].'" relParentId="'.$row['parentkey'].'" relId="'.$row['pkey'].'">'; 
		 $rowHtml .= '<th class="freeze-pane-border" style="'.$parentStyle.'; padding-left: '.($row['level']*2).'em" >';
		 
		 $coaNameDiv = '<div class="flex align-items-flex-start"><div>'.$row['code'].'</div><div class="consume">'.$row['name'].'</div></div>';
        

		$excelParentStyle = ($row['isleaf'] == 0) ? $EXPORT_EXCEL_DATA['class']['row-parent'] : array();
		$excelParentAmountStyle = ($row['isleaf'] == 0) ? $EXPORT_EXCEL_DATA['class']['amount-parent'] : array();
		 
         array_push($tempRow, array('value' => $row['code'], 'style'  =>$excelParentStyle,  'format'=> array('format' =>'string') ));
         array_push($tempRow, array('value' => $row['name'], 'style'  =>$excelParentStyle ));
        
		if ($row['isleaf'] == 1)
			$coaNameDiv = '<a href="'.$row['gl-url'].'" target="_blank">'.$coaNameDiv.'</a>';
		
		$rowHtml .= $coaNameDiv;
		$rowHtml .= '</th>';
		
		 // loop per periode
		 $totalAmount = 0; 
		 foreach($row['periodamount'] as $periodkey => $periodRow){   
			 
			 $coaAmount =  $periodRow['amount'] * $periodRow['balanceAsPositive']; 

			if ($coaAmount != 0)
				$hasNonZeroAmount = true;
			 
			 $rowHtml .= '<td class="'.$colOddClass.'" style="text-align:right; '.$parentStyle. '">'.$class->formatNumber($coaAmount).'</td>';
			  
		
			 if(!isset($arrIncomeTotal[$periodkey])) $arrIncomeTotal[$periodkey] = 0; 
			 
			 if($row['isleaf'] == 1)
			 	$arrIncomeTotal[$periodkey] += $periodRow['amount'];
			 
			 $totalAmount += ($periodRow['amount']* $periodRow['balanceAsPositive']);
			 array_push($tempRow, array('value' => $coaAmount, 'format'=> array('format' =>'decimal'), 'style'  =>$excelParentAmountStyle ));
			  
		 }
        
		
		if(in_array($reportType, array(1,2,3))){ 
			$rowHtml .= '<td style="text-align:right;'.$parentStyle.'">'. $class->formatNumber($totalAmount).'</td>';  
			array_push($tempRow, array('value' => $totalAmount, 'format'=> array('format' =>'decimal'), 'style'  =>$excelParentAmountStyle ));
		}
		
         
		$rowHtml .= '</tr>'; 

		if ($hideEmptyAmount && !$hasNonZeroAmount) {
			continue;
		}
		
		$returnVal .= $rowHtml;
        pushExcelDataRow($tempRow);
	}
    
    
	
	if(in_array($reportType, array(1,2,3))){
		
		$totalAmount = 0;
		foreach($arrIncomeTotal as $row) $totalAmount += $row;

		$arrIncomeTotal['total'] = $totalAmount; 
	}
		
	return $returnVal;
}

function generateIncome($arrCOAType,$startDt,$endDt,$arrWarehouse){
	
	// $startDt : d / m / Y
	
    global $chartOfAccount; 
     
    $rsCOAKey = $chartOfAccount->searchData('','',true,' and '.$chartOfAccount->tableName.'.coatype in ('.implode(',',$chartOfAccount->oDbCon->paramString($arrCOAType)).')');  
     
    $arrCriteria = array();
     
    for($i=0;$i<count($rsCOAKey);$i++) {  
        array_push($arrCriteria,$chartOfAccount->tableName.'.pkey = \''.$rsCOAKey[$i]['pkey'].'\''); 
        array_push($arrCriteria,$chartOfAccount->tableName.'.rootkey = \''.$rsCOAKey[$i]['pkey'].'\''); 
    }
     
    $coaCriteria = ' and ('.implode(' or ' , $arrCriteria).')';
    $rs = $chartOfAccount->sumRunningAmount($startDt,$endDt, $coaCriteria,FINANCIAL_REPORT['incomeStatement'],-1,$arrWarehouse); 
   
        
    for ($i=0;$i<count($rs);$i++){ 
  
			$GLStartDate = str_replace('\'','',$chartOfAccount->oDbCon->paramDate($startDt,' / ','Y-01-01'));
			$GLEndDate = str_replace('\'','',$chartOfAccount->oDbCon->paramDate($endDt,' / ','Y-12-31'));
			$rs[$i]['gl-url'] = 'reportGeneralLedger/'.$rs[$i]['pkey'].'/'.$GLStartDate.'/'.$GLEndDate;
			  
			$rs[$i]['balanceAsPositive'] = 1; // init
			if($rs[$i]['debittype'] == 1)  $rs[$i]['balanceAsPositive'] *= BALANCE_AS_POSITIVE; 
			if(BALANCE_AS_POSITIVE == -1 && ($rs[$i]['pkey'] == 5 || $rs[$i]['rootkey'] == 5))   $rs[$i]['balanceAsPositive'] *= BALANCE_AS_POSITIVE;
		
    }
  
    $return['rs'] = array_column($rs,null,'pkey'); // agar mudah diakses per coa

    return $return;
} 

?>
