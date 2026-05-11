<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('ChartOfAccount.class.php'); 
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());

include '_global.php';
 
$securityObject = 'reportBalanceSheet'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class

 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$arrFilterInformation = array();
 
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $class->lang['balanceSheetReport'];

$hideEmptyAmount =  (isset($_POST['isHideEmptyAmount']) && !empty($_POST['isHideEmptyAmount'])) ? true : false;

if (isset($_POST) && !empty($_POST['hidAction'])){ 
   
    if(isset($_POST) && !empty($_POST['trEndDate'])){ 
		array_push($arrFilterInformation,array("label" => 'Per Tanggal', 'filter' => $_POST['trEndDate'] ));
	}
     
    
    $arrLeft = generateCol(array('assets'),$_POST['trEndDate']);
    $arrRight = generateCol(array('liability','equity'),$_POST['trEndDate'], -1); 
        
    $tempreport = '<div style="min-width:800px" class="rewrite-row">';
    $tempreport .= $arrLeft['html']; 
    $tempreport .= $arrRight['html']; 
    $tempreport .= '<div style="clear:both; height: 0.5em"></div>'; 
    $tempreport .= '<table style="table-layout:fixed; width:48%; float:left; margin:0 5px; text-align:right"><tr><td class="footer-group">'.$class->formatNumber($arrLeft['total']).'</td></tr></table>'; 
    $tempreport .= '<table style="table-layout:fixed; width:48%; float:left; margin:0 5px; text-align:right"><tr><td class="footer-group">'.$class->formatNumber($arrRight['total']).'</td></tr></table>'; 
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
        $arrContent['left'] = $arrLeft;
        $arrContent['right'] = $arrRight;
        exportToExcel($arrHeaderTemplate['reportTitle'],$arrTemplate, $arrContent);  
    }else{ 
        echo json_encode($reportResult);
        die;
    }
    
}else{ 
	$_POST['trEndDate'] = date('d / m / Y'); 
}
  
//$_POST['trEndDate'] = '30 / 09 / 2017';
$arrTwigVar['inputIsHideEmptyAmount'] =  $class->inputCheckBox('isHideEmptyAmount');
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  


echo $twig->render('reportBalanceSheet.html', $arrTwigVar);   

function generateCol($arrCOAType,$endDt,$invert = 1){ 
    global $chartOfAccount; 
    global $hideEmptyAmount;
	
    $defaultShowedLevel = 1;
    
    $rsCOAKey = $chartOfAccount->searchData('','',true,' and '.$chartOfAccount->tableName.'.coatype in ('.implode(',',$chartOfAccount->oDbCon->paramString($arrCOAType)).')'); 
     
    $total = 0; 
	    
    $arrCriteria = array();
    for($i=0;$i<count($rsCOAKey);$i++) {  
        array_push($arrCriteria,$chartOfAccount->tableName.'.pkey = \''.$rsCOAKey[$i]['pkey'].'\''); 
        array_push($arrCriteria,$chartOfAccount->tableName.'.rootkey = \''.$rsCOAKey[$i]['pkey'].'\''); 
    }
      
                            
    $coaCriteria = ' and ('.implode(' or ' , $arrCriteria).')';   
    
    $rs = $chartOfAccount->sumRunningAmount('',$endDt, $coaCriteria,FINANCIAL_REPORT['balanceSheet'],$invert); 
  
//    $chartOfAccount->setLog($rs,true);
    
			
            // dummy for column width
			$tabledetail = '';
			$tabledetail .= '<table style="table-layout:fixed; width:48%; float:left; margin:0 5px" >';
			$tabledetail .= '<thead><tr>';
			$tabledetail .= '<td style="width:70%">'; 
			$tabledetail .= '</td>';
			$tabledetail .= '<td style="text-align:right">'; 
			$tabledetail .= '</td>'; 
			$tabledetail .= '</tr></thead>';
			
			for ($i=0;$i<count($rs);$i++){   
				 
                	if ($hideEmptyAmount && $rs[$i]['amount'] == 0) continue; // berarti kalo parentnya 0, meskipun detailny ad isi, akan ke skip
				
                    $headerStyle = ''; 
                    if ($rs[$i]['isleaf'] == 0)
                       $headerStyle = 'expand-link clickable '; 
                    else
                        $total += $rs[$i]['amount'];
                     
                    $displayStyle =($rs[$i]['level'] > $defaultShowedLevel) ? "display:none;": "";  
                
                    if ($rs[$i]['level'] < $defaultShowedLevel && $rs[$i]['isleaf'] == 0) 
                        $headerStyle .= ' expand ';
                     
                                    
                    $boldStyle = ($rs[$i]['isleaf'] == 0) ? ' font-weight:bold; ' : '';
                    $firstRowStyle = ($i == 0) ? ' border-top:1px solid #000; ' : '';
                
                    $coaname = '';
                 
                    $coaname .= '<table class="no-padding-margin">';
                    $coaname .= '<tr>';     
                    $coaname .= '<td style="width:'.($rs[$i]['level']*2).'em"></td>';
                    $coaname .= '<td style="'.$boldStyle.'">'.$rs[$i]['code'].'</td>';
                    $coaname .= '<td style="'.$boldStyle.' width:1em; text-align:center;">-</td>';
                    $coaname .= '<td style="'.$boldStyle.'">'.$rs[$i]['name'].'</td>';
                    $coaname .= '</tr>';
                    $coaname .= '</table>';
                
                
                    if ($rs[$i]['isleaf'] == 1 && !empty($rs[$i]['pkey'])){ 
                        $GLStartDate = str_replace('\'','',$chartOfAccount->oDbCon->paramDate($endDt,' / ','Y'));
                        $GLStartDate = date($GLStartDate.'-01-01');
                        $GLEndDate = str_replace('\'','',$chartOfAccount->oDbCon->paramDate($endDt,' / ','Y-m-d'));
       	
                        $coaname = '<a class="link-gl-detail" href="reportGeneralLedger/'.$rs[$i]['pkey'].'/'.$GLStartDate.'/'.$GLEndDate.'" target="_blank">'.$coaname.'</a>';  
                    } 
                
             		$tabledetail .= '<tr class="row-plain '.$rs[$i]['rootpath'].' '.$headerStyle.' "  style="'.$boldStyle.$displayStyle.'" relParentId="'.$rs[$i]['parentkey'].'" relId="'.$rs[$i]['pkey'].'">';
					$tabledetail .= '<td style="'.$firstRowStyle.'">';
						$tabledetail .=$coaname ; 
					$tabledetail .= '</td>';
					$tabledetail .= '<td style="'.$firstRowStyle.' text-align:right">';
						$tabledetail .= $chartOfAccount->formatNumber($rs[$i]['amount']); 
					$tabledetail .= '</td>'; 
					$tabledetail .= '</tr>';  
			}
	  
			$tabledetail .= '</table>';
			 
		 return array('html' => $tabledetail, 'rs' => $rs, 'total' => $total);
}   

function exportToExcel($reportTitle,$arrTemplate, $arrContent){ 
       
      $excel = new Excel();
    
      $highestColumnRow = 5;
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
      $highestRow = $firstRow;
      
      // left side
      $rsSide = array($arrContent['left'], $arrContent['right']);
    
     $repeat = 3;
     $colOffset = 0;
     foreach($rsSide as $side){
          $rs = $side['rs'];
        
          $runningRows = $firstRow;
          
          foreach($rs as $row){
              
            $el = array();
              
            if($row['level'] == 0 && $runningRows <> $firstRow)   
                $runningRows++;
            
            $el['bold'] = ($row['isleaf'] == 0) ? true : false; 
            $row['name'] = html_entity_decode(strip_tags($row['name']));  
            $colToWrite = 1 + $colOffset; 
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
         
            if( $runningRows > $highestRow) $highestRow = $runningRows;
         
            $colOffset += 3;
          
     }
    
        $highestRow++;
    
        $styleArray = [ 
        'font' => [
                    'bold' => true, 
                    'color' =>  ['argb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 
            'startColor' => [
                'argb' =>  '428bca',
            ],
        ], 
        ]; 

    
        // TOTAL
        $colToWrite = 2;
        $excel->activeSheet->setCellValueByColumnAndRow($colToWrite, $highestRow, $rsSide[0]['total']);
        $columnAlpha = $excel->getColumnAlpha($colToWrite); 
        $cell = $excel->activeSheet->getStyle($columnAlpha.$highestRow);   
        $excel->formatCell($cell,array('bold'=>true, 'format' => 'number')); 
        $cell = $excel->activeSheet->getStyle('A'.$highestRow.':'.$columnAlpha.$highestRow);
        $cell->applyFromArray($styleArray);

        $colToWrite = 5;
        $excel->activeSheet->setCellValueByColumnAndRow($colToWrite, $highestRow, $rsSide[1]['total']);
        $columnAlpha = $excel->getColumnAlpha($colToWrite); 
        $cell = $excel->activeSheet->getStyle($columnAlpha.$highestRow);   
        $excel->formatCell($cell,array('bold'=>true, 'format' => 'number')); 
        $cell = $excel->activeSheet->getStyle( $excel->getColumnAlpha(4).$highestRow.':'.$columnAlpha.$highestRow);
        $cell->applyFromArray($styleArray);

        $highestRow++;
     
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
        $cell = $excel->activeSheet->getStyle('A'.$firstRow.':'.$highestColumnAlpha.($highestRow-1));
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