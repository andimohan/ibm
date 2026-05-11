<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('EMKLJobOrder.class.php', 'ItemUnit.class.php'));
$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder()); 
$emklJobOrderImport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['import']));
$emklJobOrderExport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['export']));
$emklJobOrderDomestic = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['domestic']));
$emklJobOrderWarehouse= createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['warehouse']));
$emklJobOrderTrucking= createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['trucking']));

$itemUnit = new ItemUnit();

$obj = $emklJobOrder;    
if(isset($_GET['jobtype']) && !empty($_GET['jobtype'])){
    //$obj = ($_GET['jobtype'] == EMKL['jobType']['import']) ? $emklJobOrderImport : $emklJobOrderExport;
    
    switch($_GET['jobtype']){
        case EMKL['jobType']['import'] : $obj = $emklJobOrderImport; break;
        case EMKL['jobType']['export'] : $obj = $emklJobOrderExport; break;
        case EMKL['jobType']['domestic'] : $obj = $emklJobOrderDomestic; break;
        case EMKL['jobType']['warehouse'] : $obj = $emklJobOrdeWarehouse; break;
        case EMKL['jobType']['trucking'] : $obj = $emklJobOrderTrucking; break;
        default :  $obj = $emklJobOrderImport;
    }

}
     
$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){ 
     
                case 'searchDataMaster' :   
 
                     $order = 'order by '.$obj->tableName.'.code asc';
                    
                    $returnField = array('key' => $obj->tableName.'.pkey','value' => $fieldValue) ;
                    $searchFieldValue = (isset($_GET['searchField']) && !empty($_GET['searchField'])) ? explode(',',$_GET['searchField']) : $fieldValue;
                    $searchOptions = array('field' => $searchFieldValue,  'key' => $_GET['term']) ;
 
                     $arrCriteria = array(); 
                     array_push ($arrCriteria, $obj->tableName.'.ismaster = 1' );
                     array_push ($arrCriteria, $obj->tableName.'.jobtypekey = ' . $obj->oDbCon->paramString($obj->jobType) );
                     array_push ($arrCriteria, $obj->tableName.'.statuskey in (2,3)');
                 
                    $criteria = implode(' and ', $arrCriteria);  

                    $searchOptions['criteria'] = ' and '.$criteria; 
                    
                    $rsData = $obj->searchDataForAutoComplete($returnField,$searchOptions,$order); 
 
                    echo json_encode($rsData);   
                    break; 
 
                case 'getUnInvoicedItemDetail' :
                    if (empty($_GET['pkey'])) die;

                    // utk ek jensi reimburse atau bukan pake field servicetype saja, 
                    // agar lebih fleksible kedepannya dalam pengelompokan data
                    
                    $customCodeKey = $_GET['typekey'];
                    $rsCustomCode = $customCode->getDataRowById($customCodeKey);
                    $serviceType = (!empty($rsCustomCode)) ? $rsCustomCode[0]['servicetype'] : 0;
                    
                    $rs = $obj->getUnInvoicedItemDetail($_GET['pkey'],'',$serviceType);
                    
                    $rs = $obj->reindexDetailCollections($rs,'refkey');
                    
                    echo json_encode($rs); 
                    break; 

      
                case 'getJobOrderByDetailId' : 
                    if (empty($_GET['pkey'])) die;

                    $rsDetail = $obj->getDetailByColumn($obj->tableNameDetail.'.pkey ',$_GET['pkey'],true);
					if(empty($rsDetail)) return array();
					
                    $rsHeader = $obj->searchData('','',true, ' and '.$obj->tableName.'.pkey = '.$obj->oDbCon->paramString($rsDetail[0]['refkey']).'
                                                        and '.$obj->tableName.'.statuskey in (1,2,3)');
					
					// gk bisa pake is empty ke 0.000
					if($rsDetail[0]['weight'] > 0) $rsHeader[0]['weight'] = $rsDetail[0]['weight'];
					if($rsDetail[0]['measurement'] > 0) $rsHeader[0]['measurement'] = $rsDetail[0]['measurement'];
					
					$rsDetail[0]['package'] = '';
					if($rsDetail[0]['qty'] > 0){
						$rsUnit = $itemUnit->getDataRowById($rsDetail[0]['unitkey']);	
						$rsHeader[0]['package'] =  ($rsDetail[0]['qty'] > 0) ? $obj->formatNumber($rsDetail[0]['qty']) . ' '. $rsUnit[0]['name'] : ''; 
					}
					
                    echo json_encode($rsHeader); 
                    break; 

           case 'getDetailContainer':

                if (empty($_GET['pkey']))
                    die;

                $rsContainer = $obj->getDetailContainer($_GET['pkey']);

                if (empty($rsContainer))
                    return array();

                $arrContainer = array();

                $qty = 0;
                $netWeight = 0;
                $grossWeight = 0;
                $chargeWeight = 0;
                $meas = 0;

                for ($i = 0; $i < count($rsContainer); $i++) {
                    $qty += $rsContainer[$i]['qty'];
                    $netWeight += $rsContainer[$i]['netweight'];
                    $grossWeight += $rsContainer[$i]['grossweight'];
                    $chargeWeight += $rsContainer[$i]['chargeweight'];
                    $meas += $rsContainer[$i]['meas'];
                }

                $arrContainer[0]['qty'] = $qty;
                $arrContainer[0]['unitkey'] = $rsContainer[0]['unitkey'];//ambil index ke 0
                $arrContainer[0]['grossweight'] = $grossWeight;
                $arrContainer[0]['chargeweight'] = $chargeWeight;
                $arrContainer[0]['netweight'] = $netWeight;
                $arrContainer[0]['meas'] = $meas;

                echo json_encode($arrContainer);
                break;

                case 'searchDataForInvoice' :   
                    $order = 'order by '.$obj->tableNameDetail.'.code asc'; 

                    $arrCriteria = array(); 

                    // bedakan parameter kosong atau tdk pernah dikirim
                    if (isset($_GET['pkey'])){ 
                         $_GET['pkey'] = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                         array_push ($arrCriteria, $obj->tableNameDetail.'.pkey = ' . $obj->oDbCon->paramString($_GET['pkey']) );  
                    }

                    if (isset($_GET['customerkey']) && !empty($_GET['customerkey'])){  
                        array_push ($arrCriteria, $obj->tableNameDetail.'.customerkey = '. $obj->oDbCon->paramString($_GET['customerkey']));  
                    }
 

                    if ( isset($_GET['term']) && !empty($_GET['term']) ) 
                        array_push ($arrCriteria, '('.$obj->tableNameDetail.'.code like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );  

                    array_push ($arrCriteria, $obj->tableName.'.statuskey = 2' );  

                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 

                    $rs = $obj->searchDataForInvoice('','',false,$criteria,$order );  
                     
                    for($i=0;$i<count($rs);$i++){
                        $rs[$i]['value'] = htmlspecialchars_decode($rs[$i]['value']); 
                    }

                    echo json_encode($rs); 
                    break;
		
                case 'getContainerDetailForHBL' :

                    if (empty($_GET['jokey'])) die;

                    
                    $rsContainer = $obj->getDetailContainer($_GET['jokey']);

                    if(isset($_GET['containerkey'])) {

                        $jokey = 0;

                        if(empty($_GET['containerkey'])) die;

                        $jodetailkey = $_GET['jokey'];

                        $rsDetail = $obj->getDetailByColumn($obj->tableNameDetail.'.pkey ',$_GET['jokey'],true);
                        
                        if(empty($rsDetail)) return array();

                        $jokey = $rsDetail[0]['refkey'];

                        $criteria = ' and ' . $obj->tableContainerDetail . '.pkey = '. $obj->oDbCon->paramString($_GET['containerkey']) .' ';
                        $rsContainer = $obj->getDetailContainer($jokey, $criteria);
                    
                    }

                    if (empty($rsContainer))
                        return array();
                    echo json_encode($rsContainer);
                break;	
                     

                case 'getContainerVolume':

                    if (empty($_GET['pkey']))
                        die;
        
                    $pkey = $_GET['pkey'];
                    $rs = $obj->getDetailVolume($pkey);
        
                    echo json_encode($rs);
        
                    break;
                   
} 
}
die;
 
?>
