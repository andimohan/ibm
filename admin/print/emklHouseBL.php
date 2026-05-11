<?php  
$PRINT_SETTINGS =  array(   
         'showPrintHeader' => false,
         'footer' => '',
         'pdfMarginHeader' => 8, 
		 'marginFooter' => 6
);
   

includeClass(array('EMKLHouseBL.class.php','Vessel.class.php'));
$emklHBL = new EMKLHouseBL();
$vessel =  new Vessel();

$arrContainers = array();
$arrCopy = array('Original', 'Non-Negotiable Copy');
$needAttachment = false;
///$containerIndex = 0;

$obj = $emklHBL; 

function generateHeaderTable($dataset, $param){
global $pdf;    
global $arrContainers;
//global $containerIndex;
global $needAttachment;
	
$obj = new EMKLHouseBL();
$emklJobOrder = new EMKLJobOrder();    
$customer = new Customer();
$consignee = new Consignee();
$port = new Port();
$vessel = new Vessel();
$setting = new Setting();
    
$rs = $dataset['rs'];     
$attachment = $param['attachment'];
	
$rsJobOrder = $emklJobOrder->searchData($emklJobOrder->tableName.'.pkey',$rs[0]['refheaderkey']);

$rsCustomer = $customer->getDataRowById($rs[0]['shipperkey']);
$rsConsignee = $consignee->getDataRowById($rs[0]['consigneekey']);
$rsCarrier = $consignee->getDataRowById($rs[0]['carrierkey']);
$rsPOD = $port->getDataRowById($rs[0]['podkey']);
$rsPOL = $port->getDataRowById($rs[0]['polkey']);
$rsPODelivery = $port->getDataRowById($rs[0]['podeliverykey']);
    

	
// kondisi yg harus dicek
if(!$attachment){ 		
	$rsContainerDetail = $emklJobOrder->getDetailContainer($rs[0]['refheaderkey']); 
	foreach ($rsContainerDetail as $key => $row)
		  array_push($arrContainers,array('container' => $row['containerno'], 'seal' => $row['sealno']) );
 
	$maxContainerPerPage = (empty($rs[0]['marksnumber'])) ? 7 : 5; // tentukan batasan container per halaman, tergantung dr ad marks number atau tdk
}else{ 
	$maxContainerPerPage = 9999 ; //count($arrContainers) - $containerIndex; 
}
	
if (!empty($rs[0]['description']) || count($arrContainers) > $maxContainerPerPage) $needAttachment = true; // kondisi ketika perlu attachment
	
$feederName = '';
if(!empty($rsJobOrder[0]['feederkey'])){
	$rsFeeder = $vessel->getDataRowById($rsJobOrder[0]['feederkey']);
	$feederName = $rsFeeder[0]['name'] .' '.$rsJobOrder[0]['feedernumber'];
}
    
/*$arrCustomer = array(); 
if (!empty($rsCustomer[0]['name'])) array_push($arrCustomer, $rsCustomer[0]['name']); 
if (!empty($rsCustomer[0]['address'])) array_push($arrCustomer, str_replace(chr(13),'<br>',$rsCustomer[0]['address'])); */
    
$arrCustomer = array(); 
if (!empty($rs[0]['shippername'])) array_push($arrCustomer, $rs[0]['shippername']); 
if (!empty($rs[0]['shipperaddress'])) array_push($arrCustomer, str_replace(chr(13),'<br>',$rs[0]['shipperaddress'])); 
    
$placeOfDeliveryName = (!empty($rs[0]['podeliverykey'])) ? $rsPODelivery[0]['name'] : $rsPOD[0]['name'];
$podName = (!empty($rs[0]['podkey'])) ? $rsPOD[0]['name'] :  $rsJobOrder[0]['podname'];
$polName = (!empty($rs[0]['polkey'])) ? $rsPOL[0]['name'] :  $rsJobOrder[0]['polname'];
    
// marks and number hanya muncul di halaman pertama
$marksNumber = '';
if(!$attachment){ 
	$marksNumber = (!empty($rs[0]['marksnumber'])) ? str_replace(chr(13),'<br>',$rs[0]['marksnumber']) : 'N/M';
	$marksNumber = '<tr><td>'.$marksNumber.'</td></tr>';
}
	
$party = '';
if(in_array($rsJobOrder[0]['loadcontainertypekey'], array(EMKL['container']['fcl'],EMKL['container']['trucking'])) &&  $rsJobOrder[0]['transportationtypekey'] == EMKL['shipping']['sea']){   
    $arrParty = array();    
    $rsParty = $emklJobOrder->getDetailVolume($rsJobOrder[0]['pkey']); 
    for($i=0;$i<count($rsParty);$i++) 
         array_push($arrParty,$obj->formatNumber($rsParty[$i]['qty']) . ' x ' . $rsParty[$i]['itemname']);
    $party = implode('<br>',$arrParty);
}  
    
//$heightDesc = ($needAttachment) ? 'height:250px' : 'height:260px';
$heightDesc = ($needAttachment) ? 'height:230px' : 'height:240px';
             
$carieerInformation = (!empty($rs[0]['carrierkey'])) ? $rsCarrier[0]['name'].'<br>'.str_replace(chr(13),'<br>',$rsCarrier[0]['address']) : 'Same as consignee';

if(!$attachment){ 
	// untuk jenis halaman pertama
	$description =  $rs[0]['shortdescription'];
	$attachtmentBorder = '';
	$attachContainerType = $party.' <br>'.$rs[0]['package'];
	$attachWeight =  $obj->formatNumber($rs[0]['weight'],2).' KGS';
	$attachVolume = $obj->formatNumber($rs[0]['volume'],3).' CBM';
}else{
	// untuk jenis attachment
	$description =  $rs[0]['description'];
	$attachtmentBorder = 'border-bottom:1px solid #333';
	$heightDesc =  'height:460px';
	$attachContainerType =  '';
	$attachWeight = '';
	$attachWeight = '';
	$attachVolume = '';
}	

$companyName = strtoupper($setting->loadSetting('companyName'));
$companyAddress = $setting->loadSetting('companyAddress');
    
$profileImg = $obj->loadSetting('companyLogo'); 
$logo =  (isset($_GET['logo']) && $_GET['logo'] == 0) ? '' : '<img src="'.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'" style="height:120px">';

// sementara 
if (DOMAIN_NAME == 'cif.wintera.co.id')
	$logo = '';
	
if($rs[0]['isrelease'] == 1 && !$attachment){
 	$myX = 150;  
	$myY = 190;
	
	$surrenderHTML = '<div class="surrender" style="color:#f8a0a4;  border:1px solid #f8a0a4; font-weight:bold;  font-size: 2em; text-align:center;">SURRENDER</div>'; 
	$pdf->writeHTMLCell(50, '', $myX, $myY, $surrenderHTML, 0, 0, 0, true, 'C', true);

	//reset position
	$pdf->SetXY(10,10,true);
}



$html = $obj->printSetting['defaultStyle'];
    
$html .= ' 
<style>
    .border-bottom-right{ border-bottom:1px solid #333; border-right:1px solid #333; } 
    .border-right{ border-right:1px solid #333;} 
    .border-bottom{ border-bottom:1px solid #333;} 
    .head-title{ font-weight:bold; }
</style>';

//<table style="text-align:right;"><tr><td style="width:740px">Page '.$pdf->getAliasNumPage().' of '.$pdf->getAliasNbPages().'</td></tr></table>
$html .= '
<table style="text-align:right;"><tr><td style="width:740px">Page '.$pdf->getPageNumGroupAlias().' of '.$pdf->getPageGroupAlias().'</td></tr></table>
<table cellpadding="4"> 
<tr>
<td  class="border-bottom-right" style="width:375px;"><div  style="font-size:12px;font-weight:bold;text-align:center">Carrier : '.$companyName.'</div></td>
<td class="border-bottom "  style="width:310px;font-size:12px;font-weight:bold;text-align:center">Multimodal Transport Bill of Landing	</td>
</tr>
<tr>
<td class="border-right head-title" style="width:375px;">Shipper / Exporter</td>
<td rowspan="2" class="border-bottom" style="text-align:center; height:130px; ">
'.$logo.'
</td>
</tr>
<tr>
<td  class="border-bottom-right" style="width:375px;">'.implode('<br>',$arrCustomer).'</td> 
</tr> 

</table>

<table cellpadding="4" >
<tr>
<td class="border-right head-title" style="width:375px;">Consignee (not negotiable unless consigned to order) </td>
<td class="border-right head-title" style="width:155px;">Booking No.</td>
<td class=" head-title" style="width:155px;">House Bill of Landing</td>
</tr>
<tr>
<td class="border-bottom-right" rowspan="5">'.$rs[0]['consigneename'].'<br>'.str_replace(chr(13),'<br>',$rs[0]['consigneeaddress']).'</td>
<td class="border-bottom-right">'.$rsJobOrder[0]['code'].'</td>
<td class="border-bottom">'.$rs[0]['code'].'</td>
</tr>
<tr>
<td class="border-right head-title">Master Bill of Landing</td>
<td class=" head-title">Export References</td>
</tr>
<tr>
<td  class="border-bottom-right">'.$rsJobOrder[0]['mblnumber'].'</td>
<td  class="border-bottom">'.$rs[0]['exportreference'].'</td>
</tr>
<tr>
<td class="border-right head-title">Pre-Carriage / Voyage</td>
<td class=" head-title">Place Of Initial Receipt</td>
</tr>
<tr>
<td  class="border-bottom-right" >'.$feederName.'</td>
<td  class="border-bottom" >'.$rsJobOrder[0]['polname'].'</td>
</tr>
</table> 

<table cellpadding="4" >
<tr>
<td class="border-right head-title" style="width:375px;">Notify Party (Carrier not responsible for failure to notify; see clause)</td>
<td class="border-right head-title" style="width:155px;">Vessel / Voyage</td>
<td class=" head-title" style="width:155px;">Port of Loading</td>
</tr>
<tr>
<td class="border-bottom-right" rowspan="4">'.$carieerInformation.'</td>
<td class="border-bottom-right">'.$rsJobOrder[0]['vesselname'].' '.$rsJobOrder[0]['vesselnumber'].'</td>
<td class="border-bottom">'.$polName.'</td>
</tr>
<tr>
<td class="border-right head-title">Port of Discharge</td>
<td class=" head-title">Place of Delivery</td>
</tr>
<tr>
<td  class="border-bottom-right" >'.$podName.'</td>
<td  class="border-bottom">'.$placeOfDeliveryName.'</td>
</tr>
</table> ';

$html.= '  
<table cellpadding="5" style="'.$attachtmentBorder.'">
<tr>
<td class="border-bottom-right head-title" style="width:120px;text-align:left">Marks & Numbers,<br>Container No.,<br>Seal No.</td>
<td class="border-bottom-right head-title" style="width:100px;text-align:left">No. of Containers &amp; Packages</td>
<td class="border-bottom-right head-title" style="width:285px;text-align:left">Descriptions of Packages and Goods;<br>HS Code and Other Details</td>
<td class="border-bottom-right head-title" style="width:90px;text-align:center">Gross Weight</td>
<td class="border-bottom head-title" style="width:90px;text-align:center">Measurements</td>
</tr>';
	    
if($attachment)  $html .= '<tr><td colspan="4" style="text-align:center"><b>** CONTINUATION **</b></td></tr>';

$tableContainer = '<table cellpadding="2">';
$tableContainer .= $marksNumber;

//$obj->setLog($maxContainerPerPage,true);
for($i=0;$i<$maxContainerPerPage;$i++) { 
	 if(!isset($arrContainers[$i])) break;
	 $tableContainer .= '<tr><td>'.$arrContainers[$i]['container'].'<br>'.$arrContainers[$i]['seal'].'</td></tr>';
	 unset($arrContainers[$i]);
}
	
$arrContainers = array_values($arrContainers);

$tableContainer .= '</table>';
	
$html.= '  
<tr>
<td class="border-right">'.$tableContainer.'</td>
<td class="border-right" style="'.$heightDesc.'">'.$attachContainerType.'</td>
<td class="border-right" >'.str_replace(chr(13),'<br>',$description).'</td>
<td class="border-right" style="text-align:right">'.$attachWeight.'</td>
<td class="" style="text-align:right">'.$attachVolume.'</td>
</tr>
</table>   
';    

return $html;
}

function generateFooterTable($dataset,$param){ 
global $arrCopy;
	
$obj = new EMKLHouseBL(); 
$emklJobOrder = new EMKLJobOrder(); 
$customer = new Customer();
	
$rs = $dataset['rs'];
$rsJobOrder = $emklJobOrder->searchData($emklJobOrder->tableName.'.pkey',$rs[0]['refheaderkey']);
	
$rsDetail = $emklJobOrder->getDetailByColumn($emklJobOrder->tableNameDetail.'.pkey',$rs[0]['refkey']);
//$paymentType = ($rsDetail[0]['freighttermkey'] == 1) ? 'Origin' : 'Destination';

$paymentType = $emklJobOrder->getFreightTerm($rsDetail[0]['freighttermkey']) ;

$rsAgent =  $customer->getDataRowById($rs[0]['agentkey']);

$arrAgentInformation = array();
if(!empty($rsAgent)){
	array_push($arrAgentInformation, $rsAgent[0]['name']);
	array_push($arrAgentInformation, $rsAgent[0]['address']);
}
	
//	
//<table cellpadding="4" style="border-top:1px solid #333" >
//<tr>
//<td class="border-right head-title" style="width:250px;">Merchants Declared Value:</td>
//<td class=" head-title" style="width:440px;">Note</td>
//</tr>
//<tr>    
//<td class="border-bottom-right">'.$rs[0]['merchant'].'</td>
//<td class="border-bottom">'.$rs[0]['note'].'</td> 
//</tr>
//</table>
//	
//<tr>
//<td class="border-right head-title" style="width:120px;">Freight & Charges</td>
//<td class="border-right head-title" style="width:130px;">Revenue Tons</td>
//<td class="border head-title" style="width:120px;">Rate </td>
//<td class="border-right head-title" style="width:120px;">Per </td>
//<td class="border-right head-title" style="width:100px;">Prepaid</td>
//<td class="head-title" style="width:100px;">Collect</td>
//</tr>
		
$html = ' 

<table cellpadding="4" style="border-top:1px solid #333" >
<tr>
<td colspan="2" rowspan="2" class="border-bottom-right head-title"  style="width:250px; height:70px; text-align:center; font-size:2em ">'.$arrCopy[$param['originalLabelKey']].'</td> 
<td colspan="2" class="border head-title"  style="width:430px;">For Delivery, Please Kindly Apply to: </td>
</tr> 
<tr> 
<td colspan="2"  class="border-bottom">'.implode('<br>',$arrAgentInformation).'</td> 
</tr> 
<tr>
<td class="border-right head-title">Exchange Rate</td>
<td class="border-right head-title">Prepaid / Collect</td>
<td class="border-right head-title">No of Original Bill of Landings: </td>
<td class="border head-title">Place and Date of Issue</td>
</tr>
<tr>
<td class="border-bottom-right"></td>
<td class="border-bottom-right">'.$paymentType[0]['name'].'</td>
<td  class="border-bottom-right">Three (3) Original Copies</td>
<td  class="border-bottom">Jakarta, '.$obj->formatDBDate($rsJobOrder[0]['etdpol'],'d / m / Y',array('returnOnEmpty' => true)).'</td>
</tr>

<tr>
<td colspan="3" class="border-bottom-right " style="font-size:9px">In accepting this Bill of Landing, the merchant of the goods expressly accept and agree to all its stipulations, exceptions, conditions, whether written, stamped or printed, as fully as if signed by the merchants themselves. No agent is authroized to waive any of the provisions of the clauses. In Witness Whereoff, the merchants has affirmed to this Bill of Landing. All of this tenor and date. One of which being accomplished, the others to stand void.<br><br>Received by the carrier in apparent good order and condition unless otherwise indicated herein, the goods, or the container(s) or package(s) said to contain the cargo herein metnioned to be carried subject to all the terms and conditions provided for on the fact and back of this Bill of Landing by the vessle named herein or any substitute at the Carriers option and/or other means of transport, from the place of receipt or the port of loading to the port of discharge or the place of delivery shown herein and there to be deliver unto order or assigns.
An enlarged copy of the back clauses is available from the Carrier upon request.
</td>
<td  class="border-bottom">As Carrier,</td>
</tr>
</table> 
';
    
return $html;    
}

$content = function ($dataset){ 
	global $needAttachment;
	global $arrCopy;
	 
	$returnHTML = array();
	foreach($arrCopy as $index=>$label){
		$needAttachment = false;
		
		$html = generateHeaderTable($dataset,array('attachment' => false,   'originalLabelKey' => $index)); 
		$html .= ($needAttachment) ?  '<div style="width:680px; text-align:center;"><b>** TO BE CONTINUED ON ATTACHED LIST **</b></div>' : '';  
		$html .= generateFooterTable($dataset,array('attachment' => false, 'originalLabelKey' => $index )); 
		array_push($returnHTML,$html);

		// kalo ada attachment
		if($needAttachment){ 
			$html = generateHeaderTable($dataset,array('attachment' => true ));   
			array_push($returnHTML,$html);
		} 
	}
	return $returnHTML;
};

$generateReportContent = array();
array_push($generateReportContent , array('content' => $content));

?>