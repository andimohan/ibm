<?php  

$PRINT_SETTINGS=array(
    'showPrintHeader' => false,
    'paperSetting' => 'LEGAL', 
    'showPrintFooter' => false,
	'marginFooter' => 6
);

includeClass('LogisticSalesOrder.class.php');
$logisticSalesOrder = createObjAndAddToCol( new LogisticSalesOrder()); 



$obj = $logisticSalesOrder;

function generateTableTransaction($dataset,$copy,$color){
$obj = new LogisticSalesOrder(); 

$termOfPayment = new TermOfPayment();
$customer = new Customer();
$setting = new Setting();
$city = new City();
$employee = new Employee();

$rs = $dataset['rs'];
$qrResult = $obj->createQR($rs[0]['code'],3);
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
$img = $obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg;

$html = $obj->printSetting['defaultStyle'];
$createByName = $employee->searchDataRow( array($employee->tableName.'.name'),' and '.$employee->tableName.'.pkey= '. $obj->oDbCon->paramString($rs[0]['createdby']) );
$arrDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
    
$arrWeights= array();
foreach ($arrDetail as $details) 
    array_push($arrWeights,$obj->formatNumber($details['finalweight']));

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
                <tr><td style="font-size:1.2em; border:1px solid #333; width: 80px"><div class="ori-label" style="'.$colorLabel.'">'.$oriLabel.'</div></td><td class="ori-label" style="width:50px; border:1px solid #333; font-size:1.2em;">'.$transportationType.'</td><td style="width:90px; border:1px solid #333;  font-size:1.1em;">'.$obj->formatDBDate($rs[0]['trdate'], 'd / m / Y').'</td></tr> 
            </table> 
        </td>
    </tr>
</table> 
';
 
$html.= '<table >
    <tr>
        <td style="width:240px;">
            <table >
                <tr>
                    <td colspan="2"><b>'.strtoupper($obj->lang['recipient']).'</b></td>
                </tr>
                <tr>
                    <td class="head">'.$obj->lang['name'].'</td>
                    <td class="colon">:</td>
                    <td style="'.$colorLabel.'"><b>'.$recipientName.'</b></td>
                </tr>
                <tr>
                    <td class="head">'.$obj->lang['phone'].'</td>
                    <td class="colon">:</td>
                    <td style="'.$colorLabel.'">'.$recipientPhone.'</td>
                </tr>
                <tr>
                    <td class="head">'.$obj->lang['address'].'</td>
                    <td class="colon">:</td>
                    <td style="'.$colorLabel.'">'.str_replace(chr(13),'<br>',$rs[0]['recipientaddress']).'</td>
                </tr>
                <tr>
                    <td class="head">'.$obj->lang['city'].'</td>
                    <td class="colon">:</td>
                    <td style="font-size: 1.3em;'.$colorLabel.'">'.strtoupper($recipientCity).'</td>
                </tr>
            </table>
        </td>
        <td style="width:240px;">
            <table>
                <tr>
                    <td colspan="2"><b>'.strtoupper($obj->lang['sender']).'</b></td>
                </tr>
                <tr>
                    <td class="head">'.$obj->lang['name'].'</td>
                    <td class="colon">:</td>
                    <td style="'.$colorLabel.'"><b>'.$senderName.'</b></td>
                </tr>
                <tr>
                    <td class="head">'.$obj->lang['phone'].'</td>
                    <td class="colon">:</td>
                    <td style="'.$colorLabel.'">'.$senderPhone.'</td>
                </tr>
                <tr>
                    <td class="head">'.$obj->lang['address'].'</td>
                    <td class="colon">:</td>
                    <td style="'.$colorLabel.'">'.str_replace(chr(13),'<br>',$rs[0]['senderaddress']).'</td>
                </tr>
                <tr>
                    <td class="head">'.$obj->lang['city'].'</td>
                    <td class="colon">:</td>
                    <td style="font-size: 1.3em;'.$colorLabel.'">'.strtoupper($senderCity).'</td>
                </tr>
            </table>
        </td>
        <td style="width:200px;">
        </td>
    </tr>
    <tr><td colspan="3"></td></tr>
    <tr>
        <td>
            <table>
                <tr >
                    <td align="center" style="height: 15px;width: 50px;" >'.$obj->lang['bale'].'</td>
                    <td align="center" style="height: 15px;">'.$obj->lang['weight'].' (Kg)</td>
                </tr>
                <tr>
                    <td align="center" ><b style="font-size: 1.3em;'.$colorLabel.'">'.$rs[0]['totalqty'].'</b></td>
                    <td align="center">
						<b style="font-size: 1.3em;'.$colorLabel.'">'.$obj->formatNumber($rs[0]['totalweight']).'</b><br><br>
                        <table cellpadding="2" style="font-size:0.9em">'; 
	
                            for ($i=0; $i<10; $i++){ 
								$weight = (isset($arrDetail[$i])) ? $obj->formatNumber($arrDetail[$i]['finalweight']) : '';
								$boxStyle =(isset($arrDetail[$i]) && !empty($arrDetail[$i]['finalweight'])) ? ' border:1px solid #666; ' : '';
								
                             	if ($i==0 || $i%5==0) $html .= '<tr>';
								$html .= '<td style="text-align:center; height: 15px; width:20px;'.$boxStyle.'" ><b>'.$weight.'</b></td>'; 
                             	if ( ($i+1) %5==0) $html .= '</tr>';
							}
								
                            $html .= '  
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td>
            <table>
                <tr><td class="text-left"><b>ISI BARANG TIDAK DIPERIKSA</b></td></tr>'; 
                for ($i=0; $i<count($arrDetail); $i++) 
					if (!empty($arrDetail[$i]['description']))
                    	$html .='<tr><td align="left" style="'.$colorLabel.'">'.str_replace(chr(13),'<br>',$arrDetail[$i]['description']).'</td></tr>';
          
            $html .='</table>
        </td>
    </tr>
</table>';
 
$html .= '
<table>
    <tr>
        <td>
            <table>
                <tr>
                    <td class="text-center">'.$obj->lang['senderSignature'].'</td>
                </tr>
                <tr>
                    <td class="text-center row-footer"></td>
                </tr>
                <tr>
                    <td class="text-center">( <b>'.((!empty($rs[0]['courier'])) ? $rs[0]['courier'] : '....................').'</b> )</td>
                </tr>
                
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <td class="text-center">'.$obj->lang['recipientSignature'].'</td>
                </tr>
                <tr>
                    <td class="text-center row-footer"> </td>
                </tr>
                <tr>
                    <td class="text-center" >( .................... )</td>
                </tr>
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <td class="text-center"><b>'.$obj->lang['officer'].'</b></td>
                </tr>
                <tr>
                    <td class="text-center row-footer"></td>
                </tr>
                <tr>
                    <td class="text-center" style="'.$colorLabel.'"> ( <b> '.$createByName[0]['name'].'</b> ) </td>
                </tr>

            </table>
        </td>
    </tr>
</table>';

    
    return $html;
}

$generateReportContent = function ($dataset){
 
    
$obj = new LogisticSalesOrder(); 

$html = generateTableTransaction($dataset,true,false);
$html .= 	'<div style="clear:both"></div><div style=" border-top:1px solid #333"></div>';
$html .= generateTableTransaction($dataset,false,false);
$html .= 	'<div style="clear:both"></div><div style=" border-top:1px solid #333"></div>';
$html .= generateTableTransaction($dataset,false,true);
$return = $html;
//$return .= 	'<div style="clear:both"></div><div style="clear::both; border-top:1px solid #333"></div><div style="clear::both"></div>';
//$return .= str_replace('<div class="ori-label">ORIGINAL</div>', '<div class="ori-label"><b>COPY</b></div>',$html);
//	
return  $return;
}
?>