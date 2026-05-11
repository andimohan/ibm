<?php 
    
$pdf->setCustomSettings(
    array( 
         'marginFooter' => '14',  
         'logoSize' => '60,24',  
         'headerAlign' => 'right',   
         'footer' => '<div style="clear:both; border-bottom:1px solid #000; width: 670px;"></div><br><i>IN CASE OF ANY DISCREPANCIES, PLEASE NOTIFY US WITHIN SEVEN (7) DAYS UPON RECEIVING OF THIS INVOICE</i>',   
    ) 
); 

$generateReportContent = function ($dataset){ 
  
    $obj = new TruckingServiceOrderInvoice();  
    $truckingServiceOrder = new TruckingServiceOrder();    
    $truckingServiceOrderCategory = new TruckingServiceOrderCategory();    
    $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
    $customer = new Customer();
    $consignee = new Consignee();
    $cost = new Service(TRUCKING_SERVICE,1);
    $customCode = new CustomCode();
    $termOfPayment = new TermOfPayment(); 
    
    $rs = $dataset['rs']; 

    $rsInvoiceType = $customCode->searchData($customCode->tableName.'.pkey',$rs[0]['customcodekey'], true);
    $rsDownpayment = $obj->getDownpaymentDetail($rs[0]['pkey']); 
 
    
    $rsDetail = $obj->getDetailById($rs[0]['pkey']);
    $rsDetailPayment = $obj->getDataRowById($rs[0]['pkey']);
    $sayNumber =  $obj->sayNumberInEnglish($rs[0]['beforetaxtotal'] + $rs[0]['stampfee']); //$obj->sayNumberInEnglish($rs[0]['grandtotal']);
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    $rsTop = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']);

    if(!empty($rsDetail[0]['salesorderkey'])){
        $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[0]['salesorderkey']);  
        $rsConsignee = $consignee->getDataRowById($rsSOHeader[0]['consigneekey']);  
      }    

    $reference = '';
 /*   if($rs[0]['customcodekey'] == 2 && !empty($rsConsignee)){ 
        $customerName = $rsConsignee[0]['name'] ;
        $customerAddress = str_replace(chr(13),'<br>',$rsConsignee[0]['address']);
    }else{
        $customerName = $rsCustomer[0]['name'] ;
        $customerAddress = str_replace(chr(13),'<br>',$rsCustomer[0]['address']);
        $reference = $rsConsignee[0]['name'] ;
    }*/
    
    if($rs[0]['invoiceto'] == 1){
       $customerName = $rsCustomer[0]['name'];
       $customerAddress =  str_replace(chr(13),'<br>',$rsCustomer[0]['address']);
       $reference = $rsConsignee[0]['name'] ;
    }else{ 
        $customerName = $rs[0]['invoiceconsigneename']; //$rsConsignee[0]['name'] ;
        $customerAddress = nl2br($rs[0]['invoiceconsigneeaddress']);  //str_replace(chr(13),'<br>',$rsConsignee[0]['address']);
    }


    $html = $obj->printSetting['defaultStyle'];
    $html .= ' 
    <div style="font-size: 0.9em">
    <table cellpadding="2" > 
    <tr><td><div class="title" style="text-decoration:underline; font-size:1.8em">'.strtoupper($rsInvoiceType[0]['name']).'</div></td></tr>
    <tr><td style="font-weight:bold;"><div class="subtitle" style="font-size:1.2em">'.$rs[0]['code'].'</div></td></tr>
    </table>  
    <div style="clear:both"></div>
    <table> 
    <tr>
    <td style="width:380px">
    <table cellpadding="2"> 
    <tr><td style="font-weight:bold;width:380px;">TO</td></tr> 
    <tr><td style="font-weight:bold;" >'. $customerName.'<br style="font-weight:bold;">'.$customerAddress.'</td></tr> 
    ';

    //if(!empty($reference))

    if($rs[0]['customcodekey'] <> 2){
		
		if(isset($_GET['noReference']) && $_GET['noReference'] == 1){ 
			
		}else{ 
        	$html .='<tr><td style="font-weight:bold;">Reference : '.$reference.'</td></tr>';
		}
    }
	
    $html .='</table>
    </td>
    <td style="width:30px;"></td>
    <td style="width:260px;">
    <table cellpadding="2"> 
    <tr><td class="header-row-header" style="width:120px">Date</td><td style="width:10px; text-align:center">:</td><td style="width:140px;">'.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td></tr>
    <tr><td class="header-row-header">Term of Payment</td><td>:</td><td>'.$rsTop[0]['name'].'</td></tr>
    <tr><td class="header-row-header">Your Reference</td><td>:</td><td>'.$rsSOHeader[0]['poreference'].'</td></tr>
    </table>
    </td>
    </tr>
    </table>';

    $html .='<div style="clear:both"></div>
    <table cellpadding="2" class="table-transaction" style="border:1px solid #000;">
    <tr><td style="text-align:center;font-weight:bold;width:30px; border:1px solid #000">No.</td><td style="text-align:center;font-weight:bold;width:90px; border:1px solid #000">JO</td><td style="text-align:center;font-weight:bold;width:170px; border:1px solid #000">DESCRIPTION</td><td style="text-align:center;font-weight:bold;width:100px; border:1px solid #000">NO BOOKING</td><td style="text-align:center;font-weight:bold;width:100px; border:1px solid #000">ACT DATE</td><td style="text-align:center;font-weight:bold;width:90px; border:1px solid #000">UNIT PRICE</td><td style="text-align:center;font-weight:bold;width:90px; border:1px solid #000">AMOUNT</td></tr>  
    ';


    $color = '#000';

    for($i=0;$i<count($rsDetail);$i++){ 

        $itemname = '';
        $containerDetail = '';
        $serviceJO = '';
        $trdate = '';

        $rsWO = array();
        $rsCost = array();
        $rsSOCategory = array();
        $rsConsignee = array();
        $rsSOHeader = array(); 

        $description =  array();

        if (!empty($rsDetail[$i]['salesorderkey'])){ 

            $rsSOHeader = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);   
            $rsSOCategory = $truckingServiceOrderCategory->getDataRowById($rsSOHeader[0]['categorykey']);  
            $rsConsignee = $consignee->getDataRowById($rsSOHeader[0]['consigneekey']);  
            $rsInvoiceItemDetail = $obj->getItemDetail($rsDetail[$i]['pkey'],'refkey',' order by '.$obj->tableItem.'.servicecost asc, '. $obj->tableNameItemDetail. '.pkey asc');   
            
            $arrItemKey = array_column($rsInvoiceItemDetail,'itemkey');
            
            $rsItemCol = $cost->searchDataRow(array($cost->tableName.'.pkey',$cost->tableName.'.name',$cost->tableName.'.aliasname'), ' and '.$cost->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrItemKey,',').')');
            
            $rsItemCol = array_column($rsItemCol,null,'pkey');
                  
            $rsWO = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey',$rsSOHeader[0]['pkey'],true,' and '.$truckingServiceWorkOrder->tableName.'.statuskey in (2,3) ', 'order by '.$truckingServiceWorkOrder->tableName.'.stuffingdatetime asc');

            $salesCode = $rsSOHeader[0]['code'];  
            array_push($description,$rsSOCategory[0]['name']);   
            $amount = '';
            
            $actDate = $rsSOHeader[0]['trdate']; 
            
            if (!empty($rsWO)){ 
                $arrContainer = array(); 
                $containerDetail = '';
                $actDate = $rsWO[count($rsWO)-1]['stuffingdatetime'];
                for($k=0;$k<count($rsWO);$k++){

                        $arrSeal = array();

                        $rsWO[$k]['containernumber'] = str_replace(' ','',$rsWO[$k]['containernumber']);
                        if (!empty($rsWO[$k]['containernumber']) && !in_array($rsWO[$k]['containernumber'],$arrContainer ))
                        array_push($arrContainer,$rsWO[$k]['containernumber']);

                        $rsWO[$k]['container2number'] = str_replace(' ','',$rsWO[$k]['container2number']);
                        if (!empty($rsWO[$k]['container2number']) && !in_array($rsWO[$k]['container2number'],$arrContainer ))
                        array_push($arrContainer,$rsWO[$k]['container2number']);

                }

                $containerDetail .= '<tr><td></td><td style ="font-weight:bold ; border:1px solid #000" colspan="4">CONTAINER NO : <br style ="font-weight:normal">'.implode(', ',$arrContainer).'</td><td style="border-right:1px solid #000;"></td><td></td></tr>';

            }

            if (!empty($rsInvoiceItemDetail)){
                $serviceJO = '';
                for($j=0;$j<count($rsInvoiceItemDetail);$j++){
                    $detailDesc = array();
                    $itemname = '';
                    
                    $rsItem = $rsItemCol[$rsInvoiceItemDetail[$j]['itemkey']];
                    $itemname = (!empty($rsItem['aliasname'])) ? $rsItem['aliasname'] : $rsInvoiceItemDetail[$j]['itemname'];
                    $itemname = (!empty($rsInvoiceItemDetail[$j]['aliasname'])) ? $rsInvoiceItemDetail[$j]['aliasname'] : $itemname;
                   
                    if($rsInvoiceItemDetail[$j]['servicecost'] == 1){
                  
                        array_push($detailDesc,$itemname); 
                        $itemname = $obj->formatNumber($rsInvoiceItemDetail[$j]['qtyinbaseunit']);
                    } else {
        
                         array_push($detailDesc,$itemname.'. '.$rsDetail[$i]['description']); 
                         $itemname = $obj->formatNumber($rsInvoiceItemDetail[$j]['qtyinbaseunit']);
                    }
                    
                    $serviceJO .= '<tr><td style="border-right:1px solis #000"></td><td style="text-align:center;  border-right:1px solid #000; ">'.$itemname.'</td><td colspan="3" style="border-right:1px solid #000;">'.implode('<br>',$detailDesc).'</td><td style="text-align:right; border-right:1px solid #000;">'.$obj->formatNumber($rsInvoiceItemDetail[$j]['priceinunit']).'</td><td style="text-align:right;font-weight:bold">'.$obj->formatNumber($rsInvoiceItemDetail[$j]['total']).'</td></tr>'; 
                }  
            }

        } else { 
            $rsCost = $cost->getDataRowById($rsDetail[$i]['itemkey']);
            $salesCode = $rsCost[0]['name'];
            array_push($description,$rsDetail[$i]['description']);   
            $amount = $obj->formatNumber($rsDetail[$i]['amount']);
        }
 
        $html .= '<tr><td style="text-align:right; font-weight:bold; border-right:1px solid #000;  ">'.($i+1).'.</td><td style="font-weight:bold; text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;">'.$salesCode.'</td><td style="font-weight:bold;border-bottom:1px solid #000;border-right:1px solid #000;">'.implode('<br>',$description).'</td><td style="font-weight:bold;text-align:center;border-bottom:1px solid #000;border-right:1px solid #000;">'.$rsSOHeader[0]['shipmentnumber'].'</td><td style="text-align:center; border-right:1px solid #000;border-bottom:1px solid #000;">'.$obj->formatDBDate($actDate,'d / m / Y', array('returnOnEmpty'=>true)).'</td><td style ="border-right:1px solid #000; text-align:right">'.$amount.'</td><td style="text-align:right; font-weight: bold;">'.$amount.'</td></tr>';
        $html .= (!empty($serviceJO)) ? $serviceJO : '';
        $html .= (!empty($containerDetail)) ? $containerDetail : '';

    } 
        $rowspan = 0;
        $discount = '';
        $downpayment = '';
        $outstanding = '';
    
        $arrSubtotalRows = array();
        
        if($rs[0]['finaldiscount'] <> 0){ 
            $discVal = ($rs[0]['finaldiscounttype'] == 1) ?  $rs[0]['finaldiscount']  : $rs[0]['finaldiscount'] /100 * $rs[0]['subtotal']; 
            $rowspan += 2;  
            array_push($arrSubtotalRows, '<td style="text-align:center; font-weight:bold; border:1px solid #000">SUBTOTAL</td><td style="text-align:right; font-weight:bold; border:1px solid #000">'.$obj->formatNumber($rs[0]['subtotal']).'</td>');
            array_push($arrSubtotalRows, '<td style="text-align:center; font-weight:bold; border:1px solid #000">DISCOUNT</td><td style="text-align:right; font-weight:bold; border:1px solid #000">'.$obj->formatNumber($discVal * -1).'</td>');
        }
    
		// ppn
//		if($rs[0]['taxvalue'] > 0){
//			$rowspan += 2;   
//			array_push($arrSubtotalRows, '<td style="text-align:center; font-weight:bold; border:1px solid #000">DPP</td><td style="text-align:right; font-weight:bold; border:1px solid #000">'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td>');
//			array_push($arrSubtotalRows, '<td style="text-align:center; font-weight:bold; border:1px solid #000">PPN '.$obj->formatNumber($rs[0]['taxpercentage'],-2).'%</td><td style="text-align:right; font-weight:bold; border:1px solid #000">'.$obj->formatNumber($rs[0]['taxvalue']).'</td>');
//    	}
	
    
        if($rs[0]['stampfee'] <> 0){
            array_push($arrSubtotalRows, '<td style="text-align:center; font-weight:bold; border:1px solid #000">SUBTOTAL</td><td style="text-align:right; font-weight:bold; border:1px solid #000">'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</td>');
            array_push($arrSubtotalRows, '<td style="text-align:center; font-weight:bold; border:1px solid #000">MATERAI</td><td style="text-align:right; font-weight:bold; border:1px solid #000">'.$obj->formatNumber($rs[0]['stampfee']).'</td>');
            $rowspan += 2;  
        }
    
        // total
	    // grandtotal diganti DPP
    
        array_push($arrSubtotalRows, '<td style="text-align:center; font-weight:bold; border:1px solid #000">TOTAL</td><td style="text-align:right; font-weight:bold; border:1px solid #000">'.$obj->formatNumber($rs[0]['beforetaxtotal'] + $rs[0]['stampfee']).'</td>');
        
        if($rs[0]['totaldownpayment'] <> 0){
            
            //$outstanding = $rs[0]['grandtotal'] - $rs[0]['totaldownpayment']; 
			$outstanding = $rs[0]['beforetaxtotal'] - $rs[0]['totaldownpayment'] +  $rs[0]['stampfee']; 
            $sayNumber =  $obj->sayNumberInEnglish($outstanding);
            
            $rowspan += 2; 
            array_push($arrSubtotalRows, '<td style="text-align:center; font-weight:bold; border:1px solid #000">DOWNPAYMENT</td><td style="text-align:right; font-weight:bold; border:1px solid #000">'.$obj->formatNumber($rs[0]['totaldownpayment'] * -1).'</td>');
            array_push($arrSubtotalRows, '<td style="text-align:center; font-weight:bold; border:1px solid #000">OUTSTANDING</td><td style="text-align:right; font-weight:bold; border:1px solid #000">'.$obj->formatNumber($outstanding).'</td>');
     
        }

        $subtotalRows = '';       
        for($i=0;$i<count($arrSubtotalRows);$i++){
            $rowspanCol = ($rowspan > 0) ? 'rowspan="'.($rowspan+1).'"' : ''; 
            $rowspanCol = ($i == 0) ? '<td style="border:1px solid #000" colspan="5" '.$rowspanCol.'></td>': '';  // tetep perlu colspannya
            $subtotalRows .= '<tr>'.$rowspanCol.$arrSubtotalRows[$i].'</tr>';

        }
     
    $html .= $subtotalRows;
    
    //$html .= $discount; 
    //$html .= $total; 
    //$html .= '<tr><td style="border:1px solid #000" colspan = "5" rowspan="'.$rowspan.'"></td><td style="text-align:center; font-weight:bold; border:1px solid #000">TOTAL</td><td style="text-align:right; font-weight:bold; border:1px solid #000">'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>';    
    //$html .= $downpayment;
    //$html .= $outstanding;
 

    $html .= '<tr><td colspan="2" style="font-weight:bold;">Said Amount :</td><td colspan="5" style="font-weight:bold;">'.ucwords($sayNumber).'</td></tr>'; 
    $html .= '</table>';

	$ppnNote = ($rs[0]['taxvalue'] > 0) ? '<br><br><table cellpadding="4" style="border:1px solid #333"><tr><td>
	<ol><li>PPN dibebaskan sesuai PP No. 146 Tahun 2000 sebagaimana telah diubah dengan PP No. 38 Tahun 2003.</li>
<li>Revisi e-faktur maksimal adalah tanggal 5 di bulan berikutnya.</li>
<li>Invoice sudah mengenakan BEA MATERAI sebagaimana diatur dalam undang-undang No.10 tahun 2020.</li>
</ol>
	</td></tr></table>': '';
	
    $html .= ' 
    <table> 
    <tr><td style="width:450px;">'.$ppnNote.'</td> 
    <td  style="width:220px;">
    <table>  
    <tr><td style="height:100px"></td></tr> 
    <tr><td style="text-align:center;"> AUTHORIZED SIGNATURE </td></tr> 
    </table>
    </td> 
    </tr>
    </table> 
    <div style="clear:both"></div>
    ';

    $html .= '
    <div style="clear:both"><u>PLEASE TRANSFER ALL PAYMENT TO :</u></div>
    <br>
    <table>
    <tr><td style="font-weight:bold;width:100px;">BANK</td><td style="font-weight:bold;width:10px;">:</td><td style="font-weight:bold;width:560;">BANK CENTRAL ASIA(BCA)</td></tr>
    <tr><td style="font-weight:bold;">ACC NO</td><td style="font-weight:bold;">:</td><td style="font-weight:bold;">8710 1818 20</td></tr>
    <tr><td style="font-weight:bold;">UNDERSIGN</td><td style="font-weight:bold;;">:</td><td style="font-weight:bold;">PT. ELANG TRANSPORTASI INDONESIA</td></tr>
    <tr><td style="font-weight:bold;">BRANCH</td><td style="font-weight:bold;">:</td><td style="font-weight:bold;">KCP GADING RIVERA</td></tr>
    <tr><td style="font-weight:bold;">SWIFT CODE</td><td style="font-weight:bold;">:</td><td style="font-weight:bold;">CENAIDJA</td></tr>
    </table> 
    </div>
    ';
    
    return $html;
};
 
?>
