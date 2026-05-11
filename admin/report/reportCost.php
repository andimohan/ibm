<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('CostGrouping.class.php'); 
$obj = createObjAndAddToCol(new CostGrouping());
$warehouse = createObjAndAddToCol(new Warehouse());

include '_global.php';
 
$securityObject = 'reportCost'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class

 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$arrFilterInformation = array();
 
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $class->lang['costReport'];
$defaultShowedLevel = 0;

if (isset($_POST) && !empty($_POST['hidAction'])){ 
     
    $arrCost = $obj->generateTreeReport($_POST['trStartDate'],$_POST['trEndDate'], $_POST['selWarehouse']);  
    
    $tempreport = '<table>';
    foreach($arrCost as $row){ 
        
        if ($row['isleaf'] == 1)   $total += $row['amount'];
            
        $boldStyle = ($row['isleaf'] == 0 || $row['parentkey'] == 0) ? 'font-weight:bold' : '';
        $expand = ($row['isleaf'] == 0) ? ' expand-link clickable ' : ''; 
        $displayStyle =($row['level'] > $defaultShowedLevel) ? " display:none; ": "";  
        
        
        $tempreport .= '<tr class="row-plain '.$expand. ' ' .$row['rootpath']. '" style="'.$displayStyle.'" relParentId="'.$row['parentkey'].'" relId="'.$row['pkey'].'">'; 
        $tempreport .= '<td> <table class="no-padding-margin"><tr><td style="width:'.($row['level']*2).'em"></td><td  style="'.$boldStyle.'">'.$row['name'].'</td></table></td>';
        $tempreport .= '<td style="width:20em; text-align:right">'.$obj->formatNumber($row['amount']).'</td>';
        $tempreport .= '</tr>';
    }
    
    $tempreport .= '<tr class="row-plain" style="border-top:2px solid #333 !important; border-bottom:0">'; 
    $tempreport .= '<td> <table class="no-padding-margin"><tr><td></td><td  style="font-weight:bold">'.strtoupper($obj->lang['total']).'</td></table></td>';
    $tempreport .= '<td style="width:20em; text-align:right">'.$obj->formatNumber($total).'</td>';
    $tempreport .= '</tr>';
    $tempreport .= '</table>';
    
    array_push($arrFilterInformation,array("label" => $obj->lang['period'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
    

    if(!empty($_POST['selWarehouse'])){
        $key = $_POST['selWarehouse'];
        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$obj->oDbCon->paramString($key,',').')');

        $arrTempStatus = array();
        for ($k=0;$k<count($rsCriteria);$k++)
        array_push($arrTempStatus,$rsCriteria[$k]['name']);

        $warehouseName = implode(", ",$arrTempStatus); 
        array_push($arrFilterInformation,array("label" => $obj->lang['warehouse'], 'filter' => $warehouseName )); 

    }
  
     
    $tempreport .= '<script>$(".expand-link").bind( "click", function( event ) { expandLevel($(this));});</script>';
    
	$reportResult = array(); 
    $reportResult['filterInformation'] = $arrFilterInformation;  
 	$reportResult['content'] = $tempreport;
     	 
    if ((isset($_POST['hidExportExcel']) && $_POST['hidExportExcel'] == 1)){  
        $arrTemplate = array();
        $arrTemplate[0]['dataToExport'] = array();
        $arrTemplate[0]['filterInformation'] = $arrFilterInformation;
        
        $arrContent = array();
        $arrContent['rs'] = $arrCost; 
        $arrContent['total'] = $total; 
        exportToExcel($arrHeaderTemplate['reportTitle'],$arrTemplate, $arrContent);  
    }else{ 
        echo json_encode($reportResult);
        die;
    }
    
}else{ 
	$_POST['trStartDate'] = date('d / m / Y'); 
	$_POST['trEndDate'] = date('d / m / Y'); 
}
   
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  


echo $twig->render('reportCost.html', $arrTwigVar);   

 
function exportToExcel($reportTitle,$arrTemplate, $arrContent){ 
       
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
      $repeat = 3; 
    
      $rs = $arrContent['rs'];

      $runningRows = $firstRow;

      foreach($rs as $row){

        $el = array();
 
        $el['bold'] = ($row['isleaf'] == 0 || $row['parentkey'] == 0) ? true : false; 
        $row['name'] = html_entity_decode(strip_tags($row['name']));  
           
        $colToWrite = 1; 
        $excel->activeSheet->setCellValueByColumnAndRow($colToWrite, $runningRows, $row['name']  );
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
        $el['bold'] =  true;
        $colToWrite = 1; 
        $excel->activeSheet->setCellValueByColumnAndRow($colToWrite, $runningRows, 'TOTAL');
        $columnAlpha = $excel->getColumnAlpha($colToWrite); 
        $cell = $excel->activeSheet->getStyle($columnAlpha.$runningRows);    
        $el['indent'] = 0;
        $excel->formatCell($cell,$el); 
        $colToWrite++;

        $excel->activeSheet->setCellValueByColumnAndRow($colToWrite, $runningRows, $arrContent['total']);
        $columnAlpha = $excel->getColumnAlpha($colToWrite); 
        $cell = $excel->activeSheet->getStyle($columnAlpha.$runningRows);  
        $el['format'] = 'number';
        $el['indent'] = 0;
        $excel->formatCell($cell,$el); 
        $colToWrite++; 


        
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