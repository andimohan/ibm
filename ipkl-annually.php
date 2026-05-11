<?php  
 
require_once '_config.php';
require_once '_include-fe-v2.php';

ini_set('max_execution_time', '3000'); //300 seconds = 5 minutes i
 
$today = date('d / m / Y');

$runningMonthYear = date('M Y');
$runningYear = date('Y');


$arStatuskey =  (isset($_GET) && $_GET['duedate'] == 1) ? ' ar.statuskey in (1,2) ' : ' ar.statuskey in (1,2,3) ';

$sql = 'select 
			sum(ar.outstanding) as outstanding,customerkey, customer.name as customername, year(trdate) as year
		from 
			ar,customer
		where
			ar.customerkey = customer.pkey and
		'.$arStatuskey.' and ar.trdate <= ' . $class->oDbCon->paramDate($today).'
        group by customerkey, year(trdate)
		order by customerkey asc, ar.trdate asc
		';


$rs = $class->oDbCon->doQuery($sql);

$totalCol = 2;
$startYear = 0;
foreach($rs as $row)
    if ($row['year'] < $startYear || $startYear == 0) $startYear = $row['year'];

$rs = $class->reindexDetailCollections($rs,'customerkey');

echo '<style> 
.flex {display: flex; align-items:center} 
.flex > div {margin-right: 0.5em; border:1px solid #dedede; border-radius :0.2em; padding: 0.3em; margin-bottom:0.5em}
.flex > div:last-child{margin-right: 0}
.flex .consume {flex:1}  

table {border-top:1px solid #666; border-right: 1px solid #666; width: 100%}
table tr td {border-bottom:1px solid #666; border-left:1px solid #666;  margin:0; padding:0.3em; vertical-align:top}
.col-header {background-color:#333; color: #fff}
</style>';
 

echo '<table cellpadding="0" cellspacing="0" >';
echo '<tr class="col-header" style="position: sticky; top:0">';
echo '<td  style="width: 50px; text-align:center">UNIT</td>';

for($i=$startYear;$i<=$runningYear;$i++){ 
    $totalCol++;
    echo '<td  style="width: 50px; text-align:center">TUNGGAKAN '.$i.'</td>';
}

echo '<td  style="width: 180px; text-align:center">TOTAL TUNGGAKAN PER '.strtoupper($runningMonthYear).'</td>';

echo '</tr>';

$allOutstanding = 0;
foreach($rs as $customerRow){ 
    
    $totalOutstandingPerCustomer = 0; 
    
    // lebih baik di reindex berdasarkan tahun agar aman
    $rsOutstandingAnnual = array_column($customerRow, null, 'year');
 
    echo '<tr>';
    echo '<td style="text-align:center">'.$customerRow[0]['customername'].'</td>';
 
    for($i=$startYear;$i<=$runningYear;$i++){ 
        $totalOutstandingPerCustomer += $rsOutstandingAnnual[$i]['outstanding'];
        echo '<td style="text-align:right">'.$class->formatNumber($rsOutstandingAnnual[$i]['outstanding']).'</td>';
    }
    
    $allOutstanding+=$totalOutstandingPerCustomer;
    
    echo '<td style="text-align:right">'.$class->formatNumber($totalOutstandingPerCustomer).'</td>';
    echo '</tr>';
    
}

echo '<tr><td colspan="'.($totalCol-1).'" style="font-weight:bold; text-align:right">Total</td><td style="text-align:right;font-weight:bold">'.$class->formatNumber($allOutstanding).'</td></tr>';

echo '</table>';

//	
//$allOutstanding = 0;
//foreach($rs as $customerRow){
//	
//	$detailMonth = '';
//	$totalRow = 0; 
//	$totalOutstanding = 0; 
//	foreach($customerRow as $arRow){
//		$style = ($class->formatDBDate($arRow['trdate'],'Y') < $runningYear) ? 'background-color:#DBB5B5' : '';
//		$totalRow++;
//		$totalOutstanding += $arRow['outstanding'];
//		$detailMonth .= '<div style="'.$style.'">';
//		$detailMonth .= $class->formatDBDate($arRow['trdate'],'M Y'); 
//		$detailMonth .= '</div>'; 
//	}
//	  
//	$allOutstanding += $totalOutstanding;
//	
//	$warningColor = ($totalRow>=3) ? 'color:#fff; background-color:#C00' : '';
//	
//	echo '<tr>';
//	echo '<td  style="text-align:right">'.($ctr++).'</td>';
//	echo '<td  style="text-align:center">'.$customerRow[0]['customername'].'</td>';
//	echo '<td  style="text-align:center; '.$warningColor.' ">'.$totalRow.'</td>';
//	echo '<td  style="text-align:right">'.$class->formatNumber($totalOutstanding).'</td>';
//	echo '<td >';
//	echo '<div class="flex" style="flex-wrap:wrap">'.$detailMonth.'</div>';
//	echo '</td>';
//	echo '</tr>';
//}
//	
//
//	
//	echo '<tr>';
//	echo '<td  style="text-align:right"></td>';
//	echo '<td  style="text-align:center"></td>';
//	echo '<td  style="text-align:center"></td>';
//	echo '<td  style="text-align:right">'.$class->formatNumber($allOutstanding).'</td>';
//	echo '<td ></td>';
//	echo '</tr>';
//
//echo '</table>';
	 
die;
 
?>