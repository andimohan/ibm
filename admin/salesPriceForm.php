<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('SalesPrice.class.php', 'Customer.class.php', 'Item.class.php'));
$salesPrice = createObjAndAddToCol( new SalesPrice() );
$customer = createObjAndAddToCol(new Customer());

$obj= $salesPrice;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    
$formAction = 'salesPriceList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$rsDetail = array();
$rs = prepareOnLoadData($obj);

$editWarehouseInactiveCriteria = '';

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	
    if(!empty($rs[0]['customerkey'])) {
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
        $_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'];
        $_POST['customerName'] = $rsCustomer[0]['name'];
    }
    $_POST['selType'] = PRICE_TYPE['selling'];
    $_POST['trDesc'] = $rs[0]['trnote'];
} 

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){   
 
        	   
	    var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>   
        
       
        var salesPrice = new SalesPrice(tabID); 
         
    
        prepareHandler(salesPrice);  
        
         var fieldValidation =  {
                                    code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 		
                                   supplierName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.supplier[1]
                                            }, 
                                        }
                                    },
                                } ; 
 
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
						
                    
                        <div class="form-group iscustomer">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                            <div class="col-xs-9"> 
                                  <?php  echo $obj->inputAutoComplete(array( 
                                                                    'objRefer' => $customer,
                                                                    'revalidateField' => true,
                                                                    'element' => array('value' => 'customerName',
                                                                                'key' => 'hidCustomerKey'),
                                                                    'source' =>array(
                                                                                        'url' => 'ajax-customer.php',
                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                    ) ,
                                                                    'popupForm' => array(
                                                                                        'url' => 'customerForm.php',
                                                                                        'element' => array('value' => 'customerName',
                                                                                               'key' => 'hidCustomerKey'),
                                                                                        'width' => '1000px',
                                                                                        'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['customer'])
                                                                                    )
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
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div> 
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>" style="width:45px"></div>
                </div>
                
				<?php 
                    $totalRows = count($rsDetail);
                    for ($i=0;$i<=$totalRows; $i++){  
                        
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                        } else {  
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                            $_POST['hidItemKey[]'] =  $rsDetail[$i]['itemkey'];
                            $_POST['itemName[]'] =  $rsDetail[$i]['itemname']; 
                            $_POST['price[]'] =   $obj->formatNumber($rsDetail[$i]['price']);  
                        }
                ?>
                    
                    <div class="div-table-row <?php echo $class; ?>"> 
                        <div class="div-table-col detail-col-detail">
                            <?php 
                                echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' => $etc));  
                                echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc));          
                            ?> 
                            <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                        </div> 
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('price[]',array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right" ' .$etc)); ?></div> 
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" ')); ?></div>
                     </div>
                <?php } ?>  
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
        
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
