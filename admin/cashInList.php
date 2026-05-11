<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('CashIn.class.php');
$cashIn= createObjAndAddToCol( new CashIn()); 


$obj = $cashIn;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'cashInForm'; 

function generateQuickView($obj,$id){ 
	$coa = new ChartOfAccount();
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	    
	$detail = '';

	$detailInformation  = ' <div class="data-card no-border">
					<h1>'. ucwords($obj->lang['detail']).'</h1> 
					<div class="content">
					<div class="div-table  quick-view-table">
						  <div class="div-table-row"> ';
    
    if($obj->useMasterRevenue)
      $detailInformation  .= ' <div class="div-table-col detail-col-header"  style="width:250px;">'.ucwords($obj->lang['revenue']).'</div>';
    
    
    $detailInformation  .= '<div class="div-table-col detail-col-header"  style="width:250px;">'.ucwords($obj->lang['account']).'</div>
								<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">'.ucwords($obj->lang['amount']).'</div> 
								<div class="div-table-col detail-col-header" style="width:200px;">'.ucwords($obj->lang['note']).'</div> 
								<div class="div-table-col detail-col-header"></div>  
							</div>';
							
	for ($i=0;$i<count($rsDetail);$i++){
	 
		$detailInformation  .= '
			<div class="div-table-row"> ';
        
        
        if($obj->useMasterRevenue)
			$detailInformation  .= '<div class="div-table-col">'.$rsDetail[$i]['revenuename'].'</div>';
        
        
		$detailInformation  .= '
				<div class="div-table-col">'.$rsDetail[$i]['coacodename'].'</div>
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['amount']).'</div> 
				<div class="div-table-col">'. $rsDetail[$i]['trdesc'].'</div> 
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
