<?php  
include '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Car.class.php');
$car = createObjAndAddToCol(new Car()); 
$warehouse = createObjAndAddToCol(new Warehouse()); 
$brand = createObjAndAddToCol(new Brand()); 
$carCategory = createObjAndAddToCol(new CarCategory()); 
$supplier = createObjAndAddToCol(new Supplier()); 
$employee = createObjAndAddToCol(new Employee());
$gps = createObjAndAddToCol(new GPS());
$item = createObjAndAddToCol(new Item());

$obj= $car;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class


if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'carList'; 
    
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = '';
$editGpsInactiveCriteria = '';
$finalDiscDecimalType = 'inputnumber';
$finalDiscDecimal = 0;

$_POST['licenseExpiryDate'] = date('d / m / Y');
$_POST['kirExpiryDate'] = date('d / m / Y');
$_POST['tidExpiryDate'] = date('d / m / Y');
$_POST['licenseTaxExpiryDate'] = date('d / m / Y');
  
$showVendor = false;

$rs = prepareOnLoadData($obj); 
if (!empty($_GET['id'])){ 
    $id = $_GET['id'];	 
    

    $_POST['cbm'] = $obj->formatNumber($rs[0]['cbm'],2); 
    $_POST['length'] = $obj->formatNumber($rs[0]['length'], 2); 
    $_POST['width'] = $obj->formatNumber($rs[0]['width'], 2); 
    $_POST['height'] = $obj->formatNumber($rs[0]['height'], 2); 
    if ($rs[0]['isvendorkey']){ 
        $showVendor = true;
        
        if ($rs[0]['commissiontype']  == 2){ 
            $finalDiscDecimal = 2;
            $finalDiscDecimalType = 'inputdecimal';
        }
        
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        $_POST['supplierName'] = $rsSupplier[0]['name'] ;
        $_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'] ;  
        $_POST['selCommissionType'] = $rs[0]['commissiontype'] ;
        $_POST['commissionValue'] = $obj->formatNumber($rs[0]['commission'],$finalDiscDecimal);
        $_POST['adminFee'] = $obj->formatNumber($rs[0]['adminfee']);
    }
	$_POST['hidBrandKey'] = $rs[0]['brandkey']; 
	if (!empty($_POST['hidBrandKey'])){
		$rsBrand = $brand->searchData($brand->tableName.'.pkey',$rs[0]['brandkey'],true);
		$_POST['brandName'] = $rsBrand[0]['name'];
	}
    
    $_POST['hidDriverKey'] = $rs[0]['driverkey'];
    if (!empty($_POST['hidDriverKey'])) {
        $rsEmployee = $employee->searchData($employee->tableName . '.pkey', $rs[0]['driverkey'], true);
        $_POST['driverName'] = $rsEmployee[0]['name'];
    }
	$_POST['hidCategoryKey'] = $rs[0]['categorykey']; 
	if (!empty($_POST['hidCategoryKey'])){
		$rsCategory = $carCategory->searchData($carCategory->tableName.'.pkey',$rs[0]['categorykey'],true);
		$_POST['categoryName'] = $rsCategory[0]['name'];
	}
      
     
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
	$editGpsInactiveCriteria = ' or '.$gps->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['gpskey']);
    
	$qrResult = $obj->createQR( $rs[0]['code'],12);
    
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');   
$arrContract = $class->convertForCombobox($car->getPartnershipType(), 'pkey','name');

$arrNoGPS = array(array('pkey' => 0, 'name' => '-----')); // harus 2 kali array, karena 2 dimensi
$rsGPS = $gps->searchData('', '', true, ' and (' . $gps->tableName . '.statuskey = 1' . $editGpsInactiveCriteria . ')');
$rsGPS = array_merge($arrNoGPS,$rsGPS);
$arrGps = $class->convertForCombobox($rsGPS, 'pkey', 'name');
$rsSparePartType = $item->getSparePartType();



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  

<script type="text/javascript"> 
 jQuery(document).ready(function(){   
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>     
         var car = new Car(tabID);
    
         prepareHandler(car);   
        
         var fieldValidation =  {
                code: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        }, 
                    }
                }, 
				
				policeNumber: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.car[1]
                        }, 
                    }
                },  	
                
				categoryName: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.category[1]
                        },  
                    }
                }
        }; 
        
        setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
	});
			
			
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
    <?php prepareOnLoadDataForm($obj); ?>     
    <div class="div-table main-tab-table-2">
              <div class="div-table-row">
                    <div class="div-table-col">  
                  		   	<div class="div-tab-panel">    
                                    <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                                        </div> 
                                    </div>     
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                     </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo  $obj->inputSelect('selWarehouse',$arrWarehouse); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['carRegistrationNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('policeNumber'); ?>
                                        </div> 
                                     </div>  
                                 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['brand']); ?></label> 
                                        <div class="col-xs-9"> 
                                               <?php  
                                                        $popupOpt = (!$isQuickAdd) ? array(
                                                                'url' => 'brandForm.php',
                                                                'element' => array('value' => 'brandName',
                                                                       'key' => 'hidBrandKey'),
                                                                'width' => '600px',
                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['brand'])
                                                            )  : '';
                                     
                                            
                                                        echo $obj->inputAutoComplete(array(  
                                                                                'objRefer' => $brand,
                                                                                'element' => array('value' => 'brandName',
                                                                                                   'key' => 'hidBrandKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-brand.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'popupForm' => $popupOpt
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                     </div>   
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php  
                                                        $popupOpt = (!$isQuickAdd) ? array(
                                                                'url' => 'carCategoryForm.php',
                                                                'element' =>  array('value' => 'categoryName', 'key' => 'hidCategoryKey') ,
                                                                'width' => '600px',
                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['carCategory'])
                                                            )  : ''; 
                                            
                                                        echo $obj->inputAutoComplete(array(  
                                                                                'objRefer' => $carCategory,
                                                                                'revalidateField' => true, 
                                                                                'element' => array('value' => 'categoryName',
                                                                                                   'key' => 'hidCategoryKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-car-category.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'popupForm' => $popupOpt
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                     </div>   
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['driver']; ?></label>
                                        <div class="col-xs-9">
                                                    <?php
                                                    echo $obj->inputAutoComplete(
                                                        array(  
                                                            'element' => array(
                                                                'value' => 'driverName',
                                                                'key' => 'hidDriverKey'
                                                            ),
                                                            'source' => array(
                                                                'url' => 'ajax-employee.php',
                                                                'data' => array(
                                                                    'action' => 'searchData',
                                                                    'isdriver' => 1
                                                                )
                                                            )
                                                        )
                                                    );
                                                    ?>

                                        </div>

                                    </div>
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['GPS']); ?> / <?php echo ucwords($obj->lang['GPSID']); ?></label> 
                                        <div class="col-xs-9"> 
 											<div class="flex">
                                                <div class="consume"><?php echo $obj->inputSelect('selGPSKey', $arrGps); ?>
                                                </div>
                                                <div>/</div>
                                                <div class="consume"><?php echo $obj->inputText('gpsTrackerId'); ?></div>
                                            </div>                                        </div> 
                                     </div>   
                                
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('trDesc',array('etc' => 'style="height:10em;"' )); ?>
                                        </div> 
                                     </div>  
                            </div>   
                        
                        <div class="div-tab-panel">
                         <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['entrustedCar']); ?></div> 
                         <div class="form-group">
					                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['entrustedCar']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputCheckBox('chkIsVendor'); ?>   
 
                                        </div> 
                                    </div>
                                    <div class="vendor" style="<?php echo (!$showVendor) ? 'display:none' :''; ?>">
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label> 
                                            <div class="col-xs-9"> 
                                               <?php  echo $obj->inputAutoComplete(array( 
                                                                                        'objRefer' => $supplier,
                                                                                        'revalidateField' => true,
                                                                                        'element' => array('value' => 'supplierName',
                                                                                                           'key' => 'hidSupplierKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-supplier.php',
                                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                                        ) ,
                                                                                        'popupForm' => array(
                                                                                                            'url' => 'supplierForm.php',
                                                                                                            'element' => array('value' => 'supplierName',
                                                                                                                   'key' => 'hidSupplierKey'),
                                                                                                            'width' => '1000px',
                                                                                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['supplier'])
                                                                                                        ),
                                                                                        'callbackFunction' => 'getTabObj().updateTOP()'
                                                                                      )
                                                                                );  
                                                    ?>
                                            </div> 
                                        </div> 
                                         <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['partnershipType']); ?></label> 
                                            <div class="col-xs-9"> 
                                                  <?php echo  $obj->inputSelect('selCarContract',$arrContract); ?> 
                                            </div> 
                                        </div> 
                                        <!--<div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['commission']); ?></label> 
                                            <div class="col-xs-9"> 
                                                <div class="flex">
                                                    <div><?php echo  $obj->inputSelect('selCommissionType', $obj->arrDiscountType); ?></div>
                                                    <div class="consume"><?php echo $obj->inputNumber('commissionValue', array ('class'=> 'form-control ' . $finalDiscDecimalType)); ?></div>
                                                </div> 
                                            </div>  
                                        </div>-->
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['adminFee']); ?></label> 
                                            <div class="col-xs-9">  
                                                  <?php echo $obj->inputNumber('adminFee'); ?>
                                             </div>  
                                        </div>
                                    </div>
                        </div>
                        
                         
                     	<?php if (isset($qrResult) && !empty($qrResult)){  
                                echo ' <div class="div-tab-panel">';
                                echo '<div class="div-table-caption border-black">'.ucwords($obj->lang['QRCode']).'</div>';
                                echo ' <div class="col-xs-12">';
                                    echo '<a href="../phpthumb/phpThumb.php?src='.$qrResult['url'].'&far=C&f=png&hash='.getPHPThumbHash($qrResult['filename']).'" target="_blank"><img src="../phpthumb/phpThumb.php?src='.$qrResult['url'].'&w=150&h=150&far=C&f=png&hash='.getPHPThumbHash($qrResult['filename']).'"></a>';
                                echo ' </div>';
                                echo ' </div>';
                            }
                        ?>
                    
                  </div> 
                  
                    <div class="div-table-col">  
                  		   	<div class="div-tab-panel">    
                                    <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['othersInformation']); ?></div> 
                                     
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bpkbRegisteredName']); ?></label> 
                                            <div class="col-xs-9"> 
                                                  <?php echo $obj->inputText('bpkbName'); ?>
                                            </div> 
                                        </div>  
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bpkbRegisteredNumber']); ?></label> 
                                            <div class="col-xs-9"> 
                                                  <?php echo $obj->inputText('bpkbNumber'); ?>
                                            </div> 
                                        </div> 
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['year']); ?></label> 
                                            <div class="col-xs-9"> 
                                                  <?php echo $obj->inputText('year'); ?>
                                            </div> 
                                        </div> 
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['stnkNumber']); ?></label> 
                                            <div class="col-xs-9"> 
                                                  <?php echo $obj->inputText('licenseNumber'); ?>
                                            </div> 
                                         </div>  
                                         <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['stnkExpiredDate']); ?></label> 
                                            <div class="col-xs-9"> 
                                                 <?php echo $obj->inputDate('licenseExpiryDate'); ?>
                                            </div> 
                                         </div>  
                                         <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['licenseTaxExpiryDate']); ?></label> 
                                            <div class="col-xs-9"> 
                                                 <?php echo $obj->inputDate('licenseTaxExpiryDate'); ?>
                                            </div> 
                                         </div> 
                                         <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['kirNumber']); ?></label> 
                                            <div class="col-xs-9"> 
                                                  <?php echo $obj->inputText('kir'); ?>
                                            </div> 
                                         </div>  
                                         <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['kirExpiredDate']); ?></label> 
                                            <div class="col-xs-9"> 
                                                 <?php echo $obj->inputDate('kirExpiryDate'); ?> 
                                            </div> 
                                         </div>  
                                         <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['tidNumber']); ?></label> 
                                            <div class="col-xs-9"> 
                                                  <?php echo $obj->inputText('tid'); ?> 
                                            </div> 
                                         </div>  
                                         <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['tidExpiredDate']); ?></label> 
                                            <div class="col-xs-9"> 
                                                 <?php echo $obj->inputDate('tidExpiryDate'); ?>  
                                            </div> 
                                         </div>  
                                         <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['machineNumber']); ?></label> 
                                            <div class="col-xs-9"> 
                                                  <?php echo $obj->inputText('machineNumber'); ?>   
                                            </div> 
                                         </div>  
                                         <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['chassisNumber']); ?></label> 
                                            <div class="col-xs-9"> 
                                                  <?php echo $obj->inputText('chassisNumber'); ?>   
                                            </div> 
                                         </div>   
                            </div>   
 							<div class="div-tab-panel">
                                <div class="div-table-caption border-purple"><?php echo $obj->lang['cargoInformation']; ?></div> 
                                <div class="form-group">
                                   <label class="col-xs-3 control-label" style="margin-top:15px"><?php echo ucwords($obj->lang['cubication']); ?></label> 
                                    <div class="col-xs-9" >     
                                        <div class="flex" style="text-align:right">
                                            <div>
                                                <?php echo ucwords($obj->lang['length']); ?> (M)<br>
                                                <?php echo $obj->inputDecimal('length', array('etc' => 'style="text-align:right" ')); ?>
                                            </div>

                                            <div>
                                                <?php echo ucwords($obj->lang['width']); ?> (M)<br>
                                                <?php echo $obj->inputDecimal('width', array('etc' => 'style="text-align:right" ')); ?>
                                            </div> 
                                            <div>
                                                <?php echo ucwords($obj->lang['height']); ?> (M)<br>
                                                <?php echo $obj->inputDecimal('height', array('etc' => 'style="text-align:right" ')); ?>
                                            </div>
                                            <div>
                                                CBM<br>
                                                <?php echo $obj->inputDecimal('cbm', array('etc' => 'style="text-align:right"', 'readonly' => true)); ?>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                                
                                <?php if($obj->activeModule['carservicemaintenance']  && !empty($id)) { ?> 
                                <div class="div-tab-panel"> 
                                    <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['maintenanceReminder']); ?></div>

                                    <?php 
                                        
                                        for ($i = 0; $i < count($rsSparePartType); $i++) { 
                                            
                                            if ($rsSparePartType[$i]['isreminder'] != 1) {
                                                continue;
                                            }

                                            $rsItemInterval = $obj->getCarSparePartIntervalDetail($id, $rsSparePartType[$i]['pkey']);
                                            if (!empty($rsItemInterval)) {
                                                $_POST['hidDetailSparePartIntervalKey_' . $rsSparePartType[$i]['pkey'] . '[]'] = $rsItemInterval[0]['pkey'];
                                                $_POST['month_' . $rsSparePartType[$i]['pkey'] . '[]'] = $obj->formatNumber($rsItemInterval[0]['month']);
                                                $_POST['mileage_' . $rsSparePartType[$i]['pkey'] . '[]'] = $obj->formatNumber($rsItemInterval[0]['mileage']);
                                            }

                                    ?>


                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($rsSparePartType[$i]['name']); ?></label>  
                                            <div class="col-xs-9"> 
                                                <div class="flex">
                                                    <div class="consume">
                                                        <?php echo $obj->inputHidden('hidDetailSparePartIntervalKey_' . $rsSparePartType[$i]['pkey'] . '[]', array('readonly' => true)); ?>
                                                        <?php echo $obj->inputHidden('hidSparepartTypeKey[]', array('readonly' => true, 'value' => $rsSparePartType[$i]['pkey'])); ?>
                                                        <?php echo $obj->inputNumber('mileage_' . $rsSparePartType[$i]['pkey'] . '[]'); ?>
                                                    </div>
                                                    <div class="text-muted"> / <?php echo ucwords($obj->lang['mileage']); ?></div>
                                                    <div class="consume">
                                                        <?php echo $obj->inputNumber('month_' . $rsSparePartType[$i]['pkey'] . '[]'); ?>
                                                    </div>
                                                    <div class="text-muted"> / <?php echo ucwords($obj->lang['month']); ?></div>
                                                </div>
                                            </div>
                                        </div> 

                                    <?php } ?>
                                </div> 
                                <?php } ?>
                        </div>
                        
             </div>
 

        </div>    
       
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div>  
    </form>  
   <?php echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
