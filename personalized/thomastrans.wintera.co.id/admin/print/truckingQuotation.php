<?php  
$pdf->setCustomSettings(
    array(  
		 'logoSize' => '35,21', 
         'footer' => '<table><tr><td style="text-align:center">Powered by www.wintera.co.id</td></tr></table>',   
         ) 
);  


$generateReportContent = function ($dataset) {

    $obj = new TruckingQuotation(); 
    $employee = new Employee();

    $rs = $dataset['rs'];
    
    $useSign = (isset($_GET) && $_GET['sign'] == 1) ? true : false; 
    $autoSign = ($useSign) ? '<br><br><img src="/personalized/yellowegg.wintera.co.id/img/ttd.jpg" style="width:150px">' : '';
 
    $approvedBy = (isset($_GET) && $_GET['approvedBy'] == 1) ? true : false; 
    
    $rsDetail = $obj->getDetailById($rs[0]['pkey']);
    $rsCreatedBy = $employee->getDataRowById($rs[0]['createdby']);
    $sayNumber = $obj->sayNumber($rs[0]['total']);
    $trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Notes :</strong> <br>' . str_replace(chr(13), '<br>', $rs[0]['trdesc']) : '';
    $html = $obj->printSetting['defaultStyle'];
     
    $html .= ' 
    <br>    <br>
<table cellpadding="2" > 
<tr><td><div class="title">QUOTATION</div></td></tr>
<tr><td><div class="subtitle">' . $rs[0]['code'] . '</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table>
<tr>
<td style="width:500px;" >
<table cellpadding="2">
<tr><td class="header-row-header" style="width:120px">To</td><td style="width:10px; text-align:center">:</td><td style="width:390px">' . $rs[0]['customername'] . '</td></tr>
<tr><td class="header-row-header">UP</td><td style="text-align:center">:</td><td >' . $rs[0]['recipientname'] . '</td></tr>
<tr><td class="header-row-header">Request</td><td style="text-align:center">:</td><td >' . $rs[0]['name'] . '</td></tr>
</table>
</td>
<td style="width:160px;"> 
<table cellpadding="2">
<tr><td style="width:160px; text-align:right">Jakarta, '.$obj->formatDBDate($rs[0]['trdate'], 'd F Y').'</td></tr>
</table>
</td>
</tr>
</table>

<div style="clear:both"></div>
<table cellpadding="6" class="table-transaction" style="border:1px solid #000">
<tr class="col-header"><td style="text-align:center;width:60px; border-right:1px solid #000">Qty</td><td style="text-align:left;width:330px; border-right:1px solid #000">Description</td><td style="text-align:right;width:140px; border-right:1px solid #000">Price</td><td style="text-align:right;width:140px">Amount</td></tr>  
';

for ($i = 0; $i < count($rsDetail); $i++) {
 
    $detailDesc = (!empty($rsDetail[$i]['trdesc'])) ? '<br>'.nl2br($rsDetail[$i]['trdesc']) : '';
    $html .= '
        <tr> 
            <td style ="text-align:center; border-right:1px solid #000">' . $obj->formatNumber($rsDetail[$i]['qtyinbaseunit']) . '</td>
            <td style = "border-right:1px solid #000">' . $rsDetail[$i]['servicename'] .$detailDesc. '</td>
            <td style ="text-align:right; border-right:1px solid #000">' . $obj->formatNumber($rsDetail[$i]['priceinunit']) . '</td>
            <td style ="text-align:right; border-right:1px solid #000">' . $obj->formatNumber($rsDetail[$i]['subtotal']) . '</td>
            </tr>';
}

$html .= '    
</table>  
' . $trnotes . '
<div style="clear:both"></div>  
<i>PT. Thomas Trans refered to STC ALFI / ILFA 2016 3rd edition (available upon request).</i>
<div style="clear:both"></div>  
';

$arrSignLabel = array(); 
array_push($arrSignLabel, array('Best Regards,',$rsCreatedBy[0]['name'].',<br>PT. Thomas Trans')); 
    
if($approvedBy)
array_push($arrSignLabel, array('Approved By,','')); 
    
$html .=' 
	<table cellpadding="4" class="sign">
		<tr>'; 
			for ($i=0;$i<count($arrSignLabel);$i++){
                 if ($i>0) $autoSign = '';
                
                 $html .='<td  class="sign-col" style="height:130px">'.$arrSignLabel[$i][0].$autoSign.'</td>';
                 if ($i <> count($arrSignLabel) - 1)
                     $html .= '<td class="sign-col-space"></td>';
             }
	
        $html .='</tr> 
        <tr>'; 
            for ($i=0;$i<count($arrSignLabel);$i++){
               	$arrSignLabel[$i][1] = (isset($arrSignLabel[$i][1])) ? $arrSignLabel[$i][1] : '';
                $html .='<td  class="sign-name" style="border-bottom:none;">'.$arrSignLabel[$i][1].'</td>';
                if ($i <> count($arrSignLabel) - 1)
                     $html .= '<td class="sign-col-space"></td>';
                }
    	$html .='</tr> 
    	</table>' ; 
     
return $html;
}
?>