<?php 

// need to add token


if(!isset($_GET) || empty($_GET['location'])) die;

$location = explode(',',$_GET['location']);
$latitude = $location[0]; 
$longitude = $location[1]; 
    
?>
<!DOCTYPE html>
<html>
  <head>
    <style>
       /* Set the size of the div element that contains the map */
      #map {
        height: 100vh;  /* The height is 400 pixels */
        width: 100%;  /* The width is the width of the web page */
       }
    </style>
  </head>
  <body>
    <h3>WINTERA DEMO</h3>
    <!--The div element for the map -->
    <div id="map"></div>
    <script>
// Initialize and add the map
function initMap() {
     
  // The map, centered at Uluru
  var map = new google.maps.Map(
      document.getElementById('map'), 
      {center: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>), zoom: 16} 
  ); 
    
    
    var iconBase = 'https://wintera.co.id/include/img/';

    var icons = {
      vehicle: {
        icon: iconBase + 'icon-map.png'
      } 
    };
    
    var features = [
          {
            position: new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>),
            type: 'vehicle'
          } 
        ];

   // Create markers.
    for (var i = 0; i < features.length; i++) {
      var marker = new google.maps.Marker({
        position: features[i].position,
        icon: icons[features[i].type].icon,
        map: map
      });
    }; 
    
}
    </script> 
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZVMFYaUiiU4AeLswwH5wtX8eQTv7YJ9E&callback=initMap">
    </script>
  </body>
</html>