<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 


$obj = $itemInReceive;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'itemInReceiveForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Kode Pemasukan Barang', $obj->tableItemInHeader. '.code'));
array_push($arrSearchColumn, array('Kode Referensi', $obj->tableItemInHeader. '.refcode'));
array_push($arrSearchColumn, array('Supplier', $obj->tableSupplier. '.name'));
array_push($arrSearchColumn, array('Gudang', $obj->tableWarehouse. '.name'));
 
$arrColumn = array ();
array_push($arrColumn, array(ucwords($obj->lang['code']),'code',100));
array_push($arrColumn, array(ucwords($obj->lang['date']),'trdate',100 , 'center','date'));
array_push($arrColumn, array(ucwords($obj->lang['itemInCode']),'itemincode',150 ));
array_push($arrColumn, array(ucwords($obj->lang['reference']),'refcode',150 ));
array_push($arrColumn, array(ucwords($obj->lang['supplier']),'suppliername' ));
array_push($arrColumn, array(ucwords($obj->lang['warehouse']),'warehousename',150 ));
array_push($arrColumn, array(ucwords($obj->lang['status']),'statusname',70));
		 
$printTransactionFunction = $class->generatePrintContextMenu('print','print/purchaseReceive');   
$overwriteContextMenu["printSeparator"] = "-";
$overwriteContextMenu["print"] = array("name" => $obj->lang['printTransaction'],"icon" =>"print","callbackFunction" => $printTransactionFunction); 

function generateQuickView($obj,$id){ 
    
	    
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);   
 	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	   
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>'.ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table general-information-table">
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
								<div class="div-table-col">'.ucwords($obj->lang['itemInCode']).'</div> 
								<div class="div-table-col">'.$rs[0]['itemincode'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
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
						<h1>'.ucwords($obj->lang['itemDetail']).'</h1> 
						<div class="content">
						<div class="div-table quick-view-table">
							  <div class="div-table-row"> 
									<div class="div-table-col detail-col-header" style="width:120px;">'.ucwords($obj->lang['itemCode']).'</div>
									<div class="div-table-col detail-col-header">'.ucwords($obj->lang['itemName']).'</div>
									<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">'.ucwords($obj->lang['orderedQty']).'</div>
									<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">'.ucwords($obj->lang['outstanding']).'</div>
									<div class="div-table-col detail-col-header" style="width:90px; text-align:right;">'.ucwords($obj->lang['receivedQty']).'</div> 
									<div class="div-table-col detail-col-header" style="width:70px; text-align:right;"></div> 
								</div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			  
			$detailInformation  .= '
				<div class="div-table-row"> 
					<div class="div-table-col">'.$rsDetail[$i]['itemcode'].'</div>
					<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['orderedqtyinbaseunit']).'</div> 
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qtyminusinbaseunit']).'</div> 
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['receivedqtyinbaseunit']).'</div> 
					<div class="div-table-col text-muted">'. $rsDetail[$i]['baseunitname'] .'</div> 
				</div>
			';
		}
								
		$detailInformation  .= ' </div>
						</div>
					</div>  
		'; 	
		
		$detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5"  style="width:25%;">
								'.$basicInformation.'
								</div> 
								<div class="div-table-col-5" >
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
