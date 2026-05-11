<?php 
$PRINT_SETTINGS =  array(   
'showPrintHeader' => false,
);

includeClass(array('EMKLJobOrder.class.php','EMKLCommission.class.php','CreditNote.class.php','DebitNote.class.php'));
$emklJobOrderImport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['import']));

$obj = $emklJobOrderImport;
 
$generateReportContent = function ($dataset){ 
 
$obj = new EMKLJobOrder(EMKL['jobType']['import']);  
$emklPurchaseOrderImport = new EMKLPurchaseOrder(EMKL['jobType']['import']);  
    
$service = new Service(SERVICE);
$employee = new Employee();
$container = new Container();
$security = new Security();
$currency = new Currency(); 
$emklCommission = new EMKLCommission();
$creditNote = new CreditNote();
$debitNote = new DebitNote();
	
$rsCurrency = $currency->searchData();
$rsCurrency = array_column($rsCurrency,'name','pkey');
    
$rsContainer = $container->searchData();
$rsContainer = array_column($rsContainer,'name','pkey');
    
$rsService = $service->searchData();  
$rsService = array_column($rsService,'name','pkey');
     
$rsFreightTerm = $obj->getFreightTerm();
$rsFreightTerm = array_column($rsFreightTerm,'name','pkey');

$rs = $dataset['rs']; 
$rsInvoiceCol = $obj->getAmountInvoiced($rs[0]['pkey']);
$rsInvoiceCol = $obj->reindexDetailCollections($rsInvoiceCol,'refdetailkey'); 
	
// kalo LCL harus merge dengan anak2nya
if($rs[0]['ismaster'] &&  in_array( $rs[0]['loadcontainertypekey'] , array(EMKL['emklType']['lcl'],EMKL['emklType']['lclnc'])) ) { 
	$rsDetail = $obj->getLCLDetailWithRelatedInformation($rs[0]['pkey']);
}else{ 
	$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
}
    
$arrParty = array();    

$party = '';
if(in_array($rs[0]['loadcontainertypekey'], array(EMKL['container']['fcl'],EMKL['container']['trucking'])) &&  $rs[0]['transportationtypekey'] == EMKL['shipping']['sea']){   
    $arrParty = array();    
    $rsParty = $obj->getDetailVolume($rs[0]['pkey']);
    for($i=0;$i<count($rsParty);$i++) 
         array_push($arrParty,$obj->formatNumber($rsParty[$i]['qty']) . 'x ' . $rsParty[$i]['itemname'] );
    
    $party = implode('<br>',$arrParty);
}else{
//    $rsParty = $obj->getCubicVolume($rs[0]['pkey']);
    
    $temp = array();
    if(!empty($rs[0]['weight'])) array_push($temp, $obj->formatNumber($rs[0]['weight'],2) . ' KG');
    if(!empty($rs[0]['volume'])) array_push($temp, $obj->formatNumber($rs[0]['volume'],2). ' CBM');
    
    $party = implode(', ',$temp);
}
    

// utk cek kalo LCL punya anak
$jobOrderKeys = array($rs[0]['pkey']);
if($rs[0]['ismaster'] && in_array( $rs[0]['loadcontainertypekey'] , array(EMKL['emklType']['lcl'],EMKL['emklType']['lclnc']))) { 
	$rsLCL = $obj->getLCLChild($rs[0]['pkey']);
	$arrDetailPkey = array_column($rsLCL,'pkey'); 
	
	$jobOrderKeys = array_merge($jobOrderKeys, $arrDetailPkey);
}

    $dateReturnOnEmpty = array('returnOnEmpty'=>true, 'value' => '00 / 00 / 0000');
   

$emklPurchaseOrderExportAccess = $security->hasSecurityAccess( $obj->userkey ,$security->getSecurityKey($emklPurchaseOrderImport->securityObject),10);
$emklCommissionAccess = $security->hasSecurityAccess( $obj->userkey ,$security->getSecurityKey($emklCommission->securityObject),10);	
$creditNoteAccess = $security->hasSecurityAccess( $obj->userkey ,$security->getSecurityKey($creditNote->securityObject),10);	
$debitNoteAccess = $security->hasSecurityAccess( $obj->userkey ,$security->getSecurityKey($debitNote->securityObject),10);	
 

$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
$trnotes = (!empty($rs[0]['trdesc'])) ? '<div style="clear:both"></div><strong>'.$obj->lang['note'].' :</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
  
$totalBuying = 0;
$totalRefund = 0;
$totalCN = 0; 
$totalDN = 0; 

$html = $obj->printSetting['defaultStyle'];
$html .= '
<style>
.table-transaction {border-bottom:1px solid #999}
.col-header td{border-bottom:1px solid #999; border-top:1px solid #999; font-weight: bold}
</style>
<table cellpadding="2" > 
<tr><td><div class="title">'.$obj->lang['jobOrderSummary'].' Import</div></td></tr>
<tr><td><div class="subtitle">'.$rs[0]['code'].'</div></td></tr>
</table> 

<div style="clear:both"></div>
 
<table>
<tr>
<td style="width:300px;" >
<table cellpadding="2">
<tr><td class="header-row-header">'.$obj->lang['jobOrder'].'</td><td style="width:10px; text-align:center">:</td><td style="width:170px">'.$rs[0]['code'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['poReference'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['ponumber'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['bookingNumber'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['bookingnumber'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['typeOfJob'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['jobtypeunion'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['consignee'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['customername'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['salesman'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['salesname'].'</td></tr>
<tr><td class="header-row-header">AJU / PIB</td><td style="text-align:center">:</td><td>'.$rs[0]['aju'].' / '.$rs[0]['peb'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['party'].'</td><td style="text-align:center">:</td><td>'.$party.'</td></tr>
</table>
</td>
<td style="width:10px;"></td>
<td style="width:360px;"> 
<table cellpadding="2">
<tr><td class="header-row-header">MBL</td><td style="width:10px; text-align:center">:</td><td style="width:230px">'.$rs[0]['mblnumber'].'</td></tr> 
<tr><td class="header-row-header">POL, ETD</td><td style="text-align:center">:</td><td >'. $rs[0]['polname'] .', '. $obj->formatDBDate($rs[0]['etdpol'],'d / m / Y', $dateReturnOnEmpty) .'</td></tr>
<tr><td class="header-row-header">POD, ETA</td><td style="text-align:center">:</td><td >'. $rs[0]['podname'] .', '. $obj->formatDBDate($rs[0]['etapod'],'d / m / Y', $dateReturnOnEmpty) .'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['carrier'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['carriername'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['agent'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['agentname'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['vessel'].' / '.$obj->lang['voyage'].'</td><td style="text-align:center">:</td><td>'.$rs[0]['vesselname'].' / '.$rs[0]['vesselnumber'].'</td></tr>
<tr><td class="header-row-header">'.$obj->lang['container'].'</td><td style="text-align:center">:</td><td>'.str_replacE(chr(13),'<br>',$rs[0]['containernumber']).'</td></tr>
<tr><td class="header-row-header">Stuffing Location</td><td style="text-align:center">:</td><td>'.$rs[0]['stuffinglocation'].'</td></tr>
<tr><td class="header-row-header">Stuffing</td><td style="text-align:center">:</td><td><strong>In </strong> '. $obj->formatDBDate($rs[0]['stuffingin'],'',array('returnOnEmpty' => true)) .'&nbsp;&nbsp;&nbsp; <strong>Out </strong> '. $obj->formatDBDate($rs[0]['stuffingout'],'',array('returnOnEmpty' => true)) .'</td></tr>
</table>
</td>
</tr>
</table>';
    
$html .= $trnotes;
$html .= '<div></div>';
        
  // BUYING
    $buyingTable = '';
    $buyingTable .= '<div style="clear:both; text-align:left" class="subtitle"><strong>'.strtoupper($obj->lang['purchaseOrder']).'</strong></div>
<br>';      
$tabelItem ='<table cellpadding="2" class="table-transaction">';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '115'));
array_push($cellArray, array('label' => $obj->lang['container'], 'width' => '60'));
array_push($cellArray, array('label' => $obj->lang['qty'],'align' => 'right', 'width' => '55'));
array_push($cellArray, array('label' => '', 'width' => '5'));
array_push($cellArray, array('label' => $obj->lang['description']));
array_push($cellArray, array('label' => $obj->lang['currencyShort'], 'align' => 'center', 'width' => '40'));
array_push($cellArray, array('label' => $obj->lang['price'] , 'align' => 'right', 'width' => '70'));   
array_push($cellArray, array('label' => $obj->lang['currencyRate'] , 'align' => 'right', 'width' => '50'));   
//array_push($cellArray, array('label' => $obj->lang['subtotal'] , 'align' => 'right', 'width' => '70'));   
array_push($cellArray, array('label' => $obj->lang['subtotal'] .' (IDR)' , 'align' => 'right', 'width' => '90'));   

$tabelItem .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  
 

    $rsBuying = array();
    if($emklPurchaseOrderExportAccess){
//$rsBuying = $emklPurchaseOrderImport->searchData($emklPurchaseOrderImport->tableName.'.refkey', $rs[0]['pkey'], true, ' and '.$emklPurchaseOrderImport->tableName.'.statuskey in (1,2,3)');
$rsBuying = $emklPurchaseOrderImport->searchData('','', true, ' and '.$emklPurchaseOrderImport->tableName.'.refkey in ('.$obj->oDbCon->paramString($jobOrderKeys,',').') and '.$emklPurchaseOrderImport->tableName.'.statuskey in (1,2,3)');

foreach($rsBuying as $buyingRow){ 
    $rsDetailBuying = $emklPurchaseOrderImport->getDetailWithRelatedInformation($buyingRow['pkey']);
    $status = ($buyingRow['statuskey'] == 1) ? ' * ' : '';
 
    if(empty($rsDetailBuying)) continue; 
    
    $tabelItem .= '<tr><td colspan="'.count($cellArray).'" style="font-style:italic;font-size: 0.9em"><br><br>'.$status.'<span style="font-weight: bold; ">'.$buyingRow['suppliername'].'</span></td></tr>';
 
    for($i=0;$i<count($rsDetailBuying);$i++){
        
         
        $rate = ($rsDetailBuying[$i]['currencykey'] == CURRENCY['idr'] ) ? 1 : $buyingRow['rate'] ;
        $subtotal =  $rsDetailBuying[$i]['subtotalcurrency'] ;
        
        if ($rsDetailBuying[$i]['currencykey'] <> CURRENCY['idr'] ) 
            $subtotal = $rsDetailBuying[$i]['subtotalcurrency'] * $buyingRow['rate'] ;
        $itemDescription = (!empty($rsDetailBuying[$i]['description'])) ? ', '.$rsDetailBuying[$i]['description'] : '';
        
        $tabelItem .= '<tr>
                            <td>'.$buyingRow['code'].'</td>
                            <td>'.$rsContainer[$rsDetailBuying[$i]['itemkey']].'</td>
                            <td style ="text-align:right">'.$obj->formatNumber($rsDetailBuying[$i]['qty'],-2).'</td>
                            <td></td>
                            <td>'.$rsService[$rsDetailBuying[$i]['servicekey']].$itemDescription.'</td>
                            <td style ="text-align:center">'.$rsCurrency[$rsDetailBuying[$i]['currencykey']].'</td>
                            <td style ="text-align:right">'.$obj->formatNumber($rsDetailBuying[$i]['priceinunit'],-2).'</td> 
                            <td style ="text-align:right">'.$obj->formatNumber($rate,-2).'</td>  
                            <td style ="text-align:right">'.$obj->formatNumber($subtotal).'</td> 
                        </tr>';
        $totalBuying += $subtotal;
        
    }
     
}
    
$tabelItem .= '<tr> <td colspan="'.count($cellArray).'"></td></tr>';
    }else{
        $tabelItem .= '<tr> <td colspan="'.count($cellArray).'" style="text-align:center">'.$obj->lang['noAccess'].'</td></tr>';
    }

$tabelItem .= '</table>';
$tabelItem .= '<table cellpadding="3"><tr><td style="font-style:italic" colspan="'.(count($cellArray)-1).'">*) Pending Approval</td><td style="text-align:right; font-weight: bold">'.$obj->formatNumber($totalBuying).'</td></tr></table>';


    $buyingTable .= $tabelItem; 
    $buyingTable .= '<div></div>'; 
    $html .= $buyingTable;

    // REFUND
    $refundTable = '<div style="clear:both; text-align:left" class="subtitle"><strong>'.strtoupper($obj->lang['purchaseRefund']).'</strong></div>
<br>';                                 
     

$tabelItem ='<table cellpadding="2" class="table-transaction">';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '115')); 
array_push($cellArray, array('label' => $obj->lang['qty'],'align' => 'right', 'width' => '40'));
array_push($cellArray, array('label' => '', 'width' => '5'));
array_push($cellArray, array('label' => $obj->lang['description']));
array_push($cellArray, array('label' => $obj->lang['currencyShort'], 'align' => 'center', 'width' => '40'));
array_push($cellArray, array('label' => $obj->lang['price'] , 'align' => 'right', 'width' => '70'));   
array_push($cellArray, array('label' => $obj->lang['currencyRate'] , 'align' => 'right', 'width' => '50'));  
array_push($cellArray, array('label' => $obj->lang['subtotal'] .' (IDR)' , 'align' => 'right', 'width' => '90'));    
$tabelItem .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  
     
    

//$rsRefund = $emklCommission->searchData($emklCommission->tableName.'.refkey', $rs[0]['pkey'], true, ' and '.$emklCommission->tableName.'.statuskey in (1,2,3)');
$rsRefund = $emklCommission->searchData('','', true, 'and '.$emklCommission->tableName.'.refkey in ('.$obj->oDbCon->paramString($jobOrderKeys,',').')  and '.$emklCommission->tableName.'.statuskey in (1,2,3)');
    if($emklCommissionAccess){
     
foreach($rsRefund as $buyingRow){  
  
    $rsDetailBuying = $emklCommission->getDetailWithRelatedInformation($buyingRow['pkey']);
    $status = ($buyingRow['statuskey'] == 1) ? ' * ' : '';
 
    if(empty($rsDetailBuying)) continue; 
    
    $tabelItem .= '<tr><td colspan="'.count($cellArray).'" style="font-style:italic;font-size: 0.9em"><br><br>'.$status.'<span style="font-weight: bold; ">'.$buyingRow['suppliername'].'</span></td></tr>';
  
     for($i=0;$i<count($rsDetailBuying);$i++){
        
        $rate = ($rsDetailBuying[$i]['currencykey'] == CURRENCY['idr'] ) ? 1 : $buyingRow['rate'] ;
        $subtotal =  $rsDetailBuying[$i]['subtotalcurrency'] ;
         
        if ($rsDetailBuying[$i]['currencykey'] <> CURRENCY['idr'] ) 
            $subtotal = $rsDetailBuying[$i]['subtotalcurrency'] * $buyingRow['rate'] ;
        
        $tabelItem .= '<tr>
                            <td>'.$buyingRow['code'].'</td> 
                            <td style ="text-align:right">'.$obj->formatNumber($rsDetailBuying[$i]['qty'],-2).'</td>
                            <td></td>
                            <td>'.$rsDetailBuying[$i]['description'].'</td>
                            <td style ="text-align:center">'.$rsCurrency[$rsDetailBuying[$i]['currencykey']].'</td>
                            <td style ="text-align:right">'.$obj->formatNumber($rsDetailBuying[$i]['priceinunit'],-2).'</td> 
                            <td style ="text-align:right">'.$obj->formatNumber($rate,-2).'</td>  
                            <td style ="text-align:right">'.$obj->formatNumber($subtotal).'</td> 
                        </tr>';
        $totalRefund += $subtotal;
        
    }
}  
        

        $tabelItem .= '<tr> <td colspan="'.count($cellArray).'"></td></tr>';

    }else{

        $tabelItem .= '<tr> <td colspan="'.count($cellArray).'" style="text-align:center">'.$obj->lang['noAccess'].'</td></tr>';
    }
$tabelItem .= '</table>'; 
$tabelItem .= '<table cellpadding="3"><tr><td style="font-style:italic" colspan="'.(count($cellArray)-1).'">*) Pending Approval</td><td style="text-align:right; font-weight: bold">'.$obj->formatNumber($totalRefund).'</td></tr></table>';


    $refundTable .= $tabelItem; 
    $refundTable .= '<div></div>'; 

    if(!empty($rsRefund))
        $html .= $refundTable;

//CN
	
 
	$cnTable ='<div style="text-align:left" class="subtitle"><strong>'.strtoupper($obj->lang['creditNote']).'</strong></div>
	<br>';


	$tabelItem ='<table cellpadding="2" class="table-transaction">';

	$cellArray = array ();
	array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '115'));  
	array_push($cellArray, array('label' => $obj->lang['JOCode'], 'width' => '115'));  
	array_push($cellArray, array('label' => $obj->lang['invoice']));
	array_push($cellArray, array('label' => $obj->lang['currencyShort'], 'align' => 'center', 'width' => '40'));
	array_push($cellArray, array('label' => $obj->lang['price'] , 'align' => 'right', 'width' => '70'));   
	array_push($cellArray, array('label' => $obj->lang['currencyRate'] , 'align' => 'right', 'width' => '50'));  
	array_push($cellArray, array('label' => $obj->lang['subtotal'] .' (IDR)' , 'align' => 'right', 'width' => '90'));    
	$tabelItem .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  

	// group per CN
	// jgn per customer, agar bisa bedain statusnya
	//$rsCNGroup = $obj->reindexDetailCollections($rsCN,'pkey'); 
		
    $rsCN = array();  
    if($creditNoteAccess){
        
        $rsCN = $creditNote->getCreditNoteByEMKLJO($rs[0]['pkey'],'	and '.$creditNote->tableName.'.statuskey <> 4');
        foreach($rsCN as $key=>$row){

            $status = ($row[0]['statuskey'] == 1) ? ' * ' : '';
            $tabelItem .= '<tr><td colspan="'.count($cellArray).'" style="font-style:italic;font-size: 0.9em"><br><br>'.$status.'<span style="font-weight: bold; ">'.$row[0]['customername'].'</span></td></tr>';

            foreach($row as $detailRow){
                $totalCredit = $detailRow['totalcredit'];
                $rate = $detailRow['rate'];
                $subtotal = $totalCredit * $rate;

                $tabelItem .= '<tr>
                                    <td>'.$detailRow['code'].'</td>   
                                    <td>'.$detailRow['jodetailcode'].'</td>   
                                    <td>'.$detailRow['invoicecode'].'</td>  
                                    <td style ="text-align:center">'.$rsCurrency[$detailRow['currencykey']].'</td>
                                    <td style ="text-align:right">'.$obj->formatNumber($totalCredit,-2).'</td> 
                                    <td style ="text-align:right">'.$obj->formatNumber($rate,-2).'</td> 
                                    <td style ="text-align:right">'.$obj->formatNumber($subtotal).'</td> 
                                </tr>
                                ';
                $totalCN += $subtotal;
		}
	}
        $tabelItem .= '<tr> <td colspan="'.count($cellArray).'"></td></tr>';
    }else{
        $tabelItem .= '<tr> <td colspan="'.count($cellArray).'" style="text-align:center">'.$obj->lang['noAccess'].'</td></tr>';
    }
	
	$tabelItem .= '</table>'; 
	$tabelItem .= '<table cellpadding="3"><tr><td style="font-style:italic" colspan="'.(count($cellArray)-1).'">*) Pending Approval</td><td style="text-align:right; font-weight: bold">'.$obj->formatNumber($totalCN).'</td></tr></table>';

	$cnTable .= $tabelItem; 
	$cnTable .= '<div></div>'; 

    if(!empty($rsCN))
        $html .= $cnTable;


// DN    
 
$dnTable ='<div style="text-align:left" class="subtitle"><strong>'.strtoupper($obj->lang['debitNote']).'</strong></div>
<br>';

$tabelItem ='<table cellpadding="2" class="table-transaction">';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '115'));  
array_push($cellArray, array('label' => $obj->lang['JOCode'], 'width' => '115'));  
array_push($cellArray, array('label' => $obj->lang['purchase']));
array_push($cellArray, array('label' => $obj->lang['currencyShort'], 'align' => 'center', 'width' => '40'));
array_push($cellArray, array('label' => $obj->lang['price'] , 'align' => 'right', 'width' => '70'));   
array_push($cellArray, array('label' => $obj->lang['currencyRate'] , 'align' => 'right', 'width' => '50'));  
array_push($cellArray, array('label' => $obj->lang['subtotal'] .' (IDR)' , 'align' => 'right', 'width' => '90'));    
$tabelItem .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  

// group per CN
// jgn per customer, agar bisa bedain statusnya

$rsDN = array();
if($debitNoteAccess){
    $rsDN = $debitNote->getSourceTransaction($rs[0]['pkey'],array(1,2,3)); 
    $rsDN = $obj->reindexDetailCollections($rsDN,'debitnotekey'); 

    foreach($rsDN as $key=>$row){

        $status = ($row[0]['debitnotestatuskey'] == 1) ? ' * ' : '';
        $tabelItem .= '<tr><td colspan="'.count($cellArray).'" style="font-style:italic;font-size: 0.9em"><br><br>'.$status.'<span style="font-weight: bold; ">'.$row[0]['suppliername'].'</span></td></tr>';

        foreach($row as  $detailRow){

            $totalDebit = $detailRow['totaldebit'];
            $rate = $detailRow['rate'];
            $subtotal = $totalDebit * $rate;

            $tabelItem .= '<tr>
                                <td>'.$detailRow['debitnotecode'].'</td>   
                                <td>'.$detailRow['socode'].'</td>   
                                <td>'.$detailRow['pocode'].'</td>  
                                <td style ="text-align:center">'.$rsCurrency[$detailRow['currencykey']].'</td>
                                <td style ="text-align:right">'.$obj->formatNumber($totalDebit,-2).'</td> 
                                <td style ="text-align:right">'.$obj->formatNumber($rate,-2).'</td> 
                                <td style ="text-align:right">'.$obj->formatNumber($subtotal).'</td> 
                            </tr>
                            ';
            $totalDN += $subtotal;
        }
    }

    $tabelItem .= '<tr> <td colspan="'.count($cellArray).'"></td></tr>';
}else{
    $tabelItem .= '<tr> <td colspan="'.count($cellArray).'" style="text-align:center">'.$obj->lang['noAccess'].'</td></tr>';
}


$tabelItem .= '</table>'; 
$tabelItem .= '<table cellpadding="3"><tr><td style="font-style:italic" colspan="'.(count($cellArray)-1).'">*) Pending Approval</td><td style="text-align:right; font-weight: bold">'.$obj->formatNumber($totalDN).'</td></tr></table>';

$dnTable .= $tabelItem; 
$dnTable .= '<div></div>'; 

if(!empty($rsDN))
    $html .= $dnTable;
	
        
	$html .='<div style="clear:both; text-align:left" class="subtitle"><strong>'.strtoupper($obj->lang['selling']).'</strong></div>
<br>';                                 
     

$tabelItem ='<table cellpadding="2" class="table-transaction">';

$cellArray = array ();
array_push($cellArray, array('label' => $obj->lang['code'], 'width' => '115'));
array_push($cellArray, array('label' => $obj->lang['container'], 'width' => '60'));
array_push($cellArray, array('label' => $obj->lang['qty'],'align' => 'right', 'width' => '55'));
array_push($cellArray, array('label' => '', 'width' => '5'));
array_push($cellArray, array('label' => $obj->lang['description']));
array_push($cellArray, array('label' => $obj->lang['currencyShort'], 'align' => 'center', 'width' => '40'));
array_push($cellArray, array('label' => $obj->lang['price'] , 'align' => 'right', 'width' => '70'));   
array_push($cellArray, array('label' => $obj->lang['currencyRate'] , 'align' => 'right', 'width' => '50'));  
array_push($cellArray, array('label' => $obj->lang['subtotal'] .' (IDR)' , 'align' => 'right', 'width' => '90'));    
$tabelItem .= $obj->generatePrintTableRow( array('class' => 'col-header','docWidth' => '670', 'cell' =>  $cellArray));  
    
    
$totalSelling = 0;
for($i=0;$i<count($rsDetail);$i++){
    
    $rsItemDetail = $obj->getItemDetail($rsDetail[$i]['pkey']); 
    if(empty($rsItemDetail)) continue;

	$rsInvoice = (isset( $rsInvoiceCol[$rsDetail[$i]['pkey']] )) ? $rsInvoiceCol[$rsDetail[$i]['pkey']] : array();
	
	$invoiceList = '';
	if(!empty($rsInvoice)){
		$invoiceList = implode(', ',array_column($rsInvoice,'code'));
		$invoiceList = '<div class="invoice-list">'. $invoiceList.'</div>'; 
	}
	
    $note = (!empty($rsDetail[$i]['description'])) ? '<br>'.str_replace(chr(13),'<br>',$rsDetail[$i]['description']) : '';
    $tabelItem .= '<tr><td colspan="'.count($cellArray).'" style="font-style:italic;font-size: 0.9em"><br><br><span style="font-weight: bold; ">'.$rsDetail[$i]['customername'].'</span>'.$invoiceList.$note.'</td></tr>';

    for($j=0;$j<count($rsItemDetail);$j++){ 
         
        $rate = ($rsItemDetail[$j]['currencykey'] == CURRENCY['idr'] ) ? 1 : $rsDetail[$i]['rate'] ;
               //item name, kalau ada alias di itemdetail tampil alias
        $itemName = (empty($rsItemDetail[$j]['alias']) ? $rsService[$rsItemDetail[$j]['servicekey']] : $rsItemDetail[$j]['alias']);
         
        $tabelItem .= '<tr>
                            <td>'.$rsDetail[$i]['code'].'</td>
                            <td>'.$rsContainer[$rsItemDetail[$j]['itemkey']].'</td>
                            <td style ="text-align:right">'.$obj->formatNumber($rsItemDetail[$j]['qty'],-2).'</td>
                            <td></td>
                            <td>'.$itemName.'</td>
                            <td style ="text-align:center">'.$rsCurrency[$rsItemDetail[$j]['currencykey']].'</td>
                            <td style ="text-align:right">'.$obj->formatNumber($rsItemDetail[$j]['priceinunit'],-2).'</td> 
                            <td style ="text-align:right">'.$obj->formatNumber($rate,-2).'</td> 
                            <td style ="text-align:right">'.$obj->formatNumber($rsItemDetail[$j]['subtotal']).'</td> 
                        </tr>
                        ';
        $totalSelling += $rsItemDetail[$j]['subtotal'];
    }
}  
        
    
$tabelItem .= '<tr> <td colspan="'.count($cellArray).'"></td></tr>';
$tabelItem .= '</table>';
    
$balance = $totalSelling - $totalBuying - $totalRefund - $totalCN + $totalDN;

if($balance > 0)  $color = 'color:#568203' ;
else if ($balance < 0)    $color = 'color:#C41E3A' ;
else $color = '';   
    
$tabelItem .= '<table cellpadding="3">
                    <tr><td  colspan="'.(count($cellArray)-1).'"></td><td style="text-align:right; font-weight: bold">'.$obj->formatNumber($totalSelling).'</td></tr>
                    <tr><td style="font-weight: bold; text-align:right;" colspan="'.count($cellArray).'"></td></tr>
                    <tr ><td style="font-weight: bold; text-align:right;'.$color.'" colspan="'.(count($cellArray)-1).'">Balance</td><td style="text-align:right; font-weight: bold;'.$color.'">'.$obj->formatNumber($balance).'</td></tr>
                </table>';

$html .= $tabelItem; 
    
$html  = '<div style="font-size:0.9em">'.$html.'</div>';    
return $html;
}

?>
