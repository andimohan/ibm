<?php 

$pdf->setCustomSettings(
    array( 
         'paperSetting' => 'A5,L',
         'showPrintHeader' => false,
         'showPrintFooter' => false,
         'footer' => '',  
         ) 
);

$generateReportContent = function ($dataset){ 
global $pdf;
    
$obj = new EMKLOrderInvoice(); 
$item = new Item();
//$service = new Service(SERVICE);
$emklJobOrder = new EMKLJobOrder(EMKL['jobType']['export']);
$emklPurchaseOrder = new EMKLPurchaseOrder(EMKL['jobType']['export']);
$container = new Container();
$employee = new Employee();
$customer = new Customer(); 
$currency = new Currency();
$setting = new Setting();
$emklInvoiceOrderDetail = array(); 
$arrCurrency = $currency->searchData();
$arrCurrency = array_column($arrCurrency,'name','pkey'); 
$rsContainer = $container->searchData();
$rsContainer = array_column($rsContainer,'name','pkey');

$termOfPayment = new TermOfPayment();
      
$rs = $dataset['rs']; 
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
 
$rsCurrency = $currency->getDataRowById($rs[0]['currencykey']);

if(!empty($rsDetail[0]['refsalesorderheaderkey']))   { 
    
    
	$rsJobOrder = $emklJobOrder->searchDataRow(array($emklJobOrder->tableName.'.pkey',$emklJobOrder->tableName.'.itemdescription',$emklJobOrder->tableName.'.jobtypekey',$emklJobOrder->tableName.'.weight',$emklJobOrder->tableName.'.volume',$emklJobOrder->tableName.'.loadcontainertypekey',$emklJobOrder->tableName.'.aju',$emklJobOrder->tableName.'.peb',$emklJobOrder->tableName.'.mblnumber',$emklJobOrder->tableName.'.ponumber'),
									   ' and '.$emklJobOrder->tableName.'.pkey = '.$obj->oDbCon->paramString($rsDetail[0]['refsalesorderheaderkey'])
										); 

	$labelTypePIB = ($rsJobOrder[0]['jobtypekey'] == 1) ? 'PIB No. AJU' : 'PEB No. AJU';
	$isLCL = (in_array($rsJobOrder[0]['loadcontainertypekey'], array(EMKL['container']['lcl'],EMKL['container']['lclnc']))) ? true : false;

	// kalo jenis nya bkn LCL
	if(!$isLCL){
		$rsItemDetail = $emklJobOrder->getDetailVolume($rsDetail[0]['refsalesorderheaderkey']); 
		  $arrParty = array();
			foreach($rsItemDetail as $row => $val)
				array_push($arrParty,$obj->formatNumber($val['qty'],0).' x '.$rsContainer[$val['itemkey']]);


		$party = implode(', ',$arrParty);
		$partyLabel = 'Partai';
	}else{
		$party = $emklJobOrder->formatNumber($rsJobOrder[0]['weight']). ' KG, '.$emklJobOrder->formatNumber($rsJobOrder[0]['volume']).' CBM';
		$partyLabel = 'Volume';

	}

}
        
    

$rsJobOrderDetail = $emklJobOrder->getDetailByColumn($emklJobOrder->tableNameDetail.'.pkey',$rsDetail[0]['salesorderkey']); 
$pkey = $rsDetail[0]['pkey'];
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);

$arrCustomer = array();
    
if (!empty($rsCustomer[0]['name'])) array_push($arrCustomer, $rsCustomer[0]['name']); 
if (!empty($rsCustomer[0]['address'])) array_push($arrCustomer, str_replace(chr(13),'<br>',$rsCustomer[0]['address'])); 
    
$companyPhone = $setting->getDetailByCode('companyPhone');
$companyAddress = $setting->loadSetting('companyAddress');
$arrCompanyPhone = array();  
for($i=0;$i<count($companyPhone);$i++) 
    array_push($arrCompanyPhone, 'Telp :'.$companyPhone[$i]['value']);

$companyContact = '';
if(!empty($arrCompanyPhone))
    $companyContact = implode (', ', $arrCompanyPhone);
    
$companyName = strtoupper($setting->loadSetting('companyName'));    
    
    $approvedName = '';
if(!empty($rs[0]['approvedbykey'])){
    $rsApproved = $employee->getDataRowById($rs[0]['approvedbykey']);
    $approvedName = $rsApproved[0]['name'];
}
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);

$borderTop = 'border-top:1px solid black;';
$borderLeft = 'border-left:1px solid black;';
$borderRight = 'border-right:1px solid black;';
$borderBottom = 'border-bottom:1px solid black;';    
    $sayNumber = $obj->sayNumber($rs[0]['grandtotal']);

$rsTOP =   $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
$name='';
if(!empty($rsCustomer[0]['alias']))
    $name = $rsCustomer[0]['alias'];
else
    $name = $rsCustomer[0]['name'];
    
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
$profileImg = $obj->loadSetting('companyLogo'); 
$img =  ''; //HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=220&h=110&hash='.getPHPThumbHash($profileImg);

    $html = $obj->printSetting['defaultStyle'];

$html .= ' 

<table>
    <tr>
        <td>
        <table cellpadding="3"> 
            <tr>
                <td style="vertical-align:middle;" ><img src="'.$img.'"></td>
            </tr>
        </table>
        </td>

    </tr> 
</table>
<div style="clear:both"></div>
<div style="clear:both"></div>



';

$html .= '
<table>
    <tr><td class="title"><u><i>Kwitansi</i></u></td></tr>
</table>
<div style="clear:both"></div>

<br>
<table cellpadding="4" style="width:640px;">
    <tr><td style="width:150px"><i>No</i></td><td style="width:20px">:</td><td style="width: 480px">'.$rs[0]['code'].'</td></tr>
    <tr><td ><i>Sudah terima dari</i></td><td>:</td><td>'.$rsCustomer[0]['name'].'</td></tr>
    <tr><td ><i>Banyaknya uang</i></td><td>:</td><td>'.ucwords($sayNumber).' Rupiah</td></tr>
    <tr><td ><i>Untuk pembayaran</i></td><td>:</td><td>
            <table>';
    
for($i=0;$i<count($rsDetail);$i++){
     
    $rsInvoiceDetail = $obj->getItemDetail($rsDetail[$i]['pkey']);
    
    if(!empty($rsDetail[$i]['invoicekey']))
        $rsInvoice = $obj->getDataRowById($rsDetail[$i]['invoicekey']);
    
    
    if(empty($rsInvoiceDetail)) continue;
    
    for($j=0; $j<count($rsInvoiceDetail);$j++){
        
        $rate = ($rs[0]['currencykey'] == CURRENCY['idr'] ) ? 1 : $rs[0]['rate'] ;
        $itemname = (!empty($rsInvoiceDetail[$j]['aliasname'])) ? $rsInvoiceDetail[$j]['aliasname'] : $rsInvoiceDetail[$j]['itemname'];

        $html .=' 
            <tr>
                <td style="width:300px;">'.$itemname.'</td>
                <td style="width:40px"> Rp. </td>
                <td style="width:110px; text-align:right"> '.$obj->formatNumber($rsInvoiceDetail[$j]['total']).'</td>
            </tr>
            '; 
        
    }
}       $taxValueTable = '';
        if ($rs[0]['taxvalue'] != 0){
            $taxValueTable = '
             <tr>
                <td>PPN '.$obj->formatNumber($rs[0]['taxpercentage'],2).' %</td> 
                <td> Rp. </td>
                <td style="text-align:right"> '.$obj->formatNumber($rs[0]['taxvalue']).'</td>
            </tr>
            ';       
        }
        $html .= $taxValueTable;
        $html .= '</table>';
        $html .= '<table>
                <tr>
                <td style="width:300px;">Total</td> 
                <td style="width:40px;"> Rp. </td>
                <td style="text-align:right;width:110px;  font-weight:bold; '.$borderTop.$borderBottom.'">'.$obj->formatNumber($rs[0]['grandtotal']).'</td>
                </tr>
            </table>     
        ';

        
        $html .='</td>
    </tr>
</table>
<div style="clear:both"></div>';
    
    
//table ini yang di stay ke bawah
$html .= '
<table>
<tr>
<td style="width:300px">
<table cellpadding="">
<tr><td style="width:100px;">Jenis Barang</td><td style="width:20px;">:</td><td style="width:300px">'.$rsJobOrder[0]['itemdescription'].'</td></tr>
<tr><td style="width:100px;">'.$partyLabel.'</td><td style="width:20px;">:</td><td>'.$party.'</td></tr>
<tr><td style="width:100px;">No. BL</td><td style="width:20px;">:</td><td>'.$rsJobOrder[0]['mblnumber'].'</td></tr>
<tr><td style="width:100px;">'.$labelTypePIB.'</td><td style="width:20px;">:</td><td>'.$rsJobOrder[0]['aju'].'</td></tr>
<tr><td style="width:100px;">No. PO</td><td style="width:20px;">:</td><td>'.$rsJobOrder[0]['ponumber'].'</td></tr>
</table>
<table cellpadding="">
<tr><td></td></tr>
</table>
<table cellpadding="4" style="'.$borderTop.$borderBottom.'width:120px;">
<tr><td style="width:30px">Rp. </td><td style="text-align:right;width:100px">'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>
</table>
</td> 
<td style="width:400px">
<table> 
<tr>
<td></td>
</tr>
</table>
<table> 
<tr>
<td></td>
<td style="text-align:center;"><i>Jakarta, '.$obj->formatDBDate($rs[0]['trdate'],'d F Y').'</i></td></tr>
<tr><td></td><td style="height:60px;text-align:center;"><img src="'.$imgSignature.'" /></td></tr>
<tr>
<td></td>
<td style="text-align:center;"><b>'.$approvedName.'</b></td>
</tr>
</table>
</td>
</tr>
</table>
';
    
    
//$html .= $obj->generateSignLabel($rs); 
return '<div style="font-size:13px">'.$html.'</div>';
}

?>