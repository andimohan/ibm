<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass('CashBankOut.class.php');
$cashBankOut = createObjAndAddToCol( new CashBankOut()); 

$obj = $cashBankOut;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'cashBankOutForm';
 
function generateQuickView($obj,$id){ 
	$cashBank = new CashBank();
    $rs = $obj->getDataRowById($id);
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	    
	$detail = '';

	$detailInformation  = ' <div class="data-card no-border">
					<h1>'. ucwords($obj->lang['detail']).'</h1> 
					<div class="content">
					<div class="div-table quick-view-table">
						  <div class="div-table-row"> 
								<div class="div-table-col detail-col-header" style="width:120px;">'. ucwords($obj->lang['cashBankNumber']).'</div> 
								<div class="div-table-col detail-col-header" style="width:120px;">'. ucwords($obj->lang['transactionType']).'</div> 
								<div class="div-table-col detail-col-header" style="width:100px; text-align:right;">'. ucwords($obj->lang['amount']).'</div>  
								<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">PPN</div>  
								<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">PPH</div>  
								<div class="div-table-col detail-col-header" style="width:100px; text-align:right;">'. ucwords($obj->lang['total']).'</div>  
								<div class="div-table-col detail-col-header"  style="width:250px;">'. ucwords($obj->lang['description']).'</div>
								<div class="div-table-col detail-col-header"></div>  
							</div>';
							
	for ($i=0;$i<count($rsDetail);$i++){
		  
        $rsCashBank = $cashBank->getCashBankRef($id,$obj->tableName,$rs[0]['coakey'],$rsDetail[$i]['pkey']);
        
		$detailInformation  .= '
			<div class="div-table-row"> 
				<div class="div-table-col">'. $rsCashBank['code'].'</div> 
				<div class="div-table-col">'. ((!empty($rsDetail[$i]['costname'])) ? $rsDetail[$i]['costname'] : $obj->lang['temporaryAccount']).'</div> 
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['beforetax']).'</div>  
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['taxvalue']).'</div>  
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['pphvalue']).'</div>  
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['total']).'</div>  
				<div class="div-table-col">'.$rsDetail[$i]['trdesc'].'</div>
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