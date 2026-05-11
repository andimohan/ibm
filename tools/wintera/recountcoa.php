<?php
 

include_once '../../_config.php'; 
include_once '../../_include-v2.php';

includeClass(array('ChartOfAccount.class.php'));
 
 
// ====== UPDATE COA AMOUNT 

$coa = new ChartOfAccount(); 
$rs = $coa->searchData();
 
 try{			  
                if(!$coa->oDbCon->startTrans())
				    throw new Exception($coa->errorMsg[100]); 

                for($i=0;$i<count($rs);$i++)  
                    $coa->updateCOAAmount($rs[$i]['pkey']); 


                for($i=0;$i<count($rs);$i++)
                    $coa->updateParentAmountFromRoot($rs[$i]['rootkey']); 

                $coa->updateCurrentYearEarnings();  
     
     
            $coa->oDbCon->endTrans(); 

		}catch(Exception $e){
			$coa->oDbCon->rollback();    
        }	

// ====== END OF UPDATE COA AMOUNT
              
echo 'done';

?>