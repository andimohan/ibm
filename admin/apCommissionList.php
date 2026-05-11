<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('AP.class.php','APCommission.class.php'));
$apCommission = createObjAndAddToCol(new APCommission());

$obj = $apCommission;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
   
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'apCommissionForm';
//$quickView = false;
		  
//$overwriteContextMenu['showDetail'] = '';
//$overwriteContextMenu['hideDetail'] = ''; 
function generateQuickView($obj,$id){  
	$apPayment = $obj->getPaymentObj();
	
	$detail = ''; 
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);
	$rsDetailPayment = $apPayment->getDetailPaymentByAPKey($id);
	  
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>'. ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table general-information-table">
							<div class="div-table-row">
								<div class="div-table-col" style="width:40%">'. ucwords($obj->lang['status']).'</div> 
								<div class="div-table-col">'.$rs[0]['statusname'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['code']).'</div> 
								<div class="div-table-col">'.$rs[0]['code'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['reference']).'</div> 
								<div class="div-table-col">'.$rs[0]['refcode'].'</div> 
							</div>
							
							 <div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['duedate']).'</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['duedate']).'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['supplier']).'</div> 
								<div class="div-table-col">'. $rs[0]['suppliername'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['amount']).'</div> 
								<div class="div-table-col">'.$rs[0]['currencyname'].' '.$obj->formatNumber($rs[0]['amount'],-2).'</div> 
							 </div> 
							 <div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['outstanding']).'</div> 
								<div class="div-table-col">'.$rs[0]['currencyname'].' '.$obj->formatNumber($rs[0]['outstanding'],-2).'</div> 
							 </div>
							 <div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['note']).'</div> 
								<div class="div-table-col">'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</div> 
							</div>
						</div>
						</div>
					</div>  
		'; 	
		
		$detailInformation  = ' <div class="data-card border-green">
						<h1>'. ucwords($obj->lang['paymentDetail']).'</h1> 
						<div class="content">
						<div class="div-table  quick-view-table">
							  <div class="div-table-row"> 
									<div class="div-table-col detail-col-header" style="width:150px">'. ucwords($obj->lang['paymentCode']).'</div>
									<div class="div-table-col detail-col-header" style="width:150px; text-align:center;">'. ucwords($obj->lang['date']).'</div>
									<div class="div-table-col detail-col-header" style="text-align:right;">'. ucwords($obj->lang['amount']).'</div>
								</div>';
								
		for ($i=0;$i<count($rsDetailPayment);$i++){
			
			$rsApPayment= $apPayment->getDataRowById($rsDetailPayment[$i]['refkey']); 
			$detailInformation  .= '
				<div class="div-table-row"> 
					<div class="div-table-col">'.$rsApPayment[0]['code'].'</div>
					<div class="div-table-col" style="text-align:center;">'.$obj->formatDBDate($rsApPayment[0]['trdate']).'</div>
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetailPayment[$i]['amount']).'</div>
				</div>
			'; 
		}
								
		$detailInformation  .= ' </div>
						</div>
					</div>  
		'; 
		
		$detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5"  style="width:25%; text-align:center;">
								'.$basicInformation.'
								</div> 
								<div class="div-table-col-5"  style="text-align:center; ">
								 '.$detailInformation.'
								</div> 
							</div>
					</div>';
				  
		$detail .= '<div style="clear:both;"></div>';	
	
	
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>