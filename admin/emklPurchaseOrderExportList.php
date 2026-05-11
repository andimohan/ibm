<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('EMKLPurchaseOrder.class.php'));
$emklPurchaseOrderExport = createObjAndAddToCol(new EMKLPurchaseOrder(EMKL['jobType']['export']));
$customer = createObjAndAddToCol(new Customer());
$emklJobOrderHeaderExport = createObjAndAddToCol(new EMKLJobOrderHeader(EMKL['jobType']['export']));
$emklJobOrderExport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['export']));
$supplier = createObjAndAddToCol(new Supplier());

$obj = $emklPurchaseOrderExport;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'emklPurchaseOrderExportForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));  
array_push($arrSearchColumn, array('Supplier', $supplier->tableName. '.name')); 
array_push($arrSearchColumn, array('Customer', $customer->tableName. '.name')); 
array_push($arrSearchColumn, array(ucwords($obj->lang['invoiceReference']), $obj->tableName. '.refinvoicecode')); 
array_push($arrSearchColumn, array('Job Order', $emklJobOrderExport->tableName. '.code')); 
array_push($arrSearchColumn, array('Job Order', $emklJobOrderHeaderExport->tableName. '.code')); 
array_push($arrSearchColumn, array('Container', $emklJobOrderExport->tableName. '.containernumber')); 
array_push($arrSearchColumn, array('MBL', $emklJobOrderExport->tableName. '.mblnumber')); 
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trdesc'));
array_push($arrSearchColumn, array('Realisasi Kasbon', $obj->tableCashAdvanceRealization. '.code')); 
 
 
function generateQuickView($obj,$id){ 
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id);   
 	$rsDetail = $obj->getDetailWithRelatedInformation($id);
        
	if ($rs[0]['finaldiscounttype'] == 2)
		$rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
	  
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>'.ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table  general-information-table">
							<div class="div-table-row">
								<div class="div-table-col" style="width:50%">'.ucwords($obj->lang['status']).'</div> 
								<div class="div-table-col">'.$rs[0]['statusname'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['code']).'</div> 
								<div class="div-table-col">'.$rs[0]['code'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['bookingDate']).'</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['trdate']).'</div> 
							</div> 
                            <div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['warehouse']).'</div> 
                                <div class="div-table-col">'.$rs[0]['warehousename'].'</div> 
                            </div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['invoiceReference']).'</div> 
								<div class="div-table-col">'.$rs[0]['refinvoicecode'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['soCode']).'</div> 
								<div class="div-table-col">'.$rs[0]['jocode'].'</div> 
							</div>
                            <div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['jobType']).'</div> 
								<div class="div-table-col">'.$rs[0]['jobtype'].' , '.$rs[0]['transportationtype'].' - '.$rs[0]['loadcontainertype'].'</div> 
							</div>
                            <div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['bookingNumber']).'</div> 
								<div class="div-table-col">'.$rs[0]['bookingnumber'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col"></div> 
								<div class="div-table-col" style="height: 1em"></div> 
							</div>  
                            <div class="div-table-row">
								<div class="div-table-col">POL / POD</div> 
								<div class="div-table-col">'.$rs[0]['polname'].' - '.$rs[0]['podname'].'</div> 
							</div> 
                               <div class="div-table-row">
								<div class="div-table-col">ETD</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['etdpol']).'</div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">ETA</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['etapod']).'</div> 
							</div>  
							<div class="div-table-row">
								<div class="div-table-col"></div> 
								<div class="div-table-col" style="height: 1em"></div> 
							</div>  
                            <div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['note']).'</div> 
                                <div class="div-table-col">'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</div> 
                            </div> 

						</div>
						</div>
					</div>  
		'; 	
		
		$detailInformation  = ' <div class="data-card border-green">
						<h1>'.ucwords($obj->lang['detail']).'</h1> 
						<div class="content">
						<div class="div-table  quick-view-table">
							    <div class="div-table-row">  
									<div class="div-table-col detail-col-header" style="width:50px">'.ucwords($obj->lang['container']).'</div>
                                    <div class="div-table-col detail-col-header" style="text-align:right; width:80px">'.ucwords($obj->lang['qty']).'</div>
									<div class="div-table-col detail-col-header" style="text-align:left; width:100px">' . ucwords($obj->lang['unit']) . '</div>
									<div class="div-table-col detail-col-header" >'.ucwords($obj->lang['service']).'</div>  
									<div class="div-table-col detail-col-header" style="text-align:center; width:70px">'.ucwords($obj->lang['curr']).'</div>  
									<div class="div-table-col detail-col-header" style="text-align:right; width:70px;">'.ucwords($obj->lang['price']).'</div> 
									<div class="div-table-col detail-col-header" style="text-align:right; width:100px;">'.ucwords($obj->lang['subtotal']).'</div> 
									<div class="div-table-col detail-col-header" style="text-align:right; width:100px;">PPH</div> 
									<div class="div-table-col detail-col-header" style="text-align:right; width:100px;">'.ucwords($obj->lang['subtotal']).' '.$rs[0]['currencyname'].'</div> 
                                </div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
            $decimalPrice = 2; //($rsDetail[$i]['currencykey'] == CURRENCY['idr'] ) ? 0 : 2;  
			$decimalPriceSubtotal = 2; //($rs[0]['currencykey'] <> $rsDetail[$i]['currencykey'] || $rsDetail[$i]['currencykey'] <> CURRENCY['idr']) ? 2 : 0;

                
			$detailInformation  .= '
				<div class="div-table-row">  
					<div class="div-table-col">'.$rsDetail[$i]['containername'].'</div>
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</div> 
					<div class="div-table-col" style="text-align:left;">' . $rsDetail[$i]['unitname'] . '</div> 
                    <div class="div-table-col">'.$rsDetail[$i]['servicename'].'</div>     
                    <div class="div-table-col" style="text-align:center; ">'.$rsDetail[$i]['currencyname'].'</div>     
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['priceinunit'],$decimalPrice).'</div>
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['subtotalcurrency'],$decimalPrice).'</div>
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['pphamount'],$decimalPrice).'</div>
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['subtotal'],$decimalPriceSubtotal).'</div>
				</div>
			';
		}
								
		$detailInformation  .= ' </div>
						</div>
					</div>  
		'; 	
		

		$detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5" style="width:25%;">
								'.$basicInformation.'
								</div> 
								<div class="div-table-col-5">
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
