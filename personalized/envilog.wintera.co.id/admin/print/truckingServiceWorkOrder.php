<?php  
  $pdf->setCustomSettings(
    array( 
         'showPrintHeader' => false,
         'footer' => '', 
         ) 
);  

$generateReportContent = function ($dataset){  
    
    
$truckingServiceOrder = new TruckingServiceOrder();     
$truckingServiceWorkOrder = new TruckingServiceWorkOrder(); 
//$truckingPurchase = new TruckingPurchase(); 
    
$obj = new $truckingServiceWorkOrder();  
    
$setting = new Setting();
$item = new Item();
$supplier = new Supplier();
    
$rs = $dataset['rs']; 
 
$rsJO = $truckingServiceOrder->searchDataRow(array($truckingServiceOrder->tableName.'.code',$truckingServiceOrder->tableName.'.trdate', $truckingServiceOrder->tableName.'.donumber',
                                                   $truckingServiceOrder->tableName.'.shipmentnumber', $truckingServiceOrder->tableName.'.poreference'),
                                            ' and '  .$truckingServiceOrder->tableName.'.pkey  = ' .$obj->oDbCon->paramString($rs[0]['refkey'])
                                             );

//$rsSPK =  $truckingPurchase->getDetailById($rsTruckingPurchase[0]['pkey']);
//$arrSPKKey =  array_column($rsSPK,'wokey');    

$joCode = $rsJO[0]['code'];
$doNumber = $rsJO[0]['donumber'];
$shipmentNumber = $rsJO[0]['shipmentnumber'];  
$poReference = $rsJO[0]['poreference'];    

$routeFrom = $rs[0]['routefrom'];
$routeTo = $rs[0]['routeto'];
$route = $routeFrom . ' - ' . $routeTo;

$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
    
$companyPhone = $setting->getDetailByCode('companyPhone');
$companyAddress = $setting->loadSetting('companyAddress');
$arrCompanyPhone = array();  
for($i=0;$i<count($companyPhone);$i++) 
    array_push($arrCompanyPhone, $companyPhone[$i]['value']);

$companyContact = '';
if(!empty($arrCompanyPhone))
    $companyContact = implode (', ', $arrCompanyPhone);
    
$companyName = strtoupper($setting->loadSetting('companyName'));

$profileImg = $obj->loadSetting('companyLogo'); 
$img = HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=200&h=60&hash='.getPHPThumbHash($profileImg);
$proforma = ($rs[0]['statuskey'] == 1) ? '<div style="font-weight:normal; font-size:0.9em">(PROFORMA)</div>' : '';
$html = $obj->printSetting['defaultStyle'];
    
$tableWidth = '670';    
$html .= '<table >
<tr>
    <td style="width:415px;">
        <table cellpadding="3" style=""> 
            <tr>
                <td style="vertical-align:middle; width:120px" ><img src="'.$img.'"></td>
                <td style="width: 300px;"><b>'.$companyName.'</b><br>'.str_replace(chr(13),'<br>',$companyAddress).'
                </td>
            </tr>
        </table>
    </td> 
    <td style="width:255px; text-align:right">
        <table cellpadding="2" style="width:245px;"> 
            <tr><td style="text-align:right;font-size:20px;">SHIPPING INSTRUCTION</td></tr>   
            <tr> <td style="text-align:right;">'.$rs[0]['code'].'</td></tr>   
        </table>
    </td>
</tr>  
</table>
<div style="border-bottom:2px solid black; clear:both;"></div>
';
    
$html .= '<br>
<table cellpadding="2"> 
<tr><td class="" style="width:480px">To :</td><td style="width:50px"></td><td style="width:140px;"><strong>Order ID</strong></td></tr>
<tr><td rowspan="4"><strong>'.strtoupper($rsSupplier[0]['name']) .'</strong><br>'.str_replace(chr(13),'<br>',strtoupper($rsSupplier[0]['address'])).'</td><td></td><td><strong>'.$joCode.'</strong></td></tr>
<tr><td></td><td></td></tr>
<tr><td></td><td><strong>Issued Date</strong></td></tr>
<tr><td></td><td><strong>'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</strong></td></tr>
</table> 
<div style="border-bottom:2px solid black; clear:both;"></div>';
      
$html .='
<br>
<table cellpadding="2" style="font-size:0.9em;">
<tr class=""><td style="width:120px">Booking Code :</td><td style="width:120px">Kode Ref :</td><td style="width:190px;">Container Quantity :</td><td style="width:240px">Route :</td></tr>  
';
        
    
$color = '#333';
$serviceSPK = '<table cellpadding="4" style="font-size:0.9em;">';
$arrServicePPN = array();
$arrServicePPH = array();
$totalServiceSPK = 0;
$totalReimburse = 0;
    
$serviceReimburse = ''; 
$colspan = 6;
 
$qtyContainer = array(); 
$rsCarDetail = array();
$arrCarGroup = array(); 
$arrContainerNumber = array();
    
// detail item 
$rsItemDetail =  $obj->getCostDetail($rs[0]['pkey'],'',' and  '.$obj->tableSupplier.'.pkey = '.$obj->oDbCon->paramString($rs[0]['supplierkey']),'');
    
$arrItemKey = array_column($rsItemDetail, 'costkey');
$rsItem = $item->searchDataRow( array($item->tableName.'.pkey',$item->tableName.'.reimburse', $item->tableName.'.servicecost') , 
                        ' and '.$item->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrItemKey,',').')'  
               ); 


$rsItem = array_column($rsItem,null,'pkey'); 
  
$rsCarDetail = $obj->getCarDetail($rs[0]['pkey']);

for($j=0;$j<count($rsCarDetail);$j++){ 

    $itemkey = $rsCarDetail[$j]['itemkey'];
    $qty =  $rsCarDetail[$j]['qty'];
    $amount = $rsCarDetail[$j]['price'];
    $ppnValue = $rsCarDetail[$j]['taxpercentage'];

    if($amount <=0 ) continue;

    if($rsCarDetail[$j]['taxvalue']>0){
          $indexTax = $obj->formatNumber($ppnValue);
          if(!isset($arrServicePPN[$indexTax])) $arrServicePPN[$indexTax] = 0;
          $arrServicePPN[$indexTax] +=  $rsCarDetail[$j]['taxvalue'];
    } 

    if($rsCarDetail[$j]['tax23value']>0){
          $indexTax = $obj->formatNumber($rsCarDetail[$j]['tax23percentage']);
          if(!isset($arrServicePPH[$indexTax])) $arrServicePPH[$indexTax] = 0;
          $arrServicePPH[$indexTax] +=  $rsCarDetail[$j]['tax23value'];
    }

    $totalServiceSPK += ($qty * $amount);
    $itemname = $rsCarDetail[$j]['itemname'];
    //$ppn = ($ppnValue > 0) ? '(PPN '.$obj->formatNumber($ppnValue).'%)' : '';
    $indexItem = $itemkey.'-'.$amount.'-'. $ppnValue;
    if(!isset($arrCarGroup[$indexItem])){
        $arrCarGroup[$indexItem]['itemkey'] = $itemkey;
        $arrCarGroup[$indexItem]['itemname'] = $itemname;
        $arrCarGroup[$indexItem]['price'] = $amount;
        $arrCarGroup[$indexItem]['ppn'] = $ppnValue;
        $arrCarGroup[$indexItem]['qty'] = $qty;
    }else{
        $arrCarGroup[$indexItem]['qty'] += $qty;
    }

    // hitung total container
    if($rsItem[$itemkey]['servicecost'] == 0){
        if(!isset($qtyContainer[$itemname]))   $qtyContainer[$itemname] = 0;  
        $qtyContainer[$itemname] += $qty;      
    }      

    if(!empty($rsCarDetail[$j]['container'])) array_push($arrContainerNumber,$rsCarDetail[$j]['container']); 

}    


$containerNumber = implode(', ', $arrContainerNumber );
    

	
foreach($arrCarGroup as $row){
		$ppn = ($row['ppn'] > 0) ? '(PPN '.$obj->formatNumber($row['ppn']).'%)' : '';
		$serviceSPK .=  '<tr>
		<td style="width:200px;">'.strtoupper($row['itemname']).'</td>
		<td style="width:100px;">'.$ppn.'</td>
		<td style="width:50px; text-align:right">'.$obj->formatNumber($row['qty']).'</td>
		<td style="width:20px;">X</td>
		<td style="text-align:right;width:100px;">'.$obj->formatNumber($row['price']).'</td>
		<td style="text-align:right;width:30px;">Rp.</td>
		<td style="text-align:right;width:80px;">'.$obj->formatNumber($row['qty'] * $row['price']).'</td>
		<td style="width:90px;"></td>
		</tr>'; 
	
}
    
for($j=0;$j<count($rsItemDetail);$j++){  
     
    $itemkey = $rsItemDetail[$j]['costkey'];
    $qty =  $rsItemDetail[$j]['qty'];
    $amount = ( $rsItemDetail[$j]['realizationkey'] != 0 ) ? $rsItemDetail[$j]['amount'] :  $rsItemDetail[$j]['requestamount']; 
    
    if($rsItemDetail[$j]['isreimburse']){
        // gk ad pph, jd gk masuk reimburse
        
        $totalReimburse += $rsItemDetail[$j]['total'];
        $itemname =  $rsItemDetail[$j]['name'];
        $serviceReimburse .= '<tr>
        <td colspan="'.($colspan-1).'">'.strtoupper($itemname).'</td>
        <td style="text-align:right;">Rp.</td>
        <td style="text-align:right;">'.$obj->formatNumber($rsItemDetail[$j]['total']).'</td>
        <td></td>
        </tr>'; 
    }else{
        if($rsItemDetail[$j]['taxvalue']>0){
              $indexTax = $obj->formatNumber($rsItemDetail[$j]['taxpercentage']);
              if(!isset($arrServicePPN[$indexTax])) $arrServicePPN[$indexTax] = 0;
              $arrServicePPN[$indexTax] +=  $rsItemDetail[$j]['taxvalue'];
        } 
        
        if($rsItemDetail[$j]['tax23value']>0){
              $indexTax = $obj->formatNumber($rsItemDetail[$j]['tax23percentage']);
              if(!isset($arrServicePPH[$indexTax])) $arrServicePPH[$indexTax] = 0;
              $arrServicePPH[$indexTax] +=  $rsItemDetail[$j]['tax23value'];
        }

        $totalServiceSPK += ($qty * $amount);
        $itemname = $rsItemDetail[$j]['name'];
        $ppn = ($rsItemDetail[$j]['taxpercentage'] > 0) ? '(PPN '.$obj->formatNumber($rsItemDetail[$j]['taxpercentage']).'%)' : '';
        $serviceSPK .=  '<tr>
        <td style="width:200px;">'.strtoupper($itemname).'</td>
        <td style="width:100px;">'.$ppn.'</td>
        <td style="width:50px;text-align:right">'.$obj->formatNumber($qty).'</td>
        <td style="width:20px;">X</td>
        <td style="text-align:right;width:100px;">'.$obj->formatNumber($amount).'</td>
        <td style="text-align:right;width:30px;">Rp.</td>
        <td style="text-align:right;width:80px;">'.$obj->formatNumber($qty * $amount).'</td>
        <td style="width:90px;"></td>
        </tr>'; 
  
    } 
}  

// total container
$containerQtyInformation = array();
foreach($qtyContainer as $key=>$qty) 
    array_push( $containerQtyInformation , $qty.'x '.$key);

$containerQtyInformation = implode('<br>',$containerQtyInformation);


$html .= '<tr><td style="font-weight:bold ">'.$doNumber.'</td><td style="font-weight:bold ">'.$poReference.'</td><td style="font-weight:bold ">'.strtoupper($containerQtyInformation).'</td><td style="font-weight:bold ">'.$route.'</td></tr>';
    
$serviceSPK .= '<tr>
<td colspan ="'.$colspan.'">Sub Total</td>
<td style="text-align:right;">Rp.</td>
<td style="text-align:right;">'.$obj->formatNumber($totalServiceSPK).'</td>
</tr>';

$totalPPN = 0;  
foreach($arrServicePPN as $key=>$value){
    $serviceSPK .= '<tr>
    <td colspan ="'.$colspan.'">PPN '.$key.'%</td>
    <td style="text-align:right;">Rp.</td>
    <td style="text-align:right;">'.$obj->formatNumber($value).'</td>
    </tr>';
    $totalPPN += $value;
}
    
$totalPPH = 0;   
foreach($arrServicePPH as $key=>$value){
    $serviceSPK .= '<tr>
    <td colspan ="'.$colspan.'">PPH 23 '.$key.'%</td>
    <td style="text-align:right;">Rp.</td>
    <td style="text-align:right;">- '.$obj->formatNumber($value).'</td>
    </tr>';
    $totalPPH += $value;
}
    
$total =  $totalPPN + $totalServiceSPK - $totalPPH;
    
$serviceSPK .= '<tr class="col-header" style="background-color: #dedede;">
<td colspan ="'.$colspan.'">SERVICES TOTAL</td>
<td style="text-align:right;">Rp.</td>
<td style="text-align:right;">'.$obj->formatNumber($total).'</td>
</tr>';
     
if($totalReimburse>0){
   $serviceSPK .= $serviceReimburse; 
   $serviceSPK .= '<tr class="col-header" style="background-color: #dedede;">
<td colspan ="'.$colspan.'">REIMBURSEMENT TOTAL</td>
<td style="text-align:right;">Rp.</td>
<td style="text-align:right;">'.$obj->formatNumber($totalReimburse).'</td>
</tr>';
} 
    
$serviceSPK .= '<tr class="col-header" style="background-color: #dedede;">
<td colspan ="'.$colspan.'">GRAND TOTAL</td>
<td style="text-align:right;">Rp.</td>
<td style="text-align:right;">'.$obj->formatNumber(($total+$totalReimburse)).'</td>
</tr>';

$serviceSPK .= '</table>';
      
$html .= '</table>';
    
$html .= '<br><br>'.$serviceSPK;

$html .= ' <div style="clear:both; border-bottom:2px solid black"></div>';
    
$html .= '<br>
<strong>Container Number</strong>:<br>
'.$containerNumber.'
<div style="clear:both"></div>
Stuffing / Unstuffing Date :<br>
'.$obj->formatDBDate($rsJO[0]['trdate'],'d / m / Y').' 
<div style="clear:both"></div>';
    
    

//$html .= '<table>
//<tr><td>Note :<br>'.$rsSPK[0]['trdesc'].'</td></tr>
//</table>';  
    
$html .= '<table>
    <tr>
        <td style="width:480px">
        </td>
        <td style="width:190px; text-align:center">
                Jakarta, '.$obj->formatDBDate($rs[0]['trdate'],'d F Y').'<br><br><br><br><br><br><br><br><br>
                Billing Team
        </td>
    </tr>   
</table>';    
    
return $html;
}

?>