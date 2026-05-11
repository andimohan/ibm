<?php
die;

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

$class->oDbCon->startTrans(); 

$sql = 'update general_journal_header set statuskey = 3 where statuskey = 2 and year(trdate) <= \'2020\' ' ;
$class->oDbCon->execute($sql);

/*$sql = 'delete from chart_of_account_amount';
$class->oDbCon->execute($sql);*/

$sql = 'select * from chart_of_account';
$rs = $class->oDbCon->doQuery($sql);

$sql = 'select 
                            chart_of_account.pkey,
                            coalesce(coaamount.amount,0) as amount
                        from chart_of_account left join ( 
                                select  
                                    chart_of_account.pkey,
                                    sum(general_journal_detail.debit - general_journal_detail.credit) as amount
                               from 
                                    chart_of_account,
                                    general_journal_header, 
                                    general_journal_detail
                                where 
                                    general_journal_header.statuskey = 3  and
                                    year(trdate) <= 2020  and
                                    general_journal_header.pkey = general_journal_detail.refkey and
                                    general_journal_detail.coakey = chart_of_account.pkey
                                group by
                                    general_journal_detail.coakey
                        ) coaamount on chart_of_account.pkey = coaamount.pkey
                        where
                            chart_of_account.statuskey = 1 
';
$rsRunningAmount = $class->oDbCon->doQuery($sql);
$rsRunningAmount = array_column($rsRunningAmount,'amount','pkey');

foreach($rs as $coarow){
    $amount = $rsRunningAmount[$coarow['pkey']];
    $sql = 'update chart_of_account_amount set startingamount = 0, runningamount = '.$amount.', closingamount = '.$amount .'  where refkey = ' .$coarow['pkey'];
    if($coarow['pkey'] == 453)
        $class->setLog($sql,true);
    
    $class->oDbCon->execute($sql);
}





// ====== UPDATE COA AMOUNT 

$coa = new ChartOfAccount(); 
 
for($i=0;$i<count($rs);$i++)  
    $coa->updateCOAAmount($rs[$i]['pkey']); 
 
 
for($i=0;$i<count($rs);$i++)
    $coa->updateParentAmountFromRoot($rs[$i]['rootkey']); 

$coa->updateCurrentYearEarnings();  
// ====== END OF UPDATE COA AMOUNT




$class->oDbCon->endTrans();
    
echo 'done';
 
?>