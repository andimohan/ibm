<?php 
require_once '../_config.php'; 
require_once '../_include.php';  

$obj = $item;    

$arrCriteria = array();   
  
if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                case 'searchCategoryGroup' :  
                     
                    $criteria  = '';
                    
                    $arrCriteria = array();      
                    $getKeys = array_keys($_GET);
                    for($i=0;$i<count($getKeys);$i++){
                        if ($getKeys[$i] == 'action' || $getKeys[$i] == 'term' ||  $getKeys[$i] == 'limit' || $getKeys[$i] == 'searchField')
                            continue; 
                        
                        if (empty($_GET[$getKeys[$i]]) || empty($getKeys[$i]))
                            continue;
 
                        $concat = '=';
                        $value = $obj->oDbCon->paramString($_GET[$getKeys[$i]]);
                         
                        
                        if ($getKeys[$i] == 'statuskey' && preg_match('/^\(.*\)\Z/', $_GET[$getKeys[$i]]) ){ 
                            $concat = ' in ';
                            $value = $_GET[$getKeys[$i]];
                        }
                        
                        array_push ($arrCriteria, $getKeys[$i].$concat.$value);  
                    }

                    $tempcriteria = implode(' and ', $arrCriteria);   
                    $criteria .= (!empty($tempcriteria)) ? ' and ' . $tempcriteria : '';  
                    
                    
                    $rsData = $obj->searchCategoryGroup('',$criteria);
                    echo json_encode($rsData); 
                    break; 
                    
            }
}

die;
  
?>