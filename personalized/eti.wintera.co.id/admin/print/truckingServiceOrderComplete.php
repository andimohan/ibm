<?php 

$pdf->setCustomSettings(
    array( 
         'marginFooter' => '14',  
         'logoSize' => '60,24',  
         'headerAlign' => 'right', 
         'showPrintHeader' => false,   
         'footer' => '',   
    ) 
); 
 
$generateReportContent = function ($dataset){ 
   
$obj = new TruckingServiceOrder();
$employee = new Employee();
$service = new Service(TRUCKING_SERVICE,1); 
$security = new Security();
 
$generateSellingTable = function ($obj, $rs, $rsDetail, &$totalSelling){
     
$html = '
<div style="font-weight:bold">SELLING DESCRIPTION (PIC: CUSTOMER SERVICE)<br></div>
<table cellpadding="4" class="table-transaction" style="font-size:10px; border-bottom:0px solid #fff;">';

$cellArray = array ();
array_push($cellArray, array('label' => ''));
array_push($cellArray, array('label' => 'SATUAN', 'align' => 'right', 'width' => '70'));
array_push($cellArray, array('label' => 'HARGA','align' => 'right', 'width' => '60'));
array_push($cellArray, array('label' => 'JUMLAH','align' => 'right', 'width' => '70')); 
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '330', 'cell' =>  $cellArray));  
 
$rsSellingCost = $obj->getSellingCostDetail($rs[0]['pkey']);
    
$totalSelling = 0; 
for($i=0;$i<count($rsDetail);$i++){  
    
    $qty = $rsDetail[$i]['qtyinbaseunit']; 
    $amount = $rsDetail[$i]['priceinunit']; 
    $subtotal = $qty * $amount;
    $totalSelling += $subtotal;  

    $borderStyle = ($i<count($rsDetail) -1 || !empty($rsSellingCost)) ? 'col-border-bottom' : 'last-border-bottom';
    
    $html .= '
    <tr>   
    <td class="'.$borderStyle.'">'.$rsDetail[$i]['itemname'].'</td>    
    <td class="'.$borderStyle.'" style="text-align:center">'.$obj->formatNumber($qty).'</td>  
    <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($amount).'</td>  
    <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
    </tr>
    '; 
}    
  
// selling cost 
for($j=0;$j<count($rsSellingCost);$j++){ 
    
    $qty = $rsSellingCost[$j]['qty'];
    $price = $rsSellingCost[$j]['price'];
    $subtotal = $qty * $price;
    $totalSelling += $subtotal;
         
    $borderStyle = ($j<count($rsSellingCost) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
    
    $html .= '
    <tr> 
    <td class="'.$borderStyle.'">'.$rsSellingCost[$j]['itemname'].'</td> 
    <td class="'.$borderStyle.'" style="text-align:center;">'.$obj->formatNumber($qty).'</td> 
    <td class="'.$borderStyle.'" style="text-align:right;">'.$obj->formatNumber($price).'</td> 
    <td class="'.$borderStyle.'" style="text-align:right;">'.$obj->formatNumber($subtotal).'</td> 
    </tr>'; 
}
    
    
$html .= ' 
<tr><td colspan="2"></td><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($totalSelling).'</td></tr>
</table>
';

    
return $html;
};

$generateCostTable = function ($obj, $rs, $title , $outsource = 0, &$totalCost){ 
    
    $html  = '<div style="font-weight:bold">'.$title.'<br></div>
    <table cellpadding="4" class="table-transaction" style="font-size:10px; border-bottom:0px solid #fff">';
    
    $cellArray = array ();
    array_push($cellArray, array('label' => ''));
    array_push($cellArray, array('label' => 'SATUAN', 'align' => 'center', 'width' => '70'));
    array_push($cellArray, array('label' => 'HARGA','align' => 'right', 'width' => '60'));
    array_push($cellArray, array('label' => 'JUMLAH','align' => 'right', 'width' => '70')); 
    $html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '330', 'cell' =>  $cellArray));  
 
    $total = 0;
    $useRealization = $obj->useRealization();
    
    $rsCost = $obj->getWorkOrderCostDetail($rs[0]['pkey'],$outsource, false); 
    $rsCost = $obj->groupCostAmount($rsCost);
    
    for($i=0;$i<count($rsCost);$i++){
        $isRealize = (!$useRealization) ? true : $rsCost[$i]['isrealization'];
        $qty = $rsCost[$i]['qty']; 
        $amount = $rsCost[$i]['amount']; 
        $subtotal = $qty * $amount;
        $total += $subtotal;  
        
        $borderStyle = ($i<count($rsCost) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
        
        //$rsCost[$i]['costname'] = ($rsCost[$i]['headerrow']) ? 'ONGKOS VENDOR (TL)' : $rsCost[$i]['costname'];
        $rsCost[$i]['costname'] = ((!$isRealize) ? '* ' : '') . $rsCost[$i]['costname'];
            
        $html.= '
        <tr>   
        <td class="'.$borderStyle.'">'.$rsCost[$i]['costname'].'</td>    
        <td class="'.$borderStyle.'" style="text-align:center">'.$obj->formatNumber($qty).'</td>  
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($amount).'</td>  
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
        </tr>
        ';  
    } 
  
     
    $html .= ' 
    <tr><td colspan="2"></td><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($total).'</td></tr>
    </table>
    <div style="clear:both"></div> 
    ';
    
    $totalCost += $total;
    
    return ($total == 0) ? '' : $html;
} ;
    
$generateAdditionalCostTableCustom = function ($obj,$rs,&$totalCost){ 
    
    $obj = new TruckingServiceOrder();
    
    $id = $rs[0]['pkey'];
    $rs = $obj->getHeaderCost($id);
    
    $html  = '<div style="font-weight:bold">BIAYA TAMBAHAN<br></div>
    <table cellpadding="4" class="table-transaction" style="font-size:10px; border-bottom:0px solid #fff">';
    
    $cellArray = array ();
    array_push($cellArray, array('label' => ''));
    array_push($cellArray, array('label' => 'SATUAN', 'align' => 'center', 'width' => '70'));
    array_push($cellArray, array('label' => 'HARGA','align' => 'right', 'width' => '60'));
    array_push($cellArray, array('label' => 'JUMLAH','align' => 'right', 'width' => '70')); 
    $html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '330', 'cell' =>  $cellArray));  
    
    $total = 0; 
 
    $borderStyle = 'col-border-bottom';

    for($i=0;$i<count($rs);$i++){
        $qty = $rs[$i]['qty']; 
        $amount = $rs[$i]['amount']; 
        $subtotal = $qty * $amount;
        $total += $subtotal;  
        
        if ($subtotal == 0) continue; 

        $borderStyle = ($i<count($rs) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
        
        $html .= '
                <tr>   
                <td class="'.$borderStyle.'">'.$rs[$i]['itemname'].'</td>    
                <td class="'.$borderStyle.'" style="text-align:center">'.$obj->formatNumber($qty).'</td>  
                <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($amount).'</td>  
                <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
                </tr>';

        $servicename = '';  
    }
  
    $html .= ' 
    <tr><td colspan="2"></td><td style="text-align:right; font-weight:bold;">Total</td><td style="text-align:right; font-weight:bold;">'.$obj->formatNumber($total).'</td></tr>
    </table>
    <div style="clear:both"></div> 
    ';
     
    $totalCost += $total;
    return ($total == 0) ? '' : $html;
};
     
$generateContainerTable = function ($obj,$rs){
    
    $serviceWorkOrder = new TruckingServiceWorkOrder();
    $supplier = new Supplier();
    $rsSPK = $serviceWorkOrder->searchData($serviceWorkOrder->tableName.'.refkey',$rs[0]['pkey'],true,' and '.$serviceWorkOrder->tableName.'.statuskey in (2,3)' , ' order by refkey asc');
    
    // generate semua SPK
    
    $html  = '<div style="font-weight:bold;">CONTAINER DESCRIPTION (PIC: ADM / OPS)<br></div>
    <table cellpadding="4" class="table-transaction" style="font-size:10px">';
    
    $cellArray = array ();
    array_push($cellArray, array('label' => 'SEQ', 'align' => "center",  'width' => '40'));
    array_push($cellArray, array('label' => 'NO CONTAINER', 'width' => '90'));
    array_push($cellArray, array('label' => 'TRUCK', 'align' => 'center','width' => '80'));
    array_push($cellArray, array('label' => 'MITRA / SUPPLIER')); 
    $html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '330', 'cell' =>  $cellArray));  
 
    
   // <tr class="col-header"><td style="text-align:right;width:30px">SEQ</td><td style="width:120px; text-align:center">NO CONTAINER</td><td style="width:75px; text-align:center;">TRUCK</td><td style="width:75px;">SOPIR</td></tr> ';

    for($i=0;$i<count($rsSPK);$i++){  
        $containerNumber = '';
        $registrationNumber = '';
        $driverName = '';
        
        $containerNumber = $rsSPK[$i]['containernumber'];
        $containerNumber .= (!empty($rsSPK[$i]['container2number'])) ? '<br>'.$rsSPK[$i]['container2number'] : '';
            
        if (empty($rsSPK[$i]['TL'])){ 
            $driverName = (!empty($rsSPK[$i]['drivername'])) ? $rsSPK[$i]['drivername'] : '';
            $registrationNumber = (!empty($rsSPK[$i]['policenumber'])) ? $rsSPK[$i]['policenumber'] : '';
            //$chassisnumber = (!empty($rsSPK[$k]['chassisnumber'])) ? $rsSPK[$k]['chassisnumber'] : ''; 
            //$TL = '';
        }else{
            $registrationNumber = (!empty($rsSPK[$i]['outsourcecarregistrationnumber'])) ? $rsSPK[$i]['outsourcecarregistrationnumber'] : '';
            $rsSupplier = $supplier->getDataRowById($rsSPK[$i]['supplierkey']);
            $driverName = $rsSupplier[0]['name'];
        }

        $borderStyle = ($i<count($rsSPK) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
        $html  .= '<tr>
                        <td class="'.$borderStyle.'" style="text-align:center;">'.($i+1).'</td>
                        <td class="'.$borderStyle.'">'.$containerNumber.'</td>
                        <td class="'.$borderStyle.'" style="text-align:center;">'.$registrationNumber.'</td>
                        <td class="'.$borderStyle.'">'.$driverName.'</td>
                   </tr>'; 
    }
    
    $html .= '</table>';
    
    return $html;
};
    
    
$hasSellingPriceAccess = $security->isAdminLogin($obj->sellingPriceSecurityObject,10);  
    
$rs = $dataset['rs'];
    
$arrParty = array();
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);

$partyDecimal = $obj->loadSetting('jobOrderPartyDecimal'); 
if (empty($partyDecimal)) $partyDecimal = 0; // buat jaga2
	
for($i=0;$i<count($rsDetail);$i++) {
	 $party =  $obj->formatNumber($rsDetail[$i]['qtyinbaseunit'],$partyDecimal); 
	 array_push($arrParty,$party. 'x ' . $rsDetail[$i]['itemname'] );
}
	
$party = implode('<br>',$arrParty);
 
$totalCost = 0;
    
$trnotes = (!empty($rs[0]['trnotes'])) ? '<div style="clear:both"></div><strong>Catatan :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trnotes']) : '';     
 
 
$costTable = $generateCostTable($obj,$rs,'COST DESCRIPTION (PIC: ADM / OPS)',0,$totalCost); 
$costOutsourceTable = $generateCostTable($obj,$rs, 'OUTSOURCE COST DESCRIPTION (PIC: ADM / OPS)', 1,$totalCost);
$additionalCost =  $generateAdditionalCostTableCustom($obj,$rs,$totalCost);
    
$sellingTable =  ($hasSellingPriceAccess) ? $generateSellingTable($obj,$rs, $rsDetail, $totalSelling) : '';
$containerTable =  $generateContainerTable($obj,$rs);
    
$html = $obj->printSetting['defaultStyle'];
    
    
// HEADER
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">JOB ORDER COMPLETE FORM</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2"> 
<tr><td class="header-row-header" style="width:120px">Tanggal</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">DO Pelanggan</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['donumber'] .'</td></tr> 
<tr><td class="header-row-header">Booking Pelayaran</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['shipmentnumber'] .'</td></tr>  
<tr><td class="header-row-header">Pekerjaan</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['cargotype'] .', ' . $rs[0]['categoryname'].'</td></tr>  
<tr><td class="header-row-header">Party</td><td style="width:10px; text-align:center">:</td><td>'. $party .'</td></tr>  
</table> 
</td>
<td style="width:370px;">
<table cellpadding="2" >
<tr><td class="header-row-header" style="width:120px">Pelanggan</td><td style="width:10px; text-align:center">:</td><td style="width:240px;">'.$rs[0]['customername'].'</td></tr> 
<tr><td class="header-row-header">Consignee</td><td style="text-align:center">:</td><td>'.$rs[0]['consigneename'].'</td></tr> 
<tr><td class="header-row-header">Lokasi Stuffing</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['locationname'].'</td></tr> 
<tr><td class="header-row-header">Pabrik / Gudang</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['consigneewarehousename'].'</td></tr>   
<tr><td class="header-row-header">Depot</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['depotname'].'</td></tr>   
</table>
</td>
</tr>
</table>'; 
    
$html .= '<div style="clear:both"></div>';
 
// DETAIL
$profit = $totalSelling -  $totalCost; 
$percentageSelling = (!empty($totalSelling)) ? ($profit / $totalSelling) : 0;
    
$profitTable  = '<div style="clear:both"></div>  
<table cellpadding="2" style="width: 250px" >
<tr><td style="width:100px"><strong>TOTAL BIAYA</strong></td><td style="text-align:right">'.$obj->formatNumber($totalCost).'</td><td style="width:30px"></td></tr>
<tr><td ><strong>HARGA JUAL</strong></td><td style="border-bottom:1px solid #333; text-align:right">'.$obj->formatNumber($totalSelling).'</td><td></td></tr>
<tr><td><strong>BALANCE</strong></td><td style="text-align:right">'.$obj->formatNumber($profit).'</td><td style="text-align:right; width: 75px">'.$obj->formatNumber( $percentageSelling * 100,2).'% </td></tr>
</table>
'; 
     
$html .= '<table>
<tr>
<td style="width:330px;">'.$costTable.$costOutsourceTable.$additionalCost.$sellingTable.$profitTable.'</td>
<td style="width:20px"></td>
<td style="width:330px;">'.$containerTable.'</td>
</tr>
</table>  
'; 
        

    
// FOOTER
$html .= '<div style="clear:both"></div> 
'.$trnotes.'  
<div style="clear:both"></div> 
';
 
$rsEmployee = $employee->getDataRowById(base64_decode($_SESSION[$employee->loginAdminSession]['id']));
    
$arrSignLabel = array(); 
array_push($arrSignLabel, array('Dibuat'));
array_push($arrSignLabel, array('Diperiksa') ); 
array_push($arrSignLabel, array('Disetujui') ); 

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

      
    
return '<div style="font-size:0.8em">'.$html.'</div>' ;
};
