<?php

require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('BankReconsiliation.class.php');
$bankReconsiliation = createObjAndAddToCol(new BankReconsiliation());

$obj = $bankReconsiliation;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$addDataFile = 'bankReconsiliationForm';
// $quickView = false;


// $overwriteContextMenu['showDetail'] = '';
// $overwriteContextMenu['hideDetail'] = '';

function generateQuickView($obj, $id)
{

    $detail = '';
    $rs = $obj->searchData($obj->tableName .'.pkey',$id,true);
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
 
    $basicInformation = '
            <div class="data-card border-orange">
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
								<div class="div-table-col">' . ucwords($obj->lang['account']) . '</div> 
								<div class="div-table-col">' . $rs[0]['codename'] . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['currency']) . '</div> 
								<div class="div-table-col">' . $rs[0]['currencyname'] . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['beginingBalance']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['beginingbalance']) . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['endingBalance']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['endingbalance']) . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['note']) . '</div> 
								<div class="div-table-col">' .$rs[0]['trdesc'] . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
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
                        <div class="div-table-col detail-col-header" style="width:125px; text-align:left;">' . ucwords($obj->lang['voucherCode']) . '</div>
                        <div class="div-table-col detail-col-header" style="width:125px; text-align:left;">' . ucwords($obj->lang['refCode']) . '</div>
                        <div class="div-table-col detail-col-header" style="width:130px; text-align:center">' . ucwords($obj->lang['date']) . '</div>
                        <div class="div-table-col detail-col-header" style="width:250px; text-align:left;">' . ucwords($obj->lang['note']) . '</div>
                        <div class="div-table-col detail-col-header" style="width:100px; text-align:center;">' . ucwords($obj->lang['currency']) . '</div>
                        <div class="div-table-col detail-col-header" style="width:140px; text-align:right;">' . ucwords($obj->lang['debit']) . '</div>
                        <div class="div-table-col detail-col-header" style="width:140px; text-align:right;">' . ucwords($obj->lang['credit']) . '</div>
                </div>';

    for ($i=0;$i<count($rsDetail);$i++){

			 $detailInformation  .= '
				<div class="div-table-row">
					<div class="div-table-col" style="text-align:left;">'.$rsDetail[$i]['vouchercode'].'</div>
					<div class="div-table-col" style="text-align:left;">'.$rsDetail[$i]['refcode']. '</div>
                    <div class="div-table-col" style="text-align:center;">'. $obj->formatDBDate($rsDetail[$i]['date'], 'd / M / Y') .'</div>
                    <div class="div-table-col" style="text-align:left;">'.$rsDetail[$i]['trdesc'].'</div> 
                    <div class="div-table-col" style="text-align:center;">'.$rsDetail[$i]['currencyname'].'</div> 
                    <div class="div-table-col" style="text-align:right;">'. $obj->formatNumber($rsDetail[$i]['debit']).'</div> 
                    <div class="div-table-col" style="text-align:right;">'. $obj->formatNumber($rsDetail[$i]['credit']).'</div> 
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


include('dataList.php');
