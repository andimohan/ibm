<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('EMKLReminderJobOrder.class.php');
$emklReminderJobOrder = createObjAndAddToCol(new EMKLReminderJobOrder(EMKL['jobType']['export']));
$customer = createObjAndAddToCol(new Customer());

$obj = $emklReminderJobOrder; 
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
  
    
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'emklReminderJobOrderForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));  
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer. '.name')); 
array_push($arrSearchColumn, array(ucwords($obj->lang['jobType']), $obj->tableJobType. '.name')); 
array_push($arrSearchColumn, array(ucwords($obj->lang['jobType']), $obj->tableTransportationType. '.name')); 
array_push($arrSearchColumn, array(ucwords($obj->lang['jobType']), $obj->tableLoadContainer. '.name')); 
array_push($arrSearchColumn, array(ucwords($obj->lang['volume']), $obj->tableName. '.volume')); 
array_push($arrSearchColumn, array(ucwords($obj->lang['unit']), $obj->tableVolumeUnit. '.name')); 
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trdesc'));
array_push($arrSearchColumn, array('POL', 'pol.name'));
array_push($arrSearchColumn, array('POD', 'pod.name'));
array_push($arrSearchColumn, array('MBL', $obj->tableName.'.mblnumber'));
array_push($arrSearchColumn, array('HBL', $obj->tableName.'.hblnumber'));
//array_push($arrSearchColumn, array('AJU',  $obj->tableName. '.aju'));
//array_push($arrSearchColumn, array('PEB',  $obj->tableName. '.peb'));
array_push($arrSearchColumn, array('Container Type',  $obj->tableContainerType. '.name'));


function generateQuickView($obj,$id){ 
$detail = '';
	$rs = $obj->searchData($obj->tableName .'.pkey',$id);   
    
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>'.ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table  general-information-table">
							<div class="div-table-row">
								<div class="div-table-col" style="width:50%">'.ucwords($obj->lang['status']).'</div> 
								<div class="div-table-col">'.$rs[0]['statusname'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['code']).'</div> 
								<div class="div-table-col">'.$rs[0]['code'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['bookingDate']).'</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['trdate']).'</div> 
							</div> 
                            <div class="div-table-row">
                                <div class="div-table-col">'.ucwords($obj->lang['warehouse']).'</div> 
                                <div class="div-table-col">'.$rs[0]['warehousename'].'</div> 
                            </div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['jobType']).'</div> 
								<div class="div-table-col">'.$rs[0]['jobtype'].' , '.$rs[0]['transportationtype'].' - '.$rs[0]['loadcontainertype'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col"></div> 
								<div class="div-table-col" style="height: 1em"></div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">POL / POD</div> 
								<div class="div-table-col">'.$rs[0]['polname'].' - '.$rs[0]['podname'].'</div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">ETD</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['etdpol']).'</div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">ETA</div> 
								<div class="div-table-col">'.$obj->formatDBDate($rs[0]['etapod']).'</div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['carrier']).'</div> 
								<div class="div-table-col">'.$rs[0]['carriername'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['vessel']).'</div> 
								<div class="div-table-col">'.$rs[0]['vesselname'].' - '.$rs[0]['vesselnumber'].'</div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['agent']).'</div> 
								<div class="div-table-col">'.$rs[0]['agentname'].'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col"></div> 
								<div class="div-table-col" style="height: 1em"></div> 
							</div>  
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['note']).'</div> 
								<div class="div-table-col">'.str_replace(chr(13),'<br>',$rs[0]['trdesc']).'</div> 
							</div> 
						</div>
						</div>
					</div>  
		'; 	

        $snInformation = '';
    
        if(!empty($rs[0]['containernumber']))
            $snInformation .= '<div style=" margin-left:1em; margin-bottom:0.5em"><strong>'.ucwords($obj->lang['containerNumber']).'</strong><br>'.str_replace(chr(13),', ',$rs[0]['containernumber']).'</div>';
    
		$detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5" style="width:25%;">
								'.$basicInformation.'
								</div> 
								<div class="div-table-col-5">
								 '.$snInformation.'
								</div>  
							</div>
					</div>';
				  
		$detail .= '<div style="clear:both;"></div>';	
		 
	 
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
