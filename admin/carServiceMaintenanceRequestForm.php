<?php
require_once '../_config.php';
require_once '../_include-v2.php';


includeClass('CarServiceMaintenanceRequest.class.php');
$carServiceMaintenanceRequest = createObjAndAddToCol(new CarServiceMaintenanceRequest());
$brand = createObjAndAddToCol(new Brand());
$car = createObjAndAddToCol(new Car());
$carCategory = createObjAndAddToCol(new CarCategory());
$chassis = createObjAndAddToCol(new Chassis());
$warehouse = createObjAndAddToCol(new Warehouse());

$obj = $carServiceMaintenanceRequest;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true))
   ;

$formAction = 'carServiceMaintenanceRequestList';
$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = '';
$editSalesInactiveCriteria = '';

$rsSalesDetail = array();
$rsPaymentMethodDetail = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['estDate'] = date('d / m / Y');

$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';


$arrType = $obj->convertForCombobox($obj->getMaintenanceType(), 'pkey', 'name');
$displayType = array();
foreach ($arrType as $key => $row)
   $displayType[$key] = '';

$rs = prepareOnLoadData($obj);


if (!empty($_GET['id'])) {
   $id = $_GET['id'];
   $rsDetail = $obj->getDetailWithRelatedInformation($id);

   $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'], 'd / m / Y');
   $_POST['estDate'] = $obj->formatDBDate($rs[0]['estdate'], 'd / m / Y');
   $_POST['selWarehouseKey'] = $rs[0]['warehousekey'];

   $_POST['selType'] = $rs[0]['typekey'];
   foreach ($arrType as $key => $row)
      $displayType[$key] = ($key == $rs[0]['typekey']) ? 'display:inline' : 'display:none';

   $_POST['trDesc'] = $rs[0]['trdesc'];
   $_POST['mileage'] = $obj->formatNumber($rs[0]['mileage']);

   switch ($rs[0]['typekey']) {
      case '1':
         $_POST['hidCarKey'] = $rs[0]['carkey'];
         if (!empty($_POST['hidCarKey'])) {
            $rsCar = $car->getDataRowById($_POST['hidCarKey']);
            $_POST['policeNumber'] = $rsCar[0]['policenumber'];
            $_POST['year'] = $rsCar[0]['year'];
            $_POST['fuelType'] = $rsCar[0]['fueltype'];

            $rsCarCategory = $carCategory->getDataRowById($rsCar[0]['categorykey']);
            $_POST['categoryName'] = $rsCarCategory[0]['name'];

            $rsBrand = $brand->getDataRowById($rsCar[0]['brandkey']);
            $_POST['brandName'] = (!empty($rsBrand)) ? $rsBrand[0]['name'] : '';
         }
         break;
      case '2':
         $_POST['hidChassisKey'] = $rs[0]['chassiskey'];
         if (!empty($rs[0]['chassiskey'])) {
            $rsChassis = $chassis->getDataRowById($rs[0]['chassiskey']);
            $_POST['chassisNumber'] = $rsChassis[0]['chassisnumber'];
         }
         break;
   }

   $editWarehouseInactiveCriteria = ' or  ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);

   $_POST['action'] = 'edit';
}


if (!empty($_GET['id']) && ($_POST['selStatus'] == 2 || $_POST['selStatus'] == 3)) {
   $_POST['action'] = 'resendEmail';
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrCategory = $obj->convertForCombobox($obj->getMaintenanceCategory(), 'pkey', 'name');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('', '', true, ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'), 'pkey', 'name');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
    
   jQuery(document).ready(function(){    
        var tabID = <?php echo ($isQuickAdd) ? $_GET['tabID'] : 'selectedTab.newPanel[0].id'; ?> 
        var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName, array('key'))['key']; ?>   
                
        var carServiceMaintenanceRequest = new CarServiceMaintenanceRequest(tabID); 
        prepareHandler(carServiceMaintenanceRequest); 
        
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
                                            <?php echo $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div>      
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selCategory', $arrCategory); ?>
                                        </div> 
                                    </div>    
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
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
                                            <?php echo $obj->inputSelect('selType', $arrType); ?>
                                        </div> 
                            </div>     
                            
                            <div class=" vehicle-type type-2" style="display:none;  <?php echo $displayType[2]; ?>">
                             <div class="form-group">
                                        <label class="col-xs-3 control-label">Chassis</label> 
                                        <div class="col-xs-9"> 
                                            <?php
                                            echo $obj->inputAutoComplete(
                                               array(
                                                  'objRefer' => $chassis,
                                                  'revalidateField' => false,
                                                  'element' => array(
                                                     'value' => 'chassisNumber',
                                                     'key' => 'hidChassisKey'
                                                  ),
                                                  'source' => array(
                                                     'url' => 'ajax-chassis.php',
                                                     'data' => array('action' => 'searchData')
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
                                            <?php echo $obj->inputAutoComplete(
                                               array(
                                                  'objRefer' => $car,
                                                  'element' => array(
                                                     'value' => 'policeNumber',
                                                     'key' => 'hidCarKey'
                                                  ),
                                                  'source' => array(
                                                     'url' => 'ajax-car.php',
                                                     'data' => array('action' => 'searchData')
                                                  ),
                                                  'revalidateField' => true,
                                                  'callbackFunction' => 'getTabObj().updateCarInformation()'
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
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mileage']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputNumber('mileage'); ?>
                                        </div> 
                                    </div> 
                           </div>        
                         </div>
                           
                    </div>
           </div>
      </div> 
  

    <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
		<div class="div-table-row"> 
            <div class="div-table-col detail-col-header">
                <?php echo ucwords($obj->lang['description']) . ' / ' . ucwords($obj->lang['complaint']); ?>
            </div>
            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col">
            </div>
            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col">
            </div>
        </div>
    
        <?php
        $totalDetail = count($rsDetail);

        for ($i = 0; $i <= $totalDetail; $i++) {

            $class = 'transaction-detail-row';
            $overwrite = true;
            $disabled = false;

            if ($i == $totalDetail) {
                $class = 'detail-row-template';
                $overwrite = false;
                $disabled = true;
            } else {
                $_POST['hidDetailKey[]'] = $rsDetail[$i]['pkey'];
                $_POST['trDetailDesc[]'] = $rsDetail[$i]['trdesc'];
            }

            ?>
    
            <div class="div-table-row <?php echo $class; ?>" style="">
                <div class="div-table-col detail-col-detail" style="vertical-align:top">
                    <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                    <?php echo $obj->inputTextArea('trDetailDesc[]', array('overwritePost' => $overwrite,'etc' => 'style="height:10em;"'));?>
                    
                </div>
                <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col" style="vertical-align:top">
                    <?php echo $obj->inputLinkButton('btnAddRow', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="detail-row-template"')); ?>
                </div>
                <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>" style="vertical-align:top">
                    <?php echo $obj->inputLinkButton('btnDeleteRowsCash', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" style="padding:6px 0"')); ?>
                </div>
            </div>
    
        <?php } ?>
    
    </div>
    
      
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php echo $obj->generateSaveButton(array(), true); ?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
