<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $itemPackage;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    

$formAction = 'itemPackageList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$itemPackageDetail = array();
/*$rsItemDescription = array();  */

$rs = prepareOnLoadData($obj); 

$allowChangeUnit = '';
$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';
 
if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
  
	$itemPackageDetail = $obj->getDetailWithRelatedInformation($id);
	 
	$_POST['name'] = $rs[0]['name']; 
 	$_POST['sellingPrice'] = $obj->formatNumber($rs[0]['sellingprice']);
	$_POST['shortdescription'] = $rs[0]['shortdescription'];
 
    /*$rsItemDescription = $obj->getItemDescription($id);	*/
    
    if ($rs[0]['commissiontype']  == 2){ 
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    } 
    
    $_POST['selCommissionType'] = $rs[0]['commissiontype'] ;
	$_POST['commissionValue'] = $obj->formatNumber($rs[0]['commission'],$finalDiscDecimal);
 
    $_POST['hidCategoryKey'] = $rs[0]['categorykey']; 
    if (!empty($rs[0]['categorykey'])){
		$rsCategory = $itemCategory->getDataRowById($rs[0]['categorykey']);
        $categoryName =  $itemCategory->getPath($rsCategory[0]['pkey']);
        $categoryName =  $itemCategory->getPath($rsCategory[0]['pkey']);
		$_POST['categoryName'] = $categoryName[0]['path'];
	}
     
	   
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrDefaultUnit = $obj->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 

  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  

<script type="text/javascript">  
    jQuery(document).ready(function(){    
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?> 
                 
        var itemPackage = new ItemPackage(tabID); 
        prepareHandler(itemPackage); 
        
        var fieldValidation =  {
                                     code: { 
                                            validators: {
                                                notEmpty: {
                                                    message: phpErrorMsg.code[1]
                                                }, 
                                            }
                                        },  

                                        name: { 
                                            validators: {
                                                notEmpty: {
                                                    message: phpErrorMsg.name[1]
                                                },  
                                            }
                                        }, 

                                        categoryName: { 
                                            validators: {
                                                notEmpty: {
                                                    message: phpErrorMsg.category[1]
                                                },  
                                            }
                                        }, 
                                        sellingPrice: {
                                            validators: { 
                                                greaterThan: {
                                                    value: -1,
                                                    inclusive: false,
                                                    separator: ',', 
                                                    message: phpErrorMsg.sellingPrice[2]
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['packageName']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                    </div> 
                                      <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                             <div class="col-xs-9">  
                                               <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                    'objRefer' => $itemCategory,
                                                                                    'revalidateField' => true, 
                                                                                    'element' => array('value' => 'categoryName',
                                                                                                       'key' => 'hidCategoryKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-item-category.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    ) ,
                                                                                    'popupForm' => array(
                                                                                                            'url' => 'itemCategoryForm.php',
                                                                                                            'element' => array('value' => 'categoryName',
                                                                                                                   'key' => 'hidCategoryKey'),
                                                                                                            'width' => '600px',
                                                                                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['serviceCategory'])
                                                                                                        )
                                                                                  )
                                                                            );  
                                                ?> 
                                            </div> 
                                        </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['sellingPrice']); ?></label> 
                                        <div class="col-xs-9"> 
                                                 <?php echo $obj->inputNumber('sellingPrice'); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['commission']); ?></label> 
                                        <div class="col-xs-3"> 
                                               <?php echo  $obj->inputSelect('selCommissionType', $obj->arrDiscountType,   array( 'etc' => 'onChange="itemPackage.updateCommissionDecimal(this);"')); ?>
                                        </div> 
                                        <div class="col-xs-6" style="padding-left:0"> 
                                            <?php echo $obj->inputNumber('commissionValue', array ('class'=> 'form-control ' . $finalDiscDecimalType)); ?>    
                                        </div>
                                    </div> 
                                       
                           </div>  
                    </div>  
                    <div class="div-table-col">  
                                <div class="div-tab-panel transaction-detail" style="margin-bottom:3em; "> 
                          		<div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['description']); ?></div> 
                                <div class="form-group"> 
                                    <div class="col-xs-12"> 
                                           <?php echo  $obj->inputTextArea('shortdescription',array('etc'=>'style="height:12em;"')); ?>
                                    </div> 
                                </div>  
                     </div>    
                </div>
             </div>   
       </div>
       
      
      
        <div class="div-table mnv-transaction  transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> @</div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right; padding-right:0;"></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right; padding-left:0.2em;"><?php echo ucwords($obj->lang['discount']); ?> @</div>
                    <div class="div-table-col detail-col-header" style="width:180px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?>"  style="width:45px"></div>
                </div>
                
				<?php 
                    $totalRows = count($itemPackageDetail); 
            
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

                            if ($itemPackageDetail[$i]['discounttype']  == 2){ 
                                $decimal = 2;
                                $inputnumber = 'inputdecimal';
                            } 

                            $_POST['hidItemKey[]'] =  $itemPackageDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] =  $itemPackageDetail[$i]['itemname']; 
                            $_POST['qty[]'] =   $obj->formatNumber($itemPackageDetail[$i]['qty']); 
                            $_POST['priceInUnit[]'] =   $obj->formatNumber($itemPackageDetail[$i]['priceinunit']); 
                            $_POST['selDiscountType[]'] =  $itemPackageDetail[$i]['discounttype'] ; 
                            $_POST['discountValueInUnit[]'] =   $obj->formatNumber($itemPackageDetail[$i]['discount'],$decimal); 
                            $_POST['detailSubtotal[]'] =   $obj->formatNumber($itemPackageDetail[$i]['total']); 

                            $_POST['selUnit[]'] =  $itemPackageDetail[$i]['unitkey']; 
                            
                            // diopen dulu sementara karena DOMO byk data yg salah satuan unitnya
                            //$arrUnit = array(); 
                            //$arrUnit[$itemPackageDetail[$i]['unitkey']] = $itemPackageDetail[$i]['unitname'];
                        } 
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite,'value' => 1, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selUnit[]',$arrUnit, array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite,'readonly' => true, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('discountValueInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selDiscountType[]',$obj->arrDiscountType, array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                </div>
                 
            <?php } ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
       
          <div> 
              <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:45px; height: 1em"></div> 
              <div class="div-table" style="float:right;"> 
                   <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;"> 
                             <?php echo ucwords($obj->lang['total']); ?> 
                        </div>  
                        <div class="div-table-col-5" style="width:180px;"> 
                            <?php echo $obj->inputNumber('total', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?>  
                        </div> 
                    </div>  
              </div>     
              <div style="clear:both"></div> 
        </div>
          
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div>  
    </form>  
     <?php echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
