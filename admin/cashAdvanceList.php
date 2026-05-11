<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass('CashAdvance.class.php');
$cashAdvance = createObjAndAddToCol( new CashAdvance()); 

$obj = $cashAdvance;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'cashAdvanceForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array($obj->lang['code'], $obj->tableName . '.code')); 
array_push($arrSearchColumn, array($obj->lang['warehouse'], $obj->tableWarehouse . '.name')); 
array_push($arrSearchColumn, array($obj->lang['employee'], $obj->tableEmployee . '.name')); 
array_push($arrSearchColumn, array($obj->lang['note'], $obj->tableName . '.trdesc')); 

function generateQuickView($obj,$id){ 
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id);   
    
    $cashAdvanceRealization = new CashAdvanceRealization();
    
    $rsCashAdvanceRealization = $cashAdvanceRealization->searchData($cashAdvanceRealization->tableName .'.refkey',$id,true,' and '.$cashAdvanceRealization->tableName .'.statuskey in (2,3) ');
        
    $refCode = '';
    $dateCashAdvanceRealization = '';
    $amountCashAdvanceRealization = '';
    $totalCashAdvanceRealization = '';
    $balanceCashAdvanceRealization = '';
    $noteRealization = '';
    
    if(!empty($rsCashAdvanceRealization)){
        
        $refCode = $rsCashAdvanceRealization[0]['code'];
        $dateCashAdvanceRealization  = $obj->formatDbDate($rsCashAdvanceRealization[0]['trdate'],'d / m / Y');
        $amountCashAdvanceRealization = $obj->formatNumber($rsCashAdvanceRealization[0]['amount']);
        $totalCashAdvanceRealization = $obj->formatNumber($rsCashAdvanceRealization[0]['total']);
        $balanceCashAdvanceRealization = $obj->formatNumber($rsCashAdvanceRealization[0]['balance']);
        $noteRealization = str_replace(chr(13),'<br>',$rsCashAdvanceRealization[0]['trdesc']);
    }
    
    
    $rsDetailCashAdvanceRealization = (!empty($rsCashAdvanceRealization)) ? $cashAdvanceRealization->getDetailWithRelatedInformation($rsCashAdvanceRealization[0]['pkey']) : array();
    
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>'.ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table  general-information-table">
							<div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['code']).'</div> 
                                <div class="div-table-col">'.$rs[0]['code'].'</div> 
                            </div>
                            <div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['realizationCode']).'</div> 
                                <div class="div-table-col">'.$refCode.'</div> 
                            </div>
                            <div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['realizationDate']).'</div> 
                                <div class="div-table-col">'.$dateCashAdvanceRealization.'</div> 
                            </div>
							<div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['cashAdvance']).'</div> 
                                <div class="div-table-col">'.$amountCashAdvanceRealization.'</div> 
                            </div>
							<div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['total']).'</div> 
                                <div class="div-table-col">'.$totalCashAdvanceRealization.'</div> 
                            </div>
							<div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['balance']).'</div> 
                                <div class="div-table-col">'.$balanceCashAdvanceRealization.'</div> 
                            </div>
		                    <div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div> 
                            <div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['note']).'</div> 
                                <div class="div-table-col">'.$noteRealization.'</div> 
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
									<div class="div-table-col detail-col-header" >'.ucwords($obj->lang['description']).'</div>   
									<div class="div-table-col detail-col-header" style="width:100px;">'.ucwords($obj->lang['service']).'</div>   
									<div class="div-table-col detail-col-header" style="width:140px;">'.ucwords($obj->lang['supplier']).'</div>   
									<div class="div-table-col detail-col-header" style="width:80px;">'.ucwords($obj->lang['reference']).'</div>   
									<div class="div-table-col detail-col-header" style="text-align:right; width:60;">'.ucwords($obj->lang['qty']).'</div> 
									<div class="div-table-col detail-col-header" style="text-align:right; width:80px;">'.ucwords($obj->lang['subtotal']).'</div> 
                                </div>';
								
		for ($i=0;$i<count($rsDetailCashAdvanceRealization);$i++){
			$detailDesc ='';
			$invoiceReference ='';
			$serviceName = (!empty($rsDetailCashAdvanceRealization[$i]['servicename'])) ? $rsDetailCashAdvanceRealization[$i]['servicename']:'';
			$supplierName = (!empty($rsDetailCashAdvanceRealization[$i]['suppliername'])) ? $rsDetailCashAdvanceRealization[$i]['suppliername']:'';
			
			if($rsDetailCashAdvanceRealization[$i]['cashtypekey']==1){
				$detailDesc = $rsDetailCashAdvanceRealization[$i]['jobordercode'].' - '.$rsDetailCashAdvanceRealization[$i]['containername'];
				$invoiceReference = (!empty($rsDetailCashAdvanceRealization[$i]['refcode'])) ? $rsDetailCashAdvanceRealization[$i]['refcode']:'';
			}else if($rsDetailCashAdvanceRealization[$i]['cashtypekey']==2) {
				$detailDesc = $obj->lang['downpayment'];  
			}else if($rsDetailCashAdvanceRealization[$i]['cashtypekey']==3){
				$detailDesc = $rsDetailCashAdvanceRealization[$i]['coaname'];  
			}else if($rsDetailCashAdvanceRealization[$i]['cashtypekey']==4){
				$detailDesc = $rsDetailCashAdvanceRealization[$i]['jobheadercode'].' - '.$rsDetailCashAdvanceRealization[$i]['containername'];
				$invoiceReference = (!empty($rsDetailCashAdvanceRealization[$i]['refcode'])) ? $rsDetailCashAdvanceRealization[$i]['refcode']:'';
			}
			
			$detailInformation  .= '
				<div class="div-table-row">  
                    <div class="div-table-col">'.$detailDesc.'</div>     
                    <div class="div-table-col">'.$serviceName.'</div>     
                    <div class="div-table-col">'.$supplierName.'</div>     
                    <div class="div-table-col">'.$invoiceReference.'</div>     
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetailCashAdvanceRealization[$i]['qty']).'</div>
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetailCashAdvanceRealization[$i]['subtotal']).'</div>
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
