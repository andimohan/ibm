<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('EMKLJobOrderHeader.class.php');
$emklJobOrderHeader = createObjAndAddToCol(new EMKLJobOrderHeader()); 
$emklJobOrderHeaderImport = createObjAndAddToCol(new EMKLJobOrderHeader(EMKL['jobType']['import']));
$emklJobOrderHeaderExport = createObjAndAddToCol(new EMKLJobOrderHeader(EMKL['jobType']['export']));
$emklJobOrderHeaderDomestic = createObjAndAddToCol(new EMKLJobOrderHeader(EMKL['jobType']['domestic']));

$obj = $emklJobOrderHeader;    

if(isset($_GET['jobtype']) && !empty($_GET['jobtype'])){
    //$obj = ($_GET['jobtype'] == EMKL['jobType']['import']) ? $emklJobOrderImport : $emklJobOrderExport;
    
    switch($_GET['jobtype']){
        case EMKL['jobType']['import'] : $obj = $emklJobOrderHeaderImport; break;
        case EMKL['jobType']['export'] : $obj = $emklJobOrderHeaderExport; break;
        case EMKL['jobType']['domestic'] : $obj = $emklJobOrderHeaderDomestic; break;
        default :  $obj = $emklJobOrderHeaderImport;
    }

}


$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';
    

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){ 
                 case 'getContainerVolume':

                    if (empty($_GET['pkey']))
                        die;
        
                    $pkey = $_GET['pkey'];
                    $rs = $obj->getDetailWithRelatedInformation($pkey);
        
                    echo json_encode($rs);
        
                    break;
                     

       
            }
} 

die;
  
?>
