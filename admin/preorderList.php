<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 


$obj = $preorder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'preOrderForm';
 
$arrSearchColumn = array(
	'0' => array('Kode', $obj->tableName . '.code'), 
	'1' => array('Tanggal', $obj->tableName . '.trdate'), 
	'2' => array('Gudang', $obj->tableWarehouse. '.name') , 
	'3' => array('Pelanggan', $obj->tableCustomer. '.name')  , 
	'4' => array('Sales', $obj->tableEmployee. '.name')  , 
	'5' => array('Total', $obj->tableName. '.grandtotal') 
); 		 
		
$arrColumn = array (
  '0' => array('Kode','code',70,'true','left'),
  '1' => array('Tanggal','trdate',100,'true','center','date'), 
  '2' => array('Gudang','warehousename',100,'true','left'), 
  '3' => array('Pelanggan','customername',200,'true','left'), 
  '4' => array('Sales','salesname',200,'true','left'), 
  '5' =>  array('Total','grandtotal','100','true','right','integer'),  
  '6' =>  array('Status','statusname','','true','left'),
);  


$function = 'case "printLabel": 
				var selectedTabId = selectedTab.newPanel[0].id;
				var selectedPkey = tabParam[selectedTabId].selectedPkey; 
				
				if (selectedPkey.length == 0){
					showMsgDialog ("Anda belum memilih data yang hendak dicetak."); 
					break ;
				}
			 
			   window.open(\'printSalesLabel/\' + selectedPkey);
			   break;'.chr(13);
			   
$overwriteContextMenu["printSeparator"] = "-";
$overwriteContextMenu["printLabel"] = array("name" => "Cetak Label","icon" =>"print","callbackFunction" => $function);
  
 
function generateQuickView($obj,$id){ 
	$item = new Item();
	    
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);   
 	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	
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
								<div class="div-table-col">('.$obj->formatNumber($rs[0]['finaldiscount']). ')</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">Point</div> 
								<div class="div-table-col">('.$obj->formatNumber($rs[0]['pointvalue']). ')</div> 
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