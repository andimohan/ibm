<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $salesOrderDumperInvoice;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'salesOrderDumperInvoiceList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
$editJobDetailsInactiveCriteria = ''; 
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';

$rsSalesDetail = array();
$rsPaymentMethodDetail = array();
$arrPaymentMethod = array();

$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber'; 
$_POST['trDate'] = date('d / m / Y');
$_POST['trStartDate'] = date('d / m / Y');
$_POST['trEndDate'] = date('d / m / Y'); 

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id); 
	$rsSalesDetail = $obj->getDetailWithRelatedInformation($id);
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['trStartDate'] = $obj->formatDBDate($rs[0]['startdateperiod'],'d / m / Y');
	$_POST['trEndDate'] = $obj->formatDBDate($rs[0]['enddateperiod'],'d / m / Y');
	
    $_POST['selWarehouseKey'] = $rs[0]['warehousekey']; 
    $_POST['hidProjectKey'] = $rs[0]['refkey']; 
    $rsProject = $projectDumper->getDataRowById($rs[0]['refkey']);
    if(!empty($rsProject[0]['pkey'])){
        $_POST['projectName'] = $rsProject[0]['code'].'-'.$rsProject[0]['name'];
    }
     
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'];
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'];

	$_POST['trDesc'] = $rs[0]['trdesc'];
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']);  

	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
	$_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']);
    $_POST['chkIncludeTax'] = $rs[0]['ispriceincludetax'];
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']);
    
    if ($rs[0]['finaldiscounttype']  == 2){ 
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    } 
    $_POST['selTermOfPayment'] =  $rs[0]['termofpaymentkey'];  
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance']) ;
    $_POST['totalPayment'] = $obj->formatNumber($rs[0]['totalpayment']);    
	$_POST['selFinalDiscountType'] = $rs[0]['finaldiscounttype'] ;
	$_POST['finalDiscount'] = $obj->formatNumber($rs[0]['finaldiscount'],$finalDiscDecimal);
    
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
    $editJobDetailsInactiveCriteria = ' or  '.$jobDetails->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['jobdetailskey']);  
    $editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
    $editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
    //$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'),'pkey','name');    

}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
//$arrJobDetails = $obj->convertForCombobox($jobDetails->searchData('','',true,' and ('.$jobDetails->tableName.'.statuskey = 1' .$editJobDetailsInactiveCriteria.')',' order by '.$jobDetails->tableName.'.name asc'),'pkey','name');  
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrDefaultUnit = $obj->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 
$arrMedia = $class->convertForCombobox($media->searchData ('','',true,' and ('.$media->tableName.'.statuskey = 1 )'),'pkey','name');    
$rsTOP = $termOfPayment->searchData('','',true, ' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')', ' order by duedays asc');
$arrTOP = $obj->convertForCombobox($rsTOP,'pkey','name');
$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'),'pkey','name');    

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
        
         var salesOrderDumperInvoice = new SalesOrderDumperInvoice(tabID, <?php echo json_encode($rs); ?> ,cashTOP);
    
         prepareHandler(salesOrderDumperInvoice);
        
        var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 

                                   customerName: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.customer[1]
                                            }
                                        } 
                                    },
                                   /*locationName: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.location[1]
                                            }
                                        } 
                                    },*/

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
      
       <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col"> 
      						 <div class="div-tab-panel"> 
                                   <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div><?php echo $obj->inputDate('trStartDate'); ?></div>
                                                <div class="consume"><?php echo $obj->inputDate('trEndDate'); ?></div>  
                                            </div> 
                                        </div> 
                                   </div>
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['project']); ?></label>  
                                        <div class="col-xs-9"> 
                                         <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $projectDumper,
                                                                                'element' => array('value' => 'projectName',
                                                                                                   'key' => 'hidProjectKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-project-dumper.php',
                                                                                                    'data' => array(  'action' =>'searchData', 'statuskey' => "(2)", 'searchField' => 'code,name')
                                                                                ),
                                                                                'callbackFunction' => 'getTabObj().updateProjectInformation()'   
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array(
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'hidCustomerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData', 
                                                                                                                              'statuskey' => 2  )
                                                                                ),
                                                                                'readonly' => true                                                                         
                                                                                )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>  
                                 
                             </div>
                         
                    </div>
                     <div class="div-table-col">   
                             <div class="div-tab-panel"> 
                              <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div> 
                              <div class="form-group">
                                    <div class="col-xs-12"> 
                                        <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>                                        
  
                                    </div> 
                                </div>
                            </div>   
                    </div>
           </div>
      </div>   
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col" style="padding:0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['location']); ?></div>
                                <div class="div-table-col detail-col-header" style="width:100px;text-align:right;"><?php echo ucwords($obj->lang['ritase']); ?></div>
                                <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['distance']); ?> <span class="text-muted">(KM)</span></div>
                                <div class="div-table-col detail-col-header" style="width:100px;text-align:right;"><?php echo ucwords($obj->lang['weight']); ?></div>
                                <!--<div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> @</div>-->
                                <div class="div-table-col detail-col-header" style="width:180px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div>
                                <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:30px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
				<?php 
                    $totalRows = count($rsSalesDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  
							
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false; 
                        $arrUnit = $arrDefaultUnit;

                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true; 
                            $unitname = 'Pcs';
                        } else {  
                            $decimal = 0;
                            $inputnumber = 'inputnumber';
                            
                            $_POST['hidDetailKey[]'] =  $rsSalesDetail[$i]['pkey'];
                            $_POST['hidRefSOKey[]'] =  $rsSalesDetail[$i]['refsokey']; 
                            $_POST['hidItemKey[]'] =  $rsSalesDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] =  $rsSalesDetail[$i]['locationname']; 
                            //$_POST['hidGramasi[]'] =   $obj->formatNumber($rsSalesDetail[$i]['gramasi']); 
                            //$_POST['hidGramasiSubtotal[]'] =   $obj->formatNumber($rsSalesDetail[$i]['gramasi'] * $rsSalesDetail[$i]['qtyinbaseunit']); 
                            //$_POST['qty[]'] =   $obj->formatNumber($rsSalesDetail[$i]['qty']); 
                            //$_POST['priceInUnit[]'] =   $obj->formatNumber($rsSalesDetail[$i]['priceinunit']); 
                            //$_POST['selUnit[]'] =  $rsSalesDetail[$i]['unitkey']; 
                            $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsSalesDetail[$i]['total']);
                            $_POST['ritase[]'] =   $obj->formatNumber($rsSalesDetail[$i]['ritase']);
                            $_POST['distance[]'] =   $obj->formatNumber($rsSalesDetail[$i]['distance']);
                            $_POST['weight[]'] =   $obj->formatNumber($rsSalesDetail[$i]['weight']);
                            $_POST['description[]'] =  $rsSalesDetail[$i]['description']; 
                            $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsSalesDetail[$i]['itemkey']),'conversionunitkey','unitname','',array('relconversionmultiplier' => 'conversionmultiplier')); 
                 
                        } 
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col" >
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-detail">
                                    <?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite,'readonly'=>true, 'disabled' => $disabled)); ?>
                                    <?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                                    <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                    <?php echo $obj->inputHidden('hidRefSOKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                </div> 
                                <div class="div-table-col detail-col-detail" style="width:100px; text-align:right;"><?php echo $obj->inputNumber('ritase[]', array('overwritePost' => $overwrite, 'readonly'=>true,'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
                                <div class="div-table-col detail-col-detail" style="width:100px; text-align:right;"><?php echo $obj->inputNumber('distance[]', array('overwritePost' => $overwrite, 'readonly'=>true,'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
                                <div class="div-table-col detail-col-detail" style="width:100px; text-align:right;"><?php echo $obj->inputNumber('weight[]', array('overwritePost' => $overwrite, 'readonly'=>true,'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
                                <!--<div class="div-table-col detail-col-detail" style="width:100px; text-align:right;"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' , 'disabled' => $disabled)); ?></div>-->
                                <div class="div-table-col detail-col-detail" style="width:180px; text-align:right;"><?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'readonly'=>true, 'etc' => 'style="text-align:right;" ' , 'disabled' => $disabled)); ?></div>
                                <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>" style="width:30px;"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                            </div>
                        </div>
                        
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-detail"><?php echo $obj->inputTextArea('description[]',array('overwritePost' => $overwrite, 'etc' => 'placeholder="'.$obj->lang['note'].'"')); ?></div> 
                                <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>" style="width:30px;vertical-align:top;"></div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                 
            <?php } ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
          <div> 
              <div style="width:300px; margin-left:2em; float:right;"> 
               <!-- <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:50px; height: 1em"></div>-->
                <div class="div-table" style="width:100%" >
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['payment']); ?> 
                        </div>  
                        <div class="div-table-col-3" style="width:180px;"> 
                             <?php echo  $obj->inputSelect('selTermOfPayment', $arrTOP); ?>
                        </div> 
                        <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                    </div> 
                 </div>    
                   <!--<div class="mnv-total-group mnv-downpayment">  
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['downpayment']; ?>
                                    </div>  
                                    <div class="div-table-col-3"  style="width:180px"> 
                                            <?php echo $obj->inputCollapsibleNumber('totalDownpayment', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>
                         
                        <div class="mnv-total-group-detail ">
                        <div class="div-table transaction-detail" style="width: 100%">
                            <?php  
                                $totalRows = count($rsInvoiceDP);
                                for($i=0;$i<=$totalRows;$i++) {
                                        $class =  'transaction-detail-row';
                                        $overwrite = true; 
                                        $disabled = false; 

                                        if ($i == $totalRows ){
                                            $class = 'downpayment-row-template row-template'; 
                                            $overwrite = false; 
                                            $disabled = true; 
                                        } else {   
                                            $_POST['hidDetailDownpaymentKey[]'] = $rsInvoiceDP[$i]['pkey'];
                                            $_POST['hidDownpaymentKey[]'] = $rsInvoiceDP[$i]['downpaymentkey'];
                                            $_POST['downpaymentCode[]'] = $rsInvoiceDP[$i]['refcode'];
                                            $_POST['downpaymentAmount[]'] = $obj->formatNumber($rsInvoiceDP[$i]['amount']); 
                                        }
                            ?> 

                            <div class="div-table-row form-group <?php echo $class; ?>">
                                <div class="div-table-col-3" style="text-align:right;">  
                                        <?php echo $obj->inputHidden('hidDetailDownpaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                        <?php echo $obj->inputHidden('hidDownpaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?> 
                                        <?php echo  $obj->inputText('downpaymentCode[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                </div>  
                                <div class="div-table-col-3" style="width:180px"> 
                                       <?php echo $obj->inputNumber('downpaymentAmount[]', array('overwritePost' => $overwrite, 'class'=>'form-control inputnumber mnv-detail-field', 'disabled' => $disabled, 'etc' => 'style="text-align:right;"')); ?>
                                </div>  
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                    <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>',array('etc' => 'tabIndex="-1"', 'class' =>'btn btn-link remove-button' )); ?>
                                </div>
                            </div> 

                            <?php } ?> 

                            <div class="div-table-row form-group ">
                                <div class="div-table-col-3"></div>  
                                <div class="div-table-col-3"><div class="form-detail-button mnv-total-group-hide-detail" style="float:right; text-align:right;" ><?php echo ucwords($obj->lang['hideDetail']); ?></div> </div>   
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?>"></div>
                            </div>  
                            <div class="div-table-row form-group ">
                                <div class="div-table-col-3 " style="height:1em"></div> <div class="div-table-col-3 "></div> <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> "></div>
                            </div>  
                          
                       </div>   
                        </div>
                    </div> -->
                    <div class="mnv-total-group mnv-payment-method cashTOP" >  
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
                        <div class="div-table transaction-detail" style="width: 100%">
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
                                       <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'class'=>'form-control inputnumber mnv-detail-field', 'etc' => 'style="text-align:right;"')); ?>
                                </div>  
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                    <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>',array('etc' => 'tabIndex="-1"', 'class' =>'btn btn-link remove-button' )); ?>
                                </div>
                            </div> 

                            <?php } ?> 

                            <div class="div-table-row form-group ">
                                <div class="div-table-col-3"></div>   
                                <div class="div-table-col-3">
                                    <div class="form-detail-button mnv-total-group-hide-detail" style="float:right; text-align:right;" ><?php echo ucwords($obj->lang['hideDetail']); ?> </div> 
                                </div>
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?>"></div>
                            </div>  
                            <div class="div-table-row form-group ">
                                <div class="div-table-col-3 " style="height:1em"></div> <div class="div-table-col-3 "></div> <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> "></div>
                            </div>  
                          
                       </div>   
                        </div>
                    </div>  
                    <div class="div-table" style="width:100%;"> 
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
                <div class="div-table" style="float:right;">
                                 <div class="div-table-row  form-group not-downpayment-field" style="<?php echo $notDownpaymentField; ?>"> 
                                    <div class="div-table-col-5" style="text-align:right;">
                                        <?php echo ucwords($obj->lang['subtotal']); ?> 
                                    </div>  
                                    <div class="div-table-col-5" style="width:200px;"> 
                                        <?php echo $obj->inputNumber('subtotal', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                                    </div>

                                </div>

                                 <div class="div-table-row  form-group not-downpayment-field" style="<?php echo $notDownpaymentField; ?>"> 
                                    <div class="div-table-col-5"  style="text-align:right;">
                                         <?php echo ucwords($obj->lang['discount']); ?>
                                    </div>  
                                    <div class="div-table-col-5"> 
                                        <div class="flex">          
                                            <div><?php echo $obj->inputSelect('selFinalDiscountType',$obj->arrDiscountType); ?> </div>
                                            <div class="consume"> <?php echo $obj->inputNumber('finalDiscount', array ('class'=> 'form-control ' . $finalDiscDecimalType, 'etc' => 'style="text-align:right;"')) ;?> </div>
                                         </div> 
                                    </div> 
                                </div>
                                 <div class="div-table-row  form-group not-downpayment-field" style="<?php echo $notDownpaymentField; ?>"> 
                                    <div class="div-table-col-5"></div>  
                                    <div class="div-table-col-5"></div> 
                                </div>


                                 <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-5" style="text-align:right;">
                                       <?php echo ucwords($obj->lang['beforeTax']); ?>
                                    </div>  
                                    <div class="div-table-col-5" style="width:200px;"> 
                                         <?php echo $obj->inputNumber('beforeTaxTotal', array( 'readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                                    </div>

                                </div>
                                 <div class="div-table-row  form-group"> 
                                      <div class="div-table-col-5"  style="text-align:right;">                                        
                                          <?php echo strtoupper($obj->lang['PPN']); ?> [Include]
                                     </div>   
                                     <div class="div-table-col-5"> 
                                         <div class="flex">    
                                            <div><?php echo $obj->inputCheckBox('chkIncludeTax'); ?></div>  
                                            <div class="percentage-col"><?php echo $obj->inputDecimal('taxPercentage', array('etc' => 'style="text-align:right;"')); ?></div> 
                                            <div>%</div>
                                            <div class="consume"><?php echo $obj->inputNumber('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                                          </div> 
                                    </div> 
                                 </div>    
                    
                                <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-5" style="text-align:right;">
                                        <?php echo ucwords($obj->lang['total']); ?> 
                                    </div>  
                                    <div class="div-table-col-5" > 
                                         <?php echo  $obj->inputNumber('total', array('readonly' => true, 'etc' =>'style="text-align:right"')); ?>
                                    </div> 
                                </div>
 

                                <!--<div class="div-table-row  form-group"> 
                                      <div class="div-table-col-5"  style="text-align:right;  padding-top:2em;">
                                        <?php echo ucwords($obj->lang['tax23']); ?>
                                     </div>   
                                     <div class="div-table-col-5" style=" padding-top:2em;">
                                        <div class="flex"> 
                                            <div class="percentage-col"><?php echo $obj->inputDecimal('tax23Percentage', array('etc' => 'style="text-align:right;"')); ?></div> 
                                            <div>%</div>
                                            <div class="consume"><?php echo $obj->inputNumber('tax23Value', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                                        </div>
                                    </div> 
                                 </div>-->  

                          </div>   
        
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
