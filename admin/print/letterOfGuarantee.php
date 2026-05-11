<?php 

includeClass('MedicalPurchaseOrder.class.php');
$medicalPurchaseOrder = createObjAndAddToCol( new MedicalPurchaseOrder()); 

$PRINT_SETTINGS =  array(   
    'showPrintHeader' => false,
    );

$obj = $medicalPurchaseOrder;
$generateReportContent = function ($dataset){ 
global $pdf;
$obj = new MedicalPurchaseOrder();  
$supplier = new Supplier();
$customer = new Customer();
$medicalJobOrder = new MedicalJobOrder();
$termOfPayment = new TermOfPayment();
$city = new City();
$setting = new Setting();
$companyName = $setting->loadSetting('companyName');
$companyAddress = $setting->loadSetting('companyAddress');
$companyOthersInformation= $setting->loadSetting('othersInformation');
$companyEmail= $setting->getDetailByCode('companyEmail');
$companyPhone= $setting->getDetailByCode('companyPhone');
$companyMessenger= $setting->getDetailByCode('companyMessenger');
$arrCompanyMessenger = array();  
for($i=0;$i<count($companyMessenger);$i++) 
    array_push($arrCompanyMessenger, $companyMessenger[$i]['value']);

$companyFax = '';
if(!empty($arrCompanyMessenger))
    $companyFax = implode (', ', $arrCompanyMessenger);


$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);   
$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);

$rsMedicalJobOrder = $medicalJobOrder->searchData($medicalJobOrder->tableName.'.pkey', $rs[0]['refkey']);   

$rsDiagnoseDetail = $medicalJobOrder->getDetailDiagnose($rs[0]['refkey']);

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
<tr><td><div class="title">INITIAL LETTER OF GUARANTEE</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].' / '.$rsMedicalJobOrder[0]['codelog'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table>
    <tr>
        <td style="width:350px;" >
            <table cellpadding="2">
                <tr>
                    <td class="header-row-header" style="width:120px">Tanggal</td>
                    <td style="width:10px; text-align:center">:</td>
                    <td style="width:220px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td>
                </tr>
                <tr>
                    <td class="header-row-header" style="width:120px">Provider</td>
                    <td style="width:10px; text-align:center">:</td>
                    <td style="width:220px">BUNDA JAKARTA, RSU</td>
                </tr>
                <tr>
                    <td class="header-row-header">Fax/Email</td>
                    <td style="text-align:center">:</td>
                    <td >/</td>
                </tr>
                <tr>
                    <td><strong>TO</strong></td>
                    <td style="text-align:center">:</td>
                    <td>'.implode('<br>',$arrRecipient).'</td>
                </tr>
                <tr>
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
<tr>
    <td style="width:430px;"> 
        <table cellpadding="2">
            <tr>
                <td colspan="3">Dear Sir/Madam,</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="3">Please accept our <b>Letter of Guarantee</b> for the following patient :</td>
            </tr>
            <tr>
                <td class="header-row-header" style="width:120px">Nama Tertanggung</td>
                <td style="width:10px; text-align:center">:</td>
                <td style="width:300px">'. $rsMedicalJobOrder[0]['insuredname'] .'</td>
            </tr>
            <tr>
                <td class="header-row-header">No Polis</td>
                <td style="text-align:center">:</td>
                <td>'. $rsMedicalJobOrder[0]['policynumber'] .'</td>
            </tr>
            <tr>
                <td class="header-row-header">No. Identitas</td>
                <td style="text-align:center">:</td>
                <td>'.$rsMedicalJobOrder[0]['insuredid'].'</td>
            </tr>
            <tr>
                <td class="header-row-header">Tgl. Lahir</td>
                <td style="text-align:center">:</td>
                <td>'.$obj->formatDBDate($rsMedicalJobOrder[0]['dateofbirth'],'d / m / Y').'</td>
            </tr>
            <tr>
                <td class="header-row-header">Email</td>
                <td style="text-align:center">:</td>
                <td>'.$rsMedicalJobOrder[0]['insuredemail'].'</td>
            </tr>
            <tr>
                <td class="header-row-header">Diagnosis</td>
                <td style="text-align:center">:</td>
                <td ></td>
            </tr>
            <tr>
                <td colspan="3">'.implode('<br>',$arrInitialDiagnose).'</td>
            </tr>
            
            <div style="clear:both"></div>
            <tr>
                <td style="text-align:center; border:1px solid #333; height:30px;" colspan="2"><strong>Excess Fee [IDR]</strong></td>
            </tr>
            <tr>
                <td style="text-align:right; border:1px solid #333; height:30px;" colspan="2"><strong>'.$obj->formatNumber($rs[0]['excessfee']).'</strong></td>
            </tr>
            <div style="clear:both"></div>
            <tr>
                <td style="text-align:left; border:1px solid #333; height:40px;" colspan="3">Remarks : <br>'.$rs[0]['trdesc'].'</td>
            </tr>
            <div style="clear:both"></div>
            <tr>
                <td colspan="3">Should the bill exceed the given limit, please inform us immediately. This guarantee letter will supersede any
                guarantee letter previously issued for this case.</td>
            </tr>
            <tr>
                <td colspan="3">Please send all your original invoice with the copy of this letter to the following address including your bank account details within 30 days from the date of this letter to:</td>
            </tr>
        </table>
    </td>
</tr>
</table>
<div style="clear:both"></div>';


$html .= '<div style="clear:both"></div>';


$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>