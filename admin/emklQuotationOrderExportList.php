<?php

require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('EMKLQuotationOrder.class.php');
$emklQuotationOrderExport = createObjAndAddToCol(new EMKLQuotationOrder(EMKL['jobType']['export']));

$obj = $emklQuotationOrderExport; 

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
  
    
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'emklQuotationOrderExportForm';
 
/*$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));  
array_push($arrSearchColumn, array('Bisnis Unit', $obj->tableBusinessUnit . '.name'));  
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer.'.name')); 
//array_push($arrSearchColumn, array(ucwords($obj->lang['jobType']), $obj->tableJobType. '.name')); 
array_push($arrSearchColumn, array(ucwords($obj->lang['jobType']), $obj->tableTransportationType. '.name')); 
array_push($arrSearchColumn, array(ucwords($obj->lang['jobType']), $obj->tableLoadContainer. '.name')); 
array_push($arrSearchColumn, array('Sales', $obj->tableEmployee. '.name'));
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trdesc'));
array_push($arrSearchColumn, array('POL', 'pol.name'));
array_push($arrSearchColumn, array('POD', 'pod.name'));
array_push($arrSearchColumn, array('Container Type',  $obj->tableContainerType. '.name'));
array_push($arrSearchColumn, array('Commodity',  $obj->tableName. '.commoditycache'));
array_push($arrSearchColumn, array('Location',  $obj->tableName. '.locationcache'));
array_push($arrSearchColumn, array('Status',  $obj->tableStatus. '.status'));*/


function generateQuickView($obj,$id){ 
$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id);   
//	$rsDetail = $obj->getDetailWithRelatedInformation($id);
    
    $rsDetailCommodity = $obj->getDetailCommodity($id);
    $arrCommodity = array_column($rsDetailCommodity,'commodityname');

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
                                <div class="div-table-col">'.ucwords($obj->lang['customer']).'</div> 
                                <div class="div-table-col">'.$rs[0]['customername'].'</div> 
                            </div>
                            <div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['sales']).'</div> 
                                <div class="div-table-col">'.$rs[0]['salesname'].'</div> 
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
								<div class="div-table-col"></div> 
								<div class="div-table-col" style="height: 1em"></div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['commodity']).'</div> 
								<div class="div-table-col">'.implode(',',$arrCommodity).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col"></div> 
								<div class="div-table-col" style="height: 1em"></div> 
							</div>  
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['note']).'</div> 
								<div class="div-table-col">'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</div> 
							</div> 
						</div>
						</div>
					</div>  
		'; 	
		
	/*	$detailInformation  = ' <div class="data-card border-green">
						<h1>'.ucwords($obj->lang['detail']).'</h1> 
						<div class="content">';
    */
    
/*    $detailInformation .=  '<div class="div-table  quick-view-table">
							     <div class="div-table-row">  
									<div class="div-table-col detail-col-header">POL / POD</div>  
									<div class="div-table-col detail-col-header" style=" width:120px;">'.ucwords($obj->lang['currency']).'</div>  
									<div class="div-table-col detail-col-header" style=" width:120px;">'.ucwords($obj->lang['carrier']).'</div>  
									<div class="div-table-col detail-col-header" style="">'.ucwords($obj->lang['description']).'</div> 
                                </div>';*/
								
	/*	for ($i=0;$i<count($rsDetail);$i++){
		
		    
            
                  
			$detailInformation  .= '
				<div class="div-table-row">  
                    <div class="div-table-col" style=" ">'.$rsDetail[$i]['polname'].' - '.$rsDetail[$i]['podname'].'</div> 
                    <div class="div-table-col" style=" ">'.$rsDetail[$i]['currencyname'].'</div> 
                    <div class="div-table-col" style=" ">'.$rsDetail[$i]['carriername'].'</div> 
                    <div class="div-table-col" style="">'. str_replace(chr(13),'<br>',$rsDetail[$i]['description']) .'</div>
				</div>
			';
		}
				
		$detailInformation  .= ' </div>
						</div>
					</div>  
		'; 	*/
		
    
        

        $snInformation = '';
    
    
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
