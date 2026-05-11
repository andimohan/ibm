<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array('EMKLOrderInvoice.class.php'));

$emklOrderInvoice = new EMKLOrderInvoice();
$currency = new Currency();
$obj = $emklOrderInvoice;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'emklOrderInvoiceForm';
		
 
function generateQuickView($obj,$id){ 
	$detail = '';
	$currency = new Currency();
	$rs = $obj->searchData($obj->tableName .'.pkey',$id); 
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
	$rsCurrency = $currency->getDataRowById($rs[0]['currencykey']);
	$cur = $rsCurrency[0]['name'];
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
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['invoiceAmount']).'</div> 
                                        <div class="div-table-col">'.$obj->formatnumber($rs[0]['grandtotal']).'</div> 
                                    </div>
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['downpayment']).'</div> 
                                        <div class="div-table-col">'.$obj->formatnumber($rs[0]['totaldownpayment']).'</div> 
                                    </div>
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['invoiceOutstanding']).'</div> 
                                        <div class="div-table-col">'.$obj->formatnumber($rs[0]['outstanding']).'</div> 
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
                                                    .ucwords($obj->lang['JOCode']).
                                                '</div>
                                                <div class="div-table-col detail-col-header" style="text-align:center;  width: 100px">'
                                                    .ucwords($obj->lang['date']).
                                                '</div>
                                                <div class="div-table-col detail-col-header" style="text-align:left;  width: 100px">'
                                                    .ucwords($obj->lang['hbl']).
                                                '</div>
                                                <div class="div-table-col detail-col-header" style="text-align:left;">'
                                                    .ucwords($obj->lang['description']).
                                                '</div>  
                                                <div class="div-table-col detail-col-header" style="text-align:right;  width: 100px">'
                                                    .ucwords($obj->lang['total']).
                                                ' <span class="text-muted">'.$cur.'</span></div>
                                            </div>
                                   ';
		for ($i=0;$i<count($rsDetail);$i++){ 
            
             $itemname = '';
             $hbl = '';
             $trdate  = '';
             if ( $rsDetail[$i]['invoicetype'] == 1) { 
                 $itemname = $rsDetail[$i]['socode'];
                 $trdate =  $obj->formatDBDate($rsDetail[$i]['sodate']);
                 $hbl =  $rsDetail[$i]['hbl'];
             }else if($rsDetail[$i]['invoicetype'] == 2) { 
                $itemname = $rsDetail[$i]['itemname'];
             }else {
                $rsInvoice = $obj->getDataRowById($rsDetail[$i]['invoicekey']);
                 $itemname = $rsInvoice[0]['code'];
             }
            
		     $detailInformation  .= '   <div class="div-table-row">  
                                                <div class="div-table-col" style="text-align:left;">'.$itemname.'</div> 
                                                <div class="div-table-col" style="text-align:center;">'.$trdate.'</div>  
                                                <div class="div-table-col" style="text-align:left;">'.$hbl.'</div>  
                                                <div class="div-table-col" style="text-align:left;">'.$rsDetail[$i]['description'].'</div>   
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
		 //$detail = '';
	 
	return $detail;  
}


	 

// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
