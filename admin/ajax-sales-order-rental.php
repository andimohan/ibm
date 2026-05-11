<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass('SalesOrderRental.class.php');
$salesOrderRental = createObjAndAddToCol(new SalesOrderRental());   

$obj = $salesOrderRental;    

if(!isset($arrCriteria)) $arrCriteria = array();   
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
                    
                    if (isset($_GET['isfulldeliver'])){ 
                        $_GET['isfulldeliver'] = (empty($_GET['isfulldeliver'])) ? 0 : $_GET['isfulldeliver'];
                        array_push ($arrCriteria, $obj->tableName.'.isfulldeliver = '. $obj->oDbCon->paramString($_GET['isfulldeliver']));  
                    }
                      
                    if ( isset($_GET['term']) && !empty($_GET['term']) ) 
                        array_push ($arrCriteria, '('.$obj->tableCustomer.'.name like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').' or '.$obj->tableName.'.code like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );  
               
                    
                    if ( isset($_GET['statuskey']) && !empty($_GET['statuskey']) ) 
                        array_push ($arrCriteria, $obj->tableName.'.statuskey in ('.$_GET['statuskey'].')' );  
                    else
                        array_push ($arrCriteria, $obj->tableName.'.statuskey in (2,3)' );  
                    
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 
              
                    $rs = $obj->searchDataForAutoComplete('','',false,$criteria,$order );
                    for($i=0;$i<count($rs);$i++){
                        $rs[$i]['value'] = htmlspecialchars_decode($rs[$i]['value']); 
                    }
 
                    echo json_encode($rs); 
                    break;
                    
                case 'getDataRowById' :
                    
                    if (!isset($_GET['pkey'])) die;
                    
                    $pkey = $_GET['pkey'];
                    
                    $arrCriteria = array(); 
                    array_push ($arrCriteria, $obj->tableName.'.pkey = ' .  $obj->oDbCon->paramString($pkey));   
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';   

                    //pakai searchdata agar dapat narik informasi join table yg lain.
                    //jgn pake getDataRowById
                    $rsData = $obj->searchData('','',true,$criteria);
                    
                    echo json_encode($rsData); 
                    break; 
                
                    case 'getDetailById' : 
                        if (!isset($_GET['pkey'])) die;
                        
                        $pkey = $_GET['pkey'];
                        $arrCriteria = array();   
                        $criteria = implode(' and ', $arrCriteria);  
                        $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';   
    
                        //pakai searchdata agar dapat narik informasi join table yg lain. 
                        $rsData = $obj->getDetailWithRelatedInformation($pkey,$criteria);
                        
                        echo json_encode($rsData); 
                        break; 
                    
		 case 'getDetailForInvoice' : 
                    if (!isset($_GET['pkey'])) die;

                    $pkey = $_GET['pkey'];
//                    $trDate =  $_GET['trdate'];
                    $startDate =  date('Y-m-d',strtotime($_GET['startdate']));
                    $endDate =  date('Y-m-d',strtotime($_GET['enddate']));
                    
                    if($startDate>$endDate)
                        die;

                    //$criteria = implode(' and ', $arrCriteria);  
                    //$criteria = (!empty($criteria)) ? ' and ' . $criteria : '';   

                    $rsData = $obj->getItemForInvoice($pkey,$startDate,$endDate);

                    echo json_encode($rsData); 
                    break; 
					
					
		
		case 'getCalculateDate' : 
                    if (!isset($_GET['startdate']) || !isset($_GET['enddate'])) die;
					
					//strtotime
					$timeStart = strtotime(str_replace(' / ', '-', $_GET['startdate']));
					$timeRest = ($_GET['restdate']==DEFAULT_EMPTY_DATE_Time) ? 0 : strtotime(str_replace(' / ', '-', $_GET['restdate']));
					$timeStart2 = ($_GET['startdate2']==DEFAULT_EMPTY_DATE_Time) ? 0 : strtotime(str_replace(' / ', '-', $_GET['startdate2']));
					//$timeRest2 = ($_GET['restdate2']==DEFAULT_EMPTY_DATE_Time) ? 0 : strtotime(str_replace(' / ', '-', $_GET['restdate2']));
					$timeEnd = strtotime(str_replace(' / ', '-', $_GET['enddate']));
					
					if($timeStart>$timeEnd) die;
					
					$workingTime = 0;
					$restHour = 0;
					//selisih jam kerja awal dan akhir
					$endTime = $timeEnd - $timeStart;
					$endTime = ceil($endTime/(60 * 60));
					$workingTime = $endTime;
					if($timeRest>0 && $timeStart2>0 && $timeRest>$timeStart && $timeStart2>$timeRest){
						$restTime = $timeStart2 - $timeRest;
						$restTime = ceil($restTime/(60 * 60));
						if($restTime>0)
							$workingTime -= $restTime;
						
					}
					$rsData = array();
					$rsData[0]['worktime'] = $workingTime;
					$rsData[0]['resttime'] = $restTime;
					
                    echo json_encode($rsData); 
					//$obj->setLog($rsData,true);
                    break; 
		
					
            }
}

die;
  
?>
