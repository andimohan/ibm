<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $itemOutDelivery;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'itemOutDeliveryList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title']; 

$showVendorPartNumber = $item->loadSetting('showVendorPartNumber');

$rsItemOutDetail = array();
$rsPaymentMethodDetail = array();

$_POST['trDate'] = date('d / m / Y');  

$rs = prepareOnLoadData($obj);  
 
if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	$rsItemOutDetail = $obj->getDetailWithRelatedInformation($id);
	  
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
	$_POST['trDesc'] = $rs[0]['trdesc'];
     
    $rsItemOut = $itemOut->getDataRowById($rs[0]['refkey']);
    $_POST['hidItemOutKey'] = $rsItemOut[0]['pkey'];
    $_POST['itemOutCode'] = $rsItemOut[0]['code']; 
    $rsCustomer = $customer->getDataRowById($rsItemOut[0]['customerkey']);
    $_POST['customerName'] = $rsCustomer[0]['name'] ;
    $_POST['reference'] = $rsItemOut[0]['refcode'] ;
    
    $rsSupplier = $supplier->getDataRowById($rs[0]['shippingcourierkey']);
    if(!empty($rsSupplier)){
        $_POST['hidSupplierKey'] = $rs[0]['shippingcourierkey'];
        $_POST['supplierName'] = $rsSupplier[0]['name'] ; 
    }
    $_POST['shippingReceipt'] = $rs[0]['shippingreceipt'] ;
     
}
 
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style> 
    .total-sn-label {font-size: 0.9em; color:#999; font-style: italic}
    .tag-list li {height: 2em; text-align: center;}
    .transaction-detail>.div-table-row:nth-child(2n+3) .tag-list li {background-color: #dedede !important}
    .options-row .form-panel-result {max-height: 10em; overflow: auto}
</style>
<title></title> 
 
<script type="text/javascript">   
	   
	jQuery(document).ready(function(){   
        var tabID = selectedTab.newPanel[0].id;
        var itemOutDelivery = new ItemOutDelivery(tabID,<?php echo json_encode($rs); ?>);
        prepareHandler(itemOutDelivery);   
        
        
        var fieldValidation =  {
             code: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        }, 
                    }
                }, 

               itemOutCode: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.itemOut[1]
                        }, 
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
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
    <?php prepareOnLoadDataForm($obj); ?>     
    <?php echo $obj->inputHidden('hidCustomerKey'); ?>
    
      
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
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemOutCode']); ?></label> 
                            <div class="col-xs-9"> 
                               <?php     
                                        echo $obj->inputAutoComplete(array( 
                                                                            'revalidateField' => true, 
                                                                            'element' => array('value' => 'itemOutCode',
                                                                                               'key' => 'hidItemOutKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-item-out.php',
                                                                                                'data' => array(  'action' =>'searchData','statuskey' => 2, 'isfulldelivered' => '0' )
                                                                                            ) ,  
                                                                            'callbackFunction' => 'getTabObj().importData()'
                                                                          )
                                                                    ); 

                                ?>   
                            </div> 
                        </div>    
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?></label> 
                            <div class="col-xs-9"> 
                               <?php echo $obj->inputText('reference', array('readonly' => true)); ?>
                            </div> 
                        </div>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['recipient']); ?></label> 
                            <div class="col-xs-9"> 
                               <?php echo $obj->inputText('customerName', array('readonly' => true)); ?>
                            </div> 
                        </div>
                        
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shippingCourier']); ?></label> 
                            <div class="col-xs-9"> 
                               <?php  echo $obj->inputAutoComplete(array( 
                                                                        'objRefer' => $supplier, 
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
                                                                                        ))
                                                                );  
                                    ?>
                            </div> 
                        </div> 
                        
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shippingReceipt']); ?></label> 
                            <div class="col-xs-9"> 
                               <?php echo $obj->inputText('shippingReceipt'); ?>
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
       
        <div class="div-table transaction-detail" style="width:100%;"> 
                <div class="div-table-row">  
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['orderedQty']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['outstanding']); ?></div>  
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['deliveredQty']); ?></div>  
                    <div class="div-table-col detail-col-header" style="width:40px;"></div> 
                    <div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>" style="width:35px"></div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?>  icon-col" ></div> 
                </div>
             </div> 
      
            <div class="div-table mnv-transaction  transaction-detail" style="width:100%; border-bottom:1px solid #333; ">       
      
				<?php 
                    $totalRows = count($rsItemOutDetail);
                            
                    for ($i=0;$i<=$totalRows; $i++){  
                        
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';
                        $txtSN = '';
                        $needsn = false;
                        $showOptions = false;
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                            $unitname = 'Pcs';
                            
                        } else {
                            
                            $rsSN = $obj->getSerialNumber($rsItemOutDetail[$i]['pkey']);
                            $arrSN = array_column($rsSN, 'serialnumber');
                               
                            if (!empty($rsSN)){ 
                                $txtSN = "<ul  class=\"tag-list\">";
                                
                                for($j=0;$j<count($rsSN);$j++) 
                                    $txtSN .= '<li>'.$rsSN[$j]['serialnumber'].'</li>';
                           
                                $txtSN .= "</ul>";
                            }
                            
                            
                            $decimal = 0;
                            $inputnumber = 'inputnumber';
 
                            $unitname = $rsItemOutDetail[$i]['baseunitname'];
                            $_POST['hidDetailKey[]'] =  $rsItemOutDetail[$i]['pkey'];
                            $_POST['hidItemOutDetailKey[]'] = $rsItemOutDetail[$i]['refitemoutdetailkey']; 
                            $_POST['hidItemKey[]'] = $rsItemOutDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] = $rsItemOutDetail[$i]['itemname']; 
                            $_POST['hidVendorPartNumberKey[]'] = $rsItemOutDetail[$i]['vendorpartnumberkey'];
                            $_POST['vendorPartNumber[]'] = $rsItemOutDetail[$i]['partnumber'];
                            $_POST['hidNeedSN[]'] = $rsItemOutDetail[$i]['needsn'];
                            $_POST['orderedQtyInBaseUnit[]'] = $obj->formatNumber($rsItemOutDetail[$i]['orderedqtyinbaseunit']);
                            $_POST['qtyMinusInBaseUnit[]'] = $obj->formatNumber($rsItemOutDetail[$i]['qtyminusinbaseunit']); 
                            $_POST['deliveredQtyInBaseUnit[]'] = $obj->formatNumber($rsItemOutDetail[$i]['deliveredqtyinbaseunit']);  
                            $_POST['snList[]'] =  implode(chr(13),$arrSN); 
 
                            $showOptions = (USE_SN && $rsItemOutDetail[$i]['needsn'] == 1) ? true : false;
                        }
                         

                    ?>
            
                <div class="div-table-row  odd-style-adjustment  <?php echo $class; ?>">
                    
                      <div class="div-table-col" style="padding:0">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row">
                                    <div class="div-table-col" style="padding:0">
                                        <div class="div-table" style="width: 100%">
                                            <div class="div-table-row"> 
                                                <div class="div-table-col detail-col-detail">
                                                    <?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite,'readonly' => true,  'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemOutDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                                    <?php echo $obj->inputHidden('hidNeedSN[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                                </div> 
                                                <div class="div-table-col detail-col-detail" style="vertical-align:top;width:100px; "><?php echo $obj->inputNumber('orderedQtyInBaseUnit[]', array('overwritePost' => $overwrite,'readonly' => true,  'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                                                <div class="div-table-col detail-col-detail" style="vertical-align:top;width:100px; "><?php echo $obj->inputNumber('qtyMinusInBaseUnit[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                                                <div class="div-table-col detail-col-detail" style="vertical-align:top;width:100px; "><?php echo $obj->inputNumber('deliveredQtyInBaseUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>  
                                                <div class="div-table-col detail-col-detail" style="vertical-align:top; width:40px; line-height:3em"><div class="text-muted"><span class="baseitemunit"><?php echo $unitname;?></span></div></div>
                                           </div>
                                        </div> 
                                    </div>
                                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>"  style="width:35px;"><?php echo $obj->inputLinkButton('btnMoreOptions' , '<i class="fas fa-ellipsis-h"></i>', array('class' => 'btn btn-link btn-more-options','disabled' => true )); ?></div>
                                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1"','class' => 'btn btn-link remove-button')); ?></div>
                                </div> 
                            </div> 
                              <div class="options-row" style="<?php if  (!$showOptions) { echo 'display:none;'; } ?>">
                                     <div style="<?php if  (!$showOptions) { echo 'display:none;'; } ?>" class="total-sn-label need-sn">Selisih SN : <span class="total-sn-remaining">0</span></div>
                                    <div class="panel form-panel-result" <?php if(!empty($txtSN)) echo 'style="display:block"'; ?> >
                                        <?php echo $txtSN; ?>
                                            <div style="clear:both"></div>
                                    </div>
                                    <div class="panel form-panel" style="display:none; width: 100%">
                                        <div style="font-weight:bold"><?php echo $obj->lang['serialNumber']; ?></div>
                                        <?php echo  $obj->inputTextArea('snList[]', array('overwritePost' => $overwrite, 'etc' => 'attr-label="mnv-opt-sn"  style="height:10em;"')); ?>  
                                    </div>
                                   <div class="panel summary-panel" style="width:200px; float:left"></div>
                              </div>  
                        </div>
                    
                    
                       </div>
         
             <?php } ?> 
         </div>         
         <div style="clear:both; height:1em"></div>           
         
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
