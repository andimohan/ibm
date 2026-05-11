<?php  

$PRINT_SETTINGS=array(
    'showPrintHeader' => false,
    'paperSetting' => 'F4', 
    'showPrintFooter' => false,
	'marginFooter' => 0,
	'pdfMarginHeader' => 5,
);

includeClass('LogisticSalesOrder.class.php');
$logisticSalesOrder = createObjAndAddToCol( new LogisticSalesOrder()); 


$obj = $logisticSalesOrder;

function generateTableTransaction($dataset,$copy,$color){
$obj = new LogisticSalesOrder(); 
    global $pdf;

$termOfPayment = new TermOfPayment();
$customer = new Customer();
$setting = new Setting();
$city = new City();
$employee = new Employee();

$rs = $dataset['rs'];
$rsTransportationType = $obj->getTransportationType(); 
$rsTransportationType = array_column($rsTransportationType,null,'pkey');
$transportationType = $rsTransportationType[$rs[0]['transportationkey']]['name'];	
	
//Sender
$rsSender = $customer->getDataRowById($rs[0]['senderkey']);
$senderName = $rsSender[0]['name'] ;
$senderPhone = $rsSender[0]['phone'];

if(!empty($rs[0]['sendercitykey'])){ 
	$rsCitySender = $city->searchData($city->tableName.'.pkey',$rs[0]['sendercitykey'],true);
	$senderCity = $rsCitySender[0]['name']; 
}	    


$oriLabel = ($copy) ? 'ORIGINAL' : 'COPY';
$tripleyLabel = (isset($_GET['triplay']) && $_GET['triplay'] == 1) ? 'COPY' : $oriLabel;
$colorLabel = ($color) ? 'color:blue' : '';
	
//Recipient
$rsRecipient = $customer->getDataRowById($rs[0]['recipientkey']);
$recipientName= $rsRecipient[0]['name'] ;
$recipientPhone = $rsRecipient[0]['phone'];
if(!empty($rs[0]['recipientcitykey'])){ 
	$rsCityRecipient = $city->searchData($city->tableName.'.pkey',$rs[0]['recipientcitykey'],true);
	$recipientCity= $rsCityRecipient[0]['name']; 
}

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
$img =  $obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg;

$html = $obj->printSetting['defaultStyle'];
$createByName = $employee->searchDataRow( array($employee->tableName.'.name'),' and '.$employee->tableName.'.pkey= '. $obj->oDbCon->paramString($rs[0]['createdby']) );
$arrDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
    
$arrWeights= array();
$arrDescriptions= array();
$arrVol = array();
foreach ($arrDetail as $details) {
    array_push($arrWeights,$obj->formatNumber($details['finalweight']));
    array_push($arrDescriptions,$details['description']);
    
    if($details['length'] != 0 && $details['width'] != 0 && $details['height'] != 0)
    array_push($arrVol,$obj->formatNumber($details['length']).' x '.$obj->formatNumber($details['width']).' x '.$obj->formatNumber($details['height']));
}

    $volume = (!empty($arrVol)) ? 'Vol : '.implode(', ', $arrVol) : '';

$html .= ' 
<style>  
    .text-center { text-align: center; }
    .text-right {   text-align: right;  }
    .head{   width: 50px;  }
    .colon {   width: 10px;  }
    .currency {  width: 20px;  } 
    .bold{  font-weight:bold;  } 
    .row-footer{  height: 40px;  }
    .head-right{  width: 75px; }
</style>
<table cellpadding="2" >
    <tr>
        <td style="width:100px;vertical-align:middle;"><img src="'.$img.'" alt="Logo"></td>
        <td style="width:350px;"><b style="font-size:16px;">'.$companyName.'</b><br>'.str_replace(chr(13),'<br>',$companyAddress).'</td>
        <td style="width:230px;text-align:center;">
            <table>
                <tr><td class="title" ><b> COURIER & CARGO</b></td></tr>
                <tr><td><b> SURAT TANDA TITIPAN</b></td></tr>
            </table>
			<br><br> 
			<table cellpadding="2" style="width:220px; font-weight:bold; text-align:center;" >
                <tr><td colspan="3" class="title" style="border:1px solid #333;'.$colorLabel.'" ><b>'.$rs[0]['code'].'</b></td></tr>
                <tr><td style="font-size:1.2em; border:1px solid #333; width: 80px"><div class="ori-label" style="'.$colorLabel.'">'.$tripleyLabel.'</div></td><td class="ori-label" style="width:50px; border:1px solid #333; font-size:1.2em;">'.$transportationType.'</td><td style="width:90px; border:1px solid #333;  font-size:1.1em;">'.$obj->formatDBDate($rs[0]['trdate'], 'd / m / Y').'</td></tr> 
            </table> 
        </td>
    </tr>
</table> 
';
 
$arrSubtotal = array(); 
    
if ($rs[0]['finaldiscount'] != 0){
    if ($rs[0]['finaldiscounttype'] == 2)
        $rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
 
    $rs[0]['finaldiscount'] *= -1;
   array_push($arrSubtotal, '<tr><td class="head-right">'.ucwords($obj->lang['discount']).'</td><td class="colon">:</td><td class="currency">Rp.</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($rs[0]['finaldiscount']).'</td></tr>');
}    

if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td style="width:20px"></td><td class="head-right">DPP</td><td class="colon">:</td><td class="currency">Rp.</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style="width:20px"></td><td class="head-right">Pajak</td><td class="colon">:</td><td class="currency">Rp.</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>');

}   

if ($rs[0]['taxvalue'] != 0){
    array_push($arrSubtotal, '<tr><td style="width:20px"></td><td class="head-right">DPP</td><td class="colon">:</td><td class="currency">Rp.</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td></tr>');
    array_push($arrSubtotal, '<tr><td style="width:20px"></td><td class="head-right">Pajak</td><td class="colon">:</td><td class="currency">Rp.</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['taxvalue']).'</td></tr>');

}   

if ($rs[0]['packingfee'] != 0){
    array_push($arrSubtotal, '<tr><td style="width:20px"></td><td class="head-right">'.ucwords($obj->lang['packingFee']).'</td><td class="colon">:</td><td class="currency">Rp.</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['packingfee']).'</td></tr>');

}   

if ($rs[0]['etccost'] != 0){
    array_push($arrSubtotal, '<tr><td style="width:20px"></td><td class="head-right">'.ucwords($obj->lang['others']).'</td><td class="colon">:</td><td class="currency">Rp.</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['etccost']).'</td></tr>');

}    
$subtotalTable = '';    
    
if( (isset($_GET['original']) && $_GET['original'] == 1) || $copy){

    
$subtotalTable = ' <table>
                <tr><td></td></tr>
                <tr>
                    <td style="width:20px"></td>
                    <td class="head-right">'.$obj->lang['price'].'</td>
                    <td class="colon">:</td>
                    <td class="currency"><b>Rp</b>.</td>
                    <td class="text-right bold" style="width:80px">'.$obj->formatNumber($rs[0]['subtotal']).'</td>
                </tr>
            ';
    
    $subtotalTable .= implode('',$arrSubtotal); 
 
    $subtotalTable .= '
                <tr>
                    <td style="width:20px"></td>
                    <td class="head-right">'.$obj->lang['total'].'</td>
                    <td class="colon">:</td>
                    <td class="currency"><b>Rp</b>.</td>
                    <td class="text-right bold">'.$obj->formatNumber($rs[0]['grandtotal']).'</td>
                </tr>
    </table>';
}
    
    
$html.= '<table >
    <tr>
        <td style="width:230px;">
            <table >
                <tr>
                    <td colspan="2" ><b>'.strtoupper($obj->lang['recipient']).'</b></td>
                </tr>
                <tr>
                    <td style="width:230px;'.$colorLabel.';height:142px">
							<b>'.$recipientName.'</b><br>
							'.$recipientPhone.'<br>
							'.str_replace(chr(13),'<br>',$rs[0]['recipientaddress']).'
							<br>'.strtoupper($recipientCity).'
					</td>
                </tr>
				<tr>
				<td >
				 <table>
						<tr >
							<td align="center" style="height: 15px;width: 50px;" >'.$obj->lang['bale'].'</td>
							<td align="center" style="height: 15px;">'.$obj->lang['weight'].' (Kg)</td>
						</tr>
						<tr>
							<td align="center" ><b style="font-size: 1.5em;'.$colorLabel.'">'.$rs[0]['totalqty'].'</b></td>
							<td align="center">
								<b style="font-size: 1.5em;'.$colorLabel.'">'.$obj->formatNumber($rs[0]['totalweight']).'</b><br><br>
								<table cellpadding="2" style="font-size:0.9em">'; 

									for ($i=0; $i<10; $i++){ 
										$weight = (isset($arrDetail[$i])) ? $obj->formatNumber($arrDetail[$i]['finalweight']) : '';
										$boxStyle =(isset($arrDetail[$i]) && !empty($arrDetail[$i]['finalweight'])) ? ' border:1px solid #666; ' : '';

										if ($i==0 || $i%5==0) $html .= '<tr>';
										$html .= '<td style="text-align:center; height: 15px; width:25px;'.$boxStyle.'" ><b>'.$weight.'</b></td>'; 
										if ( ($i+1) %5==0) $html .= '</tr>';
									}

									$html .= '  
								</table>
							</td>
						</tr>
            		</table>
					</td>
				</tr>
				<tr>
				<td style="text-align:left;">
				</td>
				</tr>
				<tr>
				<td style="text-align:left;">
					<br>'.$rs[0]['code'].'
				</td>
				</tr>
			
            </table>
        </td>
        <td style="width:230px;">
            <table>
                <tr>
                    <td colspan="2"><b>'.strtoupper($obj->lang['sender']).'</b></td>
                </tr>
                <tr>
                    <td style="width:220px;'.$colorLabel.';height:130px">
						<b>'.$senderName.'</b><br>
						'.$senderPhone.'<br>
						'.str_replace(chr(13),'<br>',$rs[0]['senderaddress']).'
						<br>'.strtoupper($senderCity).'
					</td>
                </tr>
				<tr>
				<td>
					<table>
						<tr><td style="width:230px;" class="text-left"><b>ISI BARANG TIDAK DIPERIKSA</b></td></tr>'; 
		//ini saya rubah karena kalau 10 barang bakal turun saya fix kan pakai height dan nama barang saya implode dan pakai koma
	/*                for ($i=0; $i<count($arrDetail); $i++) 
						if (!empty($arrDetail[$i]['description']))
												$html .='<tr><td align="left" style="'.$colorLabel.'">'.str_replace(chr(13),', ',$arrDetail[$i]['description']).'</td></tr>';   */ 
							$html .='<tr><td style="'.$colorLabel.';height:60px;">'.implode(', ', $arrDescriptions).'</td></tr>';

            		$html .='</table>
					</td>
				</tr>
                <tr><td>'.$volume.'</td></tr>
            </table>
        </td>
        <td style="width:250px;">
			<table >
				<tr>
					<td style="height:80px">'.$subtotalTable.'</td>
                    
			   </tr>
               <tr> <td style="height:55px"  align="center" ><i style="font-size: 1em;">'.$rs[0]['trdesc'].'</i></td></tr>
			   <tr>
			   		<td style="height:90px;">
						<table cellpadding="8" style="border:1px solid #333">
							<tr><td style="width:5px;"></td><td  style="width:65px;border-bottom:1px solid #333">Pengirim</td><td  style="width:20px;border-bottom:1px solid #333">:</td><td  style="width:100px;border-bottom:1px solid #333"><br><br><br>( ..................... )</td><td style="width:5px;"></td></tr>
							<tr><td style=""></td><td  style="border-bottom:1px solid #333">Penerima</td><td  style="border-bottom:1px solid #333">:</td><td  style="border-bottom:1px solid #333"><br><br><br>( ..................... )</td><td ></td></tr> 
						</table>
					</td>
		   	   </tr>
		   </table>
        </td>
    </tr>
 
</table>';

//<tr><td style=""></td><td  style="">Petugas</td><td  >:</td><td  >'.$createByName[0]['name'].'</td><td   >( ......... )</td><td style=""></td></tr>
    
    return $html;
}

$generateReportContent = function ($dataset){
 
$obj = new LogisticSalesOrder(); 

if(isset($_GET['original']) && $_GET['original'] == 1){
    $templateHTML = generateTableTransaction($dataset,true,false);
    $templateHTML .= 	'<div style=" border-top:1px solid #333"></div>';
    
}else if(isset($_GET['triplay']) && $_GET['triplay'] == 1){
    $templateHTML  = generateTableTransaction($dataset,true,false);
    $templateHTML .= 	'<div style=" border-top:1px solid #333"></div>';
    $templateHTML .= generateTableTransaction($dataset,false,false);
    $templateHTML .= 	'<div style=" border-top:1px solid #333"></div>';
    $templateHTML .= generateTableTransaction($dataset,false,true);
}else if(isset($_GET['copy']) && $_GET['copy'] == 1){
//    $templateHTML  = generateTableTransaction($dataset,true,false);
//    $templateHTML .= 	'<div style="clear:both"></div><div style=" border-top:1px solid #333"></div>';
    $templateHTML .= generateTableTransaction($dataset,false,false);
    $templateHTML .= 	'<div style=" border-top:1px solid #333"></div>';
    
}
    
$html = $templateHTML;

//$html  = generateTableTransaction($dataset,true,false);

$return = $html;
//$return .= 	'<div style="clear:both"></div><div style="clear::both; border-top:1px solid #333"></div><div style="clear::both"></div>';
//$return .= str_replace('<div class="ori-label">ORIGINAL</div>', '<div class="ori-label"><b>COPY</b></div>',$html);
//	
return  $return;
}
?>