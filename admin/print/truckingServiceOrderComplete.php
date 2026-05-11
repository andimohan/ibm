<?php 
  
includeClass(array('TruckingServiceOrder.class.php','TruckingPurchaseRefund.class.php'));
$truckingServiceOrder = createObjAndAddToCol( new TruckingServiceOrder()); 
$truckingServiceWorkOrder = createObjAndAddToCol( new TruckingServiceWorkOrder()); 

$obj = $truckingServiceOrder;
       
$arrID = array();
if (isset( $_GET['id']) && !empty( $_GET['id'])){ 
    $arrID = explode(',',$_GET['id']);
}else if (isset( $_GET['wokey']) && !empty( $_GET['wokey'])){ 
    $arrWO = explode(',',$_GET['wokey']);
    
    for ($i=0;$i<count($arrWO);$i++){
        $rsTemp = $truckingServiceWorkOrder->getDataRowById($arrWO[$i]);
        if (!in_array($rsTemp[0]['refkey'], $arrID))
          array_push($arrID,$rsTemp[0]['refkey']);
    }
}
        


$summaryContent = function ($dataset){  
   
$totalSelling = 0;
     
$generateSellingTable = function ($obj, $rs, $rsDetail, &$totalSelling){ 
      
$html  = '<br>
<div style="font-weight:bold; line-height:30px; ">'.strtoupper($obj->lang['sellingPrice']).'</div>
<table cellpadding="4" class="table-transaction" style="font-size:10px; border-bottom:0px solid #fff">';
    
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['description']));
array_push($cellArray, array('label' => $obj->lang['qty'], 'align' => 'right', 'width' => '40'));
array_push($cellArray, array('label' => $obj->lang['price'],'align' => 'right', 'width' => '60'));
array_push($cellArray, array('label' => $obj->lang['total'],'align' => 'right', 'width' => '70')); 
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '330', 'cell' =>  $cellArray));  
      
$totalSelling = 0;  
for($j=0;$j<count($rsDetail);$j++){ 
    $totalSelling += $rsDetail[$j]['total'];

    $borderStyle = ($j<count($rsDetail) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
    
    $html .= '
    <tr>  
    <td class="'.$borderStyle.'">'.$rsDetail[$j]['itemname'].'</td>
    <td class="'.$borderStyle.'" style="text-align:right;">'.$obj->formatNumber($rsDetail[$j]['qtyinbaseunit']).'</td> 
    <td class="'.$borderStyle.'" style="text-align:right;">'.$obj->formatNumber($rsDetail[$j]['priceinunit']).'</td> 
    <td class="'.$borderStyle.'" style="text-align:right;">'.$obj->formatNumber($rsDetail[$j]['total']).'</td> 
    </tr>';  
}    
  
// selling cost 
$rsSellingCost = $obj->getSellingCostDetail($rs[0]['pkey'],' and reimburse = 0'); 
for($i=0;$i<count($rsSellingCost);$i++){ 
    
    $qty = $rsSellingCost[$i]['qty'];
    $price = $rsSellingCost[$i]['price'];
    $subtotal = $qty * $price;
    $totalSelling += $subtotal;
         
    $borderStyle = ($i<count($rsSellingCost) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
    
    $html .= '
    <tr>  
    <td class="'.$borderStyle.'">'.$rsSellingCost[$i]['itemname'].'</td> 
    <td class="'.$borderStyle.'" style="text-align:right;">'.$obj->formatNumber($qty).'</td> 
    <td class="'.$borderStyle.'" style="text-align:right;">'.$obj->formatNumber($price).'</td> 
    <td class="'.$borderStyle.'" style="text-align:right;">'.$obj->formatNumber($subtotal).'</td> 
    </tr>'; 
     
}
    
    
$html .= ' 
<tr><td colspan="3"></td><td class="subtotal" style="text-align:right;">'.$obj->formatNumber($totalSelling).'</td></tr>
</table>
';
     
return $html;
};

$generateCostTable = function ($obj, $rs, $outsource = 0, &$totalCost){
   
    $html = '';
    $total = 0;
    
 	 
    $rsCost = $obj->getWorkOrderCostDetail($rs[0]['pkey'],$outsource,false, ' and reimburse = 0', ' order by costname asc, amount asc, requestamount asc ');  
    $rsCost = $obj->groupCostAmount($rsCost);
      
    $useRealization = $obj->useRealization(); 
    
    for($i=0;$i<count($rsCost);$i++){
        
        $isRealize = (!$useRealization) ? true : $rsCost[$i]['isrealization']; 
        
        $qty = $rsCost[$i]['qty']; 
        $amount = ($isRealize) ? $rsCost[$i]['amount'] : $rsCost[$i]['requestamount']; 
        $subtotal = $qty * $amount;
        $total += $subtotal;  
        
        $borderStyle = ($i<count($rsCost) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
        $rsCost[$i]['costname'] = ((!$isRealize) ? '* ' : '') . $rsCost[$i]['costname'];
        
        $html.= '
        <tr>   
        <td class="'.$borderStyle.'">'.$rsCost[$i]['costname'].'</td>    
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($qty).'</td>  
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($amount).'</td>  
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
        </tr>
        ';
         

    } 
  
    $totalCost += $total; 
    return $html;
    //return ($total == 0) ? '' : $html;
} ; 
    
$generateAdditionalCostTable = function ($obj,$rs,&$totalCost){ 
    
    $id = $rs[0]['pkey'];
    
    $obj = new TruckingServiceOrder();
    $rsCost = $obj->getHeaderCost($id,' and reimburse = 0');
    
    $html  = ''; 
    $total = 0; 

    $useRealization = $obj->useRealization(); 
    for($i=0;$i<count($rsCost);$i++){
        
        $isRealize = (!$useRealization) ? true : $rsCost[$i]['isrealization']; 
        
        $qty = $rsCost[$i]['qty']; 
        $amount = ($isRealize) ? $rsCost[$i]['amount'] : $rsCost[$i]['requestamount']; 
        $subtotal = $qty * $amount;
        $total += $subtotal;  
      
       
        $rsCost[$i]['itemname'] = ((!$isRealize) ? '* ' : '') . $rsCost[$i]['itemname'];
 
        $borderStyle = ($i<count($rsCost) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
 
        $html .= '
        <tr>   
        <td class="'.$borderStyle.'">'.$rsCost[$i]['itemname'].'</td>    
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($qty).'</td>  
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($amount).'</td>  
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
        </tr>
        '; 
    }
  
    
    $totalCost += $total;
    
    return $html;
    //return ($total == 0) ? '' : $html;
} ;

$generateRefundTable = function ($obj,$rs,&$totalCost){ 
    
    $id = $rs[0]['pkey'];
    
    $obj = new TruckingPurchaseRefund();
    $employee = new Employee();
    $rsCost = $obj->searchData('','',true,' and ' .$obj->tableName.'.statuskey in (2,3) 
											and '.$obj->tableName.'.refjoborderkey = ' . $obj->oDbCon->paramString($id)
							  );
     
    $html  = ''; 
    $total = 0; 
    
    for($i=0;$i<count($rsCost);$i++){
 
        $amount =  $rsCost[$i]['total'];    
        $qty = 1;  
        $subtotal = $qty * $amount;
        $total += $subtotal;
         
        $borderStyle = ($i<count($rsCost) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
 
        $html .= '
        <tr>   
        <td class="'.$borderStyle.'">'.$rsCost[$i]['suppliername'].'</td>   
        <td class="'.$borderStyle.'" style="text-align:right">1</td>    
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
        </tr>
        ';
 
        $title = '';
    }
   
    $totalCost += $total;
    
    return $html; 
} ;   	
	
$generateContainerTable = function ($obj,$rs){
    
    $supplier = new Supplier();
    $serviceWorkOrder = new TruckingServiceWorkOrder();
    $rsSPK = $serviceWorkOrder->searchData($serviceWorkOrder->tableName.'.refkey',$rs[0]['pkey'],true,' and '.$serviceWorkOrder->tableName.'.statuskey in (2,3)' , ' order by refkey asc');
    
    // generate semua SPK
    
    $html  = '<div style="font-weight:bold; line-height:30px; ">'.strtoupper($obj->lang['container']).'</div>
    <table cellpadding="4" class="table-transaction" style="font-size:10px;  border-bottom:1px solid #333;">';

    $cellArray = array ();
    array_push($cellArray, array('label' => $obj->lang['containerNumber'], 'width' => '130'));
    array_push($cellArray, array('label' => $obj->lang['sealNumber'], 'width' => '130'));
    array_push($cellArray, array('label' => $obj->lang['car'], 'width' => '130'));
    array_push($cellArray, array('label' => $obj->lang['driver'] . ' / ' .  $obj->lang['supplier'] ));
     
    $html .= $obj->generatePrintTableRow( array('class' => 'col-header',  'cell' =>  $cellArray));  
 
    //<tr class="col-header"><td style="width:150px;">NO CONTAINER</td><td style="width:75px; text-align:center;">'.ucwords(($obj->lang['car'])).'</td><td style="width:105px;">'.ucwords(($obj->lang['driver'])).'</td></tr> ';

    for($i=0;$i<count($rsSPK);$i++){  
        $containerNumber = '';
        $registrationNumber = '';
        $driverName = '';
        
        $containerNumber = $rsSPK[$i]['containernumber'];
        $containerNumber .= (!empty($rsSPK[$i]['container2number'])) ? '<br>'.$rsSPK[$i]['container2number'] : '';
            
        $sealNumber = $rsSPK[$i]['sealnumber'];
        $sealNumber .= (!empty($rsSPK[$i]['seal2number'])) ? '<br>'.$rsSPK[$i]['seal2number'] : '';
            
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

        $borderStyle = 'col-border-bottom';
        $html  .= '<tr>
                        <td class="'.$borderStyle.'">'.$containerNumber.'</td>
                        <td class="'.$borderStyle.'">'.$sealNumber.'</td>
                        <td class="'.$borderStyle.'">'.$registrationNumber.'</td>
                        <td class="'.$borderStyle.'">'.$driverName.'</td>
                   </tr>'; 
    }
    
    $html .= '</table>';
    
    return $html;
}; 
     
$generateReimbursementCost = function ($obj,$rs, &$totalCost){
      
    $total = 0; 
    
    $html = '
    <div style="font-weight:bold; line-height:30px; ">'.strtoupper($obj->lang['reimburse']).'</div>
    <table cellpadding="4" class="table-transaction cost-detail" style="font-size:10px; border-bottom:0px solid #fff">';

    $cellArray = array ();
    array_push($cellArray, array('label' => $obj->lang['description']));
    array_push($cellArray, array('label' => $obj->lang['qty'], 'align' => 'right', 'width' => '40'));
    array_push($cellArray, array('label' => $obj->lang['cost'],'align' => 'right', 'width' => '60'));
    array_push($cellArray, array('label' => $obj->lang['total'],'align' => 'right', 'width' => '70')); 
    $html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '330', 'cell' =>  $cellArray));  
  
    $detailHTML = '';
        
    $useRealization = $obj->useRealization(); 
    
    // INHOUSE
    $rsCost = $obj->getWorkOrderCostDetail($rs[0]['pkey'],false,false, ' and reimburse = 1', ' order by costname asc, amount asc, requestamount asc '); 
    $rsCost = $obj->groupCostAmount($rsCost);
    for($i=0;$i<count($rsCost);$i++){
        
        $isRealize = (!$useRealization) ? true : $rsCost[$i]['isrealization']; 
        
        $qty = $rsCost[$i]['qty']; 
        $amount = ($isRealize) ? $rsCost[$i]['amount'] : $rsCost[$i]['requestamount']; 
        $subtotal = $qty * $amount;
        $total += $subtotal;  
         
        $rsCost[$i]['costname'] = ((!$isRealize) ? '* ' : '') . $rsCost[$i]['costname'];
        $borderStyle = ($i<count($rsCost) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
        
        $detailHTML.= '
        <tr>   
        <td class="'.$borderStyle.'">'.$rsCost[$i]['costname'].'</td>    
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($qty).'</td>  
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($amount).'</td>  
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
        </tr>
        '; 

    } 
  

    // OUTSOURSE
    $rsCost = $obj->getWorkOrderCostDetail($rs[0]['pkey'],true,false, ' and reimburse = 1', ' order by costname asc, amount asc, requestamount asc '); 
    $rsCost = $obj->groupCostAmount($rsCost);
    
    for($i=0;$i<count($rsCost);$i++){
        
        $isRealize = (!$useRealization) ? true : $rsCost[$i]['isrealization']; 
        
        $qty = $rsCost[$i]['qty']; 
        $amount = ($isRealize) ? $rsCost[$i]['amount'] : $rsCost[$i]['requestamount'];
        $subtotal = $qty * $amount;
        $total += $subtotal;  
        
        $rsCost[$i]['costname'] = ((!$isRealize) ? '* ' : '') . $rsCost[$i]['costname'];
        $borderStyle = ($i<count($rsCost) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
        
        $detailHTML.= '
        <tr>   
        <td class="'.$borderStyle.'">'.$rsCost[$i]['costname'].'</td>    
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($qty).'</td>  
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($amount).'</td>  
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
        </tr>
        '; 

    } 
    
    // ADDITIONAL
    $rsCost = $obj->getHeaderCost($rs[0]['pkey'],' and reimburse = 1');
   
    for($i=0;$i<count($rsCost);$i++){
        
        $isRealize = (!$useRealization) ? true : $rsCost[$i]['isrealization']; 
        
        $qty = $rsCost[$i]['qty']; 
        $amount = ($isRealize) ? $rsCost[$i]['amount'] : $rsCost[$i]['requestamount']; 
        $subtotal = $qty * $amount;
        $total += $subtotal;  
      
 
        $rsCost[$i]['itemname'] = ((!$isRealize) ? '* ' : '') . $rsCost[$i]['itemname'];
        $borderStyle = ($i<count($rsCost) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
 
        $detailHTML .= '
        <tr>   
        <td class="'.$borderStyle.'">'.$rsCost[$i]['itemname'].'</td>    
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($qty).'</td>  
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($amount).'</td>  
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
        </tr>
        ';
  
    }
    
    $html .= $detailHTML;
        
    $html .= '  
    <tr >
        <td colspan="3"></td>
        <td class="subtotal" style="text-align:right;">'.$obj->formatNumber($total).'</td>
    </tr>';

    $html .= '</table>';
     
    $totalCost += $total;
    
    return $html;
}; 
     
$generateReimbursementSelling = function ($obj, $rs, &$totalSellingCost){ 
      
$html  = '
    <div style="font-weight:bold; line-height:30px; "> </div>
    <table cellpadding="4" class="table-transaction" style="font-size:10px; border-bottom:0px solid #fff">';
    
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['description']));
array_push($cellArray, array('label' => $obj->lang['qty'], 'align' => 'right', 'width' => '40'));
array_push($cellArray, array('label' => $obj->lang['price'],'align' => 'right', 'width' => '60'));
array_push($cellArray, array('label' => $obj->lang['total'],'align' => 'right', 'width' => '70')); 
$html .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '330', 'cell' =>  $cellArray));  
      
$totalSelling = 0;   
    
// selling cost 
$rsSellingCost = $obj->getSellingCostDetail($rs[0]['pkey'],' and reimburse = 1'); 
for($i=0;$i<count($rsSellingCost);$i++){ 
    
    $qty = $rsSellingCost[$i]['qty'];
    $price = $rsSellingCost[$i]['price'];
    $subtotal = $qty * $price;
    $totalSelling += $subtotal;
         
    $borderStyle = ($i<count($rsSellingCost) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
    
    $html .= '
    <tr>  
    <td class="'.$borderStyle.'">'.$rsSellingCost[$i]['itemname'].'</td> 
    <td class="'.$borderStyle.'" style="text-align:right;">'.$obj->formatNumber($qty).'</td> 
    <td class="'.$borderStyle.'" style="text-align:right;">'.$obj->formatNumber($price).'</td> 
    <td class="'.$borderStyle.'" style="text-align:right;">'.$obj->formatNumber($subtotal).'</td> 
    </tr>';  
}
     
$html .= ' 
<tr><td colspan="3"></td><td class="subtotal" style="text-align:right;">'.$obj->formatNumber($totalSelling).'</td></tr>
</table>
';
     
$totalSellingCost = $totalSelling;
return $html;
};
    
$obj = new TruckingServiceOrder();
$truckingPurchaseRefund = new TruckingPurchaseRefund();
$employee = new Employee(); 
$security = new Security();

$hasSellingPriceAccess = $security->isAdminLogin($obj->sellingPriceSecurityObject,10);  
$hasPurchaseRefundAccess = $security->hasSecurityAccess( $obj->userkey ,$security->getSecurityKey($truckingPurchaseRefund->securityObject),10);
    
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
$totalReimburseCost = 0;
$totalSellingReimburse = 0;
 
$costTable = ' <br>
    <div style="font-weight:bold; line-height:30px; ">'.strtoupper($obj->lang['cost']).'</div>
    <table cellpadding="4" class="table-transaction cost-detail" style="font-size:10px; border-bottom:0px solid #fff">';
     
$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['description']));
array_push($cellArray, array('label' => $obj->lang['qty'], 'align' => 'right', 'width' => '40'));
array_push($cellArray, array('label' => $obj->lang['cost'],'align' => 'right', 'width' => '60'));
array_push($cellArray, array('label' => $obj->lang['total'],'align' => 'right', 'width' => '70')); 
$costTable .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '330', 'cell' =>  $cellArray));  
     
    
$costTable .= $generateCostTable($obj,$rs,0,$totalCost); 
$costTable .= $generateCostTable($obj,$rs,1,$totalCost);
$costTable .= $generateAdditionalCostTable($obj,$rs,$totalCost);
	
$costTable .= '  
    <tr >
        <td colspan="3"></td>
        <td class="subtotal" style="text-align:right;">'.$obj->formatNumber($totalCost).'</td>
    </tr>';

$costTable .= '</table>';
	
// refund
if($hasPurchaseRefundAccess){
	$totalRefund = 0;	
	$costTable .= '<div style="font-weight:bold; line-height:30px; ">'.strtoupper($obj->lang['purchaseRefund']).'</div>
		<table cellpadding="4" class="table-transaction cost-detail" style="font-size:10px; border-bottom:0px solid #fff">';

	$cellArray = array ();
	array_push($cellArray, array('label' => $obj->lang['description']));
	array_push($cellArray, array('label' => $obj->lang['qty'], 'align' => 'right', 'width' => '40'));
	array_push($cellArray, array('label' => $obj->lang['cost'],'align' => 'right', 'width' => '60'));
	array_push($cellArray, array('label' => $obj->lang['total'],'align' => 'right', 'width' => '70')); 
	$costTable .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '330', 'cell' =>  $cellArray));  

	$costTable .= $generateRefundTable($obj,$rs,$totalRefund);

	$costTable .= '  
		<tr >
			<td colspan="3"></td>
			<td class="subtotal" style="text-align:right;">'.$obj->formatNumber($totalRefund).'</td>
		</tr>';

	$costTable .= '</table>';

	$totalCost += $totalRefund;
}	

	
$sellingTable =  ($hasSellingPriceAccess) ? $generateSellingTable($obj,$rs, $rsDetail, $totalSelling) : '';

    
// COST REIMBURSEMENT 
$reimbursementCostTable = $generateReimbursementCost($obj,$rs,$totalReimburseCost); 
$reimbursementSellingTable = $generateReimbursementSelling($obj,$rs,$totalSellingReimburse); 
    
$containerTable = $generateContainerTable($obj,$rs);

$depotName = (!empty($rs[0]['depotname'])) ? $rs[0]['depotname']  : '-';
$terminalName = (!empty($rs[0]['terminalname'])) ? $rs[0]['terminalname']  : '-';
     
$rsInvoice = $obj->getInvoiceInformation($rs[0]['pkey']);
$invoiceCode = array_column($rsInvoice,'code');
$invoiceCode = implode('<br>',$invoiceCode);
    
// HEADER
$html = $obj->printSetting['defaultStyle'];
$html .= ' 
 <style> 
    .service-name{font-weight: bold}
    .recipient{color:#666; font-style: italic}
    .cost-detail tr td {vertical-align: middle}
 </style>
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($obj->lang['jobOrderSummary']).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2"> 
<tr><td class="header-row-header" style="width:120px">'.ucwords($obj->lang['date']).'</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>  
<tr><td class="header-row-header">'.ucwords($obj->lang['si']).'</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['donumber'] .'</td></tr> 
<tr><td class="header-row-header">'.ucwords($obj->lang['bookingNumber']).'</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['shipmentnumber'] .'</td></tr>  
<tr><td class="header-row-header">'.ucwords($obj->lang['typeOfJob']).'</td><td style="width:10px; text-align:center">:</td><td>'. $rs[0]['cargotype'] .', ' . $rs[0]['categoryname'].'</td></tr>  
<tr><td class="header-row-header">'.ucwords($obj->lang['party']).'</td><td style="width:10px; text-align:center">:</td><td>'. $party .'</td></tr>  
<tr><td class="header-row-header">'.ucwords($obj->lang['invoiceCode']).'</td><td style="width:10px; text-align:center">:</td><td>'. $invoiceCode .'</td></tr>  
</table> 
</td>
<td style="width:370px;">
<table cellpadding="2" >
<tr><td class="header-row-header" style="width:120px">'.ucwords($obj->lang['customer']).'</td><td style="width:10px; text-align:center">:</td><td style="width:240px;">'.$rs[0]['customername'].'</td></tr> 
<tr><td class="header-row-header">'.ucwords($obj->lang['consignee']).'</td><td style="text-align:center">:</td><td>'.$rs[0]['consigneename'].'</td></tr> 
<tr><td class="header-row-header">'.ucwords($obj->lang['stuffing']).'</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['locationname'].'</td></tr> 
<tr><td class="header-row-header">'.ucwords($obj->lang['warehouse']).'</td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['consigneewarehousename'].'</td></tr>   
<tr><td class="header-row-header">'.ucwords($obj->lang['depot']).' / '.ucwords($obj->lang['terminal']).' </td><td style="width:10px; text-align:center">:</td><td>'.$depotName.' / '.$terminalName.'</td></tr>   
<tr><td class="header-row-header">'.ucwords($obj->lang['route']).' </td><td style="width:10px; text-align:center">:</td><td>'.$rs[0]['routefrom'].' - ' .$rs[0]['routeto'].'</td></tr>   
</table>
</td>
</tr>
</table>'; 
      
// DETAIL 
$html .= '    
<table>
<tr>
<td style="width:330px;">'.$costTable.'</td>  
<td style="width:20px;"></td>  
<td style="width:330px;">'.$sellingTable.'</td>  
</tr> 
</table>';
    
// REIMBURSEMENT 
$html .= '    
<table>
<tr>
<td style="width:330px;">'.$reimbursementCostTable.'</td>  
<td style="width:20px;"></td>  
<td style="width:330px;">'.$reimbursementSellingTable.'</td>  
</tr> 
</table>';
        
 
// SUMMARY
if ($hasSellingPriceAccess) {
    $grossProfit = ($totalSelling + $totalSellingReimburse ) - ($totalCost + $totalReimburseCost);
    $color = ($grossProfit <= 0) ? '#C41E3A' : '#568203';
    $html .= '<div style="clear:both"></div>
              <table cellpadding="2">
              <tr><td style="width: 130px;"><strong>Gross Profit Margin</strong></td><td style="width: 120px;">: '.$obj->formatNumber( $obj->calculateGrossProfitMargin($rs[0]['pkey']) ,2).'%</td><td style="width: 300px;"><span style="color: #666; font-size:0.9em"><i>** Gross Profit Margin = Laba Kotor - Pendapatan Penjualan</i></span></td></tr>
              <tr><td><strong>Gross Profit</strong></td><td>: <span style="color:'.$color.'">'.$obj->formatNumber($grossProfit).'</span></td><td><span style="color: #666; font-size:0.9em"><i>** Gross Profit = Total Penjualan - Total Biaya</i></span></td></tr>
              </table> 
               '; 
}
    
$useRealization = $obj->useRealization();
if($useRealization)    
    $html .= '<div style="clear:both"></div><div style="font-style:italic">*) Belum direalisasi</div>';    
    
$html .= '<div style="clear:both"></div>';
$html .= $containerTable; 

$trnotes = (!empty($rs[0]['trnotes'])) ? '<div style="clear:both"></div><strong>'.$obj->lang['note'].' :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trnotes']) : '';     
$html .= '<div>'.$trnotes.'</div>';

  
// FOOTER
$html .= '<div style="clear:both"></div> 
'.$trnotes.'  
<div style="clear:both"></div> 
'; 
return $html; 
    
} ; 

$detailContent = function ($dataset){
    
global $pdf;
$obj = new TruckingServiceOrder();
$security = new Security();
$truckingPurchaseRefund = new TruckingPurchaseRefund();

$hasPurchaseRefundAccess = $security->hasSecurityAccess( $obj->userkey ,$security->getSecurityKey($truckingPurchaseRefund->securityObject),10);
	
//$pdf->AddPage();
    
$rs = $dataset['rs'];    
    
$generateCostTable = function ($obj, $rs,  $title , $outsource = 0, &$totalCost){ 
          
    $html = '';
    $total = 0;
    $useRealization = $obj->useRealization(); 
    
    $rsCost = $obj->getWorkOrderCostDetail($rs[0]['pkey'],$outsource, false); 
    for($i=0;$i<count($rsCost);$i++){
 
        $isRealize = (!$useRealization) ? true : $rsCost[$i]['isrealization']; 
        
        $amount = ($isRealize) ? $rsCost[$i]['amount'] : $rsCost[$i]['requestamount'];   
        $rsCost[$i]['costname'] = ((!$isRealize) ? '* ' : '') . $rsCost[$i]['costname'];
         
        $subtotal =$amount; 
        $total += $subtotal;  
        
        $borderStyle = ($i<count($rsCost) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
        
        $html.= '
        <tr>  
        <td class="'.$borderStyle.'" style="font-weight:bold">'.$title.'</td>  
        <td class="'.$borderStyle.'"><span class="service-name">'.$rsCost[$i]['costname'].'</span></td>   
        <td class="'.$borderStyle.'">'.$rsCost[$i]['recipientname'].'</td>   
        <td class="'.$borderStyle.'">'.$rsCost[$i]['workordercode'].'</td>  
        <td class="'.$borderStyle.'">'.$rsCost[$i]['cashoutcode'].'</td>   
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
        </tr>
        ';
        
        $title = '';

    } 
  
    //  <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
    $totalCost += $total; 
    
    return $html;
    //return ($total == 0) ? '' : $html;
} ; 
 
$generateAdditionalCostTable = function ($obj,$rs,&$totalCost){ 
    
    $id = $rs[0]['pkey'];
    
    $obj = new TruckingServiceOrder();
    $employee = new Employee();
    $rsCost = $obj->getHeaderCost($id);
    
    //$rsPlanner = $employee->getDataRowById($rs[0]['plannerkey']);
    //$plannerName = (!empty($rsPlanner)) ? $rsPlanner[0]['name'] : '';
    
    $html  = '';
    $title = strtoupper($obj->lang['additional']);
    $total = 0; 
    
    $useRealization = $obj->useRealization(); 
    
    for($i=0;$i<count($rsCost);$i++){

        $isRealize = (!$useRealization) ? true : $rsCost[$i]['isrealization']; 
        
        $amount = ($isRealize) ? $rsCost[$i]['amount'] : $rsCost[$i]['requestamount'];   
        $rsCost[$i]['itemname'] = ((!$isRealize) ? '* ' : '') . $rsCost[$i]['itemname'];
        $qty = $rsCost[$i]['qty'];  
        $subtotal = $qty * $amount;
        $total += $subtotal;
         
 
        $borderStyle = ($i<count($rsCost) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
 
        $html .= '
        <tr>  
        <td class="'.$borderStyle.'" style="font-weight:bold">'.$title.'</td>  
        <td class="'.$borderStyle.'"><span class="service-name">'.$rsCost[$i]['itemname'].'</span></td>   
        <td class="'.$borderStyle.'">'.$rsCost[$i]['recipientname'].'</td>   
        <td class="'.$borderStyle.'">'.$rs[0]['code'].'</td>  
        <td class="'.$borderStyle.'">'.$rsCost[$i]['refcashoutcode'].'</td>   
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
        </tr>
        ';
 
        $title = '';
    }
  
    //        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
    
    $totalCost += $total;
    
    return $html;
    //return ($total == 0) ? '' : $html;
} ;   

$generateRefundTable = function ($obj,$rs,&$totalCost){ 
    
    $id = $rs[0]['pkey'];
    
    $obj = new TruckingPurchaseRefund();
    $employee = new Employee();
    $rsCost = $obj->searchData('','',true,' and ' .$obj->tableName.'.statuskey in (2,3) 
											and '.$obj->tableName.'.refjoborderkey = ' . $obj->oDbCon->paramString($id)
							  );
     
    $html  = '';
    $title = strtoupper($obj->lang['purchaseRefund']);
    $total = 0; 
    
    for($i=0;$i<count($rsCost);$i++){
 
        $amount =  $rsCost[$i]['total'];    
        $qty = 1;  
        $subtotal = $qty * $amount;
        $total += $subtotal;
         
        $borderStyle = ($i<count($rsCost) -1 ) ? 'col-border-bottom' : 'last-border-bottom';
 
        $html .= '
        <tr>  
        <td class="'.$borderStyle.'" style="font-weight:bold">'.$title.'</td>  
        <td class="'.$borderStyle.'"></td>   
        <td class="'.$borderStyle.'">'.$rsCost[$i]['suppliername'].'</td>   
        <td class="'.$borderStyle.'">'.$rs[0]['code'].'</td>  
        <td class="'.$borderStyle.'">'.$rsCost[$i]['refcashoutcode'].'</td>   
        <td class="'.$borderStyle.'" style="text-align:right">'.$obj->formatNumber($subtotal).'</td>  
        </tr>
        ';
 
        $title = '';
    }
   
    $totalCost += $total;
    
    return $html; 
} ;   	
	
    
$costTable = $obj->printSetting['defaultStyle'];
$costTable .= ' <style> 
                    .service-name{font-weight: bold}
                    .recipient{color:#666; font-style: italic}
                    .cost-detail tr td {vertical-align: middle}
                 </style>
<table cellpadding="2" > 
<tr><td><div class="title">'.strtoupper($obj->lang['attachment']).'</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> ';

    
$costTable .= ' <br>
    <div style="font-weight:bold; line-height:30px; ">'.strtoupper($obj->lang['cost']).'</div>
    <table cellpadding="4" class="table-transaction cost-detail" style="font-size:10px; border-bottom:0px solid #fff">';
    
    
$cellArray = array ();
array_push($cellArray, array('label' => '', 'width' => '80'));
array_push($cellArray, array('label' => $obj->lang['description']));
array_push($cellArray, array('label' => $obj->lang['recipient'], 'width' => '140'));
array_push($cellArray, array('label' => $obj->lang['transactionCode'],  'width' => '110'));
array_push($cellArray, array('label' => $obj->lang['cash/ap'],  'width' => '110'));
array_push($cellArray, array('label' => $obj->lang['cost'],'align' => 'right', 'width' => '100')); 
//array_push($cellArray, array('label' => $obj->lang['total'],'align' => 'right', 'width' => '80')); 
$costTable .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray));  
       
    
$costTable .= $generateCostTable($obj,$rs,strtoupper($obj->lang['inhouse']),0,$totalCost); 
$costTable .= $generateCostTable($obj,$rs, strtoupper($obj->lang['outsource']), 1,$totalCost); 
$costTable .= $generateAdditionalCostTable($obj,$rs,$totalCost);
	
if($hasPurchaseRefundAccess)	
	$costTable .= $generateRefundTable($obj,$rs,$totalCost);    
	
$costTable .= '  
    <tr >
        <td colspan="5"></td>
        <td class="subtotal" style="text-align:right;">'.$obj->formatNumber($totalCost).'</td>
    </tr>';

$costTable .= '</table>';


$useRealization = $obj->useRealization();
if($useRealization)    
    $costTable .= '<div style="clear:both"></div><div style="font-style:italic">*) Belum direalisasi</div>';
return $costTable;
    
};

$generateReportContent = array();
array_push($generateReportContent , $summaryContent);
array_push($generateReportContent , $detailContent);

?>