import { MarkerClusterer } from "https://cdn.skypack.dev/@googlemaps/markerclusterer@2.3.1";

var markerCluster;
var vehicle={}; // pake variabel utk nampung, agar kalo gagal tetep muncul di map, kareneda beda vendor

function parseJSON(data){ 

	data = $.trim(data);

	if(!data) data = '[]'; 
	if(data.length == 0) data = '[]'; 

	return JSON.parse(data);
}

 function disableButton(targetContent,status){
        if(status == undefined) status = true;

        targetContent.prop("disabled",status);

        if(status){ 
            targetContent.find(".loading-icon:first").show();  
        }else{ 
            targetContent.find(".loading-icon:first").hide();  
        }
    }

 


$('.multi-selectbox').searchableOptionList({
    maxHeight: '250px',
    showSelectAll: true,
    showSelectionBelowList: true
});

async function initMap() {
  // Request needed libraries.
  const { Map, InfoWindow } = await google.maps.importLibrary("maps");
  const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary( "marker",);
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 9,
    center: { lat: -6.2295694, lng: 106.7469458 },
    mapId: "33e9ca9d26d95f77",
  });
  const infoWindow = new google.maps.InfoWindow({
    content: "",
    disableAutoPan: true,
  });
  // Create an array of alphabetical characters used to label the markers.
  
  const trafficLayer = new google.maps.TrafficLayer();

  trafficLayer.setMap(map);
	
  var markers = [];
  var lastPosition = {};
 
 const iconBase = './';
  const icons = {
    driving: {
      icon: iconBase + "truck.png",
    }
//	  ,
//    library: {
//      icon: iconBase + "library_maps.png",
//    },
//    info: {
//      icon: iconBase + "info-i_maps.png",
//    },
  };
	
 
  markerCluster = new MarkerClusterer({ markers, map });
    
	 function bearing( from, to ) {
        // Convert to radians.
        var lat1 = from.lat;
        var lon1 = from.lng;
        var lat2 = to.lat;
        var lon2 = to.lng;
        // Compute the angle.
        var angle = - Math.atan2( Math.sin( lon1 - lon2 ) * Math.cos( lat2 ), Math.cos( lat1 ) * Math.sin( lat2 ) - Math.sin( lat1 ) * Math.cos( lat2 ) * Math.cos( lon1 - lon2 ) );
        if ( angle < 0.0 )
            angle  += Math.PI * 2.0;
        if (angle == 0) {angle=1.5;}
        return angle;
    }
 

    function getCriteriaData() {
		 
		var filterCriteria = '';

		var optionsGps = document.getElementById('gps-provider').selectedOptions;
		var optionsWarehouse = document.getElementById('gps-warehouse').selectedOptions;
		var optionsCar = document.getElementById('gps-car').selectedOptions;
		var customer = $("[name=hidCustomerKey]").val();
		var jobOrder = $("[name=hidJobOrderKey]").val();
	
		var valuesGps = Array.from(optionsGps, option => option.value);
		var valuesWarehouse = Array.from(optionsWarehouse , option => option.value);
		var valuesCar = Array.from(optionsCar , option => option.value);

		filterCriteria = valuesGps.length > 0 
				? 'gpsProviderKey=' + JSON.stringify(valuesGps) 
				: '';
	
		filterCriteria += valuesWarehouse.length > 0 
				? (filterCriteria ? '&' : '') + 'warehousekey=' + JSON.stringify(valuesWarehouse) 
			: '';

		filterCriteria += valuesCar.length > 0 
				? (filterCriteria ? '&' : '') + 'carkey=' + JSON.stringify(valuesCar) 
			: '';
			
		filterCriteria += customer != '' 
				? (filterCriteria ? '&' : '') + 'customerkey=' + customer 
			: '';
		
		filterCriteria += jobOrder != ''
				? (filterCriteria ? '&' : '') + 'jobOrderKey=' + jobOrder 
			: '';
 
//		criteriaData = filterCriteria ? filterCriteria : '';
	
		return filterCriteria;
	}

	
	function updateMarker(manualFilter){
		   
        if (typeof manualFilter === 'undefined') manualFilter = false;
        
            // buat lemparan dari GET ? nanti otomatis lempar ke filter aj
//            var registrationNumberCriteria =($("[name=hidRegistrationNumber]").val() != "") ? 'registrationNumber='+$("[name=hidRegistrationNumber]").val() : '';
               
        	var criteriaSearchData = getCriteriaData();
//			console.log(criteriaSearchData)
        
		       $.ajax({
					type: "GET",
					////data : registrationNumberCriteria, 
					data : criteriaSearchData,   
					url: "ajax-marker.php",   
					beforeSend : function() { 
                        // hanya jika klik tombol filter ulang
                        if(manualFilter){ 
                            markerCluster.clearMarkers();
                            map.setZoom(10);      
                        }
					},
					success: function(data){   

						if (!data) return;

						 data = parseJSON(data); 
                        // pake length jadinya undefined
//						 if(data.length == 0)  return; 
 
						 
 						// update latest data  
                        var totalVehicle = 0;

						var vehicle = {};//reset vehicle
						jQuery.each(data, function(index, item)  { 
						var carPkey = item.pkey;
							totalVehicle++;
                            
							if(typeof vehicle[carPkey] === 'undefined'){
								vehicle[carPkey] = {}; // init
							} 
							
							 var lat = parseFloat(item['gpsdata']['location']['latitude']);
							 var lng = parseFloat(item['gpsdata']['location']['longitude']); 
							 
							// kalo lat lng kosong, diskip, karena mungkin kena timeout atau limit request
							if (lat == 0) return;
							
							 // init first log
							 if(typeof lastPosition[carPkey] === 'undefined') {  
								lastPosition[carPkey] = {"lat" : lat, "lng" : lng}
							 }
							
							 // update angle  
							 var angle = bearing({'lat':lastPosition[carPkey].lat,'lng': lastPosition[carPkey].lng},{'lat':lat,'lng': lng})
//							 if ( item['policenumber'] == 'B9944GJ') 
//								 console.log(angle)
//							
						 
							 vehicle[carPkey]={"registrationNumber" : item['policenumber'],
											 "angle" : angle, 
											 "route" : item['route'],
											 "drivername" : item['drivername'],
											 "workordercode" : item['workordercode'], 
											 "lat" : lat,
											 "lng" : lng, 
											 "latbefore" : lastPosition[carPkey].lat,
											 "lngbefore" :  lastPosition[carPkey].lng, 
											 "speed" : item['gpsdata']['speed'], 
											 "providername" : item['gpsdata']['providername'],
											 "consigneename" : item['consigneename'], 
											};  
							
							
							 // log last pos
							 lastPosition[carPkey] = {"lat" : lat, "lng" : lng}
//							  
						});
						
						var markers = []; 
						jQuery.each(vehicle, function(index, item)  {  
							
							var driverName = (item.drivername || '-') ;
							var speed = (item.speed  || 0) ;
							var workOrderCode = (item.workordercode  || '-') ;
							var route = (item.route  || '-') ;
							var latBefore = (item.latbefore  || 0) ;
							var lngBefore = (item.lngbefore  || 0) ;
							var lat = (item.lat  || 0) ;
							var lng = (item.lng  || 0) ;
							var providerName = (item.providername  || 0) ;
							var consigneeName = (item.consigneename || '-');
							 
							if (lat == 0) return;
						 
							const iconImage = document.createElement("img"); 
							iconImage.src = icons['driving'].icon;
							 
								
							var rotate = (lngBefore > lng || latBefore > lat ) ?  'scaleX(-1)' : '';
							
							$(iconImage).css({  '-webkit-transform': rotate, '-moz-transform': rotate, '-o-transform': rotate, '-ms-transform': rotate, 'transform': rotate });


							
							const marker = new google.maps.marker.AdvancedMarkerElement({   position: { lat: lat,  lng:lng},
																						    content : iconImage 
																						});

							var speedColor = (speed<=0) ? 'text-red-cardinal' : 'text-green-avocado';
							
							marker.addListener("click", () => {
							    
								const contentString =
								'<div id="content">' +
								'<div id="siteNotice">' +
								"</div>" +
								'<div class="flex"><div class="consume"><h1>'+ item.registrationNumber+'</h1></div><div class="'+speedColor+'" style="text-align:right">'+speed+' km/h</div></div>' +
								'<div>' +
								'<table  class="information-table">' +
								'<tr><td class="row-header">Sopir</td><td>:</td><td>' + driverName + '</td></tr>'+
								'<tr><td class="row-header">SJ</td><td>:</td><td>' + workOrderCode  + '</td></tr>'+
								'<tr><td class="row-header">Consignee</td><td>:</td><td>' + consigneeName  + '</td></tr>'+
								'<tr><td class="row-header">Rute</td><td>:</td><td>' + route  + '</td></tr>'+
								'<tr><td class="row-header">Provider</td><td>:</td><td>' + providerName  + '</td></tr>'+
								'</table>' +
								'</div>' +
								'</div>';

								  infoWindow.setContent(contentString);
	  							  infoWindow.open(map, marker);
  							   
							});
							
							markers.push(marker);
								
						});
						
//						console.log(markers.length);
						markerCluster.clearMarkers();
						markerCluster.addMarkers(markers); 
                        
                        if(manualFilter){  
                            if(totalVehicle == 1){
                                map.setZoom(17);   
                            } 
                            map.panTo(markers[0].position);
                        }
                        
                       disableButton($("[name=filterButton]"),false);
                        
					},
					complete:function(xhr, desc) {    
 
					 }
				}); 
			 }
 
	updateMarker(); 
	setInterval(updateMarker, 30000); 
	$("[name=filterButton]").click(function() {  
        disableButton($(this));
        updateMarker(true);  
    
    });
 
	
}

initMap();
