<?php
die;

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 
 
$class->oDbCon->startTrans();
  
$sql = 'select * from emkl_order_header';
$rs = $class->oDbCon->doQuery($sql);
$delimiter = '*';
foreach ($rs as $emklRow){
     $containerTypeCahce ='';
     $arrContainerType = array();
     echo $emklRow['code']. '<br>';
     if($emklRow['jobtypekey'] == EMKL['emklType']['fcl'] || $emklRow['jobtypekey'] == EMKL['emklType']['trucking']){
         $rsDetail = $emklJobOrderHeader->getDetailWithRelatedInformation($emklRow['pkey']);
         if(!empty($rsDetail)){
             for($i=0;$i<count($rsDetail);$i++){
                 $rsItem = $container->getDataRowById($rsDetail[$i]['itemkey']);
                 if(empty($rsItem[0]['containertypekey']) || in_array($rsItem[0]['containertypekey'],$arrContainerType))
                    continue;
                 
                 array_push($arrContainerType,$rsItem[0]['containertypekey']);
             }
         }
     }
    
     if($emklRow['jobtypekey'] == EMKL['emklType']['lcl']){
         $rsItem = $container->getDataRowById($emklRow['itemkey']);
         if(empty($rsItem[0]['containertypekey']) || !in_array($rsItem[0]['containertypekey'],$arrContainerType))
            array_push($arrContainerType,$rsItem[0]['containertypekey']);
     }
    
    if(!empty($arrContainerType)){
        $containerTypeCahce = $delimiter.''.implode($delimiter,$arrContainerType).''.$delimiter;
        echo $containerTypeCahce;
        $sql = 'update emkl_order_header set containertypecache = '. $class->oDbCon->paramString($containerTypeCahce) .' where pkey = ' .$emklRow['pkey'] ; 
        $class->oDbCon->execute($sql);
        echo ' Berhasil !!!!';
    }
  

    
    echo '<br>';
}

$class->oDbCon->endTrans();
echo '<bR><br>done ';
?>