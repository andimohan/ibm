<?php  
$pdf->setCustomSettings(
    array(
         'showPrintHeader' => false,
         'paperSetting' => 'A5,L',
         ) 
); 
   
$generateReportContent = function ($dataset){

$obj = new TruckingServiceWorkOrder();  
$truckingServiceOrder = new TruckingServiceOrder();   
$city = new City();
$service = new Service();
$employee = new Employee();
$customer = new Customer();
$consignee = new Consignee();
    
$rs = $dataset['rs']; 
        
$rsJOHeader =  $truckingServiceOrder->getDataRowById($rs[0]['refkey']);
$rsDetail = $truckingServiceOrder->getDetailByColumn('pkey',$rs[0]['refdetailkey']); 
$rsService = $service->getDataRowById($rsDetail[0]['itemkey']);      
    
$driverName = $rs[0]['drivername'];

$locationname = '';
if (!empty($rs[0]['citykey'])){ 
$rsCity = $city->searchData($city->tableName.'.pkey',$rs[0]['citykey']);
$locationname = $rsCity[0]['citycategoryname']; 
}
        
$timeformat = ($obj->formatDBDate($rs[0]['stuffingdatetime'],'H:i') == "00:00") ? 'd / m / Y' : 'd / m / Y H:i';     
    
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';     
    
$container = $rs[0]['containernumber'];
if (!empty($rs[0]['container2number'])) $container .= ', ' . $rs[0]['container2number']; 
     
$productDesc = str_replace(chr(13),'<br>',$rs[0]['productdesc']);
    
$depotname = (!empty($rs[0]['depotname'])) ? $rs[0]['depotname'] : ' - ';
$terminalname = (!empty($rs[0]['terminalname'])) ? $rs[0]['terminalname'] : ' - ';
    
$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
$rsConsignee = $consignee->getDataRowById($rsJOHeader[0]['consigneekey']);
//$customer->setLog($rsConsignee,true);
/*$rsContactPerson = $customer->getContactPerson($rs[0]['customerkey']);
$rsContactPerson[0]['name'] = (!empty($rsContactPerson[0]['name'])) ? $rsContactPerson[0]['name'] : '';
$rsContactPerson[0]['phone'] = (!empty($rsContactPerson[0]['phone'])) ? $rsContactPerson[0]['phone'] : '';*/
    
$stuffingAddress = (isset($rs[0]['stuffingaddress']) && !empty($rs[0]['stuffingaddress'])) ? $rs[0]['stuffingaddress'] : $rsCustomer[0]['address'];
    
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.ucwords($obj->lang['deliveryNotes']).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table>
<div style="clear:both"></div>
<table>
<tr>
<td>
<table cellpadding="2"> 
<tr><td class="header-row-header" style="width:140px">'.ucwords($obj->lang['date']).'</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header" style="width:140px">'.ucwords($obj->lang['stuffingDate']).'/ Bongkar</td><td style="text-align:center">:</td><td>'.$obj->formatDBDate($rs[0]['stuffingdatetime'],$timeformat).'</td></tr>  
<tr><td class="header-row-header" style="width:140px">'.ucwords($obj->lang['orderNumber']).'</td><td style="text-align:center">:</td><td>'. $rs[0]['serviceordercode'] .'</td></tr> 
<tr><td class="header-row-header" style="width:140px">'.ucwords($obj->lang['si']).'</td><td style="text-align:center">:</td><td>'. $rs[0]['donumber'] .'</td></tr>  
<tr><td class="header-row-header" style="width:140px">No. PO & Jenis Barang</td><td style="text-align:center">:</td><td>'. $rs[0]['shipmentnumber'] .'</td></tr>  
<tr><td colspan="3"></td></tr> 
</table>
<table>
<tr><td style="font-weight:bold; width: 80px">'.ucwords($obj->lang['car']).'</td><td style="font-weight:bold">'.ucwords($obj->lang['driver']).'</td></tr>  
<tr><td>'. $rs[0]['policenumber'] .'</td><td>'. $driverName.'</td></tr>  
</table> 
</td>
<td>
<table cellpadding="2" >
<tr><td class="header-row-header" style="width:120px">Kepada Yth</td><td style="width:10px; text-align:center">:</td><td style="width:200px;">'.$rs[0]['customername'].'</td></tr> 
<tr><td class="header-row-header">'.ucwords($obj->lang['consignee']).'</td><td style="text-align:center">:</td><td>'. $rsConsignee[0]['name'] .'</td></tr>
<tr><td class="header-row-header">'.ucwords($obj->lang['contactPerson']).'</td><td style="text-align:center">:</td><td>'. $rsConsignee[0]['contactperson'] .'</td></tr>
<!-- <tr><td class="header-row-header"></td><td style="text-align:center"></td><td>'. $rsContactPerson[0]['phone'] .'</td></tr> -->
<tr><td class="header-row-header">'.ucwords($obj->lang['address']).'</td><td style="text-align:center">:</td><td>'.str_replace(chr(13),'<br>',$stuffingAddress).'</td></tr>  
</table>
</td>
</tr>
</table>

<div style="clear:both"></div> 
 
<table cellpadding="4" class="table-transaction">
<tr class="col-header"><td style="text-align:right;width:30px">'.ucwords($obj->lang['number']).'</td><td style="width:160px">'.ucwords($obj->lang['size']).'</td><td style="width:240px">'.ucwords($obj->lang['containerNumber']).'</td><td style="width:240px">Jumlah / Jenis Kemasan</td></tr>  
<tr><td style="text-align:right;">1.</td><td>'.$rsService[0]['name'].'</td><td>'.$container.'</td><td>'.$productDesc.'</td></tr>   
</table>  
'.$trnotes.' 
<div style="clear:both"></div>  
';
 
$rsEmployee = $employee->getDataRowById(base64_decode($_SESSION[$employee->loginAdminSession]['id']));
    
$arrSignLabel = array(); 
array_push($arrSignLabel, array(ucwords($obj->lang['opsTrucking']),$rsEmployee[0]['name']));
array_push($arrSignLabel, array(ucwords($obj->lang['driver']),$driverName) ); 
array_push($arrSignLabel, array(ucwords($obj->lang['recipient']).'/'.ucwords($obj->lang['warehouse']))); 

 $html .=' 
        <table cellpadding="4" class="sign">
        <tr>'; 
        for ($i=0;$i<count($arrSignLabel);$i++){
            $html .='<td  class="sign-col" style="height:50px"><strong>'.$arrSignLabel[$i][0].'</strong></td>';
            if ($i <> count($arrSignLabel) - 1)
                $html .= '<td class="sign-col-space"></td>';
        }
        $html .='</tr> 
        <tr>'; 
        for ($i=0;$i<count($arrSignLabel);$i++){
            $arrSignLabel[$i][1] = (isset($arrSignLabel[$i][1])) ? $arrSignLabel[$i][1] : '';
            $html .='<td  class="sign-name">'.$arrSignLabel[$i][1].'</td>';
            if ($i <> count($arrSignLabel) - 1)
                $html .= '<td class="sign-col-space"></td>';
        }
        $html .='</tr> 
        </table>' ;

    
$html .=' 
<div style="clear:both"></div>  
';
    
$html = '<div style="font-size:0.9em">'.$html.'</div>';    
return $html;
}

?>
