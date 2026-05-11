<?php  
$PRINT_SETTINGS =  array(   
         'showPrintHeader' => false,
         'footer' => '',
		 'marginFooter' => 6
);
   

includeClass(array('EMKLJobOrderHeader.class.php','EMKLJobOrder.class.php','Vessel.class.php','ItemUnit.class.php'));
$emklJobOrderHeader = new EMKLJobOrderHeader();
$emklJobOrder = new EMKLJobOrder();
$vessel =  new Vessel();
$itemUnit =  new ItemUnit();

$arrContainers = array();
$needAttachment = false;  

$obj = (isset($_GET['joborder']) && $_GET['joborder'] == 1) ? $emklJobOrder : $emklJobOrderHeader; 
   
function generateHeaderTable($dataset, $param){

$obj = new EMKLJobOrderHeader();    
    

$setting = new Setting();

$rs = $dataset['rs'];    
$attachment = $param['attachment'];

$companyName = strtoupper($setting->loadSetting('companyName'));
$companyAddress = $setting->loadSetting('companyAddress');
    
$profileImg = $obj->loadSetting('companyLogo'); 
$img = $obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg ;
    
$html = $obj->printSetting['defaultStyle'];

$html .= '
<table >
<tr>
<td style="width:345px">
<table cellpadding="2" style="border:1px solid black;">
<tr>
<td rowspan="2" style="width:105px;height:60px;line-height:60px">
<img src="'.$img.'">
</td>
<td style="width:240px;height:60px;text-align:center;">
<span style="font-size:12px;">'.$companyName.'</span><br>
<span style="font-size:9px;">'.$companyAddress.'</span>
</td>
</tr>
</table>
</td>
<td style="width:330px">
<table cellpadding="2" >
<tr><td  style="border:1px solid black;font-size:16px;text-align:center;background-color:#eee;font-weight:bold">Shipping Instruction</td></tr>
<tr><td style="border:1px solid black;height:36px;"></td></tr>
</table>
</td>
</tr>
<tr><td colspan="2" style="border:1px solid black;line-height:5px;background-color:#eee"></td></tr>
<tr><td colspan="2" style="border:1px solid black;"></td></tr>
<tr><td colspan="2" style="border:1px solid black;line-height:5px;background-color:#eee"></td></tr>';
    
return $html;

}

function generateFirstRowTable($dataset, $param){

$obj = new EMKLJobOrderHeader();

    
$emklJobOrder = new EMKLJobOrder();
$customer = new Customer();
$consignee = new Consignee();
$port = new Port();
$city = new City();
$vessel = new Vessel();

$rs = $dataset['rs'];    
    
if(isset($rs[0]['headerorderkey']) && isset($_GET['joborder']) && $_GET['joborder'] == 1){
    $rsSI = $obj->getDataRowById($rs[0]['headerorderkey'])  ;

}else{
    $rsSI = $rs;
}
    
$attachment = $param['attachment'];

$rsSIShipper= $customer->getDataRowById($rsSI[0]['sishipperkey']);
$rsNotify = $customer->getDataRowById($rsSI[0]['notifykey']);
$rsNotify2 = $customer->getDataRowById($rsSI[0]['notifykey2']);
$rsPODelivery = $port->getDataRowById($rs[0]['placeofdeliverykey']);
$rsPOReceipt = $port->getDataRowById($rs[0]['placeofreceiptkey']);
$rsPOIssue = $city->searchData('city.pkey',$rsSI[0]['placeofissuekey'],true);

$rsBillType= $emklJobOrder->getBillType($rsSI[0]['billtypekey']);
$rsSeelingTermFreight = $emklJobOrder->getFreightTerm($rsSI[0]['freighttermkey']);

$arrSIShipper = array(); 
if (!empty($rsSIShipper[0]['name'])) array_push($arrSIShipper, $rsSIShipper[0]['name']); 
if (!empty($rsSIShipper[0]['address'])) array_push($arrSIShipper, str_replace(chr(13),'<br>',$rsSIShipper[0]['address'])); 
 
$arrNotifyParty = array(); 
if (!empty($rsNotify[0]['name'])) array_push($arrNotifyParty, $rsNotify[0]['name']); 
if (!empty($rsNotify[0]['address'])) array_push($arrNotifyParty, str_replace(chr(13),'<br>',$rsNotify[0]['address'])); 
        
$arrNotifyParty2 = array(); 
if (!empty($rsNotify2[0]['name'])) array_push($arrNotifyParty2, $rsNotify2[0]['name']); 
if (!empty($rsNotify2[0]['address'])) array_push($arrNotifyParty2, str_replace(chr(13),'<br>',$rsNotify2[0]['address'])); 
            
$mbl = (isset($_GET['joborder']) && $_GET['joborder'] == 1) ? $rs[0]['mblnumber'] : $rs[0]['mbl'];

$html = '<tr>
<td >
<table cellpadding="2" style="">
<tr><td style="border:1px solid black;font-weight:bold;line-height:20px;">Shipper (complete name and address)* :</td></tr>
<tr><td style="border:1px solid black;height:80px;">'.implode('<br>',$arrSIShipper).'</td></tr>
<tr><td style="border:1px solid black;font-weight:bold;">Consignee (complete name and address)* :</td></tr>
<tr><td style="border:1px solid black;height:80px">'.$rsSI[0]['siconsigneename'].'<br>'.$rsSI[0]['siconsigneeaddress'].'</td></tr>
<tr><td style="border:1px solid black;font-weight:bold;">Notify Party (complete name and address)* :</td></tr>
<tr><td style="border:1px solid black;height:80px">'.implode('<br>',$arrNotifyParty).'</td></tr>
</table>
</td>
<td >
<table cellpadding="2" style="">
<tr><td style="border:1px solid black;font-weight:bold;font-size:16px;">BOOKING NUMBER : '.$mbl.'</td></tr>
<tr><td style="border:1px solid black;height:80px;font-size:20px">'.$rsBillType[0]['name'].'</td></tr>
<tr><td style="border:1px solid black;font-weight:bold;">Export / Customer Reference </td></tr>
<tr>
<td style="border:1px solid black;height:80px">
<b>PEB :</b> '.$rs[0]['aju'].' <br>
<b>DATE :</b> '.$obj->formatDBDate($rsSI[0]['pebdate'],'d / m / Y',array('returnOnEmpty'=>true)).' <br>
<b>HS CODE :</b> '.$rsSI[0]['hscode'].' <br>
<b>KPBC :</b> '.$rsSI[0]['kpbc'].'
</td>
</tr>
<tr><td style="border:1px solid black;font-weight:bold;">Notify Party 2 (complete name and address)* :</td></tr>
<tr><td style="border:1px solid black;height:80px">'.implode('<br>',$arrNotifyParty2).'</td></tr>
</table>
</td>
</tr>
<tr><td colspan="2" style="border:1px solid black;line-height:5px;background-color:#eee"></td></tr>
<tr>
<td>
<table cellpadding="2"><tr><td style="border:1px solid black;font-weight:bold;">Place of issue of B/L :  '.$rsPOIssue[0]['citycategoryname'].'</td></tr></table>
</td>
<td>
<table cellpadding="2"><tr><td style="border:1px solid black;font-weight:bold;">Payment Term (Prepaid or Collect) : '.$rsSeelingTermFreight[0]['name'].'</td></tr></table>
</td>
</tr>
<tr><td colspan="2" style="border:1px solid black;line-height:5px;background-color:#eee"></td></tr>
<tr>
<td>
<table cellpadding="2">
<tr>
<td style="border:1px solid black;line-height:20px;font-weight:bold;">Vessel:</td>
<td style="border:1px solid black;line-height:20px;font-weight:bold;">Voyage Number:</td>
</tr>
<tr>
<td style="border:1px solid black;">'.$rs[0]['vesselname'].'</td>
<td style="border:1px solid black;">'.$rs[0]['vesselnumber'].'</td>
</tr>
<tr>
<td style="border:1px solid black;line-height:20px;font-weight:bold;">ETD :</td>
<td style="border:1px solid black;line-height:20px;font-weight:bold;">ETA :</td>
</tr>
<tr>
<td style="border:1px solid black;">'.$obj->formatDBDate($rs[0]['etdpol'],'d / m / Y',array('returnOnEmpty'=>true)).'</td>
<td style="border:1px solid black;">'.$obj->formatDBDate($rs[0]['etapod'],'d / m / Y',array('returnOnEmpty'=>true)).'</td>
</tr>
<tr>
<td style="border:1px solid black;line-height:20px;font-weight:bold;">Port of Landing*:</td>
<td style="border:1px solid black;line-height:20px;font-weight:bold;">Port of Discharge*:</td>
</tr>
<tr>
<td style="border:1px solid black;">'.$rs[0]['polname'].'</td>
<td style="border:1px solid black;">'.$rs[0]['podname'].'</td>
</tr>
</table>
</td>
<td>
<table cellpadding="2">
<tr><td style="border:1px solid black;line-height:20px;"></td></tr>
<tr><td style="border:1px solid black;"></td></tr>
<tr><td style="border:1px solid black;line-height:10px;"><b>Place of receipt</b> <span style="font-size:9px">(Only mandatory in case of inland transport under carriers responbililty)</span></td></tr>
<tr><td style="border:1px solid black;">'.$rsPOReceipt[0]['name'].'</td></tr>
<tr><td style="border:1px solid black;line-height:10px;"><b>Place of delivery</b> <span style="font-size:9px">(Only mandatory in case of inland transport under carriers responbililty)</span></td></tr>
<tr><td style="border:1px solid black;">'.$rsPODelivery[0]['name'].'</td></tr>
</table>
</td>
</tr>
<tr><td colspan="2" style="border:1px solid black;line-height:5px;background-color:#eee"></td></tr>
';
    
return $html;
}

function generateSecondRowTable($dataset, $param){
global $arrContainers;
global $needAttachment;
if(isset($_GET['joborder']) && $_GET['joborder'] == 1){
$obj = new EMKLJobOrder();

}else{
    $obj = new EMKLJobOrderHeader();

}
$itemUnit =  new ItemUnit();

$rs = $dataset['rs'];    
$attachment = $param['attachment'];

 $arrWeightContainer = array();
$arrVolumeContainer = array();
    
if(!$attachment){ 		
    $rsContainerDetail= $obj->getDetailContainer($rs[0]['pkey']);
	foreach ($rsContainerDetail as $key => $row){
            
            
		  array_push($arrContainers,array('container' => $row['containerno'], 'seal' => $row['sealno'] , 'qty' => $row['qty'],'unitname' => $row['unitname'],'weight' => $row['weight'], 'volume' => $row['volume']) );
          array_push($arrWeightContainer, $obj->formatNumber($row['weight'],2).' KGS');
          array_push($arrVolumeContainer,$obj->formatNumber($row['volume'],3).' CBM');
    }
	$maxContainerPerPage = 3; // tentukan batasan container per halaman, tergantung dr ad marks number atau tdk
}else{ 
	$maxContainerPerPage = 9999 ; //count($arrContainers) - $containerIndex; 
}    

if ((!empty($rs[0]['attachment'])) || count($arrContainers) > $maxContainerPerPage) $needAttachment = true; // kondisi ketika perlu attachment

$tableContainer = '<table cellpadding="1">';

    $totalWeight = 0;
    $totalVolume = 0;
for($i=0;$i<$maxContainerPerPage;$i++) { 
	 if(!isset($arrContainers[$i])) break;
    
    
    $totalWeight += $arrContainers[$i]['weight'];
    $totalVolume += $arrContainers[$i]['volume'];
	 $tableContainer .= '<tr><td style="font-weight:bold;font-size:10px;">'.$arrContainers[$i]['container'].' // '.$arrContainers[$i]['seal'].' // '.$obj->formatNumber($arrContainers[$i]['qty'],2).' '.$arrContainers[$i]['unitname'].' // '.$obj->formatNumber($arrContainers[$i]['weight'],2).' KGS // '.$obj->formatNumber($arrContainers[$i]['volume'],2).' CBM</td></tr>';
	 unset($arrContainers[$i]);
}
	
$arrContainers = array_values($arrContainers);

$tableContainer .= '</table>';    
    
if(!$attachment){ 
	// untuk jenis halaman pertama
	$description =  $rs[0]['itemdescription'];
	$heightWeight =  'height:178.1px';
	$heightContainer =  'height:60px';
	$attachWeight = $obj->formatNumber($totalWeight,2).' KGS';
	$attachVolume = $obj->formatNumber($totalVolume,3).' CBM';
}else{
	// untuk jenis attachment
	$description =  $rs[0]['attachment'];
	$heightWeight =  'height:356.2px';	
	$heightContainer =  'height:238.1px';	
    $attachContainerType =  '';
	$attachWeight = '';
	$attachVolume = '';
}	    
    
    
$html .= '<tr>
<td colspan="2">
<table cellpadding="2">
<tr><td  style="border:1px solid black;font-weight:bold;">Particulars as furnished by shipper - Carrier not responsible</td></tr>
</table>
</td>
</tr>
<tr>
<td  style="width:385px;">
<table cellpadding="2">
<tr><td style="border:1px solid black;">Kind of Packages*; Description of Goods*; Marks & Numbers.*</td></tr>
<tr><td style="border:1px solid black;height:100px;">'.	str_replace(chr(13),'<br>',$description).'</td></tr>
<tr><td style="border:1px solid black;">Container & Seal No. *</td></tr>
<tr><td style="border:1px solid black;'.$heightContainer.'">'.$tableContainer.'</td></tr>
</table>
</td>
<td style="width:145px;">
<table cellpadding="2">
<tr><td style="border:1px solid black;text-align:center;">Gross Weight * (KGS)</td></tr>
<tr><td style="border:1px solid black;text-align:center;font-weight:bold;'.$heightWeight.'">'.$attachWeight.'</td></tr>
</table>
</td>
<td style="width:145px;">
<table cellpadding="2">
<tr><td style="border:1px solid black;text-align:center:">Measurement * (CBM)</td></tr>
<tr><td style="border:1px solid black;text-align:center;font-weight:bold;'.$heightWeight.'">'.$attachVolume.'</td></tr>
</table>
</td>
</tr>
<tr><td colspan="3" style="border:1px solid black;line-height:5px;background-color:#eee"></td></tr>';
    
return $html;
        
}

function generateThirdRowTable($dataset, $param){

$obj = new EMKLJobOrderHeader();
    
$rs = $dataset['rs'];    
$attachment = $param['attachment'];
    
$arrFreightComponents = array( 'Ocean Freight','Origin Local Charges','Destination Local Charges');

    
$arrCheckFreight = array();

    
foreach($arrFreightComponents as $key => $row){

    switch ($key){
            
        case 0:
            $checkListPrepaid = ($rs[0]['freighttermkey'] == 1) ? 1 : 0;
            $checkListCollect = ($rs[0]['freighttermkey'] == 1) ? 0 : 1;
        break;
        case 1:
            $checkListPrepaid = 1;
            $checkListCollect = 0;
        break;
        case 2:
            $checkListPrepaid = 0;
            $checkListCollect = 1;
        break;
        default:
            $checkListPrepaid = 0;
            $checkListCollect = 0;  
        break;
            
    }
    
    
    array_push($arrCheckFreight, array('name' => $row, 'prepaid' =>$checkListPrepaid, 'collect'=> $checkListCollect ));
    
}
    
$tableComponents = '<table cellpadding="2">
<tr>
<td rowspan="2" style="border:1px solid black;line-height:32px;font-weight:bold;">Freight Components</td>
<td colspan="3" style="border:1px solid black;text-align:center;">(Please select one) *</td></tr>
<tr>
<td style="border:1px solid black;width:120px;font-weight:bold;text-align:center;">Prepaid</td>
<td style="border:1px solid black;width:120px;font-weight:bold;text-align:center;">Collect</td>
<td style="border:1px solid black;width:266px;font-weight:bold;text-align:center;">To be paid by?</td>
</tr>
';    
    
foreach($arrCheckFreight as $key => $row){
    
    $bgChecklistPrepaid = ($row['prepaid'] == 1) ? 'background-color:black' : '';
    $bgChecklistCollect = ($row['collect'] == 1) ? 'background-color:black' : '';
    $tableComponents .= '
    
    <tr>
        <td style="border:1px solid black;">'.$row['name'].'</td>
        <td style="border:1px solid black;">
            <table >
                <tr><td style="width:47px;line-height:5px;"></td><td style="width:5px;line-height:5px;"></td><td style="width:5px;line-height:5px;"></td></tr>
                <tr><td style="width:47px;line-height:5px;"></td><td style="width:5px;line-height:5px;border:1px solid black;'.$bgChecklistPrepaid.'"></td><td style="width:5px;line-height:5px;"></td></tr>
            </table>
        </td>
        <td style="border:1px solid black;">
            <table >
                <tr><td style="width:47px;line-height:5px;"></td><td style="width:5px;line-height:5px;"></td><td style="width:5px;line-height:5px;"></td></tr>
                <tr><td style="width:47px;line-height:5px;"></td><td style="width:5px;line-height:5px;border:1px solid black;'.$bgChecklistCollect.'"></td><td style="width:5px;line-height:5px;"></td></tr>
            </table>        
        </td>
        <td style="border:1px solid black;"></td>
    </tr>
    ';
    
    
}    
    
$tableComponents .= '</table>';
    
    
$html = '<tr>
<td colspan="3">
'.$tableComponents.'
</td>
</tr>
<tr><td colspan="3" style="border:1px solid black;line-height:5px;background-color:#eee;"></td></tr>
<tr>
<td colspan="3">
<table cellpadding="2" style="border:1px solid black;">
<tr>
<td style="height:90px;font-size:10px">
<b>REMARKS</b><br>
1. DO NOT ROLL OVER (URGENT SHIPMENT)<br>
2. PLEASE PROVIDE GOOD AND CLEAN CONTAINER<br>
3. PLEASE SCAN BOOKING CONFIRMATION<br>
</td>
</tr>
</table>
</td>
</tr>
</table>
';
    
    
return $html;
    
}

$content = function ($dataset){ 

    $obj = new EMKLJobOrderHeader();

    global $needAttachment;
	
	$returnHTML = array();
	
	$html = generateHeaderTable($dataset,array('attachment' => false )); 
	$html .= generateFirstRowTable($dataset,array('attachment' => false )); 
	$html .= generateSecondRowTable($dataset,array('attachment' => false )); 
	$html .= generateThirdRowTable($dataset,array('attachment' => false )); 
	array_push($returnHTML,$html);
	
	// kalo ada attachment
	if($needAttachment){ 
		$html = generateHeaderTable($dataset,array('attachment' => true ));  
	    $html .= generateSecondRowTable($dataset,array('attachment' => true )); 
	    $html .= '</table>'; 
		array_push($returnHTML,$html);
	}
 
	return $returnHTML;
};

$generateReportContent = array();
array_push($generateReportContent , array('content' => $content));

?>
