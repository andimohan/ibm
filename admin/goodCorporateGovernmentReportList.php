<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('GoodCorporateGovernmentReport.class.php'));
$gcgReport = createObjAndAddToCol(new GoodCorporateGovernmentReport());

$obj            = $gcgReport;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$addDataFile = 'goodCorporateGovernmentReportForm';

function generateQuickView($obj, $id){

   $rs = $obj->getDataRowById($id);

   $description = '<div class="data-card no-border">
                     <h1>' . ucwords($obj->lang['description']) . '</h1>
                     <div class="content" style="overflow:hidden;">
                        <div class="div-table" style="float:left; width:100%">
                           <div class="div-table-row">
                              <div class="div-table-col" style="padding:0.3em; ">' . $rs[0]['description'] . '</div> 
                        </div>  
                     </div>
                  </div>';

   $detail = $description;


   $detail .= '<div style="clear:both;"></div>';


   return $detail;
}

// ========================================================================== STARTING POINT ==========================================================================
include('dataList.php');
?>