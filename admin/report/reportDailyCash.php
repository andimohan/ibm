<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('GeneralJournal.class.php','CashIn.class.php', 'CashOut.class.php','ChartOfAccount.class.php')); 
$generalJournal = createObjAndAddToCol(new GeneralJournal());
$cashIn = createObjAndAddToCol(new CashIn());
$cashOut = createObjAndAddToCol(new CashOut());
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());

include '_global.php';

$securityObject = 'reportDailyCash'; // the value of security object is manually inserted to handle
								  // some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$arrFilterInformation = array();
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $class->lang['dailyCashReport'];

$arrCOA = $class->convertForCombobox($chartOfAccount->searchData($chartOfAccount->tableName . '.statuskey', 1, true, ' and ' . $chartOfAccount->tableName . '.isleaf = 1'), 'pkey', 'coaname');

if (isset($_POST) && !empty($_POST['hidAction'])){

	$criteria = '';

    if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' AND ' . $generalJournal->tableName . '.trdate between ' . $generalJournal->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $generalJournal->oDbCon->paramDate($_POST['trEndDate'], ' / ', 'Y-m-d 23:59');
		array_push($arrFilterInformation, array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate']));
	}

	if (isset($_POST) && !empty($_POST['selCOA'])) {

		$key = implode(",", $class->oDbCon->paramString($_POST['selCOA']));
		$rsCriteria = $chartOfAccount->searchData('', '', true, ' and ' . $chartOfAccount->tableName . '.pkey in (' . $key . ')');

		$arrTempCOA = array();
		for ($k = 0; $k < count($rsCriteria); $k++)
			array_push($arrTempCOA, $rsCriteria[$k]['coaname']);

		$COAName = implode(", ", $arrTempCOA);
		array_push($arrFilterInformation, array("label" => 'Akun', 'filter' => $COAName));
	}
	
    $tempreport = '<div style="min-width:500px"  class="rewrite-row">'; 
	
    $tempreport .= '<table style="width:1300px; max-width:auto" class="no-odd-even-style" >';
//    $tempreport .= '<thead><tr class="table-header">';
//    $tempreport .= '<th style="width:50px;text-align:right;transform: translate(0px, 0px);">#</th>
//					<th style="width:150px;transform: translate(0px, 0px);">'.$class->lang['code'].'</th>
//					<th style="width:150px;transform: translate(0px, 0px);">'. $class->lang['reference'] .'</th>
//					<th style="width:120px;transform: translate(0px, 0px);">'. $class->lang['accountCode'] .'</th>
//					<th style="transform: translate(0px, 0px);">'. $class->lang['accountName'] .'</th>
//					<th style="width:150px;text-align:right;transform: translate(0px, 0px);">'. $class->lang['debit'].'</th>
//					<th style="width:150px;text-align:right;transform: translate(0px, 0px);">'. $class->lang['credit'] .'</th>
//					<th style="300px;transform: translate(0px, 0px);">'. $class->lang['description'] .'</th>
//				';
//    $tempreport .= '</tr></thead>';

	$rsCasInAndCashOut = generateCashInAndCashOut($_POST['selCOA'], $_POST['trStartDate'], $_POST['trEndDate']);

	$tempreport .= $rsCasInAndCashOut['report'];

	$tempreport .= '</table><br>';
    
    $tempreport .= '</div>';

	$reportResult = array();
	$reportResult['filterInformation'] = $arrFilterInformation;
 	$reportResult['content'] = $tempreport;

	if ((isset($_POST['hidExportExcel']) && $_POST['hidExportExcel'] == 1)){  

		$arrTemplate = array();
        $arrTemplate[0]['dataToExport'] = array();
        $arrTemplate[0]['filterInformation'] = $arrFilterInformation;

		$arrContent = array();
        $arrContent['cashinandcashout'] = $rsCasInAndCashOut;
        exportToExcel($arrHeaderTemplate['reportTitle'],$arrTemplate, $arrContent); 

	} else {
		echo json_encode($reportResult);
		die;
	}

} else {
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}


$arrTwigVar['inputSelCOA'] = $class->inputSelect('selCOA[]', $arrCOA, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;

echo $twig->render('reportDailyCash.html', $arrTwigVar);

function generateCashInAndCashOut($arrCOA, $startDate, $endDate) 
{
	global $class,$generalJournal, $chartOfAccount;

	$criteria = '';
	$totalCashIn = 0;
	$totalCashOut = 0;
    $cashInRows = '';
	$cashOutRows = '';
	$tabledetail = '';
	

		$rs = array();

		if (isset($_POST) && !empty($_POST['trStartDate'])) {
			$criteria .= ' AND ' . $generalJournal->tableName . '.trdate between ' . $generalJournal->oDbCon->paramDate($startDate, ' / ') . ' AND ' . $generalJournal->oDbCon->paramDate($endDate, ' / ', 'Y-m-d 23:59');
		}

		if (isset($_POST) && !empty($_POST['selCOA'])) {
			$key = implode(",", $generalJournal->oDbCon->paramString($arrCOA));
			$criteria .= ' AND ' . $generalJournal->tableNameDetail . '.coakey in (' . $key . ') ';
		}
    
		$rs = $generalJournal->generateDailyCashStatementReport($criteria, ' order by '.$generalJournal->tableName.'.trdate asc,  '.$generalJournal->tableName.'.pkey asc '); 

        $arrGLKey = array();
        $rsGLDetail = $generalJournal->getDetailWithRelatedInformation(array_unique(array_column($rs,'glkey'))); 
        $rsGLDetail = $generalJournal->reindexDetailCollections($rsGLDetail,'refkey');



		$cashIn = [];
		$cashOut = [];

		foreach ($rs as $row) {
			if ($row['debit'] > 0) {
				$cashIn[] = $row;
			} else {
				$cashOut[] = $row;
			}
		}

		// Loop PENERIMAAN KAS
        $colHeader = '<tr class="col-header">';
        $colHeader .= '<td style="text-align:right; width: 2em; font-weight:bold">'.$class->lang['number'].'</td>';
        $colHeader .= '<td style="text-align:center; width: 8em; font-weight:bold">' . $class->lang['date'] . '</td>';
        $colHeader .= '<td style="text-align:left; width: 8em; font-weight:bold">' . $class->lang['code'] . '</td>';
        $colHeader .= '<td style="text-align:left; width: 8em; font-weight:bold">' .$class->lang['reference'] . '</td>';
        $colHeader .= '<td style="text-align:left; width: 7em; font-weight:bold">' . $class->lang['accountCode'] . '</td>';
        $colHeader .= '<td style="text-align:left;width: 10em; font-weight:bold" >' . $class->lang['accountName'] . '</td>';
        $colHeader .= '<td style="text-align:right; width: 7em; font-weight:bold">' . $class->lang['debit'] . '</td>';
        $colHeader .= '<td style="text-align:right;  width: 7em; font-weight:bold">' .$class->lang['credit'] . '</td>';
        $colHeader .= '<td style="text-align:left;width: 15em; font-weight:bold" >' . $class->lang['journalAccounts'] . '</td>';
        $colHeader .= '<td style="text-align:right;width: 15em; font-weight:bold" >' . $class->lang['amount'] . '</td>';
        $colHeader .= '<td style="text-align:left; font-weight:bold">' .$class->lang['description']. '</td>';
        $colHeader .= '</tr>';
    
		$number = 1;
        $cashInRows .= $colHeader;
		foreach ($cashIn as $row) {
                        
            $arrGLDetail = $rsGLDetail[$row['glkey']]; 
            
            $accountDetail = array(); 
            $accountAmount = array();
            
            for($j=0;$j<count($arrGLDetail);$j++) { 
                array_push($accountDetail,$arrGLDetail[$j]['coacodename']);  
                
                $amount = ($arrGLDetail[$j]['debit'] > 0) ? $arrGLDetail[$j]['debit']  : $arrGLDetail[$j]['credit'] ;
                array_push($accountAmount,$class->formatNumber($amount));  
            }
            
            $desc = array(); 
//			if(isset($glDesc[$rs[$i]['pkey']]) && !empty($glDesc[$rs[$i]['pkey']])){
//                array_push($desc, $glDesc[$rs[$i]['pkey']]);  
//				
//            }else{ 
				if(!empty($row['headerdesc']))  array_push($desc,$row['headerdesc'] );
				if(!empty($row['trdesc']))  array_push($desc,$row['trdesc']);  
//			}
			
            $desc = implode('<br>',$desc);
              
			$cashInRows .= '<tr style="background-color:#fff;border-bottom:1px solid #efefef;">';
			$cashInRows .= '<td style="text-align:right;">' . $number++ . '.</td>';
			$cashInRows .= '<td style="text-align:center;">' . $class->formatDBDate($row['trdate'] ). '</td>';
			$cashInRows .= '<td style="text-align:left;">' . $row['code'] . '</td>';
			$cashInRows .= '<td style="text-align:left;">' . $row['refcode'] . '</td>';
			$cashInRows .= '<td style="text-align:left;">' . $row['coacode'] . '</td>';
			$cashInRows .= '<td style="text-align:left;">' . $row['coaname'] . '</td>';
			$cashInRows .= '<td style="text-align:right;color:#568203;">' . $generalJournal->formatNumber($row['debit']) . '</td>';
			$cashInRows .= '<td style="text-align:right;color:#C41E3A;">' . $generalJournal->formatNumber(0) . '</td>';
			$cashInRows .= '<td style="text-align:left;">' . implode('<br>',$accountDetail) . '</td>';
			$cashInRows .= '<td style="text-align:right;">' . implode('<br>',$accountAmount) . '</td>';
			$cashInRows .= '<td style="text-align:left;">' . $desc . '</td>';
			$cashInRows .= '</tr>';

			$totalCashIn += $row['debit'];
		}

     
    
		// Loop PENGELUARAN KAS
		$number = 1;
     
        $cashOutRows .= $colHeader;
		foreach ($cashOut as $row) {
            
            
            $arrGLDetail = $rsGLDetail[$row['glkey']]; 
            $accountDetail = array(); 
            $accountAmount = array();
            for($j=0;$j<count($arrGLDetail);$j++) { 
                array_push($accountDetail,$arrGLDetail[$j]['coacodename']);  
                
                $amount = ($arrGLDetail[$j]['debit'] > 0) ? $arrGLDetail[$j]['debit']  : $arrGLDetail[$j]['credit'] ;
                array_push($accountAmount,$class->formatNumber($amount));   
            }
            
            $desc = array(); 
//			if(isset($glDesc[$rs[$i]['pkey']]) && !empty($glDesc[$rs[$i]['pkey']])){
//                array_push($desc, $glDesc[$rs[$i]['pkey']]);  
//				
//            }else{ 
				if(!empty($row['headerdesc']))  array_push($desc,$row['headerdesc'] );
				if(!empty($row['trdesc']))  array_push($desc,$row['trdesc']);  
//			}
			
            $desc = implode('<br>',$desc);
            
			$cashOutRows .= '<tr style="background-color:#fff;border-bottom:1px solid #efefef;">';
			$cashOutRows .= '<td style="text-align:right;">' . $number++ . '.</td>';
			$cashOutRows .= '<td style="text-align:center;">' . $class->formatDBDate($row['trdate'] ). '</td>';
			$cashOutRows .= '<td style="text-align:left;">' . $row['code'] . '</td>';
			$cashOutRows .= '<td style="text-align:left;">' . $row['refcode'] . '</td>';
			$cashOutRows .= '<td style="text-align:left;">' . $row['coacode'] . '</td>';
			$cashOutRows .= '<td style="text-align:left;">' . $row['coaname'] . '</td>';
			$cashOutRows .= '<td style="text-align:right;color:#568203;">' . $generalJournal->formatNumber(0) . '</td>';
			$cashOutRows .= '<td style="text-align:right;color:#C41E3A;">' . $generalJournal->formatNumber($row['credit']) . '</td>';
			$cashOutRows .= '<td style="text-align:left;">' . implode('<br>',$accountDetail) . '</td>';
			$cashOutRows .= '<td style="text-align:right;">' . implode('<br>',$accountAmount) . '</td>';
			$cashOutRows .= '<td style="text-align:left;">' . $desc. '</td>';
			$cashOutRows .= '</tr>';

			$totalCashOut += $row['credit'];
		}

		
		$tabledetail .= '<tr><td colspan="8" style=" font-size:1.5em; ">PENERIMAAN KAS</td></tr>';
		
		$tabledetail .= $cashInRows;

    
		$tabledetail .= '<tr class="" style="border-top:2px solid #428bca !important; border-bottom:0;background-color:#fff">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td style="text-align:right;">'.  $generalJournal->formatNumber($totalCashIn) .'</td>
						<td style="text-align:right;">0</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>';
    
		$tabledetail .= '<tr><td colspan="8" style=" font-size:1.5em;  padding-top:1em">PENGELUARAN KAS</td></tr>';
		$tabledetail .= $cashOutRows;

		$tabledetail .= '<tr class="" style="border-top:2px solid #428bca !important; border-bottom:0;background-color:#fff">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td style="text-align:right;">0</td>
						<td style="text-align:right;">'.  $generalJournal->formatNumber($totalCashOut) .'</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>';


    $return['rs'] = $rs;
    $return['report'] = $tabledetail;

	return $return;
}


function exportToExcel($reportTitle,$arrTemplate, $arrContent){

	global $class, $generalJournal;   
    $excel = new Excel();
    
	$companyName = (isset($companyInformation['name'])) ? $companyInformation['name'] : $excel->loadSetting('companyName');
    
    $excel->activeSheet->setTitle(substr($reportTitle,0,31));
    $excel->activeSheet->setShowGridlines(false);

	// ======= WRITE HEADER
	$excel->filenameRespon = $reportTitle . '_' . $excel->userkey . time() . '.xlsx';

	$headers = ['No.', $class->lang['code'], $class->lang['reference'], $class->lang['accountCode'], $class->lang['accountName'], $class->lang['debit'], $class->lang['credit'], $class->lang['journalAccounts'], $class->lang['amount'], $class->lang['description']];
	$columnWidths = [10, 20, 25, 25, 40, 30, 30, 40, 40, 100]; //width column
	$columnCount = count($headers);
	$highestColumnAlpha = $excel->getColumnAlpha($columnCount);

	$reportHeaderRows = 1;
    $excel->activeSheet->setCellValueByColumnAndRow(1, $reportHeaderRows,$companyName);  
    $cell = $excel->activeSheet->getStyle('A'.$reportHeaderRows); 
    $cell->getFont()->getColor()->setARGB('428bca');    
    $cell->getFont()->setSize('16'); 
    $excel->activeSheet->mergeCells('A'.$reportHeaderRows.':'.$highestColumnAlpha.$reportHeaderRows);
    $reportHeaderRows++;
    
    $excel->activeSheet->setCellValueByColumnAndRow(1, $reportHeaderRows,$reportTitle);  
    $cell = $excel->activeSheet->getStyle('A'.$reportHeaderRows);   
    $cell->getFont()->setSize('16'); 
    $excel->activeSheet->mergeCells('A'.$reportHeaderRows.':'.$highestColumnAlpha.$reportHeaderRows);
    $reportHeaderRows++;

	// FILTER INFORMATION
    $filterInformation = $arrTemplate[0]['filterInformation'] ;
    foreach ($filterInformation as $item){
    	$excel->activeSheet->setCellValueByColumnAndRow(1, $reportHeaderRows,$item['label'] . ': ' . $item['filter']);  
    	$cell = $excel->activeSheet->getStyle('A'.$reportHeaderRows);    
    	$excel->activeSheet->mergeCells('A'.$reportHeaderRows.':'.$highestColumnAlpha.$reportHeaderRows);
    	$reportHeaderRows++;
    }

	$firstRow = $reportHeaderRows + 1;

	//$colToWrite = 1;

	function addHeaderTitle($headers, $excel, $colToWrite, $firstRow, $columnWidths) 
	{
		foreach ($headers as $i => $header) {
			$excel->activeSheet->setCellValueByColumnAndRow($colToWrite, $firstRow, $header);
			$columnAlpha = $excel->getColumnAlpha($colToWrite);
			$cell = $excel->activeSheet->getStyle($columnAlpha . $firstRow);

			// Style font dan background
			$cell->getFont()->setBold(true);
			//$cell->getFont()->getColor()->setARGB('FFFFFF');
			$cell->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
			//$cell->getFill()->getStartColor()->setARGB('428BCA');
			$cell->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
			$cell->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

			if (in_array($i, [0, 5, 6])) {
				$cell->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
			}


			// Set lebar kolom
			$excel->activeSheet->getColumnDimension($columnAlpha)->setAutoSize(false);
			$excel->activeSheet->getColumnDimension($columnAlpha)->setWidth($columnWidths[$i]);

			$colToWrite++;
		}
	}


	$runningRows = $firstRow + 1;

	// left side
	$rsSide = array($arrContent['cashinandcashout']);

	$repeat = 3; 
    $runningRows = $firstRow;
	function addBottomBorder($excel, $row, $highestColumnAlpha)
	{
		foreach (range('A', $highestColumnAlpha) as $colLetter) {
			$cell = $excel->activeSheet->getStyle($colLetter . $row);
			$cell->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
			$cell->getBorders()->getBottom()->getColor()->setARGB('EFEFEF');
		}
	}


	foreach($rsSide as $side){
		
		$cashIn = [];
		$cashOut = [];

		$rs = $side['rs'];
		
		$arrGLKey = array();
		$rsGLDetail = $generalJournal->getDetailWithRelatedInformation(array_unique(array_column($rs, 'glkey')));
		$rsGLDetail = $generalJournal->reindexDetailCollections($rsGLDetail, 'refkey');

		foreach ($rs as $row) {
			if ($row['debit'] > 0) {
				$cashIn[] = $row;
			} else {
				$cashOut[] = $row;
			}
		}

		$rowNumber = 1;
		$totalCashIn = 0;
		$totalCashOut = 0;

		//PENERIMAAN KAS
		$excel->activeSheet->setCellValueByColumnAndRow(1, ++$runningRows, 'PENERIMAAN KAS');
		$excel->activeSheet->mergeCells('A'.$runningRows.':'.$highestColumnAlpha.$runningRows);
		$style = $excel->activeSheet->getStyle('A' . $runningRows);
		$style->getFont()->setBold(true);
		// $style->getFont()->setUnderline(true);

		$colToWrite = 1;

		//add header title
		addHeaderTitle($headers, $excel, $colToWrite, ++$runningRows, $columnWidths);

		foreach ($cashIn as $row) {

			$arrGLDetail = $rsGLDetail[$row['glkey']];
			$accountAmount = array();
			$accountDetail = array();
			for ($j = 0; $j < count($arrGLDetail); $j++) {
				array_push($accountDetail, $arrGLDetail[$j]['coacodename']);
                $amount = ($arrGLDetail[$j]['debit'] > 0) ? $arrGLDetail[$j]['debit']  : $arrGLDetail[$j]['credit'] ;
                array_push($accountAmount,$class->formatNumber($amount));   
			}

			$desc = array();
			if (!empty($row['headerdesc']))
				array_push($desc, $row['headerdesc']);
			if (!empty($row['trdesc']))
				array_push($desc, $row['trdesc']);

			$desc = implode("\n", $desc);

			$col = 1;
			$excel->activeSheet->setCellValueByColumnAndRow($col++, ++$runningRows, $rowNumber++);
			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, $row['code']);
			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, $row['refcode']);
			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, $row['coacode']);
			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, html_entity_decode($row['coaname']));
			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, $row['debit']);
			
			$debitCell = $excel->activeSheet->getStyle('F' . $runningRows);
    		$debitCell->getFont()->getColor()->setARGB('568203'); 
			
			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, 0);
			$creditCell = $excel->activeSheet->getStyle('G' . $runningRows);
    		$creditCell->getFont()->getColor()->setARGB('C41E3A'); 

			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, html_entity_decode(implode("\n", $accountDetail)));
			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, implode("\n", $accountAmount));
			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, html_entity_decode($desc));
			$totalCashIn += $row['debit'];

			addBottomBorder($excel, $runningRows, $highestColumnAlpha);
		}
        
		//Total
		$col = 6; // Kolom debit
        $runningRows++;
		$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, $totalCashIn);
		$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, 0);

		//PENGELUARAN KAS
        $runningRows++;
    	$excel->activeSheet->setCellValueByColumnAndRow(1, ++$runningRows, 'PENGELUARAN KAS');
    	$excel->activeSheet->mergeCells('A'.$runningRows.':'.$highestColumnAlpha.$runningRows);
    	$style = $excel->activeSheet->getStyle('A' . $runningRows);
		$style->getFont()->setBold(true);
		//$style->getFont()->setUnderline(true);

		//add header title
		addHeaderTitle($headers, $excel, $colToWrite, ++$runningRows, $columnWidths);

		foreach ($cashOut as $row) {

			$arrGLDetail = $rsGLDetail[$row['glkey']];
			$accountAmount = array();
			$accountDetail = array();
			for ($j = 0; $j < count($arrGLDetail); $j++) {
				array_push($accountDetail, $arrGLDetail[$j]['coacodename']);
                $amount = ($arrGLDetail[$j]['debit'] > 0) ? $arrGLDetail[$j]['debit']  : $arrGLDetail[$j]['credit'] ;
                array_push($accountAmount,$class->formatNumber($amount));   
			}

			$desc = array();
			if (!empty($row['headerdesc']))
				array_push($desc, $row['headerdesc']);
			if (!empty($row['trdesc']))
				array_push($desc, $row['trdesc']);

			$desc = implode("\n", $desc);

			$col = 1;
			$excel->activeSheet->setCellValueByColumnAndRow($col++, ++$runningRows, $rowNumber++);
			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, $row['code']);
			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, $row['refcode']);
			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, $row['coacode']);
			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, html_entity_decode($row['coaname']));

			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, 0);
			$debitCell = $excel->activeSheet->getStyle('F' . $runningRows);
    		$debitCell->getFont()->getColor()->setARGB('568203'); 

			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, $row['credit']);
			$creditCell = $excel->activeSheet->getStyle('G' . $runningRows);
    		$creditCell->getFont()->getColor()->setARGB('C41E3A');

			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, html_entity_decode(implode("\n", $accountDetail)));
			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, implode("\n", $accountAmount));
			$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows,  html_entity_decode($desc));
			$totalCashOut += $row['credit'];

			addBottomBorder($excel, $runningRows, $highestColumnAlpha);	
		}

		//Total
		$col = 6; // Kolom debit
        $runningRows++;
		$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, 0);
		$excel->activeSheet->setCellValueByColumnAndRow($col++, $runningRows, $totalCashOut);


	}

	$excel->activeSheet->getStyle('F' . $firstRow . ':G' . $runningRows)->getNumberFormat()->setFormatCode('#,##0');


	// set rest of style
	$styleArray = [
		'alignment' => [
			'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
			'wrapText' => true
		],
		'borders' => [
			'outline' => [
				'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => ['argb' => '999999'],
			],
		],
	];
	$cell = $excel->activeSheet->getStyle('A' . $firstRow . ':' . $highestColumnAlpha . ($runningRows - 1));
	$cell->applyFromArray($styleArray);

	// autosize
	for ($i = 1; $i <= $highestColumnRow; $i++) {
		$columnAlpha = $excel->getColumnAlpha($i);
		$excel->activeSheet->getColumnDimension($columnAlpha)->setAutoSize(true);
	}
    
    $fullpath = $excel->uploadTempDoc . $excel->uploadFolder; 
    if (!file_exists($fullpath)) 
        mkdir($fullpath, 0755, true); 

    $fullpath .= $excel->filenameRespon;

    $excel->writerRespon->save($fullpath);

    header('location:'.HTTP_HOST.'download.php?temp=1&filename='.$excel->uploadFolder.$excel->filenameRespon); 
    die; 
}

?>