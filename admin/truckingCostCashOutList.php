<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('TruckingCostCashOut.class.php'); 
$truckingCostCashOut = createObjAndAddToCol(new TruckingCostCashOut());

$obj = $truckingCostCashOut;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
										
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'truckingCostCashOutForm';
$quickView = true;

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode',$obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama',$obj->tableEmployee . '.name'));  
array_push($arrSearchColumn, array('Ref',$obj->tableName . '.refcode'));  
array_push($arrSearchColumn, array('Ref2',$obj->tableName . '.refcode2'));  
array_push($arrSearchColumn, array('Gudang',$obj->tableWarehouse . '.name'));  
array_push($arrSearchColumn, array('Deskripsi',$obj->tableName . '.trdesc'));  
array_push($arrSearchColumn, array('Pekerjaan',$obj->tableName . '.jobdescription'));  
array_push($arrSearchColumn, array('S / I',$obj->tableSalesOrder . '.donumber'));  
array_push($arrSearchColumn, array('Pelanggan',$obj->tableCustomer . '.name'));   
   

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
                                    <div class="div-table-row">
                                        <div class="div-table-col">'.ucwords($obj->lang['warehouse']).'</div> 
                                        <div class="div-table-col">'.$rs[0]['warehousename'].'</div> 
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
                                                <div class="div-table-col detail-col-header" style="text-align:right;  width: 50px">'.ucwords($obj->lang['qty']).'</div>
                                                <div class="div-table-col detail-col-header" style="width: 150px">'.ucwords($obj->lang['costName']).'</div>
                                                <div class="div-table-col detail-col-header" style="width: 200px">'.ucwords($obj->lang['account']).'</div>
                                                <div class="div-table-col detail-col-header">'.ucwords($obj->lang['note']).'</div>
                                                <div class="div-table-col detail-col-header" style="text-align:right;width: 80px">'.ucwords($obj->lang['cost']).'</div>
                                                <div class="div-table-col detail-col-header" style="text-align:right;width: 80px">'.ucwords($obj->lang['total']).'</div>
                                            </div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			 
                  
			$detailInformation  .= '
				<div class="div-table-row">
					<div class="div-table-col" style="text-align:right">'.$obj->formatnumber($rsDetail[$i]['qty']).'</div> 
					<div class="div-table-col" style="text-align:left; ">'.$rsDetail[$i]['costname'].'</div> 
					<div class="div-table-col" style="text-align:left; ">'.$rsDetail[$i]['coaname'].'</div> 
					<div class="div-table-col" style="text-align:left; ">'.$rsDetail[$i]['description'].'</div> 
					<div class="div-table-col" style="text-align:right">'.$obj->formatnumber($rsDetail[$i]['costvalue']).'</div> 
					<div class="div-table-col" style="text-align:right">'.$obj->formatnumber($rsDetail[$i]['amount']).'</div> 
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
