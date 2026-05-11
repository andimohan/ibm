<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Consignee.class.php');
$consignee = createObjAndAddToCol(new Consignee());
$location = createObjAndAddToCol(new Location());

$obj= $consignee;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'consigneeList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$rs = prepareOnLoadData($obj); 
$rsShippingAddress = array();

if (!empty($_GET['id'])){ 
    
    $id = $_GET['id'];
    $rsShippingAddress = $obj->getMultipleAddress($id,1);
	   
	$_POST['name'] = $rs[0]['name']; 
	$_POST['warehouseName'] = $rs[0]['warehousename']; 
	$_POST['address'] = $rs[0]['address']; 
	$_POST['contactPerson'] = $rs[0]['contactperson']; 
    
	$_POST['hidLocationKey'] = $rs[0]['locationkey']; 
	if (!empty($_POST['hidLocationKey'])){
		$rsLocation = $location->getDataRowById($rs[0]['locationkey']);
		$_POST['locationName'] = $rsLocation[0]['name'];
	}
    
	 
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  
<script type="text/javascript">  
	  
    function Consignee(tabID) {   
         /*
         this.updateCity = function updateCity( ){
               var locationkey = $( "#" + tabID + " [name=hidLocationKey]" ).val();  
                       
                if(!locationkey)
                    return;

                $.ajax({
                    type: "GET",
                    url:  'ajax-location.php',
                    async: false,
                    data: "action=getDataRowById&pkey=" + locationkey ,  
                }).done(function( data ) { 

                        data = JSON.parse(data) ; 
                        data = data[0];
 
                        $( "#" + tabID + " [name=hidCityKey]" ).val(data.citykey); 
                        $( "#" + tabID + " [name=cityName]" ).val(data.citycategoryname); 
                });
         }
         */
     }
    
	jQuery(document).ready(function(){  
         
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        consignee = new Consignee(tabID);
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
	
				name: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.consignee[1]
                        },  
                    }
                },  
                
				locationName: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.location[1]
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
                                    <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('warehouseName'); ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputTextArea('address',array('etc'=> 'style="height:10em;"')); ?> 
                                        </div> 
                                    </div>    
                            
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['location']); ?></label> 
                                        <div class="col-xs-9"> 
                                                <?php   
                                                        $popupOpt = (!$isQuickAdd) ? array(
                                                                'url' => 'locationForm.php',
                                                                'element' => array('value' => 'locationName',
                                                                       'key' => 'hidLocationKey'),
                                                                'width' => '600px',
                                                                'title' => $obj->lang['add'] . ' - ' . $obj->lang['location']
                                                            )  : '';
                                    
                                                        echo $obj->inputAutoComplete(array(
                                                                'objRefer' => $location, 
                                                                'revalidateField' => true, 
                                                                'element' => array('value' => 'locationName',
                                                                                   'key' => 'hidLocationKey'),
                                                                'source' =>array(
                                                                                    'url' => 'ajax-location.php',
                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                ) ,
                                                                'popupForm' => $popupOpt,  
                                                              )
                                                        );  
                                            ?>
                                        </div> 
                                    </div>     
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['contactPerson']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('contactPerson'); ?>
                                        </div> 
                                    </div>    
                                    
                           </div>
                    </div>  
                    <div class="div-table-col">  
                        <div class="div-tab-panel">  
                                <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['shippingAddress']); ?></div>
                                <?php echo $obj->multipleAddressPlugin($rsShippingAddress); ?>    
                           </div>
                        
                    </div>
            </div>
      </div>    
      <div class="form-button-panel" > <?php echo $obj->generateSaveButton(); ?>  </div>   
    </form>
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
