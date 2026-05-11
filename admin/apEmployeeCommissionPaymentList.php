<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('APEmployeeCommissionPayment.class.php','Item.class.php'));
$apEmployeeCommissionPayment = new APEmployeeCommissionPayment();

$obj = $apEmployeeCommissionPayment;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'apEmployeeCommissionPaymentForm';

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));  
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate')); 
array_push($arrSearchColumn, array('Karyawan', $obj->tableEmployee. '.name'));
array_push($arrSearchColumn, array('Gudang', $obj->tableWarehouse. '.name'));
array_push($arrSearchColumn, array('Total', $obj->tableName. '.totalpaid')); 
 
function generateQuickView($obj,$id){ 
    $purchaseOrder = new PurchaseOrder();
	$ap = $obj->getAPObj(); 
	    
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);   
 	$rsDetail = $obj->getDetailById($id);
    $rsAPPaymentMethodDetail = $obj->getPaymentMethodDetail($id);
	   
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>'. ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table general-information-table">
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
								<div class="div-table-col">'. ucwords($obj->lang['supplier']).'</div> 
								<div class="div-table-col">'.$rs[0]['employeename'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['total']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['totalpayment']).'</div> 
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
									<div class="div-table-col detail-col-header"  style="width:120px;">'. ucwords($obj->lang['apCode']).'</div>';
     
            switch (PLAN_TYPE['categorykey']){
                case COMPANY_TYPE['trucking'] :   $detailInformation .= '<div class="div-table-col detail-col-header"  style="width:120px;">'. ucwords($obj->lang['WOCode']).'</div> 
                                                   <div class="div-table-col detail-col-header"  style="width:120px;">'. ucwords($obj->lang['JOCode']).'</div>';
												break;
                default :
                            $detailInformation .= '<div class="div-table-col detail-col-header"  style="width:120px;">'. ucwords($obj->lang['refCode']).'</div>';
            }
 
        
        	$detailInformation  .= ' 
									<div class="div-table-col detail-col-header" style="text-align:right;">'. ucwords($obj->lang['amount']).'</div>
									<div class="div-table-col detail-col-header" style="text-align:right; width:100px;">'. ucwords($obj->lang['tax23']).'</div>
								</div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			
			$rsAp= $ap->getDataRowById($rsDetail[$i]['apkey']);  
            $rsPO = $purchaseOrder->getDataRowById($rsAp[0]['refheaderkey']); 
            $refcode = '';
		    $refdate = '';
            if (!empty($rsPO)) {
                $refcode = $rsPO[0]['code'];
                $refdate = $obj->formatDBDate($rsPO[0]['trdate']);
            }
            
			$detailInformation  .= '
				<div class="div-table-row"> 
					<div class="div-table-col">'.$rsAp[0]['code'].'</div>
                    <div class="div-table-col">'.$rsAp[0]['refcode'].'</div>  
                    ';
            
	    
            if(PLAN_TYPE['categorykey'] == COMPANY_TYPE['trucking'])
                $detailInformation .= ' <div class="div-table-col">'.$rsAp[0]['refcode2'].'</div>  ';

			$detailInformation  .= '
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['amount']).'</div>
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['taxamount']).'</div>
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
						<div class="div-table general-information-table" style="width:150px">';
    
        for ($j=0;$j<count($rsAPPaymentMethodDetail);$j++){ 

                $paymentInformation .= '<div class="div-table-row">';
                $paymentInformation .= '<div class="div-table-col" style="width: 50%;">'.$rsAPPaymentMethodDetail[$j]['paymentmethodname'].'</div>';
                $paymentInformation .= '<div class="div-table-col" style="text-align:right">'.$obj->formatNumber($rsAPPaymentMethodDetail[$j]['amount']).'</div> '; 
                $paymentInformation .= '</div>'; 
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
