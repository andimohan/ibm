<?php   
 
include_once '../_config.php';  
include_once '../_include-v2.php';

if( ! in_array(DOMAIN_NAME, array('thmsrv.local', 'mandy.wintera.co.id') ) ) die;
 
$year = 2022;

$rsFinal = array();

// TCO
$sql = ' select 
            concat(chart_of_account.pkey,\'-\', month(trucking_cost_cash_out_header.trdate), \'-\', year(trucking_cost_cash_out_header.trdate) ) as indexkey,
            concat(month(trucking_cost_cash_out_header.trdate), \'-\', year(trucking_cost_cash_out_header.trdate) ) as monthindex,
            month(trucking_cost_cash_out_header.trdate) as transactionmonth, 
            sum(trucking_cost_cash_out_detail.amount) as amount,
            chart_of_account.name,
            chart_of_account.pkey as coapkey 
            from trucking_cost_cash_out_header, trucking_cost_cash_out_detail,chart_of_account,item
            where 
                trucking_cost_cash_out_header.pkey = trucking_cost_cash_out_detail.refkey and
                year(trucking_cost_cash_out_header.trdate) = '. $class->oDbCon->paramString($year).' and
                trucking_cost_cash_out_header.statuskey in (2,3) and 
                trucking_cost_cash_out_detail.costkey = item.pkey and
                item.costcoakey = chart_of_account.pkey 
            group by monthindex, coapkey
        ';

$rsTCO = $class->oDbCon->doQuery($sql);
$rsTCO = array_column($rsTCO,null,'indexkey');


foreach($rsTCO as $key=>$row){
    if(!isset($rsFinal[$key])) 
            $rsFinal[$key] = array('indexkey' => $key,
                                   'transactionmonth' => $row['transactionmonth'], 
                                   'amount' => 0 , 
                                   'name' => $row['name'],
                                   'coapkey' => $row['coapkey'],
                                  );
    
     $rsFinal[$key]['amount'] += $row['amount']; 
}



// CO
$sql = ' select 
            concat(chart_of_account.pkey,\'-\', month(cash_out_header.trdate), \'-\', year(cash_out_header.trdate) ) as indexkey,
            concat(month(cash_out_header.trdate), \'-\', year(cash_out_header.trdate) ) as monthindex,
            month(cash_out_header.trdate) as transactionmonth, 
            sum(cash_out_detail.amount) as amount,
            chart_of_account.name,
            chart_of_account.pkey as coapkey 
            from cash_out_header, cash_out_detail,chart_of_account
            where 
                cash_out_header.pkey = cash_out_detail.refkey and
                year(cash_out_header.trdate) = '. $class->oDbCon->paramString($year).' and
                cash_out_header.statuskey in (2,3) and 
                cash_out_detail.coakey = chart_of_account.pkey 
            group by monthindex, coapkey
        ';

$rsCO = $class->oDbCon->doQuery($sql);
$rsCO = array_column($rsCO,null,'indexkey');

foreach($rsCO as $key=>$row){
    if(!isset($rsFinal[$key])) 
            $rsFinal[$key] = array('indexkey' => $key,
                                   'transactionmonth' => $row['transactionmonth'], 
                                   'amount' => 0 , 
                                   'name' => $row['name'],
                                   'coapkey' => $row['coapkey'],
                                  );
    
     $rsFinal[$key]['amount'] += $row['amount']; 
}


// REIMBURSE
$sql = 'select  
            concat(chart_of_account.pkey,\'-\', month(trucking_service_order_invoice_header.trdate), \'-\', year(trucking_service_order_invoice_header.trdate) ) as indexkey,
            concat(month(trucking_service_order_invoice_header.trdate), \'-\', year(trucking_service_order_invoice_header.trdate) ) as monthindex,
            month(trucking_service_order_invoice_header.trdate) as transactionmonth, 

        from 
            trucking_service_order_invoice_header,
            trucking_service_order_invoice_detail,
            trucking_service_order_invoice_item_detail 
        where    
        ';


echo '<table>';
foreach($rsFinal as $row){
    echo '<tr><td>'.$row['indexkey'].'</td><td>'.$row['name'].'</td><td>'.$class->formatNumber($row['amount']).'</td></tr>';
}
echo '</table>';


echo 'done';



 
?>