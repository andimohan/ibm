<?php
require_once '../_config.php'; 
require_once '../_include-v2.php';  


includeClass(array('TruckingServiceWorkOrder.class.php','GPS.class.php','Warehouse.class.php', 'Car.class.php','Customer.class.php', 'TruckingServiceOrder.class.php'));

$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$gps = new GPS();
$warehouse = new Warehouse();
$car = new Car();
$customer = new Customer();
$truckingServiceOrder = new TruckingServiceOrder();
$obj = $truckingServiceWorkOrder;

$securityObject = $obj->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

// kalo utk nampilin mobil tertentu dari SPK
//if (isset($_GET) && !empty($_GET['registrationNumber'])){
//    $_POST['hidRegistrationNumber'] = $_GET['registrationNumber'];
//}
if (isset($_GET) && !empty($_GET['carkey'])){
    $_POST['selCar[]'] = $_GET['carkey'];
}
    
  
  $arrGPS = $obj->convertForCombobox($gps->searchData($gps->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
  $arrWarehouse = $obj->convertForCombobox($warehouse->searchData($warehouse->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
  $arrCar = $obj->convertForCombobox($car->searchData($car->tableName . '.statuskey', 1, true, '', 'order by policenumber asc'), 'pkey', 'policenumber');
 //  $arrCustomer = $obj->convertForCombobox($customer->searchData($customer->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');
 // $arrJobOrder = $obj->convertForCombobox($truckingServiceOrder->searchData($truckingServiceOrder->tableName . '.statuskey', 2, true, '', 'order by code asc'), 'pkey', 'code');

  $arrSelectGPS =  $obj->inputSelect('selGPS[]', $arrGPS, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox', 'id' => 'gps-provider'));
  $arrSelectWarehouse  = $obj->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox', 'id' => 'gps-warehouse'));
  $arrSelectCar = $obj->inputSelect('selCar[]', $arrCar, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox', 'id' => 'gps-car'));
 // $arrSelectCustomer = $obj->inputSelect('selCar[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox', 'id' => 'gps-customer'));
 // $arrSelectJobOrder = $obj->inputSelect('selJobOrder[]', $arrJobOrder, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox', 'id' => 'gps-job-order'));


?>
<html>
  <head>
    <title>Map Demo</title>

    <link rel="stylesheet" type="text/css" href="style.css" />  
 	<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />  
 	<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css" /> 
   <link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>sol.css"/> 
 	<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>responsive-1.0.min.css" /> 
 	<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath . ADMIN_CSS_VERSION; ?>" /> 
   
	
  <script type="module" src="index.js"></script>   
	<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>     
	<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-ui.min.js" ></script> 

  <script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>sol.js"></script>   
  <script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>main-3.111.min.js"></script>     
  </head>
  <body>
<!--    <?php echo $class->inputHidden('hidRegistrationNumber'); ?>-->
    <div id="map"></div>

    <div id="control-box">

        <div> 
            <div class="form-group">
              <label class="col-xs-12">GPS</label>
              <div class="col-xs-12"><?php echo $arrSelectGPS; ?></div>
            </div>
            <div style="clear:both; height:1em"></div>
            <div class="form-group">
              <label class="col-xs-12"><?php echo $obj->lang['warehouse'] ?></label>
              <div class="col-xs-12"><?php echo $arrSelectWarehouse ?></div>
            </div>
            <div style="clear:both; height:1em"></div>
            <div class="form-group">
              <label class="col-xs-12"><?php echo $obj->lang['car'] ?></label>
              <div class="col-xs-12"><?php echo $arrSelectCar ?></div>
            </div>
            <div style="clear:both; height:1em"></div>
            <div class="form-group">
              <label class="col-xs-12"><?php echo $obj->lang['customer'] ?></label>
              <div class="col-xs-12">
                <?php    
                    echo $obj->inputAutoComplete(array(
                                              'objRefer' => $customer,
                                              'revalidateField' => false, 
                                              'element' => array('value' => 'customerName',
                                                                  'key' => 'hidCustomerKey'),
                                              'source' =>array(
                                                                  'url' => '../admin/ajax-customer.php',
                                                                  'data' => array('action' =>'searchData&statuskey=2' )
                                                              ) 
                                            )
                                        );  
                ?>
              </div>
            </div>
            <div style="clear:both; height:1em"></div>
            <div class="form-group">
              <label class="col-xs-12"><?php echo $obj->lang['jobOrder'] ?></label>
              <div class="col-xs-12"><?php 
                echo $obj->inputAutoComplete(array(
                                              'objRefer' => $truckingServiceOrder,
                                              'revalidateField' => false, 
                                              'element' => array('value' => 'jobOrderCode',
                                                                  'key' => 'hidJobOrderKey'),
                                              'source' =>array(
                                                                  'url' => '../admin/ajax-trucking-service-order.php',
                                                                  'data' => array('action' =>'searchData', 'statuskey' => '(1,2,3,4,5,6)' )
                                                              ) 
                                            )
                                        );  
              ?></div>
            </div>
            <div style="clear:both; height:1em"></div>
            <div class="form-group">
                <div class="col-xs-12">
                    <?php echo $obj->inputButton('filterButton',$obj->lang['submit'],array('etc' => 'style="width: 100%"')); ?>
                </div>
            </div>  
            <div style="clear:both; height:1em"></div>
        </div>  
    </div>

    <!-- prettier-ignore -->
    <script>(g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})
        ({key: "AIzaSyDU279CJVLdg3hukSbRQpVqhJ9yQ8BSB6U", v: "weekly"});
    
    
      jQuery(document).ready(function(){ 

        

        $(document).on("fullscreenchange webkitfullscreenchange mozfullscreenchange MSFullscreenChange", function() {
            let $controlBox = $("#control-box");
            if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) {
                $(".gm-style").append($controlBox); 
            } else {
                $("body").append($controlBox); 
            }
        });

      })
</script>
  </body>
</html>
