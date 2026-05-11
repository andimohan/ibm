<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ChartOfAccount.class.php';    
 
?> 
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />   
<title>Upload - COA</title>  
</head> 
<body>    
    
<div style="padding: 1em"> 
    <div class="import-template">
    <h1>Updating COA...</h1>
        <ul class="progress-list"> 
            <?php 
                $obj = new ChartOfAccount();
                
				try{		

					if (!$obj->oDbCon->startTrans())
						throw new Exception($obj->errorMsg[100]);

					$arrSQL = array();
					array_push($arrSQL,'delete from chart_of_account where parentkey <> 0' );
					array_push($arrSQL,'update chart_of_account set amount = 0' );
					array_push($arrSQL,'delete from chart_of_account_amount' );
					array_push($arrSQL,'delete from chart_of_account_counter' );
					array_push($arrSQL,'delete from chart_of_account_active_period where pkey <> 1' );
					array_push($arrSQL,'update chart_of_account_active_period set isclosed = 0' );
					array_push($arrSQL,'update item set costcoakey =0, revenuecoakey=0, inventorycoakey=0,inventorytempcoakey=0' );
					
					foreach($arrSQL as $sql)
						$obj->oDbCon->execute($sql);
					
					$obj->oDbCon->endTrans();  

				}catch(Exception $e){ 
					$obj->oDbCon->rollback();  
 					var_dump($e->getMessage());
				}		
			
				  $arrParentKey = array();
                  $currentLevel = 0;
                  $levelOnePkey = 1;
                  for ($row = 1; $row <= $highestRow; ++$row) { 
                      for($col=1;$col <= $highestColumnIndex; ++$col){
                          $code = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                          $name = $worksheet->getCellByColumnAndRow($col+1, $row)->getValue();
                           
                             if (empty($code) || empty($name)) {
                                 continue; 
                             }

                             if ($col == 1) {
                                $pkey  =  $code;
                                $arrParentKey = array();
                                $arrParentKey[$col] = $pkey; 
                                continue; 
                             }
                            
                                $currentLevel = $col;
                          
                                //echo '$currentLevel ' . $currentLevel;
                                    
                                //$obj = new ChartOfAccount(); // create ulang obj agar field pkey gk ilang
                                $code = trim($code); 
                                $name = trim($name);  
                                $benchmark = array('field' => 'name' , 'value' => $name);  
                                $arrParam = array(); 
                                $arrParam['selStatus'] = 1;  
                                $arrParam['code'] = $code;
                                $arrParam['name'] = $name;
                                $arrParam['chkCashBank'] = 0;
                                $arrParam['selCategory'] = (isset($arrParentKey[$currentLevel-1])) ?  $arrParentKey[$currentLevel-1] : 0;  
                                
						  		$result = $obj->addData($arrParam); 
                                
                                $color = ($result[0]['valid']) ?  'text-green-avocado'  : 'text-red-cardinal';
                                echo '<li class="'.$color.'"><strong>'.$benchmark['value'].'</strong>, '.$result[0]['message'].'</li>'; 
                          
                                $arrParentKey[$currentLevel] = $result[0]['data']['pkey']; 
                            
                                if(!$result[0]['valid']){
									var_dump($result);
									die;
								}
                        }
                      
                       
                }

            
                echo 'done!';  
  
            ?>
        </ul>
    </div>
</div>     
    
</body> 
</html> 
