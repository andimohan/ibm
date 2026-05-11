<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php';  
include '../_include-v2.php'; 

includeClass(array('SalesOrderInvoiceReceipt.class.php'));
$salesOrderInvoiceReceipt = new SalesOrderInvoiceReceipt();

$obj = $salesOrderInvoiceReceipt;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'salesOrderInvoiceReceiptForm';

function generateQuickView($obj,$id){ 
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id); 
    $rsDetail = $obj->getDetailWithRelatedInformation($id); 
	  
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
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['note']).'</div> 
                                        <div class="div-table-col">'.$rs[0]['trdesc'].'</div> 
                                    </div>
                                    <div class="div-table-row" style="height:20px">
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
                                                <div class="div-table-col detail-col-header" style="text-align:left;  width: 150px">'
                                                    .ucwords($obj->lang['invoiceCode']).
                                                '</div>
                                                
                                                <div class="div-table-col detail-col-header" style="text-align:center;  width: 100px">'
                                                    .ucwords($obj->lang['date']).
                                                '</div> 
                                                <div class="div-table-col detail-col-header" style="text-align:right;  width: 100px">'
                                                    .ucwords($obj->lang['total']).
                                                '</div>
                                            </div>
                                   ';
		for ($i=0;$i<count($rsDetail);$i++){ 
            $itemname = $rsDetail[$i]['invoicecode'];
            $trdate =  $obj->formatDBDate($rsDetail[$i]['invoicedate']);
            
		     $detailInformation  .= '   <div class="div-table-row">  
                                                <div class="div-table-col" style="text-align:left;">'.$itemname.'</div> 
                                                <div class="div-table-col" style="text-align:center;">'.$trdate.'</div>    
                                                <div class="div-table-col" style="text-align:right;">'.$obj->formatnumber($rsDetail[$i]['amount']).'</div>
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
