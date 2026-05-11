<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass('GeneralJournal.class.php');
$generalJournal = createObjAndAddToCol(new GeneralJournal());

$obj = $generalJournal;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'generalJournalForm';
  
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Debit', $obj->tableName . '.totaldebit'));
array_push($arrSearchColumn, array('Credit', $obj->tableName . '.totalcredit'));
array_push($arrSearchColumn, array('Catatan', $obj->tableName . '.trdesc'));
array_push($arrSearchColumn, array('Ref. Code', $obj->tableName . '.refcode'));
 
		 
/*
  
$printLabelFunction = ' case "printGeneralJournal": 
				var selectedTabId = selectedTab.newPanel[0].id;
				var selectedPkey = tabParam[selectedTabId].selectedPkey; 
				
				if (selectedPkey.length == 0){
					showMsgDialog (phpErrorMsg.print[1]); 
					break ;
				}
			 
			   window.open(\'print\generalJournal/\' + selectedPkey);
			   break;'.chr(13);
*/

/*
 
$printTransactionFunction = $class->generatePrintContextMenu('print','print/generalJournal');   
$overwriteContextMenu["printSeparator"] = "-";
$overwriteContextMenu["printGeneralJournal"] = array("name" => $obj->lang['printTransaction'],"icon" =>"print","callbackFunction" => $printTransactionFunction);
*/




function generateQuickView($obj,$id){
    
    $useCurrencyRevaluation = $obj->loadSetting('currencyRevaluation');
    $useCurrencyRevaluation = ($useCurrencyRevaluation == 1) ? true: false;

	$coa = new ChartOfAccount();
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	    
	$detail = '';

	$detailInformation  = ' <div class="data-card no-border">
					<h1>'. ucwords($obj->lang['detail']).'</h1> 
					<div class="content">
					<div class="div-table quick-view-table">
						  <div class="div-table-row"> 
								<div class="div-table-col detail-col-header"  style="width:250px;">'. ucwords($obj->lang['account']).'</div>';
    
        if($useCurrencyRevaluation){ 
            
                $detailInformation  .= '<div class="div-table-col detail-col-header" style="width:80px; text-align:right;">'. ucwords($obj->lang['debitSource']).'</div> 
								<div class="div-table-col detail-col-header" style="width:80px; text-align:right;">'. ucwords($obj->lang['creditSource']).'</div>  
								<div class="div-table-col detail-col-header" style="width:50px;">'. ucwords($obj->lang['curr']).'</div>  
								<div class="div-table-col detail-col-header" style="width:60px; text-align:right;">'. ucwords($obj->lang['rate']).'</div> ';
	
            
        }
    
	 $detailInformation  .= '<div class="div-table-col detail-col-header" style="width:80px; text-align:right;">'. ucwords($obj->lang['debit']).'</div> 
								<div class="div-table-col detail-col-header" style="width:80px; text-align:right;">'. ucwords($obj->lang['credit']).'</div>  
								<div class="div-table-col detail-col-header" style="width:100px;">'. ucwords($obj->lang['reference']).'</div>  
								<div class="div-table-col detail-col-header" style="width:200px;">'. ucwords($obj->lang['note']).'</div>  
							</div>';
							
	for ($i=0;$i<count($rsDetail);$i++){
		  
        $debit = $obj->formatWithColor($rsDetail[$i]['debit']);
        $credit = $obj->formatWithColor($rsDetail[$i]['credit']);
   
        $debitSource = $obj->formatWithColor($rsDetail[$i]['debitsource']);
        $creditSource = $obj->formatWithColor($rsDetail[$i]['creditsource']); 
        $rate = $obj->formatNumber($rsDetail[$i]['rate']); 
       
		$detailInformation  .= '
			<div class="div-table-row"> 
				<div class="div-table-col">'.$rsDetail[$i]['coacode'] .' - ' .$rsDetail[$i]['coaname'].'</div>';
        
        if($useCurrencyRevaluation){ 
             $detailInformation  .= '
				<div class="div-table-col" style="text-align:right;">'.$debitSource.'</div>
				<div class="div-table-col" style="text-align:right;">'.$creditSource.'</div> 
				<div class="div-table-col">'. $rsDetail[$i]['currencyname'].'</div>  
				<div class="div-table-col" style="text-align:right;">'.$rate.'</div> ';
            
        }

	   $detailInformation  .= '
				<div class="div-table-col" style="text-align:right;">'.$debit.'</div>
				<div class="div-table-col" style="text-align:right;">'.$credit.'</div> 
				<div class="div-table-col">'. $rsDetail[$i]['refcode'].'</div>  
				<div class="div-table-col">'. $rsDetail[$i]['trdesc'].'</div>  
			</div>
		';
	}
							
	$detailInformation  .= ' </div>
					</div>
				</div>  
	'; 	
		
	$detail .= $detailInformation;
			  
	$detail .= '<div style="clear:both;"></div>';	
	 
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
