<?php

die("die, comment open for reset transaction");

require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('DisposalJobOrder.class.php'));
$disposalJobOrder = createObjAndAddToCol(new DisposalJobOrder());

$obj = $disposalJobOrder;
$securityObject = $obj->securityObject;


$sql = 'select 
                    '.$obj->tableName.'.pkey 
                from
                    '.$obj->tableName.'
        ';
        
$rsJO = $obj->oDbCon->doQuery($sql); 

foreach ($rsJO as $JO) {
    $pkey = $JO['pkey'];
    try {

        if (!$obj->oDbCon->startTrans(true))
            throw new Exception($obj->errorMsg[100]);
        
            $obj->updateDetailJO($pkey); 
    
        $obj->oDbCon->endTrans();
    } catch (Exception $e) {
        $obj->oDbCon->rollback();
    }
    
}



?>