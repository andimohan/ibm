<?php 
includeClass('CashAdvanceRealization.class.php');
$cashAdvanceRealization = createObjAndAddToCol( new CashAdvanceRealization());
$obj = $cashAdvanceRealization;

$generateReportContent = function ($dataset){ 
$obj = new CashAdvanceRealization();  
$cashAdvance = new CashAdvance();
$employee = new Employee();
$warehouse = new Warehouse(); 
$chartOfAccount = new ChartOfAccount();    
	
$rs = $dataset['rs'];  
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey'],' order by cashtypekey asc'); 
$rsCash = $cashAdvance->getDataRowById($rs[0]['refkey']);
$rsWarehouse = $warehouse->getDataRowById($rs[0]['warehousekey']); 
$rsCOA = $chartOfAccount->getDataRowById($rs[0]['coakey']);
$rsEmployee = $employee->getDataRowById($rsCash[0]['employeekey']); 
$coakey = '';	

$rsCOAEmployee = $chartOfAccount->getDataRowById($rs[0]['cashadvancecoakey']);
	
$recipientName = $rsEmployee[0]['name'];	
//$trnotes = (!empty($rs[0]['note'])) ? '<div style="clear:both"></div><strong>'.$obj->lang['note'].'</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
/*$arrCashType = array();
$arrCashType[1] = $obj->lang['jobOrder'];
$arrCashType[2] = $obj->lang['downpayment'];
$arrCashType[3] = $obj->lang['cost'];*/
	
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($obj->lang['cashBankRealization']).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table>
<div style="clear:both"></div>
<table style="width: 640px">
<tr>
    <td style="width:360px; vertical-align:top">
        <table cellpadding="2" style="width:330px;">
            <tr><td class="header-row-header">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width:190px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
            <tr><td class="header-row-header">'.$obj->lang['warehouse'].'</td><td style="text-align:center">:</td><td>'.$rsWarehouse[0]['name'].'</td></tr>
            <tr><td class="header-row-header">'.$obj->lang['settlementAccount'].'</td><td style="text-align:center">:</td><td>'.$rsCOAEmployee[0]['code'].'-'.$rsCOAEmployee[0]['name'].'</td></tr>
            <tr><td class="header-row-header">'.$obj->lang['cashAdvance'].'</td><td style="text-align:center">:</td><td>'. str_replace(chr(13),', ',$rs[0]['cashadvancecache']) .'</td></tr> 
            <tr><td class="header-row-header">'.$obj->lang['recipient'].'</td><td style="text-align:center">:</td><td>'. $recipientName .'</td></tr>  
        </table>
    </td> 
    <td style="width:320px; vertical-align:top"><strong>'.$obj->lang['note'].'</strong><br>'. str_replace(chr(13),'<br>',$rs[0]['trdesc']).'
    </td>
</tr>
</table>


<div style="clear:both"></div>
<table cellpadding="4" class="table-transaction">';
$html .= '<tr class="col-header">
			<td style="text-align:right;width:30px">No</td>
			<td style="width:190px">'.$obj->lang['description'].'</td>
			<td style="width:180px">'.$obj->lang['service'].'</td>
			<td style="text-align:right;width:40px">'.$obj->lang['qty'].'</td>
			<td style="text-align:right;width:80px">'.$obj->lang['amount'].'</td>
			<td style="text-align:right;width:60px">'.$obj->lang['PPN'].' </td>
			<td style="text-align:right;width:90px">'.$obj->lang['subtotal'].'</td>
		  </tr>';

for($i=0;$i<count($rsDetail);$i++){ 
	$detailDesc ='';
	$invoiceReference ='';
	$inc = ($rsDetail[$i]['ispriceincludetax']) ? 'Yes' : 'No';
	$serviceName = (!empty($rsDetail[$i]['servicename'])) ? $rsDetail[$i]['servicename']:'';
	$supplierName = (!empty($rsDetail[$i]['suppliername'])) ? '<b>'.$obj->lang['supplier'].': </b>'.$rsDetail[$i]['suppliername']:'';
	
	if($rsDetail[$i]['cashtypekey']==1){
		$detailDesc = $rsDetail[$i]['jobordercode'].' - '.$rsDetail[$i]['containername'];
		$invoiceReference = (!empty($rsDetail[$i]['refcode'])) ? '<b>'.$obj->lang['reference'].': </b> '.$rsDetail[$i]['refcode']:'';
	}else if($rsDetail[$i]['cashtypekey']==2) {
        $detailDesc = $obj->lang['downpayment'];  
    }else if($rsDetail[$i]['cashtypekey']==3){
        $detailDesc = $rsDetail[$i]['coaname'];  
    }elseif($rsDetail[$i]['cashtypekey']==4){
		$detailDesc = $rsDetail[$i]['jobheadercode'].' - '.$rsDetail[$i]['containername'];
		$invoiceReference = (!empty($rsDetail[$i]['refcode'])) ? '<b>'.$obj->lang['reference'].': </b> '.$rsDetail[$i]['refcode']:'';
	} 
	
    $html .= '<tr>
				<td style="width:30px;text-align:right;">'.($i+1).'</td>
				<td>'.$detailDesc.'</td>
				<td>'.$serviceName.'</td>
				<td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td> 
				<td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['beforetaxtotalinunit']).'</td>
				<td style ="text-align:right">'.$obj->formatNumber($rsDetail[$i]['taxvalueinunit']).'</td>
				<td style ="width:90px;text-align:right">'.$obj->formatNumber($rsDetail[$i]['subtotal']).'</td>
			  </tr>';
    
	$arrScndRow = array();
	$arrSup = array();
	if(!empty($supplierName)) array_push($arrSup,$supplierName);
	if(!empty($invoiceReference)) array_push($arrSup,$invoiceReference);
	if(!empty($arrSup)) array_push($arrScndRow,implode(', ',$arrSup));
	if(!empty($rsDetail[$i]['description'])) array_push($arrScndRow,str_replace(chr(13),'<br>',$rsDetail[$i]['description']));
		
    if(!empty($arrScndRow))
	$html .= '<tr>
                <td></td>
				<td colspan="5">'.implode('<br>',$arrScndRow).'</td> 
                <td></td>
			  </tr>';
	
} 

$sayNumber = $obj->sayNumber($rs[0]['total']);
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td style="width:460px"></td><td style="text-align:right; font-weight:bold;  width:120px; ">'.$obj->lang['total'].'</td><td style="text-align:right; font-weight:bold;  width:90px;"  >'.$obj->formatNumber($rs[0]['total']).'</td></tr>
<tr><td></td><td style="text-align:right;font-weight:bold;" >'.$obj->lang['cashAdvance'].'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['amount']).'</td></tr>
<tr><td></td><td style="text-align:right;font-weight:bold;" >'.$obj->lang['balance'].'</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['balance']).'</td></tr>
</table>
<div style="clear:both"></div>   
'.$trnotes.'
<div style="clear:both"></div>  
'; 
      
$html .= $obj->generateSignLabel($rs); 
return $html;
}
?>
