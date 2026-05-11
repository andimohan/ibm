<?php  

$PRINT_SETTINGS=array(
    'showPrintHeader' => false,
    'showPrintFooter' => false,
	'marginFooter' => 0,
	'pdfMarginHeader' => 5,
);

includeClass('LogisticSalesOrder.class.php');
$logisticSalesOrder = createObjAndAddToCol( new LogisticSalesOrder()); 

$obj = $logisticSalesOrder;

$generateReportContent = function ($dataset){
// print berdasarkan jumlah koli
	
$obj = new LogisticSalesOrder(); 
$setting = new Setting();
$city = new City();
	
$companyName = strtoupper($setting->loadSetting('companyName'));
$profileImg = $obj->loadSetting('companyLogo'); 
$img = $obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg;	
	
$rs = $dataset['rs'];
$qrResult = $obj->createQR($rs[0]['code'],3);
$totalBale = $rs[0]['totalqty']; 
$code = $rs[0]['code']; 	
	
$rsTransportationType = $obj->getTransportationType(); 
$rsTransportationType = array_column($rsTransportationType,null,'pkey');
$transportationType = $rsTransportationType[$rs[0]['transportationkey']]['name'];	

if(!empty($rs[0]['recipientcitykey'])){ 
	$rsCityRecipient = $city->searchData($city->tableName.'.pkey',$rs[0]['recipientcitykey'],true);
	$recipientCity= $rsCityRecipient[0]['name']; 
}

$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$arrGoodsDescription = array();
for ($i=0; $i<count($rsDetail); $i++) 
	if (!empty($rsDetail[$i]['description']))
		array_push($arrGoodsDescription, $rsDetail[$i]['description']);
	
$html = '<style>
			.box{border:1px solid #333; width: 330px;}
			.code{font-size:1.5em; font-weight:bold} 
		</style>';	
$html .= '<table  cellpadding="4" style="width:680px">';	
	
for($i=0;$i<$totalBale;$i++){
	
	if($i==0) $html .= '<tr>';
	if($i>0 && ($i % 2) == 0) $html .= '<tr>';
		
	$html .= '
	<td>
		<table cellpadding="4" class="box" border="1">
		<tr>
		<td>
			<table cellpadding="4">
				<tr>
					<td rowspan="2" style="width:90px"><img src="'.$img.'" style="width:80px"></td>
					<td style="width:170px;" class="code">'.$code.'</td>
					<td style="width:60px; text-align:right; font-size:1.3em">'.$transportationType.'</td> 
				</tr>
				<tr>
					<td colspan="2" style="font-size:2em"> '.($i+1).' / '.$totalBale.' koli</td> 
				</tr>
			</table>
		</td>
		</tr>
		<tr><td>
			<table cellpadding="4">
				<tr>
				<td style="width:220px">
					<b>PENERIMA</b><br>
					<b>'.$rs[0]['recipientname'].'</b><br>
					'.str_replace(chr(13),'<br>',$rs[0]['recipientaddress']).'<br>
					'.$recipientCity.'<br>
					'.$rs[0]['recipientphone'].'<br><br>
					'.implode(', ',$arrGoodsDescription).' 
				</td>
				<td style="text-align:center;width:100px">
				<img src="'.$qrResult['url'].'" /><br>
				'.$rs[0]['verificationcode'].' </td>
				</tr>
			</table>
		</td> 
		</tr>
		</table>
	</td>	
	';
	
	if( ($i+1) %2==0) $html .= '</tr>';
}
	
if( $i%2 != 0) $html .= '<td></td></tr>';
	
$html .= '</table>';

return  $html;
}
?>