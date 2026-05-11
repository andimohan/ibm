<?php 

include '../_config.php'; 
include '../_include.php'; 
 
$obj= $carMaintenanceChecklist;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'carMaintenanceChecklistList'; 
    
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');
$editOilInactiveCriteria = '';

$rs = prepareOnLoadData($obj); 
$rsDetail = array();
if (!empty($_GET['id'])){ 
    $id = $_GET['id'];	
    $rsDetail = $obj->getDetailById($id); 
      
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['hidCarKey'] = $rs[0]['carkey']; 
	if (!empty($_POST['hidCarKey'])){
		$rsCar = $car->searchData($car->tableName.'.pkey',$rs[0]['carkey'], true);
		$_POST['policeNumber'] = $rsCar[0]['policenumber'];
        $_POST['capacity'] = $obj->formatNumber($rsCar[0]['capacity']);
        $_POST['year'] = $rsCar[0]['year'];
        $_POST['carSeriesName'] = $rsCar[0]['seriesname'];
        $_POST['fuelType'] = $rsCar[0]['fueltype']; 
	}
    
    $_POST['mileage'] = $obj->formatNumber($rs[0]['mileage']);
     
    $_POST['acTemperatureBefore'] = $obj->formatNumber($rs[0]['actemperaturebefore'],2); 
    $_POST['acTemperatureAfter'] = $obj->formatNumber($rs[0]['actemperatureafter'],2); 
    $_POST['fogging'] = $obj->formatNumber($rs[0]['fogging']);
    
    $_POST['accuCheck'] = $rs[0]['accucheck']; 
    
    $_POST['oilIn'] = $obj->formatNumber($rs[0]['oilin'],2); 
    $_POST['oilOut'] = $obj->formatNumber($rs[0]['oilout'],2); 
    
    $_POST['accuLife'] = $obj->formatNumber($rs[0]['acculife'],2); 
    $_POST['accuAh'] = $obj->formatNumber($rs[0]['accuah'],2); 
    $_POST['accuResistance'] = $rs[0]['accuresistance']; 
    
    $_POST['oilFilter'] = $rs[0]['oilfilter']; 
    $_POST['airFilter'] = $rs[0]['airfilter']; 
    $_POST['selAc'] = $rs[0]['ackey'];
    $_POST['selTuneUp'] = $rs[0]['tuneupkey'];
    $_POST['selOilType'] = $rs[0]['oiltypekey'];
    $_POST['selTuneUp'] = $rs[0]['tuneupkey'];
    $_POST['oilBrandKey'] = $rs[0]['oilbrandkey'];
    $_POST['selUltimate'] = $rs[0]['ultimatepackagekey'];
    
    
    $_POST['mileageMaintenance'] = $obj->formatNumber($rs[0]['mileagemaintenance']); 
    $_POST['mileageNextDue'] = $obj->formatNumber($rs[0]['mileagenextdue']); 
    
    
	$_POST['hidCustomerKey'] = $rs[0]['customerkey']; 
	if (!empty($_POST['hidCustomerKey'])){
		$rsCustomer = $customer->searchData($customer->tableName.'.pkey',$rs[0]['customerkey'],true);
		$_POST['customerName'] = $rsCustomer[0]['name'];
        $_POST['phoneNumber'] = $rsCustomer[0]['mobile'];
        $_POST['email'] = $rsCustomer[0]['email'];
	}
    
     
    $_POST['trDesc'] = $rs[0]['trdesc']; 
    $_POST['trWorkDesc'] = $rs[0]['trworkdesc'];  
    $_POST['trPartChangeDesc'] = $rs[0]['trpartchangedesc'];  
    $_POST['trSuggestionDesc'] = $rs[0]['trsuggestiondesc']; 
    $editOilInactiveCriteria = ' or '.$oilType->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['oiltypekey']);
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');
 

$arrAc = $obj->convertForCombobox($itemPackage->searchItemByGroupCategory('acPackage'),'pkey','name');  
$arrTuneUp = $obj->convertForCombobox($itemPackage->searchItemByGroupCategory('tuneupPackage'),'pkey','name');   
$arrBBM = $obj->convertForCombobox($service->searchItemByGroupCategory('bbmPackage'),'pkey','name');   

$arrOilType = $class->convertForCombobox($oilType->searchData('','',true, ' and ('.$oilType->tableName.'.statuskey'. $editOilInactiveCriteria.')'),'pkey','name');  

$oilGroupCategory = $item->searchCategoryGroup('oil');
$arrOilBrand = $obj->convertForCombobox($item->getBrandByCategory(array_column($oilGroupCategory,'categorykey')),'pkey','name');
//$arrUltimate = $obj->convertForCombobox($obj->getUltimate(),'pkey','name','Paket Hemat BBM');
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  

<script type="text/javascript"> 
    function CarWorkChecklist(tabID) {
        
     this.updateCarInformation = function updateCarInformation(){
            $("#" + tabID + " [name=year]").val("");
            $("#" + tabID + " [name=carSeriesName]").val("");
            $("#" + tabID + " [name=capacity]").val("");
            $("#" + tabID + " [name=fuelType]").val("");
          
            $("#" + tabID + " [name=hidCustomerKey]").val("");
            $("#" + tabID + " [name=customerName]").val("");
            $("#" + tabID + " [name=phoneNumber]").val("");
            $("#" + tabID + " [name=email]").val(""); 
         
            var carkey = $( "#" + tabID + " [name=hidCarKey]" ).val();
            if(!carkey)
                return; 
         
            $.ajax({
                    type: "GET",
                    url:  'ajax-car.php',
                    async: true,
                    data: "action=getDataRowById&pkey=" + carkey ,  
                }).done(function( data ) { 
                        data = JSON.parse(data) ;  
                        if (data.length != 0){   
                            data = data[0];  
                              
                            $("#" + tabID + " [name=year]").val(data.year);
                            $("#" + tabID + " [name=carSeriesName]").val(data.seriesname);
                            $("#" + tabID + " [name=capacity]").val(data.capacity).blur();
                            $("#" + tabID + " [name=fuelType]").val(data.fueltype);
                            $("#" + tabID + " [name=hidCustomerKey]").val(data.customerkey); 
                            
                            carWorkChecklist.updateCustomerInformation();
                        }  
                });   
         
     }
     
     this.updateCustomerInformation = function updateCustomerInformation(){
            $("#" + tabID + " [name=customerName]").val("");
            $("#" + tabID + " [name=phoneNumber]").val("");
            $("#" + tabID + " [name=email]").val("");
    
            var customerkey = $( "#" + tabID + " [name=hidCustomerKey]" ).val();
          
            if(!customerkey)
                return;
          
            $.ajax({
                    type: "GET",
                    url:  'ajax-customer.php',
                    async: true,
                    data: "action=getDataRowById&pkey=" + customerkey ,  
                }).done(function( data ) { 
                        data = JSON.parse(data) ;  
                        if (data.length != 0){   
                            data = data[0];  
                              
                            $("#" + tabID + " [name=hidCustomerKey]").val(data.pkey); 
                            $("#" + tabID + " [name=customerName]").val(data.name);
                            $("#" + tabID + " [name=phoneNumber]").val(data.mobile);
                            $("#" + tabID + " [name=email]").val(data.email);
                              
                        }  
                });    
     }
     
     this.updateChkBox = function updateChkBox(obj){ 
           
         $objHidden = $(obj).closest("div").find("input:hidden");
         if ($(obj).prop("checked"))
             $objHidden.val(1);
         else
             $objHidden.val(0);
           
    }
    }
    
     
	jQuery(document).ready(function(){   
        
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        carWorkChecklist = new CarWorkChecklist(tabID);
        setOnDocumentReady(tabID); 
     
		 $('#defaultForm-' + tabID )
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
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
                
            }
        })
        .on('success.form.bv', function(e) { 
              <?php echo $obj->submitFormScript(); ?>
        });
        
   
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
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                            <div class="col-xs-9">  
                                                <?php echo $obj->inputDate('trDate'); ?> 
                                            </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['carRegistrationNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                               <?php  
                                                    
                                                    echo $obj->inputAutoComplete(array(  
                                                                                'objRefer' => $car,
                                                                                'element' => array('value' => 'policeNumber',
                                                                                                   'key' => 'hidCarKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-car.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ),
                                                                                                'callbackFunction' =>  'carWorkChecklist.updateCarInformation()'
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
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['capacity']); ?> (CC)</label> 
                                            <div class="col-xs-9">  
                                             <?php echo $obj->inputNumber('capacity', array('readonly' => true)); ?>
                                            </div> 
                                </div> 
                                     
                               <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mileage']); ?></label> 
                                            <div class="col-xs-9"> 
                                                  <?php echo $obj->inputNumber('mileage'); ?>
                                            </div> 
                                </div>  
                                
                                
                        </div> 
                        	<div class="div-tab-panel">    
                               <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['checkingResult']); ?></div>    
                                <div class="col-xs-12 section-title"><?php echo  strtoupper($obj->lang['AC']); ?></div>
                                
                                    <div class="col-xs-4" style="padding-left:5px">
                                        <div class="form-group">   
                                            <label class="col-xs-12 control-label"> <?php echo ucwords($obj->lang['temperature']); ?> <span class="text-muted">(<?php echo ucwords($obj->lang['before']); ?>)</span></label>    
                                            <div class="col-xs-12"><?php echo $obj->inputDecimal('acTemperatureBefore'); ?></div>  
                                        </div>   
                                    </div>
                                   
                                    <div class="col-xs-4" style="padding-left:5px">
                                        <div class="form-group">   
                                            <label class="col-xs-12 control-label"> <?php echo ucwords($obj->lang['temperature']); ?> <span class="text-muted">(<?php echo ucwords($obj->lang['after']); ?>)</span></label>     
                                            <div class="col-xs-12"><?php echo $obj->inputDecimal('acTemperatureAfter'); ?></div>  
                                        </div>   
                                    </div> 
                                    <div class="col-xs-4"  style="padding-left:5px; padding-right:5px">
                                        <div class="form-group">   
                                            <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['fogging']); ?> <span class="text-muted"><?php echo  '('.ucwords($obj->lang['minute']).')'; ?></span></label>    
                                            <div class="col-xs-12"><?php echo $obj->inputNumber('fogging'); ?></div>  
                                        </div>   
                                    </div> 
                                
                                  <div style="clear:both; height: 1em"></div>
                                
                                  
                                <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['accu']); ?></div> 
                                <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['accuCheck']); ?></label> 
                                            <div class="col-xs-9"> 
                                                  <?php echo $obj->inputText('accuCheck'); ?>
                                            </div> 
                                </div> 
                                
                                <div class="col-xs-4" style="padding-left:5px">
                                    <div class="form-group">   
                                        <label class="col-xs-12 control-label"> <?php echo ucwords($obj->lang['life']); ?> <span class="text-muted">(%)</span></label>    
                                        <div class="col-xs-12"><?php echo $obj->inputDecimal('accuLife'); ?></div>  
                                    </div>   
                                </div>

                                <div class="col-xs-4" style="padding-left:5px">
                                    <div class="form-group">   
                                        <label class="col-xs-12 control-label"> <?php echo ucwords($obj->lang['AH']); ?> <span class="text-muted">(Ah)</span></label>     
                                        <div class="col-xs-12"><?php echo $obj->inputDecimal('accuAh'); ?></div>  
                                    </div>   
                                </div> 
                                <div class="col-xs-4"  style="padding-left:5px; padding-right:5px">
                                    <div class="form-group">   
                                        <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['resistance']); ?> <span class="text-muted"><?php echo '(&Omega;)'; ?></span></label>    
                                        <div class="col-xs-12"><?php echo $obj->inputNumber('accuResistance'); ?></div>  
                                    </div>   
                                </div>  
                               
                                <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['oil']); ?></div> 
                                
                                <div class="col-xs-6"  style="padding-left:5px; padding-right:5px">
                                    <div class="form-group">   
                                        <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['typesOfOil']); ?></label>    
                                        <div class="col-xs-12"><?php echo $obj->inputSelect('selOilType',$arrOilType); ?></div>  
                                    </div>   
                                </div>  
                                <div class="col-xs-6"  style="padding-left:5px; padding-right:5px">
                                    <div class="form-group">   
                                        <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['brand']); ?></label>    
                                        <div class="col-xs-12"><?php echo $obj->inputSelect('oilBrandKey',$arrOilBrand); ?></div>  
                                    </div>   
                                </div>   
                                
                                 <div class="col-xs-3"  style="padding-left:5px; padding-right:5px">
                                    <div class="form-group">   
                                        <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['in']); ?></label>    
                                        <div class="col-xs-12"><?php echo $obj->inputNumber('oilIn'); ?></div>  
                                    </div>   
                                </div>  
                                <div class="col-xs-3"  style="padding-left:5px; padding-right:5px">
                                    <div class="form-group">   
                                        <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['out']); ?></label>    
                                        <div class="col-xs-12"><?php echo $obj->inputNumber('oilOut'); ?></div>  
                                    </div>   
                                </div>  
                                 
                                 <div class="col-xs-3"  style="padding-left:5px; padding-right:5px">
                                    <div class="form-group">   
                                        <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['mileageMaintenance']); ?></label>    
                                        <div class="col-xs-12"><?php echo $obj->inputNumber('mileageMaintenance'); ?></div>  
                                    </div>   
                                </div>  
                                <div class="col-xs-3"  style="padding-left:5px; padding-right:5px">
                                    <div class="form-group">   
                                        <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['mileageNextDue']); ?></label>    
                                        <div class="col-xs-12"><?php echo $obj->inputNumber('mileageNextDue'); ?></div>  
                                    </div>   
                                </div>  
                                
                                <div class="col-xs-4"  style="padding-left:5px; padding-right:5px">
                                    <div class="form-group">   
                                        <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['acPackage']); ?></label>    
                                        <div class="col-xs-12"><?php echo $obj->inputSelect('selAc', $arrAc); ?></div>  
                                    </div>   
                                </div>  
                                <div class="col-xs-5"  style="padding-left:5px; padding-right:5px">
                                    <div class="form-group">   
                                        <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['tuneupPackage']); ?></label>    
                                        <div class="col-xs-12"><?php echo $obj->inputSelect('selTuneUp', $arrTuneUp); ?></div>  
                                    </div>   
                                </div>
                                <div class="col-xs-3"  style="padding-left:5px; padding-right:5px">
                                    <div class="form-group">   
                                        <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['bbmPackage']); ?></label>    
                                        <div class="col-xs-12"><?php echo $obj->inputSelect('selUltimate', $arrBBM); ?></div>  
                                    </div>   
                                </div>
                                
                                <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['filter']); ?></div> 
                                <div class="col-xs-6"  style="padding-left:5px; padding-right:5px">
                                    <div class="form-group">   
                                        <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['oilFilter']); ?></label>    
                                        <div class="col-xs-12"> 
                                             <?php
                                                $options = array();
                                                array_push($options,array('label' => ucwords($obj->lang['change']), 'value' => '1' ));
                                                array_push($options,array('label' => ucwords($obj->lang['clean']), 'value' => '2' )); 
                                                echo $obj->inputRadio('oilFilter', array('optionItems' => $options)); 
                                            ?>  
                                         </div>  
                                    </div>   
                                </div> 
                                
                                <div class="col-xs-6"  style="padding-left:5px; padding-right:5px">
                                    <div class="form-group">   
                                        <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['airFilter']); ?></label>    
                                        <div class="col-xs-12"> 
                                             <?php
                                                $options = array();
                                                array_push($options,array('label' => ucwords($obj->lang['change']), 'value' => '1' ));
                                                array_push($options,array('label' => ucwords($obj->lang['clean']), 'value' => '2' )); 
                                                echo $obj->inputRadio('airFilter', array('optionItems' => $options)); 
                                            ?>  
                                         </div>  
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
                                                                                'readonly' => true,
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'hidCustomerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                )
                                                                              )
                                                                        );   ?>
                                            </div> 
                                        </div>
                                
                                <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                            <div class="col-xs-9">  
                                               <?php echo $obj->inputText('phoneNumber', array('readonly' => true)); ?>
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
                                    <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['othersInformation']); ?></div> 
                                        
                                        <div class="form-group"> 
                                            <div class="col-xs-12"> 
                                                <div><?php echo ucwords($obj->lang['customerComplain']); ?></div>
                                                <?php echo  $obj->inputTextArea('trDesc',array('etc' => 'style="height:10em;"' )); ?>
                                            </div> 
                                        </div> 
                                
                                        <div class="form-group"> 
                                            <div class="col-xs-12"> 
                                                <div><?php echo ucwords($obj->lang['workDescription']); ?></div>
                                                <?php echo  $obj->inputTextArea('trWorkDesc',array('etc' => 'style="height:10em;"' )); ?>
                                            </div> 
                                        </div> 
                                    
                                        <div class="form-group"> 
                                            <div class="col-xs-12"> 
                                                <div><?php echo ucwords($obj->lang['partChange']); ?></div>
                                                <?php echo  $obj->inputTextArea('trPartChangeDesc',array('etc' => 'style="height:10em;"' )); ?>
                                            </div> 
                                        </div> 
                                        
                                        <div class="form-group"> 
                                            <div class="col-xs-12"> 
                                                <div><?php echo ucwords($obj->lang['technicianSolutions']); ?></div>
                                                <?php echo  $obj->inputTextArea('trSuggestionDesc',array('etc' => 'style="height:10em;"' )); ?>
                                            </div> 
                                        </div>  
                                         
                            </div>   
                  </div>  
               </div>    
      </div>
      
        <div class="div-table" style="width:100%; ">
        <div class="div-table-row"> 
            <?php $arrGroup = array(1,2); ?>
            <?php for($ctr=0;$ctr<count($arrGroup);$ctr++) {
                $rsGroup = $itemChecklistGroup->getDataRowById($arrGroup[$ctr]);
                $groupkey = $rsGroup[0]['pkey'];
     
                $rsDetailValue = (!empty($id)) ? $obj->getDetailValue($id, $groupkey) : array();
                $arrDetailValue = array_column($rsDetailValue, 'description', 'itemkey'); 
                $arrDetailCheck = array_column($rsDetailValue, 'ischeck', 'itemkey'); 
                $arrDetailReplace = array_column($rsDetailValue, 'isreplace', 'itemkey');
            ?> 
            <div class="div-table-col-5" style="vertical-align:top">
            <div class="div-table transaction-detail" style="width:100%; ">
                <div class="div-table-caption" style="font-weight:bold">  <?php echo strtoupper ($rsGroup[0]['name']); ?> </div> 
                
                <div class="div-table-col" style="width: 160px"></div> 
                <div class="div-table-col" style="text-align:center">C</div> 
                <div class="div-table-col" style="text-align:center">R</div>
                <div class="div-table-col"><?php echo $obj->lang['note']; ?></div>


				<?php 
             
                    $rsCheckDetail = $itemChecklistGroup->getDetailById($groupkey);  
                    $totalRows = count($rsCheckDetail);
            
                    $class =  'transaction-detail-row';
                    $overwrite = true;
                    $readonly = false;
                    $etc = '';
    
                    for ($i=0;$i<$totalRows; $i++){  
                        $itemkey = $rsCheckDetail[$i]['itemkey'];
                        
                        $rsItemlist = $itemChecklist->getDataRowById($itemkey);
                        $_POST['description_'.$groupkey.'[]'] =  (isset($arrDetailValue[$itemkey]) && !empty($arrDetailValue[$itemkey])) ? $arrDetailValue[$itemkey] : ''; 
                        $_POST['chkIsCheck_'.$groupkey.'[]'] =  (isset($arrDetailCheck[$itemkey]) && !empty($arrDetailCheck[$itemkey])) ? $arrDetailCheck[$itemkey] : ''; 
                        $_POST['hidChkIsCheck_'.$groupkey.'[]'] = (isset($arrDetailCheck[$itemkey]) && !empty($arrDetailCheck[$itemkey])) ? $arrDetailCheck[$itemkey] : 0; 
                        $_POST['chkIsReplace_'.$groupkey.'[]'] =  (isset($arrDetailReplace[$itemkey]) && !empty($arrDetailReplace[$itemkey])) ? $arrDetailReplace[$itemkey] : ''; 
                        $_POST['hidChkIsReplace_'.$groupkey.'[]'] =  (isset($arrDetailReplace[$itemkey]) && !empty($arrDetailReplace[$itemkey])) ? $arrDetailReplace[$itemkey] : 0; 
                  ?>
                
                <div class="div-table-row <?php echo $class; ?>"> 
                    <?php
                        $rsItemlist = $itemChecklist->getDataRowById($itemkey);
                        $_POST['hidItemKey_'.$groupkey.'[]'] =  $itemkey; 
                        $_POST['itemName_'.$groupkey.'[]'] =  $rsItemlist[0]['name'];   
                    ?>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemName_'.$groupkey.'[]', array('readonly' => true)); echo $obj->inputHidden('hidItemKey_'.$groupkey.'[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail" style="text-align:center"><?php echo $obj->inputCheckBox('chkIsCheck_'.$groupkey.'[]', array('overwritePost' => $overwrite, 'etc' => $etc . ' onChange="carWorkChecklist.updateChkBox(this)"')); ?> <?php echo $obj->inputHidden('hidChkIsCheck_'.$groupkey.'[]'); ?></div>
                    <div class="div-table-col detail-col-detail" style="text-align:center"><?php echo $obj->inputCheckBox('chkIsReplace_'.$groupkey.'[]', array('overwritePost' => $overwrite, 'etc' => $etc. ' onChange="carWorkChecklist.updateChkBox(this)"')); ?><?php echo $obj->inputHidden('hidChkIsReplace_'.$groupkey.'[]'); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('description_'.$groupkey.'[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                    
                </div> 
             
                <?php } ?> 
            </div>   
            </div>
            <?php } ?>
            </div>        
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
