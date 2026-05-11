<?php  
 
require_once '_config.php';
require_once '_include-fe-v2.php';

ini_set('max_execution_time', '3000'); //300 seconds = 5 minutes i
 
$today = date('d / m / Y');
$runningYear = date('Y');

$sql = 'select 
			ar.*, customer.name as customername
		from 
			ar,customer
		where
			ar.customerkey = customer.pkey and
		ar.statuskey in (1,2) and ar.trdate <= ' . $class->oDbCon->paramDate($today).'
		order by customerkey asc, ar.trdate asc
		';
$rs = $class->oDbCon->doQuery($sql);


$onlyDueDate = false;
$showDetailMonth = true;

if(isset($_GET) && !empty($_GET['layout'])){ 
    switch($_GET['layout']){ 
        case 1  : $onlyDueDate = true; $showDetailMonth = false; break;       
    }
}

$rs = $class->reindexDetailCollections($rs,'customerkey');

$ctr = 1;
echo '<style> 
	.flex {display: flex; align-items:center} 
	.flex > div {margin-right: 0.5em; border:1px solid #dedede; border-radius :0.2em; padding: 0.3em; margin-bottom:0.5em}
	.flex > div:last-child{margin-right: 0}
	.flex .consume {flex:1}  
	  
	table {border-top:1px solid #666; border-right: 1px solid #666; width: 100%}
	table tr td {border-bottom:1px solid #666; border-left:1px solid #666;  margin:0; padding:0.3em; vertical-align:top}
	.col-header {background-color:#333; color: #fff}
	</style>';
 
    $style = ($showDetailMonth) ? '' : 'style="width: 300px"';

	echo '<table cellpadding="0" cellspacing="0" '.$style.'>';
	echo '<tr class="col-header" style="position: sticky; top:0">';
	echo '<td  style="width: 30px; text-align:right">No.</td>';
	echo '<td  style="width: 50px; text-align:center">Unit</td>';
	echo '<td  style="width: 60px; text-align:center">Jml Bln</td>';
	echo '<td  style="width: 100px; text-align:right">Jml Tagihan</td>';

    if ($showDetailMonth){ 
        echo '<td >';
        echo '<div class="flex" style="flex-wrap:wrap">Bulan Tunggakan</div>';
        echo '</td>';
    }

	echo '</tr>';

	
$allOutstanding = 0;
foreach($rs as $customerRow){
	
	$detailMonth = '';
	$totalRow = 0; 
	$totalOutstanding = 0; 
	foreach($customerRow as $arRow){
		$style = ($class->formatDBDate($arRow['trdate'],'Y') < $runningYear) ? 'background-color:#DBB5B5' : '';
		$totalRow++;
		$totalOutstanding += $arRow['outstanding'];
		$detailMonth .= '<div style="'.$style.'">';
		$detailMonth .= $class->formatDBDate($arRow['trdate'],'M Y'); 
		$detailMonth .= '</div>'; 
	}
	  
	$allOutstanding += $totalOutstanding;
	
    if ($onlyDueDate && $totalRow<3) continue;
    
	$warningColor = ($totalRow>=3) ? 'color:#fff; background-color:#C00' : '';
	
	echo '<tr>';
	echo '<td  style="text-align:right">'.($ctr++).'</td>';
	echo '<td  style="text-align:center">'.$customerRow[0]['customername'].'</td>';
	echo '<td  style="text-align:center; '.$warningColor.' ">'.$totalRow.'</td>';
	echo '<td  style="text-align:right">'.$class->formatNumber($totalOutstanding).'</td>';
    
    if ($showDetailMonth){ 
        echo '<td >'; 
        echo '<div class="flex" style="flex-wrap:wrap">'.$detailMonth.'</div>'; 
        echo '</td>';
    }
    
	echo '</tr>';
}
	

	
	echo '<tr>';
	echo '<td  style="text-align:right"></td>';
	echo '<td  style="text-align:center"></td>';
	echo '<td  style="text-align:center"></td>';
	echo '<td  style="text-align:right">'.$class->formatNumber($allOutstanding).'</td>';
    if ($showDetailMonth)
	   echo '<td ></td>';
	echo '</tr>';

echo '</table>';
	 
die;
 
?>