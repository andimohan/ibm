<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('Amortization.class.php'));
$amortization = createObjAndAddToCol(new Amortization());

$obj = $amortization;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$addDataFile = 'amortizationForm';
// $quickView = false;

// $overwriteContextMenu['showDetail'] = '';
// $overwriteContextMenu['hideDetail'] = '';
$overwriteContextMenu['print'] = '';

function generateQuickView($obj, $id)
{
    $detail = '';

    $rsDetail = $obj->getDetailWithRelatedInformation($id);

    $detailInformation = ' <div class="data-card no-border">
            <h1>' . ucwords($obj->lang['detail']) . '</h1> 
            <div class="content">
            <div class="div-table quick-view-table" >
                  <div class="div-table-row"> 
                        <div class="div-table-col detail-col-header" style="width:150px;">' . ucwords($obj->lang['prepaidExpense']) . '</div>
                        <div class="div-table-col detail-col-header">' . ucwords($obj->lang['itemOrService']) . '</div>
                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">' . ucwords($obj->lang['subtotal']) . '</div> 
                </div>';

    for ($i = 0; $i < count($rsDetail); $i++) {

        $detailInformation .= '
				<div class="div-table-row"> 
					<div class="div-table-col">' . $rsDetail[$i]['prepaidexpensecode'] . '</div>
					<div class="div-table-col">' . $rsDetail[$i]['servicename'] . '</div>
                    <div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['amount']) . '</div> 
                </div>';
    }

    $detailInformation .= ' </div>
						</div>
					</div>  
		';

    $detail .= $detailInformation;

    $detail .= '<div style="clear:both;"></div>';

    return $detail;
}

// ========================================================================== STARTING POINT ==========================================================================
include('dataList.php');
?>