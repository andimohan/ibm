<?php 

includeClass('MedicalRequestClaim.class.php');
$medicalRequestClaim = createObjAndAddToCol( new MedicalRequestClaim()); 

$obj = $medicalRequestClaim;
$generateReportContent = function ($dataset){ 

$obj = new MedicalRequestClaim(); 
$supplier = new Supplier();
$customer = new Customer();
$termOfPayment = new TermOfPayment();
$city = new City();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);   
$rsDiagnoseDetail = $obj->getDetailDiagnose($rs[0]['pkey']);
$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);

$rsCustomer = $customer->getDataRowById($rsDetail[0]['customerkey']);
if (!empty($rsCustomer[0]['citykey'])){
    $rsCity = $city->searchData('city.pkey',$rsCustomer[0]['citykey'],true);
    $insuredCityName = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname']; 
}

$arrInitialDiagnose = array();
for ($i=0; $i<count($rsDiagnoseDetail); $i++) {
    array_push($arrInitialDiagnose, $rsDiagnoseDetail[$i]['codenameinitialdiagnose']);
}


$arrRecipient = array();
array_push($arrRecipient, $rsSupplier[0]['name']);
if (!empty($rsSupplier[0]['address1'])) array_push($arrRecipient, $rsSupplier[0]['address1']);
if (!empty($rsSupplier[0]['address2'])) array_push($arrRecipient, $rsSupplier[0]['address2']); 
    
if (!empty($rsSupplier[0]['citykey'])){
    $rsCity = $city->searchData('city.pkey',$rsSupplier[0]['citykey'],true);
    $cityname = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname'];
    
    array_push($arrRecipient, $cityname); 
}
if (!empty($rsSupplier[0]['zipcode'])) array_push($arrRecipient, $rsSupplier[0]['zipcode']); 
if (!empty($rsSupplier[0]['phone'])) array_push($arrRecipient, $rsSupplier[0]['phone']); 
if (!empty($rsSupplier[0]['mobile'])) array_push($arrRecipient, $rsSupplier[0]['mobile']); 
     
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
    <tr><td><br><div class="title">PERMINTAAN BARU</div></td></tr>
    <tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table>
    <tr>
        <td style="width:300px;" >
            <table cellpadding="2">
                <tr>
                    <td class="header-row-header">'.$obj->lang['date'].'</td>
                    <td style="width:10px; text-align:center">:</td>
                    <td style="width:170px">'. $obj->formatDBDate($rs[0]['trdate']) .'</td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['policyNumber'].'</td>
                    <td style="text-align:center">:</td>
                    <td>'.$rs[0]['policynumber'].'</td>
                </tr> 
                <tr>
                    <td class="header-row-header">'.$obj->lang['category'].'</td>
                    <td style="text-align:center">:</td>
                    <td >'.$rs[0]['categoryname'].'</td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['company'].'</td>
                    <td style="text-align:center">:</td>
                    <td >'.$rs[0]['customername'].'</td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['insuranceCompany'].'</td>
                    <td style="text-align:center">:</td>
                    <td>'.$rs[0]['insurancecompanyname'].'</td>
                </tr>
            </table>
        </td>
        <td style="width:10px;"></td>
        <td style="width:360px;"> 
            <table cellpadding="2">
                <tr>
                    <td class="header-row-header">'.$obj->lang['insuredName'].'</td>
                    <td style="text-align:center; width:10px;">:</td>
                    <td style="width:180px;">'.$rs[0]['insuredname'].'</td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['IDNumber'].'</td>
                    <td style="text-align:center">:</td>
                    <td>'.$rs[0]['insuredid'].'</td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['country'].'</td>
                    <td style="text-align:center">:</td>
                    <td>'.$rs[0]['countryname'].'</td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['email'].'</td>
                    <td style="text-align:center">:</td>
                    <td>'.$rs[0]['insuredemail'].'</td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['mobilePhone'].'</td>
                    <td style="text-align:center">:</td>
                    <td>'.$rs[0]['insuredmobile'].'</td>
                </tr>
                <tr>
                    <td class="header-row-header"></td>
                    <td style="text-align:center"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="header-row-header">'.$obj->lang['diagnose'].'</td>
                    <td style="text-align:center"></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3">'.implode('<br>',$arrInitialDiagnose).'</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div style="clear:both"></div>
<div style="clear:both"></div>
';

$html .= ' 
    <table  cellpadding="4" class="table-transaction">
        <tr class="col-header" >
            <td style="width:280px;">Layanan</td>
            <td style="width:90px;text-align:right" >Jumlah</td>
            <td style="width:150px;text-align:right" >Harga @</td>
            <td style="text-align:right; width:160px;">Subtotal</td>
        </tr>';

for ($i=0;$i<count($rsDetail);$i++){  
    
    $html .= '
    <tr>
        <td>'.$rsDetail[$i]['itemname'].'</td>
        <td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td>
        <td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td>
        <td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td>
    </tr>' ; 
}
$html .= '</table>' ;

$html .= '<div style="clear:both"></div>';
 
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
    
$arrSubtotal = array(); 

if ( !empty($arrSubtotal)){
    array_push($arrSubtotal, '<tr><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;"  >'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>');
}    
 
$subtotalLabel = (!empty($arrSubtotal)) ? ucwords($obj->lang['subtotal']) : ucwords($obj->lang['total']) ; 

    
// $topSaid = ($rsTOP[0]['duedays'] > 0 ) ? $rsTOP[0]['duedays'] . ' ' . $obj->lang['day'] : $obj->lang['cash'];
     
$html .= '<table cellpadding="4"> 
<tr>
<td rowspan="'.(count($arrSubtotal) + 1).'" style="width:470px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.<br><br><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td>
<td style="text-align:right; font-weight:bold;  width:100px;">'.$subtotalLabel.'</td>
<td style="text-align:right; font-weight:bold;  width:110px;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td>
</tr>
';  
    
$html .= '
</table>
<div style="clear:both"></div>';

$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>