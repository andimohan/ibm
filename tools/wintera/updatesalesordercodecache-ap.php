<?php 

include_once '../../_config.php'; 
include_once '../../_include-v2.php';


includeClass(array('AP.class.php','APPayment.class.php','EMKLJobOrder.class.php','EMKLPurchaseOrder.class.php'));

try {

    $class->oDbCon->startTrans();
    
    $emklPurchaseOrder = new EMKLPurchaseOrder();
    $emklJobOrder = new EMKLJobOrder();
    $apPayment = new APPayment();
    $ap = new AP();
    
    $rsPOKey = $class->getTableKeyAndObj($emklPurchaseOrder->tableName,array('key'))['key']; 

    $sql = '
        select
            '.$apPayment->tableNameDetail.'.pkey,
            '.$apPayment->tableNameDetail.'.refkey,
            '.$apPayment->tableNameDetail.'.apkey,
            '.$apPayment->tableName.'.code,
            '.$apPayment->tableName.'.statuskey,
            '.$ap->tableName.'.code as apcode,
            '.$ap->tableName.'.refkey as refpokey,
            '.$ap->tableName.'.refcode as refpocode,
            '.$ap->tableName.'.statuskey as apstatuskey,
            '.$emklJobOrder->tableName.'.code as jocode
        from
            '.$apPayment->tableNameDetail.',
            '.$ap->tableName.'
                left join '.$emklPurchaseOrder->tableName.' on '.$ap->tableName.'.refkey = '.$emklPurchaseOrder->tableName.'.pkey
                left join '.$emklJobOrder->tableName.' on '.$emklPurchaseOrder->tableName.'.refkey = '.$emklJobOrder->tableName.'.pkey,
            '.$apPayment->tableName.'
         where
            '.$apPayment->tableNameDetail.'.refkey = '.$apPayment->tableName.'.pkey and
            '.$apPayment->tableNameDetail.'.apkey = '.$ap->tableName.'.pkey and
            '.$ap->tableName.'.reftabletype = '.$class->oDbCon->paramString($rsPOKey).' and
            '.$ap->tableName.'.statuskey in (1,2,3) and
            '.$apPayment->tableName.'.statuskey in (1,2,3)
    ';

    $rsAPP = $class->oDbCon->doQuery($sql);
    
    if(empty($rsAPP)) return;

    $arrAPPGroup = array();
    $arrAPGroup = array();
    for($i=0; $i<count($rsAPP); $i++) {
        $appkey = $rsAPP[$i]['refkey'];
        $jocode = $rsAPP[$i]['jocode'];
        $apkey = $rsAPP[$i]['apkey'];

        $arrAPPGroup[$appkey][] = $jocode;
        $arrAPGroup[$apkey][] = $jocode;

    }

    foreach ($arrAPGroup as $apKey => $jocodes) {
        $implodeJo = implode(', ', array_unique($jocodes));
        $sql = '
            update
                '.$ap->tableName.'
            set
                '.$ap->tableName.'.salesordercodecache = '.$class->oDbCon->paramString($implodeJo).'
            where
                '.$ap->tableName.'.pkey = '.$class->oDbCon->paramString($apKey).'
        ';

        $class->oDbCon->execute($sql);

    }

    foreach ($arrAPPGroup as $appKey => $jocodes) {
        $implodeJo = implode(', ', array_unique($jocodes));

        $sql = '
            update
                '.$apPayment->tableName.'
            set
                ' . $apPayment->tableName . '.salesordercodecache = '.$class->oDbCon->paramString($implodeJo).'
            where
                ' . $apPayment->tableName . '.pkey = '.$class->oDbCon->paramString($appKey).'
        ';

        $class->oDbCon->execute($sql);

    }

    $class->oDbCon->endTrans();

} catch(Exception $e){
    $class->setLog($e,true);
	$class->oDbCon->rollback();
}

echo 'done';

?>