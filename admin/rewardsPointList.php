<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $rewardsPoint;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'rewardsPointForm';
 
$arrSearchColumn = array(
	'0' => array('Kode', $obj->tableName . '.code'), 
	'1' => array('Referensi','salescode') , 
	'2' => array('Pelanggan', 'customername')  , 
	'3' => array('Point', $obj->tableName . '.point')  ,
	'4' => array('Catatan', $obj->tableName . '.trdesc')  
); 		 
		
$arrColumn = array (
  '0' => array('Kode','code',70,'true','left'),
  '1' => array('Tanggal','trdate',100,'true','center','date'), 
  '2' => array('Referensi','salescode',100,'true','left'), 
  '3' => array('Pelanggan','customername',150,'true','left'), 
  '4' => array('Point','point',100,'true','right','integer'), 
  '5' =>  array('Catatan','trdesc','250','true','left'),
  '6' =>  array('Status','statusname','','true','left'),
);   
   
function generateQuickView($obj,$id){ 
	$detail = '';

 	$salesOrder = new SalesOrder();
	
	$rsRewardsPoint = $obj->getDataRowById($id);
	
	$rs = $salesOrder->searchData($salesOrder->tableName .'.pkey',$rsRewardsPoint[0]['refkey'],true);   
 	if (empty($rs))
		return $detail; 
			
	$rsDetail = $salesOrder->getDetailWithRelatedInformation($rsRewardsPoint[0]['refkey']);
	
	if ($rs[0]['finaldiscounttype'] == 2)
		$rs[0]['finaldiscount'] = $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'];
	  
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
								<div class="div-table-col">Tanggal</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['trdate']).'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">Gudang</div> 
								<div class="div-table-col">'. $rs[0]['warehousename'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">Pelanggan</div> 
								<div class="div-table-col">'.$rs[0]['customername'].'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">Subtotal</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['subtotal']).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">Final Diskon</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['finaldiscount']). '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">Total Sebelum Pajak</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['beforetaxtotal']).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">Pajak</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['taxvalue']).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">Ongkos Kirim</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['shipmentfee']).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">Biaya Lain</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['etccost']).'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">Total</div> 
								<div class="div-table-col">'.$obj->formatNumber($rs[0]['grandtotal']).'</div> 
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
									<div class="div-table-col detail-col-header text-black-jet" style="width:60px;">Kode Item</div>
									<div class="div-table-col detail-col-header text-black-jet">Nama Barang</div>
									<div class="div-table-col detail-col-header text-black-jet" style="width:70px; text-align:right;">Jumlah</div>
									<div class="div-table-col detail-col-header text-black-jet" style="width:50px; text-align:right;">Harga @</div> 
									<div class="div-table-col detail-col-header text-black-jet" style="width:60px; text-align:right;">Diskon @</div>
									<div class="div-table-col detail-col-header text-black-jet" style="width:60px; text-align:right;">Subtotal</div> 
								</div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			 
			if ($rsDetail[$i]['discounttype'] == 2)
				$rsDetail[$i]['discount'] = $rsDetail[$i]['discount']/100 * $rsDetail[$i]['priceinunit'];
		
			$detailInformation  .= '
				<div class="div-table-row"> 
					<div class="div-table-col">'.$rsDetail[$i]['itemcode'].'</div>
					<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</div>
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</div> 
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['discount']).'</div>
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['total']).'</div> 
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