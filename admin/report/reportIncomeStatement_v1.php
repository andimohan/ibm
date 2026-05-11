<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('ChartOfAccount.class.php'); 
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());

include '_global.php';

$securityObject = 'reportIncomeStatement'; // the value of security object is manually inserted to handle
								  // some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$arrFilterInformation = array();
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $class->lang['incomeStatementReport'];

if (isset($_POST) && !empty($_POST['hidAction'])){
  
    if(isset($_POST) && !empty($_POST['trEndDate'])){
		 array_push($arrFilterInformation,array("label" => $class->lang['period'], 'filter' => $_POST['trEndDate'] ));
        
         $_POST['trEndDate']  = date('t / m / Y',strtotime($_POST['trEndDate']));
	}
    
    $_POST['trStartDate'] = '01 / 01 / 2000';
    
    //$class->setLog($_POST['trStartDate'] . ' ' . $_POST['trEndDate'],true);
      
    $tempreport = '<div style="min-width:500px"  class="rewrite-row">'; 

    // dummy for column width
    $tempreport .= '<table style="table-layout:fixed;  width:100%" >';
    $tempreport .= '<thead><tr>';
    $tempreport .= '<th style="width:70%">';
       /* $tempreport .= 'Akun';*/
    $tempreport .= '</th>';
    $tempreport .= '<th style="text-align:right">';
       /* $tempreport .= 'Total';*/
    $tempreport .= '</th>';
    $tempreport .= '</tr></thead>';


    $rsIncome = generateIncome(array('income'), $_POST['trStartDate'], $_POST['trEndDate'],  ucwords($class->lang['totalIncome'])); 
    $income = $rsIncome['amount'];
    $tempreport .= $rsIncome['report'];
    
    $tempreport .= '<tr class="row-plain"><td style="height:2em"></td><td></td></tr>';
    
    $rsExpense = generateIncome(array('expense'), $_POST['trStartDate'], $_POST['trEndDate'], ucwords($class->lang['totalExpense'])); 
    $cost = $rsExpense['amount'];
    $tempreport .= $rsExpense['report'];
    
    // test dulu UTK ETI / TEL
    $balanceInPositive = $chartOfAccount->loadSetting('GLAsPositiveBalance');
    $balanceInPositive = ($balanceInPositive) ? -1 : 1;
    
    // hitung selisih saja agar lebih cepat
    $tempreport .= generateTotalIncomeStatement( $income + ($cost * $balanceInPositive) );
 
    $tempreport .= '</table><br>';
    
    $tempreport .= '</div>';
    $tempreport .= '<script>$(".expand-link").bind( "click", function( event ) { expandLevel($(this));});</script>'; 

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
        exportToExcel($arrHeaderTemplate['reportTitle'],$arrTemplate, $arrContent);  
    }else{ 
        echo json_encode($reportResult);
        die;
    }
}else{
    $_POST['trStartDate'] = date('F Y');
	$_POST['trEndDate'] = date('F Y'); 
}

//test 
//$_POST['trStartDate'] = '01 / 07 / 2017';
//$_POST['trEndDate'] = '30 / 09 / 2017';

$arrTwigVar['inputStartDate'] = $class->inputMonth('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputMonth('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  
echo $twig->render('reportIncomeStatement.html', $arrTwigVar);

function generateIncome($arrCOAType,$startDt,$endDt,$totalText){
    global $chartOfAccount; 
    
    $defaultShowedLevel = 1;
    
    $rsCOAKey = $chartOfAccount->searchData('','',true,' and '.$chartOfAccount->tableName.'.coatype in ('.implode(',',$chartOfAccount->oDbCon->paramString($arrCOAType)).')');  
    
    $total = 0;  
    $tabledetail = '';   
    $arrCriteria = array();
     
    for($i=0;$i<count($rsCOAKey);$i++) {  
        array_push($arrCriteria,$chartOfAccount->tableName.'.pkey = \''.$rsCOAKey[$i]['pkey'].'\''); 
        array_push($arrCriteria,$chartOfAccount->tableName.'.rootkey = \''.$rsCOAKey[$i]['pkey'].'\''); 
    }
     
    $coaCriteria = ' and ('.implode(' or ' , $arrCriteria).')';
    $rs = $chartOfAccount->sumRunningAmount($startDt,$endDt, $coaCriteria,FINANCIAL_REPORT['incomeStatement'],-1); 
  
    // test dulu UTK ETI / TEL
    $balanceInPositive = $chartOfAccount->loadSetting('GLAsPositiveBalance');
    $balanceInPositive = ($balanceInPositive) ? -1 : 1;

    for ($i=0;$i<count($rs);$i++){ 

            // soalnya sudah diinvert di function sum nya
            if($rs[$i]['debittype'] == 1)
                $rs[$i]['amount'] *= $balanceInPositive;
                
            $headerStyle = '';
            if ($rs[$i]['isleaf'] == 0)
               $headerStyle = 'expand-link clickable '; 
            else
                $total += $rs[$i]['amount'];

            $displayStyle =($rs[$i]['level'] > $defaultShowedLevel) ? "display:none;": "";  


            if ($rs[$i]['level'] < $defaultShowedLevel && $rs[$i]['isleaf'] == 0)  
                $headerStyle .= ' expand '; 

            $boldStyle = ($rs[$i]['isleaf'] == 0)  ? ' font-weight:bold; ' : '';
            $firstRowStyle = ($i == 0) ? ' border-top:1px solid #000; ' : '';

            $coaname = '<table class="no-padding-margin">';
            $coaname .= '<tr>'; 
            $coaname .= '<td style="width:'.($rs[$i]['level']*2).'em"></td>';
            $coaname .= '<td style="'.$boldStyle.'">'.$rs[$i]['code'].'</td>';
            $coaname .= '<td style="'.$boldStyle.' width:1em; text-align:center;">-</td>';
            $coaname .= '<td style="'.$boldStyle.'">'.$rs[$i]['name'].'</td>';
            $coaname .= '</tr>';
            $coaname .= '</table>';

            if ($rs[$i]['isleaf'] == 1 && !empty($rs[$i]['pkey'])){ 
                $GLStartDate = str_replace('\'','',$chartOfAccount->oDbCon->paramDate($startDt,' / ','Y-m-d'));
                $GLEndDate = str_replace('\'','',$chartOfAccount->oDbCon->paramDate($endDt,' / ','Y-m-d'));

                $coaname = '<a class="link-gl-detail" href="reportGeneralLedger/'.$rs[$i]['pkey'].'/'.$GLStartDate.'/'.$GLEndDate.'" target="_blank">'.$coaname.'</a>';  
            }

            $tabledetail .= '<tr class="row-plain '.$rs[$i]['rootpath'].' '.$headerStyle.' "  style="'.$boldStyle.$displayStyle.'" relParentId="'.$rs[$i]['parentkey'].'" relId="'.$rs[$i]['pkey'].'">';
            $tabledetail .= '<td style="'.$firstRowStyle.'">';  
            $tabledetail .=  $coaname;
            $tabledetail .= '</td>';
            $tabledetail .= '<td style="'.$firstRowStyle.' text-align:right">';
                $tabledetail .= $chartOfAccount->formatNumber($rs[$i]['amount']);
            $tabledetail .= '</td>';
            $tabledetail .= '</tr>';
    }

    $tabledetail .= '<tr >';
        $tabledetail .= '<td class="footer-group">'.$totalText.'</td>';
        $tabledetail .= '<td class="footer-group" style="text-align:right">';
            $tabledetail .= $chartOfAccount->formatNumber($total);
        $tabledetail .= '</td>';
    $tabledetail .= '</tr>'; 

    $return['amount'] = $total;
    $return['rs'] = $rs;
    $return['report'] = $tabledetail;

    return $return;
} 

function generateTotalIncomeStatement($amount){
    global $chartOfAccount;  
  
    if($amount == 0)
        $bgStyle = 'bg-gray-dim';
    else
        $bgStyle = ($amount < 0) ? 'bg-red-cardinal' : 'bg-green-avocado';
    
    $tabledetail = '';
    $tabledetail .= '<table style="table-layout:fixed; width:100%; margin-top:1em;" >';

    $tabledetail .= '<tr class="row-plain" >';
    $tabledetail .= '<td class="footer-group '.$bgStyle.'">'.ucwords($chartOfAccount->lang['profitLoss']).'</td>';
    $tabledetail .= '<td class="footer-group '.$bgStyle.'" style="text-align:right">';
    $tabledetail .= $chartOfAccount->formatNumber($amount);
    $tabledetail .= '</td>';
    $tabledetail .= '</tr>';

    $tabledetail .= '</table>';

	return $tabledetail;
}

function exportToExcel($reportTitle,$arrTemplate, $arrContent){ 
      global $class;   
      $excel = new Excel();
    
      $highestColumnRow = 2;
      $highestColumnAlpha = $excel->getColumnAlpha($highestColumnRow);
        
      $companyName = (isset($companyInformation['name'])) ? $companyInformation['name'] : $excel->loadSetting('companyName');
    
      $excel->activeSheet->setTitle(substr($reportTitle,0,31));
      $excel->activeSheet->setShowGridlines(false);
        
    
      // ======= WRITE HEADER
      $excel->filenameRespon = $reportTitle.'_'.$excel->userkey.time().'.xlsx';
          
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
      
      // left side
      $arrContent['income']['title'] = strtoupper($class->lang['totalIncome']);
      $arrContent['expense']['title'] = strtoupper($class->lang['totalExpense']);
      $rsSide = array($arrContent['income'], $arrContent['expense']);
    
     $repeat = 3; 
     $runningRows = $firstRow;
     foreach($rsSide as $side){
          $rs = $side['rs'];
          
          foreach($rs as $row){
              
            $el = array();
              
            if($row['level'] == 0 && $runningRows <> $firstRow)   
                $runningRows++;
            
            $el['bold'] = ($row['isleaf'] == 0) ? true : false; 
              
            $colToWrite = 1; 
            $excel->activeSheet->setCellValueByColumnAndRow($colToWrite, $runningRows, $row['code'] .' - '. $row['name'] );
            $columnAlpha = $excel->getColumnAlpha($colToWrite); 
            $cell = $excel->activeSheet->getStyle($columnAlpha.$runningRows);    
            $el['indent'] = $row['level'] * $repeat;
            $excel->formatCell($cell,$el); 
            $colToWrite++;
   
            $excel->activeSheet->setCellValueByColumnAndRow($colToWrite, $runningRows, $row['amount']);
            $columnAlpha = $excel->getColumnAlpha($colToWrite); 
            $cell = $excel->activeSheet->getStyle($columnAlpha.$runningRows);  
            $el['format'] = 'number';
            $el['indent'] = 0;
            $excel->formatCell($cell,$el); 
            $colToWrite++; 
  
            $runningRows++;
            
          } 
           
            $el = array();
            $colToWrite= 1;
            $excel->activeSheet->setCellValueByColumnAndRow($colToWrite, $runningRows, $side['title']);
            $columnAlpha = $excel->getColumnAlpha($colToWrite); 
            $cell = $excel->activeSheet->getStyle($columnAlpha.$runningRows);  
            $el['bold'] = true; 
            $el['indent'] = 0;
            $excel->formatCell($cell,$el); 
            $colToWrite++;
         
            $excel->activeSheet->setCellValueByColumnAndRow($colToWrite, $runningRows, $side['amount']);
            $columnAlpha = $excel->getColumnAlpha($colToWrite); 
            $cell = $excel->activeSheet->getStyle($columnAlpha.$runningRows);  
            $el['bold'] = true;
            $el['format'] = 'number';
            $el['indent'] = 0;
            $excel->formatCell($cell,$el); 
            $colToWrite++;
         

            $styleArray = [ 
            'font' => [
                        'bold' => true, 
                        'color' =>  ['argb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 
                'startColor' => [
                    'argb' => '428bca',
                ],
            ], 
            ]; 

            $cell = $excel->activeSheet->getStyle('A'.$runningRows.':'.$highestColumnAlpha.$runningRows);
            $cell->applyFromArray($styleArray);

            $runningRows++; 
           
     }
     
        $profitLoss = $rsSide[0]['amount'] + $rsSide[1]['amount'];
            
        $el = array();
        $colToWrite= 1;
        $runningRows++;
        $excel->activeSheet->setCellValueByColumnAndRow($colToWrite, $runningRows,strtoupper($class->lang['profitLoss']));
        $columnAlpha = $excel->getColumnAlpha($colToWrite); 
        $cell = $excel->activeSheet->getStyle($columnAlpha.$runningRows);  
        $el['bold'] = true; 
        $el['indent'] = 0;
        $excel->formatCell($cell,$el); 
        $colToWrite++;
     
        $excel->activeSheet->setCellValueByColumnAndRow($colToWrite, $runningRows,$profitLoss);
        $columnAlpha = $excel->getColumnAlpha($colToWrite); 
        $cell = $excel->activeSheet->getStyle($columnAlpha.$runningRows);  
        $el['bold'] = true;
        $el['format'] = 'number';
        $el['indent'] = 0;
        $excel->formatCell($cell,$el); 

        if($profitLoss == 0 )
            $color = '696969';
        else 
            $color = ($profitLoss > 0) ? '568203' : 'C41E3A';
            
        $styleArray = [ 
        'font' => [
                    'bold' => true, 
                    'color' =>  ['argb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 
            'startColor' => [
                'argb' =>  $color,
            ],
        ], 
        ]; 

        $cell = $excel->activeSheet->getStyle('A'.$runningRows.':'.$highestColumnAlpha.$runningRows);
        $cell->applyFromArray($styleArray);

        $runningRows++;

        //  ======= STYLING   
        // HEADER TABLE STYLE 
        $excel->activeSheet->freezePane("A".$firstRow);
       
      
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
        $cell = $excel->activeSheet->getStyle('A'.$firstRow.':'.$highestColumnAlpha.($runningRows-1));
        $cell->applyFromArray($styleArray);
 
        // autosize
        for($i=1;$i<=$highestColumnRow;$i++){ 
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
