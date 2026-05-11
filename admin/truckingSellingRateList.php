<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('TruckingSellingRate.class.php');
$truckingSellingRate = createObjAndAddToCol(new TruckingSellingRate());

$obj = $truckingSellingRate;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'truckingSellingRateForm'; 
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama Kontrak', $obj->tableName . '.name')); 
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer . '.name')); 
array_push($arrSearchColumn, array('Consignee', $obj->tableConsignee. '.name')); 
array_push($arrSearchColumn, array('Jenis Pekerjaan', $obj->tableCargoType. '.name')); 
array_push($arrSearchColumn, array('Kategori', $obj->tableCategory . '.name'));
array_push($arrSearchColumn, array('Lokasi', $obj->tableLocation . '.name'));


function generateQuickView($obj,$id){ 
    $service = new Service(TRUCKING_SERVICE,1);
    $truckingServiceOrderCategory = new TruckingServiceOrderCategory();
    
    $detail = '';
     
    $rsHeader = $obj->getDataRowById($id);
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
 
	$detailInformation  = ' <div class="data-card border-orange">
						    <h1>'.ucwords($obj->lang['pricelist']).'</h1> 
                            <div class="content">
                            <div class="div-table  quick-view-table" >
                                  <div class="div-table-row"> 
                                        <div class="div-table-col detail-col-header" style="width:150px;">Layanan</div>
                                        <div class="div-table-col detail-col-header" style="width:80px; text-align:right">Harga</div> 
                                 </div>
                            ';
							
    for ($i=0;$i<count($rsDetail);$i++){  
			$detailInformation  .= '
				<div class="div-table-row">    
					<div class="div-table-col">'.$rsDetail[$i]['itemname'].'</div>  
                    <div class="div-table-col" style="text-align:right;">'.$obj->formatNumber($rsDetail[$i]['price']).'</div> 
				</div>
			';
    }
		
    $detailInformation .= '</div>
    </div>
    </div>';
         
	$detail .= '<div class="div-table" style="width:100%; ">
                        <div class="div-table-row">
                            <div class="div-table-col-5"  style="width:33%;">
                            '.$detailInformation.'
                            </div>  
                            <div class="div-table-col-5" ></div>
                        </div>
                </div>';

    $detail .= '<div style="clear:both;"></div>';	 
    
	return $detail;   
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>