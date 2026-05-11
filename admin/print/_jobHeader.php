<?php 
$obj = $emklJobOrderExport;

$generateReportContent = function ($dataset){ 
$obj = new EMKLJobOrder(EMKL['jobType']['export']);  
$emklPurchaseOrderExport = new EMKLPurchaseOrder(EMKL['jobType']['export']);  
    
$service = new Service(SERVICE);
$employee = new Employee();
$container = new Container();
$currency = new Currency(); 
$location = new Location(); 
$emklCommission = new EMKLCommission();
    
$rsStuffing = $location->getDataRowById($rs[0]['locationkey']);
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

$arrCust = array();
for($i=0;$i<count($rsDetail);$i++){
    array_push($arrCust,$rsDetail[$i]['customername']);
}
    
$customerName = implode(' ,',$arrCust);
    
$rsCurrency = $currency->searchData();
$rsCurrency = array_column($rsCurrency,'name','pkey');
    
$rsContainer = $container->searchData();
$rsContainer = array_column($rsContainer,'name','pkey');
    
$rsService = $service->searchData();  
$rsService = array_column($rsService,'name','pkey');
     
$rsFreightTerm = $obj->getFreightTerm();
$rsFreightTerm = array_column($rsFreightTerm,'name','pkey');
        
$rs = $dataset['rs'];  
    
$html = $obj->printSetting['defaultStyle'];
$html .= '
<table cellpadding="4"><tr><td style="width:150px;">BOOKING</td><td style="width:12px;">:</td><td style="width:268px;font-weight:bold;">'.$rs[0]['bookingnumber'].'</td><td style="width:120px;"></td><td style="width:12px;"></td><td style="width:120px;">ETI</td></tr>
</table>';

$html .= '<table cellpadding="4" style="border:solid 1px black"> 
<tr><td style="border-bottom:solid 1px #e0e0;width:150px;">REF NO</td><td style="border-bottom:solid 1px #e0e0;width:12px;">:</td><td style="border-bottom:solid 1px #e0e0;width:268px;">'.$rs[0]['code'].'</td><td style="border-bottom:solid 1px #e0e0;width:120px;">ETD</td><td style="border-bottom:solid 1px #e0e0;width:12px;">:</td><td style="border-bottom:solid 1px #e0e0;width:120px;">'.$obj->formatDBDate($rs[0]['etdpol'],'d-M-y').'</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">SHIPPER</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$customerName.'</td><td style="border-bottom:solid 1px #e0e0;">ETA</td><td style="border-bottom:solid 1px #e0e0;width:12px;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$obj->formatDBDate($rs[0]['etapod'],'d-M-y').'</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">POL/POD</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$rs[0]['polname'].' - '.$rs[0]['podname'].'</td><td rowspan="4"></td><td style="border-bottom:solid 1px #e0e0;">/</td><td rowspan="4"></td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">CARRIER</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$rs[0]['carriername'].'</td><td style="border-bottom:solid 1px #e0e0;">/</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">VOLUME</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;"></td><td style="border-bottom:solid 1px #e0e0;">/</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">FEEDER</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;"></td><td style="border-bottom:solid 1px #e0e0;">/</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">M/V</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;"></td><td style="border-bottom:solid 1px #e0e0;">AJU</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$rs[0]['aju'].'</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">CLOSING</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$obj->formatDBDate($rs[0]['closingdate'],'d M').'</td><td style="border-bottom:solid 1px #e0e0;">PEB</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$rs[0]['peb'].'</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">INVOICE NO</td><td style="border-bottom:solid 1px #e0e0;">:</td><td  style="border-bottom:solid 1px #e0e0;"></td><td style="border-bottom:solid 1px #e0e0;">DATE </td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">STUFFING</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;">'.$rsStuffing[0]['name'].'</td><td style="border-bottom:solid 1px #e0e0;">IN</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;"></td></tr>
<tr><td style="border-bottom:solid 1px #e0e0;">Temp</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;"></td><td style="border-bottom:solid 1px #e0e0;">OUT</td><td style="border-bottom:solid 1px #e0e0;">:</td><td style="border-bottom:solid 1px #e0e0;"></td></tr>
</table> 
';

$html .= '<div  style="clear:both"></div><table cellpadding="4">
<tr>
<td style="border:solid 1px black;width:90px">INV</td><td style="width:5px"></td>
<td style="border:solid 1px black;width:90px">LO</td><td style="width:5px"></td>
<td style="border:solid 1px black;width:90px">PEB</td><td style="width:5px"></td>
<td style="border:solid 1px black;width:90px">NPE</td><td style="width:5px"></td>
<td style="border:solid 1px black;width:90px">CB</td><td style="width:5px"></td>
<td style="border:solid 1px black;width:120px">Submit Final</td><td style="width:5px"></td>
</tr>    
    
</table>';
//$html .= $obj->generateSignLabel($rs); 
return $html;
}

?>