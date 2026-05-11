import { MarkerClusterer } from "https://cdn.skypack.dev/@googlemaps/markerclusterer@2.3.1";

var markerCluster;

function parseJSON(data){ 

	data = $.trim(data);

	if(!data) data = '[]'; 
	if(data.length == 0) data = '[]'; 

	return JSON.parse(data);
}

async function initMap() {
  // Request needed libraries.
  const { Map, InfoWindow } = await google.maps.importLibrary("maps");
  const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary( "marker",);
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 14, 
    mapId: "33e9ca9d26d95f77",
  });
  const infoWindow = new google.maps.InfoWindow({
    content: "",
    disableAutoPan: true,
  });
 
	
  const trafficLayer = new google.maps.TrafficLayer();
  trafficLayer.setMap(map);
	
	// Create an array of alphabetical characters used to label the markers.
  
	var WOInformation = [];
	var markers = [];
 
  // Add a marker clusterer to manage the markers.
  markerCluster = new MarkerClusterer({ markers, map }); 
	
	function updateMarker(){ 
//		    var data = (WOInformation.length == 0) ? 'wokey='+wokey : 'registrationnumber='+registrationnumber;
		
			// sementara tarik ulang terus SPK nya
			var data = 'wokey='+wokey;
		
			console.log(data);
		
		       $.ajax({
					type: "GET",
					data : data, 
					url: "/ajax-work-order-marker.php",   
					beforeSend : function() {

					},
					success: function(data){  
						 
						if (!data) return;

						 data = parseJSON(data); 
						 if(data.length == 0)  return;
 
						markerCluster.clearMarkers();

						var locations = []; 
						
						// harus based on SPK, karena map ini untuk melihat kendaraan yg sedang berjalan
						
						
						jQuery.each(data, function(index, item)  {
							// hanya munculin yg dipilih saja plat nomor nya 
//							if( item['policenumber'] !=  registrationNumber.replace(/ /g, "") ) return;
							
							// hanya bisa untuk 1 WO
//							if(WOInformation.length == 0){
//								 WOInformation.push({
//									 				 "drivername" :  item['drivername'], 
//									 				 "workordercode" :  item['workordercode'], 
//								 					});
//							} 
							
							 locations.push({"registrationNumber" : item['policenumber'], 
											 "lat" : parseFloat(item['gps']['location']['latitude']),
											 "lng" :  parseFloat(item['gps']['location']['longitude']), 
											 "speed" : item['gps']['speed'], 
											 "drivername" : item['drivername'], 
											 "workordercode" : item['code'], 
											 "route" : item['route'] 
											}); 
						});
  
						  const markers = locations.map((position, i) => {
							  
							  var color = "#ff7800";
							  if(parseInt(position.speed) <= 0)
								  color = "red"; 
							  else if(parseInt(position.speed) <= 10)
								  color = "yellow";
							  
							  const pinBackground = new PinElement({
									  background:color,
								  glyphColor:"white"
									});
							  
							const marker = new google.maps.marker.AdvancedMarkerElement({
							  position,
							  content: pinBackground.element 
							});

							// markers can only be keyboard focusable when they have click listeners
							// open info window when marker is clicked
							marker.addListener("click", () => {
 
							  const contentString =
							'<div id="content">' +
							'<div id="siteNotice">' +
							"</div>" +
							'<h1>'+ position.registrationNumber+'</h1>' +
							'<div class="information-table">' +
							'<table>' +
							'<tr><td class="row-header">Sopir</td><td>:</td><td>' + position.drivername  + '</td></tr>'+
							'<tr><td class="row-header">Kec.</td><td>:</td><td>' + position.speed  + ' km/h</td></tr>'+
							'<tr><td class="row-header">SJ</td><td>:</td><td>' + position.workordercode  + '</td></tr>'+
							'<tr><td class="row-header">Rute</td><td>:</td><td>' + position.route  + '</td></tr>'+
							'</table>' +
							'</div>' +
							'</div>';

							  infoWindow.setContent(contentString);
  							  infoWindow.open(map, marker);
							});
							return marker;
						  });

 						markerCluster.addMarkers(markers); 
						map.setCenter(new google.maps.LatLng(parseFloat(locations[0].lat),parseFloat(locations[0].lng)));
						
					},
					complete:function(xhr, desc) {    
//						setTimeout(function(){
//						  updateGPSInformationBatch();
//						}, 1000); 
					 }
				}); 
			 }
 
	updateMarker();
	
	setInterval(updateMarker, 30000);
	

	
}

initMap();