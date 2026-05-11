<?php
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass(array('Assembly.class.php'));
$assembly = createObjAndAddToCol(new Assembly());

$obj= $assembly;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'assemblyForm';


function generateQuickView($obj,$id){
	
	$item = createObjAndAddToCol(new Item());
	$rsDetail = $obj->getDetailById($id);

	$detail = '';

     $detailInformation  = ' <div class="data-card no-border">
					<h1>'. ucwords($obj->lang['itemDetail']).'</h1> 
					<div class="content">
					<div class="div-table quick-view-table" >
						  <div class="div-table-row">
								<div class="div-table-col detail-col-header"  style="width:250px;">Item</div>
								<div class="div-table-col detail-col-header" style="width:70px; text-align:right;">Jml. BOM</div>
								<div class="div-table-col detail-col-header" style="width:100px; text-align:right;">Jml. Digunakan</div>
								<div class="div-table-col detail-col-header"></div>
							</div>';

	for ($i=0;$i<count($rsDetail);$i++){

		$rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);

		$detailInformation  .= '
			<div class="div-table-row">
				<div class="div-table-col">'.$rsItem[0]['name'].'</div>
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qtybom']).'</div>
				<div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['qtyused']).'</div>
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
