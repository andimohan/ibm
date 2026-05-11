<?php


include_once '../../_config.php'; 
include_once '../../_include-v2.php';


$arrTables = array();
array_push($arrTables, array('maintablename' => 'ap_payment_header' , 'tablename' => 'ap_payment_file', 'fieldtoheader'=>'refkey', 'fieldtofile' => 'file', 'uploadfolder' => 'ap-payment/'));
array_push($arrTables, array('maintablename' => 'ar_payment_header' , 'tablename' => 'ar_payment_file', 'fieldtoheader'=>'refkey', 'fieldtofile' => 'file', 'uploadfolder' => 'ar-payment/'));
array_push($arrTables, array('maintablename' => 'trucking_cost_cash_out_header' , 'tablename' => 'trucking_cost_cash_out_file', 'fieldtoheader'=>'refkey', 'fieldtofile' => 'file', 'uploadfolder' => 'trucking-cost-cash-out/'));

foreach($arrTables as $tableRow){
	
	echo '<b>Checking '.$tableRow['maintablename'].'</b><br>';
	
	$sql = 'select '.$tableRow['maintablename'].'.code,  '.$tableRow['tablename'].'.* 
			from ' . $tableRow['maintablename'].','.$tableRow['tablename'] .'
			where 	' . $tableRow['maintablename'].'.pkey = ' . $tableRow['tablename'].'.'.$tableRow['fieldtoheader'] .'
				';
				
	
	$rs =  $class->oDbCon->doQuery($sql);
	
	$fieldHeader = $tableRow['fieldtoheader'];
	$fieldFile = $tableRow['fieldtofile'];
	$uploadFolder = $tableRow['uploadfolder'];
	
	foreach($rs as $row){
	  $urlPath = DEFAULT_DOC_UPLOAD_PATH.$uploadFolder. $row[$fieldHeader].'/'.$row[$fieldFile];
	  if(!file_exists($urlPath)){ 
		  echo $row['code'].', '.$urlPath.'<br>';
	  }
	}
	
	echo '<br>';
	
} 
	
echo 'done'
 
?>
