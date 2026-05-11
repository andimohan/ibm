<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $truckingCostCashIn;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
										
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'truckingCostCashInForm';
$quickView = true;

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode',$obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama',$obj->tableEmployee . '.name')); 
array_push($arrSearchColumn, array('SPK',$obj->tableSalesWorkOrder . '.code')); 
array_push($arrSearchColumn, array('SPK',$obj->tableSalesOrder . '.code')); 
  
$arrColumn = array ();
array_push($arrColumn, array(ucwords($obj->lang['code']),'code',100)); 
array_push($arrColumn, array(ucwords($obj->lang['date']),'trdate',100,'center','date')); 
array_push($arrColumn, array(ucwords($obj->lang['jobOrderCode']),'socode',150)); 
array_push($arrColumn, array(ucwords($obj->lang['WOCode']),'refcode',120)); 
array_push($arrColumn, array(ucwords($obj->lang['driver']),'drivername')); 
array_push($arrColumn, array(ucwords($obj->lang['amount']),'total',150,'right','integer')); 
array_push($arrColumn, array(ucwords($obj->lang['status']),'statusname',70));
	  

$printTransactionFunction = $class->generatePrintContextMenu('print','printTruckingCostCashOut');  
$overwriteContextMenu["printSeparator"] = "-";
$overwriteContextMenu["print"] = array("name" => $obj->lang['printTransaction'],"icon" =>"print","callbackFunction" => $printTransactionFunction);
  
//$overwriteContextMenu['showDetail'] = '';
//$overwriteContextMenu['hideDetail'] = ''; 



function generateQuickView($obj,$id){ 
    $item = new Item();
    
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
                                    <div class="div-table-row" style="height:20px">
                                    </div>
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['amount']).'</div> 
                                        <div class="div-table-col">'.$obj->formatNumber($rs[0]['total']).'</div> 
                                    </div>
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['note']).'</div> 
                                        <div class="div-table-col">'.$rs[0]['trdesc'].'</div> 
                                    </div>
                                </div>
                                </div>
                            </div>  
		'; 	
//		
		$detailInformation  = ' <div class="data-card border-green">
                                    <h1>'.ucwords($obj->lang['detail']).'</h1> 
                                    <div class="content">
                                    <div class="div-table  quick-view-table">
                                             <div class="div-table-row">
                                                <div class="div-table-col detail-col-header" style="text-align:left;  width: 150px">'.ucwords($obj->lang['cost']).'</div>
                                                <div class="div-table-col detail-col-header" style="text-align:left;  width: 200px">'.ucwords($obj->lang['account']).'</div>
                                                <div class="div-table-col detail-col-header" style="text-align:left;  width: 120px">'.ucwords($obj->lang['note']).'</div>
                                                <div class="div-table-col detail-col-header" style="text-align:right;">'.ucwords($obj->lang['amount']).'</div>
                                            </div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			
            $rsCost = $item->getDataRowById($rsDetail[$i]['costkey']);
                  
			$detailInformation  .= '
				<div class="div-table-row">
					<div class="div-table-col" style="text-align:left; ">'.$rsDetail[$i]['costname'].'</div> 
					<div class="div-table-col" style="text-align:left; ">'.$rsDetail[$i]['coaname'].'</div> 
					<div class="div-table-col" style="text-align:left; ">'.$rsDetail[$i]['description'].'</div> 
					<div class="div-table-col" style="text-align:right">'.$obj->formatnumber($rsDetail[$i]['price']).'</div> 
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

 
include ('dataList.php');

?>
