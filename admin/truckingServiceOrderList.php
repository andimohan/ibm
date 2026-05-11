<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('TruckingServiceOrder.class.php');
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());

$obj = $truckingServiceOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'truckingServiceOrderForm';
 


function generateQuickView($obj,$id){
		$partyDecimal = $obj->loadSetting('jobOrderPartyDecimal'); // harusnya aman karena sudah load dulu diawal
		if (empty($partyDecimal)) $partyDecimal = 0;
	
		if (!empty($partyDecimal)){
			$qtyWidth = '80px'; 
		}else{
			$qtyWidth = '60px'; 
		}
	
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id);   
 	$rsDetail = $obj->getDetailWithRelatedInformation($id);
    $rsInvoice = $obj->getInvoiceInformation($id);
        
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
								<div class="div-table-col">'.ucwords($obj->lang['cargoType']).'</div> 
								<div class="div-table-col">'.$rs[0]['cargotype'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['bookingNumber']).'</div> 
								<div class="div-table-col">'.$rs[0]['shipmentnumber'].'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col"></div> 
								<div class="div-table-col" style="height: 1em"></div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['depot']).'</div> 
								<div class="div-table-col">'.$rs[0]['depotname'].'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['terminal']).'</div> 
								<div class="div-table-col">'.$rs[0]['terminalname'].'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['location']).'</div> 
								<div class="div-table-col">'.$rs[0]['locationname'].'</div> 
							</div>    
							<div class="div-table-row">
								<div class="div-table-col"></div> 
								<div class="div-table-col" style="height: 1em"></div> 
							</div>  
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['note']).'</div> 
								<div class="div-table-col">'.$rs[0]['trdesc'].'</div> 
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
									<div class="div-table-col detail-col-header" style="text-align:right;  width: 30px">#</div>
									<div class="div-table-col detail-col-header" style="text-align:right;  width: '.$qtyWidth.'">'.ucwords($obj->lang['party']).'</div>
									<div class="div-table-col detail-col-header">'.ucwords($obj->lang['container']).'</div>
									<div class="div-table-col detail-col-header" style="text-align:center; width:120px">'.ucwords($obj->lang['shippingDate']).'</div>  
									<div class="div-table-col detail-col-header" style="text-align:right; width:70px;">'.ucwords($obj->lang['price']).'</div> 
									<div class="div-table-col detail-col-header" style="text-align:right; width:70px;">'.ucwords($obj->lang['total']).'</div> 
									<div class="div-table-col detail-col-header"  style="width:100px">'.ucwords($obj->lang['status']).'</div> 
                                </div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			/* 
            switch ($rsDetail[$i]['statuskey']) {
                case '1': $rsDetail[$i]['class'] = 'text-green-avocado'; 
                          break;
                    
                case '2' :  $rsDetail[$i]['class'] = 'text-blue-munsell';
                            break; 
            }
		    */
                  
			$detailInformation  .= '
				<div class="div-table-row">  
					<div class="div-table-col" style="text-align:right; ">'.$rsDetail[$i]['numberkey'].'.</div> 
					<div class="div-table-col" style="text-align:right; '.$qtyWidth.'">'.$obj->formatNumber($rsDetail[$i]['qtyinbaseunit'],$partyDecimal).'</div> 
					<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div> 
                    <div class="div-table-col" style="text-align:center;">'.$obj->formatDBDate($rsDetail[$i]['trdate'],'d / m / Y H:i').'</div>     
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</div>
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['total']).'</div>
					<div class="div-table-col"><span class="'.$rsDetail[$i]['class'].'" style="padding:0 0.5em; color: #fff">'.$rsDetail[$i]['statusname'].'</span></div>  
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
            $snInformation .= '<div style=" margin-left:1em; margin-bottom:0.5em"><strong>'.ucwords($obj->lang['containerNumber']).'</strong><br>'.$rs[0]['containernumber'].'</div>';
    
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
