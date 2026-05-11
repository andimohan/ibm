<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('ARPayment.class.php');
$arPayment = createObjAndAddToCol( new ARPayment()); 

$salesOrder = createObjAndAddToCol( new SalesOrder()); 
$truckingServiceOrderInvoice = createObjAndAddToCol( new TruckingServiceOrderInvoice()); 
$emklOrderInvoice = createObjAndAddToCol( new EMKLOrderInvoice()); 
$salesOrderProperty = createObjAndAddToCol( new SalesOrderProperty()); 

$obj = $arPayment;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'arPaymentForm';
 

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code')); 
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate')); 
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer. '.name'));
array_push($arrSearchColumn, array('Gudang', $obj->tableWarehouse. '.name'));
array_push($arrSearchColumn, array('Total', $obj->tableName. '.totalreceived')); 
array_push($arrSearchColumn, array('Total', $obj->tableName. '.prepaidtax23')); 
array_push($arrSearchColumn, array('Total', $obj->tableName. '.grandtotal')); 
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trnotes')); 
array_push($arrSearchColumn, array('JO Code', $obj->tableName. '.salesordercodecache')); 
 
 
function generateQuickView($obj,$id){  
	$ar = $obj->getARObj();
	    
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);   
    
    $decimalPrice = ($rs[0]['currencykey'] == CURRENCY['idr'] ) ? 0 : 2;  
     
 	$rsDetail = $obj->getDetailById($id);
    $rsARPaymentMethodDetail = $obj->getPaymentMethodDetail($id);
    $rsARDP = $obj->getDownpaymentDetail($id);
	   
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
								<div class="div-table-col">'. ucwords($obj->lang['customer']).'</div> 
								<div class="div-table-col">'.$rs[0]['customername'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['payingOffAmount']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['totalreceived'],$decimalPrice).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['discount']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['totaldiscount'],$decimalPrice).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['tax23']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['prepaidtax23'],$decimalPrice).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['downpayment']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['totaldownpayment'],$decimalPrice).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['outstanding']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['grandtotal'],$decimalPrice).'</div> 
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
									<div class="div-table-col detail-col-header" style="width: 120px">'. ucwords($obj->lang['arCode']).'</div> 
									<div class="div-table-col detail-col-header" style="width: 120px">'. ucwords($obj->lang['invoiceCode']).'</div>';

        	$detailInformation  .= ' <div class="div-table-col detail-col-header" style="width: 120px">'. ucwords($obj->lang['reference']).'</div>';
            $detailInformation  .= ' <div class="div-table-col detail-col-header" style="text-align:center; width: 100px">'. ucwords($obj->lang['date']).'</div> 
									<div class="div-table-col detail-col-header" style="text-align:right;">'. ucwords($obj->lang['amount']).'</div>
									<div class="div-table-col detail-col-header" style="text-align:right; width: 100px">'. ucwords($obj->lang['tax23']).'</div>
								</div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			
			$rsAr= $ar->getDataRowById($rsDetail[$i]['arkey']);  
            
			$detailInformation  .= '
				<div class="div-table-row"> 
					<div class="div-table-col">'.$rsAr[0]['code'].'</div>  
                    <div class="div-table-col">'.$rsAr[0]['refcode'].'</div> ';
            
             	$detailInformation  .= ' <div class="div-table-col">'.$rsAr[0]['refcode2'].'</div> ';
            
              	$detailInformation  .= ' <div class="div-table-col" style="text-align:center;">'.$obj->formatDBDate($rsAr[0]['trdate']).'</div>  
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['amount'],$decimalPrice).'</div>
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['taxamount'],$decimalPrice).'</div>
				</div>
			';
		}
								
		$detailInformation  .= ' </div>
						</div>
					</div>  
		'; 	
		
        $paymentInformation  = ' <div class="data-card border-blue">
						<h1>'. ucwords($obj->lang['paymentMethod']).'</h1> 
						<div class="content" style="height:auto;">
						<div class="div-table  general-information-table" style="width:200px">';
    
        if(!empty($rs[0]['nettingkey'])){
            $paymentInformation .= '<div class="div-table-row">';
            $paymentInformation .= '<div class="div-table-col" style="width: 45%;">'.$obj->lang['netting'].'</div>';
            $paymentInformation .= '<div class="div-table-col" style="text-align:right">'.$obj->formatNumber($rs[0]['totalpayment'],$decimalPrice).'</div> '; 
            $paymentInformation .= '</div>'; 
        }else{ 
            for ($j=0;$j<count($rsARDP);$j++){  
                    $paymentInformation .= '<div class="div-table-row">';
                    $paymentInformation .= '<div class="div-table-col" style="width: 45%;">'.$rsARDP[$j]['refcode'].'</div>';
                    $paymentInformation .= '<div class="div-table-col" style="text-align:right">'.$obj->formatNumber($rsARDP[$j]['amount'],$decimalPrice).'</div> '; 
                    $paymentInformation .= '</div>'; 
            } 

            for ($j=0;$j<count($rsARPaymentMethodDetail);$j++){ 

                    $paymentInformation .= '<div class="div-table-row">';
                    $paymentInformation .= '<div class="div-table-col" style="width: 45%;">'.$rsARPaymentMethodDetail[$j]['paymentmethodname'].'</div>';
                    $paymentInformation .= '<div class="div-table-col" style="text-align:right">'.$obj->formatNumber($rsARPaymentMethodDetail[$j]['amount'],$decimalPrice).'</div> '; 
                    $paymentInformation .= '</div>'; 
            } 
        }
    
		$paymentInformation  .= ' 
						</div>
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
                                 '.$paymentInformation.' 
								</div>  
							</div>
					</div>';
				  
		$detail .= '<div style="clear:both;"></div>';	
		 
	 
	return $detail;  
}
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
