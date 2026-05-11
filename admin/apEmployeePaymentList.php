<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('APPayment.class.php','APEmployeePayment.class.php'));
$apEmployeePayment = createObjAndAddToCol(new APEmployeePayment());
$employee = createObjAndAddToCol(new Employee());

$obj = $apEmployeePayment;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'apEmployeePaymentForm';
  
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate')); 
array_push($arrSearchColumn, array('Karyawan', $obj->tableEmployee. '.name'));
array_push($arrSearchColumn, array('Gudang', $obj->tableWarehouse. '.name'));
array_push($arrSearchColumn, array('Total', $obj->tableName. '.grandtotal')); 

function generateQuickView($obj,$id){ $detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);   
 	$rsDetail = $obj->getDetailWithRelatedInformation($id);
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
								<div class="div-table-col">'. ucwords($obj->lang['employee']).'</div> 
								<div class="div-table-col">'.$rs[0]['employeename'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'. ucwords($obj->lang['total']).'</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['totalpaid']).'</div> 
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
									<div class="div-table-col detail-col-header"  style="width:120px;">'. ucwords($obj->lang['apCode']).'</div> 
									<div class="div-table-col detail-col-header" style="width:120px;text-align:center;">'. ucwords($obj->lang['date']).'</div> 
                                    <div class="div-table-col detail-col-header" style="width:100px;">'. ucwords($obj->lang['refCode']).'</div> 
									<div class="div-table-col detail-col-header" style="width:120px;">'. ucwords($obj->lang['jobOrderCode']).'</div> 
									<div class="div-table-col detail-col-header" style="width:220px;">'. ucwords($obj->lang['customer']).'</div> 
									<div class="div-table-col detail-col-header" style="text-align:right;">'. ucwords($obj->lang['amount']).'</div> 
								</div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
		  
            $refcode = $rsDetail[$i]['refcode'];
            $refdate = $obj->formatDBDate($rsDetail[$i]['refdate']);
              
			$detailInformation  .= '
				<div class="div-table-row">
					<div class="div-table-col">'.$rsDetail[$i]['apcode'].'</div> 
                    <div class="div-table-col" style="text-align:center;">'.$obj->formatDBDate($rsDetail[$i]['refdate'], 'd / m / Y').'</div> 
                    <div class="div-table-col" style="">'.$rsDetail[$i]['refcode'].'</div> 
                    <div class="div-table-col" style="">'.$rsDetail[$i]['reftranscode2'].'</div> 
                    <div class="div-table-col" style="">'.$rsDetail[$i]['customername'].'</div> 
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['amount']).'</div> 
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
						<div class="div-table general-information-table" style="width:200px">';
    
        if(!empty($rs[0]['nettingkey'])){
            $paymentInformation .= '<div class="div-table-row">';
            $paymentInformation .= '<div class="div-table-col" style="width: 45%;">'.$obj->lang['netting'].'</div>';
            $paymentInformation .= '<div class="div-table-col" style="text-align:right">'.$obj->formatNumber($rs[0]['totalpayment']).'</div> '; 
            $paymentInformation .= '</div>'; 
        }else{  
            for ($j=0;$j<count($rsAPPaymentMethodDetail);$j++){ 

                    $paymentInformation .= '<div class="div-table-row">';
                    $paymentInformation .= '<div class="div-table-col" style="width: 50%;">'.$rsAPPaymentMethodDetail[$j]['paymentmethodname'].'</div>';
                    $paymentInformation .= '<div class="div-table-col" style="text-align:right">'.$obj->formatNumber($rsAPPaymentMethodDetail[$j]['amount']).'</div> '; 
                    $paymentInformation .= '</div>'; 
            }  
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
