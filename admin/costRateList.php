<?php  
require_once '../_config.php';  
require_once '../_include-v2.php'; 

includeClass('CostRate.class.php'); 
$costRate = createObjAndAddToCol(new CostRate());

$obj = $costRate;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'costRateForm'; 
$quickView = false;
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array(ucwords($obj->lang['code']), $obj->tableName . '.code'));
array_push($arrSearchColumn, array(ucwords($obj->lang['name']), $obj->tableName . '.name')); 
array_push($arrSearchColumn, array(ucwords($obj->lang['location']), $obj->tableLocation . '.name'));  
array_push($arrSearchColumn, array(ucwords($obj->lang['cargoType']), $obj->tableCargoType . '.name'));  
 
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){ 
    $detail = '';
    
    /*$cost = new Service(TRUCKING_SERVICE,1);  
    $rs = $obj->getDataRowById($id);
    
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
    $rsCost = $cost->searchData($cost->tableName.'.statuskey',1, true, ' and showincostrate = 1 and chargetype = 2','order by fixedcost desc, name asc');  

 
	$detailInformation  = ' <div class="data-card border-orange">
						    <h1>'.ucwords($obj->lang['costInformation']).'</h1> 
                            <div class="content">
                            <div class="div-table  quick-view-table" >
                                  <div class="div-table-row"> 
                                        <div class="div-table-col detail-col-header" style="width:150px;">'.ucwords($obj->lang['services']).'</div> 
                                        <div class="div-table-col detail-col-header" >'.ucwords($obj->lang['cost']).'</div> 
                                 </div>
                            ';
					
    for ($i=0;$i<count($rsDetail);$i++){  
        
          $costList = '<ul style="padding:0; margin: 0">';
          for($k=0;$k<count($rsCost);$k++) {   
                     $asterix = ($rsCost[$k]['fixedcost'] == 0) ? '' : '<span class="asterix">*</span>' ; 

                     $rsCostPrice = $obj->getCostDetail($rs[0]['jobtypekey'],$rs[0]['citykey'],$rsDetail[$i]['itemkey'], $rsCost[$k]['pkey']);   
                     $price = (empty($rsCostPrice[0]['price'])) ? 0 : $rsCostPrice[0]['price']; 

                     $costList .= '<li style="width:120px; float:left; display: inline-block; margin:0 0.2em 0.4em 0.2em">';
                     $costList .= '<div class="auto-height" ><strong>'.ucwords($rsCost[$k]['name']) . $asterix .'</strong></div>'; 
                     $costList .= '<div>'.$obj->formatNumber($price).'</div>'; 
                     $costList .= '</li>';
            } 
          $costList .= '</ul>';
        

			$detailInformation  .= '
				<div class="div-table-row">    
					<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>   
					<div class="div-table-col">'.$costList.'</div>   
				</div>
			';
    }
		
    $detailInformation .= '</div>
    </div>
    </div>';
         
	$detail .= '<div class="div-table" style="width:100%; ">
                        <div class="div-table-row">
                            <div class="div-table-col-5">
                            '.$detailInformation.'
                            </div>   
                        </div>
                </div>';

    $detail .= '<div style="clear:both;"></div>';	 */
    
	return $detail;    
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>