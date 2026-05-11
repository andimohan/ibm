<?php 

includeClass('CostReconsile.class.php');
$costReconsile = createObjAndAddToCol( new CostReconsile()); 
$prepaidExpense = createObjAndAddToCol( new PrepaidExpense()); 
$emklOrderInvoice = createObjAndAddToCol( new EMKLOrderInvoice()); 
$service = createObjAndAddToCol( new Service(SERVICE)); 
$currency = createObjAndAddToCol( new Currency()); 

$obj = $costReconsile;
$generateReportContent = function ($dataset){ 

$obj = new CostReconsile();  
$emklOrderInvoice = new EMKLOrderInvoice();
$prepaidExpense = new PrepaidExpense();
$service = new Service(SERVICE);
$currency = new Currency();    
    
$rs = $dataset['rs'];
    
    
$rsCurrency = $currency->getDataRowById($rs[0]['currencykey']); 
$currencyName = $rsCurrency[0]['name'];

     
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);    
$rsDetailItemInvoice = $emklOrderInvoice->getItemDetail($rs[0]['refkey'],'refheaderkey');
$rsJOCode = $emklOrderInvoice->searchDataRow(array($emklOrderInvoice->tableName.'.salesordercodecache'), 
                                             ' and '.$emklOrderInvoice->tableName.'.pkey ='.$obj->oDbCon->paramString($rs[0]['refkey'])
											 );
     
$joCode = implode("<br>",explode(" ",$rsJOCode[0]['salesordercodecache']));
    
if($rs[0]['currencykey'] == CURRENCY['idr']){ 
    $sayCurrencyName = 'Rupiah';
}else{
	$sayCurrencyName = 'USD'; 
}
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($obj->lang['costReconsiliation']).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table cellpadding="2"> 
<tr><td class="header-row-header">'.$obj->lang['date'].'</td><td style="width:10px; text-align:center">:</td><td style="width:540px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">'.$obj->lang['invoiceNumber'].'</td><td style="text-align:center">:</td><td>'. $rs[0]['invoicecode'] .'</td></tr>    
<tr><td class="header-row-header">'.$obj->lang['jobOrderCode'].'</td><td style="text-align:center">:</td><td>'. $joCode.'</td></tr>    
<tr><td class="header-row-header">'.$obj->lang['currency'].'</td><td style="text-align:center">:</td><td>'. $currencyName.'</td></tr>    
</table> 
 
<div style="clear:both"></div> '; 

$cellArray = array();
array_push($cellArray, array('label' => $obj->lang['refCode'], 'width' => '100')); 
array_push($cellArray, array('label' => $obj->lang['reference'],'width' => '100')); 
array_push($cellArray, array('label' => $obj->lang['dateRef'], 'align' => 'center','width' => '100'));
array_push($cellArray, array('label' => $obj->lang['service']));
array_push($cellArray, array('label' => $obj->lang['amount'],'align' => 'right', 'width' => '80'));

$html .= '<table  cellpadding="4" class="table-transaction">';
$html .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray)); 
//
//$arrPrepaidKey = array_column($rsDetail,'refreconsilekey');  
//$rsPrepaidCol = $prepaidExpense->searchDataRow(array($prepaidExpense->tableName.'.pkey', 
//                                                     $prepaidExpense->tableName.'.code',  
//                                                     $prepaidExpense->tableName.'.refcode',  
//                                                     $prepaidExpense->tableName.'.currencykey',  
//                                                     $prepaidExpense->tableName.'.costkey', 
//                                                     $prepaidExpense->tableName.'.trdate',  
//                                                    ), 
//                                              
//                                                ' and '.$prepaidExpense->tableName.'.pkey in ( '.$obj->oDbCon->paramString($arrPrepaidKey,',').' ) ' 
//                                              );
//
//$rsPrepaidCol = array_column($rsPrepaidCol,null,'pkey');
//
////search costkey saat di prepaid karena didetail tidak simpan itemkey
//$arrCostKey = array_column($rsPrepaidCol,'costkey'); 
//$rsServiceCol = $service->searchDataRow(array($service->tableName.'.pkey', $service->tableName.'.name'),  
//                                             ' and '.$service->tableName.'.pkey in ( '.$obj->oDbCon->paramString($arrCostKey,',').' )' 
//                                        );
//    
//    
//$rsServiceCol = array_column($rsServiceCol,'name','pkey');   

	
for ($i=0;$i<count($rsDetail);$i++){   
       
  $html .= '<tr>
  <td>'.$rsDetail[$i]['pecode'].'</td><td>'.$rsDetail[$i]['refcode'].'</td>
  <td style="text-align:center">'. $obj->formatDBDate($rsDetail[$i]['podate']) .'</td>
  <td style="">'. $rsDetail[$i]['servicename'] .'</td>  
  <td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td>
  </tr>' ; 
}
    
$html .= '</table>' ;

    
$arrSubtotal = array(); 

    
if (!empty($arrSubtotal)) { 
   array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>'); 
}

    

     
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 
    
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);

    
    
$html .= '    
</table>  
<div style="clear:both"></div> 
<table cellpadding="4"> 
<tr><td rowspan="'.(count($arrSubtotal)+1).'" style="width:450px;"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' '.$sayCurrencyName.'. </td><td style="text-align:right; font-weight:bold;  width:120px; ">'.$subtotalLabel.'</td><td style="text-align:right; font-weight:bold;  width:110px;"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>
';


$html .= implode('',$arrSubtotal); 
    
$html .= '</table>';
 

   

$html .= '
<table cellpadding="4">
<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trnotes']).'</td></tr>
</table>
<div "clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>
