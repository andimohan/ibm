<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  


includeClass(array('Item.class.php',  
                   'ItemUnit.class.php',
                   'ItemMovement.class.php',
                  ));

$item = new Item(); 
$itemMovement = new ItemMovement(); 

$obj = $item;

$arrayToJs = array(); 

if(isset($_POST['action'])){
    switch ( $_POST['action']){ 
        case 'addToCompare' : 

                    if (!isset($_POST['itemkey']) || empty($_POST['itemkey']))
                        break;

                    $arrayToJs = $obj->addToCompareSession($_POST['itemkey']);  
                    break;

        case 'removeFromCompare' : 

                    if (!isset($_POST['itemkey']) || empty($_POST['itemkey']))
                        break;

                    $arrayToJs = $obj->removeFromCompareSession($_POST['itemkey']);  
                    break;

        case 'compareQty' : 
                    $arrayToJs['totalQty'] = (isset($_SESSION['itemsToCompare'])) ? count($_SESSION['itemsToCompare']) : 0;
                    break; 

        case 'updateFavoritProduct' :
                if(!isset($_POST['itemkey']) || empty($_POST['itemkey'])) die;
                if(empty(USERKEY)) die;

                $arrParam['itemKey'] = $_POST['itemkey'];
				$arrParam['hidUserKey'] = USERKEY;

                $arrayToJs = $obj->updateFavoritProduct($arrParam);  
                break;



        case 'addReview' : 
	        		
            $arrParam = array();  
			$arrParam['rating'] = $_POST['hidRating'];
			$arrParam['review'] = $_POST['review'];
			$arrParam['itemkey'] = $_POST['hidItemKey'];
			$arrParam['salesorderkey'] = $_POST['hidId'];
			$arrParam['hidUserKey'] = USERKEY;
            
			$arrayToJs = $obj->addReview($arrParam); 
            break;
    }
    

    echo json_encode($arrayToJs); 
    die;
}

     
if(isset($_GET['action'])){

    switch ($_GET['action']){ 
        case 'searchData' :  

                                $order = 'order by item.name asc'; 
                                $term = ''; 
                                $criteria = ''; 

                                $arrCriteria = array();

                                if (isset($_GET) && !empty($_GET['term'])){
                                    $term = $_GET['term'];

                                    if (isset($_GET)  && !empty($_GET['exact']) && $_GET['exact'] == 1)
                                         $criteria = $obj->tableName.'.name = '.$obj->oDbCon->paramString($term).' or '.$obj->tableName.'.code = '.$obj->oDbCon->paramString($term);
                                    else
                                         $criteria = $obj->tableName.'.name like '.$obj->oDbCon->paramString('%'.$term.'%').' or '.$obj->tableName.'.code like '.$obj->oDbCon->paramString('%'.$term.'%'). ' or '.$obj->tableBrand.'.name like '.$obj->oDbCon->paramString('%'.$term.'%').' or '.$obj->tableName.'.tag like '.$obj->oDbCon->paramString('%'.$term.'%').' or '.$obj->tableName.'.shortdescription like '.$obj->oDbCon->paramString('%'.$term.'%');

                                    array_push($arrCriteria,'('.$criteria.')') ;
                                }


                                // bedakan parameter kosong atau tdk pernah dikirim 
                                if (isset($_GET['pkey'])){  
                                     $_GET['pkey'] = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                                     array_push ($arrCriteria, $obj->tableName.'.pkey = ' . $obj->oDbCon->paramString($_GET['pkey']) );  
                                } 

                                // hanya ambil item yg aktif
                                array_push ($arrCriteria,  $obj->tableName.'.statuskey = 1 ' );  

                                $criteria = implode(' and ', $arrCriteria);

                                $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';  

                                $rsItem = array();
                                $rsItem = $obj->searchDataForAutoComplete('','',false,$criteria,$order );

                                for($i=0;$i<count($rsItem);$i++){ 
                                    
                                    $rsImage = $obj->getItemImage($rsItem[$i]['pkey']); 
                                    if(!empty($rsImage)){ 
                                        $rsItem[$i]['file'] = $rsImage[0]['file'];	
                                        $rsItem[$i]['phpThumbHash'] = getPHPThumbHash($rsImage[0]['file']);	
                                    }
 
                                    if (isset($_GET) && !empty($_GET['getQOH'])){ 
                                            $warehousekey = '';
                                            if (isset($_GET) && !empty($_GET['warehousekey']))
                                                $warehousekey = $_GET['warehousekey'];

                                            $trdate = '';
                                            if (isset($_GET) && !empty($_GET['trdate']))
                                                $trdate = $_GET['trdate'];

                                            $qoh = $itemMovement->sumItemMovement($rsItem[$i]['pkey'],$warehousekey,$trdate);
                                            $rsItem[$i]['qoh'] = $qoh;
                                    } 

                                    $rsItem[$i]['value'] = htmlspecialchars_decode($rsItem[$i]['value']); 
                                } 
 
                            echo json_encode($rsItem); 
                            break;
            
                case 'getCTA' :  
                    echo $obj->loadSetting('CTAMessage');
                    break; 
                    

                case 'getMainImage' :
                    if (!isset($_GET['itemkey']) || empty($_GET['itemkey'])) die;
            
                    $itemkey = $_GET['itemkey'];
                    
                    $rsImage = $item->getMainImage($itemkey);
                        
                    //// kalo variant, gk ad image, ambil image parentnya 
                    echo json_encode($rsImage); 
                    break;


    }
    die;
}

?>
