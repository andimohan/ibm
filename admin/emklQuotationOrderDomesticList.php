<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('EMKLQuotationOrder.class.php');
$emklQuotationOrderDomestic = createObjAndAddToCol(new EMKLQuotationOrder(EMKL['jobType']['domestic']));

$obj = $emklQuotationOrderDomestic;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class


if (!$security->isAdminLogin($securityObject, 10, true))
    ;

$addDataFile = 'emklQuotationOrderDomesticForm';

function generateQuickView($obj, $id)
{
    $detail = '';
    $rs = $obj->searchData($obj->tableName . '.pkey', $id);

    $basicInformation = ' <div class="data-card border-orange">
						<h1>' . ucwords($obj->lang['generalInformation']) . '</h1> 
						<div class="content">
						<div class="div-table  general-information-table">
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
                                <div class="div-table-col">' . ucwords($obj->lang['sales']) . '</div> 
                                <div class="div-table-col">' . $rs[0]['salesname'] . '</div> 
                            </div>
                            <div class="div-table-row">
                                <div class="div-table-col">' . ucwords($obj->lang['warehouse']) . '</div> 
                                <div class="div-table-col">' . $rs[0]['warehousename'] . '</div> 
                            </div>
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['jobType']) . '</div> 
								<div class="div-table-col">' . $rs[0]['jobtype'] . ' , ' . $rs[0]['transportationtype'] . ' - ' . $rs[0]['loadcontainertype'] . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col"></div> 
								<div class="div-table-col" style="height: 1em"></div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['commodity']) . '</div> 
								<div class="div-table-col">' . $rs[0]['commoditydesc'] . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col"></div> 
								<div class="div-table-col" style="height: 1em"></div> 
							</div>  
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['note']) . '</div> 
								<div class="div-table-col">' . str_replace(chr(13), '<br>', $rs[0]['trdesc']) . '</div> 
							</div> 
						</div>
						</div>
					</div>  
		';

    $detailInformation = '';


    $snInformation = '';


    $detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5" style="width:25%;">
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

?>