<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $pawnSalesOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'pawnSalesOrderForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Cabang', $obj->tableWarehouse. '.name'));
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer. '.name')); 
array_push($arrSearchColumn, array('Nama Penerima','recipientname'));
array_push($arrSearchColumn, array('Nama Penerima','recipientphone'));
array_push($arrSearchColumn, array('Nama Penerima','recipientemail'));
array_push($arrSearchColumn, array('Nama Penerima','recipientaddress'));
array_push($arrSearchColumn, array('Total', $obj->tableName. '.grandtotal'));
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trnotes'));

$arrColumn = array ();
array_push($arrColumn, array('Kode','code',100));
array_push($arrColumn, array('Tanggal','trdate',100,'center','date'));
array_push($arrColumn, array('Jatuh Tempo','trduedate',100,'center','date'));
array_push($arrColumn, array('Cabang','warehousename',100));
array_push($arrColumn, array('Pelanggan','customername')); 
array_push($arrColumn, array('Total','grandtotal',100,'right','integer'));
array_push($arrColumn, array('Penutupan','closingtypename',100));
array_push($arrColumn, array('Status','statusname',70));
 

$printTransactionFunction = $class->generatePrintContextMenu('print','printSalesOrder');  
$printSalesOrderDeliveryFunction =  $class->generatePrintContextMenu('printSalesOrderDelivery','printSalesOrderDelivery'); 
$printLabelFunction = $class->generatePrintContextMenu('printLabel','printSalesLabel', false); 
			    
$overwriteContextMenu["printSeparator"] = "-";
$overwriteContextMenu["print"] = array("name" => $obj->lang['printInvoice'],"icon" =>"print","callbackFunction" => $printTransactionFunction);
$overwriteContextMenu["printSalesOrderDelivery"] = array("name" => $obj->lang['printDeliveryNote'],"icon" =>"print","callbackFunction" => $printSalesOrderDeliveryFunction);
$overwriteContextMenu["printLabel"] = array("name" => "Cetak Slip Pengiriman","icon" =>"print","callbackFunction" => $printLabelFunction);
  
 
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
						<div class="div-table general-information-table">
							<div class="div-table-row">
								<div class="div-table-col" style="width:50%">Status</div> 
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
						<div class="div-table  quick-view-table">
							  <div class="div-table-row">  
									<div class="div-table-col detail-col-header ">Nama Barang</div>
									<div class="div-table-col detail-col-header " style="width:70px; text-align:right;">Jumlah</div>';
        if (!$rs[0]['isfulldeliver'])
           $detailInformation  .= '<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">Jml. Terkirim</div>';
         
            
        $detailInformation  .= '    <div class="div-table-col detail-col-header " style="width:80px;">Unit</div> 
                                    <div class="div-table-col detail-col-header " style="width:100px; text-align:right;">Harga @</div> 
									<div class="div-table-col detail-col-header " style="width:60px; text-align:right;">Diskon @</div>
									<div class="div-table-col detail-col-header " style="width:100px; text-align:right;">Subtotal</div> 
								</div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			 
			if ($rsDetail[$i]['discounttype'] == 2)
				$rsDetail[$i]['discount'] = $rsDetail[$i]['discount']/100 * $rsDetail[$i]['priceinunit'];
		
			$detailInformation  .= '
				<div class="div-table-row">  
					<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</div>';
                  
           if (!$rs[0]['isfulldeliver'])
                   $detailInformation  .= '<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['deliveredqty']).'</div>';
     
			 $detailInformation  .= ' <div class="div-table-col">'. $rsDetail[$i]['unitname'] .'</div>
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
								<div class="div-table-col-5"  style="width:25%;">
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
