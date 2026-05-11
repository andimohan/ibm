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
    
$obj = new CashOut();
$cashBank = new CashBank();
$chartOfAccount = new ChartOfAccount();
    
$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
$rsCOA = $chartOfAccount->getDataRowById($rs[0]['coakey']);
$cashBankCode = (ADV_FINANCE) ? $cashBank->getCashBankRef($rs[0]['pkey'],$obj->tableName)['code'] : '';  
$borderTop = 'border-top:1px solid black;';
$borderLeft = 'border-left:1px solid black;';
$borderRight = 'border-right:1px solid black;';
$borderBottom = 'border-bottom:1px solid black;';
    $profileImg = $obj->loadSetting('companyLogo'); 
$img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&w=220&h=110&hash='.getPHPThumbHash($profileImg);

$html = $obj->printSetting['defaultStyle'];
$html .= '
<table style="'.$borderTop.$borderRight.$borderLeft.'width:660px">
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
            <tr><td style="width:40px;"></td><td style="text-algin:center"><div class="title">BUKTI KAS</div></td></tr>
            <tr><td style="width:200px;font-size:1.2em"><b>Tgl.</b> '.$obj->formatDBDate($rs[0]['trdate'],'d / m / Y').'</td><td style="width:190px;font-size:1.2em"><b>No: '.$rs[0]['code'].' </b></td></tr>
            </table>  
        </td>
        <td style="width:229.9px">

        </td>
    </tr> 
    <tr><td></td></tr>
</table>
';
    
//if(ADV_FINANCE)
// $html .= '<tr><td class="header-row-header">'.$obj->lang['voucherNumber'].'</td><td style=" text-align:center">:</td><td>'.$cashBankCode.'</td></tr>';
//  
    
$html .= '
<table cellpadding="2" style="'.$borderRight.$borderLeft.'">
<tr><td style="width:30px"></td><td class="header-row-header" style="font-size:1.2em;">Dibayar kepada :</td><td style="width:530px;font-size:1.2em;">'.$rs[0]['recipientname'].'</td></tr> 

</table>   ';

//$cellArray = array();
//array_push($cellArray, array('label' => /*$obj->lang['cost']*/ 'Keterangan', 'width' => '225', 'etc' => 'style="'.$borderRight.'"'));
//array_push($cellArray, array('label' => $obj->lang['description']));
//array_push($cellArray, array('label' => $obj->lang['amount'],'align' => 'right', 'width' => '100'));
//  
$html .= '<table  cellpadding="3" style="'.$borderLeft.$borderRight.'">
<tr class="col-header" ><td style="'.$borderRight.'width:235px;">Keterangan</td><td style="'.$borderRight.'width:335px;" >Deskripsi</td><td style="text-align:right; width:110px;">Jumlah</td></tr>';
//$html .= $obj->generatePrintTableRow( array('class' => 'col-header', 'cell' =>  $cellArray)); 
    
    


for ($i=0;$i<count($rsDetail);$i++){
            
    $itemName = $rsDetail[$i]['coaname'];
    
    if($obj->useMasterCost)
        $itemName = $rsDetail[$i]['costname'].'<br>'.$itemName;      
    
    $html .= '<tr><td style="'.$borderRight.'">'.$itemName.'</td><td style="'.$borderRight.'">'. $rsDetail[$i]['trdesc'] .'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['amount']).'</td></tr>' ; 
    
} 
$html .= '</table>' ;

$sayNumber = $obj->sayNumber($rs[0]['grandtotal']); 
  
//$cellArray = array ();
//array_push($cellArray, array('label' => ""));
//array_push($cellArray, array('label' => $obj->lang['total'],'align' => 'right', 'width' => '60','style' => 'font-weight:bold'));
//array_push($cellArray, array('label' => $rs[0]['grandtotal'],'align' => 'right', 'format' => 'number', 'width' => '100'));
//$html .= $obj->generatePrintTableRow( array('cell' =>  $cellArray));  

$html .= '<table  cellpadding="4" style="'.$borderTop.'">
<tr class="" ><td style="width:235px;"></td><td style="width:335px;text-align:right" ></td><td style="'.$borderRight.$borderBottom.$borderLeft.'text-align:right; width:110px;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td></tr>
<tr class="" ><td style="width:235px;"></td><td style="width:335px;text-align:right" ></td><td style="text-align:right; width:110px;"></td></tr>
</table> 
';
     

    
//$html .= '</table>
//<table cellpadding="4">
//<tr><td><strong>Catatan</strong> :<br>'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</td></tr>
//</table>
//<div "clear:both"></div>';

//$html .= $obj->generateSignLabel($rs); 
    
return $html;
}
?>
