<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array('MedicalRecord.class.php','Customer.class.php'));
$medicalRecord = createObjAndAddToCol(new MedicalRecord()); 
$customer = createObjAndAddToCol(new Customer()); 

$obj = $medicalRecord;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'medicalRecordForm';
 
		
$arrSearchColumn = array ();
array_push($arrSearchColumn, array($obj->lang['code'], $obj->tableName . '.code')); 
array_push($arrSearchColumn, array($obj->lang['warehouse'], $obj->tableWarehouse . '.name')); 
array_push($arrSearchColumn, array($obj->lang['customer'], $obj->tableCustomer . '.name')); 
array_push($arrSearchColumn, array($obj->lang['employee'], $obj->tableEmployee . '.name')); 
array_push($arrSearchColumn, array($obj->lang['address'], $obj->tableCustomer . '.address')); 

function generateQuickView($obj,$id){ 
    $customer = new Customer();
	$detail = '';
    $rs = $obj->searchData($obj->tableName .'.pkey',$id,true);   
 	$rsDetail = $obj->getDetailWithRelatedInformation($id);
    $age = $customer->getCustomersAge($rs[0]['customerkey']);

	  
	$basicInformation  = ' <div class="data-card border-orange">
						<h1>'.ucwords($obj->lang['generalInformation']).'</h1> 
						<div class="content">
						<div class="div-table general-information-table">
							<div class="div-table-row">
								<div class="div-table-col" style="width:40%">'.ucwords($obj->lang['status']).'</div> 
								<div class="div-table-col">'.$rs[0]['statusname'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['code']).'</div> 
								<div class="div-table-col">'.$rs[0]['code'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['warehouse']).'</div> 
								<div class="div-table-col">'. $rs[0]['warehousename'].'</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['customer']).'</div> 
								<div class="div-table-col">'.$rs[0]['customername'].'</div> 
							</div> 
                              <div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['age']).'</div> 
								<div class="div-table-col">'.$age.'</div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['DPJP']).'</div> 
								<div class="div-table-col">'.$rs[0]['doctorname'].'</div> 
							</div> 
                            <div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['address']).'</div> 
								<div class="div-table-col">'.$rs[0]['address'].'</div> 
							</div> 
                             <div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['medicineAllergy']).'</div> 
								<div class="div-table-col">'.$rs[0]['description'].'</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">'.ucwords($obj->lang['note']).'</div> 
								<div class="div-table-col">'.$rs[0]['note'].'</div> 
							</div> 
						</div>
						</div>
					</div>  
		'; 	
		
		$detailInformation  = ' <div class="data-card border-green">
						<h1>'.ucwords($obj->lang['detail']).'</h1> 
						<div class="content">
						<div class="div-table  quick-view-table">
							  <div class="div-table-row">  
                                <div class="div-table-col detail-col-header" style="width:120px; text-align:center;">'.ucwords($obj->lang['date']).'</div>
                                <div class="div-table-col detail-col-header">'.ucwords($obj->lang['profession']).'</div>
                                <div class="div-table-col detail-col-header" style="width:250px;">'.ucwords($obj->lang['soap']).'</div> 
                                <div class="div-table-col detail-col-header" style="width:250px;">'.ucwords($obj->lang['theraphy']).'</div> 
								</div>';
								
		for ($i=0;$i<count($rsDetail);$i++){
			 

			$detailInformation  .= '
				<div class="div-table-row">  
					<div class="div-table-col" style="text-align:center;">'.$obj->formatDBDate($rsDetail[$i]['date'],'d / m / Y H:i').'</div>
					<div class="div-table-col">'.$rsDetail[$i]['employeename'].'</div>
					<div class="div-table-col" >'.$rsDetail[$i]['soapdescription'].'</div>
                    <div class="div-table-col">'. str_replace(chr(13),'<br>',$rsDetail[$i]['therapydescription']) .'</div> 
				</div>
			';
		}
								
		$detailInformation  .= ' </div>
						</div>
					</div>  
		'; 	
		
		$detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5"  style="width:25%;">
								'.$basicInformation.'
								</div> 
								<div class="div-table-col-5">
								 '.$detailInformation.'
								</div>  
							</div>
					</div>';
				  
		$detail .= '<div style="clear:both;"></div>';	
    return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
