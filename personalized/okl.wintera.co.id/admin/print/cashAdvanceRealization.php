<?php 

$borderTop = 'border-top:1px solid black;';
$borderLeft = 'border-left:1px solid black;';
$borderRight = 'border-right:1px solid black;';
$borderBottom = 'border-bottom:1px solid black;';

//Kolom ttd 
$signTable = ' 
<div></div>
<table cellpadding="3" style="'.$borderLeft.$borderRight.$borderBottom.'text-align:center;font-weight:bold">
<tr><td style="width:100px;border:1px solid black;">Direksi</td><td style="width:110px;border:1px solid black;">Kabag Keu/Acc</td><td style="width:120px;border:1px solid black;">Kabag</td><td style="width:120px;border:1px solid black;">Acounting</td><td  style="width:120px;border:1px solid black;">Kasir</td><td style="width:110px;border:1px solid black;">Penerima</td></tr>
<tr><td style="width:100px;'.$borderRight.'"></td><td style="width:110px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td></tr>
<tr><td style="width:100px;'.$borderRight.'"></td><td style="width:110px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td></tr>
<tr><td style="width:100px;'.$borderRight.'"></td><td style="width:110px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td><td style="width:120px;'.$borderRight.'"></td></tr>
</table>';    


$pdf->setCustomSettings(
    array( 
         'paperSetting' => 'A5,L',
         'showPrintHeader' => false, 
		 'marginFooter' => '25',
         'footer' => $signTable,  
         ) 
);

$generateReportContent = function ($dataset){ 
    
global $pdf;

$obj = new CashAdvanceRealization();  
$cashAdvance = new CashAdvance();
$employee = new Employee();
$warehouse = new Warehouse(); 
$chartOfAccount = new ChartOfAccount();    
$emklJobOrder = new EMKLJobOrder();    
$emklJobOrderHeader = new EMKLJobOrderHeader();    
$customer = new Customer();    
$container = new Container();  

	
$rs = $dataset['rs'];  

    
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey'],' order by cashtypekey asc'); 
$rsCash = $obj->getDetailCashAdvance($rs[0]['pkey']);

$arrEmployee = array();    
foreach($rsCash as $row => $val)
    array_push($arrEmployee,$val['employeename']);
    
$rsWarehouse = $warehouse->getDataRowById($rs[0]['warehousekey']); 
$rsCOA = $chartOfAccount->getDataRowById($rs[0]['coakey']);
$rsEmployee = $employee->getDataRowById($rsCash[0]['employeekey']); 
$coakey = '';	
$borderTop = 'border-top:1px solid black;';
$borderLeft = 'border-left:1px solid black;';
$borderRight = 'border-right:1px solid black;';
$borderBottom = 'border-bottom:1px solid black;';
$rsCOAEmployee = $chartOfAccount->getDataRowById($rs[0]['cashadvancecoakey']);
	$profileImg = $obj->loadSetting('companyLogo'); 
$img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=220&h=110&hash='.getPHPThumbHash($profileImg);

$recipientName = implode(',',$arrEmployee);	
//$trnotes = (!empty($rs[0]['note'])) ? '<div style="clear:both"></div><strong>'.$obj->lang['note'].'</strong> <br>' . str_replace(chr(13),'<br>',$rs[0]['trdesc']) : '';
/*$arrCashType = array();
$arrCashType[1] = $obj->lang['jobOrder'];
$arrCashType[2] = $obj->lang['downpayment'];
$arrCashType[3] = $obj->lang['cost'];*/
	
$html = $obj->printSetting['defaultStyle'];
//    $html .= ' 
//<table cellpadding="2" style="'.$borderTop.$borderRight.$borderLeft.'width:680px"> 
//<tr><td colspan="3" style="font-size:3.5em;font-family:arial">&nbsp;&nbsp;&nbsp;OKL</td></tr>
//<tr><td></td><td><div class="title">BUKTI KAS (CA)</div></td></tr>
//<tr><td style="width:210px"></td><td style="width:160px;font-size:1.2em"><b>Tgl.</b> '.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td><td style="width:150px;font-size:1.2em"><b>No: '.$rs[0]['code'].' </b></td></tr>
//<tr><td style="width:230px"></td><td style="width:100px;font-size:1.2em"></td><td style="width:150px;font-size:1.2em"></td></tr>
//</table> 
//
//
//';
$html .= '
<table style="'.$borderTop.$borderRight.$borderLeft.'width:680px">
    <tr>
        <td  style="width:170px">
        <table cellpadding="3"> 
            <tr>
                <td style="vertical-align:middle; width:180px;font-size:2.4em;font-weight:bold;font-family:Arial Black;font-style:italic" >OKATRANS</td>
            </tr>
        </table>
        </td>
        <td style="width:280px">
            <table cellpadding="2" style="text-align:left;"> 
            <tr><td></td></tr>
            <tr><td style="width:40px;"></td><td style="text-algin:center"><div class="title">BUKTI KAS (BS)</div></td></tr>
            <tr><td style="width:200px;font-size:1.2em"><b>Tgl.</b> '.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td><td style="width:190px;font-size:1.2em"><b>No: '.$rs[0]['code'].' </b></td></tr>
            </table> 
        </td>
        <td style="width:229.9px">

        </td>
    </tr> 
    <tr><td></td></tr>
</table>
';
    
$html .= '
<table cellpadding="2" style="'.$borderRight.$borderLeft.'">
<tr><td style="width:30px"></td><td class="header-row-header" style="font-size:1.2em;">Dibayar kepada :</td><td style="width:530px;font-size:1.2em;">'.$recipientName.'</td></tr> 
</table>   ';

$html .= '<table cellpadding="3" style="'.$borderLeft.$borderRight.'">
<tr class="col-header" ><td style="'.$borderRight.'width:310px;">Keterangan</td><td style="'.$borderRight.'width:90px;" >No. Order</td><td style="'.$borderRight.'width:90px;" >Customer</td><td style="'.$borderRight.'width:80px;text-align:center" >Partai</td><td style="text-align:right; width:110px;">Jumlah</td></tr>';

 
for($i=0;$i<count($rsDetail);$i++){ 
	$detailDesc ='';
	$invoiceReference ='';
	$customerName ='';
	$inc = ($rsDetail[$i]['ispriceincludetax']) ? 'Yes' : 'No';
	$serviceName = (!empty($rsDetail[$i]['servicename'])) ? $rsDetail[$i]['servicename']:'';
	$supplierName = (!empty($rsDetail[$i]['suppliername'])) ? $rsDetail[$i]['suppliername']:'';
	 
	if($rsDetail[$i]['cashtypekey']==1){
		//$detailDesc = $rsDetail[$i]['jobordercode'].' - '.$rsDetail[$i]['containername'];
        $rsJobOrder = $emklJobOrder->searchDataRow(array($emklJobOrder->tableName.'.pkey',$emklJobOrder->tableName.'.customerkey',$emklJobOrder->tableName.'.loadcontainertypekey',$emklJobOrder->tableName.'.weight',$emklJobOrder->tableName.'.volume' ),
                                                    ' and '.$emklJobOrder->tableName.'.pkey = '.$obj->oDbCon->paramString($rsDetail[$i]['joborderkey'])
                                                  );
        
		$rsDetailVolume = $emklJobOrder->getDetailVolume($rsJobOrder[0]['pkey']);
		
        if($rsJobOrder[0]['loadcontainertypekey'] != 5){
            $arrParty = array();
            foreach($rsDetailVolume as $volumeRow)
                  array_push($arrParty,$obj->formatNumber($volumeRow['qty']).' x '.$volumeRow['itemname']);

            $party = implode('<br>',$arrParty);
        }else{
            
             $temp = array();
            if(!empty($rsJobOrder[0]['volume'])) array_push($temp, $obj->formatNumber($rsJobOrder[0]['volume'],2). ' CBM');
            if(!empty($rsJobOrder[0]['weight'])) array_push($temp, $obj->formatNumber($rsJobOrder[0]['weight'],2) . ' KG');


            $party = implode('<br> ',$temp);

        }
        
        
        $rsCustomer = $customer->getDataRowById($rsJobOrder[0]['customerkey']); 
            
        $rsContainer = $container->searchData();
        $rsContainer = array_column($rsContainer,'name','pkey'); 
        
     
        
        $customerName = (!empty($rsCustomer[0]['alias'])) ? $rsCustomer[0]['alias'] : $rsCustomer[0]['name'];
        $joCode = $rsDetail[$i]['jobordercode'];
		$invoiceReference = (!empty($rsDetail[$i]['refcode'])) ? '<i>'.$rsDetail[$i]['refcode'].'</i>' :'';
	}else if($rsDetail[$i]['cashtypekey']==2) {
        
            
        $detailDesc = $obj->lang['downpayment'] . ' ' .$supplierName;  
    }else if($rsDetail[$i]['cashtypekey']==3){
        $detailDesc = $rsDetail[$i]['coaname'];  
		//$coaname =  $rsDetail[$i]['coaname'];  
    }elseif($rsDetail[$i]['cashtypekey']==4){
		//$detailDesc = $rsDetail[$i]['jobheadercode'].' - '.$rsDetail[$i]['containername'];
        
        
               $rsJobOrderHeader = $emklJobOrderHeader->searchDataRow(array($emklJobOrderHeader->tableName.'.pkey',$emklJobOrderHeader->tableName.'.code',$emklJobOrderHeader->tableName.'.customerkey',$emklJobOrderHeader->tableName.'.loadcontainertypekey',$emklJobOrderHeader->tableName.'.weight',$emklJobOrderHeader->tableName.'.volume' ),
                                                    ' and '.$emklJobOrderHeader->tableName.'.pkey = '.$obj->oDbCon->paramString($rsDetail[$i]['jobheaderkey'])
                                                  );
    		$rsDetailVolume = $emklJobOrderHeader->getDetailContainer($rsJobOrderHeader[0]['pkey']);

            if($rsJobOrderHeader[0]['loadcontainertypekey'] != 5){
            $arrParty = array();
            foreach($rsDetailVolume as $volumeRow)
                  array_push($arrParty,$obj->formatNumber($volumeRow['qty']).' x '.$volumeRow['itemname']);

            $party = implode('<br>',$arrParty);
        }else{
            
             $temp = array();
            if(!empty($rsJobOrderHeader[0]['volume'])) array_push($temp, $obj->formatNumber($rsJobOrderHeader[0]['volume'],2). ' CBM');
            if(!empty($rsJobOrderHeader[0]['weight'])) array_push($temp, $obj->formatNumber($rsJobOrderHeader[0]['weight'],2) . ' KG');


            $party = implode('<br> ',$temp);

        }
        
        $rsCustomer = $customer->getDataRowById($rsJobOrderHeader[0]['customerkey']); 
        $joCode = $rsJobOrderHeader[0]['code'];
        
        $customerName = (!empty($rsCustomer[0]['alias'])) ? $rsCustomer[0]['alias'] : $rsCustomer[0]['name'];

		$invoiceReference = (!empty($rsDetail[$i]['refcode'])) ? '<i>'.$rsDetail[$i]['refcode'].'</i>':'';
	}      
    
    $arrDesc = array();
	//if(!empty($supplierName)) array_push($arrDesc,$supplierName);
	//if(!empty($invoiceReference)) array_push($arrDesc,$invoiceReference);
	if(!empty($serviceName)) array_push($arrDesc,$serviceName);
	if(!empty($detailDesc)) array_push($arrDesc,$detailDesc);
    if(!empty($rsDetail[$i]['description'])) array_push($arrDesc,$rsDetail[$i]['description']);
    
    $ket = implode('<br>',$arrDesc);
    
    
   $html .= '<tr><td style="'.$borderRight.'">'.$ket.'</td><td style="'.$borderRight.'">'. $joCode .'</td><td style="'.$borderRight.'">'. $customerName.'</td><td style="'.$borderRight.'text-align:center">'.$party.'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['subtotal']).'</td></tr>' ; 
     
} 
$html .= '</table>' ;

//$sayNumber = $obj->sayNumber($rs[0]['total']);
//$html .= '<table cellpadding="4">';

$html .= '<table  cellpadding="3" style="'.$borderTop.'">
<tr class="" ><td style="width:235px;"></td><td style="width:335px;text-align:right" ></td><td style="'.$borderRight.$borderBottom.$borderLeft.'text-align:right; width:110px;">'.$obj->formatNumber($rs[0]['total']).'</td></tr>
<tr class="" ><td style="width:235px;"></td><td style="width:335px;text-align:right" ></td><td style="text-align:right; width:110px;"></td></tr>
</table>';
 
return $html;
}
?>