<?php
die;
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

$class->oDbCon->startTrans(); 

$sql = 'select * from emkl_commission_header where currencykey = 2 and rate > 1 and statuskey <> 4' ;
$rs = $class->oDbCon->doQuery($sql);
 
foreach($rs as $row) { 
    $rate = $row['rate'];
    $pkey = $row['pkey'];
    
    $total = 0;
    $rsDetail = $emklCommission->getDetailById($pkey);
    
    for($i=0;$i<count($rsDetail); $i++){
        //if($rsDetail[$i]['currencykey'] == 1) continue;
         
        $subtotalcurrency = $rsDetail[$i]['qty'] *  $rsDetail[$i]['priceinunit'];
        $subtotal = $subtotalcurrency;
        
        // USD ke IDR
        if ($rsDetail[$i]['currencykey'] == 1)
            $subtotal = $subtotalcurrency / $rate; 
            
        $sql = 'update emkl_commission_detail set  
                subtotalcurrency = '.$subtotalcurrency.',
                subtotal = '.$subtotal.' where pkey = ' . $rsDetail[$i]['pkey'];
        echo $sql.'<br>';
        $class->oDbCon->execute($sql); 
    }

    $sql = 'update emkl_commission_header 
            set grandtotal = (select sum(subtotal) from emkl_commission_detail where refkey = '.$pkey.' )  where currencykey = 2 and pkey = ' . $pkey;
    $class->oDbCon->execute($sql); 
     
}

$sql = 'update emkl_commission_header 
        set balance=grandtotal where currencykey = 2 ';
$class->oDbCon->execute($sql); 


$class->oDbCon->endTrans();
    
echo 'done';
 
?>