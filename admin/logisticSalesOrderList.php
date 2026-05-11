<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('LogisticSalesOrder.class.php');
$logisticSalesOrder = createObjAndAddToCol(new LogisticSalesOrder());

$obj = $logisticSalesOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class
if (!$security->isAdminLogin($securityObject, 10, true));

$addDataFile = 'logisticSalesOrderForm';



function generateQuickView($obj, $id)
{

	$rs = $obj->searchData($obj->tableName . '.pkey', $id, true);
	$rsDetail = $obj->getDetailWithRelatedInformation($id);

	if ($rs[0]['finaldiscounttype'] == 2)
		$rs[0]['finaldiscount'] = $rs[0]['finaldiscount'] / 100 * $rs[0]['subtotal'];

	$basicInformation  = ' <div class="data-card border-orange">
						<h1>' . ucwords($obj->lang['generalInformation']) . '</h1> 
						<div class="content">
						<div class="div-table general-information-table">
							<div class="div-table-row">
								<div class="div-table-col" style="width:40%">' . ucwords($obj->lang['status']) . '</div> 
								<div class="div-table-col">' . $rs[0]['statusname'] . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['code']) . '</div> 
								<div class="div-table-col">' . $rs[0]['code'] . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['date']) . '</div> 
								<div class="div-table-col">' . $obj->formatDBDate($rs[0]['trdate']) . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['warehouse']) . '</div> 
								<div class="div-table-col">' . $rs[0]['warehousename'] . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['transportation']) . '</div> 
								<div class="div-table-col">' . $rs[0]['transportationname'] . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">Total ' . ucwords($obj->lang['bale']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['totalqty']) . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">Total ' . ucwords($obj->lang['weight']) . ' (Kg)</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['totalweight']) . '</div> 
							</div>
                        	<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div>   
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['subtotal']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['subtotal']) . '</div> 
							</div>  
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['packingFee']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['packingfee']) . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['others']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['etccost']) . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['total']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['grandtotal']) . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['note']) . '</div> 
								<div class="div-table-col">' . $rs[0]['trdesc'] . '</div> 
							</div> 
						</div>
						</div>
					</div>  
		';

	$detailInformation  = ' <div class="data-card border-green">
						<h1>' . ucwords($obj->lang['detail']) . '</h1> 
						<div class="content">
						<div class="div-table  quick-view-table">
							  <div class="div-table-row">  
                                <div class="div-table-col detail-col-header">' . ucwords($obj->lang['description']) . '</div>
                                <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">' . ucwords($obj->lang['weight']) . ' (KG)</div> 
                                <div class="div-table-col detail-col-header" style="width:150; text-align:center;">' . ucwords($obj->lang['lengthShort']) . ' x ' .  ucwords($obj->lang['widthShort']) . ' x ' .  ucwords($obj->lang['heightShort']) . ' (cm)</div> 
                                <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">' . ucwords($obj->lang['weight']) . ' (CBM)</div> 
                                <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">' . ucwords($obj->lang['weight']) . '</div> 
                                <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">' . ucwords($obj->lang['price']) . '</div> 
								</div>';

	for ($i = 0; $i < count($rsDetail); $i++) {

		$detailInformation  .= '
				<div class="div-table-row">  
					<div class="div-table-col">' . $rsDetail[$i]['description'] . '</div>
					<div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['weight']) . '</div> 
					<div class="div-table-col" style="text-align:center;">' . $obj->formatNumber($rsDetail[$i]['length']) . ' x ' . $obj->formatNumber($rsDetail[$i]['width']) . ' x ' . $obj->formatNumber($rsDetail[$i]['height']) . '</div> 
					<div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['cbmweight']) . '</div> 
					<div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['finalweight']) . '</div> 
					<div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['priceinunit']) . '</div> 
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
								' . $basicInformation . '
								</div> 
								<div class="div-table-col-5">
								 ' . $detailInformation . '
								</div>  
							</div>
					</div>';

	$detail .= '<div style="clear:both;"></div>';

	return $detail;
}

// ========================================================================== STARTING POINT ==========================================================================
include('dataList.php');
