<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('AssetPurchase.class.php'));
$assetPurchase = createObjAndAddToCol(new AssetPurchase());
$obj = $assetPurchase;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$addDataFile = 'assetPurchaseForm';


function generateQuickView($obj, $id)
{
	$detail = '';
	

	$detail = '';
	$rs = $obj->searchData($obj->tableName . '.pkey', $id, true);
	$rsDetail = $obj->getDetailWithRelatedInformation($id);



	$basicInformation  = ' <div class="data-card border-orange">
						<h1>' . ucwords($obj->lang['generalInformation']) . '</h1> 
						<div class="content">
						<div class="div-table general-information-table">
							<div class="div-table-row">
								<div class="div-table-col " style="width:50%">' . ucwords($obj->lang['status']) . '</div> 
								<div class="div-table-col">' . $rs[0]['statusname'] . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col ">' . ucwords($obj->lang['code']) . '</div> 
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
								<div class="div-table-col">' . ucwords($obj->lang['supplier']) . '</div> 
								<div class="div-table-col">' . $rs[0]['suppliername'] . '</div> 
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
								<div class="div-table-col">' . ucwords($obj->lang['beforeTax']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['beforetaxtotal']) . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['tax']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['taxvalue']) . '</div> 
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
            <div class="div-table quick-view-table" >
                  <div class="div-table-row"> 
                        <div class="div-table-col detail-col-header">' . ucwords($obj->lang['name']) . '</div>
                        <div class="div-table-col detail-col-header" style="width:120px;">' . ucwords($obj->lang['category']) . '</div>
                        <div class="div-table-col detail-col-header" style="width:70px; text-align:right;">' . ucwords($obj->lang['qty']) . '</div>
                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">' . ucwords($obj->lang['price']) . ' @</div> 
                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">' . ucwords($obj->lang['subtotal']) . '</div> 
                </div>';

	$assetCategory = createObjAndAddToCol(new AssetCategory());
	for ($i = 0; $i < count($rsDetail); $i++) {
		$categoryname = $assetCategory->searchDataRow(array($assetCategory->tableName . '.name'), ' and ' . $assetCategory->tableName . '.pkey = ' . $obj->oDbCon->paramString($rsDetail[$i]['categorykey']))[0]['name'];

		$detailInformation  .= '
				<div class="div-table-row"> 
					<div class="div-table-col">' . $rsDetail[$i]['name'] . '</div>
					<div class="div-table-col" style="">' . $categoryname . '</div>
					<div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['qty']) . '</div>
                    <div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['priceinunit']) . '</div> 
                    <div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['subtotal']) . '</div> 
                </div>';
	}

	$detailInformation  .= ' </div>
						</div>
					</div>  
		';

	$detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5"  style="width:25%; text-align:center;">
								' . $basicInformation . '
								</div> 
								<div class="div-table-col-5"  style="text-align:center; ">
								 ' . $detailInformation . '
								</div>  
							</div>
					</div>';

	$detail .= '<div style="clear:both;"></div>';


	return $detail;
}

// ========================================================================== STARTING POINT ==========================================================================
include('dataList.php');
