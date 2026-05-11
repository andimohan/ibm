<?php  

includeClass(array('OfferSimulator.class.php'));
$offerSimulator =  new OfferSimulator(); 


$obj = $offerSimulator;
$generateReportContent = function ($dataset){ 
    
$obj = new OfferSimulator(); 
$item = new Item(); 

$rs = $dataset['rs'];
$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']); 

$html = $obj->printSetting['defaultStyle'];
$html .= ' 
<table cellpadding="2" > 
<tr><td><div class="title">'.$rs[0]['name'].'</div></td></tr>
</table> 
<div style="clear:both"></div> 
<div style="clear:both"></div> ';

$html .= ' 
<table  cellpadding="4" class="table-transaction">
<tr class="col-header" ><td style="width:100px;">Gambar</td><td style="width:300px;">Nama Barang</td><td style="width:70px;text-align:right" >Qty</td><td style="width:100px;text-align:right" >Harga</td><td style="text-align:right; width:110px;">Subtotal</td></tr>';
    $arrImage = array();

    for($i=0;$i<count($rsDetail); $i++){
    
        $rsImage = $item->getItemImage($rsDetail[$i]['itemkey']); 
        for($j=0;$j<count($rsImage);$j++){
                $rsImage[$j]['phpThumbHash'] = getPHPThumbHash($rsImage[$j]['file']);	
        }
    
        $rsDetail[$i]['file'] = $rsImage[0]['file'];
        $rsDetail[$i]['phpThumbHash'] = getPHPThumbHash($rsImage[0]['file']);
            
        $img = HTTP_HOST.'phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc.'item/'.$rsDetail[$i]['itemkey'].'/'.$rsDetail[$i]['file'].'&w=200&h=200&hash='.$rsDetail[$i]['phpThumbHash'];

        $shortdescription  = (!empty($rsDetail[$i]['itemshortdescription'])) ? '<br>'.str_replace(chr(13),'<br>',$rsDetail[$i]['itemshortdescription']) : '';
        $html .= '<tr><td><img src="'.$img.'"></td><td style=""><b>'.$rsDetail[$i]['itemname'].'</b>'.$shortdescription.'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['qty']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</td><td style="text-align:right">'.$obj->formatNumber($rsDetail[$i]['total']).'</td></tr>' ; 

    } 

$html .= '</table>' ;

$html .= '<div style="clear:both"></div>';
        
$sayNumber = $obj->sayNumber($rs[0]['grandtotal']);
    
$subtotalLabel =  ucwords($obj->lang['total']) ; 

$html .= '<table cellpadding="4" > 
<tr>
<td rowspan="'.(count($arrSubtotal) + 1).'" style="width:470px"></td>
<td style="text-align:right; font-weight:bold;  width:100px;">'.$subtotalLabel.'</td>
<td style="text-align:right; font-weight:bold;  width:110px;">'.$obj->formatNumber($rs[0]['grandtotal']).'</td>
</tr>
';  
    
$html .= implode('',$arrSubtotal); 
    
$html .= '
</table>
<div style="clear:both"></div>
<div>* Harga dapat berubah sewaktu-waktu</div>
';

    
return $html;
}
?>
