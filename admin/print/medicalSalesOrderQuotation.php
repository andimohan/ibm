<?php 

includeClass('MedicalSalesOrderQuotation.class.php');
$medicalSalesOrderQuotation = createObjAndAddToCol( new MedicalSalesOrderQuotation()); 

$obj = $medicalSalesOrderQuotation;
$generateReportContent = function ($dataset){ 

$obj = new MedicalSalesOrderQuotation();  
$supplier = new Supplier();
$customer = new Customer();
$medicalJobOrder = new MedicalJobOrder();
$medicalRequestClaim = new MedicalRequestClaim();
$termOfPayment = new TermOfPayment();
$city = new City();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
// $obj->setLog($rsDetail, true);

$rsMedicalRequestClaim = $medicalRequestClaim->searchDataRow(array($medicalRequestClaim->tableName . '.supplierkey', $medicalRequestClaim->tableName . '.customerkey', $medicalRequestClaim->tableName . '.codelog'), ' and ' . $medicalRequestClaim->tableName . '.pkey=' . $rs[0]['refrequestkey']);
$rsSupplier = $supplier->getDataRowById($rsMedicalRequestClaim[0]['supplierkey']);
$rsCustomer = $customer->getDataRowById($rsMedicalRequestClaim[0]['customerkey']);


if (!empty($rsCustomer[0]['citykey'])){
    $rsCity = $city->searchData('city.pkey',$rsCustomer[0]['citykey'],true);
    $insuredCityName = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname']; 
}

if ($rsCustomer[0]['isinsured'] == 1) {
    $supplierName = $rsSupplier[0]['name'];
} else {
    $supplierName = $rsCustomer[0]['name'];
}
    
$html = $obj->printSetting['defaultStyle'];

$html .= '
<table cellpadding="2" > 
    <tr><td><div class="title">Penawaran Harga</div></td></tr>
    <tr><td><div class="subtitle">'.$rs[0]['code'].' / '.$rsMedicalRequestClaim[0]['codelog'].'</div></td></tr>
</table> 
<div style="clear:both"></div> 
<table>
<tr>
    <td style="width:300px;" >
        <table cellpadding="2">
            <tr>
                <td class="header-row-header">Date</td>
                <td style="width:10px; text-align:center">:</td>
                <td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td>
            </tr>
            <tr>
                <td class="header-row-header">To</td>
                <td style="text-align:center">:</td>
                <td >'.$supplierName.'</td>
            </tr>
        </table>
    </td>
    <td style="width:500px;"> 
    <table cellpadding="2">
        <tr>
                <td class="header-row-header">Phone</td>
                <td style="width:10px; text-align:center">:</td>
                <td>'.$rsSupplier[0]['phone'].'</td>
            </tr>
            <tr>
                <td class="header-row-header">Email Address</td>
                <td style="width:10px; text-align:center">:</td>
                <td>'.$rsSupplier[0]['email'].'</td>
            </tr>
            <tr>
                <td class="header-row-header">Attn</td>
                <td style="width:10px; text-align:center">:</td>
                <td>'.$rs[0]['attention'].'</td>
            </tr>
            <tr>
                <td class="header-row-header">Address</td>
                <td style="width:10px; text-align:center">:</td>
                <td>'.$rsSupplier[0]['address1'].'</td>
            </tr>
    </table>
</td>
</tr>
</table>';

$html .= '<div style="clear:both"></div><div style="clear:both"></div>';

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

$html .= '<table cellpadding="4"> 
<tr>
<td rowspan="'.(count($arrSubtotal) + 1).'" style="width:470px"><strong>Terbilang</strong> :<br>'.ucwords($sayNumber).' Rupiah.<br><br><strong>Note</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td>
<td style="text-align:right; font-weight:bold;  width:100px;">'.$subtotalLabel.'</td>
<td style="text-align:right; font-weight:bold;  width:110px;">'.$obj->formatNumber($rs[0]['subtotal']).'</td>
</tr>
';


$html .= '<div style="clear:both"></div>';
$html .= $obj->loadSetting('emailInvoiceFooter');
$html .= '<div style="clear:both"></div>';
$html .= '<div style="clear:both"></div>';
$html .= '<div style="clear:both"></div>';

$html .= '<table> 
            <tr>
                <td style="width:200px; font-weight:bold;">
                    Emergency Response Indonesia,
                    
                </td>
                <td style="text-align:right; font-weight:bold;  width:100px;"></td>
            </tr>
            <tr>
                <td style="width:200px; font-weight:bold;">
                    Operation Coordinator
                </td>
                <td style="text-align:right; font-weight:bold;  width:100px;"></td>
            </tr>
            </table>
';  
    
$html .= '
</table>
<div style="clear:both"></div>';



    
return $html;
};
