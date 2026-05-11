<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 


$obj = $itemPromo;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'itemPromoForm';
 
$arrSearchColumn = array(
	'0' => array('Kode', $obj->tableName . '.code') 
); 		 
		
$arrColumn = array (
  '0' => array('Kode','code',70,'true','left'),
  '1' => array('Tgl Mulai','startdate',130,'true','center','date'),  
  '2' => array('Tgl Selesai','enddate',130,'true','center','date'), 
  '3' =>  array('Status','statusname','','true','left'),
);   
   

function generateQuickView($obj,$id){ 
	$item = new Item();
	    
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);   
 	$rsDetail = $obj->getDetailById($id);
	
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>Informasi Umum</h1> 
						<div class="content">
						<div class="div-table" style="width:100%;">
							<div class="div-table-row">
								<div class="div-table-col" style="width:40%">Status</div> 
								<div class="div-table-col">'.$rs[0]['statusname'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">Kode</div> 
								<div class="div-table-col">'.$rs[0]['code'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">Tanggal Mulai</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['startdate']).'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">Tanggal Berakhir</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['enddate']).'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">Catatan</div> 
								<div class="div-table-col">'.$rs[0]['trnotes'].'</div> 
							</div> 
						</div>
						</div>
					</div>  
		'; 	
		
		$detailInformation  = ' <div class="data-card border-green">
						<h1>Detail Item</h1> 
						<div class="content">
						<div class="div-table" style="width:100%;">
							  <div class="div-table-row"> 
									<div class="div-table-col detail-col-header">Item</div>
									<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">Harga Retail @</div> 
									<div class="div-table-col detail-col-header" style="width:60px; text-align:right;">Diskon @</div>
									<div class="div-table-col detail-col-header" style="width:80px; text-align:right;">Harga Promo @</div> 
								</div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			
			$rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
			$discounttype = '';
			if ($rsDetail[$i]['discounttype'] == 2)
				$discounttype = '%';
		
			$detailInformation  .= '
				<div class="div-table-row"> 
					<div class="div-table-col">'.$rsItem[0]['name'].'</div>
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</div> 
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['discount']).' '.$discounttype.'</div>
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['promoprice']).'</div> 
				</div>
			';
		}
								
		$detailInformation  .= ' </div>
						</div>
					</div>  
		'; 	
		
		$detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col"  style="width:33%; text-align:center;">
								'.$basicInformation.'
								</div> 
								<div class="div-table-col"  style="text-align:center; ">
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