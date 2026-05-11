<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass('CashAdvanceRealization.class.php');
$cashAdvanceRealization = createObjAndAddToCol( new CashAdvanceRealization()); 

$obj = $cashAdvanceRealization;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'cashAdvanceRealizationForm';
  
		
$arrSearchColumn = array ();
array_push($arrSearchColumn, array($obj->lang['code'], $obj->tableName . '.code')); 
array_push($arrSearchColumn, array($obj->lang['warehouse'], $obj->tableWarehouse . '.name')); 
array_push($arrSearchColumn, array($obj->lang['note'], $obj->tableName . '.trdesc')); 
array_push($arrSearchColumn, array($obj->lang['cashAdvance'], $obj->tableName . '.cashadvancecache')); 

function generateQuickView($obj,$id){ 
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id);   
 	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>'.ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table  general-information-table">
							<div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['settlementAccount']).'</div> 
                                <div class="div-table-col">'.$rs[0]['coacodename'].'</div> 
                            </div>
							<div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['cashAdvance']).'</div> 
                                <div class="div-table-col">'.$obj->formatNumber($rs[0]['amount']).'</div> 
                            </div>
							<div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['total']).'</div> 
                                <div class="div-table-col">'.$obj->formatNumber($rs[0]['total']).'</div> 
                            </div>
							<div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['balance']).'</div> 
                                <div class="div-table-col">'.$obj->formatNumber($rs[0]['balance']).'</div> 
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
									<div class="div-table-col detail-col-header" >'.ucwords($obj->lang['JOCode']).'</div>   
									<div class="div-table-col detail-col-header" style="width:80px;" >'.ucwords($obj->lang['container']).'</div>   
									<div class="div-table-col detail-col-header" style="width:100px;">'.ucwords($obj->lang['service']).'</div>   
									<div class="div-table-col detail-col-header" style="width:150px;">'.ucwords($obj->lang['supplier']).'</div>   
									<div class="div-table-col detail-col-header" style="width:80px;">'.ucwords($obj->lang['reference']).'</div>   
									<div class="div-table-col detail-col-header" style="text-align:right; width:60;">'.ucwords($obj->lang['qty']).'</div> 
									<div class="div-table-col detail-col-header" style="text-align:right; width:80px;">'.ucwords($obj->lang['subtotal']).'</div> 
                                </div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			$detailDesc ='';
			$invoiceReference ='';
			$serviceName = (!empty($rsDetail[$i]['servicename'])) ? $rsDetail[$i]['servicename']:'';
			$supplierName = (!empty($rsDetail[$i]['suppliername'])) ? $rsDetail[$i]['suppliername']:'';
			
			if($rsDetail[$i]['cashtypekey']==1){
				$detailDesc = $rsDetail[$i]['jobordercode'];
				$invoiceReference = (!empty($rsDetail[$i]['refcode'])) ? $rsDetail[$i]['refcode']:'';
			}else if($rsDetail[$i]['cashtypekey']==2) {
				$detailDesc = $obj->lang['downpayment'];  
			}else if($rsDetail[$i]['cashtypekey']==3){
				$detailDesc = $rsDetail[$i]['coaname'];  
			}else if($rsDetail[$i]['cashtypekey']==4){
				$detailDesc = $rsDetail[$i]['jobheadercode'];
				$invoiceReference = (!empty($rsDetail[$i]['refcode'])) ? $rsDetail[$i]['refcode']:'';
			}
			
			$detailInformation  .= '
				<div class="div-table-row">  
                    <div class="div-table-col">'.$detailDesc.'</div>    
                    <div class="div-table-col">'.$rsDetail[$i]['containername'].'</div>     
                    <div class="div-table-col">'.$serviceName.'</div>     
                    <div class="div-table-col">'.$supplierName.'</div>     
                    <div class="div-table-col">'.$invoiceReference.'</div>     
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</div>
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['subtotal']).'</div>
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
