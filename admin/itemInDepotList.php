<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $itemInDepot;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'itemInDepotForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer . '.name'));
array_push($arrSearchColumn, array('DO Number', $obj->tableName . '.docode'));
array_push($arrSearchColumn, array('Gudang', $obj->tableDepot. '.name') ); 
 
function generateQuickView($obj,$id){  
    $showVendorPartNumber = $obj->loadSetting('showVendorPartNumber');
	
    $rsDetail = $obj->getDetailWithRelatedInformation($id); 
    
	$detail = '';
 
	$detailInformation  = ' <div class="data-card no-border">
					<h1>Detail Item</h1> 
					<div class="content">
					<div class="div-table quick-view-table" >
						  <div class="div-table-row">  
                                <div class="div-table-col detail-col-header" style="width:300px;">'.$obj->lang['item'].'</div>
								<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">'.$obj->lang['quantity'].'</div>
								<div class="div-table-col detail-col-header" style="width:40px; padding-left:0px;"></div>  
								<div class="div-table-col detail-col-header" style="width:100px; text-align:right;">'.$obj->lang['totalWeight'].'</div> 
								<div class="div-table-col detail-col-header" style="width:40px; padding-left:0px;"></div>  
								<div class="div-table-col detail-col-header" style="width:100px; text-align:right;">'.$obj->lang['totalVolume'].'</div> 
								<div class="div-table-col detail-col-header" style="width:40px; padding-left:0px;"></div>  
								<div class="div-table-col detail-col-header"></div>  
							</div>';
							
	for ($i=0;$i<count($rsDetail);$i++){ 
         
		$detailInformation  .= '
			<div class="div-table-row">  
				<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</div>
				<div class="div-table-col text-muted" style="padding-left:0px;">'.$rsDetail[$i]['unitname'].'</div> 
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['totalweight'],2).'</div>
				<div class="div-table-col text-muted" style="padding-left:0px;">'.$rsDetail[$i]['weightunitname'].'</div> 
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['totalvolume'],2).'</div>
				<div class="div-table-col text-muted" style="padding-left:0px;">CM<sup>3</sup></div> 
				<div class="div-table-col"></div> 
			</div>
		';
	}
							
	$detailInformation  .= ' </div>
					</div>
				</div>  
	'; 	
		
	$detail .= $detailInformation;
			  
	$detail .= '<div style="clear:both;"></div>';	
	 
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
