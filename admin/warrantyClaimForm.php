<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $warrantyClaim;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'warrantyClaimList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$editWarehouseInactiveCriteria = '';  
$datediff = '';

$_POST['trDate'] = date('d / m / Y');   
 
$rs = prepareOnLoadData($obj);  
$rsDetail = array();
$rsContent = array();
if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
	 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['selWarehouse'] =$rs[0]['warehousekey']; 
    $_POST['customerPhone'] = $rs[0]['customerphone'] ;
    $_POST['customerEmail'] = $rs[0]['customeremail'] ;
	$_POST['hidCustomerKey'] = $rs[0]['customerkey'] ;   
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    if (!empty($rsCustomer)){
        $_POST['customerName'] = $rsCustomer[0]['name'] ;
        $_POST['customerAddress'] = $rsCustomer[0]['address'] ; 
    }
    
    $_POST['trDesc'] = $rs[0]['trdesc'];
   
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
 
}
 
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and isrma = 1 and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 
$arrUnit = $obj->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 
$arrIssue = $obj->convertForCombobox($issueCategory->searchData('','',true, ' and ('.$issueCategory->tableName.'.statuskey = 1 )'),'pkey','name'); 
$arrClaim  = $obj->convertForCombobox($obj->getClaimResult(),'pkey','name');

$arrProgressStatus =  array_column($obj->getAllStatus(),null,'pkey');
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    
<style> 
    .row-number {vertical-align: top; text-align: right; padding-top: 1.4em !important; width: 45px;}
</style>  
<title></title> 
 
<script type="text/javascript">  
 
	jQuery(document).ready(function(){  
        var tabID = selectedTab.newPanel[0].id;
        warrantyClaim = new WarrantyClaim(tabID); 
          
         prepareHandler(warrantyClaim);   
        
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
             
                                    customerPhone: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.phone[1]
                                            }
                                        } 
                                    },
             
                                    customerEmail: { 
                                        validators: { 
                                            emailAddress: {
                                                message:  phpErrorMsg.email[3]
                                            }
                                        } 
                                    },
              
                                   itemName: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.item[1]
                                            }
                                        } 
                                    },

                                   issueCategoryName: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.warrantyClaim[1]
                                            }
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
                                            <?php echo  $obj->inputSelect('selWarehouse', $arrWarehouse); ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo  $obj->inputTextArea('trDesc',array('etc' => 'style="height:10em;"' )); ?>
                                        </div> 
                                    </div>    
                                  
                             </div>
                         
                    </div>
                    <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['customer']); ?></div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
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
                                                                                                ),
                                                                                'callbackFunction' => 'getTabObj().updateCustomerInformation()'
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>         
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('customerPhone', array('readonly' => true)); ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('customerEmail', array('readonly' => true)); ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('customerAddress', array('etc' => 'disabled="disabled" style="height:10em;"')); ?> 
                                        </div> 
                                    </div>  
                                  
                        </div> 
                    </div>
           </div>
      </div> 
     
      
      <div class="div-table mnv-transaction transaction-detail no-odd-even-style" style="width:100%;" attr-level="0">
        
            <?php 
                    $totalRows = count($rsDetail);
                    $item = new Item();
            
                    for ($i=0;$i<=$totalRows; $i++){  
                        
                        $class =  'transaction-detail-row'; 
                        $overwrite = true;
                        $disabled = false;
                        $detailRowsToken = '';
                        $rsItemContent = array();
                        $rsIssue = array();
                        $totalContentOfPackageRows = 0;
                        $totalIssueRows = 0;
                        $expiredStyle = '';
                        
                        $_POST['claimStatus[]'] = $arrProgressStatus[1]['status'];
                            
                        if ($i == $totalRows ){
                            $class = 'detail-row-template row-template';
                            $overwrite = false;
                            $disabled = true;
                            $baseunitname = 'Pcs';
                        } else{  
                            
                            //$class = 'item-row ' . $class;
                            
                            $rsItemContent = $obj->getItemContentDetail($rsDetail[$i]['pkey']);
                            $totalContentOfPackageRows = count($rsItemContent); 
                            
                            $rsIssue = $obj->getIssueDetail($rsDetail[$i]['pkey']);
                            $totalIssueRows = count($rsIssue); 
                            
                            $rsWarrantyProgress = $warrantyClaimProgress->searchData($warrantyClaimProgress->tableName.'.refkey', $rsDetail[$i]['pkey'], true, ' and ' . $warrantyClaimProgress->tableName.'.statuskey <> 6 ');
                                 
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey']; 
                            $_POST['hidItemKey[]'] =  $rsDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] =  $rsDetail[$i]['itemname'];  
                            $_POST['hidVendorPartNumberKey[]'] = $rsDetail[$i]['vendorpartnumberkey']; 
                            $_POST['vendorPartNumber[]'] = $rsDetail[$i]['partnumber'];
                           /* $_POST['hidIssueKey[]'] =  $rsDetail[$i]['issuekey'];
                            $_POST['issueName[]'] =  $rsDetail[$i]['issuename'];*/
                            $_POST['serialNumber[]'] =  $rsDetail[$i]['serialnumber'];
                            $_POST['detailNotes[]'] =  $rsDetail[$i]['trdesc'];
                            $_POST['itemOutDate[]'] = $obj->formatDBDate($rsDetail[$i]['itemoutdate']);
                            $_POST['warrantyPeriodExpiredDate[]'] = $obj->formatDBDate($rsDetail[$i]['warrantyperiodexpireddate']);
                            $_POST['hidSellerKey[]'] =  $rsDetail[$i]['sellerkey'] ; 
                            $_POST['sellerName[]'] =  $rsDetail[$i]['sellername'] ; 
                            
                            
                            // get info from progress
                            $rsProgess = $warrantyClaimProgress->searchData($warrantyClaimProgress->tableName.'.refkey', $rsDetail[$i]['pkey'],true,' and '.$warrantyClaimProgress->tableName.'.statuskey <> 5');
                            if (!empty($rsProgess)){
                                $_POST['selClaimResult[]'] =  $rsProgess[0]['claimresultkey'] ; 
                                $_POST['claimStatus[]'] =  $rsProgess[0]['statusname'] ;  
                            }
                            
                            /*$_POST['qty[]'] =   $obj->formatNumber($rsDetail[$i]['qtyinbaseunit']); 
                            $_POST['selUnit[]'] =  $rsDetail[$i]['unitkey'];*/ 
                            
                            $datefrom = date('d / m / Y');
                            $dateto = $_POST['warrantyPeriodExpiredDate[]'];
    
                            $datediff = $obj->dateDiff($datefrom,$dateto);
                            $expiredStyle = ($rs[0]['statuskey'] == 1 && $datediff <= 0) ? 'col-red-cardinal' : '';
                            $datediff = $obj->formatNumber( round($datediff / (60 * 60 * 24)) );
                        }
                 ?> 
          
            
                <div class="div-table-row item-row <?php echo $class; ?>" > 
                    <div class="div-table-col">
                        <div class="div-table row-panel" style="width:100%">
                            <div class="div-table-row">
                                <!--<div class="div-table-col detail-col-detail row-number"></div>-->
                                <div class="div-table-col detail-col-detail" style="padding:1em 0;">
                                    <div class="div-table" style="width:100%">
                                       <div class="div-table-row">
                                            <div class="div-table-col-5" style="width:20%;">
                                                <div style="font-weight:bold"><?php echo ucwords($obj->lang['serialNumber']); ?></div>
                                                <?php echo $obj->inputText('serialNumber[]', array('overwritePost' => $overwrite, 'class' => 'form-control mnv-barcode-input', 'disabled' => $disabled, 'etc' =>'style="text-align:center"' )); ?>    
                                                <?php echo $obj->inputHidden('hidSNKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled )); ?>
                                                <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            </div>
                                            <div class="div-table-col-5 reset-on-data-not-found" style="width:20%;">
                                                <div  style="font-weight:bold"><?php echo ucwords($obj->lang['vendorPartNumber']); ?></div>
                                                <?php echo $obj->inputText('vendorPartNumber[]',array('overwritePost' => $overwrite, 'class'=> 'form-control no-tabs-index' , 'etc' => 'tabIndex=-1','readonly' =>true,  'disabled' => $disabled, 'etc' =>'style="text-align:center"')); ?>
                                                <?php echo $obj->inputHidden('hidVendorPartNumberKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                           </div> 
                                            <div class="div-table-col-5 reset-on-data-not-found" style="width:30%; padding-left:1em">
                                                 <div  style="font-weight:bold"><?php echo ucwords($obj->lang['itemName']); ?></div>  
                                                <?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite,'class'=> 'form-control no-tabs-index' , 'etc' =>'tabIndex=-1' ,'readonly' =>true, 'disabled' => $disabled)); ?>
                                                <?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                                <?php echo $obj->inputHidden('hidTempItemKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                            </div>  
                                            <div class="div-table-col" style="width:15%;">
                                                <div class="bg-blue-munsell text-white round5" style="padding:0.5em">
                                                 <div  style="font-weight:bold"><?php echo ucwords($obj->lang['claim']); ?></div>  
                                                 <?php echo $obj->inputSelect('selClaimResult[]',$arrClaim,array('overwritePost' => $overwrite)); ?>
                                                </div>
                                            </div>   
                                            <div class="div-table-col" >
                                                <div class="bg-yellow-mikado text-white round5" style="padding:0.5em">
                                                 <div  style="font-weight:bold"><?php echo ucwords($obj->lang['status']); ?></div>  
                                                 <?php echo $obj->inputText('claimStatus[]',array('readonly' => true)); ?>
                                                </div>
                                            </div>   
                                        </div>   
                                    </div> 
                                     
                                    <div class="div-table" style="width:100%; margin-top:0.5em">
                                         <div class="div-table-row">
                                            <div class="div-table-col  reset-on-data-not-found" style="width:40%; padding:0">
                                                <div class="div-table" style="width:100%">
                                                   <div class="div-table-row">
                                                        <div class="div-table-col" style="width:50%; padding-bottom:0">
                                                            <div  style="font-weight:bold"><?php echo ucwords($obj->lang['soldDate']); ?></div>
                                                           <?php echo $obj->inputText('itemOutDate[]', array('overwritePost' => $overwrite, 'readonly' => true, 'disabled' => $disabled, 'etc' =>'style="text-align:center"' )); ?> 
                                                        </div>
                                                        <div class="div-table-col expired-date <?php echo $expiredStyle; ?>" style="width:50%; padding-bottom:0">
                                                            <div class="flex">
                                                                <div class="consume" style="font-weight:bold"><?php echo ucwords($obj->lang['warrantyExpiredDate']); ?></div>
                                                                <div class="asterix-label" style="text-align:center; "><span class="date-diff inputnumber"><?php echo $datediff; ?></span> hari</div>
                                                            </div>
                                                            <?php echo $obj->inputText('warrantyPeriodExpiredDate[]', array('overwritePost' => $overwrite, 'readonly' => true, 'disabled' => $disabled, 'etc' =>'style="text-align:center"'  )); ?>  
                                                       </div>  
                                                    </div>   
                                                </div>  
                                                 <div class="div-table" style="width:100%; margin-top:0.5em">
                                                   <div class="div-table-row">
                                                        <div class="div-table-col" style="width:50%; padding-bottom:0"> 
                                                            <div  style="font-weight:bold"><?php echo ucwords($obj->lang['storeName']); ?></div> 
                                                            <?php echo $obj->inputText('sellerName[]', array('overwritePost' => $overwrite, 'readonly' => true, 'disabled' => $disabled )); ?> 
                                                            <?php echo $obj->inputHidden('hidSellerKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>  
                                                        </div> 
                                                    </div>   
                                                </div>  
                                                
                                            </div>
                                            <div class="div-table-col" style="padding-left:1em">
                                                <div style="font-weight:bold"><?php echo ucwords($obj->lang['note']); ?></div>
                                                <?php echo $obj->inputTextArea('detailNotes[]',array('overwritePost' => $overwrite, 'etc' => ' style="height: 8em" ' , 'disabled' => $disabled)); ?>
                                             </div>
                                        </div> 
                                    </div>
                                     
                                    <div class="div-table" style="width:100%; margin-top:1em">
                                         <div class="div-table-row"> 
                                            <div class="div-table-col" style="width:40%;"> 
                                                <div class="div-table mnv-transaction transaction-detail issue-list" style="width: 100%;" attr-level="1" attr-group="hidIssueDetailKey">
                                                     <div class="div-table-row">   
                                                        <div class="div-table-col detail-col-detail col-header no-border"><?php echo ucwords($obj->lang['issue']); ?></div> 
                                                     </div> 

                                                     <?php    

                                                        for ($j=0;$j<=$totalIssueRows; $j++){   

                                                            $classDetail =  'transaction-detail-row';
                                                            $overwriteDetail = true; 
                                                            $disabledDetail = false;
                                                            $styleDetail = '';

                                                            if ($j == $totalIssueRows ){  
                                                                $classDetail = 'issue-row-template row-template'; 
                                                                $overwriteDetail = false;
                                                                $disabledDetail = true;  
                                                                $styleDetail = 'style="display:none"';
                                                            } else {   

                                                                //$classDetail = 'issue-detail-row ' . $classDetail;

                                                                $_POST['hidIssueDetailKey[]'] = $rsIssue[$j]['pkey'];
                                                                $_POST['hidIssueKey[]'] = $rsIssue[$j]['issuekey'];
                                                                $_POST['issueName[]'] = $issueCategory->getPath($rsIssue[$j]['issuekey'])[0]['path']; //$rsIssue[$j]['issue']; 
                                                            } 

                                                    ?>
                                                    <div class="div-table-row <?php echo $classDetail; ?>" <?php echo $styleDetail; ?>>
                                                        <div class="div-table-col-3" style="text-align:center">
                                                            <?php echo $obj->inputHidden('hidIssueDetailKey[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?>
                                                            <?php echo $obj->inputHidden('hidIssueKey[]', array('overwritePost' => $overwriteDetail,'disabled' => $disabledDetail)); ?>
                                                            <?php echo $obj->inputText('issueName[]', array('overwritePost' => $overwriteDetail, 'class' => 'form-control s-input', 'disabled' => $disabledDetail)); ?> 
                                                        </div>
                                                        <div class="div-table-col-3 icon-col" style="font-weight:1.2em"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="issue-row-template"')); ?></div>
                                                    </div> 
                                                    <?php } ?>
 
                                                </div>
                                               
                                            </div>
                                            <div class="div-table-col" style="padding-left:1em" >
                                                  <!-- CONTENT OF PACKAGE -->
                                                    <div class="div-table mnv-transaction transaction-detail content-of-package" style="width: 100%;" attr-level="1" attr-group="hidItemContentDetailKey">
                                                                <div class="div-table-row">  
                                                                    <!--<div class="div-table-col detail-col-detail col-header no-border" style="width:20px"></div>-->
                                                                    <div class="div-table-col detail-col-detail col-header no-border" style="width:200px"><?php echo ucwords($obj->lang['contentOfPackage']); ?></div>
                                                                    <div class="div-table-col detail-col-detail col-header no-border" style="text-align:right"><?php echo ucwords($obj->lang['qty']); ?></div>   
                                                                </div> 

                                                                <?php    

                                                                    for ($j=0;$j<=$totalContentOfPackageRows; $j++){   

                                                                        $classDetail =  'transaction-detail-row';
                                                                        $overwriteDetail = true; 
                                                                        $disabledDetail = false;
                                                                        $styleDetail = '';

                                                                        if ($j == $totalContentOfPackageRows ){  
                                                                            $classDetail = 'content-of-package-row-template row-template'; 
                                                                            $overwriteDetail = false;
                                                                            $disabledDetail = true;  
                                                                            $styleDetail = 'style="display:none"';
                                                                        } else {   

                                                                            //$classDetail = 'content-detail-row ' . $classDetail;

                                                                            $_POST['hidItemContentDetailKey[]'] = $rsItemContent[$j]['pkey'];
                                                                            $_POST['qtyDetail[]'] = $obj->formatNumber($rsItemContent[$j]['qty']);
                                                                            $_POST['hidItemDetailKey[]'] = $rsItemContent[$j]['itemkey'];
                                                                            $_POST['itemNameDetail[]'] = $rsItemContent[$j]['itemname'];
                                                                            //$_POST['chkPick[]'] = $rsItemContent[$j]['ischeck'];
                                                                            //$_POST['chkService[]'] = 1;
                                                                        } 

                                                                ?>
                                                                <div class="div-table-row <?php echo $classDetail; ?>" <?php echo $styleDetail; ?>>
                                                                 <!--   <div class="div-table-col-3" style="text-align:center">
                                                                        <?php //echo $obj->inputCheckBox('chkPick[]', array('overwritePost' => $overwriteDetail, 'value' => 1, 'disabled' => $disabledDetail)); ?>
                                                                    </div>-->
                                                                    <div class="div-table-col-3">
                                                                        <?php echo $obj->inputHidden('hidItemContentDetailKey[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?>
                                                                        <?php echo $obj->inputText('itemNameDetail[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'class' => 'form-control s-input', 'disabled' => $disabledDetail)); ?>
                                                                        <?php echo $obj->inputHidden('hidItemDetailKey[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?>
                                                                    </div>
                                                                    <div class="div-table-col-3" style="width:5em">
                                                                        <?php echo $obj->inputNumber('qtyDetail[]', array('overwritePost' => $overwriteDetail, 'class' => 'form-control inputnumber s-input', 'disabled' => $disabledDetail, 'etc' => 'style="text-align:right;" ')); ?>
                                                                        <?php  echo $obj->inputLinkButton('btnDeleteItemContentRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1" style="display:none"','class' => 'btn btn-link remove-button')); ?>
                                                                    </div> 
                                                                </div> 
                                                                <?php } ?>
                                                    </div>   
                                            </div>
                                        </div> 
                                    </div>
                                    
                                    <div class="section-title-h2" style="margin-left:0.5em; margin-top:1.5em"><?php echo $obj->lang['workProgress']; ?></div>
                                    <div class="div-table">
                                        <div class="div-table-row">
                                            <div class="div-table-col-3 row-header" style="text-align:center; width: 10em"><?php echo $obj->lang['date']; ?></div>
                                            <div class="div-table-col-3 row-header"><?php echo $obj->lang['description']; ?></div>
                                          </div>
                                    <?php  
                                        if (!empty($rsWarrantyProgress)){ 
                                            $rsProgressDetail = $warrantyClaimProgress->getDetailWithRelatedInformation($rsWarrantyProgress[0]['pkey'],'', ' order by trdate desc'); 

                                            foreach ($rsProgressDetail as $detail){
                                                echo '<div class="div-table-row">
                                                        <div class="div-table-col-3" style="text-align:center">'.$obj->formatDBDate($detail['trdate']).'</div>
                                                        <div class="div-table-col-3">'.str_replace(chr(13),'<br>',$detail['description']).'</div>
                                                      </div>';
                                            }
                                        }
                                    ?>
                                    </div>
                                    
                               </div>
                                <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>" style="vertical-align:top; padding-top:1em !important"><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1"','class' => 'btn btn-link remove-button')); ?></div>
                            </div>
                        </div>
                    </div>
            </div> 
          
          <?php  } ?>
          
          
      </div>
      
       
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
    
      
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true); ?>  
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
