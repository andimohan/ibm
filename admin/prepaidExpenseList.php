<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php';
 
includeClass('PrepaidExpense.class.php');
$prepaidExpense = createObjAndAddToCol( new PrepaidExpense());  
$emklOrderInvoice = createObjAndAddToCol( new EMKLOrderInvoice());  
$obj = $prepaidExpense;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
  
if(!$security->isAdminLogin($securityObject,10,true));
 
//$addDataFile = 'costReconsileOutstandingForm';

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tgl. Transaksi', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Referensi', $obj->tableJobOrder. '.code'));
array_push($arrSearchColumn, array('Jumlah', $obj->tableName. '.amount'));
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trdesc'));
array_push($arrSearchColumn, array('Referensi', $obj->tableName. '.refcode'));
array_push($arrSearchColumn, array('Layanan', $obj->tableService. '.name'));
array_push($arrSearchColumn, array('Mata Uang', $obj->tableCurrency. '.name')); 
  
$quickView = false; 
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 
   
function generateQuickView($obj,$id){  
	return '';
//	$costReconsile = $obj->getCostReconsileObj();
//	$emklOrderInvoice = new EMKLOrderInvoice();
//	$detail = ''; 
//	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);
//    $decimalPrice = ($rs[0]['currencykey'] == CURRENCY['idr'] ) ? 0 : 2;  
//
//	$rsDetailReconsile = $costReconsile->getDetailReconsile($id);
//	 
//	
//	$basicInformation  = ' <div class="data-card border-orange">
//						<h1>'. ucwords($obj->lang['generalInformation']).'</h1> 
//						<div class="content">
//						<div class="div-table general-information-table">
//							<div class="div-table-row">
//								<div class="div-table-col" style="width:40%">'. ucwords($obj->lang['status']).'</div> 
//								<div class="div-table-col">'.$rs[0]['statusname'].'</div> 
//							</div>
//							<div class="div-table-row">
//								<div class="div-table-col">'. ucwords($obj->lang['code']).'</div> 
//								<div class="div-table-col">'.$rs[0]['code'].'</div> 
//							</div>
//							<div class="div-table-row">
//								<div class="div-table-col">'. ucwords($obj->lang['reference']).'</div> 
//								<div class="div-table-col">'.$rs[0]['refcode'].'</div> 
//							</div>';
//
//    
//        $basicInformation   .= '<div class="div-table-row">
//								<div class="div-table-col" style="height:1em"></div> 
//								<div class="div-table-col"></div> 
//							</div>  
//							<div class="div-table-row">
//								<div class="div-table-col">'. ucwords($obj->lang['amount']).'</div> 
//								<div class="div-table-col">'.$rs[0]['currencyname'].' '.$obj->formatNumber($rs[0]['amount'],-2).'</div> 
//							 </div> 
//							 <div class="div-table-row">
//								<div class="div-table-col">'. ucwords($obj->lang['outstanding']).'</div> 
//								<div class="div-table-col">'.$rs[0]['currencyname'].' '.$obj->formatNumber($rs[0]['outstanding'],-2).'</div> 
//							 </div>
//							 <div class="div-table-row">
//								<div class="div-table-col">'. ucwords($obj->lang['note']).'</div> 
//								<div class="div-table-col">'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</div> 
//							</div>
//						</div>
//						</div>
//					</div>  
//		'; 	
//		
//		$detailInformation  = ' <div class="data-card border-green">
//						<h1>'. ucwords($obj->lang['admission']).'</h1> 
//						<div class="content">
//						<div class="div-table quick-view-table">
//							  <div class="div-table-row"> 
//									<div class="div-table-col detail-col-header" style="width:150px">'. ucwords($obj->lang['reconsileCode']).'</div>
//									<div class="div-table-col detail-col-header" style="width:150px">'. ucwords($obj->lang['invoiceCode']).'</div>
//									<div class="div-table-col detail-col-header" style="width:150px; text-align:center;">'. ucwords($obj->lang['date']).'</div>
//									<div class="div-table-col detail-col-header" style="text-align:right;">'. ucwords($obj->lang['amount']).'</div>
//								</div>';
//								
//		for ($i=0;$i<count($rsDetailReconsile);$i++){
//			
//			$rsCostReconsile= $costReconsile->getDataRowById($rsDetailReconsile[$i]['refkey']);
//            $rsInvoice = $emklOrderInvoice->getDataRowById($rsCostReconsile[0]['refkey']);
//            
//				$detailInformation  .= '
//					<div class="div-table-row"> 
//						<div class="div-table-col">'.$rsCostReconsile[0]['code'].'</div>
//						<div class="div-table-col">'.$rsInvoice[0]['code'].'</div>
//						<div class="div-table-col" style="text-align:center;">'.$obj->formatDBDate($rsCostReconsile[0]['trdate']).'</div>
//						<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetailReconsile[$i]['amount'],$decimalPrice).'</div>
//					</div>
//				'; 
//		}
//		
//		$detailInformation  .= ' </div>
//						</div>
//					</div>  
//		'; 
//		
//		
//		$detail .= '<div class="div-table" style="width:100%; ">
//							<div class="div-table-row">
//								<div class="div-table-col-5"  style="width:25%; text-align:center;">
//								'.$basicInformation.'
//								</div> 
//								<div class="div-table-col-5"  style="text-align:center; ">
//								 '.$detailInformation.'
//								</div> 
//							</div>
//					</div>';
//				  
//		$detail .= '<div style="clear:both;"></div>';	
//	
//	
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
