<?php
die;

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 


$class->oDbCon->startTrans(); 
$sql = 'update emkl_job_order_detail set rate = 1 where rate = 0' ;
$class->oDbCon->execute($sql);
$class->oDbCon->endTrans();

$sql = 'select * from emkl_job_order_header where totalselling = 0 and year(trdate) = 2021 and statuskey <> 4 and loadcontainertypekey <> 2' ;
$rs = $class->oDbCon->doQuery($sql);
 
foreach($rs as $row) 
    reCountSubtotal($row); 
    
echo 'done';

function reCountSubtotal($rs){ 
        global $class;
     
        $class->oDbCon->startTrans(); 
        $totalSelling = 0;
          
        $sql = 'select * from emkl_job_order_detail where refkey = ' . $rs['pkey'];
        $rsDetail = $class->oDbCon->doQuery($sql); 
        
        
        for ($i=0;$i<count($rsDetail);$i++){
            $sql = 'select * from emkl_job_order_detail_item where refkey = ' . $rsDetail[$i]['pkey'];
            $rsServiceDetail = $class->oDbCon->doQuery($sql);  
                 
            $amount = 0; 
            
            for($j=0;$j<count($rsServiceDetail);$j++){
                $priceInUnit = $rsServiceDetail[$j]['priceinunit'];   
				$qty = $rsServiceDetail[$j]['qty'];
                $servicekey = $rsServiceDetail[$j]['servicekey'];
                $currencykey = $rsServiceDetail[$j]['currencykey'];
                
                if(empty($servicekey) || $qty <= 0 || $priceInUnit <= 0 ) continue; 
                
                // gk bisa kalo gk kali rate karena currencykeynya diheader 
                $itemSubtotal = ($currencykey == CURRENCY['idr']) ? ($qty * $priceInUnit) : ($qty * $priceInUnit * $rate);  
                $amount += $itemSubtotal;
            } 
				
            //$this->setLog($amount,true);
            $totalSelling += $amount; 
            
            if ($totalSelling == 0) continue;
            
            $sql = 'update emkl_job_order_header set totalselling='.$totalSelling.' where pkey = ' . $rs['pkey'];
            echo $rs['code']. ' => ' .$sql.'<br>';
            $class->oDbCon->execute($sql); 
            
        }  
          
        $class->oDbCon->endTrans(); 
    }
 
?>