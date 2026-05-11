<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('AR.class.php','ARPrepaidTax23.class.php'));
$arPrepaidTax23 = createObjAndAddToCol(new ARPrepaidTax23());

$obj = $arPrepaidTax23;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
  
if(!$security->isAdminLogin($securityObject,10,true));
 
//$addDataFile = 'arForm';
//$quickView = false;
 

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tgl. Transaksi', $obj->tableName . '.trdate')); 
array_push($arrSearchColumn, array('Referensi', $obj->tableName. '.refcode'));
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer. '.name'));
array_push($arrSearchColumn, array('Jumlah', $obj->tableName. '.amount'));
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trdesc'));
 

//$overwriteContextMenu['showDetail'] = '';
//$overwriteContextMenu['hideDetail'] = ''; 
  
  
function generateQuickView($obj,$id){  
	$arPayment = $obj->getPaymentObj();
	
	$detail = ''; 
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);
	$rsDetailPayment = $arPayment->getDetailPaymentByARKey($id);
	 
	
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
								<div class="div-table-col">'. ucwords($obj->lang['duedate']).'</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['duedate']).'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['customer']).'</div> 
								<div class="div-table-col">'. $rs[0]['customername'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['reference']).'</div> 
								<div class="div-table-col">'.$rs[0]['refcode'].'</div> 
							</div>';
    
	    if ( in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])))
          $basicInformation   .= '<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['si']).'</div> 
								<div class="div-table-col">'.$rs[0]['refcode2'].'</div> 
							</div>';
    
        $basicInformation   .= '<div class="div-table-row">
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
						<div class="div-table quick-view-table">
							  <div class="div-table-row"> 
									<div class="div-table-col detail-col-header" style="width:150px">'. ucwords($obj->lang['paymentCode']).'</div>
									<div class="div-table-col detail-col-header" style="width:150px; text-align:center;">'. ucwords($obj->lang['date']).'</div>
									<div class="div-table-col detail-col-header" style="text-align:right;">'. ucwords($obj->lang['amount']).'</div>
									<div class="div-table-col detail-col-header" style="text-align:right;">'. ucwords($obj->lang['discount']).'</div>
								</div>';
								
		for ($i=0;$i<count($rsDetailPayment);$i++){
			
			$rsArPayment= $arPayment->getDataRowById($rsDetailPayment[$i]['refkey']);
			 
				$detailInformation  .= '
					<div class="div-table-row"> 
						<div class="div-table-col">'.$rsArPayment[0]['code'].'</div>
						<div class="div-table-col" style="text-align:center;">'.$obj->formatDBDate($rsArPayment[0]['trdate']).'</div>
						<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetailPayment[$i]['amount']).'</div>
						<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetailPayment[$i]['discount']).'</div>
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