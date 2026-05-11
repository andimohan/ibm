<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass('TruckingServiceWorkOrder.class.php');
$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());
$supplier = createObjAndAddToCol(new Supplier());
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());

$obj = $truckingServiceWorkOrder;

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'truckingServiceWorkOrderForm';

// sementara     
$customFile = $obj->getPersonalizedFiles($FILE_NAME);   
if($customFile <> $FILE_NAME) include DOC_ROOT.$customFile;
    
function generateQuickView($obj,$id){ 

    if(function_exists('customGenerateQuickView'))
        return customGenerateQuickView($obj,$id);
    
    //$service = new Service(TRUCKING_SERVICE,1);
    
    $rs = $obj->searchData($obj->tableName.'.pkey', $id);
    $truckingType = $obj->loadSetting('truckingType');
    
    //$rsServiceCost =  $service->searchData($service->tableName.'.statuskey',1,true,' and itemtype = 2 ', ' order by fixedcost desc, name asc');  
   
    $generalInformation = '<div class="data-card border-orange">
						<h1>'.ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table  general-information-table">  
                        <div class="div-table-row">
                            <div class="div-table-col">'.ucwords($obj->lang['code']).'</div>
                            <div class="div-table-col">'.$rs[0]['code'].'</div> 
                        </div>  
                        <div class="div-table-row">
                            <div class="div-table-col">'.ucwords($obj->lang['jobOrderDate']).'</div>
                            <div class="div-table-col">'.$obj->formatDBDate($rs[0]['trdate']).'</div> 
                        </div>    
                        <div class="div-table-row">
                            <div class="div-table-col">'.ucwords($obj->lang['warehouse']).'</div> 
                            <div class="div-table-col">'.$rs[0]['warehousename'].'</div> 
                        </div>  
                        <div class="div-table-row">
                            <div class="div-table-col">'.strtoupper($obj->lang['si']).'</div>
                            <div class="div-table-col">'.$rs[0]['donumber'].'</div> 
                        </div>   
                        <div class="div-table-row">
                            <div class="div-table-col"></div>
                            <div class="div-table-col"  style="height:1em"></div>
                        </div>
                        <div class="div-table-row">
                            <div class="div-table-col">'.ucwords($obj->lang['customer']).'</div>
                            <div class="div-table-col">'.$rs[0]['customername'].'</div> 
                        </div>
                        <div class="div-table-row">
                            <div class="div-table-col">'.ucwords($obj->lang['consignee']).'</div>
                            <div class="div-table-col">'.$rs[0]['consigneename'].'</div> 
                        </div>
                        <div class="div-table-row">
                                    <div class="div-table-col"></div>
                                    <div class="div-table-col"  style="height:1em"></div>
                        </div>';
    
    if ($rs[0]['isoutsource'] == 1){ 
         $generalInformation .= '
                                <div class="div-table-row text-blue-munsell">
                                    <div class="div-table-col">'.ucwords($obj->lang['supplier']).'</div>
                                    <div class="div-table-col">'.$rs[0]['outsourcename'].'</div> 
                                </div>
                                <div class="div-table-row">
                                    <div class="div-table-col">'.ucwords($obj->lang['car']).'</div>
                                    <div class="div-table-col">'.$rs[0]['outsourcecarregistrationnumber'].'</div> 
                                </div>
                                <div class="div-table-row">
                                    <div class="div-table-col">'.ucwords($obj->lang['cost']).'</div>
                                    <div class="div-table-col">'.$obj->formatNumber($rs[0]['outsourcecost']).'</div> 
                                </div>'; 
    }else{
        $generalInformation .= '
                                <div class="div-table-row">
                                    <div class="div-table-col">'.ucwords($obj->lang['driver']).'</div>
                                    <div class="div-table-col">'.$rs[0]['drivername'].'</div> 
                                </div>
                                <div class="div-table-row">
                                    <div class="div-table-col">'.ucwords($obj->lang['car']).'</div>
                                    <div class="div-table-col">'.$rs[0]['policecode'] . ' - ' .$rs[0]['policenumber'].'</div> 
                                </div>
                                <div class="div-table-row">
                                    <div class="div-table-col">'.ucwords($obj->lang['chassis']).'</div>
                                    <div class="div-table-col">'.$rs[0]['chassisnumber'].'</div> 
                                </div>';   
        
    }
	
	
     $generalInformation .= '    <div class="div-table-row">
                                    <div class="div-table-col"></div>
                                    <div class="div-table-col"  style="height:1em"></div>
								</div>
								<div class="div-table-row">
                                    <div class="div-table-col">'.ucwords($obj->lang['note']).'</div>
                                    <div class="div-table-col">'.nl2br($rs[0]['trdesc']).'</div> 
                                </div>';
	
    $generalInformation .= '</div>
                        </div>
                        </div>'; 
    
    $costInformation = '<div class="data-card border-blue">
						<h1>'.ucwords($obj->lang['cost']).'</h1> 
						<div class="content">
						<div class="div-table  general-information-table">';
      
    //for ($i=0;$i<count($rsServiceCost); $i++) { 
 
    $rsCost = $obj->getCostDetail($id);
    for($i=0;$i<count($rsCost);$i++){
        
        $requestAmount = false;
        $cost = (!empty($rsCost)) ? $rsCost[$i]['amount'] : 0;

        if ($cost == 0){
          if(empty($rsCost[$i]['requestamount']))   continue;  
            
          $requestAmount = true;    
          $cost = $rsCost[$i]['requestamount'];
        } 

        $asterix = ($requestAmount) ? '<span class="asterix">*</span>' : '';  

        $costInformation .= '
            <div class="div-table-row">
                <div class="div-table-col" style="width:35%">'.$rsCost[$i]['name'].$asterix.'</div>
                <div class="div-table-col">'.$obj->formatNumber($cost).'</div> 
            </div>   
        ';
    }
    
          
    //}
    
    $costInformation .= '</div></div></div>';

    $arrContainer = array();
    if (!empty( $rs[0]['containernumber']))
    array_push($arrContainer, $rs[0]['containernumber']);
    
    if (!empty( $rs[0]['container2number']))
    array_push($arrContainer, $rs[0]['container2number']);
    $containerNumber = implode('<br>',$arrContainer); 
    
    $arrSeal = array();
    if (!empty( $rs[0]['sealnumber']))
    array_push($arrSeal, $rs[0]['sealnumber']);
    
    if (!empty( $rs[0]['seal2number']))
    array_push($arrSeal, $rs[0]['seal2number']);
    $sealNumber = implode('<br>',$arrSeal);
    
    $containerInformation = '<div class="data-card border-green">
						<h1>'.ucwords($obj->lang['stuffing']).'</h1> 
						<div class="content">
						<div class="div-table  general-information-table"> 
                        <div class="div-table-row">
                            <div class="div-table-col" style="width:50%">'.ucwords($obj->lang['container']).'</div>
                            <div class="div-table-col">'.$containerNumber.'</div> 
                        </div>   
                        <div class="div-table-row">
                            <div class="div-table-col">'.ucwords($obj->lang['seal']).'</div>
                            <div class="div-table-col">'.$sealNumber.'</div> 
                        </div> 
                        <div class="div-table-row">
                            <div class="div-table-col"></div>
                            <div class="div-table-col"  style="height:1em"></div>
                        </div> 
                        <div class="div-table-row">
                            <div class="div-table-col">'.ucwords($obj->lang['depot']).'</div>
                            <div class="div-table-col">'.$rs[0]['depotname'].'</div> 
                        </div> 
                        <div class="div-table-row">
                            <div class="div-table-col">'.ucwords($obj->lang['terminal']).'</div>
                            <div class="div-table-col">'.$rs[0]['terminalname'].'</div> 
                        </div>  
                        </div>
                        '; 
    
    $detail = '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5" style="width:33%;">
								'.$generalInformation.'
								</div> 
								<div class="div-table-col-5" style="width:33%;">
								'.$costInformation.'
								</div> 
								<div class="div-table-col-5" style="width:33%;">
								'.$containerInformation.'
								</div>  
							</div>
				</div>';
				  
    $detail .= '<div style="clear:both;"></div>';	
    

	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');

?>