<?php 
require_once '../_config.php'; require_once '../_include-v2.php'; 


includeClass('CarServiceMaintenance.class.php');
$carServiceMaintenance = createObjAndAddToCol(new CarServiceMaintenance()); 
$brand = createObjAndAddToCol(new Brand()); 
$car = createObjAndAddToCol(new Car()); 
$carCategory = createObjAndAddToCol(new CarCategory()); 
$chassis = createObjAndAddToCol(new Chassis()); 
$itemUnit = createObjAndAddToCol(new ItemUnit()); 
$termOfPayment = createObjAndAddToCol(new TermOfPayment()); 
$warehouse = createObjAndAddToCol(new Warehouse()); 
$paymentMethod = createObjAndAddToCol(new PaymentMethod()); 
$shipment = createObjAndAddToCol(new Shipment()); 
$supplier = createObjAndAddToCol(new Supplier()); 
$carServiceMaintenanceRequest = createObjAndAddToCol(new CarServiceMaintenanceRequest()); 
$item = createObjAndAddToCol(new Item());

$obj= $carServiceMaintenance;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'carServiceMaintenanceList';
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$editWarehouseInactiveCriteria = ''; 
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';
$editSalesInactiveCriteria = ''; 
 
$rsSalesDetail = array();
$rsPaymentMethodDetail = array();
$arrItemPosition = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['estDate'] = date('d / m / Y');
$_POST['executeDate'] = date('d / m / Y');
$showEditExecuteDate = false;
  
$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

$useInhouse = '';
$useOutsource = 'display-none';
$readonlyOutsource = true;

$arrType = $obj->convertForCombobox($obj->getMaintenanceType(),'pkey','name');      
$displayType= array();
foreach($arrType as $key=>$row)
    $displayType[$key] = '';

$rs = prepareOnLoadData($obj);  

$useStorage = $obj->useStorage;

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	$rsSalesDetail = $obj->getDetailWithRelatedInformation($id);
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id); 
	 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['estDate'] = $obj->formatDBDate($rs[0]['estdate'],'d / m / Y');
	$_POST['executeDate'] = $obj->formatDBDate($rs[0]['executedate'],'d / m / Y');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
    $_POST['hidTechicianKey'] =$rs[0]['techniciankey'];
    $showEditExecuteDate = ($rs[0]['statuskey'] == 2) ? true : false;
	$rsEmployee = $employee->getDataRowById($rs[0]['techniciankey']);
	$_POST['techicianName'] = (!empty($rsEmployee)) ? $rsEmployee[0]['name'] : '';
	$_POST['hidDriverKey'] =$rs[0]['driverkey'];
	$rsEmployee = $employee->getDataRowById($rs[0]['driverkey']);
	$_POST['driverName'] = (!empty($rsEmployee)) ? $rsEmployee[0]['name'] : '';
     
	$_POST['selType'] = $rs[0]['typekey']; 
    foreach($arrType as $key=>$row)  
        $displayType[$key] = ($key == $rs[0]['typekey']) ? 'display:inline' : 'display:none';
  
	$_POST['trDesc'] = $rs[0]['trnotes'];
	$_POST['refCode'] = $rs[0]['refcode'];
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']); 
 
    $_POST['hidCarMaintenanceRequestKey'] = $rs[0]['refkey'];

    if(!empty($rs[0]['refkey']))
    {
        $rsCarServiceMaintenanceRequest = $carServiceMaintenanceRequest->getDataRowById($rs[0]['refkey']); 
        $_POST['referenceCode'] = $rsCarServiceMaintenanceRequest[0]['code'];
    }
    
    if ($rs[0]['finaldiscounttype']  == 2){ 
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    } 

	$_POST['selFinalDiscountType'] = $rs[0]['finaldiscounttype'] ;
	$_POST['finalDiscount'] = $obj->formatNumber($rs[0]['finaldiscount'],$finalDiscDecimal);
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
	$_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']); 
    
    $_POST['chkIncludeTax'] = $rs[0]['ispriceincludetax'];
    $_POST['chkIsOutsource'] = $rs[0]['isoutsource'];
    if ($rs[0]['isoutsource'] == 1){
        $useInhouse = 'display-none';
        $useOutsource = '';
        $readonlyOutsource = false;
    }
    
    $_POST['hidSupplierKey'] = $rs[0]['supplierkey'];
    if(!empty($_POST['hidSupplierKey'])){
        $rsSupplier = $supplier->getDataRowById($_POST['hidSupplierKey']);
		$_POST['supplierName'] = $rsSupplier[0]['name'];   
    }
	
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']);
	$_POST['etcCost'] = $obj->formatNumber($rs[0]['etccost']);
	$_POST['selTermOfPaymentKey'] = $rs[0]['termofpaymentkey'] ;
	$_POST['selCategory'] = $rs[0]['categorykey'] ;
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance']) ;  
    
	$_POST['mileage'] =  $obj->formatNumber($rs[0]['mileage']) ;
    $_POST['complaint'] = $rs[0]['complaint'];
    
    switch($rs[0]['typekey']){
        case '1' :  $_POST['hidCarKey'] = $rs[0]['carkey']; 
                    if (!empty($_POST['hidCarKey'])){
                        $rsCar = $car->getDataRowById($_POST['hidCarKey']);
                        $_POST['policeNumber'] = $rsCar[0]['policenumber'];
                        $_POST['year'] = $rsCar[0]['year'];
                        $_POST['fuelType'] = $rsCar[0]['fueltype'];

                        $rsCarCategory = $carCategory->getDataRowById($rsCar[0]['categorykey']);
                        $_POST['categoryName'] = $rsCarCategory[0]['name'] ;

                        $rsBrand = $brand->getDataRowById($rsCar[0]['brandkey']);
                        $_POST['brandName'] = (!empty($rsBrand)) ? $rsBrand[0]['name'] : '' ;
                    }
                    break;
        case '2' :  $_POST['hidChassisKey'] = $rs[0]['chassiskey']; 
                    if (!empty($rs[0]['chassiskey'])){
                        $rsChassis = $chassis->getDataRowById($rs[0]['chassiskey']);
                        $_POST['chassisNumber'] = $rsChassis[0]['chassisnumber'];
                    }
                    break;
    }
      
	  
      

    $_POST['totalPayment'] = $obj->formatNumber($rs[0]['totalpayment']); 
    
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
 
     
    //update file 
    $rsFileDetail = $obj->getFileDetail($id);
    if($useStorage){ 
        
    }else{ 
        
		
        if(count($rsFileDetail) > 0){
            $sourcePath = $obj->defaultDocUploadPath.$obj->uploadFileFolder.$id;
            $destinationPath = $obj->uploadTempDoc.$obj->uploadFileFolder.$id; 
            $obj->deleteAll($destinationPath); 

            if(!is_dir($destinationPath)) 
                mkdir ($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath,$destinationPath);  
        }

    }
    
	$_POST['action'] = 'edit'; 
} 


if (!empty($_GET['id']) && ($_POST['selStatus']==2 || $_POST['selStatus']==3 )){ 
    $_POST['action'] = 'resendEmail';
}

$rsTOP = $termOfPayment->searchData('','',true, ' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')', ' order by duedays asc');
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');  
$arrCategory = $obj->convertForCombobox($obj->getMaintenanceCategory(),'pkey','name');      
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrTOP = $class->convertForCombobox($rsTOP,'pkey','name'); 
$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->getDataForCommboboxWithPrivileges($editPaymentMethodInactiveCriteria),'pkey','name');    
$arrTechnician = $class->convertForCombobox($employee->searchData('','',true, ' and ('.$employee->tableName.'.statuskey = 2 ' .$editSalesInactiveCriteria.')'),'pkey','name'); 
$arrShipment = $class->convertForCombobox($shipment->searchData('statuskey',1,true),'pkey','name');
$arrUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
    
	jQuery(document).ready(function(){    
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?> 
        var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName,array('key'))['key'];  ?>  
             
                
         var cashTOP = Array();
         <?php 
            for ($i=0;$i<count($rsTOP);$i++){
                if ($rsTOP[$i]['duedays'] <> 0)
                    echo 'cashTOP.push('.$rsTOP[$i]['pkey'].');'.chr(13);
            }
         ?>

        var lang = new Array();     
        lang['partsPosition'] = "<?php echo $obj->lang['partsPosition']; ?>";  
    
        var varConstant = {  
                            ITEM : <?php echo json_encode(ITEM); ?>,
                            SERVICE : <?php echo json_encode(SERVICE); ?>,
                            USE_GPS_MILEAGE : <?php echo ($obj->loadSetting('useGPSMileage') == 1) ? 1 : 0; ?>,
                            LANG : lang,
                            UPLOAD_FILE_FOLDER : "<?php echo $obj->uploadFileFolder; ?>",
                            RS_ITEM_FILE : <?php echo json_encode($rsFileDetail); ?>,
                            USE_STORAGE : <?php echo ($useStorage) ? "true" : "false"; ?>
                        };
        var carServiceMaintenance = new CarServiceMaintenance(tabID,tablekey,varConstant,cashTOP); 
        prepareHandler(carServiceMaintenance); 
        
        var fieldValidation =  {
                                    code: {
                                            validators: {
                                                notEmpty: {  message: phpErrorMsg.code[1] }, 
                                            }
                                    }, 
                                } ; 
        
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
 
}); 

</script>

</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
    <?php prepareOnLoadDataForm($obj); ?>   
    <?php echo $obj->inputHidden('hidSendEmail'); ?>
    <?php echo $obj->inputHidden('hidCreditLimit'); ?>
     
       <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col"> 
      						 <div class="div-tab-panel"> 
                                   <div class="div-table-caption border-orange">Informasi Umum</div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Status</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Kode</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                    </div>   
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Tanggal</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Tanggal Pekerjaan</label>
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume"><?php echo $obj->inputDate('executeDate', array('allowedStatusForEdit' => array(1,2))); ?></div>
                                                <?php if ($showEditExecuteDate) { ?>
                                                <div><?php echo $obj->inputButton('executeBtn',$obj->lang['save'],array('class' =>'btn btn-primary btn-second-tone',  'allowedStatusForEdit' => array(2))); ?></div>
                                                <?php } ?>
                                            </div>
                                        </div> 
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Tanggal Estimasi Selesai</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('estDate'); ?> 
                                        </div> 
                                    </div>      
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div>  
                                <div class="form-group">
                                      <label class="col-xs-3 control-label"><?php echo $obj->lang['carMaintenanceRequest']; ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php     
                                                   echo $obj->inputAutoComplete(array(
                                                                                            'objRefer' => $carServiceMaintenanceRequest,
                                                                                            'revalidateField' => true, 
                                                                                            'element' => array('value' => 'referenceCode',
                                                                                                               'key' => 'hidCarMaintenanceRequestKey'),
                                                                                            'source' => array(
                                                                                                                'url' => 'ajax-car-service-maintenance-request.php',
                                                                                                                'data' => array(  'action' =>'searchData', 'statuskey' => '(2)' )
                                                                                                            ) ,  
                                                                                          )
                                                                                    );  
                                                 
                                                       
                                                ?> 
                                        </div> 
                                    </div>    
                        
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selCategory', $arrCategory); ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceReference']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('refCode'); ?>
                                        </div> 
                                    </div> 
                                 
                                     <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['technician']); ?></label> 
                                            <div class="col-xs-9"> 
                                                <?php  echo $obj->inputAutoComplete(array(  
                                                                                    'objRefer' => $employee,
                                                                                    'element' => array('value' => 'techicianName',
                                                                                                       'key' => 'hidTechicianKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-employee.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    ) ,
                                                                                    'revalidateField' => false, 
                                                                                  )
                                                                            );  
                                                ?>
                                            </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['externalWorkshop']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex" style="height: 34px">          
                                                <div> <?php echo  $obj->inputCheckBox('chkIsOutsource', array('etc' => 'style="line-height: 2em !important; margin-right:0.5em"')); ?></div>
                                                <div class="consume"> 
                                                       <?php  echo $obj->inputAutoComplete(array(  
                                                                                    'readonly' => $readonlyOutsource,
                                                                                    'objRefer' => $supplier,
                                                                                    'element' => array('value' => 'supplierName',
                                                                                                       'key' => 'hidSupplierKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-supplier.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    ) ,
                                                                                    'revalidateField' => false, 
                                                                                  )
                                                                            );  
                                                        ?>
                                                </div>
                                            </div> 
 
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div>   
                             </div>
                              
                    </div>
                     <div class="div-table-col">
                        <div class="div-tab-panel">    
                               <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['carInformation']); ?></div> 
                                <div class="form-group" >
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['type']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selType', $arrType); ?>
                                        </div> 
                            </div>     
                            
                            <div class=" vehicle-type type-2" style="display:none;  <?php echo $displayType[2]; ?>">
                             <div class="form-group">
                                        <label class="col-xs-3 control-label">Chassis</label> 
                                        <div class="col-xs-9"> 
                                            <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                    'objRefer' => $chassis,
                                                                                    'revalidateField' => false, 
                                                                                    'element' => array('value' => 'chassisNumber',
                                                                                                       'key' => 'hidChassisKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-chassis.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    )                 
                                                                                )
                                                                            );  
                                        ?> 
                                        </div> 
                            </div> 
                            </div> 
                            <div class=" vehicle-type type-1"  style="<?php echo $displayType[1]; ?>">
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">No. Polisi</label> 
                                        <div class="col-xs-9"> 
                                            <?php  echo $obj->inputAutoComplete(array(  
                                                                                'objRefer' => $car,
                                                                                'element' => array('value' => 'policeNumber',
                                                                                                   'key' => 'hidCarKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-car.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'revalidateField' => true,
                                                                                'callbackFunction' => 'getTabObj().updateCarInformation()'
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div> 
                                                     
                                     <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['driver']); ?></label> 
                                            <div class="col-xs-9"> 
                                                <?php  echo $obj->inputAutoComplete(array(  
                                                                                    'objRefer' => $employee,
                                                                                    'element' => array('value' => 'driverName',
                                                                                                       'key' => 'hidDriverKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-employee.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isdriver' => 1 )
                                                                                                    ) 
                                                                                  )
                                                                            );  
                                                ?>
                                            </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['year']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('year', array('readonly' => true)); ?>
                                        </div> 
                                    </div> 
                                 
<!--
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['typesOfFuel']); ?></label> 
                                        <div class="col-xs-9" >  
                                             <?php echo $obj->inputText('fuelType', array('readonly' => true)); ?>
                                        </div>  
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['carSeries']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php echo $obj->inputText('carSeriesName', array('readonly' => true)); ?>
                                        </div> 
                                    </div>   
-->
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mileage']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputNumber('mileage'); ?>
                                        </div> 
                                    </div> 
                           </div>        
                         </div>
                           
                            <div class="div-tab-panel">    
                                <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['complaint']); ?></div>

                                <div class="form-group"> 
                                    <div class="col-xs-12"> 
                                        <?php echo $obj->inputTextArea('complaint', array('etc' => 'style="height:10em;"', 'readonly' => true)); ?>
                                    </div>
                                </div>
                            </div>

                         
                            <?php if($useStorage) {  ?>
                             <div id="file-update-ajax" class="div-tab-panel">
                                 <div class="div-table" style="width:100%"> 
                                    <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['file']); ?></div> 
                                    <?php echo $obj->inputUploadFilePlugin($rs,$rsFileDetail, array('allowedStatusForEdit' => array(1,2,3,4))); ?> 
                                 </div>
                            </div>     
                            <?php }else { ?> 

                            <div class="div-tab-panel"> 
                             <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['files']); ?></div> 
                             <div class="form-group"> 
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['documentFiles']); ?></label> 
                                <div class="col-xs-9"> 
                                        <!-- file uploader --> 
                                        <div class="item-file-uploader">
                                            <ul class="file-list" ></ul>
                                            <div style="clear:both; height:1em; "></div>
                                            <div class="file-uploader">	
                                                <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                            </div>
                                          </div>  
                                        <!-- file uploader -->
                                        <?php if (!empty($rs) && in_array($rs[0]['statuskey'], array(2,3)) ) {
                                             echo $obj->inputButton('btnUpdateFile', $obj->lang['update'], array('allowedStatusForEdit' => array(1,2,3),'class' =>'btn btn-primary btn-second-tone'));
                                        } ?>
                                </div>  
                              </div>  
                              </div>  

                            <?php }  ?> 

                    </div>
           </div>
      </div> 
      
        <div class="div-table transaction-detail" style="width:100%; ">
                <div class="div-table-row">  
                    <div class="div-table-col detail-col-header" style="width:250px;"><?php echo ucwords($obj->lang['itemOrService']); ?></div>

                    <!--
                    <div class="div-table-col detail-col-header" style="width:130px;"><?php echo ucwords($obj->lang['position']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:200px;"><?php echo ucwords($obj->lang['lastItemOrService']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:140px;"><?php echo ucwords($obj->lang['lastSN']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:140px;"><?php echo ucwords($obj->lang['newSN']); ?></div>
                    -->

                    <div class="div-table-col detail-col-header" ><?php echo ucwords($obj->lang['description']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div>
                    <div class="div-table-col detail-col-header external-workshop <?php echo $useOutsource; ?>" style="width:100px; text-align:right; "></div>
                    <div class="div-table-col detail-col-header external-workshop <?php echo $useOutsource; ?>" style="width:70px; text-align:right; "><?php echo ucwords($obj->lang['discount']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div> 
                    <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                </div>
        </div>    
        <div class="div-table mnv-transaction item-row  transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row" style="display:none" >  
                    <div class="div-table-col"></div> 
                    <div class="div-table-col"></div>
                    <div class="div-table-col"></div> 
                </div>
            
				<?php  
                    $totalRows = count($rsSalesDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  
					 
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $readonlyPrice = true;
                        $readonlyQty = false;
                        $etc = '';  
                        $needSN = true;  
                        $disabledSN = 'disabled="disabled"'; 
                        
						if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                            $unitname = 'Pcs';
                        } else {  
                            
                            $decimal = 0;
                            $inputnumber = 'inputnumber';
                            $readonlyPrice = ($rs[0]['isoutsource'] == 0 && $rsSalesDetail[$i]['itemtype'] == ITEM ) ? true : false;
                                
                            if ($rsSalesDetail[$i]['discounttype']  == 2){ 
                                $decimal = 2;
                                $inputnumber = 'inputdecimal';
                            } 
                            
                            $_POST['hidDetailKey[]'] =  $rsSalesDetail[$i]['pkey']; 
                            $_POST['hidItemKey[]'] =  $rsSalesDetail[$i]['itemkey']; 
                            $_POST['hidItemType[]'] =  $rsSalesDetail[$i]['itemtype']; 
                            $_POST['itemName[]'] =  $rsSalesDetail[$i]['itemname']; 
                            $_POST['detailDesc[]'] =  $rsSalesDetail[$i]['trdesc']; 
                            $_POST['isPackage[]'] = $rsSalesDetail[$i]['ispackage']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsSalesDetail[$i]['qty']); 
                            $_POST['priceInUnit[]'] =   $obj->formatNumber($rsSalesDetail[$i]['priceinunit']); 
                            $_POST['selDiscountType[]'] =  $rsSalesDetail[$i]['discounttype'] ; 
                            $_POST['discountValueInUnit[]'] =   $obj->formatNumber($rsSalesDetail[$i]['discount'],$decimal); 
                            $_POST['subtotal[]'] =   $obj->formatNumber($rsSalesDetail[$i]['total']);
                            $_POST['newSN[]'] =  $rsSalesDetail[$i]['newsn'] ; 
                            $_POST['hidNewSNKey[]'] =  $rsSalesDetail[$i]['newsnkey'] ; 
                            $_POST['lastSN[]'] =  $rsSalesDetail[$i]['lastsn'] ; 
                            $_POST['selItemPosition[]'] =  $rsSalesDetail[$i]['itemposition'];
                            
                            $_POST['hidLastItemKey[]'] =  $rsSalesDetail[$i]['lastitemkey']; 
                            $_POST['lastItemName[]'] =  $rsSalesDetail[$i]['lastitemname']; 

                            if ($rsSalesDetail[$i]['needsn'] == 1) {
                                $needSN = false;  
                                $readonlyQty = true;  
                            } 
                            
                            
                            $_POST['selMovementType[]'] =  $rsSalesDetail[$i]['movementtype'];
                            
                            $_POST['hidDetailWarehouseKey[]'] =  $rsSalesDetail[$i]['warehousekey'];  
                            $rsWarehouse = $warehouse->getDataRowById($rsSalesDetail[$i]['warehousekey']);
                            $_POST['detailWarehouseName[]'] =  (!empty($rsWarehouse)) ? $rsWarehouse[0]['name'] : '';

                            $_POST['selUnit[]'] =  $rsSalesDetail[$i]['unitkey']; 
                             
                            if(USE_SN){ 
                                $arrItemPosition = $item->getItemPositionForMaintenance($rsSalesDetail[$i]['itemkey'], $rs[0]['carkey']);
                                $arrItemPosition = $obj->convertForCombobox($arrItemPosition, 'itempositionkey','positioname',$obj->lang['partsPosition']);   
                            }
                        }
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-detail" style="width:250px;"><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' => $etc,'add-class'=>'mnv-barcode-input')); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemType[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('isPackage[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidCOGS[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('detailDesc[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:80px;"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'readonly' => !$needSN, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                                <div class="div-table-col detail-col-detail" style="width:80px;"><?php echo $obj->inputSelect('selUnit[]',$arrUnit, array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('priceInUnit[]', array('readonly' => $readonlyPrice, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                                <div class="div-table-col detail-col-detail external-workshop <?php echo $useOutsource; ?>" style="width:100px; "><?php echo $obj->inputNumber('discountValueInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                                <div class="div-table-col detail-col-detail external-workshop <?php echo $useOutsource; ?>" style="width:70px; "><?php echo $obj->inputSelect('selDiscountType[]',$obj->arrDiscountType, array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('subtotal[]', array('overwritePost' => $overwrite, 'readonly' =>true, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div> 
                            </div>        
                        </div>
                    
                        <?php if(USE_SN){  ?>
                            <div class="flex" style="padding:0 0.5em">
                                <div style="width:150px"><?php echo $obj->inputSelect('selItemPosition[]',$arrItemPosition, array('overwritePost' => $overwrite, 'etc' => $etc, 'add-class'=>'label-style')); ?></div>
                                <div>
                                    <?php echo $obj->inputText('newSN[]',array('overwritePost' => $overwrite, 'etc' => $etc .' placeholder="'.$obj->lang['serialNumber'].'" ','add-class'=>'mnv-barcode-input label-style', 'readonly' => $needSN)); ?>
                                    <?php echo $obj->inputHidden('hidNewSNKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                </div>
                                <div style="margin-left: 2em"><?php echo $obj->lang['previousPart']; ?></div>
                                <div style="width:200px" >
                                    <?php echo $obj->inputText('lastItemName[]',array('overwritePost' => $overwrite, 'etc' => $etc.' placeholder="-" ','add-class'=>'label-style', 'readonly' => true)); ?>
                                    <?php echo $obj->inputHidden('hidLastItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                </div>
                                <div ><?php echo $obj->inputText('lastSN[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:left;"' .$etc.' placeholder="-" ','add-class'=>'label-style', 'readonly' => $needSN)); ?></div>
                            
                            </div>  
                        <?php }  ?>
                        
                    </div>
                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>" style="vertical-align:top; padding-top:10px !important"><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" ')); ?></div>
                </div> 
 
            <?php } ?> 
                   
         </div>        
        
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'],array('class' => 'btn btn-primary btn-second-tone')); ?></div>
       
        <div class="inhouse-workshop  <?php echo $useInhouse; ?>" > 
        <div style="width:300px; float:right; ">
            <div class="div-table" style="width:100%">
            <div class="div-table-row  form-group"> 
                <div class="div-table-col-3" style="text-align:right;"> 
                    <?php echo ucwords($obj->lang['total']); ?>
                </div>  
                <div class="div-table-col-3" style=" width: 180px"> 
                    <?php echo $obj->inputNumber('total', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?>  
                </div>
                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
            </div> 
            </div>
        </div>
        </div>  
      
        <div class="external-workshop  <?php echo $useOutsource; ?>" >
 
                      <div style="width:350px; float:right; ">
                            <div class="div-table" style="width:100%" >
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;">
                                        <?php echo ucwords($obj->lang['payment']); ?> 
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                         <?php echo  $obj->inputSelect('selTermOfPaymentKey', $arrTOP); ?>
                                    </div> 
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                </div> 
                             </div>    
                             
                    <div class="mnv-total-group mnv-payment-method cashTOP "  >  
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['totalPayment']; ?>
                                    </div>  
                                    <div class="div-table-col-3"  style="width:180px"> 
                                            <?php echo $obj->inputCollapsibleNumber('totalPayment', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>
                         
                        <div class="mnv-total-group-detail">
                            <div class="div-table  transaction-detail" style="width: 100%">
                                <?php 

                                    $totalRows = count($rsPaymentMethodDetail); 
                                
                                    for($i=0;$i<=$totalRows;$i++) {
                                            $class =  'transaction-detail-row';
                                            $overwrite = true; 
                                            $disabled = false; 

                                            if ($i == $totalRows ){
                                                $class = 'payment-method-row-template row-template'; 
                                                $overwrite = false; 
                                                $disabled = true; 
                                            } else {   
                                                $_POST['hidDetailPaymentKey[]'] = $rsPaymentMethodDetail[$i]['pkey'];
                                                $_POST['selPaymentMethod[]'] = $rsPaymentMethodDetail[$i]['paymentkey'];
                                                $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsPaymentMethodDetail[$i]['amount']); 
                                            }
                                ?> 

                                <div class="div-table-row form-group <?php echo $class; ?>">
                                    <div class="div-table-col-3" style="text-align:right;">  
                                            <?php echo $obj->inputHidden('hidDetailPaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo  $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px"> 
                                           <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled,'class'=>'form-control inputnumber mnv-detail-field', 'etc' => 'style="text-align:right;" ')); ?>
                                    </div>  
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                        <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>',array('etc' => 'tabIndex="-1"  attrhandler="getTabObj().calculateTotal()"', 'class' =>'btn btn-link remove-button' )); ?>
                                    </div>
                                </div> 

                                <?php } ?> 

                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3"></div>   
                                    <div class="div-table-col-3">
                                        <div class="text-link-01 mnv-total-group-hide-detail" style="float:right; text-align:right;" ><?php echo ucwords($obj->lang['hideDetail']); ?> </div> 
                                    </div>
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?>"></div>
                                </div>  
                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3 " style="height:1em"></div> <div class="div-table-col-3 "></div> <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> "></div>
                                </div>  

                           </div>   
                        </div>
                    </div>  
                        
                          <div class="div-table" style="width:100%; margin-top:1em">
                              
                                <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;">
                                        <?php echo ucwords($obj->lang['balance']); ?> 
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                        <?php echo $obj->inputNumber('balance', array ( 'readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>  
                                    </div> 
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                </div> 
                          </div>    
                      </div>    
                      <div class="div-table" style="float:right; margin-right:4em">
                            <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;">
                                     <?php echo ucwords($obj->lang['subtotal']); ?>
                                </div>  
                                <div class="div-table-col-3" style="width:200px;"> 
                                    <?php echo $obj->inputNumber('subtotal', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                                </div>
                                
                            </div>
                           <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3"  style="text-align:right;">
                                     <?php echo ucwords($obj->lang['discount']); ?>
                                </div>  
                                <div class="div-table-col-3"> 
                                    <div class="flex">          
                                        <div><?php echo $obj->inputSelect('selFinalDiscountType',$obj->arrDiscountType); ?> </div>
                                        <div class="consume"> <?php echo $obj->inputNumber('finalDiscount', array ('class'=> 'form-control ' . $finalDiscDecimalType, 'etc' => 'style="text-align:right;"')) ;?> </div>
                                     </div> 
                                </div> 
                            </div>
                            
                             <div class="div-table-row  form-group   form-detail-field"> 
                                <div class="div-table-col-3" style="text-align:right; padding-top:2em;">
                                      <?php echo ucwords($obj->lang['beforeTax']); ?>
                                </div>  
                                <div class="div-table-col-3" style="padding-top:2em;"> 
                                     <?php echo $obj->inputNumber('beforeTaxTotal', array( 'disabled' => true, 'etc' => 'style="text-align:right;"')); ?>
                                </div>
                                
                            </div>
                            
                             <div class="div-table-row  form-group   form-detail-field"> 
                                  <div class="div-table-col-3"  style="text-align:right;">
                                  <?php echo strtoupper($obj->lang['PPN']); ?> [Include]
                                 </div>   
                                 <div class="div-table-col-3"> 
                                     <div class="flex">    
                                        <div><?php echo $obj->inputCheckBox('chkIncludeTax' ); ?></div>  
                                        <div class="percentage-col"><?php echo $obj->inputDecimal('taxPercentage', array('etc' => 'style="text-align:right;" ')); ?></div> 
                                        <div>%</div>
                                        <div class="consume"><?php echo $obj->inputNumber('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                                      </div> 
                                </div>  
                                <div class="div-table-col" > </div>
                             </div>   
                                
                           
                            
                             <div class="div-table-row" style="display:none"> 
                                <div class="div-table-col-3" style="text-align:right;"> 
                                     <?php echo ucwords($obj->lang['etccost']); ?>
                                </div>      
                                <div class="div-table-col-3"> 
                                    <?php echo $obj->inputNumber('etcCost', array('etc' => 'style="text-align:right;"')); ?> 
                                  </div>
                                <div class="div-table-col" > </div>
                            </div>
                           <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;"> 
                                      <?php echo ucwords($obj->lang['total']); ?>
                                </div>  
                                <div class="div-table-col-3"> 
                                    <?php echo $obj->inputNumber('total', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?>  
                                </div>
                                <div class="div-table-col"> </div>
                            </div> 
                             <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;"> </div>  
                                <div class="div-table-col-3"> 
                                       <div class="form-detail-button" style="float:right; text-align:right;" relalt="Sembunyikan Detail">Tampilkan Detail</div>
                                </div>
                                <div class="div-table-col"> </div>
                            </div> 
                      </div>     
      				  <div style="clear:both"></div> 
        </div>
         
      
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
