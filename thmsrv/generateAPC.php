<?php   
 
include_once '../_config.php';  
include_once '../_include-v2.php';
 
if( ! in_array(DOMAIN_NAME, array('thmsrv.local', 'mandy.wintera.co.id') ) ) die;

$sql = 'select ap_employee_commission.trdate,refcode,amount,
    employee.name as employeename
        from 
                ap_employee_commission,
                employee
        where 
            ap_employee_commission.employeekey = employee.pkey and
                ap_employee_commission.statuskey in (1,2,3) and year(trdate) = 2021 and month(trdate) = 12 order by employeekey asc, ap_employee_commission.trdate asc, ap_employee_commission.code asc '; // and month(trdate) = 1
$rs = $class->oDbCon->doQuery($sql);


echo '<table>'; 
foreach($rs as $row){  
    echo '<tr><td>'.$row['trdate'].'</td><td>'.$row['refcode'].'</td><td>'.$row['employeename'].'</td><td>'.$row['amount'].'</td></tr>';
}

echo '</table>';
 
//$arrKey = array_column($rs,'pkey');
//$result = array_chunk($arrKey, 100);
//
//
//$ctr = 1; 
//        
//foreach($result as $chunkRow){ 
//    $link =  'https://eai.local/admin/print/apEmployeeCommission/'.implode(',', $chunkRow);  
//    echo $ctr++ . '. <a href="'.$link.'" target="_blank">'.$link.'</a><br>'; 
//}
 
 
?>