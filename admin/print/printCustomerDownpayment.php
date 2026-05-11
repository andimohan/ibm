<?php 
include '../../_config.php';  
include '../../_include.php'; 
 
$obj = $customerDownpayment;
$securityObject  = $obj->securityObject; 
  
include '_global.php'; 

// TABLE WIDTH = 670px

$arrID = (isset( $_GET['id']) && !empty( $_GET['id'])) ? explode(',',$_GET['id']) : array();

$title = array();
for($i=0;$i<count($arrID);$i++){
    $id = $arrID[$i];
    
    $pdf->startPageGroup();  
    $pdf->AddPage(); 

    $rs = $obj->searchData($obj->tableName.'.pkey',$id); 
    $pdf->rs = $rs;
    $obj->validateAllowedStatus($rs);
    
    $dataset = array();
    $dataset['rs'] = $rs; 
      
    $html = ($customPrint['status']) ? generatePrintCustom($dataset) :  generatePrintTemplate($dataset); 
    $pdf->writeHTML($html);   
 
    array_push($title,$rs[0]['code'] );
    
    if($rs[0]['statuskey'] == 2)
        $obj->changeStatus($rs[0]['pkey'], 3,'',false,true);
    
}


$title = implode(', ', $title);

$pdf->SetTitle($title); 
$pdf->Output( substr($title,0,$obj->printSetting['fileNameLength']) .'.pdf', 'I'); 
 

function generatePrintTemplate($dataset){
global $pdf;

$obj = new CustomerDownpayment();  
$customer = new Customer();
    
$rs = $dataset['rs']; 
$rsDetailPayment = $obj->getDataRowById($rs[0]['pkey']);
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$ref = '';
if(!empty($rs[0]['refkey']))
    $ref .= '<tr><td class="header-row-header"  style="width:80px">'.ucwords($obj->lang['refCode']).'</td><td style="width:10px; text-align:center">:</td><td style="width:580px;">'.$rs[0]['refcode'].'</td></tr>';
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">FAKTUR UANG MUKA</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header"  style="width:80px">'.ucwords($obj->lang['date']).'</td><td style="width:10px; text-align:center">:</td><td style="width:580px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
'.$ref.'
<tr><td colspan="3"></td></tr> 
<tr><td colspan="3" style="font-weight:bold">Kepada Yth.</td></tr> 
<tr><td colspan="3">'. $rsCustomer[0]['name'] .'<br>'.$rsCustomer[0]['address'].'</td></tr>   
</table> ';
      
$html .='<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction" >
<tr class="col-header"><td style="text-align:left;width:30px">No</td><td style="text-align:left;width:500px; ">'.ucwords($obj->lang['description']).'</td><td style="text-align:right;width:140px;">'.ucwords($obj->lang['amount']).'</td></tr>  
';

$html .= '<tr><td>1.</td><td>'. str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td><td style="text-align:right;width:140px;">'.$obj->formatNumber($rs[0]['amount']).'</td></tr>  
';
        
$sayNumber = $obj->sayNumber($rs[0]['amount']);
    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="2" style="width:460px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.</td> <td style="text-align:right; font-weight:bold;  width:100px; ">'.ucwords($obj->lang['total']).'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['amount']).'</td></tr>
';

    
$html .= '
<table cellpadding="4" style="font-size:10px"> 
</table> 
<div style="clear:both"></div>  
';
     
$html .= $obj->loadSetting('emailInvoiceFooter');   
$html .= '<div style="clear:both"></div>';
$html .= $obj->generateSignLabel($rs); 
return $html;
}
