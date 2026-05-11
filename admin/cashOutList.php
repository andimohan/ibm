<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('CashOut.class.php');
$cashOut = createObjAndAddToCol( new CashOut()); 


$obj = $cashOut;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'cashOutForm'; 


function generateQuickView($obj,$id){ 
	$coa = new ChartOfAccount();
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	    
    
    $useTax = ($obj->loadSetting('taxOnCashBank') == 1) ? true : false;
    
	$detail = '';

	$detailInformation  = ' <div class="data-card no-border">
					<h1>'. ucwords($obj->lang['detail']).'</h1> 
					<div class="content">
					<div class="div-table quick-view-table">
						  <div class="div-table-row">';
    
    if($obj->useMasterCost)
      $detailInformation  .= ' <div class="div-table-col detail-col-header"  style="width:250px;">'.ucwords($obj->lang['cost']).'</div>';
    
    $detailInformation  .= '<div class="div-table-col detail-col-header"  style="width:250px;">'.ucwords($obj->lang['account']).'</div>
								<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">'.ucwords($obj->lang['amount']).'</div>';
    
    if ($useTax){ 
			$detailInformation  .= '<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">PPN</div>  
								<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">PPH</div>  
								<div class="div-table-col detail-col-header" style="width:100px; text-align:right;">' . ucwords($obj->lang['total']) . '</div> '; 
    }
								
                        
	$detailInformation  .= '<div class="div-table-col detail-col-header" style="width:200px;">'.ucwords($obj->lang['note']).'</div> 
                            <div class="div-table-col detail-col-header"></div>  
							</div>';
							
	for ($i=0;$i<count($rsDetail);$i++){
		 
		$detailInformation  .= '
			<div class="div-table-row"> ';
        
        
        if($obj->useMasterCost)
			$detailInformation  .= '<div class="div-table-col">'.$rsDetail[$i]['costname'].'</div>';
        
        
		$detailInformation  .= '
                <div class="div-table-col">'.$rsDetail[$i]['coaname'].'</div>';
        

        if ($useTax){ 
           $detailInformation  .= ' <div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['amount']) . '</div>  
            <div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['taxvalue']) . '</div>  
            <div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['pphvalue']) . '</div> ';
        }

		 $detailInformation  .= ' <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['total']).'</div> 
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
