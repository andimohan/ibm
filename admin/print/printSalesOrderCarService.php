<?php 
include '../../_config.php';  
include '../../_include.php'; 
 
$SHOW_PRINT_HEADER = false;

$obj = $salesOrderCarService;
$securityObject  = $obj->securityObject;   

include '_global.php'; 
 
define('FONT_NAME',TCPDF_FONTS::addTTFfont(K_PATH_FONTS.'DroidSansMono.ttf'));
define('TOTAL_CHAR_LENGTH',34); 
define('PRICE_LENGTH',12);
define('FOOTER_PRICE_LENGTH',15);
define('DOC_WIDTH',75); // 60
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

$title = array();
for($i=0;$i<count($arrID);$i++){
    $id = $arrID[$i];
    
    // generate content
    $rs = $obj->getDataRowById($id);
    $pdf->rs = $rs;  
    $obj->validateAllowedStatus($rs);
    $rsDetail = $obj->getDetailWithRelatedInformation($id); 
        
    $dataset = array();
    $dataset['rs'] = $rs;
    $dataset['rsDetail'] = $rsDetail; 
     
    $txt = generatePrintTemplate($dataset); 
     
    $h = 20;
    $or = 'P'; 
    
    $pdf = new MYPDF($or, 'mm', array(DOC_WIDTH,$h), true, 'UTF-8', false); 
    $pdf = setParam($pdf);
      
    $newH = $pdf->getStringHeight(DOC_WIDTH, $txt);
 
    $newH += 20;
   // $obj->setLog('width ' . DOC_WIDTH. ' height ' . $newH);   
    
    if ($newH < DOC_WIDTH)
        $or = 'L'; 
    
    $pdf = new MYPDF($or, 'mm', array(DOC_WIDTH,$newH), true, 'UTF-8', false);  
    $pdf = setParam($pdf);
    $pdf->SetFillColor(255, 255, 255); 
    $pdf->AddPage();  
    
    // COMPANY NAME ...... 
    $headerTotalChar = 40;
    $pdf->SetFont (FONT_NAME, '', 7 , '', 'default', true );    
    $pdf->SetXY(8, 2);
    $html = $obj->formatString(strtoupper($class->loadSetting('companyName')), $headerTotalChar , array('align' => 'center', 'split' => $headerTotalChar))."\n";

    $arrAddress = explode(chr(13),$class->loadSetting('companyAddress'));
    for($i=0;$i<count($arrAddress);$i++)
        $html .= $obj->formatString($arrAddress[$i], $headerTotalChar , array('align' => 'center', 'split' => $headerTotalChar))."\n";
   
    $html .= $obj->formatString('No. NPWP / PKP : '.$class->loadSetting('companyTaxRegistrationNumber'), $headerTotalChar , array('align' => 'center', 'split' => $headerTotalChar))."\n";

    $pdf->MultiCell(DOC_WIDTH, $newH, $html, 0, 'L', 1, 0 );

    $pdf->SetFont (FONT_NAME, '', FONT_SIZE , '', 'default', true );  
    
    
    // LOGO ......
    $profileImg = $class->loadSetting('companyLogo');  
    if (!empty($profileImg)){ 

        $img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$class->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&far=C&f=png&hash='.getPHPThumbHash($profileImg);

        $x = 8;
        $y = 12; 

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $img);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $file = curl_exec($ch);
        curl_close($ch);
        
        $pdf->Image('@'.$file,$x,$y,DOC_WIDTH-15,10,'','','',true,300,'',false,false,0,'CM',false,false); 
 
    }   
       
    $pdf->SetXY(8, 22);
    $pdf->MultiCell(DOC_WIDTH, $newH, $txt, 0, 'L', 1, 0 );
    
    // footer 
    $pdf->MultiCell(DOC_WIDTH, $newH, $txt, 0, 'L', 1, 0 );
    
    $pdf->deletePage(2); // ntah kenapa selalu muncul page kedua, jd delete aj
  
    array_push($title,$rs[0]['code'] );
}


$title = implode(', ', $title);

$pdf->SetTitle($title); 
$pdf->Output( substr($title,0,$obj->printSetting['fileNameLength']) .'.pdf', 'I'); 


function generatePrintTemplate($dataset){
global $class;
    
$obj = new SalesOrderCarService();  
$car = new Car(); 
$company = new Company();
$customer = new Customer();
$warehouse = new Warehouse();
$employee = new Employee();
$termOfPayment = new TermOfPayment();
    
$rs = $dataset['rs'];
$rsDetail = $dataset['rsDetail'];  
    
$rsCar = $car->searchData($car->tableName.'.pkey',$rs[0]['carkey'],true); 
$rsPayment = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);
$rsEmployee = $employee->getDataRowById($rs[0]['techniciankey']);
$rsCompany = $company->getDataRowById($rs[0]['companykey']);
$rsWarehouse = $warehouse->getDataRowById($rs[0]['warehousekey']);
$rsSales = $employee->getDataRowById($obj->userkey);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$rsCreatedBy = $employee->getDataRowById($rs[0]['createdby']);
    
$arrRecipient = array();
array_push($arrRecipient, $rs[0]['recipientname'], $rs[0]['recipientaddress'], $rs[0]['recipientphone']);
    
if ($rs[0]['finaldiscounttype'] == 2)
    $rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
$finaldiscount = $rs[0]['finaldiscount'];  
    
$html = ''; 
  
        
$html .= "\n";    
$html .=  $obj->formatString($obj->formatDBDate($rs[0]['trdate'],'d/m/Y'),TOTAL_CHAR_LENGTH/2 ).$obj->formatString($rs[0]['code'],TOTAL_CHAR_LENGTH/2). "\n";    
$html .=  $obj->formatString($rsPayment[0]['name'],TOTAL_CHAR_LENGTH/2 ).$obj->formatString($rsCompany[0]['name'],TOTAL_CHAR_LENGTH/2). "\n";    
$html .=  $obj->formatString('',TOTAL_CHAR_LENGTH/2 ).$obj->formatString($rsCreatedBy[0]['name'],TOTAL_CHAR_LENGTH/2). "\n";     
$html .= "\n";  
    
$html .= strtoupper($obj->lang['customer']).":\n";  
$html .= $rsCustomer[0]['name'];  
$html .= "\n";  
$html .= $rsCar[0]['policenumber'];  
$html .= "\n\n";  
  
for ($i=0;$i<count($rsDetail);$i++){   
    $html .= $obj->formatString($rsDetail[$i]['itemname'], TOTAL_CHAR_LENGTH)."\n";  
    $itemName = $obj->formatString($obj->formatNumber($rsDetail[$i]['qty']).' ' .$rsDetail[$i]['unitname'], TOTAL_CHAR_LENGTH - (2*PRICE_LENGTH) );
    $priceinunit =  $obj->formatString($obj->formatNumber($rsDetail[$i]['priceinunit']), PRICE_LENGTH, array('align' => 'right') );
    $subtotal = $obj->formatString($obj->formatNumber($rsDetail[$i]['total']), PRICE_LENGTH, array('align' => 'right') );
    $html .=  $itemName .$priceinunit .$subtotal."\n";
    $html .= "\n";   
} 
$html .= "\n";  
    
$totalLabel = $obj->formatString($obj->lang['subtotal'], TOTAL_CHAR_LENGTH - FOOTER_PRICE_LENGTH, array('align' => 'right'));     
$subtotal = $obj->formatString($obj->formatNumber($rs[0]['subtotal']), FOOTER_PRICE_LENGTH, array('align' => 'right')); 
$html .=  $totalLabel .$subtotal."\n"; 
    
    
$totalLabel = $obj->formatString($obj->lang['discount'], TOTAL_CHAR_LENGTH - FOOTER_PRICE_LENGTH, array('align' => 'right'));     
$subtotal = $obj->formatString($obj->formatNumber($finaldiscount), FOOTER_PRICE_LENGTH, array('align' => 'right')); 
$html .=  $totalLabel .$subtotal."\n"; 
    
$totalLabel = $obj->formatString($obj->lang['PPN'] .' '. $obj->formatNumber($rs[0]['taxpercentage']). '%', TOTAL_CHAR_LENGTH - FOOTER_PRICE_LENGTH, array('align' => 'right'));     
$subtotal = $obj->formatString($obj->formatNumber($rs[0]['taxvalue']), FOOTER_PRICE_LENGTH, array('align' => 'right')); 
$html .=  $totalLabel .$subtotal."\n"; 
    
$totalLabel = $obj->formatString($obj->lang['shippingFee'] , TOTAL_CHAR_LENGTH - FOOTER_PRICE_LENGTH, array('align' => 'right'));     
$subtotal = $obj->formatString($obj->formatNumber($rs[0]['shipmentfee']), FOOTER_PRICE_LENGTH, array('align' => 'right')); 
$html .=  $totalLabel .$subtotal."\n"; 
    
$totalLabel = $obj->formatString($obj->lang['total'] , TOTAL_CHAR_LENGTH - FOOTER_PRICE_LENGTH, array('align' => 'right'));     
$subtotal = $obj->formatString($obj->formatNumber($rs[0]['grandtotal']), FOOTER_PRICE_LENGTH, array('align' => 'right')); 
$html .=  $totalLabel .$subtotal."\n"; 
    
     
$html .=  "\n";
$html .=  $obj->formatString($obj->loadSetting('sitesName'),TOTAL_CHAR_LENGTH,array('align' => 'center') )."\n";
    
$arrFooter = explode(chr(13),$class->loadSetting('emailInvoiceFooter'));
for($i=0;$i<count($arrFooter);$i++) 
    $html .= $obj->formatString(trim($arrFooter[$i]), TOTAL_CHAR_LENGTH , array('align' => 'center', 'split' => TOTAL_CHAR_LENGTH))."\n";
  
$html .=  "\n"; 
return $html;
}
