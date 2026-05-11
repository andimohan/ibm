<?php 

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

$OBJ = $customer;

  try{ 
        if(!$OBJ->oDbCon->startTrans()){  
            echo 'gagal start 1<br>';
            throw new Exception($OBJ->errorMsg[100]);
        }
  
      $sql = 'update customer set name = "k123123as" where pkey = 1';
       $OBJ->oDbCon->execute($sql);
      
        //if(!$result[0]['valid'])
        //  throw new Exception( 'gk valid' );

            $OBJ->oDbCon->startTrans();
            $OBJ->oDbCon->startTrans();
            $OBJ->oDbCon->startTrans();
                
       
      
        $OBJ->oDbCon->endTrans();
        $OBJ->oDbCon->endTrans(); 
        $OBJ->oDbCon->endTrans();throw new Exception( 'gk valid' );
        $OBJ->oDbCon->endTrans(); 
        echo 'end of trans<br>';

    }catch(Exception $e){
        echo 'mulai rollback<br>';
        $OBJ->oDbCon->rollback();  
    }	

    
echo 'done';
 
?>