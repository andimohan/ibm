<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('EMKLOrderInvoice.class.php');
$emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice());

$obj = $emklOrderInvoice;    
$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php'; 
 

 if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  

//      		case 'searchDataInvoice' :  
//
//                    $order = 'order by '.$obj->tableName.'.code asc';   
//                    
//                    $returnField = array('key' => $obj->tableName.'.pkey','value' => $fieldValue) ;
//                    $searchFieldValue = (isset($_GET['searchField']) && !empty($_GET['searchField'])) ? explode(',',$_GET['searchField']) : $fieldValue;
//                    $searchOptions = array('field' => $searchFieldValue,  'key' => $_GET['term']) ;
// 
//                    $arrCriteria = array(); 
//       
//
//                    if (isset($_GET['customerkey']) && !empty($_GET['customerkey'])){  
//                        array_push ($arrCriteria, $obj->tableName.'.customerkey = '. $obj->oDbCon->paramString($_GET['customerkey']));  
//                    }
//                    
//                    
//                    if (isset($_GET['currencykey']) && !empty($_GET['currencykey'])){  
//                        array_push ($arrCriteria, $obj->tableName.'.currencykey = '. $obj->oDbCon->paramString($_GET['currencykey']));  
//                    }
//                    
//                    
//                    if (isset($_GET['warehousekey']) && !empty($_GET['warehousekey'])){  
//                        array_push ($arrCriteria, $obj->tableName.'.warehousekey = '. $obj->oDbCon->paramString($_GET['warehousekey']));  
//                    }
//
//                    array_push ($arrCriteria, $obj->tableName.'.statuskey in (2,3)' );  
//
//                    $criteria = implode(' and ', $arrCriteria);  
//
//                    $searchOptions['criteria'] = ' and ' . $criteria; 
//
//                    $rsData = $obj->searchDataForAutoComplete($returnField,$searchOptions,$order); 
//
//
//                    echo json_encode($rsData); 
//                    break;

				case 'getInvoiceItemDetail' :
                    if (empty($_GET['pkey'])) die;
                     
                    $pkey = $_GET['pkey'];
                    
                    $rs = $obj->getItemDetail($pkey,'refheaderkey');
                    $rs = $obj->reindexDetailCollections($rs,'refheaderkey');
//                    $obj->setLog($rs,true);
                    echo json_encode($rs); 
                    break; 

                case 'getCostReconsileByInvoice' : 

                    $pkey = $_GET['pkey']; 

					$currencykey = (!empty($_GET['currencykey'])) ? $_GET['currencykey'] : '';
					$warehousekey = (!empty($_GET['warehousekey'])) ? $_GET['warehousekey'] : '';
                    $rsCost = $obj->getCostReconsileByInvoice($pkey,$currencykey,$warehousekey);
                    
                    $rs = (empty($rsCost)) ? array() : $rsCost;
                    
                    echo json_encode($rs); 
                    
                    break; 
                    
                case 'getItemDetailByHeader' : 

                    $pkey = $_GET['invoicekey']; 

                    $rs = $obj->getItemDetail($pkey,'refheaderkey');
                    echo json_encode($rs); 
                    
                    break; 
					
				case 'getTaxPercentageType' : 
                  
                    if (empty($_GET['pkey'])) die;

                    $pkey =  $_GET['pkey'];  
                    $rsResult = $obj->getTaxPercentageType($pkey);
                     
                    echo json_encode($rsResult); 
                    break;    
                  
				case 'searchDataForVatOut':
 
						$criteria = array();  
					
					
						// kalo search manual gpp, karena utk revisi
//						if(!isset($_GET['hastaxinvoice']) || empty($_GET['hastaxinvoice']))
//							array_push($criteria, $obj->tableName.'.reftaxinvoicekey = 0');
//						else
//							array_push($criteria, $obj->tableName.'.reftaxinvoicekey != 0');
//							
                    
                    
				        array_push($criteria, $obj->tableName.'.refvatoutkey = 0');
                    
						if (isset($_GET['term']) && !empty($_GET['term']))  
							array_push($criteria, $obj->tableName.'.code like ' . $obj->oDbCon->paramString( '%'. $_GET['term'] . '%') );

                        // nanti tergantung jenis form invocienya, ppnnya di detail ap difooter
						if (isset($_GET['taxType']) && $_GET['taxType'] >= 0)  
							array_push($criteria, $obj->tableNameItemDetail.'.taxdetail =' . $obj->oDbCon->paramString( $_GET['taxType']) );

						if(!empty($_GET['warehouseKey'])) 
							array_push($criteria, $obj->tableName.'.warehousekey =' . $obj->oDbCon->paramString( $_GET['warehouseKey']) );

//						if(!empty($_GET['businessUnitKey'])) 
//							array_push($criteria, $obj->tableName.'.businessunitkey =' . $obj->oDbCon->paramString( $_GET['businessUnitKey']) );
 
						if(!empty($_GET['period'])) { 
							array_push($criteria, 'month('.$obj->tableName.'.trdate) = ' . $obj->oDbCon->paramString( date("m",strtotime($_GET['period']))));
							array_push($criteria, 'year('.$obj->tableName.'.trdate) = ' . $obj->oDbCon->paramString( date("Y",strtotime($_GET['period']))));
						}
					
						$criteria = implode(' and ', $criteria);
						
						if (!empty($criteria)) $criteria = ' and '. $criteria;
					
						$searchOptions = array();
						$searchOptions['criteria'] = $criteria;
  
						$rsData = $obj->generateDataForVatOut($searchOptions);

						echo json_encode($rsData);
						break;
					
                    
            
            }
}
die;
  
?>
