<?php

$packagingCode = new PackagingCode();
$receivingPurchaseJewelry =  createObjAndAddToCol( new ReceivingPurchaseJewelry());
$obj = $packagingCode;
$securityObject  = $obj->securityObject;   

define('FONT_NAME',TCPDF_FONTS::addTTFfont(K_PATH_FONTS.'DroidSansMono.ttf'));
define('TOTAL_CHAR_LENGTH',40); 
define('PRICE_LENGTH',16);
define('FOOTER_PRICE_LENGTH',16);
define('DOC_WIDTH',80);  
define('X_POS',2);  
define('FONT_SIZE',8);

// TABLE WIDTH = 670px

function setParam($pdf){ 
    
    $margin = 0;
    $pdf->SetMargins($margin, $margin, $margin); 
    
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAutoPageBreak(TRUE, 0);
    
    $pdf->SetFont (FONT_NAME, '', FONT_SIZE , '', 'default', true );        
    
    return $pdf;
}

$arrID = (isset( $_GET['id']) && !empty( $_GET['id'])) ? explode(',',$_GET['id']) : array();
$module = (isset( $_GET['module']) && !empty( $_GET['module'])) ?  $_GET['module']  : '';

$tableKey = 0; 
switch($module){
        case 'receiving-jewelry' : $tableKey = $obj->getTableKeyAndObj($receivingPurchaseJewelry->tableName,array('key'))['key']; break;
       
}

if(empty($tableKey)) die("error");

$title = array();
for($i=0;$i<count($arrID);$i++){
    $id = $arrID[$i];
    
    // generate content
    $rs = $obj->searchData($obj->tableName.'.statuskey','1',true, 
                          ' and ' .$obj->tableName.'.reftabletype =  '. $obj->oDbCon->paramString($tableKey) .' and reftransactionkey = ' .  $obj->oDbCon->paramString($id) );
 
    $pdf->rs = $rs;  
    $obj->validateAllowedStatus($rs);
    
    $dataset = array();
    $dataset['rs'] = $rs;
    
    $perLabelHeight = 30;
    $barcodeHeight = 8;
    
    $totalHeight = ($perLabelHeight + $barcodeHeight) * count($dataset['rs']);
    
    $pdf = new MYPDF('P', 'mm', array(DOC_WIDTH,$totalHeight), true, 'UTF-8', false);  
    $pdf = setParam($pdf);
    $pdf->SetFillColor(255, 255, 255); 
    $pdf->AddPage();
  
    $divider = 500;
    
    foreach($dataset['rs']  as $row){
       $pdf->write1DBarcode(
            $row['code'],      // data
            'C128',            // type
            '2', '',            // x, y = auto, just flow down
            40, $barcodeHeight,            // width, height
            0.4,               // thickness
            ['position' => 'S'], // style
            'N'
        );

     $html = '';
     $html .= "\n";     
     $html .= $row['code'] .'-'. $row['rownumber'].' ' . $class->formatDBDate($row['trdate'],'ymd');    
     $html .= "\n";     
     $html .= 'NW '.$class->formatNumber($row['netweight'],2). ' GW ' . $class->formatNumber($row['grossweight'],2) . ' '.$class->formatNumber($row['sellingprice']/$divider);    
     $html .= "\n"; 
     $html .= $class->formatNumber($row['qtyinbaseunit']) .strtolower($row['baseunitname']). '/'.$class->formatNumber($row['costinbaseunit']/$divider) .' '. $class->formatNumber($row['qtyinpcs']) . 'gr/'.$class->formatNumber($row['costinpcs']/$divider);    
     $html .= "\n";      
     $html .= $row['suppliercode'].'.'.$row['itemcode'].' - '.$row['trdesc'];
     $html .= "\n";      
     $html .= (!empty($row['itemalias'])) ? $row['itemalias'] : $row['itemname'] ;    
     $html .= "\n";      
     $html .= "\n";      
         
     $pdf->MultiCell(DOC_WIDTH, $perLabelHeight, $html, 0, 'L', 0, 1 ); 
         
    }

 
    array_push($title,$rs[0]['code'] );
}

$title = implode(', ', $title);

$pdf->SetTitle($title); 
$pdf->Output( substr($title,0,$obj->printSetting['fileNameLength']) .'.pdf', 'I'); 
 
?>