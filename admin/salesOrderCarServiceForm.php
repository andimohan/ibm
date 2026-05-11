<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

 
includeClass('SalesOrderCarService.class.php');
$salesOrderCarService = createObjAndAddToCol( new SalesOrderCarService()); 

$itemUnit = createObjAndAddToCol( new ItemUnit()); 
$item = createObjAndAddToCol( new Item()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$termOfPayment = createObjAndAddToCol( new TermOfPayment()); 
$paymentMethod = createObjAndAddToCol( new PaymentMethod()); 
$shipment = createObjAndAddToCol( new Shipment()); 
//$city = createObjAndAddToCol( new City()); 
$customer = createObjAndAddToCol( new Customer()); 
$car = createObjAndAddToCol( new Car()); 

$obj= $salesOrderCarService;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'salesOrderCarServiceList';
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';
$editSalesInactiveCriteria = ''; 
 
$rsSalesDetail = array();
$rsPaymentMethodDetail = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['trDateOut'] = date('d / m / Y');
  
$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

$rs = prepareOnLoadData($obj);  


if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	$rsSalesDetail = $obj->getDetailWithRelatedInformation($id);
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id); 
	 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['trDateOut'] = $obj->formatDBDate($rs[0]['trdateout'],'d / m / Y');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
    $_POST['hidTechicianKey'] =$rs[0]['techniciankey'];
	$rsEmployee = $employee->getDataRowById($rs[0]['techniciankey']);
	$_POST['techicianName'] = (!empty($rsEmployee)) ? $rsEmployee[0]['name'] : '';
    
    $_POST['hidTechician2Key'] =$rs[0]['technician2key'];
    $rsEmployeeTwo = $employee->getDataRowById($rs[0]['technician2key']);
	$_POST['techician2Name'] = (!empty($rsEmployeeTwo)) ? $rsEmployeeTwo[0]['name'] : '';
     
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
    $_POST['phone'] = $rsCustomer[0]['phone'];
    $_POST['mobile'] = $rsCustomer[0]['mobile'];
    $_POST['email'] = $rsCustomer[0]['email'];
	
    
	$_POST['trDesc'] = $rs[0]['trnotes'];
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']);  

    if ($rs[0]['finaldiscounttype']  == 2){ 
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    } 

	$_POST['selFinalDiscountType'] = $rs[0]['finaldiscounttype'] ;
	$_POST['finalDiscount'] = $obj->formatNumber($rs[0]['finaldiscount'],$finalDiscDecimal);
	$_POST['pointValue'] = $obj->formatNumber($rs[0]['pointvalue']);
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
	$_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']); 
	
 
	$_POST['recipientName'] = $rs[0]['recipientname'];
	$_POST['recipientPhone'] = $rs[0]['recipientphone'];
	$_POST['recipientEmail'] = $rs[0]['recipientemail'];
	$_POST['recipientAddress'] = $rs[0]['recipientaddress'];
	$_POST['selShipment'] = $rs[0]['shipmentkey'];   
	$_POST['shipmentTracking'] = $rs[0]['shipmenttracking'];  
	$_POST['useInsurance'] = $rs[0]['useinsurance'];
    $_POST['chkIncludeTax'] = $rs[0]['ispriceincludetax'];
	   
    $_POST['tax23Percentage'] = $obj->formatNumber($rs[0]['tax23percentage'],2);
	$_POST['tax23Value'] = $obj->formatNumber($rs[0]['tax23value']); 
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']);
	$_POST['shipmentFee'] = $obj->formatNumber($rs[0]['shipmentfee']);
	$_POST['etcCost'] = $obj->formatNumber($rs[0]['etccost']);
	$_POST['selTermOfPaymentKey'] = $rs[0]['termofpaymentkey'] ;
	$_POST['totalPayment'] =  $obj->formatNumber($rs[0]['totalpayment']) ;  
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance']) ;  
        
    $_POST['vehicleid'] = $rs[0]['vehicleid'];
    $_POST['mileage'] =  $obj->formatNumber($rs[0]['mileage']) ;
    
	$_POST['hidCarKey'] = $rs[0]['carkey']; 
    if (!empty($_POST['hidCarKey'])){
		$rsCar = $car->getDataRowById($_POST['hidCarKey']);
		$_POST['policeNumber'] = $rsCar[0]['policenumber'];
        
        $rsCarCategory = $carCategory->getDataRowById($rsCar[0]['categorykey']);
        $_POST['categoryName'] = $rsCarCategory[0]['name'] ;
        
        $rsBrand = $brand->getDataRowById($rsCar[0]['brandkey']);
        $_POST['brandName'] = (!empty($rsBrand)) ? $rsBrand[0]['name'] : '' ;
	}
	  
    
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
 
	$_POST['action'] = 'edit'; 
} 


if (!empty($_GET['id']) && ($_POST['selStatus']==2 || $_POST['selStatus']==3 )){ 
    $_POST['action'] = 'resendEmail';
}

$rsTOP = $termOfPayment->searchData('','',true, ' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')', ' order by duedays asc');
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrTOP = $class->convertForCombobox($rsTOP,'pkey','name'); 
$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'),'pkey','name');    
$arrTechnician = $class->convertForCombobox($employee->searchData('','',true, ' and ('.$employee->tableName.'.statuskey = 2 ' .$editSalesInactiveCriteria.')'),'pkey','name'); 
$arrShipment = $class->convertForCombobox($shipment->searchData('statuskey',1,true),'pkey','name');
$arrDefaultUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
      
    jQuery(document).ready(function(){    
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?> 
                
         var cashTOP = Array();
         <?php 
            for ($i=0;$i<count($rsTOP);$i++){
                if ($rsTOP[$i]['duedays'] <> 0)
                    echo 'cashTOP.push('.$rsTOP[$i]['pkey'].');'.chr(13);
            }
         ?>

        var varConstant = {  
                            ITEM : <?php echo json_encode(ITEM); ?>,
                            SERVICE : <?php echo json_encode(SERVICE); ?>,  
                        };
        var salesOrderCarService = new SalesOrderCarService(tabID,varConstant,cashTOP); 
        prepareHandler(salesOrderCarService); 
        
        var fieldValidation =  {
                                    code: {
                                            validators: {
                                                notEmpty: {  message: phpErrorMsg.code[1] }, 
                                            }
                                    }
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Tgl. Masuk</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Tgl. Keluar</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDateOut'); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['carRegistrationNumber']); ?></label> 
                                        <div class="col-xs-9">
                                            <div class="flex">
                                                <div class="consume"> 
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
                                                <div >/</div>
                                                <div class="consume"> <?php echo $obj->inputText('vehicleid'); ?></div>
                                            </div> 
                                        </div> 
                                    </div> 
                                    <!--<div class="form-group">
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['capacity']); ?> (CC)</label> 
                                        <div class="col-xs-9">  
                                            <?php echo $obj->inputNumber('capacity', array('readonly' => true)); ?>
                                        </div> 
                                    </div> -->
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mileage']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputNumber('mileage'); ?>
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
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['technician']); ?> 2</label> 
                                            <div class="col-xs-9"> 
                                                <?php  echo $obj->inputAutoComplete(array(  
                                                                                    'objRefer' => $employee,
                                                                                    'element' => array('value' => 'techician2Name',
                                                                                                       'key' => 'hidTechician2Key'),
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
                             </div>
                         
                    </div>
                     <div class="div-table-col"> 
                        <div class="div-tab-panel">    
                               <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['customerInformation']); ?></div> 
                                
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php   echo $obj->inputAutoComplete(array(  
                                                                            'objRefer' => $customer, 
                                                                            'element' => array('value' => 'customerName',
                                                                                                'key' => 'hidCustomerKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-customer.php',
                                                                                                'data' => array(  'action'  =>'searchData' )
                                                                                            ),
                                                                            'callbackFunction' => 'getTabObj().updateCustomerInformation()'

                                                                              )
                                                                        );   ?>
                                </div> 
                            </div>
                                
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?> / <?php echo ucwords($obj->lang['mobilePhone']); ?></label> 
                                <div class="col-xs-4" style="padding-right:0"> 
                                    <?php echo $obj->inputText('phone', array('readonly' => true)); ?>
                                </div>  
                                <div class="col-xs-5" style="padding-left:5px"> 
                                    <?php echo $obj->inputText('mobile', array('readonly' => true)); ?>
                                </div> 
                            </div>    
                                        
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                <div class="col-xs-9">  
                                    <?php echo $obj->inputText('email', array('readonly' => true)); ?>
                                </div> 
                            </div> 
                        </div>
                        <div class="div-tab-panel">    
                               <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div> 
                                <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div> 
                         </div>
                         
                     <!--  <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue">Tujuan Pengiriman</div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label">Nama</label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientName'); ?>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label">Telepon</label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientPhone'); ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label">Email</label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientEmail'); ?>  
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label">Alamat</label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputTextArea('recipientAddress', array('etc' => 'style="height:10em;"')); ?>
                                </div> 
                            </div>  
                        </div>    -->
                           
                    </div>
           </div>
      </div> 
      
        <div class="div-table transaction-detail" style="width:100%; ">
                <div class="div-table-row">  
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemOrService']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:90px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"></div>
                    <div class="div-table-col detail-col-header" style="width:70px; text-align:right;"><?php echo ucwords($obj->lang['discount']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:90px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div> 
                    <!--<div class="div-table-col detail-col-header">Sales</div>-->
                    <div class="div-table-col detail-col-header" style="width:50px; text-align:center;"><?php echo ucwords($obj->lang['tax23']); ?></div>  
<!--                    <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>" style="width: 38px;"></div>-->
                    <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>" ></div>
                </div>
        </div>    
        <div class="div-table  mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
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
                        $etc = '';  
                        $arrUnit = $arrDefaultUnit; 
                        
						if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                            $unitname = 'Pcs';
                        } else {  
                            
                            $decimal = 0;
                            $inputnumber = 'inputnumber';

                            if ($rsSalesDetail[$i]['discounttype']  == 2){ 
                                $decimal = 2;
                                $inputnumber = 'inputdecimal';
                            } 
                            
                            $_POST['hidDetailKey[]'] =  $rsSalesDetail[$i]['pkey']; 
                            $_POST['hidItemKey[]'] =  $rsSalesDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] =  $rsSalesDetail[$i]['itemname']; 
                            $_POST['aliasName[]'] =  $rsSalesDetail[$i]['alias']; 
                            $_POST['detailNote[]'] =  $rsSalesDetail[$i]['description']; 
                            $_POST['isPackage[]'] = $rsSalesDetail[$i]['ispackage']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsSalesDetail[$i]['qty']); 
                            $_POST['priceInUnit[]'] =   $obj->formatNumber($rsSalesDetail[$i]['priceinunit']); 
                            $_POST['selDiscountType[]'] =  $rsSalesDetail[$i]['discounttype'] ; 
                            $_POST['discountValueInUnit[]'] =   $obj->formatNumber($rsSalesDetail[$i]['discount'],$decimal); 
                            $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsSalesDetail[$i]['total']); 
                            
                            $_POST['selMovementType[]'] =  $rsSalesDetail[$i]['movementtype'];
                            
                            $_POST['hidDetailSalesKey[]'] =  $rsSalesDetail[$i]['saleskey']; 
                            $rsEmployee = $employee->getDataRowById($rsSalesDetail[$i]['saleskey']); 
                            $_POST['detailSalesName[]'] = (!empty($rsEmployee)) ? $rsEmployee[0]['name'] : '';
                            
                            $_POST['hidDetailWarehouseKey[]'] =  $rsSalesDetail[$i]['warehousekey'];  
                            $rsWarehouse = $warehouse->getDataRowById($rsSalesDetail[$i]['warehousekey']);
                            $_POST['detailWarehouseName[]'] =  (!empty($rsWarehouse)) ? $rsWarehouse[0]['name'] : '';
                            $_POST['chkIsTax23[]'] = $rsSalesDetail[$i]['istax23'];

                            $_POST['selUnit[]'] =  $rsSalesDetail[$i]['unitkey']; 
                                
                            $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsSalesDetail[$i]['itemkey']),'conversionunitkey','unitname'); 
                        }
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail" style="padding:0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('isPackage[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:80px;"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                                <div class="div-table-col detail-col-detail" style="width:80px;"><?php echo $obj->inputSelect('selUnit[]',$arrUnit, array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                <div class="div-table-col detail-col-detail" style="width:90px;"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                                <div class="div-table-col detail-col-detail" style="width:80px;"><?php echo $obj->inputNumber('discountValueInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                                <div class="div-table-col detail-col-detail" style="width:70px;"><?php echo $obj->inputSelect('selDiscountType[]',$obj->arrDiscountType, array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                <div class="div-table-col detail-col-detail" style="width:90px;">
                                    <?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'readonly' =>true, 'etc' => 'style="text-align:right;" ' .$etc)); ?>
                                </div> 
                                <div class="div-table-col-3 " style="width:50px;text-align:center">
                                            <?php  echo $obj->inputCheckBox('chkIsTax23[]',array( 'etc' => $etc)); ?>
                                </div>
                            </div>
                        </div>  
                        <div class="div-table" style="width:100%;">
                            <div class="div-table-row">
                               
                                  <div class="div-table-col detail-col-detail" style="vertical-align:top;"><?php echo $obj->inputText('aliasName[]',array('overwritePost' => $overwrite, 'etc' =>  $etc.'style="vertical-align: top;" placeholder="'.$obj->lang['alias'].'"')); ?></div> 
                                  <div class="div-table-col detail-col-detail"><?php echo $obj->inputTextArea('detailNote[]',array('overwritePost' => $overwrite, 'etc' => 'style="height:6em" placeholder="'.$obj->lang['note'].'"')); ?></div> 
                                  <!--<div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>" style="vertical-align:top;"></div>-->

                            </div>
                            
                        </div>  
                       <div class="options-row">
                           <div class="panel form-panel-result" style="float:right;">
                            <span class="mnv-opt-warehouse"><span style="font-weight:bold">Gudang</span> <span class="mnv-opt-movement"><span class="mnv-opt-movement-value"></span>, </span><span class="mnv-opt-warehouse-value"></span>.</span> <span class="mnv-opt-sales"><span style="font-weight:bold">Sales</span> <span class="mnv-opt-sales-value"></span>.</span>
                           </div>
                           <div class="panel form-panel" style="display:none; float:right; padding-right:0">
                            <div class="div-table">
                                <div class="div-table-row">
                                    <div class="div-table-col row-header"><?php echo $obj->lang['warehouse']; ?></div>   
                                    <div class="div-table-col" >
                                        <div style="width:80px; float:left"><?php echo $obj->inputSelect('selMovementType[]', $obj->warehouseMovementRules, array('etc' => ' attr-label="mnv-opt-movement" ' )); ?></div> 
                                        <div style="width:150px; float:left; margin-left:0.5em"><?php echo $obj->inputText('detailWarehouseName[]', array('overwritePost' => $overwrite, 'etc' => ' attr-label="mnv-opt-warehouse" ' .$etc)); ?><?php echo $obj->inputHidden('hidDetailWarehouseKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>   
                                    </div>
                                </div> 
                                <div class="div-table-row">
                                    <div class="div-table-col row-header"><?php echo $obj->lang['salesman']; ?></div> 
                                    <div class="div-table-col"><?php echo $obj->inputText('detailSalesName[]', array('overwritePost' => $overwrite, 'etc' => ' attr-label="mnv-opt-sales" ' .$etc)); ?><?php echo $obj->inputHidden('hidDetailSalesKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                                </div>
                            </div>     
                            <div style="clear:both"></div>
                            </div>
                           <div class="panel summary-panel" style="width:200px; float:left"></div>
                        </div> 
                    </div>
<!--                    <div class="div-table-col detail-col-detail icon-col align-top-adjust <?php echo $obj->hideOnDisabled(); ?>"  style="width: 38px;"><?php echo $obj->inputLinkButton('btnMoreOptions' , '<i class="fas fa-ellipsis-h"></i>', array('class' => 'btn btn-link btn-more-options')); ?></div>-->
                    <div class="div-table-col detail-col-detail icon-col align-top-adjust <?php echo $obj->hideOnDisabled(); ?>"><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                </div> 
 
            <?php } ?> 
                   
         </div>        
        
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'],array('class' => 'btn btn-primary btn-second-tone')); ?></div>
        
          <div> 
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

                                <div class="div-table-row form-group payment-detail-row <?php echo $class; ?>">
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
                        
                         <div class="div-table <?php echo $obj->hideOnDisabled(); ?>" style="width:100%;"> 
                            <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" > </div>  
                                <div class="div-table-col-3" style="width:180px;"> 
                                    <div class="btn-pay bg-green-avocado text-white user-select-none" style="cursor:pointer; float:right; display:inlineblock; border-radius:0.2em; padding:0.2em 0.5em;"><?php echo $obj->lang['pay']; ?></div>
                                </div> 
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
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

                             <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3"></div>  
                                <div class="div-table-col-3"> </div>
                            </div>
                            <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;">
                                    <?php echo ucwords($obj->lang['downpayment']); ?> 
                                </div>  
                                <div class="div-table-col-3"> 
                                    <?php echo $obj->inputNumber('downpayment',array ('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>  
                                </div>
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
                                        <div class="consume"> <?php echo $obj->inputNumber('finalDiscount', array ('class'=> 'form-control ' . $finalDiscDecimalType, 'etc' => 'style="text-align:right;"')); ?> </div>
                                     </div> 
                                </div> 
                            </div>
                       <div class="div-table-row  form-group   form-detail-field"> 
                            <div class="div-table-col-3"  style="text-align:right;">
                                 <?php echo ucwords($obj->lang['point']); ?>
                            </div>  
                            <div class="div-table-col-3"> 
                                 <?php echo $obj->inputText('pointValue', array( 'etc' => 'style="text-align:right;" ')); ?>
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

                           <div class="div-table-row  form-group"> 
                                  <div class="div-table-col-3"  style="text-align:right;">
                                    <?php echo strtoupper($obj->lang['PPN']); ?> [Include]
                                 </div>   
                                 <div class="div-table-col-3"> 
                                     <div class="flex">    
                                        <div><?php echo $obj->inputCheckBox('chkIncludeTax'); ?></div>  
                                        <div class="percentage-col"><?php echo $obj->inputDecimal('taxPercentage', array('etc' => 'style="text-align:right;"')); ?></div> 
                                        <div>%</div>
                                        <div class="consume"><?php echo $obj->inputNumber('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                                      </div> 
                                </div> 
                             </div>   

                         <div class="div-table-row  form-group   form-detail-field"> 
                            <div class="div-table-col-3"  style="text-align:right;padding-top:2em;">
                                 <?php echo ucwords($obj->lang['shippingCourier']); ?> 
                            </div>  
                            <div class="div-table-col-3" style=" padding-top:2em;" > 
                                  <?php echo  $obj->inputSelect('selShipment', $arrShipment) ?>
                            </div> 
                        </div>
                           <div class="div-table-row  form-group   form-detail-field"> 
                            <div class="div-table-col-3"  style="text-align:right;">
                                 <?php echo ucwords($obj->lang['insurance']); ?> 
                            </div>  
                            <div class="div-table-col-3" > 
                                  <?php echo  $obj->inputCheckBox('useInsurance', array('etc' => 'style="margin-top:0;"')) ?> 
                            </div> 
                        </div> 

                         <div class="div-table-row  form-group   form-detail-field"> 
                            <div class="div-table-col-3"  style="text-align:right;">
                                <?php echo ucwords($obj->lang['shippingFee']); ?> 
                            </div>  
                            <div class="div-table-col-3" > 
                                <?php echo $obj->inputNumber('shipmentFee', array('etc' => 'style="text-align:right;" ')); ?>
                            </div> 
                        </div>

                         <div class="div-table-row  form-group   form-detail-field"> 
                            <div class="div-table-col-3" style="text-align:right;"> 
                                 <?php echo ucwords($obj->lang['others']); ?> 
                            </div>      
                            <div class="div-table-col-3"> 
                                <?php echo $obj->inputNumber('etcCost', array('etc' => 'style="text-align:right;"')); ?> 
                            </div> 
                        </div>
                       <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3" style="text-align:right;"> 
                                 <?php echo ucwords($obj->lang['total']); ?> 
                            </div>  
                            <div class="div-table-col-3"> 
                                <?php echo $obj->inputNumber('total', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?>  
                            </div> 
                        </div> 
                        <div class="div-table-row  form-group"> 
                                      <div class="div-table-col-5"  style="text-align:right;  padding-top:2em;">
                                        <?php echo ucwords($obj->lang['tax23']); ?>
                                     </div>   
                                     <div class="div-table-col-5" style=" padding-top:2em;">
                                        <div class="flex"> 
                                            <!--<div><?php echo $obj->inputCheckBox('chkTax23'); ?></div>  -->
                                            <div class="percentage-col"><?php echo $obj->inputDecimal('tax23Percentage', array('etc' => 'style="text-align:right;"')); ?></div> 
                                            <div>%</div>
                                            <div class="consume"><?php echo $obj->inputNumber('tax23Value', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                                        </div>
                                    </div> 
                        </div>   
                         <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3" style="text-align:right;"> </div>  
                            <div class="div-table-col-3"> 
                                   <div class="form-detail-button" style="float:right; text-align:right;" relalt="<?php echo ucwords($obj->lang['hideDetail']); ?> "><?php echo ucwords($obj->lang['showDetail']); ?> </div>
                            </div> 
                        </div> 
                  </div>   
                 <div style="clear:both"></div> 
          </div>
         
         
      
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);?>
         <?php 
			/*	if($security->isAdminLogin($obj->securityObject,11,false)) {
					$totalSent =  (isset($rs[0]['invoicesent'])) ? $rs[0]['invoicesent'] : 0; 
					echo $obj->inputSubmit('btnSaveEmail','Simpan & Email ('.$totalSent.')', array('etc' => 'style="margin-left:2em"'));
				}*/
		?> 
        <?php 
            /* if (!empty($_GET['id']) && ($_POST['selStatus']==2 || $_POST['selStatus']==3 ))
                echo  $obj->inputSubmit('btnEmail','Kirim Ulang Email ('.$rs[0]['invoicesent'].')', array ('allowedStatusForEdit' => array(2,3)));  */ 
         ?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>

