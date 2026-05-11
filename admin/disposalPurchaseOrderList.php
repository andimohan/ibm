<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass('DisposalPurchaseOrder.class.php');
$disposalPurchaseOrder = createObjAndAddToCol(new DisposalPurchaseOrder());

$obj = $disposalPurchaseOrder;

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'disposalPurchaseOrderForm';


function generateQuickView($obj,$id){ 
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id); 
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
	 
    $invoiceReceiptInformation = '';
    
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
                                        <div class="div-table-col">'.ucwords($obj->lang['date']).'</div> 
                                        <div class="div-table-col">'.$obj->formatDBDate($rs[0]['trdate']).'</div> 
                                    </div>
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['warehouse']).'</div> 
                                        <div class="div-table-col">'.$rs[0]['warehousename'].'</div> 
                                    </div> 
                                    <div class="div-table-row" style="height:20px">
                                    </div> 
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['supplier']).'</div> 
                                        <div class="div-table-col">'.$rs[0]['suppliername'].'</div> 
                                    </div>
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['workOrderDispatcherCode']).'</div> 
                                        <div class="div-table-col">'.$rs[0]['dispatchcode'].'</div> 
                                    </div>
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['invoiceReference']).'</div> 
                                        <div class="div-table-col">'.$rs[0]['refinvoicecode'].'</div> 
                                    </div>
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['totalWeight']).'</div> 
                                        <div class="div-table-col">'.$obj->formatnumber($rs[0]['totalweight'], 2).'</div> 
                                    </div>
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['total']).'</div> 
                                        <div class="div-table-col">'.$obj->formatnumber($rs[0]['grandtotal']).'</div> 
                                    </div>
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['note']).'</div> 
                                        <div class="div-table-col">'.$rs[0]['trdesc'].'</div> 
                                    </div>
                                </div>
                                </div>
                            </div>  
		'; 	
    
        $basicInformation .=  $invoiceReceiptInformation;
     
        $detailInformation  = ' <div class="data-card border-green">
                                    <h1>'.ucwords($obj->lang['detail']).'</h1> 
                                    <div class="content">
                                    <div class="div-table  quick-view-table">
                                            <div class="div-table-row">  
                                                <div class="div-table-col detail-col-header" style="text-align:left;">'
                                                    .ucwords($obj->lang['waste']).
                                                '</div>
                                                
                                                <div class="div-table-col detail-col-header" style="text-align:right;  width: 100px">'
                                                    .ucwords($obj->lang['weight']).
                                                '</div>
                                                <div class="div-table-col detail-col-header" style="text-align:right;  width: 100px">'
                                                    .ucwords($obj->lang['price']).
                                                '</div>
                                                <div class="div-table-col detail-col-header" style="text-align:right;  width: 100px">'
                                                    .ucwords($obj->lang['subtotal']).
                                                '</div>
                                            </div>
                                   ';
		for ($i=0;$i<count($rsDetail);$i++){ 
            
            
		     $detailInformation  .= '   <div class="div-table-row">  
                                                <div class="div-table-col" style="text-align:left;">'.$rsDetail[$i]['waste'].'</div> 
                                                <div class="div-table-col" style="text-align:right;">'.$obj->formatnumber($rsDetail[$i]['weightdetail'], 2).'</div>
                                                <div class="div-table-col" style="text-align:right;">'.$obj->formatnumber($rsDetail[$i]['priceinunit']).'</div>
                                                <div class="div-table-col" style="text-align:right;">'.$obj->formatnumber($rsDetail[$i]['total']).'</div>
                                            </div>';
                                            
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