<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('PurchaseOrder.class.php');
$purchaseOrder = createObjAndAddToCol(new PurchaseOrder());


$obj = $purchaseOrder;    

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                case 'searchData' :   
                    $order = 'order by '.$obj->tableName.'.code asc'; 

                    $arrCriteria = array(); 
                    
                    // bedakan parameter kosong atau tdk pernah dikirim
                    if (isset($_GET['pkey'])){ 
                         $_GET['pkey'] = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                         array_push ($arrCriteria, $obj->tableName.'.pkey = ' . $obj->oDbCon->paramString($_GET['pkey']) );  
                    }
                    
                    if (isset($_GET['isfullreceive'])){ 
                        $_GET['isfullreceive'] = (empty($_GET['isfullreceive'])) ? 0 : $_GET['isfullreceive'];
                        array_push ($arrCriteria, $obj->tableName.'.isfullreceive = '. $obj->oDbCon->paramString($_GET['isfullreceive']));  
                    }
                      
                    if ( isset($_GET['term']) && !empty($_GET['term']) ) 
                        array_push ($arrCriteria, '('.$obj->tableSupplier.'.name like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').' or '.$obj->tableName.'.code like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );  
               
                    array_push ($arrCriteria, $obj->tableName.'.statuskey = 2' );  
                    
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 
              
                    $rs = $obj->searchDataForAutoComplete('','',false,$criteria,$order );
                    for($i=0;$i<count($rs);$i++){
                        $rs[$i]['value'] = htmlspecialchars_decode($rs[$i]['value']); 
                    }
 
                    echo json_encode($rs); 
                    break;
                    
                case 'getOutstandingDetail' :
                     
                    if (!isset($_GET) ||  empty($_GET['pkey']))  
                        die;  
                    
                    $pokey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                    
                    $arrCriteria = array(); 
                    array_push ($arrCriteria, 'qtyinbaseunit > receivedqtyinbaseunit' );  
                    
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';
                    
                     $rs = $obj->getDetailWithRelatedInformation($pokey,$criteria); 
                     echo json_encode($rs);
                    
                    break;
                    
                case 'getPriceDetail' :
                    
                    if (!isset($_GET) ||  empty($_GET['itemkey']))  
                        die;  
                    
                    $itemkey = (empty($_GET['itemkey'])) ? 0 : $_GET['itemkey'];
                    $supplierkey = (empty($_GET['supplierkey'])) ? 0 : $_GET['supplierkey'];
                    
                    $rs = $obj->getPriceItem($itemkey,$supplierkey); 
                    echo json_encode($rs);
                    
                    break;    
                    
                case 'getDetailByAP' : 
                    if (!isset($_GET['apkey'])) die;

                    $rs = $obj->getDetailByAP($_GET['apkey']);
                    
                    echo json_encode($rs); 
                    break;
                case 'updateItemDetail':

                        if (empty($_GET['pkey'])) die;

                        $rs = $obj->getLastPackagingDetailByItem($_GET['pkey']);
                        $rs = $obj->reindexDetailCollections($rs, 'itemkey');
                        
                        echo json_encode($rs);

                    break;
            }
}
if (isset($_POST) && !empty($_POST['action'])) {
    switch ($_POST['action']) {
        case 'updateInvoiceReference':

            $arrData = array();
            if (isset($_POST) && !empty($_POST['data'])) {
                $arrData = $_POST['data'];
            }

            $result = $obj->updateInvoiceReference($arrData, true);

            echo json_encode($result);

            break;
    }
}
 
 
die;
  
?>
