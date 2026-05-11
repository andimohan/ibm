<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('EMKLQuotationOrder.class.php'));
$emklQuotationOrder = createObjAndAddToCol(new EMKLQuotationOrder()); 
$emklQuotationOrderImport = createObjAndAddToCol(new EMKLQuotationOrder(EMKL['jobType']['import']));
$emklQuotationOrderExport = createObjAndAddToCol(new EMKLQuotationOrder(EMKL['jobType']['export']));
$emklQuotationOrderDomestic = createObjAndAddToCol(new EMKLQuotationOrder(EMKL['jobType']['domestic']));

$obj = $emklQuotationOrder;   
if(isset($_GET['jobtype']) && !empty($_GET['jobtype'])){
//    $obj = ($_GET['jobtype'] == EMKL['jobType']['import']) ? $emklQuotationOrderImport : $emklQuotationOrderExport;
    switch($_GET['jobtype']){
        case EMKL['jobType']['import']: $obj = $emklQuotationOrderImport; break;
        case EMKL['jobType']['export']: $obj = $emklQuotationOrderExport; break;
        case EMKL['jobType']['domestic']: $obj = $emklQuotationOrderDomestic; break;
    }
}
     
$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {

			switch ( $_GET['action']){ 
                    
                    case 'getEmklType' :

                        if (!isset($_GET) ||  empty($_GET['pkey']))  
                            die;  

                        $pkey = (empty($_GET['pkey'])) ? 0 : '*'.$_GET['pkey'].'*';
 
                         $rs = $obj->getEmklType('',$criteria); 

                         echo json_encode($rs);

                        break;
                case 'getFreightDetail' :
                     
                    if (!isset($_GET) ||  empty($_GET['pkey']))  
                        die;  
                    
                    $pkey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                    
                    
                    if (!isset($_GET) ||  empty($_GET['iscopy'])) {
                       $rs = $obj->getDetailCarrier($pkey); 

                    }else{
                      $rs = $obj->getDetailFreight($pkey); 	
                      $rs = $obj->reindexDetailCollections($rs,'polpodkey');
                    }

 
                     echo json_encode($rs);
                    
                    break;
                    
                case 'getDetailOriginInformation' :
                     
                    if (!isset($_GET) ||  empty($_GET['pkey']))  
                        die;  
                    
                    $pkey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];

                      
                    if (!isset($_GET) ||  empty($_GET['iscopy'])) {
                     $rs = $obj->getDetailOriginInformation($pkey); 

                    }else{
                     $rs = $obj->getDetailOriginInformation($pkey); 
                      $rs = $obj->reindexDetailCollections($rs,'polpodkey');
                    }
                    
                    
                     echo json_encode($rs);
                    
                    break;
                    
                case 'getLocationQuotation' :
                     
                    if (!isset($_GET) ||  empty($_GET['pkey']))  
                        die;  
                    
                    $pkey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                    $type = (empty($_GET['type'])) ? 0 : $_GET['type'];
                        
                    
                     $rs = $obj->getLocationQuotation($pkey,$type);
   
                     echo json_encode($rs);
                    
                    break;
                
                case 'getDetailLocation' :
                     
                    if (!isset($_GET) ||  empty($_GET['pkey']))  
                        die;  
                    
                    $pkey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                    $type = (empty($_GET['type'])) ? 0 : $_GET['type'];
                        
                    
                     $rs = $obj->getDetailLocation($pkey,$type);
   
                     echo json_encode($rs);
                    
                    break;
                    
                case 'getDetailServiceInformation' :
                     
                    if (!isset($_GET) ||  empty($_GET['pkey']))  
                        die;  
                    
                    $pkey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];

                    
                    if (!isset($_GET) ||  empty($_GET['iscopy'])) {
                     $rs = $obj->getDetailServiceInformation($pkey); 

                    }else{
                      $rs = $obj->getDetailServiceInformation($pkey); 
                      $rs = $obj->reindexDetailCollections($rs,'polpodkey');
                    }
 
                     echo json_encode($rs);
                    
                    break;
              
                
                case 'getQuotationPriceAndCost' :

 
                      $pkey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];

                    if (isset($_GET['iscopy']) ||  !empty($_GET['iscopy'])){
              
                      $loctypekey = $_GET['loctypekey'];
                      $rs = $obj->getDetailPriceAndCost($pkey,$opt,$loctypekey); 
                      $rs = $obj->reindexDetailCollections($rs,'pkey');
                 
                    }else{

                 
                        //$carrierkey = (empty($_GET['carrierkey'])) ? 0 : $_GET['carrierkey'];
                        $polkey = (empty($_GET['polkey'])) ? 0 : $_GET['polkey'];
                        $podkey = (empty($_GET['podkey'])) ? 0 : $_GET['podkey'];
                        //$placeofreceiptkey = (empty($_GET['placeofreceiptkey'])) ? 0 : $_GET['placeofreceiptkey'];
                        //$placeofdeliverykey = (empty($_GET['placeofdeliverykey'])) ? 0 : $_GET['placeofdeliverykey'];
                        $transportationkey = (empty($_GET['transportationkey'])) ? 0 : $_GET['transportationkey'];
                        $containertypekey = (empty($_GET['containertypekey'])) ? 0 : $_GET['containertypekey'];
                        $containerkey = (empty($_GET['containerkey'])) ? '' : explode(',',$_GET['containerkey']);
                        $currencykey = (empty($_GET['currencykey'])) ? '' : explode(',',$_GET['currencykey']);
    //                    $servicekey = (empty($_GET['servicekey'])) ? '' : explode(',',$_GET['servicekey']);


                        $opt = array();
                        //$opt['carrierkey'] = $carrierkey;
                        
						$opt['polkey'] = $polkey;
                        $opt['podkey'] = $podkey;
						
						
                        //$opt['placeofreceiptkey'] = $placeofreceiptkey;
                        //$opt['placeofdeliverykey'] = $placeofdeliverykey;
                        $opt['transportationkey'] = $transportationkey;
                        $opt['containertypekey'] = $containertypekey;
                        $opt['currencykey'] = $currencykey;

                        if(in_array($containertypekey,LCL_CONTAINER_TYPE))  $containerkey = '';    

                        if(!empty($containerkey))    
                            $opt['containerkey'] = $containerkey;
                        if(!empty($currencykey))    
                            $opt['currencykey'] = $currencykey;

            /*            if(!empty($servicekey))    
                            $opt['servicekey'] = $servicekey;*/

                        $rs = $obj->getQuotationPriceAndCost($pkey,$opt); 
                    }
 
                     echo json_encode($rs);
                    
                    break;  
                    
                case 'getContainerQuotation' :
                            
                    if (!isset($_GET) ||  empty($_GET['pkey']))  
                        die;  
                    
                    $pkey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                    $isCopy = (isset($_GET['iscopy']) ||  !empty($_GET['iscopy']))  ? true : false;
                     $loctypekey = $_GET['loctypekey'];
                     $rs = ($isCopy) ? $obj->getContainerDetail($pkey,$loctypekey) : $obj->getContainerQuotation($pkey); 
               
                     echo json_encode($rs);
                    
                    break;

                case 'getDetailCommodity' :
                     
                    if (!isset($_GET) ||  empty($_GET['pkey']))  
                        die;  
                    
                     $pkey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                    
                     $rs = $obj->getDetailCommodity($pkey); 

                     echo json_encode($rs);
                    
                    break;
               case 'getDetailTermAndCondition' :
                     
                    if (!isset($_GET) ||  empty($_GET['pkey']))  
                        die;  
                    
                     $pkey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                    
                     $rs = $obj->getDetailTermAndCondition($pkey); 

                     echo json_encode($rs);
                    
                    break;
					
				case 'getQuotationInformation' :
					 
					if(empty($_GET['pkey'])) die;
					 
					// cari data default dr quotation dulu, 
					// terus di merge dengan informasi pelayaran dan jenis container dari freight
					
					$pkey = $_GET['pkey'];
                    $rs = $obj->getQuotationInformation($pkey);
					
                    echo json_encode($rs); 
					
					break;
                    
            }
}
 
die;
  
?>