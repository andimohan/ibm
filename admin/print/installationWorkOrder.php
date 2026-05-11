<?php 

includeClass(array('InstallationWorkOrder.class.php'));
$installationWorkOrder = new InstallationWorkOrder();

$obj = $installationWorkOrder;
 
$generateReportContent = function ($dataset){ 
 
$obj = new InstallationWorkOrder();  
$item = new Item();
$customer = new Customer(); 
$jobDetails = new JobDetails(); 
$employee = new Employee();
$salesOrder = new SalesOrderSubscription();
$media = new Media(); 
$tandaBenar = ''; 
$rs = $dataset['rs']; 
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsDetailTechnician = $obj->getDetailTechnicianWithRelatedInformation($rs[0]['pkey']);

    
$rsSalesOrder = $salesOrder->searchData('','',true,' and '.$salesOrder->tableName.'.pkey = ' .  $salesOrder->oDbCon->paramString($rs[0]['salesorderkey']).'');
    $rsCustomer = $customer->getDataRowById($rsSalesOrder[0]['customerkey']);

    if($rsCustomer[0]['ismainaccount']){

        $customerName ='<strong>'.$rsCustomer[0]['name'].'</strong>';

    
    }else{
        $rsCustomerHO = $customer->searchDataRow(array($customer->tableName.'.name'),
                         ' and '.$customer->tableName.'.pkey = '.$obj->oDbCon->paramString($rsCustomer[0]['parentkey'])
                        );
        $customerName = $rsCustomerHO[0]['name'];

    }    
    
$rsMedia = $media->getDataRowById($rsSalesOrder[0]['mediakey']);
//$rsSales = $employee->getDataRowById($rsSalesOrder[0]['saleskey'] );  
$salesName = (!empty($rsSales)) ? $rsSales[0]['name'] : '';
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';

$rsJobDetails = $jobDetails->searchData('','',true,' and ('.$jobDetails->tableName.'.statuskey = 1)',' order by '.$jobDetails->tableName.'.name asc');    
$detailService = array();
$rsMonthly = $salesOrder->getMonthlyDetailRelatedInformation($rsSalesOrder[0]['pkey']);
if(!empty($rsMonthly)){
    for($i=0;$i<count($rsMonthly);$i++)
        array_push($detailService,$rsMonthly[$i]['itemname']);
}
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">WORK ORDER - EXTERNAL</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 
<div style="clear:both"></div>

<table cellpadding="4" style=""> 
<tr><td style="font-weight:bold; font-size:14px;">DATA PENGERJAAN</td></tr>
<tr>
<td class="" style="width: 120px; border:solid 1px black;">Jenis Pemasangan</td><td style="width: 220px; border:solid 1px black;"> '.$rsMedia[0]['name'].'</td>
<td class="" style="width: 120px; border:solid 1px black;">Tgl. Mulai Kerja</td><td style="width: 220px; border:solid 1px black;"> '.$obj->formatDBDate($rs[0]['starttime'],'d / m / Y H:i').'</td>
</tr>
<tr>
<td class="" style="width: 120px; border:solid 1px black;">Tgl. Work Order</td><td style="width: 220px; border:solid 1px black;"> '.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td>
<td class="" style="width: 120px; border:solid 1px black;">Tgl. Selesai Kerja</td><td style="width: 220px; border:solid 1px black;"> '.$obj->formatDBDate($rs[0]['endtime'],'d / m / Y H:i').'</td>
</tr>  
<tr>
<td class="" style="width: 120px; border:solid 1px black;">Vendor</td><td colspan="3" style="width: 560px; border:solid 1px black;"></td>
</tr>
<tr>
<td></td>
</tr>
</table> 
';
    
$html .= '
<table cellpadding="4" style=""> 
<tr><td style="font-weight:bold; font-size:14px;">DATA PELANGGAN</td></tr>
<tr>
<td class="" style="width: 100px; border:solid 1px black;">Nama Pelanggan</td><td colspan="3" style="width: 580px; border:solid 1px black;"> '.$customerName.'</td>
</tr>
<tr>
<td class="" style="width: 100px; border:solid 1px black;">Alamat Lengkap</td><td colspan="3" rowspan="" style="width: 580px; border:solid 1px black;"> '.str_replace(chr(13),'<br>',$rsSalesOrder[0]['address']).'</td>
</tr>
<tr>
<td class="" style="width: 100px; border:solid 1px black;">No. Telepon</td><td style="width: 580px; border:solid 1px black;"> '.$rsSalesOrder[0]['phone'].'</td>
</tr>
<tr>
<td class="" style="width: 100px; border:solid 1px black;">Layanan</td><td style="width: 580px; border:solid 1px black;"> '.implode(', ',$detailService).'</td>
</tr> 
</table> 
<div style="clear:both"></div>

';

 /*$html .= '
<table cellpadding="4" style=""> 
<tr><td style="font-weight:bold; font-size:14px;">DETAIL PENGERJAAN</td></tr>
<tr>
<td class="" style="width: 20px; border:solid 1px black; text-align:center;">'.$tandaBenar.'</td><td  style="width: 206px;">Instalasi</td>
<td class="" style="width: 20px; border:solid 1px black; text-align:center;">'.$tandaBenar.'</td><td  style="width: 206px;">Upgrade</td>
<td class="" style="width: 20px; border:solid 1px black; text-align:center;">'.$tandaBenar.'</td><td  tyle="width: 206px;">Downgrade</td>
</tr>
<tr>
<td class="" style="width: 20px; border:solid 1px black; text-align:center;">'.$tandaBenar.'</td><td style="width: 206px;">Penarikan Kabel</td>
<td class="" style="width: 20px; border:solid 1px black; text-align:center;">'.$tandaBenar.'</td><td style="width: 206px;">Maintenence</td>
<td class="" style="width: 20px; border:solid 1px black; text-align:center;">'.$tandaBenar.'</td><td style="width: 206px;">Lainnya</td>
</tr>
</table> 
<div style="clear:both"></div>

'; */

$html .= '<table cellpadding="2"><tr><td style="font-weight:bold; font-size:14px;">DETAIL PENGERJAAN</td></tr>';
$html .= '<tr>';

for ($i=0;$i<count($rsJobDetails);$i++){
    if ($i%3==0) $html .= '</tr><tr>';
    $checkV = '';
    if($rsJobDetails[$i]['pkey']==$rsSalesOrder[0]['jobdetailskey'])
        $checkV = 'V';
    
    $html .= '<td style="width:20px; border-bottom:1px solid #333; text-align:center">'.$checkV.'</td><td style="width: 200px;">'.$rsJobDetails[$i]['name'].'</td>';

}

$i=$i%3;  
if ($i != 0)    
for($j=$i;$j<3;$j++)
    $html .= '<td style="width: 20px;"></td><td style="width: 200px;"></td>';
    
    
$html .= '</tr></table>';
    
$html .= ' 
<table cellpadding="4" class="" style="">
<tr><td style="font-weight:bold; font-size:14px;">DATA TEKNISI</td></tr>
<tr class="" ><td style="border:solid 1px black;width:170;text-align:left;">Nama</td><td style="border:solid 1px black;width:170;text-align:center;">Jam</td><td style="border:solid 1px black;width:170;text-align:center;">Tanda Tangan</td><td style="border:solid 1px black;width:170;text-align:center;">Keterangan</td></tr>';

for ($i=0;$i<count($rsDetailTechnician);$i++){  

    $html .= '<tr>
            <td style="border:solid 1px black;text-align:left;"> '.$rsDetailTechnician[$i]['technicianname'].'</td>
            <td style="border:solid 1px black;text-align:center;"></td>
            <td style="border:solid 1px black;text-align:center;"></td>
            <td style="border:solid 1px black;text-align:center;"></td>
            </tr>' ; 
}
$html .= '</table><div style="clear:both"></div>' ;
      

$html .= ' 
<table cellpadding="4" class="" style="">
<tr><td style="font-weight:bold; font-size:14px;">MATERIAL INSTALASI</td></tr>
<tr class="" ><td style="border:solid 1px black;width:40px;text-align:right;">No.</td><td style="border:solid 1px black;width:510px;;text-align:left;">Material</td><td style="border:solid 1px black;; width:70px;text-align:right" >Quantity</td><td style="border:solid 1px black;width:60px;" >Unit</td></tr>';

for ($i=0;$i<count($rsDetail);$i++){  

    $html .= '<tr>
            <td style="border:solid 1px black; text-align:right">'.($i+1).'.</td>
            <td style="border:solid 1px black;  text-align:left">'.$rsDetail[$i]['itemname'].'</td>
            <td style="border:solid 1px black; text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td>
            <td style="border:solid 1px black;">'. $rsDetail[$i]['unitname'] .'</td>
            </tr>' ; 
}
$html .= '</table><div style="clear:both"></div>' ;
    
 $html .= '
<table cellpadding="2" style="font-size:12px;"> 
<tr>
<td class="" style="border-bottom:solid 1px black; width: 150px;height:100px text-align:center;">Mengetahui,<br>Customer,</td><td ></td><td colspan="2" style="border:solid 1px black;width: 360;"> '.$rs[0]['trdesc'].' </td>
</tr>
</table> 
<div style="clear:both"></div>

';     
//$html .= $obj->generateSignLabel($rs); 
return $html;
}

?>