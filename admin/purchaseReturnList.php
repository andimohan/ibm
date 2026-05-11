<?php
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $purchaseReturn;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'purchaseReturnForm';


$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Gudang', $obj->tableWarehouse . '.name'));
array_push($arrSearchColumn, array('Supplier', $obj->tableSupplier. '.name'));
 

$arrColumn = array ();
array_push($arrColumn, array('Kode','code',70,'true','left'));
array_push($arrColumn, array('Tgl. Transaksi','trdate',100,'true','center','date'));
array_push($arrColumn, array('Gudang','warehousename',100,'true','left'));
array_push($arrColumn, array('Pemasok','suppliername',200,'true','left'));
array_push($arrColumn, array('Status','statusname','','true','left'));
 

function generateQuickView($obj,$id){

	$item = new Item();
	$rsDetail = $obj->getDetailById($id);

	$detail = '';

	$detailInformation  = ' <div class="data-card border-green">
					<h1>Detail Item</h1>
					<div class="content">
					<div class="div-table" style="width:100%;">
						  <div class="div-table-row">
								<div class="div-table-col detail-col-header"  style="width:250px;">Item</div>
								<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">Jumlah</div>
								<div class="div-table-col detail-col-header"></div>
							</div>';

	for ($i=0;$i<count($rsDetail);$i++){

		$rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);

		$detailInformation  .= '
			<div class="div-table-row">
				<div class="div-table-col">'.$rsItem[0]['name'].'</div>
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qty']).'</div>
				<div class="div-table-col"></div>
			</div>
		';
	}

	$detailInformation  .= ' </div>
					</div>
				</div>
	';

	$detail .= '<div class="div-table" style="width:100%; ">
						<div class="div-table-row">
							<div class="div-table-col"  style="width:100%; text-align:center;">
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
