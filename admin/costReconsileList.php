<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('CostReconsile.class.php'));
$costReconsile = createObjAndAddToCol( new CostReconsile()); 
$emklOrderInvoice = createObjAndAddToCol( new EMKLOrderInvoice()); 
//$service = createObjAndAddToCol( new Service(SERVICE)); 

$obj = $costReconsile;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'costReconsileForm';
function generateQuickView($obj,$id){  
	$prepaidExpense = $obj->getPrepaidExpenseObj();
	$emklOrderInvoice = new EMKLOrderInvoice(); 
    
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);   
    
      
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>'. ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table  general-information-table">
							<div class="div-table-row">
								<div class="div-table-col" style="width:40%">'. ucwords($obj->lang['status']).'</div> 
								<div class="div-table-col">'.$rs[0]['statusname'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['code']).'</div> 
								<div class="div-table-col">'.$rs[0]['code'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['date']).'</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['trdate']).'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['warehouse']).'</div> 
								<div class="div-table-col">'. $rs[0]['warehousename'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['outstanding']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['grandtotal']).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['note']).'</div> 
								<div class="div-table-col">'.$rs[0]['trnotes'].'</div> 
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
									<div class="div-table-col detail-col-header" style="width: 120px">'. ucwords($obj->lang['code']).'</div> 
									<div class="div-table-col detail-col-header" style="width: 120px">'. ucwords($obj->lang['reference']).'</div>
									<div class="div-table-col detail-col-header" style="text-align:center; width: 100px">'. ucwords($obj->lang['refDate']).'</div> 
									<div class="div-table-col detail-col-header" >'. ucwords($obj->lang['service']).'</div>
									<div class="div-table-col detail-col-header" style="text-align:right;width: 90px">'. ucwords($obj->lang['amount']).'</div>
								</div>';
    
	
 		$rsDetail = $obj->getDetailWithRelatedInformation($id);
		for ($i=0;$i<count($rsDetail);$i++){
			  
			$detailInformation  .= '
				<div class="div-table-row"> 
					<div class="div-table-col">'.$rsDetail[$i]['pecode'].'</div>  
					<div class="div-table-col">'.$rsDetail[$i]['refcode'].'</div>  
                    <div class="div-table-col" style="text-align:center;">'.$obj->formatDBDate($rsDetail[$i]['podate']).'</div>  
					<div class="div-table-col">'.$rsDetail[$i]['servicename'].'</div>  
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['amount']).'</div>
				</div>
			';
		}
								
		$detailInformation  .= ' </div>
						</div>
					</div>  
		'; 	
    
    		$invoiceInformation  = ' <div class="data-card border-blue">
						<h1>'. ucwords($obj->lang['invoiceDetail']).'</h1> 
						<div class="content">
						<div class="div-table quick-view-table">
							  <div class="div-table-row"> 
									<div class="div-table-col detail-col-header" style="width: 40px;text-align:right;">'. ucwords($obj->lang['qty']).'</div> 
									<div class="div-table-col detail-col-header" >'. ucwords($obj->lang['service']).'</div> 
									<div class="div-table-col detail-col-header" style="text-align:center">'. ucwords($obj->lang['curr']).'</div> 
									<div class="div-table-col detail-col-header" style="width: 120px;text-align:right;">'. ucwords($obj->lang['amount']).'</div>
								</div>';

    	 
		$rsDetailItemInvoice = $emklOrderInvoice->getItemDetail($rs[0]['refkey'],'refheaderkey');
    
		for ($j=0;$j<count($rsDetailItemInvoice);$j++){

            $decimalPrice = ($rsDetailItemInvoice[0]['headercurrencykey'] == CURRENCY['idr'] ) ? 0 : 2;  
			$invoiceInformation  .= '
				<div class="div-table-row"> 
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetailItemInvoice[$j]['qtyinbaseunit'],$decimalPrice).'</div>  
                    <div class="div-table-col">'.$rsDetailItemInvoice[$j]['itemname'].'</div>  
                    <div class="div-table-col"  style="text-align:center">'.$rsDetailItemInvoice[$j]['headercurrencyname'].'</div>  
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetailItemInvoice[$j]['total'],$decimalPrice).'</div>
				</div>
			';
		}
								
		$invoiceInformation  .= ' </div>
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
								 '.$invoiceInformation.'
								</div>  
							</div>
					</div>';
				  
		$detail .= '<div style="clear:both;"></div>';	
		 
	 
	return $detail;  
}
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
