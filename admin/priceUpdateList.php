<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('PriceUpdate.class.php');
$priceUpdate = createObjAndAddToCol(new PriceUpdate()); 


$obj = $priceUpdate;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'priceUpdateForm';
   
function generateQuickView($obj,$id){  
		  
	$rsDetail = $obj->getDetailWithRelatedInformation($id);    
	 
	$rate  = '<div class="data-card border-red" style="margin:auto;">
						<h1>'.$obj->lang['priceUpdate'].'</h1>
						<div class="content">';
						
	$rate  .= '<div class="div-table  quick-view-table">
                     <div class="div-table-row"> 
                            <div class="div-table-col detail-col-header" style="width:100px;">'.ucwords($obj->lang['pricingCategory']).'</div> 
                            <div class="div-table-col detail-col-header" style="text-align:right; width: 100px;">'.ucwords($obj->lang['lastRate']).'</div> 
                            <div class="div-table-col detail-col-header" style="text-align:right; width: 100px;">'.ucwords($obj->lang['currentRate']).'</div> 
                            <div class="div-table-col detail-col-header" ></div> 
                     </div>

						';
						
     for($i=0;$i<count($rsDetail);$i++){
         $rate .= ' <div class="div-table-row">
                            <div class="div-table-col" style="width:50px">'. $rsDetail[$i]['pricingcategoryname'] .' </div>
                            <div class="div-table-col" style="text-align:right;">'. $obj->formatNumber($rsDetail[$i]['ratebefore']) .' </div>
                            <div class="div-table-col" style="text-align:right;">'. $obj->formatNumber($rsDetail[$i]['rate']) .' </div>
                            <div class="div-table-col" ></div>
                    </div>';

     }
	$rate .= ' </div>';
			 
	$rate  .= '
				</div>
					</div>'; 
	 
				
	$detail = '<div class="div-table" style="width:100%; ">
			<div class="div-table-row">
				<div class="div-table-col"  style="width:100%; text-align:center">
				'.$rate.' 
				</div>     
			</div>
	</div>';
  
	$detail .= '<div style="clear:both;"></div>';	
	 
  
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
