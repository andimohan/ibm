<?php  
require_once '../_config.php';   
require_once '../_include-v2.php';  
includeClass(array('Downpayment.class.php','CustomerDownpayment.class.php'));
$customerDownpayment = createObjAndAddToCol(new CustomerDownpayment());
$salesOrder = createObjAndAddToCol(new SalesOrder());
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());


$obj = $customerDownpayment;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
  
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'customerDownpaymentForm';
 

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tgl. Transaksi', $obj->tableName . '.trdate')); 
array_push($arrSearchColumn, array('Referensi', $obj->tableName. '.refcode'));
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer. '.name'));
array_push($arrSearchColumn, array('Jumlah', $obj->tableName. '.amount'));
array_push($arrSearchColumn, array('Outstanding', $obj->tableName. '.outstanding'));
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trdesc'));
 
function generateQuickView($obj,$id){   
	$detail = ''; 
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);
    $rsDownpaymentHistory = $obj->getUsedDPList($id);
    $rsDownpaymentHistory = $rsDownpaymentHistory['history'];
	
    $rsDPSettlementHistory = $obj->getDPSettlementList($id); 
    $rsDPSettlementHistory = $rsDPSettlementHistory['history'];
	
    $rsDownpaymentHistory = array_merge($rsDownpaymentHistory,$rsDPSettlementHistory);
	
	  
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>'. ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table general-information-table">
							<div class="div-table-row">
								<div class="div-table-col" style="width:50%">'. ucwords($obj->lang['status']).'</div> 
								<div class="div-table-col">'.$rs[0]['statusname'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['code']).'</div> 
								<div class="div-table-col">'.$rs[0]['code'].'</div> 
							</div>    
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['downpayment']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['payment']).'</div> 
							 </div> 
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['tax']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['taxvalue']).'</div> 
							 </div> 
							 <div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['outstanding']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['outstanding']).'</div> 
							 </div> 
						</div>
						</div>
					</div>  
		'; 	
		
		$detailInformation  = ' <div class="data-card border-green">
						<h1>'. ucwords($obj->lang['usageHistory']).'</h1> 
						<div class="content">
						<div class="div-table quick-view-table">
							  <div class="div-table-row"> 
									<div class="div-table-col detail-col-header" style="width:150px">'. ucwords($obj->lang['reference']).'</div>
									<div class="div-table-col detail-col-header" style="width:150px; text-align:center;">'. ucwords($obj->lang['date']).'</div>
									<div class="div-table-col detail-col-header" style="text-align:right;">'. ucwords($obj->lang['amount']).'</div> 
								</div>';
								
		for ($i=0;$i<count($rsDownpaymentHistory);$i++){
			  
				$detailInformation  .= '
					<div class="div-table-row"> 
						<div class="div-table-col">'.$rsDownpaymentHistory[$i]['code'].'</div>
						<div class="div-table-col" style="text-align:center;">'.$obj->formatDBDate($rsDownpaymentHistory[$i]['trdate']).'</div>
						<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDownpaymentHistory[$i]['amount']).'</div> 
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
