<?php 

$borderTop = 'border-top:1px solid black;';
$borderLeft = 'border-left:1px solid black;';
$borderRight = 'border-right:1px solid black;';
$borderBottom = 'border-bottom:1px solid black;';

//Kolom ttd 
$signTable = ' 
<div></div>
<table cellpadding="3" style="'.$borderLeft.$borderRight.$borderBottom.'text-align:center;font-weight:bold">
<tr><td style="width:100px;border:1px solid black;">Direksi</td><td style="width:110px;border:1px solid black;">Kabag Keu/Acc</td><td style="width:120px;border:1px solid black;">Kabag</td><td style="width:120px;border:1px solid black;">Acounting</td><td  style="width:120px;border:1px solid black;">Kasir</td><td style="width:110px;border:1px solid black;">Penerima</td></tr>
<tr><td style="width:100px;'.$borderRight.'"></td><td style="width:110px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td></tr>
<tr><td style="width:100px;'.$borderRight.'"></td><td style="width:110px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td></tr>
<tr><td style="width:100px;'.$borderRight.'"></td><td style="width:110px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td></tr>
</table>';    


$pdf->setCustomSettings(
    array( 
         'paperSetting' => 'A5,L',
         'showPrintHeader' => false, 
		 'marginFooter' => '25',
         'footer' => $signTable,  
         ) 
);

$generateReportContent = function ($dataset){ 
    
global $pdf;

$obj = new EMKLPurchaseOrder(EMKL['jobType']['export']);         
$emklJobOrder = new EMKLJobOrder();    
$customer = new Customer();     
$supplier = new Supplier();    

$rs = $dataset['rs'];   
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
$rsJO = $emklJobOrder->getDataRowById($rs[0]['refkey']);
    
if($rsJO[0]['loadcontainertypekey'] != 5){   
$rsDetailVolume = $emklJobOrder->getDetailVolume($rsJO[0]['pkey']);
$arrParty = array();
foreach($rsDetailVolume as $volumeRow)
  array_push($arrParty,$obj->formatNumber($volumeRow['qty']).'x '.$volumeRow['itemname']);

$party = implode('<br>',$arrParty);
}else{
       
    $temp = array();
    if(!empty($rsJO[0]['volume'])) array_push($temp, $obj->formatNumber($rsJO[0]['volume'],2). ' CBM');
    if(!empty($rsJO[0]['weight'])) array_push($temp, $obj->formatNumber($rsJO[0]['weight'],2) . ' KG');
    
    
    $party = implode('<br> ',$temp);
}
$rsCustomer = $customer->getDataRowById($rsJO[0]['customerkey']);
		

	
	
$borderTop = 'border-top:1px solid black;';
$borderLeft = 'border-left:1px solid black;';
$borderRight = 'border-right:1px solid black;';
$borderBottom = 'border-bottom:1px solid black;'; 
$profileImg = $obj->loadSetting('companyLogo'); 
$img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=220&h=110&hash='.getPHPThumbHash($profileImg);
 
$html = $obj->printSetting['defaultStyle'];

$html .= '
<table style="'.$borderTop.$borderRight.$borderLeft.'width:680px">
    <tr>
        <td  style="width:170px">
        <table cellpadding="3"> 
            <tr>
                <td style="vertical-align:middle; width:180px;font-size:2.4em;font-weight:bold;font-family:Arial Black;font-style:italic" >OKATRANS</td>
            </tr>
        </table>
        </td>
        <td style="width:280px">
            <table cellpadding="2" style="text-align:left;"> 
            <tr><td></td></tr>
            <tr><td style="width:40px;"></td><td style="text-algin:center"><div class="title">BUKTI KAS/BANK (PO)</div></td></tr>
            <tr><td style="width:200px;font-size:1.2em"><b>Tgl.</b> '.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td><td style="width:190px;font-size:1.2em"><b>No: '.$rs[0]['code'].' </b></td></tr>
            </table> 
        </td>
        <td style="width:229.9px">

        </td>
    </tr> 
    <tr><td></td></tr>
</table>
';
    
$html .= '
<table cellpadding="2" style="'.$borderRight.$borderLeft.'">
<tr><td style="width:30px"></td><td class="header-row-header" style="font-size:1.2em;">Dibayar kepada :</td><td style="width:530px;font-size:1.2em;">'.$rsSupplier[0]['name'].'</td></tr> 
</table>   ';

$html .= '<table cellpadding="3" style="'.$borderLeft.$borderRight.'">
<tr class="col-header" ><td style="'.$borderRight.'width:310px;">Keterangan</td><td style="'.$borderRight.'width:90px;" >No. Order</td><td style="'.$borderRight.'width:90px;" >Customer</td><td style="'.$borderRight.'width:80px;text-align:center" >Partai</td><td style="text-align:right; width:110px;">Jumlah</td></tr>';

 
for($i=0;$i<count($rsDetail);$i++){  
	$serviceName = (!empty($rsDetail[$i]['servicename'])) ? $rsDetail[$i]['servicename']:''; 
	  
    $arrDesc = array(); 
	if(!empty($serviceName)) array_push($arrDesc,$serviceName); 
    if(!empty($rsDetail[$i]['description'])) array_push($arrDesc,$rsDetail[$i]['description']);
    
    $ket = implode('<br>',$arrDesc);
     
   $html .= '<tr><td style="'.$borderRight.'">'.$ket.'</td><td style="'.$borderRight.'">'. $rsJO[0]['code'] .'</td><td style="'.$borderRight.'">'. $rsCustomer[0]['alias'].'</td><td style="'.$borderRight.'text-align:center">'.$party.'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['subtotal']).'</td></tr>' ; 
     
} 
$html .= '</table>' ;
 
$html .= '<table  cellpadding="3" style="'.$borderTop.'">';
if($rs[0]['taxvalue'] > 0){
$html .= '<tr class="" ><td style="width:235px;"></td><td style="width:335px;text-align:right" ><b>DPP</b></td><td style="'.$borderRight.$borderBottom.$borderLeft.'text-align:right; width:110px;">'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>';
$html .= '<tr class="" ><td style="width:235px;"></td><td style="width:335px;text-align:right" ><b>PPN</b></td><td style="'.$borderRight.$borderBottom.$borderLeft.'text-align:right; width:110px;">'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>';	
}	
$html .= '<tr class="" ><td style="width:235px; "></td><td style="width:335px;text-align:right" ><b>Total</b></td><td style="'.$borderRight.$borderBottom.$borderLeft.'text-align:right; width:110px;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>';
$html .= '</table>';
 
return $html;
}
?>