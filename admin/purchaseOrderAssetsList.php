<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 


$obj = $purchaseOrderAssets;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'purchaseOrderAssetsForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Gudang', $obj->tableWarehouse. '.name'));
array_push($arrSearchColumn, array('Supplier', $obj->tableSupplier. '.name') );
array_push($arrSearchColumn, array('Total', $obj->tableName. '.grandtotal'));
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trnotes') );
 
$arrColumn = array ();
array_push($arrColumn, array('Kode','code',100));
array_push($arrColumn, array('Tanggal','trdate',100,'center','date'));
array_push($arrColumn, array('Gudang','warehousename',100));
array_push($arrColumn, array('Pemasok','suppliername'));
array_push($arrColumn, array('Total','grandtotal',150,'right','integer')); 
array_push($arrColumn, array('Status','statusname',70));
 
 
function generateQuickView($obj,$id){  
	    
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
								<div class="div-table-col">Tanggal</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['trdate']).'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">Gudang</div> 
								<div class="div-table-col">'. $rs[0]['warehousename'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">Supplier</div> 
								<div class="div-table-col">'.$rs[0]['suppliername'].'</div> 
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
						<h1>Detail Aset</h1> 
						<div class="content">
						<div class="div-table" style="width:100%;">
							  <div class="div-table-row"> 
									<div class="div-table-col detail-col-header">Aset</div>
									<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">Jumlah</div>';
    
        if (!$rs[0]['isfullreceive'])
           $detailInformation  .= '<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">Jml. Diterima</div>';
         
            
        $detailInformation  .= '<div class="div-table-col detail-col-header" style="width:80px; padding-left:1em;">Unit</div> 
                                <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">Harga @</div>  
                                <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">Subtotal</div> 
                            </div>';
								
		for ($i=0;$i<count($rsDetail);$i++){ 
		
			$detailInformation  .= '
				<div class="div-table-row"> 
					<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>
					<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</div>';
            
           if (!$rs[0]['isfullreceive'])
                   $detailInformation  .= '<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['receivedqty']).'</div>';
              
		 $detailInformation  .= '   <div class="div-table-col" style="padding-left:1em">Pcs</div> 
                                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['priceinunit']).'</div> 
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
