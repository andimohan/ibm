<?php  

includeClass(array('SalesOrderProperty.class.php'));
$salesOrderProperty = createObjAndAddToCol( new SalesOrderProperty()); 
$obj = $salesOrderProperty;

$generateReportContent = function ($dataset){ 
    
$obj = new SalesOrderProperty(); 
$termOfPayment = new TermOfPayment();
$customer = new Customer();
$chartOfAccount = new ChartOfAccount();
$salesOrderPropertyType = new SalesOrderPropertyType();
    
$rs = $dataset['rs'];
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
$rsBuyer = $customer->getDataRowById($rs[0]['buyerkey']);
$buyerrName = $rsBuyer[0]['name'];
$buyerAddress = (!empty($rsBuyer[0]['address'])) ? $rsBuyer[0]['address'] : '';
$buyerPhone = (!empty($rsBuyer[0]['address'])) ? $rsBuyer[0]['phone'] : '';
$rsSeller = $customer->getDataRowById($rs[0]['sellerkey']);
$rsBank = $customer->getDataRowById($rs[0]['bankkey']);
$rsType =  $salesOrderPropertyType->getDataRowById($rs[0]['typekey']);

if(!empty($rs[0]['refundcoakey'])){
$rsCOA = $chartOfAccount->getDataRowById($rs[0]['refundcoakey']);
    $coaName = $rsCOA[0]['code'].' - '.$rsCOA[0]['name'];
}
    
$totalCommissionCompany = $rs[0]['agencyfee'] * ($rs[0]['officepercentage'] / 100);
$totalCommissionAgent = $rs[0]['agencyfee'] * ($rs[0]['agentpercentage'] / 100);

$bankProvisionTotal = $rs[0]['banktotal'] * ($rs[0]['bankprovisionpercentage'] / 100);
$totalBankAgent = $bankProvisionTotal * ($rs[0]['agentbankpercentage'] / 100);
$totalBankCompany = $bankProvisionTotal * ($rs[0]['officebankpercentage'] / 100);


$arrRecipient = array();
array_push($arrRecipient, $buyerrName, str_replace(chr(13),'<br>',$buyerAddress), $buyerPhone);
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.ucwords($obj->lang['salesOrderSummary']).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<div style="clear:both"></div>
<table>
<tr>
<td style="width:370px;">
<table cellpadding="2"> 
<tr><td class="header-row-header" style="width: 120px;">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width: 250px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr> 
<tr><td class="header-row-header"  style="width:120px">'.ucwords($obj->lang['seller']).'</td><td style="width:10px; text-align:center">:</td><td style="width:250px;">'.$rs[0]['sellername'].'</td></tr> 
<tr><td class="header-row-header"  style="width:120px">'.ucwords($obj->lang['buyer']).'</td><td style="width:10px; text-align:center">:</td><td style="width:250px;">'.$rs[0]['buyername'].'</td>   </tr> 
<tr><td class="header-row-header"  style="width:120px">'.ucwords($obj->lang['agent']).'</td><td style="width:10px; text-align:center">:</td><td style="width:250px;">'.$rs[0]['employeename'].'</td>   </tr>
</table> 
</td>
<td style="width:300px;">
<table cellpadding="2"> 
<tr><td class="header-row-header"  style="width:120px">Jenis</td><td style="width:10px; text-align:center">:</td><td  style="width:180px;">'.$rsType[0]['name'].'</td></tr> 
<tr><td class="header-row-header"  style="width:120px">'.ucwords($obj->lang['propertyInformation']).'</td><td style="width:10px; text-align:center">:</td><td style="width:180px;">'.str_replace(chr(13),'<br>',$rs[0]['propertyinformation']).'</td></tr>  
<tr><td class="header-row-header"  style="width:120px">'.ucwords($obj->lang['top']).'</td><td style="width:10px; text-align:center">:</td><td style="width:180px;">'.$rsTOP[0]['duedays'].' days</td>   </tr> 
</table> 
</td>
</tr>
</table>
<div style="clear:both"></div><br>


<table>
<tr>
<td style="width:330px">
<div style="font-weight:bold; line-height:30px; ">'.strtoupper($obj->lang['transactionValue']).'</div>
<table cellpadding="2"> 
<tr><td  style="width: 120px;">'.ucwords($obj->lang['transactionValue']).'</td><td style="width:10px; text-align:center">:</td><td style="width: 200px;text-align:right">'.$obj->formatNumber($rs[0]['transactiontotal']).'</td></tr>  
<tr><td  style="width: 120px;">'.ucwords($obj->lang['downpayment']).'</td><td style="width:10px; text-align:center">:</td><td style="width: 200px;text-align:right">'.$obj->formatNumber($rs[0]['totaldownpayment']).'</td></tr> 
<tr><td   style="width:120px">'.ucwords($obj->lang['balance']).'</td><td style="width:10px; text-align:center">:</td><td style="width: 200px;text-align:right">'.$obj->formatNumber($rs[0]['balance']).'</td></tr> 
<tr><td  style="width:120px">'.ucwords($obj->lang['customerDownpaymentSettlement']).'</td><td style="width:10px; text-align:center">:</td><td style="width: 200px;text-align:right">'.$obj->formatNumber($rs[0]['downpaymentsettlement']).'</td></tr>
<tr><td  style="width:120px">'.ucwords($obj->lang['settlementAccount']).'</td><td style="width:10px; text-align:center">:</td><td style="width:250px;">'.$coaName.'</td></tr> 
</table> 
</td>
<td style="width:20px"></td>
<td style="width:330px">
<div style="font-weight:bold; line-height:30px; ">'.strtoupper($obj->lang['company']).'</div>
<table cellpadding="2"> 
<tr><td  style="width: 120px;">'.ucwords($obj->lang['commission']).'</td><td style="width:10px; text-align:center">:</td><td style="width: 200px;text-align:right">'.$obj->formatNumber($totalCommissionCompany).'</td></tr>  
<tr><td  style="width: 120px;">'.ucwords($obj->lang['bankProvision']).'</td><td style="width:10px; text-align:center">:</td><td style="width: 200px;text-align:right">'.$obj->formatNumber($totalBankCompany).'</td></tr> 
<tr><td   style="width:120px">'.ucwords($obj->lang['adminFee']) .' ('.$obj->lang['agent'] .')'.'</td><td style="width:10px; text-align:center">:</td><td style="width:80px;text-align:right;">'.$obj->formatNumber($rs[0]['adminpercentage'],-2).' %</td><td style="width: 120px;text-align:right">'.$obj->formatNumber($rs[0]['adminfee']).'</td></tr> 
<tr><td   style="width:120px">OR Lead</td><td style="width:10px; text-align:center">:</td><td style="width:80px;text-align:right;">'.$obj->formatNumber($rs[0]['orleadpercentage'],-2).' %</td><td style="width: 120px;text-align:right">'.$obj->formatNumber($rs[0]['orlead']).'</td></tr>
<tr><td  style="width: 120px;"><b>'.ucwords($obj->lang['total']).'</b></td><td style="width:10px; text-align:center">:</td><td style="width: 200px;text-align:right"><b>'.$obj->formatNumber($rs[0]['totalcompanyrevenue']).'</b></td></tr>  
</table> 
</td>
</tr>
</table>
<div style="clear:both"></div><br>


<table>
<tr>
<td style="width:330px">
<div style="font-weight:bold; line-height:30px; ">'.strtoupper($obj->lang['commissionTransaction']).'</div>
<table cellpadding="2"> 
<tr><td  style="width: 120px;">'.ucwords($obj->lang['commission']).'</td><td style="width:10px; text-align:center">:</td><td style="width:80px;text-align:right;">'.$obj->formatNumber($rs[0]['agencypercentage'],-2).' %</td><td style="width: 120px;text-align:right">'.$obj->formatNumber($rs[0]['agencyfee']).'</td></tr>  
<tr><td  style="width: 120px;">'.ucwords($obj->lang['company']).'</td><td style="width:10px; text-align:center">:</td><td style="width:80px;text-align:right;">'.$obj->formatNumber($rs[0]['officepercentage'],-2).' %</td><td style="width: 120px;text-align:right">'.$obj->formatNumber($rs[0]['officefee']).'</td></tr> 
<tr><td   style="width:120px">'.ucwords($obj->lang['agent']).'</td><td style="width:10px; text-align:center">:</td><td style="width:80px;text-align:right;">'.$obj->formatNumber($rs[0]['agentpercentage'],-2).' %</td><td style="width: 120px;text-align:right">'.$obj->formatNumber($rs[0]['agentfee']).'</td></tr> 
</table> 
</td>
<td style="width:20px"></td>
<td style="width:330px">
<div style="font-weight:bold; line-height:30px; ">'.strtoupper($obj->lang['agent']).'</div>
<table cellpadding="2"> 
<tr><td  style="width: 120px;">'.ucwords($obj->lang['commission']).'</td><td style="width:10px; text-align:center">:</td><td style="width: 200px;text-align:right">'.$obj->formatNumber($totalCommissionAgent).'</td></tr>  
<tr><td  style="width: 120px;">'.ucwords($obj->lang['bankProvision']).'</td><td style="width:10px; text-align:center">:</td><td style="width: 200px;text-align:right">'.$obj->formatNumber($totalBankAgent).'</td></tr> 
<tr><td  style="width: 120px;">Closing Fee</td><td style="width:10px; text-align:center">:</td><td style="width: 200px;text-align:right">'.$obj->formatNumber($rs[0]['closingfeetotal']).'</td></tr> 
<tr><td  style="width: 120px;">Cash Rewards</td><td style="width:10px; text-align:center">:</td><td style="width: 200px;text-align:right">'.$obj->formatNumber($rs[0]['cashrewardtotal']).'</td></tr> 
<tr><td  style="width: 120px;"><b>'.ucwords($obj->lang['total']).'</b></td><td style="width:10px; text-align:center">:</td><td style="width: 200px;text-align:right"><b>'.$obj->formatNumber($rs[0]['totalagentrevenue']).'</b></td></tr> 
</table> 
</td>
</tr>
</table>


<table>
<tr>
<td >
<div style="font-weight:bold; line-height:30px; ">'.strtoupper($obj->lang['bankProvision']).'</div>
<table cellpadding="2"> 
<tr><td  style="width: 120px;">'.ucwords($obj->lang['bank']).'</td><td style="width:10px; text-align:center">:</td><td style="width: 160px;">'.$rsBank[0]['name'].'</td></tr>  
<tr><td  style="width: 120px;">'.ucwords($obj->lang['amount']).'</td><td style="width:10px; text-align:center">:</td><td style="width: 200px;text-align:right">'.$obj->formatNumber($rs[0]['banktotal']).'</td></tr>  
<tr><td  style="width: 120px;">'.ucwords($obj->lang['commission']).'</td><td style="width:10px; text-align:center">:</td><td style="width:80px;text-align:right;">'.$obj->formatNumber($rs[0]['bankprovisionpercentage'],-2).' %</td><td style="width: 120px;text-align:right">'.$obj->formatNumber($rs[0]['bankprovision']).'</td></tr>  
<tr><td  style="width: 120px;">'.ucwords($obj->lang['company']).'</td><td style="width:10px; text-align:center">:</td><td style="width:80px;text-align:right;">'.$obj->formatNumber($rs[0]['officebankpercentage'],-2).' %</td><td style="width: 120px;text-align:right">'.$obj->formatNumber($rs[0]['officefeebank']).'</td></tr> 
<tr><td   style="width:120px">'.ucwords($obj->lang['agent']).'</td><td style="width:10px; text-align:center">:</td><td style="width:80px;text-align:right;">'.$obj->formatNumber($rs[0]['agentbankpercentage'],-2).' %</td><td style="width: 120px;text-align:right">'.$obj->formatNumber($rs[0]['agentfeebank']).'</td></tr> 
</table> 
</td>
<td></td>
</tr>
</table>
';



    
$html .= '
</table>
<div style="clear:both"></div>';

//$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>