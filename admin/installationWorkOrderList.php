<?php  
// ========================================================================== INITIALIZE ==========================================================================
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('InstallationWorkOrder.class.php');   
$installationWorkOrder = createObjAndAddToCol( new InstallationWorkOrder());


$obj = $installationWorkOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'installationWorkOrderForm';
 
		
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code')); 
array_push($arrSearchColumn, array('SC Code', $obj->tableSalesOrder . '.code'));
array_push($arrSearchColumn, array('Media', $obj->tableMedia . '.name'));
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer . '.name'));
array_push($arrSearchColumn, array('Job Details', $obj->tableJob . '.name'));

    
function generateQuickView($obj,$id){ 
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id); 
    $rsDetailEmployee = $obj->getDetailTechnicianWithRelatedInformation($id);
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
                                        <div class="div-table-col">'.ucwords($obj->lang['stagesProcess']).'</div> 
                                        <div class="div-table-col">'.$rs[0]['stagename'].'</div> 
                                    </div>
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['salesman']).'</div> 
                                        <div class="div-table-col">'.$rs[0]['employeename'].'</div> 
                                    </div>
                                </div>
                                </div>
                            </div>  
		'; 	
        $detailFirstInformation  = ' <div class="data-card border-green">
						<h1>'.ucwords($obj->lang['technician']).'</h1> 
						<div class="content">
						<div class="div-table  quick-view-table">
							  <div class="div-table-row">  
                                <div class="div-table-col detail-col-header">'.ucwords($obj->lang['technician']).'</div>
								</div>';
								
		for ($i=0;$i<count($rsDetailEmployee);$i++){
			 
			$detailFirstInformation  .= '
				<div class="div-table-row">  
					<div class="div-table-col">'.$rsDetailEmployee[$i]['technicianname'].'</div>
				</div>
			';
		}
								
		$detailFirstInformation  .= ' </div>
						</div>
					</div>  
		';
	
		$detailInformation  = ' <div class="data-card border-red">
						<h1>'.ucwords($obj->lang['item']).'</h1> 
						<div class="content">
						<div class="div-table  quick-view-table">
							  <div class="div-table-row">  
                                <div class="div-table-col detail-col-header">'.ucwords($obj->lang['item']).'</div>
                                <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">'.ucwords($obj->lang['qty']).'</div> 
                                <div class="div-table-col detail-col-header" style="width:120px;">'.ucwords($obj->lang['unit']).'</div>  
								</div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			 
			$detailInformation  .= '
				<div class="div-table-row">  
					<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</div>
                    <div class="div-table-col">'.$rsDetail[$i]['unitname'].'</div>
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
								 '.$detailFirstInformation.'
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
