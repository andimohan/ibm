<?php

require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('ReceivingPurchaseJewelry.class.php');
$receivingPurchaseJewelry = createObjAndAddToCol(new ReceivingPurchaseJewelry()); 

$obj = $receivingPurchaseJewelry;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'receivingPurchaseJewelryForm';

function generateQuickView($obj, $id)
{
	$purchaseOrderJewelry = createObjAndAddToCol(new PurchaseOrderJewelry()); 
    
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);   
 	$rsDetail = $obj->getDetailWithRelatedInformation($id);

	$rsPODetail = $purchaseOrderJewelry->getDetailWithRelatedInformation($rs[0]['refkey']);
	$rsPODetail = $obj->reindexDetailCollections($rsPODetail,'pkey');
	   
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>'.ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table general-information-table">
							<div class="div-table-row">
								<div class="div-table-col" style="width:50%">'.ucwords($obj->lang['status']).'</div> 
								<div class="div-table-col">'.$rs[0]['statusname'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['code']).'</div> 
								<div class="div-table-col">'.$rs[0]['code'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['date']).'</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['trdate']).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['poCode']).'</div> 
								<div class="div-table-col">'.$rs[0]['purchaseordercode'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div>  
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['note']).'</div> 
								<div class="div-table-col">'.$rs[0]['trdesc'].'</div> 
							</div> 
						</div>
						</div>
					</div>  
		'; 	
		
		$detailInformation  = ' <div class="data-card border-green">
						<h1>'.ucwords($obj->lang['itemDetail']).'</h1> 
						<div class="content">
						<div class="div-table quick-view-table">
							  	<div class="div-table-row"> 
									<div class="div-table-col detail-col-header" style="width:120px;">'.ucwords($obj->lang['itemType']).'</div>
									<div class="div-table-col detail-col-header">'.ucwords($obj->lang['itemName']).'</div>
									<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">'.ucwords($obj->lang['orderedQty']).'</div>
									<div class="div-table-col detail-col-header" style="width:60px; text-align:right;">'.ucwords($obj->lang['received']).'</div> 
									<div class="div-table-col detail-col-header" style="width:60px; text-align:right;"></div>
									<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">'.ucwords($obj->lang['orderedQty']).'</div>
									<div class="div-table-col detail-col-header" style="width:60px; text-align:right;">'.ucwords($obj->lang['received']).'</div> 
									<div class="div-table-col detail-col-header" style="width:30px; text-align:right;"></div>
									<div class="div-table-col detail-col-header" style="width:60px; text-align:right;">' . ucwords('GW (Gr)') . '</div>
									<div class="div-table-col detail-col-header" style="width:100px;">'.ucwords($obj->lang['packaging']).'</div> 
								</div>';
								
		for ($i=0;$i<count($rsDetail);$i++){

			if(!isset($rsPODetail[$rsDetail[$i]['refpodetailkey']])) continue;

		$rsPODetailCol = $rsPODetail[$rsDetail[$i]['refpodetailkey']];

			$detailInformation  .= '
				<div class="div-table-row"> 
					<div class="div-table-col">'.$obj->formatNumber($rsPODetailCol[0]['number'],2).' - '.$rsPODetailCol[0]['itemname'].'</div>
					<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['orderedqtyinbaseunit']).'</div> 
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['receivedqtyinbaseunit']).'</div> 
					<div class="div-table-col text-muted">'. $rsDetail[$i]['baseunitname'] .'</div> 
					<div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['orderedqtyinpcs']) . '</div> 
					<div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['receivedqtyinpcs']) . '</div> 
					<div class="div-table-col text-muted">Gr</div> 
					<div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['grossweight']) . '</div> 
					<div class="div-table-col">' . $rsDetail[$i]['packagingname'] . '</div> 
				</div>
			';
		}
								
		$detailInformation  .= ' </div>
						</div>
					</div>  
		'; 	
		
		$detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5"  style="width:25%;">
								'.$basicInformation.'
								</div> 
								<div class="div-table-col-5" >
								    '.$detailInformation.'
								</div>  
							</div>
					</div>';
				  
		$detail .= '<div style="clear:both;"></div>';	
	 
	return $detail;  
}

include ('dataList.php');

?>
