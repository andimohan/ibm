<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('SalesPrice.class.php');
$salesPrice = createObjAndAddToCol(new SalesPrice());


$obj = $salesPrice;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$addDataFile = 'salesPriceForm';


function generateQuickView($obj, $id)
{
	$detail = '';
	$rs = $obj->searchData($obj->tableName . '.pkey', $id, true);
	$rsDetail = $obj->getDetailWithRelatedInformation($id);

	$basicInformation = ' <div class="data-card border-orange">
						<h1>' . ucwords($obj->lang['generalInformation']) . '</h1> 
						<div class="content">
						<div class="div-table general-information-table">
							<div class="div-table-row">
								<div class="div-table-col" style="width:50%">' . ucwords($obj->lang['status']) . '</div> 
								<div class="div-table-col">' . $rs[0]['statusname'] . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['code']) . '</div> 
								<div class="div-table-col">' . $rs[0]['code'] . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['customer']) . '</div> 
								<div class="div-table-col">' . $rs[0]['customername'] . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div>  
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['note']) . '</div> 
								<div class="div-table-col">' . $rs[0]['notes'] . '</div> 
							</div> 
						</div>
						</div>
					</div>  
		';

	$detailInformation = ' <div class="data-card border-green">
						<h1>' . ucwords($obj->lang['detail']) . '</h1> 
						<div class="content">
						<div class="div-table quick-view-table">
							  <div class="div-table-row"> 
									<div class="div-table-col detail-col-header">' . ucwords($obj->lang['itemName']) . '</div>
									<div class="div-table-col detail-col-header" style="width:90px; text-align:right;">' . ucwords($obj->lang['price']) . '</div>
								</div>';

	for ($i = 0; $i < count($rsDetail); $i++) {

		$detailInformation .= '
				<div class="div-table-row"> 
					<div class="div-table-col">' . $rsDetail[$i]['itemname'] . '</div>
					<div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['price']) . '</div> 
				</div>
			';
	}

	$detailInformation .= ' </div>
						</div>
					</div>  
		';

	$detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5"  style="width:25%;">
								' . $basicInformation . '
								</div> 
								<div class="div-table-col-5" >
								 ' . $detailInformation . '
								</div>  
							</div>
					</div>';

	$detail .= '<div style="clear:both;"></div>';


	return $detail;
}


include('dataList.php');

?>