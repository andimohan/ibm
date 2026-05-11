<?php
die;

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 
 
$class->oDbCon->startTrans();
  
$sql = 'select * from container';
$rsContainer = $class->oDbCon->doQuery($sql);
$rsContainer = array_column($rsContainer,'containertypekey', 'pkey');

/*echo '<pre>';
var_dump($rsContainer,true);
echo '</pre>';
die;*/

$othersType = 100;

$sql = 'update emkl_job_order_header set containertypekey = '.$othersType;
$class->oDbCon->execute($sql);
$sql = 'update emkl_order_header set containertypekey = '.$othersType;
$class->oDbCon->execute($sql);


// Job Order

$sql = 'select * from emkl_job_order_header';
$rs = $class->oDbCon->doQuery($sql);

foreach($rs as $row){
    
    // kalo Udara
    if($row['transportationtypekey'] == 2) continue;
    
    // kalo Laut 
    if( $row['loadcontainertypekey'] == 1 || $row['loadcontainertypekey'] == 3){
        //kalo FCL, Trucking
        
        $sql = 'select itemkey from emkl_job_order_detail_item where refheaderkey = ' . $row['pkey'] .' order by pkey asc limit 1';
        $rsDetail = $class->oDbCon->doQuery($sql);
        
        $containerType = (isset($rsContainer[$rsDetail[0]['itemkey']]) && !empty($rsContainer[$rsDetail[0]['itemkey']])) ? $rsContainer[$rsDetail[0]['itemkey']] : $othersType;
        $sql = 'update emkl_job_order_header set containertypekey = ' . $containerType.' where pkey = ' . $row['pkey'];
        $class->oDbCon->execute($sql);

        
    }else if( $row['loadcontainertypekey'] == 2){ 
        // kalo LCL
        //echo 'key ' . $row['itemkey'] .' container : ' . $rsContainer[$row['itemkey']];
        //echo '<br>';
        
        $containerType = (isset($rsContainer[$row['itemkey']]) && !empty($rsContainer[$row['itemkey']])) ? $rsContainer[$row['itemkey']] : $othersType;
        $sql = 'update emkl_job_order_header set containertypekey = ' . $containerType.' where pkey = ' . $row['pkey'];
        $class->oDbCon->execute($sql);

    }
     
}



// Header 
$sql = 'select * from emkl_order_header';
$rs = $class->oDbCon->doQuery($sql);

foreach($rs as $row){
    
    // kalo Udara
    if($row['transportationtypekey'] == 2) continue;
    
    // kalo Laut 
    if( $row['loadcontainertypekey'] == 1 || $row['loadcontainertypekey'] == 3){
        //kalo FCL, Trucking
        
        $sql = 'select itemkey from emkl_order_detail where refkey = ' . $row['pkey'] .' order by pkey asc limit 1';
        $rsDetail = $class->oDbCon->doQuery($sql);
        
        $containerType = (isset($rsContainer[$rsDetail[0]['itemkey']]) && !empty($rsContainer[$rsDetail[0]['itemkey']])) ? $rsContainer[$rsDetail[0]['itemkey']] : $othersType;
        $sql = 'update emkl_order_header set containertypekey = ' . $containerType.' where pkey = ' . $row['pkey'];
        $class->oDbCon->execute($sql);

        
    }else if( $row['loadcontainertypekey'] == 2){ 
        // kalo LCL 
        
        $containerType = (isset($rsContainer[$row['itemkey']]) && !empty($rsContainer[$row['itemkey']])) ? $rsContainer[$row['itemkey']] : $othersType;
        $sql = 'update emkl_order_header set containertypekey = ' . $containerType.' where pkey = ' . $row['pkey'];
        $class->oDbCon->execute($sql);

    }
     
}


$class->oDbCon->endTrans();
echo '<bR><br>done ';
?>