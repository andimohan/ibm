<?php
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array("TruckingServiceWorkOrder.class.php"));
$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());

$obj= $truckingServiceWorkOrder;

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

if (!isset($_GET) || empty($_GET['ltdlng'])) die;

$ltdlng = explode(',',$_GET['ltdlng']);

$mapAPIKey = $obj->loadSetting('mapAPIKey');

//$wokey=$_GET['wokey'];
//$rsSPK  = $truckingServiceWorkOrder->getDataRowById($wokey);
//
//if(empty($rsSPK)) die;

?>
<html>
  <head>
    <title><?php echo $rsSPK[0]['code']; ?></title>
 	<style> 
		#map {  height: 100%; } 
		html,body { height: 100%; margin: 0; padding: 0;}  
  
	 </style>

	<script type="text/javascript" src="{{ TEMPLATE_JS_PATH }}jquery-3.3.1.min.js"></script>  
    <script>
      async function initMap() {
          // Request needed libraries.
          const { Map, InfoWindow } = await google.maps.importLibrary("maps");
          const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary( "marker",);
            
            const position = { lat: <?php echo $ltdlng[0]; ?>, lng: <?php echo $ltdlng[1]; ?> }; // Example: New York City
          
          const map = new google.maps.Map(document.getElementById("map"), {
            center: position,   // 👈 Set location here
            zoom: 18, 
            mapId: "33e9ca9d26d95f77",
          });
            
        new AdvancedMarkerElement({
          map,
          position: position,
          title: "Marker",
        });

        }

        // Expose globally
        window.initMap = initMap;
    </script>  
      	  
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $mapAPIKey; ?>&callback=initMap&libraries=maps,marker"  async  defer></script>

  </head>
  <body>
    <div id="map"></div> 
    <!-- prettier-ignore --> 
  </body>
</html>