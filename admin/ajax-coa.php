<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('ChartOfAccount.class.php');
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());

$obj = $chartOfAccount;  
if (isset($_POST) && !empty($_POST['action'])) {
			switch ( $_POST['action']){ 
     
                case 'monthlyClosing' : 
                        $errCode =  $obj->monthlyClosing();
                        echo json_encode($errCode);  
                        break;
                
                case 'reverseClosingMonthly' :
                        $errCode =  $obj->reverseClosingMonthly();
                        echo json_encode($errCode);  
                        break;
                    
                    
                case 'getRunningMonth' :
                        $runningMonth = $obj->getRunningPeriod();
                        $runningMonth = $class->formatDBDate($runningMonth[0]['runningmonth'],'M Y');
                        echo json_encode($runningMonth);  
                        break;
                    
                case 'getTotalClosedPeriod' : 
                        $totalRows = $obj->getTotalClosedPeriod(); 
                        echo json_encode($totalRows);  
                        break;
                
                case 'getRunningNumber' :  
                    if (!isset($_POST) || empty($_POST['pkey']) ){
                        $counter = array(0,0);
                    } else{  
                        
                        switch ($_POST['resetTypeKey']) { 
                               
                                case '3':  
                                case '4':
                                    $trDate = $_POST['trDate'];
                                    break; 
                                
                                default : 
                                    $trDate =  $_POST['trDate']; 
                                    break;

                        } 

                        $counter = $chartOfAccount->getRunningNumber($_POST['pkey'],$_POST['resetTypeKey'],$trDate);
                         
                    }
 
                    echo json_encode($counter);   
                    break;
           case 'updateRevaluation' :  
                    $arrResult = $chartOfAccount->calculateRevaluation( $chartOfAccount->getRunningPeriod()[0]['runningmonth']);
                    echo json_encode($arrResult);  
                    break;

                    
            }

}
 


if (isset($_GET) && !empty($_GET['action'])) {
            switch ( $_GET['action']){  
                case 'searchData' :    

                                $order = 'order by '.$obj->tableName.'.code asc';

                                $arrCriteria = array(); 
                                array_push ($arrCriteria, ' and '.$obj->tableName.'.isleaf = 1' );

                                if (isset($_GET) && !empty($_GET['iscashbank']))
                                    array_push ($arrCriteria, $obj->tableName.'.iscashbank = 1' ); 

                                if (isset($_GET) && !empty($_GET['rootkey']))
                                    array_push ($arrCriteria, $obj->tableName.'.rootkey = ' . $_GET['rootkey']); 


                                // bedakan parameter kosong atau tdk pernah dikirim 
                                if (isset($_GET['pkey'])){ 
                                     $_GET['pkey'] = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                                     array_push ($arrCriteria, $obj->tableName.'.pkey = ' . $obj->oDbCon->paramString($_GET['pkey']) );  
                                }
                                if (isset($_GET['term'])){  
                                     array_push ($arrCriteria, '('.($obj->tableName.'.name like ' . $obj->oDbCon->paramString('%'.$_GET['term'].'%') .' or ' . $obj->tableName.'.code like ' . $obj->oDbCon->paramString('%'.$_GET['term'].'%') ) .')');  
                                }

                                $criteria = implode(' and ', $arrCriteria);  

                                $rsCOA = $obj->searchDataForAutoComplete('','',false,$criteria,$order );

                                for($i=0;$i<count($rsCOA);$i++){
                                        $rsCOA[$i]['value'] = htmlspecialchars_decode($rsCOA[$i]['value']); 
                                }

                                echo json_encode($rsCOA); 
                                break;
                    
             
            }
}


 
die;
  
?>
