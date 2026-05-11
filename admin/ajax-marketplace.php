<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  
 
includeClass(array('Marketplace.class.php','Category.class.php','ItemCategory.class.php'));
$marketplace = createObjAndAddToCol(new Marketplace()); 
$itemCategory =  createObjAndAddToCol(new ItemCategory()); 

$obj = $marketplace;    

//$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';

$arrCriteria = array();

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){ 
                    
                case 'testConnection' :
                    if (!isset($_GET) || empty($_GET['marketplacekey'])) die;
                    
                    $obj = $obj->getMarketplaceObj($_GET['marketplacekey']);
                    $obj = $obj[0]['obj'];
                    
                    $response = $obj->testConnection();
                    //$obj->setLog($response,true,'testconn');
                    echo json_encode($response); 
                     
                    break;
                    
                case 'updateToken' :
                    
                    break;
                case 'getMarketplaceBrand' :
                    
                    if (!isset($_GET) || empty($_GET['marketplaceKey']))
                        die;
                    
                    $marketplaceKey = $_GET['marketplaceKey'];
                    
                    // replace Obj  
                    
                    $obj = $obj->getMarketplaceObj($marketplaceKey);
                    $obj = $obj[0]['obj'];
                    
                    if (isset($_GET) && !empty($_GET['term'])){
                        $term = $_GET['term'];
                            
                        $criteria = $obj->tableMarketplaceBrand.'.name like '.$obj->oDbCon->paramString('%'.$term.'%');

                        array_push($arrCriteria,'('.$criteria.')') ;
                    }

                    $criteria = implode(' and ', $arrCriteria);
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';  
                    
                    $rs = $obj->getMarketplaceBrandForAutoComplete($criteria, ' limit 25');
                    echo json_encode($rs); 
                    break;
                      
                case 'getMarketplaceCategory' :
					
                    // tetep kirim marketplacekey gpp, nanti di class akan otoamtis diambil dr providernya 
                    if (!isset($_GET) || empty($_GET['marketplaceKey']))   die;
                    
                    $marketplaceKey = $_GET['marketplaceKey'];
                    
                    // replace Obj  
                    
                    $obj = $obj->getMarketplaceObj($marketplaceKey);
                    $obj = $obj[0]['obj'];
                    
                    if (isset($_GET) && !empty($_GET['term'])){
                        $term = $_GET['term'];
                            
                        $criteria = $obj->tableMarketplaceCategory.'.name like '.$obj->oDbCon->paramString('%'.$term.'%');

                        array_push($arrCriteria,'('.$criteria.')') ;
                    }

                    $criteria = implode(' and ', $arrCriteria);
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';  
                    
                    $rs = $obj->getMarketplaceCategoryForAutoComplete($criteria, ' limit 25');
                 
                    echo json_encode($rs); 
                    break;
                    
                case 'getMarketplaceStorefront' :
                    
                    if (!isset($_GET) || empty($_GET['marketplaceKey']))
                        die;
                    
                    $marketplaceKey = $_GET['marketplaceKey'];
                    
                    // replace Obj  
                    
                    $obj = $obj->getMarketplaceObj($marketplaceKey);
                    $obj = $obj[0]['obj'];
                    
                    if (isset($_GET) && !empty($_GET['term'])){
                        $term = $_GET['term'];
                            
                        $criteria = $obj->tableMarketplaceStorefront.'.name like '.$obj->oDbCon->paramString('%'.$term.'%');

                        array_push($arrCriteria,'('.$criteria.')') ;
                    }

                    $criteria = implode(' and ', $arrCriteria);
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';  
                    
                    $rs = $obj->getMarketplaceStorefrontForAutoComplete($criteria, ' limit 25');
                  
                    echo json_encode($rs); 
                    break;
                    
                    
                case 'getMarketplaceCategoryAttributes' :
                    
                    //if (!isset($_GET) || empty($_GET['marketplaceKey']))  die;
                    if (!isset($_GET) || empty($_GET['categorykey']))  die;
                    
                    $marketplaceKey = (isset($_GET) && !empty($_GET['marketplaceKey'])) ? $_GET['marketplaceKey'] : '';
                    $categorykey = $_GET['categorykey'];
                    
                    // replace Obj  
                    

                    $rs = array();
                    $marketplaceObjs = $marketplace->getMarketplaceObj($marketplaceKey);
                    
                    foreach($marketplaceObjs as  $marketplaceRow){ 
                        
                        $marketplaceKey = $marketplaceRow['key']; 
                        $marketplaceObj = $marketplaceRow['obj'];
                            
                        $arrCriteria = array();
                        array_push($arrCriteria, 'ismandatory = 1');
                 
                        $criteria = implode(' and ', $arrCriteria);
                        $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';  

                        // cari konversi kategori di marketplace
                        $rsMarketplaceCategory = $itemCategory->getMarketplaceCategory( $categorykey,$marketplaceKey);
                        $marketplacecategorykey = (!empty($rsMarketplaceCategory)) ?  $rsMarketplaceCategory[0]['marketplacecategorykey'] : 0 ;
                        
                        // pake order desc, agar kebalik, menyesuaikan dengan load php
                        $attribute = $marketplaceObj->getMarketplaceCategoryAttributes($marketplacecategorykey,$criteria);
                        
                        foreach($attribute as $key=>$row){ 
                             
                            $options = array();
                            if ($row['inputtype'] == INPUT_TYPE['select'])
                                $options = $marketplaceObj->getSelectOptions($row['value']);
                            else if ( $row['inputtype'] == INPUT_TYPE['autocompletejs'] )
                                $options = array_keys($marketplaceObj->getSelectOptions($row['value']));
                                
                            $label = $row['label'];
                             
                            $attribute[$key]['input'] = $setting->getInput(array('multivalue' => 0, 'type' => $row['inputtype'], 'code' => 'attributeValue[]', 'options' => $options, 'etc' => 'attr-label="'.strtolower($label).'"' ));
                            
                            // add jg hidden jsonnya, karena kepake buat ambil value id nya utk shopee
                            $attribute[$key]['input'] .= $obj->inputHidden('hidRawOpt[]',array('value' => base64_encode($row['value'])));
                        }
                        
                        array_push($rs, array(
                                              'marketplacekey' =>$marketplaceKey, 
                                              'attributes' => $attribute, 
                                              'label' => $label  
                                             )
                                 ); 
                    } 
 
                    echo json_encode($rs); 
                    break;
                    
                         
                case 'getMarketplaceLogistics' :
                    
                    if (!isset($_GET) || empty($_GET['marketplaceKey']))
                        die;
                    
                    $marketplaceKey = $_GET['marketplaceKey'];
                    
                    // replace Obj  
                    
                    $obj = $obj->getMarketplaceObj($marketplaceKey);
                    $obj = $obj[0]['obj'];
                    
                    if (isset($_GET) && !empty($_GET['term'])){
                        $term = $_GET['term'];
                            
                        $criteria = $obj->tableMarketplaceLogistics.'.name like '.$obj->oDbCon->paramString('%'.$term.'%');

                        array_push($arrCriteria,'('.$criteria.')') ;
                    }

                    $criteria = implode(' and ', $arrCriteria);
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';  
                    
                    $rs = $obj->getMarketplaceLogisticsForAutoComplete($criteria);
                  
                    echo json_encode($rs); 
                    break;
                    
                case  'getMarketplaceCategoryVariant' : 
                    
                    if (!isset($_GET) || empty($_GET['categorykey']))  die;
                    
                    $marketplaceKey = (isset($_GET) && !empty($_GET['marketplaceKey'])) ? $_GET['marketplaceKey'] : '';
                    $parentkey = (isset($_GET) && !empty($_GET['parentkey'])) ? $_GET['parentkey'] : '';
                    $categorykey = $_GET['categorykey'];
                    
                    $rs = array();
                    $marketplaceObjs = $marketplace->getMarketplaceObj($marketplaceKey);
                    
                    // kalo gk ad variant, harusny return empty...
                    if(empty($parentkey)) return;
                    
                    foreach($marketplaceObjs as  $marketplaceRow){ 
                        
                        $marketplaceKey = $marketplaceRow['key']; 
                        $marketplaceObj = $marketplaceRow['obj'];
                         
                        // cari konversi kategori di marketplace
                        $rsMarketplaceCategory = $itemCategory->getMarketplaceCategory( $categorykey,$marketplaceKey);
                        $marketplacecategorykey = (!empty($rsMarketplaceCategory)) ?  $rsMarketplaceCategory[0]['marketplacecategorykey'] : 0 ;
 
                        $marketplaceVariant = $marketplaceObj->getMarketplaceCategoryVariant($marketplacecategorykey,$parentkey);
                        
                        /*$arrVariant = array();
                        foreach($marketplaceVariant as $row)
                            array_push($arrVariant, array('key' => $row['key'] , 'label' => $row['label']))
                     */
                        $rs[$marketplaceKey] = $marketplaceVariant;
                         
                    }
                       
                    echo json_encode($rs); 
                    break;
            }
}
 
die;
  
?>