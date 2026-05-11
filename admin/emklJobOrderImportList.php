<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('EMKLJobOrder.class.php');
$emklJobOrderImport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['import']));
$customer = createObjAndAddToCol(new Customer());

$obj = $emklJobOrderImport; 
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
  
    
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'emklJobOrderImportForm';

function generateQuickView($obj,$id){ 
  	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id);   
 	$rsDetail = $obj->getDetailWithRelatedInformation($id);
    $rsInvoice = $obj->getInvoiceInformation($id);
	  
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>'.ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table  general-information-table">
							<div class="div-table-row">
								<div class="div-table-col" style="width:40%">'.ucwords($obj->lang['status']).'</div> 
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
								<div class="div-table-col">'.ucwords($obj->lang['jobType']).'</div> 
								<div class="div-table-col">'.$rs[0]['jobtype'].' , '.$rs[0]['transportationtype'].' - '.$rs[0]['loadcontainertype'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['bookingNumber']).'</div> 
								<div class="div-table-col">'.$rs[0]['bookingnumber'].'</div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">POL / POD</div> 
								<div class="div-table-col">'.$rs[0]['polname'].' - '.$rs[0]['podname'].'</div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">ETD / ETA</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['etdpol']).' &bull; '.$obj->formatDBDate($rs[0]['etapod']).'</div> 
							</div>  
                            <div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['carrier']).'</div> 
								<div class="div-table-col">'.$rs[0]['carriername'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['vessel']).'</div> 
								<div class="div-table-col">'.$rs[0]['vesselname'].' - '.$rs[0]['vesselnumber'].'</div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['agent']).'</div> 
								<div class="div-table-col">'.$rs[0]['agentname'].'</div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['termofpayment']).'</div> 
								<div class="div-table-col">'.$rs[0]['topname'].'</div> 
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
									<div class="div-table-col detail-col-header" style="width:80px">'.ucwords($obj->lang['code']).'</div>
									<div class="div-table-col detail-col-header">'.ucwords($obj->lang['invoiceTo']).'</div>  
									<div class="div-table-col detail-col-header" style=" width:120px;">'.ucwords($obj->lang['hbl']).'</div>  
									<div class="div-table-col detail-col-header" style="text-align:right; width:70px;">'.ucwords($obj->lang['total']).' <span class="text-muted">IDR</span></div> 
									<div class="div-table-col detail-col-header" style="width:120px;">'.ucwords($obj->lang['description']).'</div> 
                                </div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			 
			$detailInformation  .= '
				<div class="div-table-row">  
					<div class="div-table-col">'.$rsDetail[$i]['code'].'</div> 
                    <div class="div-table-col" style=" ">'.$rsDetail[$i]['customername'].'</div> 
                    <div class="div-table-col" style=" ">'.$rsDetail[$i]['hbl'].'</div>  
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['subtotal']).'</div>
                    <div class="div-table-col" style="">'.str_replace(chr(13),'<br>',$rsDetail[$i]['description']).'</div>
				</div>
			';
		}
								
		$detailInformation  .= ' </div>
						</div>
					</div>  
		'; 	
		
        $detailInvoice = '';
        
        if (!empty($rsInvoice)){
            $detailInvoice  .= ' <div class="data-card no-border">
						<h1>'.ucwords($obj->lang['invoice']).'</h1> 
						<div class="content">
						<div class="div-table  quick-view-table">
							     <div class="div-table-row">  
									<div class="div-table-col detail-col-header" style="text-align:right;  width: 30px">#</div>
									<div class="div-table-col detail-col-header" style="width:120px">'.ucwords($obj->lang['invoiceNumber']).'</div>
									<div class="div-table-col detail-col-header" style="text-align:center; width:120px">'.ucwords($obj->lang['date']).'</div>  
									<div class="div-table-col detail-col-header"></div>  
                                </div>';
								
            for ($i=0;$i<count($rsInvoice);$i++){ 
                $detailInvoice  .= '
                    <div class="div-table-row">  
                        <div class="div-table-col" style="text-align:right; ">'.($i+1).'.</div> 
                        <div class="div-table-col">'.$rsInvoice[$i]['code'].'</div> 
                        <div class="div-table-col" style="text-align:center;">'.$obj->formatDBDate($rsInvoice[$i]['trdate']).'</div>  
                        <div class="div-table-col"></div> 
                    </div>
                ';
            }

            $detailInvoice  .= ' </div>
                            </div>
                        </div>  
            ';
        }
        

        $snInformation = '';
    
        if(!empty($rs[0]['containernumber']))
            $snInformation .= '<div style=" margin-left:1em; margin-bottom:0.5em"><strong>'.ucwords($obj->lang['containerNumber']).'</strong><br>'.str_replace(chr(13),', ',$rs[0]['containernumber']).'</div>';
    
		$detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5" style="width:25%;">
								'.$basicInformation.'
								</div> 
								<div class="div-table-col-5">
								 '.$detailInformation.'
								 '.$snInformation.'
                                 '.$detailInvoice.' 
								</div>  
							</div>
					</div>';
				  
		$detail .= '<div style="clear:both;"></div>';	
		 
	 
	return $detail;  
	
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
