<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $itemFilter;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'itemFilterForm'; 
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Kategori', $obj->tableCategory. '.name'));
 
$arrColumn = array ();
array_push($arrColumn, array('Kode','code',100));
array_push($arrColumn, array('Nama','name'));
array_push($arrColumn, array('Kategori','categoryname',200));
array_push($arrColumn, array('Status','statusname',70)); 
		 
   
function generateQuickView($obj,$id){ 
	$item = new Item();
	    
	$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id,true);   
 	$rsDetail = $obj->getDetailById($id);
	
	$basicInformation  = ' <div class="data-card no-border">
						<h1>Detail Item</h1> 
						<div class="content">
						<div class="div-table">
							  ';
								
		for ($i=0;$i<count($rsDetail);$i++){
			
			$rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);

			$basicInformation  .= '
				<div class="div-table-row"> 
					<div class="div-table-col">'.$rsItem[0]['name'].'</div>
				</div>
			';
		}
								
		$basicInformation  .= ' </div>
						</div>
					</div>  
		'; 	
		
		$detail .= $basicInformation;
				  
		$detail .= '<div style="clear:both;"></div>';
	   
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>