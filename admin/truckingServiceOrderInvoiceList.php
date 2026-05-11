<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('TruckingServiceOrderInvoice.class.php');
$truckingServiceOrderInvoice = createObjAndAddToCol(new TruckingServiceOrderInvoice());


$obj = $truckingServiceOrderInvoice;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'truckingServiceOrderInvoiceForm';
 
		
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Gudang', $obj->tableWarehouse . '.name'));
array_push($arrSearchColumn, array('Total', $obj->tableName . '.grandtotal'));
array_push($arrSearchColumn, array('Total', $obj->tableName . '.totaldownpayment'));
array_push($arrSearchColumn, array('Total', $obj->tableName . '.outstanding'));
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer . '.name')); 
array_push($arrSearchColumn, array('SI', $obj->tableName . '.donumber')); 
array_push($arrSearchColumn, array('No. Booking', $obj->tableName . '.shipmentnumber')); 
array_push($arrSearchColumn, array('Kode SO', $obj->tableName . '.salesordercodecache')); 
array_push($arrSearchColumn, array('Consignee', $obj->tableConsignee . '.name')); 
array_push($arrSearchColumn, array('Biaya', $obj->tableItem . '.name')); 
 

function generateQuickView($obj,$id){ 
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id); 
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
    $rsDetailItem = $obj->getItemDetail($id);
	  
    $salesOrderInvoiceReceipt = new SalesOrderInvoiceReceipt();
    $rsReceipt = $salesOrderInvoiceReceipt->getInvoiceReceipt($id,' and '.$salesOrderInvoiceReceipt->tableName.'.statuskey in (2,3) ');
    
    $invoiceReceiptInformation = '';
        
    if(!empty($rsReceipt)){
        $invoiceReceiptInformation  .= '
                                        <div class="data-card border-blue">
                                        <h1>'.ucwords($obj->lang['invoiceReceipt']).'</h1> 
                                        <div class="content">
                                        <div class="div-table  general-information-table">
                                                <div class="div-table-row">
                                                    <div class="div-table-col">'.ucwords($obj->lang['code']).'</div> 
                                                    <div class="div-table-col">'. $rsReceipt[0]['code'] .'</div> 
                                                </div> 
                                                <div class="div-table-row">
                                                    <div class="div-table-col">'.ucwords($obj->lang['date']).'</div> 
                                                    <div class="div-table-col">'. $obj->formatDBDate($rsReceipt[0]['trdate']).'</div> 
                                                </div> 
                                                <div class="div-table-row">
                                                    <div class="div-table-col">'.ucwords($obj->lang['recipient']).'</div> 
                                                    <div class="div-table-col">'.$rsReceipt[0]['recipientname'].'</div> 
                                                </div>
                                        </div>
                                        </div>
                                        </div>
                                        ';
    }
 
    
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
                                                <div class="div-table-col detail-col-header" style="text-align:left;  width: 150px">'
                                                    .ucwords($obj->lang['JOCode']).
                                                '</div>
                                                
                                                <div class="div-table-col detail-col-header" style="text-align:center;  width: 100px">'
                                                    .ucwords($obj->lang['date']).
                                                '</div>
                                                <div class="div-table-col detail-col-header" style="text-align:left;  width: 130px">'
                                                    .ucwords($obj->lang['si']).
                                                '</div>
                                                <div class="div-table-col detail-col-header" style="text-align:left;">'
                                                    .ucwords($obj->lang['consignee']).
                                                '</div> 
                                                <div class="div-table-col detail-col-header" style="text-align:right;  width: 100px">'
                                                    .ucwords($obj->lang['total']).
                                                '</div>
                                            </div>
                                   ';
		for ($i=0;$i<count($rsDetail);$i++){ 
            
             $itemname = '';
             $trdate  = '';
             if ( $rsDetail[$i]['invoicetype'] == 1) { 
                 $itemname = $rsDetail[$i]['socode'];
                 $trdate =  $obj->formatDBDate($rsDetail[$i]['sodate']);
             }else { 
                $itemname = $rsDetail[$i]['itemname'];
             }
            
		     $detailInformation  .= '   <div class="div-table-row">  
                                                <div class="div-table-col" style="text-align:left;">'.$itemname.'</div> 
                                                <div class="div-table-col" style="text-align:center;">'.$trdate.'</div>  
                                                <div class="div-table-col" style="text-align:left;">'.$rsDetail[$i]['donumber'].'</div>  
                                                <div class="div-table-col" style="text-align:left;">'.$rsDetail[$i]['consigneename'].'</div>   
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
				  
        $detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5">
                                    '.getJournal($obj,$id).'
                                </div>
                            </div>
                    </div>';
		$detail .= '<div style="clear:both;"></div>';	

 
	return $detail;  
}
  

// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>